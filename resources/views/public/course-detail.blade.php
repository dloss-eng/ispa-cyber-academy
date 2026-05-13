@extends('layouts.landing')

@section('title', $module->title)

@section('content')

<div class="module-container">

    <div class="module-title">
        {{ $module->title }}
    </div>

    <div class="module-description">
        {{ $module->description }}
    </div>

    @foreach($module->lessons as $i => $l)
        <div class="cyber-card lesson-card">

            <div class="lesson-index">
                {{ $i + 1 }}
            </div>

            <div class="lesson-title">
                {{ $l->title }}
            </div>

            <span class="lesson-duration">
                {{ $l->duration_minutes }} min
            </span>

        </div>
    @endforeach

    <a href="{{ route('login') }}" class="btn-cyber module-btn">
        Se connecter pour commencer
    </a>

</div>

@endsection