@extends('layouts.app')
@section('title', 'Challenges CTF')
@section('page-title', '🚩 Challenges CTF')

@section('content')

{{-- ── Stats rapides ────────────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:24px">
    <div class="cyber-card" style="text-align:center;padding:16px">
        <div style="font-size:24px;font-weight:900;color:var(--gr)">{{ $solvedCount }}</div>
        <div style="font-size:11px;color:var(--t3);margin-top:4px">Résolus</div>
    </div>
    <div class="cyber-card" style="text-align:center;padding:16px">
        <div style="font-size:24px;font-weight:900;color:var(--bl)">{{ $challenges->count() }}</div>
        <div style="font-size:11px;color:var(--t3);margin-top:4px">Total challenges</div>
    </div>
    <div class="cyber-card" style="text-align:center;padding:16px">
        <div style="font-size:24px;font-weight:900;color:var(--ye)">{{ $totalPoints }}</div>
        <div style="font-size:11px;color:var(--t3);margin-top:4px">Points CTF</div>
    </div>
</div>

{{-- ── Actions ──────────────────────────────────────────────────── --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
    <div style="font-size:12px;color:var(--t3)">
        {{ $solvedCount }} / {{ $challenges->count() }} challenges complétés
    </div>
    <a href="{{ route('ctf.leaderboard') }}" class="btn-cyber btn-sm">🏆 Classement</a>
</div>

{{-- ── Challenges ───────────────────────────────────────────────── --}}
@if($challenges->isEmpty())
    <div class="cyber-card" style="text-align:center;padding:48px">
        <div style="font-size:48px;margin-bottom:16px">🚧</div>
        <div style="font-size:16px;font-weight:700;margin-bottom:8px">Aucun challenge disponible</div>
        <div style="font-size:13px;color:var(--t3)">Les challenges CTF seront bientôt publiés.</div>
    </div>
@else
<div class="cgrid">
    @foreach($challenges as $c)
    <div class="cc" style="{{ $c->is_solved ? 'border-color:rgba(0,229,160,0.5)' : '' }}">

        <div class="cth" style="background:{{ match($c->difficulty) {
            'facile'    => 'linear-gradient(135deg,rgba(0,229,160,0.12),rgba(0,229,160,0.03))',
            'moyen'     => 'linear-gradient(135deg,rgba(255,215,0,0.12),rgba(255,215,0,0.03))',
            'difficile' => 'linear-gradient(135deg,rgba(255,107,53,0.15),rgba(255,107,53,0.04))',
            default     => 'rgba(255,255,255,0.03)'
        } }};position:relative;min-height:70px;display:flex;align-items:center;justify-content:center">
            @if($c->is_solved)
                <span style="position:absolute;top:8px;right:8px;background:var(--gr);color:#000;font-size:10px;font-weight:800;padding:2px 8px;border-radius:99px">✅ RÉSOLU</span>
            @endif
            <span style="font-size:32px">{{ $c->typeIcon() }}</span>
        </div>

        <div class="cbb">
            <div class="cn">{{ $c->title }}</div>
            <div class="cd">{{ Str::limit($c->description, 80) }}</div>

            <div style="display:flex;flex-wrap:wrap;gap:6px;margin:10px 0">
                <span class="tag {{ match($c->difficulty) {
                    'facile'    => 'tag-g',
                    'moyen'     => 'tag-y',
                    'difficile' => 'tag-r',
                    default     => 'tag-y'
                } }}">{{ ucfirst($c->difficulty) }}</span>
                <span style="font-size:11px;color:var(--t3);display:flex;align-items:center">🏆 {{ $c->points }} pts</span>
                <span style="font-size:11px;color:var(--t3);display:flex;align-items:center">
                    {{ $c->type === 'flag_hunt' ? '🚩 Flag Hunt' : '🔍 Analyse' }}
                </span>
            </div>

            @if($c->attempts_count > 0 && !$c->is_solved)
                <div style="font-size:11px;color:var(--ye);margin-bottom:8px">
                    ⚡ {{ $c->attempts_count }} tentative{{ $c->attempts_count > 1 ? 's' : '' }}
                </div>
            @endif

            @if($c->is_solved)
                <a href="{{ route('ctf.show', $c) }}" class="bcours course-btn-complete">✅ Résolu — Revoir</a>
            @elseif($c->attempts_count > 0)
                <a href="{{ route('ctf.show', $c) }}" class="bcours">▶️ Continuer</a>
            @else
                <a href="{{ route('ctf.show', $c) }}" class="bcours st">🚀 Relever le défi</a>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endif

@endsection
