@extends('layouts.app')
@section('title', 'Gestion CTF')
@section('page-title', '🚩 Gestion des Challenges CTF')

@section('content')

{{-- ── Actions ──── --}}
<div class="ctf-index-header">
    <div class="ctf-count">{{ $challenges->total() }} challenge(s)</div>
    <a href="{{ route('admin.ctf.create') }}" class="btn-cyber btn-sm">+ Nouveau challenge</a>
</div>

{{-- ── Alertes ──── --}}
@if(session('success'))
    <div class="ctf-alert-success">✅ {{ session('success') }}</div>
@endif

{{-- ── Liste ─── --}}
@forelse($challenges as $c)
<div class="cyber-card module-card ctf-item">

    {{-- Icône + ordre --}}
    <div class="ctf-icon">{{ $c->typeIcon() }}</div>

    {{-- Infos --}}
    <div class="ctf-info">
        <div class="ctf-title">{{ $c->title }}</div>
        <div class="ctf-meta">
            {{ $c->type === 'flag_hunt' ? '🚩 Flag Hunt' : '🔍 Analyse' }}
            · {{ $c->points }} pts
            · {{ $c->attempts_count }} tentative(s)
        </div>
    </div>

    {{-- Badges --}}
    <div class="ctf-badges">
        <span class="tag {{ match($c->difficulty) {
            'facile'    => 'tag-g',
            'moyen'     => 'tag-y',
            'difficile' => 'tag-r',
            default     => 'tag-y'
        } }}">{{ ucfirst($c->difficulty) }}</span>

        <span class="tag {{ $c->is_published ? 'tag-g' : 'tag-y' }}">
            {{ $c->is_published ? 'Publié' : 'Brouillon' }}
        </span>
    </div>

    {{-- Actions --}}
    <div class="ctf-actions">
        <a href="{{ route('admin.ctf.stats', $c) }}" class="btn-cyber btn-sm ctf-btn-stats">📊 Stats</a>
        <a href="{{ route('admin.ctf.edit', $c) }}" class="module-edit">Modifier</a>
        <form action="{{ route('admin.ctf.destroy', $c) }}" method="POST" class="inline-form"
              onsubmit="return confirm('Supprimer ce challenge et toutes ses tentatives ?')">
            @csrf
            @method('DELETE')
            <button class="btn-delete">Supprimer</button>
        </form>
    </div>

</div>
@empty
<div class="cyber-card ctf-empty">
    <div class="ctf-empty-icon">🚧</div>
    <div class="ctf-empty-title">Aucun challenge créé</div>
    <a href="{{ route('admin.ctf.create') }}" class="btn-cyber ctf-empty-btn">+ Créer le premier challenge</a>
</div>
@endforelse

<div class="pagination-wrapper">{{ $challenges->links() }}</div>

@endsection

@push('styles')
<style>
/* ── Header ──── */
.ctf-index-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.ctf-count { font-size: 12px; color: var(--t3); }

/* ── Alerte succès ─── */
.ctf-alert-success {
    background: rgba(0,229,160,0.1);
    border: 1px solid var(--gr);
    border-radius: 8px;
    padding: 12px 16px;
    margin-bottom: 16px;
    color: var(--gr);
    font-size: 13px;
}

/* ── Item challenge ─── */
.ctf-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 18px;
    margin-bottom: 10px;
}

/* ── Icône ─── */
.ctf-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: rgba(255,255,255,0.05);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

/* ── Infos ─── */
.ctf-info    { flex: 1; min-width: 0; }
.ctf-title   { font-size: 14px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.ctf-meta    { font-size: 11px; color: var(--t3); margin-top: 2px; }

/* ── Badges ───── */
.ctf-badges { display: flex; gap: 6px; flex-shrink: 0; }

/* ── Actions ───── */
.ctf-actions  { display: flex; gap: 8px; flex-shrink: 0; }
.ctf-btn-stats { font-size: 11px; }

/* ── État vide ──── */
.ctf-empty      { text-align: center; padding: 48px; }
.ctf-empty-icon { font-size: 48px; margin-bottom: 16px; }
.ctf-empty-title { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
.ctf-empty-btn  { margin-top: 16px; display: inline-flex; }
</style>
@endpush
