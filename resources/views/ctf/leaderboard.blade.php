@extends('layouts.app')
@section('title', 'Classement CTF')
@section('page-title', '🏆 Classement CTF')

@section('content')

{{-- ── Podium top 3 ─────────────────────────────────────────────── --}}
@if($scores->count() >= 3)
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:28px;align-items:end">

    {{-- 2ème place --}}
    <div class="cyber-card" style="text-align:center;padding:20px 14px;border-color:rgba(192,192,192,0.4)">
        <div style="font-size:32px;margin-bottom:6px">🥈</div>
        <div style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#aaa,#888);display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:900;margin:0 auto 10px">
            {{ strtoupper(substr($scores[1]->user->name ?? '?', 0, 1)) }}
        </div>
        <div style="font-size:13px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
            {{ $scores[1]->user->name ?? 'Inconnu' }}
        </div>
        <div style="font-size:20px;font-weight:900;color:#aaa;margin-top:6px">{{ $scores[1]->total_points }} pts</div>
        <div style="font-size:11px;color:var(--t3);margin-top:4px">{{ $scores[1]->solved_count }} résolu(s)</div>
    </div>

    {{-- 1ère place --}}
    <div class="cyber-card" style="text-align:center;padding:24px 14px;border-color:rgba(255,215,0,0.5);transform:translateY(-8px)">
        <div style="font-size:36px;margin-bottom:6px">🥇</div>
        <div style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#ffd700,#ffaa00);display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:900;margin:0 auto 10px;color:#000">
            {{ strtoupper(substr($scores[0]->user->name ?? '?', 0, 1)) }}
        </div>
        <div style="font-size:14px;font-weight:800;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
            {{ $scores[0]->user->name ?? 'Inconnu' }}
        </div>
        <div style="font-size:24px;font-weight:900;color:var(--ye);margin-top:6px">{{ $scores[0]->total_points }} pts</div>
        <div style="font-size:11px;color:var(--t3);margin-top:4px">{{ $scores[0]->solved_count }} résolu(s)</div>
    </div>

    {{-- 3ème place --}}
    <div class="cyber-card" style="text-align:center;padding:16px 14px;border-color:rgba(205,127,50,0.4)">
        <div style="font-size:28px;margin-bottom:6px">🥉</div>
        <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,#cd7f32,#a0522d);display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:900;margin:0 auto 10px">
            {{ strtoupper(substr($scores[2]->user->name ?? '?', 0, 1)) }}
        </div>
        <div style="font-size:13px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
            {{ $scores[2]->user->name ?? 'Inconnu' }}
        </div>
        <div style="font-size:18px;font-weight:900;color:#cd7f32;margin-top:6px">{{ $scores[2]->total_points }} pts</div>
        <div style="font-size:11px;color:var(--t3);margin-top:4px">{{ $scores[2]->solved_count }} résolu(s)</div>
    </div>

</div>
@endif

{{-- ── Tableau complet ──────────────────────────────────────────── --}}
<div class="cyber-card" style="padding:0;overflow:hidden">

    <div style="padding:16px 20px;border-bottom:1px solid var(--bd);display:flex;justify-content:space-between;align-items:center">
        <div style="font-size:13px;font-weight:700">Classement général</div>
        <div style="font-size:11px;color:var(--t3)">Top {{ $scores->count() }} joueurs</div>
    </div>

    @forelse($scores as $i => $row)
    <div style="display:flex;align-items:center;gap:14px;padding:12px 20px;border-bottom:1px solid rgba(255,255,255,0.03);
        {{ auth()->id() === $row->user_id ? 'background:rgba(75,123,255,0.06)' : '' }}">

        {{-- Rang --}}
        <div style="width:32px;text-align:center;font-size:14px;font-weight:900;color:{{ match($i) {
            0 => '#ffd700', 1 => '#aaaaaa', 2 => '#cd7f32', default => 'var(--t3)'
        } }};flex-shrink:0">
            {{ match($i) { 0 => '🥇', 1 => '🥈', 2 => '🥉', default => '#' . ($i + 1) } }}
        </div>

        {{-- Avatar --}}
        <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--gr),#009966);display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:900;flex-shrink:0">
            {{ strtoupper(substr($row->user->name ?? '?', 0, 1)) }}
        </div>

        {{-- Nom --}}
        <div style="flex:1;min-width:0">
            <div style="font-size:13px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                {{ $row->user->name ?? 'Utilisateur supprimé' }}
                @if(auth()->id() === $row->user_id)
                    <span style="font-size:10px;color:var(--bl);font-weight:600;margin-left:6px">(vous)</span>
                @endif
            </div>
            <div style="font-size:11px;color:var(--t3);margin-top:2px">
                {{ $row->solved_count }} challenge{{ $row->solved_count > 1 ? 's' : '' }} résolu{{ $row->solved_count > 1 ? 's' : '' }}
            </div>
        </div>

        {{-- Points --}}
        <div style="text-align:right;flex-shrink:0">
            <div style="font-size:16px;font-weight:900;color:var(--ye)">{{ $row->total_points }}</div>
            <div style="font-size:10px;color:var(--t3)">points</div>
        </div>

    </div>
    @empty
    <div style="text-align:center;padding:48px;color:var(--t3)">
        <div style="font-size:36px;margin-bottom:12px">🏁</div>
        <div style="font-size:14px">Aucun challenge résolu pour l'instant. Soyez le premier !</div>
    </div>
    @endforelse

</div>

<div style="margin-top:16px">
    <a href="{{ route('ctf.index') }}" style="font-size:13px;color:var(--t3)">← Retour aux challenges</a>
</div>

@endsection
