<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Mail, RateLimiter};
use Illuminate\Support\Str;

class TwoFactorController extends Controller
{
    public static function generateAndSend(User $user): void
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'two_factor_code' => bcrypt($code),
            'two_factor_expires_at' => now()->addMinutes(10)
        ]);

        try {
            Mail::raw(
                "Votre code de sécurité ISPA : {$code}",
                fn($m) => $m->to($user->email)->subject('🔐 Code 2FA')
            );
        } catch (\Exception $e) {
            // log si besoin
        }
    }

    public function show()
    {
        if (!session('2fa_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $userId = session('2fa_user_id');

        if (!$userId) {
            return redirect()->route('login');
        }

        $key = '2fa:' . $userId . '|' . $request->ip();

        // 🔐 anti brute-force
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors([
                'code' => 'Trop de tentatives. Réessayez plus tard.'
            ]);
        }

        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->two_factor_expires_at?->isPast()) {
            session()->forget(['2fa_user_id']);
            return redirect()->route('login')->withErrors([
                'code' => 'Code expiré.'
            ]);
        }

        if (!password_verify($request->code, $user->two_factor_code)) {
            RateLimiter::hit($key, 300);

            return back()->withErrors([
                'code' => 'Code incorrect.'
            ]);
        }

        RateLimiter::clear($key);

        $user->update([
            'two_factor_code' => null,
            'two_factor_expires_at' => null
        ]);

        Auth::login($user, session('2fa_remember', false));
        request()->session()->regenerate(); // ✅ F-09 FIX: évite la session fixation

        session()->forget(['2fa_user_id', '2fa_remember']);

        // ✅ F-12 FIX: redirection selon le rôle (admin, établissement, etc.)
        return app(AuthController::class)->redirectByRole($user);
    }

    public function resend(Request $request)
    {
        $userId = session('2fa_user_id');

        if (!$userId) {
            return redirect()->route('login');
        }

        $key = '2fa_resend:' . $userId;

        // 🔐 anti spam
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return back()->withErrors([
                'code' => 'Trop de demandes. Attendez un peu.'
            ]);
        }

        $user = User::find($userId);

        if ($user) {
            self::generateAndSend($user);
        }

        RateLimiter::hit($key, 60);

        return back()->with('success', 'Nouveau code envoyé.');
    }
}