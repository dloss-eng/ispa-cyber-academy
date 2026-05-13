@extends('layouts.app')

@section('title', 'Badges élèves')
@section('page-title', '🏅 Badges de mes élèves')

@section('content')

@forelse($students as $s)

    <div class="cyber-card student-badge-card">

        {{-- 👤 Header --}}
        <div class="student-badge-header">

            <div class="student-badge-avatar">
                {{ strtoupper(substr($s->name,0,1)) }}
            </div>

            <div class="student-badge-name">
                {{ $s->name }}
            </div>

            <span class="tag tag-g student-badge-count">
                {{ $s->badges->count() }} badges
            </span>

        </div>

        {{-- 🏅 Liste badges --}}
        <div class="student-badge-list">

            @foreach($s->badges as $b)

                <div class="badge-item-mini">
                    <span>{{ $b->icon }}</span>
                    {{ $b->name }}
                </div>

            @endforeach

        </div>

    </div>

@empty

    <div class="empty-state">
        Aucun de vos élèves n'a encore obtenu de badges.
    </div>

@endforelse

@endsection