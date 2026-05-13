@extends('layouts.app')

@section('title', 'Modifier enseignant')
@section('page-title', '✏️ Modifier enseignant')

@section('content')

{{-- 🔙 Retour --}}
<a href="{{ route('etablissement.enseignants') }}" class="back-link">
    ← Retour
</a>

{{-- 📦 Formulaire --}}
<div class="cyber-card teacher-form-card">

    <form method="POST"
          action="{{ route('etablissement.enseignants.update',$user) }}">

        @csrf
        @method('PUT')

        {{-- 👤 Nom --}}
        <label class="fl no-margin-top">Nom</label>
        <input type="text" name="name" value="{{ $user->name }}" required class="fi">

        {{-- 📧 Email --}}
        <label class="fl">Email</label>
        <input type="email" name="email" value="{{ $user->email }}" required class="fi">

        {{-- 📱 Téléphone --}}
        <label class="fl">Téléphone</label>
        <input type="text" name="phone" value="{{ $user->phone }}" class="fi">

        {{-- 🔐 Password --}}
        <label class="fl">Mot de passe (vide = inchangé)</label>
        <input type="password" name="password" class="fi">

        {{-- 🏫 Classes --}}
        <label class="fl">Classes assignées</label>

        <div class="class-select-box">

            @php
                $assignedIds = \App\Models\Classe::where('enseignant_id', $user->id)
                    ->pluck('id')
                    ->toArray();
            @endphp

            @foreach(\App\Models\Classe::where('etablissement_id', auth()->user()->etablissement_id)->get() as $c)

                <label class="class-checkbox">

                    <input type="checkbox"
                           name="class_ids[]"
                           value="{{ $c->id }}"
                           {{ in_array($c->id, $assignedIds) ? 'checked' : '' }}
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
            Modifier
        </button>

    </form>

</div>

@endsection