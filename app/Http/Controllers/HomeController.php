<?php
namespace App\Http\Controllers;

use App\Models\{Module, Certificate, User};
use App\Helpers\HtmlSanitizer;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, Storage};

class HomeController extends Controller
{
    public function index()
    {
        $stats = [
            'modules'      => Module::where('is_published', true)->count(),
            'students'     => User::whereHas('role', fn($q) => $q->whereIn('name', ['eleve', 'etudiant']))->count(),
            'certificates' => Certificate::count(),
        ];
        return view('public.home', compact('stats'));
    }

    public function courses()
    {
        $modules = Module::where('is_published', true)
            ->withCount('lessons')
            ->orderBy('order')
            ->get();
        return view('public.courses', compact('modules'));
    }

    public function courseDetail(Module $module)
    {
        abort_if(! $module->is_published, 404);
        return view('public.course-detail', compact('module'));
    }

    public function verifyCertificate()
    {
        $certificate = null;
        if (request('code')) {
            $certificate = Certificate::with(['user', 'module'])
                ->where('certificate_number', request('code'))
                ->first();
        }
        return view('public.verify-certificate', compact('certificate'));
    }

    public function apiDocs()
    {
        return view('public.api-docs');
    }

    // ✅ Page Contact (GET)
    public function contact()
    {
        return view('navbar.contact');
    }

    // ✅ Page À propos (GET)
    public function about()
    {
        return view('navbar.propos');
    }

    // ✅ Contact — envoie une notification à tous les admins
    public function sendContact(Request $request)
    {
        $data = $request->validate([
            'prenom'  => 'required|string|max:100',
            'nom'     => 'required|string|max:100',
            'email'   => 'required|email|max:255',
            'sujet'   => 'required|in:inscription,etablissement,entreprise,technique,autre',
            'message' => 'required|string|min:10|max:2000',
        ]);

        // 🔔 Notifier tous les admins
        NotificationService::contactMessage(
            $data['prenom'],
            $data['nom'],
            $data['email'],
            $data['sujet'],
            $data['message']
        );

        return redirect()->route('contact')->with('success', true);
    }

    // ── Profil (tous rôles) ────────────────────────────────────────

    public function showProfile()
    {
        return view('dashboard.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'name'   => 'required|string|max:255',
            'phone'  => 'nullable|string|max:30',
            'bio'    => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // ✅ F-19 FIX: validation MIME avatar
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) Storage::disk('public')->delete($user->avatar);
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->filled('password')) {
            // ✅ F-10 FIX: vérification du mot de passe actuel obligatoire
            $request->validate([
                'current_password' => 'required|current_password',
                'password'         => 'min:8|confirmed',
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return back()->with('success', 'Profil mis à jour.');
    }

    public function publicProfile(User $user)
    {
        $userBadges       = $user->badges;
        $userCertificates = $user->certificates()->with('module')->get();
        return view('dashboard.public-profile', compact('user', 'userBadges', 'userCertificates'));
    }
}
