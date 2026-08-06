@extends('layouts.app')

@section('title', 'Modifier élève')
@section('page-title', ' Modifier élève')

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
<a href="{{ route('etablissement.students') }}" class="back-link">
    ← Retour
</a>

{{--  Formulaire --}}
<div class="cyber-card student-form-card">

    <form method="POST"
          action="{{ route('etablissement.students.update', $user) }}">

        @csrf
        @method('PUT')

        {{--  Nom --}}
        <label class="fl no-margin-top">Nom</label>
        <input type="text"
               name="name"
               value="{{ old('name', $user->name) }}"
               required
               class="fi">

        {{--  Email --}}
        <label class="fl">Email</label>
        <input type="email"
               name="email"
               value="{{ old('email', $user->email) }}"
               required
               class="fi">

        {{--  Téléphone --}}
        <label class="fl">Téléphone</label>
        <input type="text"
               name="phone"
               value="{{ old('phone', $user->phone) }}"
               class="fi">

        {{--  Classe --}}
        <label class="fl">Classe</label>
        <select name="class_id" class="fi">
            <option value="">Aucune</option>
            @foreach($classes as $c)
                <option value="{{ $c->id }}"
                    {{ old('class_id', $user->classes->first()?->id) == $c->id ? 'selected' : '' }}>
                    {{ $c->name }}
                </option>
            @endforeach
        </select>

        {{--  Mot de passe --}}
        <label class="fl">Nouveau mot de passe <span style="color:var(--t3);font-weight:400">(laisser vide = inchangé)</span></label>
        <input type="password" name="password" class="fi" placeholder="Minimum 8 caractères">

        {{--  Confirmation mot de passe --}}
        <label class="fl">Confirmer le mot de passe</label>
        <input type="password" name="password_confirmation" class="fi" placeholder="Répétez le mot de passe">

        {{--  Submit --}}
        <button type="submit" class="btn-lg">
            Modifier
        </button>

    </form>

</div>

@endsection
