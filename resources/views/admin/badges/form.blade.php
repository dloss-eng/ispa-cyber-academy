@extends('layouts.app')

{{-- Titre de la page dans l'onglet --}}
@section('title', isset($badge) ? 'Modifier badge' : 'Nouveau badge')

{{-- Titre affiché dans la page --}}
@section('page-title', isset($badge) ? ' Modifier badge' : ' Nouveau badge')

@section('content')

{{--  Lien pour retourner à la liste des badges --}}
<a href="{{ route('admin.badges.index') }}" class="back-link">
    ← Retour
</a>

{{--  Conteneur principal du formulaire --}}
<div class="cyber-card badge-form-card">

    {{--  Formulaire (création ou modification selon présence de $badge) --}}
    <form method="POST"
          action="{{ isset($badge) ? route('admin.badges.update', $badge) : route('admin.badges.store') }}">

        {{--  Protection CSRF obligatoire Laravel --}}
        @csrf

        {{--  Méthode PUT si modification --}}
        @if(isset($badge))
            @method('PUT')
        @endif

        {{--  Champ : Nom du badge --}}
        <label class="fl no-margin-top">Nom</label>
        <input type="text"
               name="name"
               value="{{ old('name', $badge->name ?? '') }}"
               required
               class="fi">

        {{--  Champ : Icône (emoji) --}}
        <label class="fl">Icône (emoji)</label>
        <input type="text"
               name="icon"
               value="{{ old('icon', $badge->icon ?? '🏅') }}"
               required
               class="fi icon-input">

        {{--  Champ : Description --}}
        <label class="fl">Description</label>
        <textarea name="description"
                  rows="2"
                  required
                  class="fi">{{ old('description', $badge->description ?? '') }}</textarea>

        {{--  Champ : Catégorie --}}
        <label class="fl">Catégorie</label>
        <select name="category" required class="fi">

            {{-- Option Quiz --}}
            <option value="quiz"
                {{ old('category', $badge->category ?? '') === 'quiz' ? 'selected' : '' }}>
                Quiz
            </option>

            {{-- Option Progression --}}
            <option value="progression"
                {{ old('category', $badge->category ?? '') === 'progression' ? 'selected' : '' }}>
                Progression
            </option>

            {{-- Option Spécial --}}
            <option value="special"
                {{ old('category', $badge->category ?? '') === 'special' ? 'selected' : '' }}>
                Spécial
            </option>

        </select>

        {{--  Bouton de soumission (Créer ou Modifier) --}}
        <button type="submit" class="btn-lg">
            {{ isset($badge) ? 'Modifier' : 'Créer' }}
        </button>

    </form>
</div>

@endsection