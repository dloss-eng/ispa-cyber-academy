@extends('layouts.app')
@section('title', 'Stats — ' . $challenge->title)
@section('page-title', ' Stats CTF — ' . $challenge->title)

@section('content')

{{-- ── Résumé du challenge ───── --}}
<div class="cyber-card ctf-summary">

    <div class="ctf-summary-header ctf-diff-{{ $challenge->difficulty }}">
        <div class="ctf-summary-top">
            <div>
                <div class="ctf-summary-title">{{ $challenge->typeIcon() }} {{ $challenge->title }}</div>
                <div class="ctf-summary-desc">{{ $challenge->description }}</div>
            </div>
            <div class="ctf-summary-badges">
                <span class="tag {{ match($challenge->difficulty) {
                    'facile'    => 'tag-g',
                    'moyen'     => 'tag-y',
                    'difficile' => 'tag-r',
                    default     => 'tag-y'
                } }}">{{ ucfirst($challenge->difficulty) }}</span>
                <span class="tag {{ $challenge->is_published ? 'tag-g' : 'tag-y' }}">
                    {{ $challenge->is_published ? 'Publié' : 'Brouillon' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Métriques clés --}}
    <div class="ctf-metrics">
        <div class="ctf-metric ctf-metric-border">
            <div class="ctf-metric-value ctf-color-bl">{{ $totalAttempts }}</div>
            <div class="ctf-metric-label">Tentatives</div>
        </div>
        <div class="ctf-metric ctf-metric-border">
            <div class="ctf-metric-value ctf-color-gr">{{ $solvedCount }}</div>
            <div class="ctf-metric-label">Joueurs ayant résolu</div>
        </div>
        <div class="ctf-metric ctf-metric-border">
            <div class="ctf-metric-value ctf-color-ye">{{ $successRate }}%</div>
            <div class="ctf-metric-label">Taux de réussite</div>
        </div>
        <div class="ctf-metric">
            <div class="ctf-metric-value ctf-color-t1">{{ $challenge->points }}</div>
            <div class="ctf-metric-label">Points max</div>
        </div>
    </div>
</div>

{{-- ── Barre de progression réussite ──── --}}
<div class="cyber-card ctf-progress-card">
    <div class="ctf-progress-header">
        <div class="ctf-progress-title">Taux de réussite global</div>
        <div class="ctf-progress-rate {{ $successRate >= 60 ? 'ctf-color-gr' : ($successRate >= 30 ? 'ctf-color-ye' : 'ctf-color-re') }}">
            {{ $successRate }}%
        </div>
    </div>
    <div class="pb">
        <div class="pf {{ $successRate >= 60 ? 'pfg' : 'pf-gradient' }}"
             style="width:{{ $successRate }}%;transition:width 0.6s ease"></div>
    </div>
    <div class="ctf-progress-info">
        {{ $solvedCount }} joueur(s) ont trouvé le flag sur {{ $totalAttempts }} tentative(s) totales
    </div>
</div>

{{-- ── Historique des tentatives ──── --}}
<div class="cyber-card ctf-table-card">

    <div class="ctf-table-header">
        <div class="ctf-table-title">Historique des tentatives</div>
        <div class="ctf-table-count">{{ $attempts->total() }} tentative(s)</div>
    </div>

    @forelse($attempts as $attempt)
    <div class="ctf-attempt-row">

        {{-- Joueur --}}
        <div>
            <div class="ctf-attempt-name">{{ $attempt->user->name ?? 'Utilisateur supprimé' }}</div>
            <div class="ctf-attempt-flag {{ $attempt->is_correct ? 'ctf-color-gr' : 'ctf-color-re' }}">
                {{ $attempt->is_correct ? '✅' : '❌' }} {{ Str::limit($attempt->submitted_flag, 40) }}
            </div>
        </div>

        {{-- Indices utilisés --}}
        <div class="ctf-attempt-center">
            <div class="{{ $attempt->hints_used > 0 ? 'ctf-color-ye' : 'ctf-color-t3' }} ctf-sm">
                💡 {{ $attempt->hints_used }}
            </div>
            <div class="ctf-xs ctf-color-t3">indice(s)</div>
        </div>

        {{-- Points gagnés --}}
        <div class="ctf-attempt-center">
            <div class="ctf-sm ctf-fw-bold {{ $attempt->points_earned > 0 ? 'ctf-color-ye' : 'ctf-color-t3' }}">
                {{ $attempt->points_earned > 0 ? '+' . $attempt->points_earned : '—' }}
            </div>
            <div class="ctf-xs ctf-color-t3">pts</div>
        </div>

        {{-- Date --}}
        <div class="ctf-attempt-right">
            <div class="ctf-xs ctf-color-t3">{{ $attempt->created_at->format('d/m H:i') }}</div>
            <div class="ctf-xs ctf-color-t3">{{ $attempt->created_at->diffForHumans() }}</div>
        </div>

    </div>
    @empty
    <div class="ctf-empty-attempts">
        <div class="ctf-empty-icon">🏁</div>
        <div class="ctf-empty-text">Aucune tentative pour ce challenge.</div>
    </div>
    @endforelse

</div>

{{-- ── Pagination ─── --}}
<div class="pagination-wrapper">{{ $attempts->links() }}</div>

{{-- ── Navigation ───── --}}
<div class="ctf-nav">
    <a href="{{ route('admin.ctf.index') }}" class="ctf-nav-back">← Retour aux challenges</a>
    <a href="{{ route('admin.ctf.edit', ['ctf' => $challenge->id]) }}" class="btn-cyber btn-sm">Modifier ce challenge</a>
</div>

@endsection

@push('styles')
<style>
/* ── Résumé ──── */
.ctf-summary        { padding: 0; overflow: hidden; margin-bottom: 20px; }
.ctf-summary-header { padding: 18px 22px; border-bottom: 1px solid var(--bd); }

.ctf-diff-facile    { background: linear-gradient(135deg,rgba(0,229,160,0.1),rgba(0,229,160,0.03)); }
.ctf-diff-moyen     { background: linear-gradient(135deg,rgba(255,215,0,0.1),rgba(255,215,0,0.03)); }
.ctf-diff-difficile { background: linear-gradient(135deg,rgba(255,107,53,0.12),rgba(255,107,53,0.03)); }

.ctf-summary-top    { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
.ctf-summary-title  { font-size: 16px; font-weight: 800; }
.ctf-summary-desc   { font-size: 12px; color: var(--t2); margin-top: 4px; }
.ctf-summary-badges { display: flex; gap: 8px; }

/* ── Métriques ─── */
.ctf-metrics       { display: grid; grid-template-columns: repeat(4, 1fr); }
.ctf-metric        { text-align: center; padding: 18px 10px; }
.ctf-metric-border { border-right: 1px solid var(--bd); }
.ctf-metric-value  { font-size: 22px; font-weight: 900; }
.ctf-metric-label  { font-size: 11px; color: var(--t3); margin-top: 4px; }

/* ── Couleurs ─── */
.ctf-color-bl { color: var(--bl); }
.ctf-color-gr { color: var(--gr); }
.ctf-color-ye { color: var(--ye); }
.ctf-color-re { color: var(--re); }
.ctf-color-t1 { color: var(--t1); }
.ctf-color-t3 { color: var(--t3); }

/* ── Barre de progression ─── */
.ctf-progress-card   { margin-bottom: 20px; }
.ctf-progress-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
.ctf-progress-title  { font-size: 13px; font-weight: 700; }
.ctf-progress-rate   { font-size: 13px; font-weight: 900; }
.ctf-progress-info   { font-size: 11px; color: var(--t3); margin-top: 8px; }

/* ── Tableau tentatives ─── */
.ctf-table-card   { padding: 0; overflow: hidden; }
.ctf-table-header { padding: 14px 20px; border-bottom: 1px solid var(--bd); display: flex; justify-content: space-between; align-items: center; }
.ctf-table-title  { font-size: 13px; font-weight: 700; }
.ctf-table-count  { font-size: 11px; color: var(--t3); }

/* ── Ligne tentative ──── */
.ctf-attempt-row   { display: grid; grid-template-columns: 1fr auto auto auto; gap: 12px; align-items: center; padding: 10px 20px; border-bottom: 1px solid rgba(255,255,255,0.03); }
.ctf-attempt-name  { font-size: 13px; font-weight: 600; }
.ctf-attempt-flag  { font-family: monospace; font-size: 11px; margin-top: 2px; }
.ctf-attempt-center { text-align: center; }
.ctf-attempt-right  { text-align: right; }

/* ── Typographie utilitaires ─── */
.ctf-sm      { font-size: 12px; }
.ctf-xs      { font-size: 10px; }
.ctf-fw-bold { font-weight: 700; }

/* ── État vide ─── */
.ctf-empty-attempts { text-align: center; padding: 40px; color: var(--t3); }
.ctf-empty-icon     { font-size: 32px; margin-bottom: 10px; }
.ctf-empty-text     { font-size: 13px; }

/* ── Navigation ── */
.ctf-nav      { display: flex; justify-content: space-between; align-items: center; margin-top: 16px; }
.ctf-nav-back { font-size: 13px; color: var(--t3); }
</style>
@endpush
