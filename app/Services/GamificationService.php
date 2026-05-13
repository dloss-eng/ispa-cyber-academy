<?php
namespace App\Services;

use App\Models\{User, Badge, Certificate, Module, Quiz, QuizAttempt, StudentProgress};

class GamificationService
{
    // ============================
    // VÉRIFICATION GLOBALE
    // ============================

    public function checkBadges(User $user): void
    {
        $user->load('badges');

        // ── Badges existants ──
        $this->awardBadgeIf($user, 'premier-quiz',
            fn() => QuizAttempt::where('user_id', $user->id)->where('passed', true)->exists()
        );

        $this->awardBadgeIf($user, 'assidu',
            fn() => QuizAttempt::where('user_id', $user->id)->where('passed', true)->count() >= 10
        );

        $phishing = Module::where('slug', 'detection-phishing')->first();
        if ($phishing) {
            $this->awardBadgeIf($user, 'expert-phishing',
                fn() => $this->getProgressForModule($user, $phishing) >= 100
            );
        }

        $mobileMoney = Module::where('slug', 'securite-mobile-money')->first();
        if ($mobileMoney) {
            $this->awardBadgeIf($user, 'protecteur-mobile-money',
                fn() => $this->getProgressForModule($user, $mobileMoney) >= 100
            );
        }

        $total = Module::where('is_published', true)->count();
        if ($total > 0) {
            $completed = Module::where('is_published', true)->get()
                ->filter(fn($m) => $this->getProgressForModule($user, $m) >= 100)
                ->count();
            $this->awardBadgeIf($user, 'champion-cybersecurite',
                fn() => $completed >= $total
            );
        }
    }

    // ============================
    // BADGES PAR MODULE ← NOUVEAUX
    // ============================

    /**
     * Appelé après chaque leçon terminée ou quiz réussi.
     * Vérifie les 4 nouveaux badges liés à la progression d'un module.
     */
    public function checkModuleBadges(User $user, Module $module): void
    {
        $user->load('badges');

        $progress = $this->getProgressForModule($user, $module);

        // 🎓 Badge 1 : Module terminé à 100%
        $this->awardBadgeIf($user, 'module-complete',
            fn() => $progress >= 100
        );

        // ⚡ Badge 2 : Module à moitié (>= 50%)
        $this->awardBadgeIf($user, 'module-mi-parcours',
            fn() => $progress >= 50
        );

        // Quiz du module
        $lessonIds = $module->lessons()->pluck('id');
        $quizIds   = Quiz::whereIn('lesson_id', $lessonIds)->pluck('id');
        $totalQuizzes = $quizIds->count();

        if ($totalQuizzes > 0) {
            $passedQuizzes = QuizAttempt::where('user_id', $user->id)
                ->whereIn('quiz_id', $quizIds)
                ->where('passed', true)
                ->distinct('quiz_id')
                ->count('quiz_id');

            // 🏅 Badge 3 : Tous les quiz réussis
            $this->awardBadgeIf($user, 'quiz-master',
                fn() => $passedQuizzes >= $totalQuizzes
            );

            // 📚 Badge 4 : Moitié des quiz réussis
            $this->awardBadgeIf($user, 'quiz-apprenti',
                fn() => $passedQuizzes >= ceil($totalQuizzes / 2)
            );
        }
    }

    // ============================
    // ÉVÉNEMENTS
    // ============================

    /**
     * Appelé depuis CourseController::completeLesson()
     */
    public function onLessonComplete(User $user, \App\Models\Lesson $lesson): void
    {
        $user->addPoints(10);
        $module = $lesson->module ?? Module::find($lesson->module_id);

        if ($module) {
            $this->checkBadges($user);
            $this->checkModuleBadges($user, $module);
            $this->checkCertificate($user, $module);
        }
    }

    // ============================
    // CERTIFICATS
    // ============================

    public function checkCertificate(User $user, Module $module): ?Certificate
    {
        if ($this->getProgressForModule($user, $module) < 100) return null;

        $lessonIds = $module->lessons()->pluck('id');
        $quizIds   = Quiz::whereIn('lesson_id', $lessonIds)->pluck('id');

        foreach ($quizIds as $qid) {
            if (!QuizAttempt::where('user_id', $user->id)
                ->where('quiz_id', $qid)
                ->where('passed', true)
                ->exists()) {
                return null;
            }
        }

        $existing = Certificate::where('user_id', $user->id)
            ->where('module_id', $module->id)
            ->first();
        if ($existing) return $existing;

        $avg = QuizAttempt::where('user_id', $user->id)
            ->whereIn('quiz_id', $quizIds)
            ->where('passed', true)
            ->avg('percentage') ?? 0;

        return Certificate::create([
            'user_id'            => $user->id,
            'module_id'          => $module->id,
            'certificate_number' => Certificate::generateNumber(),
            'final_score'        => (int) $avg,
            'issued_at'          => now(),
        ]);
    }

    // ============================
    // HELPERS
    // ============================

    /**
     * Calcule le % de progression d'un user sur un module.
     */
    public function getProgressForModule(User $user, Module $module): float
    {
        $lessonIds = $module->lessons()->pluck('id');
        $total     = $lessonIds->count();
        if ($total === 0) return 0;

        $completed = StudentProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $lessonIds)
            ->where('status', 'completed')
            ->count();

        return round(($completed / $total) * 100);
    }

    protected function awardBadgeIf(User $user, string $slug, callable $condition): void
    {
        $badge = Badge::where('slug', $slug)->first();
        if (!$badge || $user->badges->contains('id', $badge->id)) return;

        if ($condition()) {
            $user->badges()->attach($badge->id, ['earned_at' => now()]);
            $user->load('badges'); // Recharger pour éviter les doublons
            NotificationService::badgeEarned($user, $badge->name, $badge->icon);
        }
    }
}
