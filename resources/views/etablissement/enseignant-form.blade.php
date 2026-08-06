@extends('layouts.app')

@section('title', 'Ajouter enseignant')
@section('page-title', ' Nouvel enseignant')

@section('content')

{{-- Alertes --}}
@if($errors->any())
    <div style="background:rgba(255,107,53,.1);border:1px solid var(--re);border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:var(--re)">
        @foreach($errors->all() as $e)
            <div>✕ {{ $e }}</div>
        @endforeach
    </div>
@endif

{{--  Retour --}}
<a href="{{ route('etablissement.enseignants') }}" class="back-link">
    ← Retour
</a>

{{--  Formulaire --}}
<div class="cyber-card teacher-form-card">

    <form method="POST" action="{{ route('etablissement.enseignants.store') }}">

        @csrf

        {{--  Nom --}}
        <label class="fl no-margin-top">Nom</label>
        <input type="text" name="name" value="{{ old('name') }}" required class="fi">

        {{--  Email --}}
        <label class="fl">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required class="fi">

        {{--  Mot de passe --}}
        <label class="fl">Mot de passe</label>
        <input type="password" name="password" required minlength="8" class="fi" placeholder="Minimum 8 caractères">

        {{--  Confirmation mot de passe --}}
        <label class="fl">Confirmer le mot de passe</label>
        <input type="password" name="password_confirmation" required class="fi" placeholder="Répétez le mot de passe">

        {{--  Classes --}}
        <label class="fl">Classes assignées</label>

        <div class="class-select-box">
            @foreach(\App\Models\Classe::where('etablissement_id', auth()->user()->etablissement_id)->get() as $c)
                <label class="class-checkbox">
                    <input type="checkbox"
                           name="class_ids[]"
                           value="{{ $c->id }}"
                           {{ in_array($c->id, old('class_ids', [])) ? 'checked' : '' }}
                           class="checkbox-green">
                    {{ $c->name }}
                    <span class="class-level">· {{ $c->level }}</span>
                </label>
            @endforeach
        </div>

        {{--  Submit --}}
        <button type="submit" class="btn-lg">Créer</button>

    </form>

</div>

@endsection
