@extends('layouts.app')
@section('title', 'Stats — ' . $challenge->title)
@section('page-title', '📊 Stats CTF — ' . $challenge->title)

@section('content')

{{-- ── Résumé du challenge ──────────────────────────────────────── --}}
<div class="cyber-card" style="padding:0;overflow:hidden;margin-bottom:20px">
    <div style="background:{{ match($challenge->difficulty) {
        'facile'    => 'linear-gradient(135deg,rgba(0,229,160,0.1),rgba(0,229,160,0.03))',
        'moyen'     => 'linear-gradient(135deg,rgba(255,215,0,0.1),rgba(255,215,0,0.03))',
        'difficile' => 'linear-gradient(135deg,rgba(255,107,53,0.12),rgba(255,107,53,0.03))',
    } }};padding:18px 22px;border-bottom:1px solid var(--bd)">
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
            <div>
                <div style="font-size:16px;font-weight:800">{{ $challenge->typeIcon() }} {{ $challenge->title }}</div>
                <div style="font-size:12px;color:var(--t2);margin-top:4px">{{ $challenge->description }}</div>
            </div>
            <div style="display:flex;gap:8px">
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
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:0">
        @foreach([
            ['label' => 'Tentatives',       'value' => $totalAttempts, 'color' => 'var(--bl)'],
            ['label' => 'Joueurs ayant résolu', 'value' => $solvedCount,   'color' => 'var(--gr)'],
            ['label' => 'Taux de réussite', 'value' => $successRate.'%',  'color' => 'var(--ye)'],
            ['label' => 'Points max',       'value' => $challenge->points, 'color' => 'var(--t1)'],
        ] as $i => $stat)
        <div style="text-align:center;padding:18px 10px;{{ $i < 3 ? 'border-right:1px solid var(--bd)' : '' }}">
            <div style="font-size:22px;font-weight:900;color:{{ $stat['color'] }}">{{ $stat['value'] }}</div>
            <div style="font-size:11px;color:var(--t3);margin-top:4px">{{ $stat['label'] }}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- ── Barre de progression réussite ───────────────────────────── --}}
<div class="cyber-card" style="margin-bottom:20px">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
        <div style="font-size:13px;font-weight:700">Taux de réussite global</div>
        <div style="font-size:13px;font-weight:900;color:{{ $successRate >= 60 ? 'var(--gr)' : ($successRate >= 30 ? 'var(--ye)' : 'var(--re)') }}">
            {{ $successRate }}%
        </div>
    </div>
    <div class="pb">
        <div class="pf {{ $successRate >= 60 ? 'pfg' : 'pf-gradient' }}"
             style="width:{{ $successRate }}%;transition:width 0.6s ease"></div>
    </div>
    <div style="font-size:11px;color:var(--t3);margin-top:8px">
        {{ $solvedCount }} joueur(s) ont trouvé le flag sur {{ $totalAttempts }} tentative(s) totales
    </div>
</div>

{{-- ── Historique des tentatives ────────────────────────────────── --}}
<div class="cyber-card" style="padding:0;overflow:hidden">

    <div style="padding:14px 20px;border-bottom:1px solid var(--bd);display:flex;justify-content:space-between;align-items:center">
        <div style="font-size:13px;font-weight:700">Historique des tentatives</div>
        <div style="font-size:11px;color:var(--t3)">{{ $attempts->total() }} tentative(s)</div>
    </div>

    @forelse($attempts as $attempt)
    <div style="display:grid;grid-template-columns:1fr auto auto auto;gap:12px;align-items:center;padding:10px 20px;border-bottom:1px solid rgba(255,255,255,0.03)">

        {{-- Joueur --}}
        <div>
            <div style="font-size:13px;font-weight:600">{{ $attempt->user->name ?? 'Utilisateur supprimé' }}</div>
            <div style="font-family:monospace;font-size:11px;color:{{ $attempt->is_correct ? 'var(--gr)' : 'var(--re)' }};margin-top:2px">
                {{ $attempt->is_correct ? '✅' : '❌' }} {{ Str::limit($attempt->submitted_flag, 40) }}
            </div>
        </div>

        {{-- Indices utilisés --}}
        <div style="text-align:center">
            <div style="font-size:12px;color:{{ $attempt->hints_used > 0 ? 'var(--ye)' : 'var(--t3)' }}">
                💡 {{ $attempt->hints_used }}
            </div>
            <div style="font-size:10px;color:var(--t3)">indice(s)</div>
        </div>

        {{-- Points gagnés --}}
        <div style="text-align:center">
            <div style="font-size:12px;font-weight:700;color:{{ $attempt->points_earned > 0 ? 'var(--ye)' : 'var(--t3)' }}">
                {{ $attempt->points_earned > 0 ? '+' . $attempt->points_earned : '—' }}
            </div>
            <div style="font-size:10px;color:var(--t3)">pts</div>
        </div>

        {{-- Date --}}
        <div style="text-align:right">
            <div style="font-size:11px;color:var(--t3)">{{ $attempt->created_at->format('d/m H:i') }}</div>
            <div style="font-size:10px;color:var(--t3)">{{ $attempt->created_at->diffForHumans() }}</div>
        </div>

    </div>
    @empty
    <div style="text-align:center;padding:40px;color:var(--t3)">
        <div style="font-size:32px;margin-bottom:10px">🏁</div>
        <div style="font-size:13px">Aucune tentative pour ce challenge.</div>
    </div>
    @endforelse

</div>

{{-- ── Pagination ───────────────────────────────────────────────── --}}
<div class="pagination-wrapper">{{ $attempts->links() }}</div>

{{-- ── Navigation ──────────────────────────────────────────────── --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-top:16px">
    <a href="{{ route('admin.ctf.index') }}" style="font-size:13px;color:var(--t3)">← Retour aux challenges</a>
    <a href="{{ route('admin.ctf.edit', $challenge) }}" class="btn-cyber btn-sm">✏️ Modifier ce challenge</a>
</div>

@endsection
