<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Module, Lesson, Quiz, Question, Answer};
use App\Services\NotificationService; 
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ModuleController extends Controller
{
    // ============================
    // MODULES
    // ============================

    public function index()
    {
        $this->authorize('viewAny', Module::class);
        $modules = Module::withCount('lessons')->orderBy('order')->paginate(20);
        return view('admin.modules.index', compact('modules'));
    }

    public function create()
    {
        $this->authorize('create', Module::class);
        return view('admin.modules.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Module::class);
        $request->validate([
            'title'       => 'required|max:255',
            'description' => 'required',
            'level'       => 'required|in:lycee,universite,tous',
        ]);

        $module = Module::create([
            ...$request->only('title', 'description', 'level', 'duration_hours', 'order'),
            'slug'         => Str::slug($request->title),
            'is_published' => $request->boolean('is_published'),
        ]);

        if ($module->is_published) {
            NotificationService::newModulePublished($module->title, $module->level);
            NotificationService::newModuleForEtablissement($module->title, $module->level);
        }

        return redirect()->route('admin.modules.index')->with('success', 'Module créé.');
    }

    public function edit(Module $module)
    {
        $this->authorize('update', $module);
        $module->load(['lessons.quizzes.questions.answers']);
        return view('admin.modules.edit', compact('module'));
    }

    public function update(Request $request, Module $module)
    {
        $this->authorize('update', $module);

        $wasPublished = $module->is_published;

        $module->update([
            ...$request->only('title', 'description', 'level', 'duration_hours', 'order'),
            'slug'         => Str::slug($request->title),
            'is_published' => $request->boolean('is_published'),
        ]);

        if (! $wasPublished && $module->is_published) {
            NotificationService::newModulePublished($module->title, $module->level);
            NotificationService::newModuleForEtablissement($module->title, $module->level);
        }

        return redirect()->route('admin.modules.index')->with('success', 'Module modifié.');
    }

    public function destroy(Module $module)
    {
        $this->authorize('delete', $module);
        $module->delete();
        return redirect()->route('admin.modules.index')->with('success', 'Module supprimé.');
    }

    // ============================
    // LESSONS
    // ============================

    public function createLesson(Module $module)
    {
        $this->authorize('update', $module);
        return view('admin.modules.lesson-form', compact('module'));
    }

    // ✅ Convertit toute URL YouTube/Vimeo en URL embed propre
    private function normalizeVideoUrl(?string $url): ?string
    {
        if (!$url) return null;

        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }
        if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1];
        }
        return $url;
    }

    // ✅ Upload PDF vers Cloudinary (persistant sur Render)
    private function uploadPdfToCloudinary($file): array
    {
        $uploaded = Cloudinary::uploadFile($file->getRealPath(), [
            'folder'        => 'ispa/resources',
            'resource_type' => 'raw',
            'public_id'     => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)
                               . '_' . Str::random(6),
        ]);

        return [
            'url'  => $uploaded->getSecurePath(),
            'name' => $file->getClientOriginalName(),
        ];
    }

    public function storeLesson(Request $request, Module $module)
    {
        $this->authorize('update', $module);
        $request->validate([
            'title'       => 'required|max:255',
            'content'     => 'required',
            'video_file'  => 'nullable|mimes:mp4,webm,ogg|max:102400',
            'pdf_files.*' => 'nullable|mimes:pdf|max:10240',
        ]);
        $videoPath = null;
        if ($request->hasFile('video_file')) {
            $videoPath = $request->file('video_file')->store('videos', 'public');
        }
        $videoUrl = $videoPath
            ? asset('storage/' . $videoPath)
            : $this->normalizeVideoUrl($request->video_url);

        $lesson = $module->lessons()->create([
            ...$request->only('title', 'content', 'duration_minutes', 'order'),
            'video_url'    => $videoUrl,
            'slug'         => Str::slug($request->title) . '-' . Str::random(4),
            'is_published' => $request->boolean('is_published'),
        ]);

        // ✅ Upload PDFs sur Cloudinary au lieu du storage local
        if ($request->hasFile('pdf_files')) {
            foreach ($request->file('pdf_files') as $pdf) {
                $result = $this->uploadPdfToCloudinary($pdf);
                $lesson->resources()->create([
                    'title'     => $result['name'],
                    'file_path' => $result['url'],
                    'type'      => 'pdf',
                ]);
            }
        }
        return redirect()
            ->route('admin.modules.lessons.edit', [$module, $lesson])
            ->with('success', 'Leçon ajoutée. Vous pouvez maintenant ajouter un quiz.');
    }

    public function editLesson(Module $module, Lesson $lesson)
    {
        $this->authorize('update', $module);
        return view('admin.modules.lesson-form', compact('module', 'lesson'));
    }

    public function updateLesson(Request $request, Module $module, Lesson $lesson)
    {
        $this->authorize('update', $module);
        $request->validate([
            'pdf_files.*' => 'nullable|mimes:pdf|max:10240',
        ]);

        $lesson->update([
            ...$request->only('title', 'content', 'duration_minutes', 'order'),
            'video_url'    => $this->normalizeVideoUrl($request->video_url),
            'is_published' => $request->boolean('is_published'),
        ]);

        // ✅ Upload PDFs sur Cloudinary au lieu du storage local
        if ($request->hasFile('pdf_files')) {
            foreach ($request->file('pdf_files') as $pdf) {
                $result = $this->uploadPdfToCloudinary($pdf);
                $lesson->resources()->create([
                    'title'     => $result['name'],
                    'file_path' => $result['url'],
                    'type'      => 'pdf',
                ]);
            }
        }
        return redirect()->route('admin.modules.edit', $module)->with('success', 'Leçon modifiée.');
    }

    public function destroyLesson(Module $module, Lesson $lesson)
    {
        $this->authorize('update', $module);
        $lesson->delete();
        return redirect()->route('admin.modules.edit', $module)->with('success', 'Leçon supprimée.');
    }

    // ============================
    // QUIZ DE LEÇON
    // ============================

    public function createQuiz(Lesson $lesson)
    {
        $this->authorize('update', $lesson->module);
        return view('admin.modules.quiz-form', compact('lesson'));
    }

    public function storeQuiz(Request $request, Lesson $lesson)
    {
        $this->authorize('update', $lesson->module);
        $request->validate([
            'title'                          => 'required|string|max:255',
            'passing_score'                  => 'required|integer|min:0|max:100',
            'questions'                      => 'required|array|min:1',
            'questions.*.question_text'      => 'required|string|max:500',
            'questions.*.answers'            => 'required|array|min:2',
            'questions.*.answers.*.text'     => 'required|string|max:255',
        ]);
        $quiz = $lesson->quizzes()->create([
            'title'              => $request->title,
            'passing_score'      => $request->passing_score,
            'time_limit_minutes' => $request->time_limit_minutes ?? 15,
            'max_attempts'       => $request->max_attempts ?? 3,
            'is_published'       => $request->boolean('is_published'),
        ]);
        foreach ($request->questions as $qData) {
            $question = $quiz->questions()->create([
                'question_text' => $qData['question_text'],
                'type'          => $qData['type'] ?? 'qcm',
                'points'        => $qData['points'] ?? 1,
                'explanation'   => $qData['explanation'] ?? null,
            ]);
            foreach ($qData['answers'] as $aData) {
                $question->answers()->create([
                    'answer_text' => $aData['text'],
                    'is_correct'  => isset($aData['is_correct']) && $aData['is_correct'],
                ]);
            }
        }
        return redirect()
            ->route('admin.modules.edit', $lesson->module)
            ->with('success', 'Quiz créé avec succès.');
    }

    // ============================
    // QUIZ DE MODULE
    // ============================

    public function createModuleQuiz(Module $module)
    {
        $this->authorize('update', $module);
        $existingQuiz = Quiz::where('module_id', $module->id)->first();
        if ($existingQuiz) {
            return redirect()->route('admin.modules.quiz.edit', [$module, $existingQuiz]);
        }
        return view('admin.modules.quiz-form', compact('module'));
    }

    public function storeModuleQuiz(Request $request, Module $module)
    {
        $this->authorize('update', $module);
        $request->validate([
            'title'                          => 'required|string|max:255',
            'passing_score'                  => 'required|integer|min:0|max:100',
            'questions'                      => 'required|array|min:1',
            'questions.*.question_text'      => 'required|string|max:500',
            'questions.*.answers'            => 'required|array|min:2',
            'questions.*.answers.*.text'     => 'required|string|max:255',
        ]);
        $quiz = Quiz::create([
            'module_id'          => $module->id,
            'lesson_id'          => null,
            'title'              => $request->title,
            'passing_score'      => $request->passing_score,
            'time_limit_minutes' => $request->time_limit_minutes ?? 15,
            'max_attempts'       => $request->max_attempts ?? 3,
            'is_published'       => $request->boolean('is_published'),
        ]);
        foreach ($request->questions as $qData) {
            $question = $quiz->questions()->create([
                'question_text' => $qData['question_text'],
                'type'          => $qData['type'] ?? 'qcm',
                'points'        => $qData['points'] ?? 1,
                'explanation'   => $qData['explanation'] ?? null,
            ]);
            foreach ($qData['answers'] as $aData) {
                $question->answers()->create([
                    'answer_text' => $aData['text'],
                    'is_correct'  => isset($aData['is_correct']) && $aData['is_correct'],
                ]);
            }
        }
        return redirect()
            ->route('admin.modules.edit', $module)
            ->with('success', 'Quiz du module créé avec succès.');
    }

    public function editModuleQuiz(Module $module, Quiz $quiz)
    {
        $this->authorize('update', $module);
        $quiz->load('questions.answers');
        return view('admin.modules.quiz-edit', compact('quiz', 'module'));
    }

    public function updateModuleQuiz(Request $request, Module $module, Quiz $quiz)
    {
        $this->authorize('update', $module);
        $request->validate([
            'title'                          => 'required|string|max:255',
            'passing_score'                  => 'required|integer|min:0|max:100',
            'questions'                      => 'required|array|min:1',
            'questions.*.question_text'      => 'required|string|max:500',
            'questions.*.answers'            => 'required|array|min:2',
            'questions.*.answers.*.text'     => 'required|string|max:255',
        ]);
        $quiz->update([
            'title'              => $request->title,
            'passing_score'      => $request->passing_score,
            'time_limit_minutes' => $request->time_limit_minutes ?? 15,
            'max_attempts'       => $request->max_attempts ?? 3,
            'is_published'       => $request->boolean('is_published'),
        ]);
        foreach ($quiz->questions as $q) { $q->answers()->delete(); }
        $quiz->questions()->delete();
        foreach ($request->questions as $qData) {
            $question = $quiz->questions()->create([
                'question_text' => $qData['question_text'],
                'type'          => $qData['type'] ?? 'qcm',
                'points'        => $qData['points'] ?? 1,
                'explanation'   => $qData['explanation'] ?? null,
            ]);
            foreach ($qData['answers'] as $aData) {
                $question->answers()->create([
                    'answer_text' => $aData['text'],
                    'is_correct'  => isset($aData['is_correct']) && $aData['is_correct'],
                ]);
            }
        }
        return redirect()
            ->route('admin.modules.edit', $module)
            ->with('success', 'Quiz du module mis à jour.');
    }

    public function destroyModuleQuiz(Module $module, Quiz $quiz)
    {
        $this->authorize('update', $module);
        foreach ($quiz->questions as $q) { $q->answers()->delete(); }
        $quiz->questions()->delete();
        $quiz->delete();
        return redirect()
            ->route('admin.modules.edit', $module)
            ->with('success', 'Quiz du module supprimé.');
    }

    // ============================
    // QUIZ DE LEÇON — édition/suppression
    // ============================

    public function editQuiz(Quiz $quiz)
    {
        $this->authorize('update', $quiz->lesson->module);
        $lesson = $quiz->lesson;
        $quiz->load('questions.answers');
        return view('admin.modules.quiz-edit', compact('quiz', 'lesson'));
    }

    public function updateQuiz(Request $request, Quiz $quiz)
    {
        $this->authorize('update', $quiz->lesson->module);
        $request->validate([
            'title'                          => 'required|string|max:255',
            'passing_score'                  => 'required|integer|min:0|max:100',
            'questions'                      => 'required|array|min:1',
            'questions.*.question_text'      => 'required|string|max:500',
            'questions.*.answers'            => 'required|array|min:2',
            'questions.*.answers.*.text'     => 'required|string|max:255',
        ]);
        $quiz->update([
            'title'              => $request->title,
            'passing_score'      => $request->passing_score,
            'time_limit_minutes' => $request->time_limit_minutes ?? 15,
            'max_attempts'       => $request->max_attempts ?? 3,
            'is_published'       => $request->boolean('is_published'),
        ]);
        foreach ($quiz->questions as $q) { $q->answers()->delete(); }
        $quiz->questions()->delete();
        foreach ($request->questions as $qData) {
            $question = $quiz->questions()->create([
                'question_text' => $qData['question_text'],
                'type'          => $qData['type'] ?? 'qcm',
                'points'        => $qData['points'] ?? 1,
                'explanation'   => $qData['explanation'] ?? null,
            ]);
            foreach ($qData['answers'] as $aData) {
                $question->answers()->create([
                    'answer_text' => $aData['text'],
                    'is_correct'  => isset($aData['is_correct']) && $aData['is_correct'],
                ]);
            }
        }
        return redirect()
            ->route('admin.modules.edit', $quiz->lesson->module)
            ->with('success', 'Quiz mis à jour.');
    }

    public function destroyQuiz(Quiz $quiz)
    {
        $this->authorize('update', $quiz->lesson->module);
        $module = $quiz->lesson->module;
        foreach ($quiz->questions as $q) { $q->answers()->delete(); }
        $quiz->questions()->delete();
        $quiz->delete();
        return redirect()
            ->route('admin.modules.edit', $module)
            ->with('success', 'Quiz supprimé.');
    }

    // ============================
    // RESOURCES
    // ============================

    public function destroyResource(\App\Models\Resource $resource)
    {
        $this->authorize('delete', $resource->lesson->module);
        // ✅ Cloudinary : pas de suppression locale nécessaire
        $resource->delete();
        return back()->with('success', 'Fichier supprimé.');
    }
}
