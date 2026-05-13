<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Challenge, ChallengeAttempt, Module};
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CtfController extends Controller
{
    // ── Liste ──────────────────────────────────────────────────────
    public function index()
    {
        $challenges = Challenge::withCount('attempts')
            ->orderBy('difficulty')
            ->orderBy('order')
            ->paginate(20);

        return view('admin.ctf.index', compact('challenges'));
    }

    // ── Formulaire création ────────────────────────────────────────
    public function create()
    {
        $modules = Module::where('is_published', true)->orderBy('order')->get();
        return view('admin.ctf.form', compact('modules'));
    }

    // ── Sauvegarder un nouveau challenge ──────────────────────────
    public function store(Request $request)
    {
        $data = $this->validateChallenge($request);

        $data['slug']  = Str::slug($data['title']) . '-' . Str::random(4);
        $data['hints'] = $this->parseHints($request);

        Challenge::create($data);

        return redirect()
            ->route('admin.ctf.index')
            ->with('success', 'Challenge CTF créé avec succès.');
    }

    // ── Formulaire édition ─────────────────────────────────────────
    public function edit(Challenge $challenge)
    {
        $modules = Module::where('is_published', true)->orderBy('order')->get();
        return view('admin.ctf.form', compact('challenge', 'modules'));
    }

    // ── Mettre à jour ──────────────────────────────────────────────
    public function update(Request $request, Challenge $challenge)
    {
        $data = $this->validateChallenge($request);
        $data['hints'] = $this->parseHints($request);

        $challenge->update($data);

        return redirect()
            ->route('admin.ctf.index')
            ->with('success', 'Challenge CTF modifié.');
    }

    // ── Supprimer ──────────────────────────────────────────────────
    public function destroy(Challenge $challenge)
    {
        $challenge->delete();
        return redirect()
            ->route('admin.ctf.index')
            ->with('success', 'Challenge supprimé.');
    }

    // ── Stats d'un challenge ───────────────────────────────────────
    public function stats(Challenge $challenge)
    {
        $attempts = ChallengeAttempt::where('challenge_id', $challenge->id)
            ->with('user:id,name')
            ->latest()
            ->paginate(30);

        $solvedCount = ChallengeAttempt::where('challenge_id', $challenge->id)
            ->where('is_correct', true)
            ->distinct('user_id')
            ->count();

        $totalAttempts = ChallengeAttempt::where('challenge_id', $challenge->id)->count();
        $successRate   = $totalAttempts > 0
            ? round(($solvedCount / $totalAttempts) * 100)
            : 0;

        return view('admin.ctf.stats', compact(
            'challenge', 'attempts', 'solvedCount', 'totalAttempts', 'successRate'
        ));
    }

    // ── Validation commune ─────────────────────────────────────────
    private function validateChallenge(Request $request): array
    {
        return $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'required|string',
            'scenario'     => 'required|string',
            'type'         => 'required|in:textual_analysis,flag_hunt',
            'flag'         => 'required|string|max:255',
            'points'       => 'required|integer|min:10|max:1000',
            'difficulty'   => 'required|in:facile,moyen,difficile',
            'module_id'    => 'nullable|exists:modules,id',
            'order'        => 'integer|min:0',
            'max_attempts' => 'integer|min:0',
            'is_published' => 'boolean',
        ]);
    }

    // ── Parser les indices depuis le formulaire ────────────────────
    private function parseHints(Request $request): array
    {
        $hints = [];
        $texts  = $request->input('hint_texts', []);
        $costs  = $request->input('hint_costs', []);

        foreach ($texts as $i => $text) {
            if (! empty(trim($text))) {
                $hints[] = [
                    'text'        => trim($text),
                    'cost_points' => (int) ($costs[$i] ?? 10),
                ];
            }
        }

        return $hints;
    }
}
