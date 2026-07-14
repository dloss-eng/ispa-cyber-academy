@extends('layouts.app')

{{-- 🏷️ Titre --}}
@section('title', isset($module) ? 'Modifier' : 'Nouveau module')

{{-- 📌 Titre page --}}
@section('page-title', isset($module) ? '✏️ Modifier' : '➕ Nouveau module')

@section('content')

{{-- Retour --}}
<a href="{{ route('admin.modules.index') }}" class="back-link">
    ← Retour
</a>

{{--  FORMULAIRE MODULE --}}
<div class="cyber-card module-form-card">

    <form method="POST"
          action="{{ isset($module) ? route('admin.modules.update',$module) : route('admin.modules.store') }}">

        @csrf
        @if(isset($module)) @method('PUT') @endif

        {{--  Ligne : titre + niveau --}}
        <div class="form-grid-2">

            <div>
                <label class="fl no-margin-top">Titre</label>
                <input type="text" name="title"
                       value="{{ old('title',$module->title??'') }}"
                       required class="fi">
            </div>

            <div>
                <label class="fl no-margin-top">Niveau</label>
                <select name="level" class="fi">
                    <option value="tous">Tous</option>
                    <option value="lycee" {{ old('level',$module->level??'')==='lycee'?'selected':'' }}>Lycée</option>
                    <option value="universite" {{ old('level',$module->level??'')==='universite'?'selected':'' }}>Université</option>
                </select>
            </div>

        </div>

        {{--  Description --}}
        <label class="fl">Description</label>
        <textarea name="description" rows="3" required class="fi">
{{ old('description',$module->description??'') }}
        </textarea>

        {{--  Ligne : durée + ordre --}}
        <div class="form-grid-2">

            <div>
                <label class="fl">Durée (h)</label>
                <input type="number" name="duration_hours"
                       value="{{ old('duration_hours',$module->duration_hours??2) }}"
                       class="fi">
            </div>

            <div>
                <label class="fl">Ordre</label>
                <input type="number" name="order"
                       value="{{ old('order',$module->order??0) }}"
                       class="fi">
            </div>

        </div>

        {{--  Publication --}}
        <label class="checkbox-inline">
            <input type="checkbox"
                   name="is_published"
                   value="1"
                   {{ old('is_published',$module->is_published??false)?'checked':'' }}
                   class="checkbox-input">
            Publié
        </label>

        {{--  Submit --}}
        <button type="submit" class="btn-lg">
            {{ isset($module)?'Mettre à jour':'Créer' }}
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

        {{--  Boutons + Leçon et + Quiz côte à côte --}}
        <div style="display:flex;gap:8px;align-items:center">

            <a href="{{ route('admin.modules.lessons.create',$module) }}"
               class="btn-cyber btn-sm">
                + Leçon
            </a>

            @php $moduleQuiz = \App\Models\Quiz::where('module_id', $module->id)->first(); @endphp

            @if($moduleQuiz)
                <a href="{{ route('admin.modules.quiz.edit', [$module, $moduleQuiz]) }}"
                   class="btn-sm"
                   style="background:rgba(75,123,255,0.15);color:var(--bl);border:1px solid rgba(75,123,255,0.3);border-radius:8px;padding:6px 14px;font-size:12px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:5px">
                    ✏️ Quiz Final
                </a>
            @else
                <a href="{{ route('admin.modules.quiz.create', $module) }}"
                   class="btn-sm"
                   style="background:rgba(0,229,160,0.12);color:var(--gr);border:1px solid rgba(0,229,160,0.3);border-radius:8px;padding:6px 14px;font-size:12px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:5px">
                    + Quiz
                </a>
            @endif

        </div>

    </div>

    {{-- Liste des leçons --}}
    @foreach($module->lessons as $l)

        @php $quiz = $l->quizzes->first(); @endphp

        <div class="cyber-card lesson-card">

            <div class="lesson-order">{{ $l->order }}</div>

            <div class="lesson-info">
                <span class="lesson-title">{{ $l->title }}</span>
                @if($quiz)
                    <span class="tag tag-b lesson-quiz-tag">Quiz</span>
                @endif
            </div>

            <div class="lesson-actions">

                <a href="{{ route('admin.modules.lessons.edit',[$module,$l]) }}" class="link-edit">
                    Modifier
                </a>

                @if($quiz)
                    <a href="{{ route('admin.lessons.quiz.edit', $quiz) }}" class="link-quiz-edit">
                        ✏️ Quiz
                    </a>
                    <form action="{{ route('admin.lessons.quiz.destroy', $quiz) }}"
                          method="POST" class="inline-form"
                          onsubmit="return confirm('Supprimer le quiz ?')">
                        @csrf @method('DELETE')
                        <button class="btn-delete-small"> Quiz</button>
                    </form>
                @else
                    <a href="{{ route('admin.lessons.quiz.create', $l) }}" class="link-quiz-add">
                        + Quiz
                    </a>
                @endif

                <form action="{{ route('admin.modules.lessons.destroy',[$module,$l]) }}"
                      method="POST" class="inline-form"
                      onsubmit="return confirm('Supprimer ?')">
                    @csrf @method('DELETE')
                    <button class="btn-delete">Supprimer</button>
                </form>

            </div>

        </div>

    @endforeach

@endif

@endsection
