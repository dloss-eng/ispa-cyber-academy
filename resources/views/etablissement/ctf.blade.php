@extends('layouts.app')
@section('title', 'Stats CTF — ' . $etab->name)
@section('page-title', '🚩 Challenges CTF — ' . $etab->name)

@section('content')

{{-- ── Stats globales ── --}}
<div class="kr ctf-stats-row">

    <div class="kc kc-b">
        <div class="kv">{{ $activeStudents }}</div>
        <div class="kl">Apprenants actifs</div>
    </div>

    <div class="kc kc-g">
        <div class="kv">{{ $totalSolved }}</div>
        <div class="kl">Flags trouvés</div>
    </div>

    <div class="kc kc-o">
        <div class="kv">{{ $totalAttempts }}</div>
        <div class="kl">Tentatives totales</div>
    </div>

    <div class="kc ctf-kc-purple">
        <div class="kv ctf-purple-val">{{ $challenges->count() }}</div>
        <div class="kl">Challenges disponibles</div>
    </div>

</div>

{{-- ── Grille des challenges ── --}}
<div class="ctf-section-label">📊 Taux de résolution par challenge</div>

<div class="ctf-grid">
    @forelse($challengeStats as $stat)

    <div class="ctf-card ctf-card-{{ $stat['challenge']->difficulty }}">

        {{-- Header --}}
        <div class="ctf-card-header">
            <span class="ctf-card-icon">{{ $stat['challenge']->typeIcon() }}</span>
            <span class="tag {{ match($stat['challenge']->difficulty) {
                'facile'    => 'tag-g',
                'moyen'     => 'tag-y',
                'difficile' => 'tag-r',
                default     => 'tag-y'
            } }}">{{ ucfirst($stat['challenge']->difficulty) }}</span>
        </div>

        {{-- Titre --}}
        <div class="ctf-card-title">{{ $stat['challenge']->title }}</div>

        {{-- Taux en grand --}}
        <div class="ctf-card-rate ctf-rate-{{ $stat['success_rate'] >= 60 ? 'ok' : ($stat['success_rate'] >= 30 ? 'mid' : 'low') }}">
            {{ $stat['success_rate'] }}%
        </div>

        {{-- Barre progression --}}
        <div class="pb ctf-pb">
            <div class="pf {{ $stat['success_rate'] >= 60 ? 'pfg' : 'pf-gradient' }}"
                 style="width:{{ $stat['success_rate'] }}%"></div>
        </div>

        {{-- Stats bas --}}
        <div class="ctf-card-meta">
            <span>✅ {{ $stat['solvers'] }} résolu{{ $stat['solvers'] > 1 ? 's' : '' }}</span>
            <span>⚡ {{ $stat['attempts'] }} tentative{{ $stat['attempts'] > 1 ? 's' : '' }}</span>
        </div>

    </div>

    @empty
    <div class="ctf-empty-grid">
        <div style="font-size:36px;margin-bottom:12px">🏁</div>
        <div>Aucune tentative enregistrée pour vos apprenants.</div>
    </div>
    @endforelse
</div>

{{-- ── Classement interne ── --}}
<div class="ctf-section-label" style="margin-top:28px">🏆 Classement interne</div>

<div class="cyber-card ctf-leaderboard">

    <div class="ctf-lb-header">
        <span>{{ $scores->count() }} apprenant(s) au classement</span>
    </div>

    @forelse($scores as $i => $row)
    <div class="ctf-lb-row">

        {{-- Rang --}}
        <div class="ctf-rank ctf-rank-{{ min($i, 3) }}">
            {{ match($i) { 0 => '🥇', 1 => '🥈', 2 => '🥉', default => '#'.($i+1) } }}
        </div>

        {{-- Avatar + Nom --}}
        <div class="ctf-lb-user">
            <div class="ctf-avatar">
                {{ strtoupper(substr($row->user->name ?? '?', 0, 1)) }}
            </div>
            <div>
                <div class="ctf-lb-name">{{ $row->user->name ?? 'Utilisateur supprimé' }}</div>
                <div class="ctf-lb-sub">
                    {{ $row->solved_count }} challenge{{ $row->solved_count > 1 ? 's' : '' }} résolu{{ $row->solved_count > 1 ? 's' : '' }}
                </div>
            </div>
        </div>

        {{-- Progression --}}
        <div class="ctf-lb-progress">
            <div class="ctf-lb-fraction">{{ $row->solved_count }} / {{ $challenges->count() }}</div>
            <div class="pb ctf-lb-pb">
                <div class="pf pfg" style="width:{{ $challenges->count() > 0 ? round(($row->solved_count / $challenges->count()) * 100) : 0 }}%"></div>
            </div>
        </div>

        {{-- Points --}}
        <div class="ctf-lb-pts">
            <div class="ctf-lb-pts-val">{{ $row->total_points }}</div>
            <div class="ctf-lb-pts-lbl">pts CTF</div>
        </div>

    </div>
    @empty
    <div class="ctf-lb-empty">
        <div>🏁</div>
        <div>Aucun apprenant n'a encore tenté de challenge.</div>
        <div>Encouragez vos élèves à visiter la section CTF !</div>
    </div>
    @endforelse

</div>

{{-- ── Navigation ── --}}
<div style="margin-top:16px">
    <a href="{{ route('etablissement.dashboard') }}" class="ctf-back-link">← Retour au dashboard</a>
</div>

@endsection

{{-- ═══════════════════════════════════
     CSS — CTF ÉTABLISSEMENT
════════════════════════════════════ --}}
<style>

/* Stats */
.ctf-stats-row  { margin-bottom: 24px; }
.ctf-kc-purple  { background: rgba(192,132,252,0.06); border: 1px solid rgba(192,132,252,0.15); }
.ctf-purple-val { color: #a78bfa; }

/* Label section */
.ctf-section-label {
    font-family: 'Orbitron', sans-serif;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: var(--t3);
    margin-bottom: 14px;
}

/* ── Grille challenges ── */
.ctf-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 8px;
}

@media (max-width: 900px) { .ctf-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px) { .ctf-grid { grid-template-columns: 1fr; } }

.ctf-card {
    background: var(--card);
    border: 1px solid var(--bd);
    border-radius: 16px;
    padding: 18px;
    transition: transform .2s, border-color .2s;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.ctf-card:hover { transform: translateY(-4px); border-color: rgba(0,229,160,.3); }

/* Bordure gauche selon difficulté */
.ctf-card-facile    { border-left: 3px solid var(--gr); }
.ctf-card-moyen     { border-left: 3px solid var(--ye); }
.ctf-card-difficile { border-left: 3px solid var(--re); }

.ctf-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.ctf-card-icon  { font-size: 24px; }
.ctf-card-title { font-size: 13px; font-weight: 700; color: var(--t); line-height: 1.3; }

/* Taux en grand */
.ctf-card-rate  { font-family: 'Orbitron', sans-serif; font-size: 28px; font-weight: 900; line-height: 1; }
.ctf-rate-ok    { color: var(--gr); }
.ctf-rate-mid   { color: var(--ye); }
.ctf-rate-low   { color: var(--re); }

.ctf-pb { margin: 2px 0; }

.ctf-card-meta {
    display: flex;
    justify-content: space-between;
    font-size: 11px;
    color: var(--t3);
}

.ctf-empty-grid {
    grid-column: 1 / -1;
    text-align: center;
    padding: 40px;
    color: var(--t3);
    font-size: 13px;
}

/* ── Classement ── */
.ctf-leaderboard { padding: 0; overflow: hidden; }

.ctf-lb-header {
    padding: 12px 20px;
    border-bottom: 1px solid var(--bd);
    font-size: 11px;
    color: var(--t3);
    text-align: right;
}

.ctf-lb-row {
    display: grid;
    grid-template-columns: 44px 1fr 140px 80px;
    gap: 12px;
    align-items: center;
    padding: 12px 20px;
    border-bottom: 1px solid rgba(255,255,255,0.03);
    transition: background .15s;
}
.ctf-lb-row:hover { background: rgba(255,255,255,.02); }

.ctf-rank   { text-align: center; font-size: 16px; font-weight: 900; color: var(--t3); }
.ctf-rank-0 { color: #ffd700; }
.ctf-rank-1 { color: #aaaaaa; }
.ctf-rank-2 { color: #cd7f32; }

.ctf-lb-user { display: flex; align-items: center; gap: 10px; min-width: 0; }
.ctf-avatar  { width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, var(--gr), #009966); display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 900; flex-shrink: 0; }
.ctf-lb-name { font-size: 13px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.ctf-lb-sub  { font-size: 11px; color: var(--t3); margin-top: 2px; }

.ctf-lb-progress  { min-width: 0; }
.ctf-lb-fraction  { font-size: 11px; color: var(--t3); margin-bottom: 4px; }
.ctf-lb-pb        { height: 4px !important; }

.ctf-lb-pts     { text-align: right; }
.ctf-lb-pts-val { font-size: 16px; font-weight: 900; color: var(--ye); line-height: 1; }
.ctf-lb-pts-lbl { font-size: 10px; color: var(--t3); margin-top: 2px; }

.ctf-lb-empty {
    text-align: center;
    padding: 40px;
    color: var(--t3);
    display: flex;
    flex-direction: column;
    gap: 8px;
    font-size: 13px;
}
.ctf-lb-empty > div:first-child { font-size: 36px; }

.ctf-back-link { font-size: 13px; color: var(--t3); text-decoration: none; }
.ctf-back-link:hover { color: var(--gr); }

</style>
