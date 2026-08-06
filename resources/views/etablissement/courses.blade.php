@extends('layouts.app')

@section('title', 'Cours')
@section('page-title', ' Cours disponibles')

@section('content')

{{-- ℹ Info --}}
<div class="info-box" style="margin-bottom:24px">
    ℹ️ Consultez les modules. Seul le super admin peut modifier le contenu.
</div>

{{--  Stats rapides --}}
<div style="display:flex;gap:20px;margin-bottom:28px;flex-wrap:wrap">
    <div class="cyber-card" style="padding:14px 20px;flex:1;min-width:120px;text-align:center">
        <div style="font-size:22px;font-weight:900;color:var(--gr)">{{ $modules->count() }}</div>
        <div style="font-size:11px;color:var(--t3);margin-top:2px">Modules</div>
    </div>
    <div class="cyber-card" style="padding:14px 20px;flex:1;min-width:120px;text-align:center">
        <div style="font-size:22px;font-weight:900;color:var(--bl)">{{ $modules->sum('lessons_count') }}</div>
        <div style="font-size:11px;color:var(--t3);margin-top:2px">Leçons</div>
    </div>
    <div class="cyber-card" style="padding:14px 20px;flex:1;min-width:120px;text-align:center">
        <div style="font-size:22px;font-weight:900;color:var(--ye)">100%</div>
        <div style="font-size:11px;color:var(--t3);margin-top:2px">Gratuit</div>
    </div>
</div>

{{--  Grille des modules --}}
<div class="etab-courses-grid">

    @php
    $colors = ['green','blue','orange','red','yellow','purple'];
    $icons  = ['🛡️','🔑','📱','🎣','💳','🌐','🔒','⚠️','📧','🕵️'];
    @endphp

    @foreach($modules as $i => $m)

    @php $color = $colors[$i % count($colors)]; @endphp

    <div class="etab-module-card">

        {{-- Bande colorée + icône --}}
        <div class="etab-card-top etab-card-{{ $color }}">

            {{-- Badge niveau --}}
            <span class="etab-level-badge">
                {{ match($m->level ?? 'tous') {
                    'lycee'      => '🏫 Lycée',
                    'universite' => '🎓 Université',
                    default      => '🌐 Tout niveau',
                } }}
            </span>

            {{-- Numéro --}}
            <span class="etab-module-num">M0{{ $i + 1 }}</span>

            {{-- Icône --}}
            <span style="font-size:52px">{{ $icons[$i % count($icons)] }}</span>
        </div>

        {{-- Corps --}}
        <div class="etab-card-body">
            <div class="etab-card-title">{{ $m->title }}</div>
            <p class="etab-card-desc">{{ Str::limit($m->description, 100) }}</p>

            <div class="etab-card-meta">
                <span>✏️ {{ $m->lessons_count }} leçons</span>
                <span>⏱️ {{ $m->duration_hours }}h</span>
                <span>👤 Tout niveau</span>
            </div>
        </div>

        {{-- Séparateur --}}
        <div class="etab-card-sep"></div>

        {{-- Footer --}}
        <div class="etab-card-footer">
            <span class="etab-card-price">🎓 Gratuit</span>
            
        </div>

    </div>

    @endforeach

</div>

@endsection
