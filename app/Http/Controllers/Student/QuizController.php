<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\{Quiz, QuizAttempt};
use App\Services\{GamificationService, NotificationService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class QuizController extends Controller
{
    public function __construct(protected GamificationService $gamification) {}

    /**
     * Affiche le quiz. On stocke en session le timestamp de début côté serveur
     * pour ne plus dépendre du client (anti-triche).
     */
    public function show(Quiz $quiz)
    {
        $quiz->load(['questions.answers', 'lesson.module']);
        $user = Auth::user();
        $remaining = $quiz->remainingAttempts($user);
        $bestAttempt = QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->orderByDesc('percentage')
            ->first();

        if ($remaining <= 0) {
            return view('quiz.exhausted', compact('quiz', 'bestAttempt'));
        }

        // Démarrage côté serveur du chrono (anti-triche)
        session(['quiz_start_' . $quiz->id => now()->timestamp]);

        return view('quiz.show', compact('quiz', 'remaining', 'bestAttempt'));
    }

    /**
     * Soumet le quiz. Plusieurs durcissements :
     * - Lock idempotent (Cache::lock) → empêche double-submit / replay
     * - Validation stricte du payload
     * - Vérification que les IDs de réponses appartiennent VRAIMENT à la question
     *   (sinon on pouvait soumettre une bonne réponse d'une autre question)
     * - Calcul du temps depuis la session serveur (le client ne décide plus)
     * - PRG pattern : redirect après save → fini le bug "GET method not supported"
     *   quand l'utilisateur rafraîchit la page de résultat
     */
    public function submit(Request $request, Quiz $quiz)
    {
        $user = Auth::user();

        // Verrou anti double-submit (5 secondes)
        $lock = Cache::lock('quiz-submit-' . $user->id . '-' . $quiz->id, 5);
        if (! $lock->get()) {
            return redirect()->route('courses.lesson', [$quiz->lesson->module, $quiz->lesson])
                ->withErrors(['quiz' => 'Soumission déjà en cours, veuillez patienter.']);
        }

        try {
            if ($quiz->remainingAttempts($user) <= 0) {
                return redirect()->route('quiz.show', $quiz)
                    ->withErrors(['quiz' => 'Tentatives épuisées.']);
            }

            $quiz->load('questions.answers');

            // Validation stricte de la structure du payload
            $request->validate([
                'answers' => 'array',
                'answers.*' => 'nullable',
            ]);

            $answers = $request->input('answers', []);
            if (! is_array($answers)) {
                throw ValidationException::withMessages(['answers' => 'Format invalide.']);
            }

            $score = 0;
            $totalPoints = 0;
            $answersData = [];

            foreach ($quiz->questions as $question) {
                $totalPoints += $question->points;

                // IDs de réponses VALIDES pour cette question uniquement
                $validAnswerIds = $question->answers->pluck('id')->toArray();
                $correctIds = $question->correctAnswers->pluck('id')->toArray();

                $userAnswerRaw = $answers[$question->id] ?? null;

                // Normalisation + filtrage : on ne garde que les IDs qui appartiennent VRAIMENT
                // à cette question. Empêche d'envoyer l'ID d'une réponse d'une autre question.
                if ($question->type === 'choix_multiple') {
                    $userIds = is_array($userAnswerRaw) ? array_map('intval', $userAnswerRaw) : [];
                    $userIds = array_values(array_intersect($userIds, $validAnswerIds));
                    $isCorrect = ! empty($userIds)
                        && empty(array_diff($correctIds, $userIds))
                        && empty(array_diff($userIds, $correctIds));
                    $userAnswerStored = $userIds;
                } else {
                    $userId = is_array($userAnswerRaw) ? null : (int) $userAnswerRaw;
                    if ($userId !== null && ! in_array($userId, $validAnswerIds, true)) {
                        $userId = null; // ID forgé → on ignore
                    }
                    $isCorrect = $userId !== null && in_array($userId, $correctIds, true);
                    $userAnswerStored = $userId;
                }

                if ($isCorrect) {
                    $score += $question->points;
                }

                $answersData[$question->id] = [
                    'user_answer' => $userAnswerStored,
                    'correct' => $isCorrect,
                    'explanation' => $question->explanation,
                ];
            }

            $percentage = $totalPoints > 0 ? round(($score / $totalPoints) * 100) : 0;
            $passed = $percentage >= $quiz->passing_score;

            // Temps écoulé : on lit depuis la session serveur (anti-triche)
            // Fallback raisonnable si la session a expiré.
            $startTimestamp = session('quiz_start_' . $quiz->id);
            if ($startTimestamp) {
                $timeSpent = max(1, now()->timestamp - (int) $startTimestamp);
                // Cap : ne peut pas dépasser la limite du quiz + 30s de marge
                $timeSpent = min($timeSpent, ($quiz->time_limit_minutes * 60) + 30);
                $startedAt = \Carbon\Carbon::createFromTimestamp($startTimestamp);
            } else {
                $timeSpent = 0;
                $startedAt = now();
            }

            $attempt = QuizAttempt::create([
                'user_id' => $user->id,
                'quiz_id' => $quiz->id,
                'score' => $score,
                'total_points' => $totalPoints,
                'percentage' => $percentage,
                'passed' => $passed,
                'answers_data' => $answersData,
                'time_spent_seconds' => $timeSpent,
                'started_at' => $startedAt,
                'completed_at' => now(),
            ]);

            // Nettoyage du chrono session
            session()->forget('quiz_start_' . $quiz->id);

            $pointsEarned = $passed ? 25 + $percentage : intval($percentage / 10);
            $user->addPoints($pointsEarned);
            $this->gamification->checkBadges($user);
            $this->gamification->checkModuleBadges($user, $quiz->lesson->module); // ✅ ajout

            if ($passed) {
                $this->gamification->checkCertificate($user, $quiz->lesson->module);
                NotificationService::quizPassed($user, $quiz->title, $percentage, $pointsEarned);
            } else {
                NotificationService::quizFailed($user, $quiz->title, $percentage);
            }

            // PRG pattern : redirect au lieu de return view().
            // Fini le bug "GET method not supported for route quiz/X/submit"
            // quand l'utilisateur rafraîchit la page.
            return redirect()->route('quiz.result', $attempt);
        } finally {
            optional($lock)->release();
        }
    }

    /**
     * Page de résultat affichée après le redirect (PRG).
     * Sécurisée : un user ne peut voir que ses propres résultats.
     */
    public function result(QuizAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        $attempt->load('quiz.questions.answers', 'quiz.lesson.module');
        $quiz = $attempt->quiz;
        $answersData = $attempt->answers_data ?? [];
        $pointsEarned = $attempt->passed ? 25 + $attempt->percentage : intval($attempt->percentage / 10);

        return view('quiz.result', compact('attempt', 'quiz', 'answersData', 'pointsEarned'));
    }
}
