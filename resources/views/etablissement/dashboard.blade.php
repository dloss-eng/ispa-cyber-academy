@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', '🏫 Dashboard')

@section('content')

{{-- 🏫 Header établissement --}}
<div class="etab-header">

    {{-- Logo ou icône --}}
    @if($etab->logo_path)
        <img src="{{ asset('storage/'.$etab->logo_path) }}"
             class="etab-logo">
    @else
        <div class="etab-icon">
            {{ $etab->type === 'lycee' ? '🏫' : '🎓' }}
        </div>
    @endif

    {{-- Infos --}}
    <div>
        <div class="etab-name">
            {{ $etab->name }}
        </div>

        <div class="etab-meta">
            {{ ucfirst($etab->type) }} · {{ $etab->city }}
        </div>
    </div>

</div>

{{-- 📊 Stats --}}
<div class="kr">

    <div class="kc kc-g">
        <div class="kv stat-green">{{ $studentCount }}</div>
        <div class="kl">Élèves/Étudiants</div>
    </div>

    <div class="kc kc-b">
        <div class="kv stat-blue">{{ $enseignantCount }}</div>
        <div class="kl">Enseignants</div>
    </div>

    <div class="kc kc-o">
        <div class="kv stat-orange">{{ $classes->count() }}</div>
        <div class="kl">Classes</div>
    </div>

</div>

{{-- 📌 Section --}}
<div class="section-title">
    ACTIVITÉ RÉCENTE
</div>

{{-- 📋 Activités --}}
@foreach($recentProgress->take(10) as $p)

    <div class="cyber-card activity-item">

        <span>
            {{ $p->status === 'completed' ? '✅' : '📖' }}
        </span>

        <span class="activity-user">
            {{ $p->user->name }}
        </span>

        <span class="activity-lesson">
            {{ $p->lesson->title ?? '' }}
        </span>

        <span class="activity-time">
            {{ $p->updated_at->diffForHumans() }}
        </span>

    </div>

@endforeach

@endsection