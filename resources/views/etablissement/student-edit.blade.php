@extends('layouts.app')

@section('title', 'Modifier élève')
@section('page-title', '✏️ Modifier élève')

@section('content')

{{-- 🔙 Retour --}}
<a href="{{ route('etablissement.students') }}" class="back-link">
    ← Retour
</a>

{{-- 📦 Formulaire --}}
<div class="cyber-card student-form-card">

    <form method="POST"
          action="{{ route('etablissement.students.update',$user) }}">

        @csrf
        @method('PUT')

        {{-- 👤 Nom --}}
        <label class="fl no-margin-top">Nom</label>
        <input type="text"
               name="name"
               value="{{ $user->name }}"
               required
               class="fi">

        {{-- 📧 Email --}}
        <label class="fl">Email</label>
        <input type="email"
               name="email"
               value="{{ $user->email }}"
               required
               class="fi">

        {{-- 📱 Téléphone --}}
        <label class="fl">Téléphone</label>
        <input type="text"
               name="phone"
               value="{{ $user->phone }}"
               class="fi">

        {{-- 🏫 Classe --}}
        <label class="fl">Classe</label>

        <select name="class_id" class="fi">
            <option value="">Aucune</option>

            @foreach($classes as $c)
                <option value="{{ $c->id }}"
                    {{ $user->classes->contains($c->id) ? 'selected' : '' }}>
                    {{ $c->name }}
                </option>
            @endforeach

        </select>

        {{-- 🔐 Mot de passe --}}
        <label class="fl">Mot de passe (vide = inchangé)</label>
        <input type="password" name="password" class="fi">

        {{-- 🚀 Submit --}}
        <button type="submit" class="btn-lg">
            Modifier
        </button>

    </form>

</div>

@endsection