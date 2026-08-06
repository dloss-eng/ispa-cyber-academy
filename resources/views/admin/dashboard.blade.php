@extends('layouts.app')

{{--  Titre navigateur --}}
@section('title', 'Administration')

{{--  Titre page --}}
@section('page-title', ' Administration')

@section('content')

{{--  STATS PRINCIPALES --}}
<div class="kr">
    @foreach([
        ['total_users','👥','Utilisateurs','kc-g','var(--gr)'],
        ['total_students','🎓','Apprenants','kc-b','var(--bl)'],
        ['total_modules','📚','Modules','kc-o','var(--or)'],
        ['total_certificates','📜','Certificats','kc-y','var(--ye)']
    ] as $s)

        <div class="kc {{ $s[3] }}">
            <div class="kv stat-color" style="--stat-color: {{ $s[4] }}">
                {{ $stats[$s[0]] }}
            </div>
            <div class="kl">{{ $s[2] }}</div>
        </div>

    @endforeach
</div>

{{--  STATS SECONDAIRES --}}
<div class="kr kr-4">
    @foreach([
        ['total_etablissements','🏫','Établissements','kc-b','var(--bl)'],
        ['total_enseignants','👨‍🏫','Enseignants','kc-o','var(--or)'],
        ['avg_score','📊','Score moyen','kc-g','var(--gr)'],
        ['recent_logins','🔐','Connexions 24h','kc-y','var(--ye)']
    ] as $s)

        <div class="kc {{ $s[3] }}">
            <div class="kv stat-color stat-small" style="--stat-color: {{ $s[4] }}">
                {{ $stats[$s[0]] }}{{ $s[0] === 'avg_score' ? '%' : '' }}
            </div>
            <div class="kl">{{ $s[2] }}</div>
        </div>

    @endforeach
</div>

{{--  SECTION 2 COLONNES --}}
<div class="grid-2 mt-10">

    {{--  CLASSEMENT ÉTABLISSEMENTS --}}
    <div class="cyber-card dash-card">
        <div class="dash-title dash-title-yellow">
             CLASSEMENT ÉTABLISSEMENTS
        </div>

        @foreach($etabRanking as $i => $er)

            <div class="list-row">
                
                <span class="rank {{ $i < 3 ? 'rank-top' : 'rank-normal' }}">
                    {{ $i === 0 ? '🥇' : ($i === 1 ? '🥈' : ($i === 2 ? '🥉' : $i + 1)) }}
                </span>

                <div class="flex-1">
                    <div class="fw-700 fs-12">{{ $er['etab']->name }}</div>
                    <div class="fs-9 text-muted">
                        {{ $er['students_count'] }} apprenants · Moy: {{ $er['avg_score'] }}%
                    </div>
                </div>

                <span class="points">
                    {{ number_format($er['total_points']) }} pts
                </span>

            </div>

        @endforeach
    </div>

    {{--  TOP APPRENANTS --}}
    <div class="cyber-card dash-card">
        <div class="dash-title dash-title-green">
             TOP APPRENANTS
        </div>

        @foreach($topStudents as $i => $s)

            <div class="list-row small">

                <span class="rank small {{ $i < 3 ? 'rank-top' : 'rank-normal' }}">
                    {{ $i === 0 ? '🥇' : ($i === 1 ? '🥈' : ($i === 2 ? '🥉' : $i + 1)) }}
                </span>

                <div class="flex-1">
                    <div class="fw-600 fs-12">{{ $s->name }}</div>
                    <div class="fs-9 text-muted">
                        {{ $s->etablissement->name ?? '—' }}
                    </div>
                </div>

                <span class="points small">{{ $s->points }}</span>

            </div>

        @endforeach
    </div>

</div>

{{--  SECTION 2 --}}
<div class="grid-2 mt-10">

    {{--  DERNIÈRES INSCRIPTIONS --}}
    <div class="cyber-card dash-card">
        <div class="dash-title">
            DERNIÈRES INSCRIPTIONS
        </div>

        @foreach($recentUsers as $u)

            <div class="list-row small">

                <div class="avatar">
                    {{ strtoupper(substr($u->name, 0, 1)) }}
                </div>

                <div class="flex-1">
                    <div class="fw-600 fs-12">{{ $u->name }}</div>
                    <div class="fs-9 text-muted">{{ $u->email }}</div>
                </div>

                <span class="tag 
                    {{ $u->role->name === 'admin' ? 'tag-r' : ($u->role->name === 'etablissement' ? 'tag-b' : 'tag-g') }}">
                    {{ $u->role->display_name }}
                </span>

            </div>

        @endforeach
    </div>

    {{--  DERNIERS QUIZ --}}
    <div class="cyber-card dash-card">
        <div class="dash-title">
            DERNIERS QUIZ
        </div>

        @foreach($recentAttempts as $a)

            <div class="list-row small">

                <span class="quiz-icon">
                    {{ $a->passed ? '✅' : '❌' }}
                </span>

                <div class="flex-1">
                    <div class="fw-600 fs-12">{{ $a->user->name }}</div>
                    <div class="fs-9 text-muted">{{ $a->quiz?->title }}</div>
                </div>

                <span class="score {{ $a->passed ? 'success' : 'danger' }}">
                    {{ $a->percentage }}%
                </span>

            </div>

        @endforeach
    </div>

</div>

@endsection
