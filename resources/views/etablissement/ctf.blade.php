@extends('layouts.app')
@section('title', 'Stats CTF — ' . $etab->name)
@section('page-title', '🚩 Challenges CTF — ' . $etab->name)

@section('content')

{{-- ── Stats globales ───────────────────────────────────────────── --}}
<div class="kr" style="margin-bottom:24px">

    <div class="kc kc-b">
        <div class="kv stat-blue">{{ $activeStudents }}</div>
        <div class="kl">Apprenants actifs</div>
    </div>

    <div class="kc kc-g">
        <div class="kv stat-green">{{ $totalSolved }}</div>
        <div class="kl">Flags trouvés</div>
    </div>

    <div class="kc kc-o">
        <div class="kv stat-orange">{{ $totalAttempts }}</div>
        <div class="kl">Tentatives totales</div>
    </div>

    <div class="kc" style="background:rgba(192,132,252,0.06);border:1px solid rgba(192,132,252,0.15)">
        <div class="kv" style="color:#a78bfa">{{ $challenges->count() }}</div>
        <div class="kl">Challenges disponibles</div>
    </div>

</div>

{{-- ── Taux de résolution par challenge ────────────────────────── --}}
<div class="cyber-card" style="margin-bottom:20px">
    <div style="font-size:13px;font-weight:700;margin-bottom:16px;color:var(--t2)">
        📊 Taux de résolution par challenge
    </div>

    @forelse($challengeStats as $stat)
    <div style="margin-bottom:14px">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
            <div style="display:flex;align-items:center;gap:8px">
                <span>{{ $stat['challenge']->typeIcon() }}</span>
                <span style="font-size:13px;font-weight:600">{{ $stat['challenge']->title }}</span>
                <span class="tag {{ match($stat['challenge']->difficulty) {
                    'facile'    => 'tag-g',
                    'moyen'     => 'tag-y',
                    'difficile' => 'tag-r',
                    default     => 'tag-y'
                } }}" style="font-size:10px">{{ ucfirst($stat['challenge']->difficulty) }}</span>
            </div>
            <div style="text-align:right;flex-shrink:0">
                <span style="font-size:13px;font-weight:700;color:{{ $stat['success_rate'] >= 60 ? 'var(--gr)' : ($stat['success_rate'] >= 30 ? 'var(--ye)' : 'var(--re)') }}">
                    {{ $stat['success_rate'] }}%
                </span>
                <span style="font-size:11px;color:var(--t3);margin-left:6px">
                    ({{ $stat['solvers'] }} / {{ $stat['attempts'] }} tentatives)
                </span>
            </div>
        </div>
        <div class="pb">
            <div class="pf {{ $stat['success_rate'] >= 60 ? 'pfg' : 'pf-gradient' }}"
                 style="width:{{ $stat['success_rate'] }}%"></div>
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:24px;color:var(--t3);font-size:13px">
        Aucune tentative enregistrée pour vos apprenants.
    </div>
    @endforelse
</div>

{{-- ── Classement interne de l'établissement ───────────────────── --}}
<div class="cyber-card" style="padding:0;overflow:hidden">

    <div style="padding:14px 20px;border-bottom:1px solid var(--bd);display:flex;justify-content:space-between;align-items:center">
        <div style="font-size:13px;font-weight:700">🏆 Classement interne</div>
        <div style="font-size:11px;color:var(--t3)">{{ $scores->count() }} apprenant(s) au classement</div>
    </div>

    @forelse($scores as $i => $row)
    <div style="display:grid;grid-template-columns:40px 1fr auto auto;gap:12px;align-items:center;padding:12px 20px;border-bottom:1px solid rgba(255,255,255,0.03)">

        {{-- Rang --}}
        <div style="text-align:center;font-size:14px;font-weight:900;color:{{ match($i) {
            0 => '#ffd700', 1 => '#aaaaaa', 2 => '#cd7f32', default => 'var(--t3)'
        } }}">
            {{ match($i) { 0 => '🥇', 1 => '🥈', 2 => '🥉', default => '#'.($i+1) } }}
        </div>

        {{-- Apprenant --}}
        <div style="display:flex;align-items:center;gap:10px;min-width:0">
            <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--gr),#009966);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:900;flex-shrink:0">
                {{ strtoupper(substr($row->user->name ?? '?', 0, 1)) }}
            </div>
            <div style="min-width:0">
                <div style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                    {{ $row->user->name ?? 'Utilisateur supprimé' }}
                </div>
                <div style="font-size:11px;color:var(--t3)">
                    {{ $row->solved_count }} challenge{{ $row->solved_count > 1 ? 's' : '' }} résolu{{ $row->solved_count > 1 ? 's' : '' }}
                </div>
            </div>
        </div>

        {{-- Challenges résolus / total --}}
        <div style="text-align:center">
            <div style="font-size:12px;color:var(--t3)">
                {{ $row->solved_count }} / {{ $challenges->count() }}
            </div>
            <div style="font-size:10px;color:var(--t3)">challenges</div>
        </div>

        {{-- Points --}}
        <div style="text-align:right">
            <div style="font-size:16px;font-weight:900;color:var(--ye)">{{ $row->total_points }}</div>
            <div style="font-size:10px;color:var(--t3)">points CTF</div>
        </div>

    </div>
    @empty
    <div style="text-align:center;padding:48px;color:var(--t3)">
        <div style="font-size:36px;margin-bottom:12px">🏁</div>
        <div style="font-size:14px">Aucun apprenant n'a encore tenté de challenge.</div>
        <div style="font-size:12px;margin-top:8px">Encouragez vos élèves à visiter la section CTF !</div>
    </div>
    @endforelse

</div>

{{-- ── Navigation ──────────────────────────────────────────────── --}}
<div style="margin-top:16px">
    <a href="{{ route('etablissement.dashboard') }}" style="font-size:13px;color:var(--t3)">← Retour au dashboard</a>
</div>

@endsection
