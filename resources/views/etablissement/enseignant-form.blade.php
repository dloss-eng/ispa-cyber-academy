@extends('layouts.app')

@section('title', 'Ajouter enseignant')
@section('page-title', '➕ Nouvel enseignant')

@section('content')

{{-- 🔙 Retour --}}
<a href="{{ route('etablissement.enseignants') }}" class="back-link">
    ← Retour
</a>

{{-- 📦 Formulaire --}}
<div class="cyber-card teacher-form-card">

    <form method="POST"
          action="{{ route('etablissement.enseignants.store') }}">

        @csrf

        {{-- 👤 Nom --}}
        <label class="fl no-margin-top">Nom</label>
        <input type="text" name="name" required class="fi">

        {{-- 📧 Email --}}
        <label class="fl">Email</label>
        <input type="email" name="email" required class="fi">

        {{-- 🔐 Mot de passe --}}
        <label class="fl">Mot de passe</label>
        <input type="password" name="password" required minlength="8" class="fi">

        {{-- 🏫 Classes --}}
        <label class="fl">Classes assignées</label>

        <div class="class-select-box">

            @foreach(\App\Models\Classe::where('etablissement_id', auth()->user()->etablissement_id)->get() as $c)

                <label class="class-checkbox">

                    <input type="checkbox"
                           name="class_ids[]"
                           value="{{ $c->id }}"
                           class="checkbox-green">

                    {{ $c->name }}

                    <span class="class-level">
                        · {{ $c->level }}
                    </span>

                </label>

            @endforeach

        </div>

        {{-- 🚀 Submit --}}
        <button type="submit" class="btn-lg">
            Créer
        </button>

    </form>

</div>

@endsection