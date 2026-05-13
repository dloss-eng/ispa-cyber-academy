<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\{User, Badge, Module, QuizAttempt, StudentProgress};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = Cache::remember("user:{$user->id}:stats", 120, function () use ($user) {
            return [
                'points'              => $user->points,
                'level'               => $user->level,
                'badges_count'        => $user->badges()->count(),
                'certificates_count'  => $user->certificates()->count(),
                'quizzes_passed'      => QuizAttempt::where('user_id', $user->id)
                                            ->where('passed', true)->count(),
                'lessons_completed'   => $user->progress()
                                            ->where('status', 'completed')->count(),
            ];
        });

        // ── Modules + progression ──────────────────────────────────
        $modules = Module::where('is_published', true)
            ->whereIn('level', $user->allowedModuleLevels())
            ->withCount('lessons as total_lessons')
            ->orderBy('order')
            ->get();

        $progressMap = StudentProgress::where('user_id', $user->id)
            ->get()
            ->groupBy('lesson.module_id');

        $modulesWithProgress = $modules->map(function ($m) use ($progressMap) {
            $total     = $m->total_lessons;
            $completed = collect($progressMap[$m->id] ?? [])
                ->where('status', 'completed')
                ->count();
            $m->user_progress = $total > 0 ? round(($completed / $total) * 100) : 0;
            return $m;
        });

        // ✅ Afficher uniquement les modules commencés (progress > 0)
        $startedModules = $modulesWithProgress->filter(fn($m) => $m->user_progress > 0);

        // Compter les modules disponibles non commencés
        $availableCount = $modulesWithProgress->filter(fn($m) => $m->user_progress === 0)->count();

        // ── Leaderboard ───────────────────────────────────────────
        $leaderboard = User::whereHas('role', fn($q) =>
                $q->whereIn('name', ['eleve', 'etudiant'])
            )
            ->with('role')
            ->orderByDesc('points')
            ->limit(10)
            ->get();

        $userRank = User::where('points', '>', $user->points)->count() + 1;

        // ── Badges récents ────────────────────────────────────────
        $recentBadges = $user->badges()
            ->latest('student_badges.earned_at')
            ->limit(3)
            ->get();

        return view('dashboard.index', compact(
            'user',
            'stats',
            'startedModules',   // ✅ remplace modulesWithProgress
            'availableCount',   // ✅ nombre de modules disponibles non commencés
            'leaderboard',
            'userRank',
            'recentBadges'
        ));
    }

    public function leaderboard()
    {
        $users = User::whereHas('role', fn($q) =>
                $q->whereIn('name', ['eleve', 'etudiant'])
            )
            ->with('role')
            ->orderByDesc('points')
            ->paginate(50);

        return view('dashboard.leaderboard', compact('users'));
    }

    public function badges()
    {
        $user      = Auth::user();
        $allBadges = Badge::select('id', 'name', 'icon')->get();
        $earnedIds = $user->badges()->pluck('badges.id')->toArray();
        return view('dashboard.badges', compact('allBadges', 'earnedIds'));
    }

    public function certificates()
    {
        $certificates = Auth::user()
            ->certificates()
            ->with('module')
            ->latest('issued_at')
            ->get();
        return view('dashboard.certificates', compact('certificates'));
    }
}
