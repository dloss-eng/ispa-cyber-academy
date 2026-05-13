@extends('layouts.app')

@section('title', 'Cours')
@section('page-title', '📚 Cours disponibles (lecture seule)')

@section('content')

{{-- ℹ️ Info --}}
<div class="info-box">
    ℹ️ Consultez les modules. Seul le super admin peut modifier le contenu.
</div>

{{-- 📚 Liste des modules --}}
@foreach($modules as $m)

    <div class="cyber-card course-item">

        {{-- 📚 Icône --}}
        <div class="course-icon">📚</div>

        {{-- 📄 Contenu --}}
        <div class="course-content">

            {{-- 🏷️ Titre --}}
            <div class="course-title">
                {{ $m->title }}
            </div>

            {{-- 📖 Description --}}
            <div class="course-description">
                {{ $m->description }}
            </div>

            {{-- 📊 Meta --}}
            <div class="course-meta">
                {{ $m->lessons_count }} leçons · {{ $m->duration_hours }}h
            </div>

        </div>

    </div>

@endforeach

@endsection