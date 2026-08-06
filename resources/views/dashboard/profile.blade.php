@extends('layouts.app')

@section('title', 'Mon Profil')
@section('page-title', ' Mon Profil')

@section('content')

<div class="cyber-card profile-card">

    {{--  Avatar --}}
    <div class="profile-avatar">

        @if(auth()->user()->avatar)

            <img src="{{ asset('storage/'.auth()->user()->avatar) }}"
                 class="profile-avatar-img">

        @else

            <div class="profile-avatar-placeholder">
                {{ strtoupper(substr(auth()->user()->name,0,1)) }}
            </div>

        @endif

    </div>

    {{--  Formulaire --}}
    <form method="POST"
          action="{{ route('profile.update') }}"
          enctype="multipart/form-data">

        @csrf
        @method('PUT')

        {{--  Nom --}}
        <label class="fl no-margin-top">Nom</label>
        <input type="text"
               name="name"
               value="{{ auth()->user()->name }}"
               required
               class="fi">

        {{--  Bio --}}
        @if(auth()->user()->isLearner())

            <label class="fl">Bio / Statut</label>
            <input type="text"
                   name="bio"
                   value="{{ auth()->user()->bio }}"
                   class="fi"
                   placeholder="Ex: Passionné de cybersécurité 🔐">

        @endif

        {{--  Téléphone --}}
        <label class="fl">Téléphone</label>
        <input type="text"
               name="phone"
               value="{{ auth()->user()->phone }}"
               class="fi">

        {{--  Avatar --}}
        <label class="fl">Photo de profil</label>
        <input type="file"
               name="avatar"
               accept="image/*"
               class="fi file-input">

        {{--  Password --}}
        <label class="fl">Nouveau mot de passe (optionnel)</label>
        <input type="password"
               name="password"
               class="fi">

        {{--  Submit --}}
        <button type="submit" class="btn-lg">
            Mettre à jour
        </button>

    </form>

</div>

@endsection