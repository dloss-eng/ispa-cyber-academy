<?php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use App\Models\{User, Classe, StudentProgress, QuizAttempt};
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $etab = $user->etablissement;

        abort_if(!$etab, 403);

        $classes = Classe::where('etablissement_id', $etab->id)
            ->withCount('students')
            ->get();

        // ⚡ optimisation : récupérer IDs étudiants une seule fois
        $studentIds = User::where('etablissement_id', $etab->id)
            ->whereHas('role', fn($q) => $q->whereIn('name', ['eleve','etudiant']))
            ->pluck('id');

        $recentProgress = StudentProgress::whereIn('user_id', $studentIds)
            ->with(['user','lesson.module'])
            ->latest()
            ->limit(20)
            ->get();

        return view('enseignant.dashboard', compact('etab','classes','recentProgress'));
    }

    public function classes()
    {
        $etab = Auth::user()->etablissement;

        $classes = Classe::where('etablissement_id', $etab->id)
            ->withCount('students')
            ->get();

        return view('enseignant.classes', compact('classes'));
    }

    public function students()
    {
        $etab = Auth::user()->etablissement;

        $students = User::where('etablissement_id', $etab->id)
            ->whereHas('role', fn($q) => $q->whereIn('name', ['eleve','etudiant']))
            ->paginate(30);

        return view('enseignant.students', compact('students'));
    }

    public function studentProgress(User $user)
    {
        abort_if($user->etablissement_id !== Auth::user()->etablissement_id, 403);

        $progress = StudentProgress::where('user_id', $user->id)
            ->with('lesson.module')
            ->get();

        $attempts = QuizAttempt::where('user_id', $user->id)
            ->with('quiz')
            ->latest()
            ->get();

        return view('enseignant.student-progress', compact('user','progress','attempts'));
    }

    public function classStats(Classe $classe)
    {
        abort_if($classe->etablissement_id !== Auth::user()->etablissement_id, 403);

        $students = $classe->students()->with('badges')->get();
        $ids = $students->pluck('id');

        // ⚡ group queries (évite N+1)
        $progressCounts = StudentProgress::whereIn('user_id', $ids)
            ->where('status','completed')
            ->selectRaw('user_id, COUNT(*) as total')
            ->groupBy('user_id')
            ->pluck('total','user_id');

        $quizCounts = QuizAttempt::whereIn('user_id', $ids)
            ->where('passed',true)
            ->selectRaw('user_id, COUNT(*) as total')
            ->groupBy('user_id')
            ->pluck('total','user_id');

        $stats = $students->map(fn($s) => [
            'user'=>$s,
            'progress'=>$progressCounts[$s->id] ?? 0,
            'quizzes'=>$quizCounts[$s->id] ?? 0,
            'points'=>$s->points
        ]);

        return view('enseignant.class-stats', compact('classe','stats'));
    }

    public function classReport(Classe $classe)
    {
        abort_if($classe->etablissement_id !== Auth::user()->etablissement_id, 403);

        $students = $classe->students()->with('badges')->get();
        $ids = $students->pluck('id');

        // ⚡ optimisation stats
        $progressCounts = StudentProgress::whereIn('user_id', $ids)
            ->where('status','completed')
            ->selectRaw('user_id, COUNT(*) as total')
            ->groupBy('user_id')
            ->pluck('total','user_id');

        $quizCounts = QuizAttempt::whereIn('user_id', $ids)
            ->where('passed',true)
            ->selectRaw('user_id, COUNT(*) as total')
            ->groupBy('user_id')
            ->pluck('total','user_id');

        $avgScores = QuizAttempt::whereIn('user_id', $ids)
            ->selectRaw('user_id, AVG(percentage) as avg_score')
            ->groupBy('user_id')
            ->pluck('avg_score','user_id');

        $stats = $students->map(fn($s) => [
            'user'=>$s,
            'progress'=>$progressCounts[$s->id] ?? 0,
            'quizzes'=>$quizCounts[$s->id] ?? 0,
            'avg_score'=>round($avgScores[$s->id] ?? 0),
            'points'=>$s->points,
            'badges'=>$s->badges->count()
        ]);

        $etab = Auth::user()->etablissement;

        $pdf = Pdf::loadView('enseignant.report-pdf', compact('classe','stats','etab'));

        return $pdf->download("rapport-{$classe->name}-".now()->format('Y-m-d').".pdf");
    }
}