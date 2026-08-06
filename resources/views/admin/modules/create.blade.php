@extends('layouts.app')

{{--  Titre navigateur --}}
@section('title', isset($module) ? 'Modifier' : 'Nouveau module')

{{--  Titre page --}}
@section('page-title', isset($module) ? ' Modifier' : ' Nouveau module')

@section('content')

{{--  Retour --}}
<a href="{{ route('admin.modules.index') }}" class="back-link">
    ← Retour
</a>

{{--  FORMULAIRE MODULE --}}
<div class="cyber-card module-form-card">

    <form method="POST"
          action="{{ isset($module) ? route('admin.modules.update', $module) : route('admin.modules.store') }}">

        @csrf
        @if(isset($module)) @method('PUT') @endif

        {{--  Ligne : titre + niveau --}}
        <div class="form-grid-2">

            <div>
                <label class="fl no-margin-top">Titre</label>
                <input type="text" name="title"
                       value="{{ old('title', $module->title ?? '') }}"
                       required class="fi">
            </div>

            <div>
                <label class="fl no-margin-top">Niveau</label>
                <select name="level" class="fi">
                    <option value="tous">Tous</option>
                    <option value="lycee" {{ old('level', $module->level ?? '') === 'lycee' ? 'selected' : '' }}>Lycée</option>
                    <option value="universite" {{ old('level', $module->level ?? '') === 'universite' ? 'selected' : '' }}>Université</option>
                </select>
            </div>

        </div>

        {{--  Description --}}
        <label class="fl">Description</label>
        <textarea name="description" rows="3" required class="fi">
            {{ old('description', $module->description ?? '') }}
        </textarea>

        {{--  Ligne : durée + ordre --}}
        <div class="form-grid-2">

            <div>
                <label class="fl">Durée (h)</label>
                <input type="number" name="duration_hours"
                       value="{{ old('duration_hours', $module->duration_hours ?? 2) }}"
                       class="fi">
            </div>

            <div>
                <label class="fl">Ordre</label>
                <input type="number" name="order"
                       value="{{ old('order', $module->order ?? 0) }}"
                       class="fi">
            </div>

        </div>

        {{--  Checkbox publication --}}
        <label class="checkbox-inline">
            <input type="checkbox"
                   name="is_published"
                   value="1"
                   {{ old('is_published', $module->is_published ?? false) ? 'checked' : '' }}
                   class="checkbox-input">
            Publié
        </label>

        {{--  Submit --}}
        <button type="submit" class="btn-lg">
            {{ isset($module) ? 'Mettre à jour' : 'Créer' }}
        </button>

    </form>
</div>

{{--  LISTE DES LEÇONS --}}
@if(isset($module))

    {{--  Header --}}
    <div class="lessons-header">
        <div class="lessons-title">
            📖 LEÇONS
        </div>

        <a href="{{ route('admin.modules.lessons.create', $module) }}"
           class="btn-cyber btn-sm">
            + Leçon
        </a>
    </div>

    {{--  Liste --}}
    @foreach($module->lessons as $l)

        <div class="cyber-card lesson-card">

            {{--  Ordre --}}
            <div class="lesson-order">
                {{ $l->order }}
            </div>

            {{--  Infos --}}
            <div class="lesson-info">

                <span class="lesson-title">
                    {{ $l->title }}
                </span>

                {{--  Badge quiz --}}
                @if($l->quiz)
                    <span class="tag tag-b lesson-quiz-tag">Quiz</span>
                @endif

            </div>

            {{--  Actions --}}
            <div class="lesson-actions">

                <a href="{{ route('admin.modules.lessons.edit', [$module, $l]) }}" class="link-edit">
                    Modifier
                </a>

                @if($l->quiz)

                    <a href="{{ route('admin.lessons.quiz.edit', $l->quiz) }}" class="link-quiz-edit">
                        Quiz
                    </a>

                    <form action="{{ route('admin.lessons.quiz.destroy', $l->quiz) }}"
                          method="POST"
                          class="inline-form"
                          onsubmit="return confirm('Supprimer le quiz ?')">

                        @csrf @method('DELETE')

                        <button class="btn-delete-small">Quiz</button>
                    </form>

                @else

                    <a href="{{ route('admin.lessons.quiz.create', $l) }}" class="link-quiz-add">
                        +Quiz
                    </a>

                @endif

                {{--  Supprimer leçon --}}
                <form action="{{ route('admin.modules.lessons.destroy', [$module, $l]) }}"
                      method="POST"
                      class="inline-form"
                      onsubmit="return confirm('Supprimer ?')">

                    @csrf @method('DELETE')

                    <button class="btn-delete">Supprimer</button>
                </form>

            </div>

        </div>

    @endforeach

@endif

@endsection