<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\{User, LoginLog};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, RateLimiter};
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // ✅ FIX — validation forte
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => ['required', \Illuminate\Validation\Rules\Password::min(12)->mixedCase()->numbers()->symbols()],
        ]);

        // ✅ FIX CRITIQUE — Rate Limiter Laravel (remplace cache manuel)
        $key = Str::lower($request->email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Trop de tentatives. Réessayez dans {$seconds} secondes.",
            ])->withInput();
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {

            $request->session()->regenerate();
            $user = Auth::user();

            // ✅ Compte désactivé
            if (! $user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Compte désactivé.']);
            }

            RateLimiter::clear($key);

            // ✅ Log succès
            LoginLog::create([
                'user_id'    => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'successful' => true,
                'created_at' => now(),
            ]);

            $user->update(['last_login_at' => now()]);

            // ✅ 2FA
            if ($user->two_factor_enabled) {
                app(TwoFactorController::class)->generateAndSend($user);
                session([
                    '2fa_user_id' => $user->id,  // ✅ F-08 FIX: session Laravel déjà chiffrée, encrypt() inutile
                    '2fa_remember' => $request->boolean('remember'),
                ]);
                Auth::logout();
                return redirect()->route('2fa.show');
            }

            return $this->redirectByRole($user);
        }

        // ✅ Incrément rate limiter sur échec (300s = 5 min)
        RateLimiter::hit($key, 300);

        // Log échec
        $user = User::where('email', $request->email)->first();
        if ($user) {
            LoginLog::create([
                'user_id'    => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'successful' => false,
                'created_at' => now(),
            ]);
        }

        return back()->withErrors([
            'email' => 'Identifiants incorrects.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function redirectByRole(User $user): \Illuminate\Http\RedirectResponse
    {
        // ✅ Tous les rôles explicitement gérés
        return match ($user->role?->name) {
            'admin'         => redirect()->route('admin.dashboard'),
            'etablissement' => redirect()->route('etablissement.dashboard'),
            'enseignant'    => redirect()->route('enseignant.dashboard'),
            'eleve',
            'etudiant'      => redirect()->route('dashboard'),
            default         => abort(403, 'Rôle non reconnu. Contactez un administrateur.'),
        };
    }
}
