@extends('layouts.app')
@section('title', 'Nouvel utilisateur')
@section('page-title', '➕ Nouvel utilisateur')

@section('content')

<a href="{{ route('admin.users.index') }}" class="back-link">← Retour</a>

{{-- ✅ Erreurs de validation lisibles (grâce aux fichiers lang/fr/) --}}
@if ($errors->any())
    <div class="alert-error">
        @foreach ($errors->all() as $e)
            <div>❌ {{ $e }}</div>
        @endforeach
    </div>
@endif

<div class="cyber-card user-form-card">
    <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
        @csrf

        <label class="fl no-margin-top">Nom</label>
        <input type="text" name="name"
               value="{{ old('name') }}" required class="fi">

        <label class="fl">Email</label>
        <input type="email" name="email"
               value="{{ old('email') }}" required class="fi">

        <label class="fl">Mot de passe</label>
        <input type="password" name="password" required class="fi"
               autocomplete="new-password">

        {{-- ✅ FIX — champ confirmation ajouté --}}
        <label class="fl">Confirmer le mot de passe</label>
        <input type="password" name="password_confirmation" required class="fi"
               autocomplete="new-password">

        <label class="fl">Téléphone</label>
        <input type="text" name="phone"
               value="{{ old('phone') }}" class="fi">

        <div class="form-grid-2">

            <div>
                <label class="fl">Rôle</label>
                {{-- ✅ FIX — display_name chargé correctement via UserController --}}
                <select name="role_id" required class="fi">
                    <option value="" disabled selected>-- Choisir un rôle --</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}"
                            {{ old('role_id') == $r->id ? 'selected' : '' }}>
                            {{ $r->display_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="fl">Établissement</label>
                <select name="etablissement_id" class="fi">
                    <option value="">Aucun</option>
                    @foreach($etablissements as $e)
                        <option value="{{ $e->id }}"
                            {{ old('etablissement_id') == $e->id ? 'selected' : '' }}>
                            {{ $e->name }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        <button type="submit" class="btn-lg">🚀 Créer l'utilisateur</button>

    </form>
</div>

@endsection
