@extends('layouts.app')

@section('title', 'Classes')
@section('page-title', '📋 Mes Classes')

@section('content')

{{-- ➕ bouton création --}}
<div class="classes-header">
    <a href="{{ route('etablissement.classes.create') }}"
       class="btn-cyber btn-sm">
        + Nouvelle classe
    </a>
</div>

{{-- 📋 Liste --}}
@foreach($classes as $c)

    <div class="cyber-card class-item">

        {{-- 📋 Icône --}}
        <div class="class-icon">📋</div>

        {{-- 📄 Infos --}}
        <div class="class-content">

            <div class="class-name">
                {{ $c->name }}
            </div>

            <div class="class-meta">
                {{ ucfirst($c->level) }} · {{ $c->year ?? '' }}
            </div>

        </div>

        {{-- 👥 Élèves --}}
        <span class="class-count">
            {{ $c->students_count }} élèves
        </span>

        {{-- 📊 Actions --}}
        <a href="{{ route('etablissement.classes.stats',$c) }}"
           class="class-link link-blue">
            📊 Stats
        </a>

        <a href="{{ route('etablissement.classes.edit',$c) }}"
           class="class-link link-orange">
            ✏️
        </a>

        <form action="{{ route('etablissement.classes.destroy',$c) }}"
              method="POST"
              class="inline-form"
              onsubmit="return confirm('Supprimer ?')">

            @csrf
            @method('DELETE')

            <button class="btn-delete">
                Supprimer
            </button>

        </form>

    </div>

@endforeach

@endsection