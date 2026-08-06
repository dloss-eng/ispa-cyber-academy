@extends('layouts.app')

@section('title', 'Ajouter élève')
@section('page-title', ' Nouvel élève')

@section('content')

{{--  Retour --}}
<a href="{{ route('etablissement.students') }}" class="back-link">
    ← Retour
</a>

{{--  Formulaire --}}
<div class="cyber-card student-form-card">

    <form method="POST"
          action="{{ route('etablissement.students.store') }}">

        @csrf

        {{--  Nom --}}
        <label class="fl no-margin-top">Nom complet</label>
        <input type="text"
               name="name"
               required
               class="fi">

        {{--  Email --}}
        <label class="fl">Email</label>
        <input type="email"
               name="email"
               required
               class="fi">

        {{--  Mot de passe --}}
        <label class="fl">Mot de passe</label>
        <input type="password"
               name="password"
               required
               minlength="8"
               class="fi">

        {{-- Confirmation mot de passe --}}
        <label class="fl">Confirmer le mot de passe</label>
        <input type="password"
            name="password_confirmation"
            required
            minlength="8"
            class="fi">

        {{--  Type --}}
        <label class="fl">Type</label>
        <select name="role_type" required class="fi">

            @if($etab->type === 'lycee')
                <option value="eleve">Élève</option>
            @else
                <option value="etudiant">Étudiant</option>
            @endif

        </select>

        {{--  Classe --}}
        <label class="fl">Classe</label>

        <select name="class_id" class="fi">
            <option value="">Aucune</option>

            @foreach($classes as $c)
                <option value="{{ $c->id }}">
                    {{ $c->name }}
                </option>
            @endforeach

        </select>

        {{--  Submit --}}
        <button type="submit" class="btn-lg">
            Créer le compte
        </button>

    </form>

</div>

@endsection