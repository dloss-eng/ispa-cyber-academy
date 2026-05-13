<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\{Module, Lesson, StudentProgress};
use App\Helpers\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    // ──────────────────────────────────────────────────────────────
    // LISTE DES MODULES
    // ──────────────────────────────────────────────────────────────

    public function index()
    {
        $user = Auth::user();

        // ✅ Filtrer selon le niveau de l'utilisateur
        $modules = Module::where('is_published', true)
            ->whereIn('level', $user->allowedModuleLevels())
            ->withCount('lessons as total_lessons')
            ->orderBy('order')
            ->get();

        $completedByModule = StudentProgress::where('user_id', $user->id)
            ->where('status', 'completed')
            ->join('lessons', 'student_progress.lesson_id', '=', 'lessons.id')
            ->selectRaw('lessons.module_id, COUNT(*) as completed_count')
            ->groupBy('lessons.module_id')
            ->pluck('completed_count', 'module_id');

        $modules->each(function ($m) use ($completedByModule) {
            $total     = $m->total_lessons;
            $completed = $completedByModule[$m->id] ?? 0;
            $m->user_progress = $total > 0 ? round(($completed / $total) * 100) : 0;
        });

        // ✅ Passer le niveau lisible à la vue pour affichage contextuel
        $userLevelLabel = $user->moduleLevelLabel();

        return view('courses.index', compact('modules', 'userLevelLabel'));
    }

    // ──────────────────────────────────────────────────────────────
    // DÉTAIL D'UN MODULE
    // ──────────────────────────────────────────────────────────────

    public function show(Module $module)
    {
        abort_if(! $module->is_published, 404);

        // ✅ Bloquer l'accès si le module ne correspond pas au niveau de l'utilisateur
        abort_if(
            ! in_array($module->level, Auth::user()->allowedModuleLevels()),
            403,
            'Ce module n\'est pas disponible pour votre niveau.'
        );

        $lessons  = $module->lessons()->orderBy('order')->get();
        $progress = StudentProgress::where('user_id', auth()->id())
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->pluck('status', 'lesson_id');

        $lessonsWithProgress = $lessons->map(function ($lesson) use ($progress) {
            $lesson->user_status = $progress[$lesson->id] ?? 'not_started';
            return $lesson;
        });

        $completedCount = $lessonsWithProgress->where('user_status', 'completed')->count();
        $totalCount     = $lessons->count();
        $moduleProgress = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;

        return view('courses.show', compact('module', 'lessonsWithProgress', 'moduleProgress'));
    }

    // ──────────────────────────────────────────────────────────────
    // PAGE D'UNE LEÇON
    // ──────────────────────────────────────────────────────────────

    public function lesson(Module $module, Lesson $lesson)
    {
        abort_if($lesson->module_id !== $module->id, 404);

        // ✅ Vérification niveau sur la leçon également
        abort_if(
            ! in_array($module->level, Auth::user()->allowedModuleLevels()),
            403,
            'Ce contenu n\'est pas disponible pour votre niveau.'
        );

        $lesson->load(['quizzes', 'resources']);
        $lesson->content = HtmlSanitizer::clean($lesson->content ?? '');

        $progress = StudentProgress::where('user_id', auth()->id())
            ->where('lesson_id', $lesson->id)
            ->first();

        $allLessons  = $module->lessons()->orderBy('order')->get();
        $allProgress = StudentProgress::where('user_id', auth()->id())
            ->whereIn('lesson_id', $allLessons->pluck('id'))
            ->pluck('status', 'lesson_id');

        $lessonsWithProgress = $allLessons->map(function ($l) use ($allProgress) {
            $l->user_status = $allProgress[$l->id] ?? 'not_started';
            return $l;
        });

        $completedCount = $lessonsWithProgress->where('user_status', 'completed')->count();
        $totalCount     = $allLessons->count();
        $moduleProgress = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;

        $currentIdx = $allLessons->search(fn($l) => $l->id === $lesson->id);
        $prevLesson = $currentIdx > 0 ? $allLessons[$currentIdx - 1] : null;
        $nextLesson = $currentIdx < $allLessons->count() - 1 ? $allLessons[$currentIdx + 1] : null;

        $quiz = $lesson->quizzes()->first();

        return view('courses.lesson', compact(
            'module', 'lesson', 'progress', 'quiz',
            'lessonsWithProgress', 'moduleProgress',
            'prevLesson', 'nextLesson'
        ));
    }

    // ──────────────────────────────────────────────────────────────
    // AJAX
    // ──────────────────────────────────────────────────────────────

    public function lessonAjax(Module $module, Lesson $lesson)
    {
        abort_if($lesson->module_id !== $module->id, 404);
        abort_if(
            ! in_array($module->level, Auth::user()->allowedModuleLevels()),
            403
        );

        $lesson->load(['quizzes', 'resources']);
        $lesson->content = HtmlSanitizer::clean($lesson->content ?? '');

        $progress = StudentProgress::where('user_id', auth()->id())
            ->where('lesson_id', $lesson->id)
            ->first();

        $allLessons = $module->lessons()->orderBy('order')->get();
        $currentIdx = $allLessons->search(fn($l) => $l->id === $lesson->id);
        $prevLesson = $currentIdx > 0 ? $allLessons[$currentIdx - 1] : null;
        $nextLesson = $currentIdx < $allLessons->count() - 1 ? $allLessons[$currentIdx + 1] : null;
        $quiz       = $lesson->quizzes()->first();

        return view('courses.lesson', compact(
            'module', 'lesson', 'progress', 'quiz', 'prevLesson', 'nextLesson'
        ));
    }

    // ──────────────────────────────────────────────────────────────
    // MARQUER UNE LEÇON COMME TERMINÉE
    // ──────────────────────────────────────────────────────────────

    public function completeLesson(Request $request, Module $module, Lesson $lesson)
    {
        abort_if($lesson->module_id !== $module->id, 404);
        abort_if(
            ! in_array($module->level, Auth::user()->allowedModuleLevels()),
            403
        );

        StudentProgress::firstOrCreate(
            ['user_id' => auth()->id(), 'lesson_id' => $lesson->id],
            ['status' => 'completed', 'completed_at' => now()]
        );

        app(\App\Services\GamificationService::class)->onLessonComplete(auth()->user(), $lesson);

        $total = $module->lessons()->count();
        $done  = StudentProgress::where('user_id', auth()->id())
            ->whereIn('lesson_id', $module->lessons()->pluck('id'))
            ->where('status', 'completed')
            ->count();
        $moduleProgress = $total > 0 ? round(($done / $total) * 100) : 0;

        return response()->json(['ok' => true, 'progress' => $moduleProgress]);
    }
}
