<?php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use App\Models\{User, Classe, StudentProgress, QuizAttempt};
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    
    private function getTeacherClasses()
    {
        return Classe::where('enseignant_id', Auth::id())
            ->where('is_active', true)
            ->get();
    }

    
    private function getTeacherStudentIds(): \Illuminate\Support\Collection
    {
        $classIds = $this->getTeacherClasses()->pluck('id');
        if ($classIds->isEmpty()) return collect();

        return User::whereHas('classes', fn($q) => $q->whereIn('classes.id', $classIds))
            ->whereHas('role', fn($q) => $q->whereIn('name', ['eleve','etudiant']))
            ->pluck('id');
    }

    public function index()
    {
        $user = Auth::user();
        $etab = $user->etablissement;

        abort_if(!$etab, 403);

        
        $classes = $this->getTeacherClasses();

        
        $studentIds = $this->getTeacherStudentIds();

        $recentProgress = $studentIds->isEmpty()
            ? collect()
            : StudentProgress::whereIn('user_id', $studentIds)
                ->with(['user','lesson.module'])
                ->latest()
                ->limit(20)
                ->get();

        return view('enseignant.dashboard', compact('etab','classes','recentProgress'));
    }

    public function classes()
    {
        $classes = $this->getTeacherClasses()->loadCount('students');

        return view('enseignant.classes', compact('classes'));
    }

    public function students()
    {
        $studentIds = $this->getTeacherStudentIds();

        $students = $studentIds->isEmpty()
            ? collect()
            : User::whereIn('id', $studentIds)->paginate(30);

        return view('enseignant.students', compact('students'));
    }

    public function studentProgress(User $user)
    {
        
        $studentIds = $this->getTeacherStudentIds();
        abort_if(!$studentIds->contains($user->id), 403);

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
        
        abort_if($classe->enseignant_id !== Auth::id(), 403);

        $students = $classe->students()->with('badges')->get();
        $ids = $students->pluck('id');

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
            'user'     => $s,
            'progress' => $progressCounts[$s->id] ?? 0,
            'quizzes'  => $quizCounts[$s->id] ?? 0,
            'points'   => $s->points
        ]);

        return view('enseignant.class-stats', compact('classe','stats'));
    }

    public function classReport(Classe $classe)
    {
        
        abort_if($classe->enseignant_id !== Auth::id(), 403);

        $students = $classe->students()->with('badges')->get();
        $ids = $students->pluck('id');

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
            'user'      => $s,
            'progress'  => $progressCounts[$s->id] ?? 0,
            'quizzes'   => $quizCounts[$s->id] ?? 0,
            'avg_score' => round($avgScores[$s->id] ?? 0),
            'points'    => $s->points,
            'badges'    => $s->badges->count()
        ]);

        $etab = Auth::user()->etablissement;

        $pdf = Pdf::loadView('enseignant.report-pdf', compact('classe','stats','etab'));

        return $pdf->download("rapport-{$classe->name}-".now()->format('Y-m-d').".pdf");
    }
}
