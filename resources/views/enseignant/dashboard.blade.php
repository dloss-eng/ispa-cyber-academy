@extends('layouts.app')

@section('title', 'Dashboard Enseignant')
@section('page-title', '👨‍🏫 Dashboard Enseignant')

@section('content')

{{-- 📊 Stats --}}
<div class="kr">

    <div class="kc kc-g">
        <div class="kv stat-green">
            {{ $classes->sum('students_count') }}
        </div>
        <div class="kl">Élèves</div>
    </div>

    <div class="kc kc-b">
        <div class="kv stat-blue">
            {{ $classes->count() }}
        </div>
        <div class="kl">Classes</div>
    </div>

</div>

{{-- 📋 Section classes --}}
<div class="section-title">
    📋 MES CLASSES
</div>

@foreach($classes as $c)

    <div class="cyber-card class-item">

        <div class="class-icon">📋</div>

        <div class="class-content">
            <div class="class-name">{{ $c->name }}</div>
            <div class="class-meta">{{ $c->students_count }} élèves</div>
        </div>

        <a href="{{ route('enseignant.classes.stats',$c) }}"
           class="btn-cyber btn-sm">
            📊 Stats
        </a>

        <a href="{{ route('enseignant.classes.report',$c) }}"
           class="btn-cyber-outline btn-sm">
            📄 PDF
        </a>

    </div>

@endforeach

{{-- 📖 Activité --}}
<div class="section-title">
    📖 ACTIVITÉ RÉCENTE
</div>

@foreach($recentProgress->take(10) as $p)

    <div class="cyber-card activity-item">

        <span class="activity-icon">
            {{ $p->status==='completed'?'✅':'📖' }}
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