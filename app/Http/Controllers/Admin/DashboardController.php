<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{User, Module, Certificate, Etablissement, QuizAttempt, LoginLog};

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_students' => User::whereHas('role', fn($q) => $q->whereIn('name', ['eleve', 'etudiant']))->count(),
            'total_modules' => Module::count(),
            'total_certificates' => Certificate::count(),
            'total_etablissements' => Etablissement::count(),
            'total_quiz_attempts' => QuizAttempt::count(),
            'avg_score' => round(QuizAttempt::avg('percentage') ?? 0),
            'recent_logins' => LoginLog::where('created_at', '>=', now()->subDay())->where('successful', true)->count(),
            'total_enseignants' => User::whereHas('role', fn($q) => $q->where('name', 'enseignant'))->count(),
        ];

        $recentUsers = User::with('role')->latest()->take(10)->get();
        $recentAttempts = QuizAttempt::with(['user', 'quiz'])->latest()->take(10)->get();

        // FIX: ->values() reindexes after sortByDesc so $i in @foreach is the actual position
        // and not the original etablissement_id. This was causing HEC ABIDJAN (170 pts)
        // to display as #5 instead of #1.
        $etabRanking = Etablissement::withCount('users')->get()->map(fn($e) => [
            'etab' => $e,
            'total_points' => User::where('etablissement_id', $e->id)->sum('points'),
            'avg_score' => round(QuizAttempt::whereHas('user', fn($q) => $q->where('etablissement_id', $e->id))->avg('percentage') ?? 0),
            'students_count' => User::where('etablissement_id', $e->id)->whereHas('role', fn($q) => $q->whereIn('name', ['eleve', 'etudiant']))->count(),
        ])->sortByDesc('total_points')->values();

        $topStudents = User::whereHas('role', fn($q) => $q->whereIn('name', ['eleve', 'etudiant']))
            ->with(['role', 'etablissement'])
            ->orderByDesc('points')
            ->take(15)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentAttempts', 'etabRanking', 'topStudents'));
    }
}
