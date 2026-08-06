@extends('layouts.app')

@section('title', 'Mes Classes')
@section('page-title', ' Mes Classes')

@section('content')

@foreach($classes as $c)

    <div class="cyber-card class-item">

        {{--  Icône --}}
        <div class="class-icon">
            📋
        </div>

        {{--  Infos --}}
        <div class="class-content">

            <div class="class-name">
                {{ $c->name }}
            </div>

            <div class="class-meta">
                {{ $c->students_count }} élèves
            </div>

        </div>

        {{--  Actions --}}
        <a href="{{ route('enseignant.classes.stats',$c) }}"
           class="class-link link-blue">
             Stats
        </a>

        <a href="{{ route('enseignant.classes.report',$c) }}"
           class="class-link link-orange">
            📄 PDF
        </a>

    </div>

@endforeach

@endsection