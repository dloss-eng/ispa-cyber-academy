@extends('layouts.app')

@section('title', 'Progression')
@section('page-title', 'stats: ' . $user->name)

@section('content')

{{--  Retour --}}
<a href="{{ route('etablissement.students') }}" class="back-link">
    ← Retour
</a>

{{--  Carte utilisateur --}}
<div class="cyber-card student-header">

    {{-- Avatar --}}
    <div class="student-avatar">
        {{ strtoupper(substr($user->name,0,1)) }}
    </div>

    {{-- Infos --}}
    <div>
        <div class="student-name">{{ $user->name }}</div>
        <div class="student-meta">
            {{ $user->email }} · {{ $user->role_display }}
        </div>
    </div>

    {{-- Stats --}}
    <div class="student-stats">
        <div class="student-points">{{ $user->points }} pts</div>
        <div class="student-level">Niveau {{ $user->level }}</div>
    </div>

</div>

{{--  Leçons --}}
<div class="section-title"> LEÇONS SUIVIES</div>

@foreach($progress as $p)
<div class="cyber-card lesson-item">

    <span>{{ $p->status === 'completed' ? '✅' : '📖' }}</span>

    <div class="lesson-content">
        {{ $p->lesson->title ?? '—' }}

        <div class="lesson-module">
            {{ $p->lesson->module->title ?? '' }}
        </div>
    </div>

    <span class="tag {{ $p->status === 'completed' ? 'tag-g' : 'tag-y' }}">
        {{ $p->status === 'completed' ? 'Terminé' : 'En cours' }}
    </span>

</div>
@endforeach

{{--  Quiz --}}
<div class="section-title"> QUIZ PASSÉS</div>

@foreach($attempts as $a)
<div class="cyber-card quiz-item">

    <span>{{ $a->passed ? '✅' : '❌' }}</span>

    <div class="quiz-title">
        {{ $a->quiz->title ?? '—' }}
    </div>

    <span class="quiz-score {{ $a->passed ? 'success' : 'danger' }}">
        {{ $a->percentage }}%
    </span>

</div>
@endforeach

@endsection