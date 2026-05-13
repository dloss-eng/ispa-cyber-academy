<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\{Challenge, ChallengeAttempt, User};
use App\Services\{GamificationService, NotificationService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CtfController extends Controller
{
    public function __construct(protected GamificationService $gamification) {}

    // ── Liste des challenges ───────────────────────────────────────
    public function index()
    {
        $user = Auth::user();

        $challenges = Challenge::where('is_published', true)
            ->orderBy('difficulty')
            ->orderBy('order')
            ->get()
            ->map(function ($c) use ($user) {
                $c->is_solved      = $c->isSolvedBy($user->id);
                $c->attempts_count = $c->attempts()->where('user_id', $user->id)->count();
                return $c;
            });

        $solvedCount = $challenges->where('is_solved', true)->count();
        $totalPoints = ChallengeAttempt::where('user_id', $user->id)
            ->where('is_correct', true)
            ->sum('points_earned');

        return view('ctf.index', compact('challenges', 'solvedCount', 'totalPoints'));
    }

    // ── Page d'un challenge ────────────────────────────────────────
    public function show(Challenge $challenge)
    {
        abort_if(! $challenge->is_published, 404);

        $user      = Auth::user();
        $isSolved  = $challenge->isSolvedBy($user->id);
        $remaining = $challenge->remainingAttempts($user->id);
        $hintsUsed = ChallengeAttempt::where('user_id', $user->id)
            ->where('challenge_id', $challenge->id)
            ->max('hints_used') ?? 0;

        $hints = $challenge->hints ?? [];

        $history = ChallengeAttempt::where('user_id', $user->id)
            ->where('challenge_id', $challenge->id)
            ->latest()
            ->take(10)
            ->get();

        return view('ctf.show', compact(
            'challenge', 'isSolved', 'remaining', 'hints', 'hintsUsed', 'history'
        ));
    }

    // ── Soumettre un flag ──────────────────────────────────────────
    public function submit(Request $request, Challenge $challenge)
    {
        abort_if(! $challenge->is_published, 404);

        $user = Auth::user();

        if ($challenge->isSolvedBy($user->id)) {
            return back()->with('info', 'Vous avez déjà résolu ce challenge !');
        }

        $remaining = $challenge->remainingAttempts($user->id);
        if ($remaining !== null && $remaining <= 0) {
            return back()->withErrors(['flag' => 'Nombre maximum de tentatives atteint.']);
        }

        $request->validate([
            'flag'       => 'required|string|max:255',
            'hints_used' => 'integer|min:0|max:10',
        ]);

        $submitted    = $request->input('flag');
        $hintsUsed    = (int) $request->input('hints_used', 0);
        $isCorrect    = $challenge->checkFlag($submitted);
        $pointsEarned = $isCorrect ? $challenge->pointsForSolve($hintsUsed) : 0;

        ChallengeAttempt::create([
            'user_id'        => $user->id,
            'challenge_id'   => $challenge->id,
            'submitted_flag' => $submitted,
            'is_correct'     => $isCorrect,
            'hints_used'     => $hintsUsed,
            'points_earned'  => $pointsEarned,
            'solved_at'      => $isCorrect ? now() : null,
        ]);

        if ($isCorrect) {
            $user->addPoints($pointsEarned);

            // ✅ CORRIGÉ — NotificationService::send() n'existe pas.
            //    On utilise badgeEarned() qui existe et correspond bien au contexte.
            NotificationService::badgeEarned(
                $user,
                "🚩 Challenge résolu : {$challenge->title} (+{$pointsEarned} pts)",
                '🚩'
            );

            $this->checkCtfBadges($user);

            return redirect()
                ->route('ctf.show', $challenge)
                ->with('success', "🎉 Bravo ! Flag correct ! Vous gagnez {$pointsEarned} points.");
        }

        return back()
            ->withErrors(['flag' => '❌ Flag incorrect. Réessayez !'])
            ->withInput();
    }

    // ── Révéler un indice (AJAX) ───────────────────────────────────
    public function revealHint(Request $request, Challenge $challenge)
    {
        abort_if(! $challenge->is_published, 404);

        $request->validate(['index' => 'required|integer|min:0']);
        $index = (int) $request->input('index');
        $hints = $challenge->hints ?? [];

        if (! isset($hints[$index])) {
            return response()->json(['error' => 'Indice introuvable.'], 404);
        }

        return response()->json([
            'hint' => $hints[$index]['text'],
            'cost' => $hints[$index]['cost_points'] ?? 10,
        ]);
    }

    // ── Classement CTF ────────────────────────────────────────────
    public function leaderboard()
    {
        $scores = ChallengeAttempt::where('is_correct', true)
            ->selectRaw('user_id, SUM(points_earned) as total_points, COUNT(*) as solved_count')
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->with('user:id,name,avatar')
            ->take(20)
            ->get();

        return view('ctf.leaderboard', compact('scores'));
    }

    // ── Vérification badges CTF ────────────────────────────────────
    private function checkCtfBadges(User $user): void
    {
        $solvedCount = ChallengeAttempt::where('user_id', $user->id)
            ->where('is_correct', true)
            ->distinct('challenge_id')
            ->count('challenge_id');

        if ($solvedCount >= 5) {
            $badge = \App\Models\Badge::where('slug', 'ctf-master')->first();
            if ($badge && ! $user->badges->contains('id', $badge->id)) {
                $user->badges()->attach($badge->id, ['earned_at' => now()]);
                // ✅ badge->icon peut être null — valeur par défaut sécurisée
                NotificationService::badgeEarned($user, $badge->name, $badge->icon ?? '🏆');
            }
        }
    }
}
