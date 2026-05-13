@extends('layouts.app')

{{-- 🏷️ Titre navigateur --}}
@section('title', isset($lesson) ? 'Modifier leçon' : 'Nouvelle leçon')

{{-- 📌 Titre page --}}
@section('page-title', isset($lesson) ? '✏️ Modifier leçon' : '➕ Nouvelle leçon')

@section('content')

{{-- 🔙 Retour au module --}}
<a href="{{ route('admin.modules.edit',$module) }}" class="back-link">
    ← Module
</a>

{{-- 📦 FORMULAIRE --}}
<div class="cyber-card lesson-form-card">

    <form method="POST"
          action="{{ isset($lesson)
              ? route('admin.modules.lessons.update',[$module,$lesson])
              : route('admin.modules.lessons.store',$module) }}"
          enctype="multipart/form-data">

        @csrf
        @if(isset($lesson)) @method('PUT') @endif

        {{-- 🏷️ Titre --}}
        <label class="fl no-margin-top">Titre</label>
        <input type="text" name="title"
               value="{{ old('title',$lesson->title??'') }}"
               required class="fi">

        {{-- 📝 Contenu --}}
        <label class="fl">Description du cours</label>
        <textarea name="content" rows="10" required class="fi"
                  placeholder="Décrivez le contenu de cette leçon...">
{{ old('content',$lesson->content??'') }}
        </textarea>

        {{-- 🎥 Vidéo --}}
        <div class="form-grid-2">

            <div>
                <label class="fl">URL Vidéo (YouTube/Vimeo)</label>
                <input type="url" name="video_url"
                       value="{{ old('video_url',$lesson->video_url??'') }}"
                       class="fi"
                       placeholder="https://youtu.be/...">
            </div>

            <div>
                <label class="fl">Ou uploader une vidéo</label>
                <input type="file" name="video_file"
                       accept="video/*"
                       class="fi file-input">
            </div>

        </div>

        {{-- 📄 PDF existants --}}
        @if(isset($lesson) && $lesson->resources->count() > 0)

            <div class="resource-section">
                <label class="fl">📄 Fichiers PDF existants</label>

                @foreach($lesson->resources as $r)

                    <div class="resource-item">

                        <span class="resource-name">
                            📄 {{ $r->title }}
                        </span>

                        <a href="{{ asset('storage/'.$r->file_path) }}"
                           target="_blank"
                           class="resource-view">
                            👁️ Voir
                        </a>

                    </div>

                @endforeach
            </div>

        @endif

        {{-- 📤 Upload PDF --}}
        <label class="fl">Ajouter des fichiers PDF</label>
        <input type="file" name="pdf_files[]" multiple accept=".pdf"
               class="fi file-input">

        {{-- ⏱️ durée + ordre --}}
        <div class="form-grid-2">

            <div>
                <label class="fl">Durée (min)</label>
                <input type="number" name="duration_minutes"
                       value="{{ old('duration_minutes',$lesson->duration_minutes??10) }}"
                       class="fi">
            </div>

            <div>
                <label class="fl">Ordre</label>
                <input type="number" name="order"
                       value="{{ old('order',$lesson->order??0) }}"
                       class="fi">
            </div>

        </div>

        {{-- ✅ publication --}}
        <label class="checkbox-inline">
            <input type="checkbox"
                   name="is_published"
                   value="1"
                   {{ old('is_published',$lesson->is_published??false)?'checked':'' }}
                   class="checkbox-input">
            Publiée
        </label>

        {{-- 🚀 submit --}}
        <button type="submit" class="btn-lg">
            Enregistrer
        </button>

    </form>

</div>

{{-- 🧠 SECTION QUIZ (visible uniquement en mode édition) --}}
@if(isset($lesson))
@php $quiz = $lesson->quizzes()->first(); @endphp
<div class="cyber-card" style="margin-top:16px">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
            <div style="font-family:'Orbitron',sans-serif;font-size:12px;font-weight:700;letter-spacing:.5px;margin-bottom:6px;color:var(--t)">
                🧠 QUIZ DE LA LEÇON
            </div>
            @if($quiz)
                <span style="font-size:12px;color:var(--t2)">
                    Quiz existant : <strong style="color:var(--t)">{{ $quiz->title }}</strong>
                </span>
            @else
                <span style="font-size:12px;color:var(--t3)">Aucun quiz pour cette leçon.</span>
            @endif
        </div>
        <div style="display:flex;gap:10px">
            @if($quiz)
                <a href="{{ route('admin.lessons.quiz.edit', $quiz) }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;background:rgba(75,123,255,0.12);color:var(--bl);border:1px solid rgba(75,123,255,0.3);transition:all .2s">
                    ✏️ Modifier le Quiz
                </a>
            @else
                <a href="{{ route('admin.lessons.quiz.create', $lesson) }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;background:rgba(0,229,160,0.12);color:var(--gr);border:1px solid rgba(0,229,160,0.3);transition:all .2s">
                    + Ajouter un Quiz
                </a>
            @endif
        </div>
    </div>
</div>
@endif

{{-- 🗑️ SUPPRESSION PDF --}}
@if(isset($lesson) && $lesson->resources->count() > 0)

<div class="cyber-card resource-delete-card">

    <div class="resource-delete-title">
        🗑️ SUPPRIMER DES FICHIERS
    </div>

    @foreach($lesson->resources as $r)

        <div class="resource-delete-item">

            <span class="resource-name">
                📄 {{ $r->title }}
            </span>

            <form action="{{ route('admin.resources.destroy', $r) }}"
                  method="POST"
                  onsubmit="return confirm('Supprimer ce fichier PDF ?')">

                @csrf
                @method('DELETE')

                <button type="submit" class="btn-delete-pdf">
                     Supprimer
                </button>

            </form>

        </div>

    @endforeach

</div>

@endif

@endsection
