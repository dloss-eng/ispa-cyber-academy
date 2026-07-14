@extends('layouts.app')
@section('title', 'Modifier utilisateur')
@section('page-title', '✏️ Modifier l\'utilisateur')

@section('content')

<a href="{{ route('admin.users.index') }}" class="back-link">← Retour</a>

@if ($errors->any())
    <div class="alert-error">
        @foreach ($errors->all() as $e)
            <div>❌ {{ $e }}</div>
        @endforeach
    </div>
@endif

<div class="cyber-card user-form-card">
    <form method="POST" action="{{ route('admin.users.update', $user) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label class="fl no-margin-top">Nom</label>
        <input type="text" name="name"
               value="{{ old('name', $user->name) }}" required class="fi">

        <label class="fl">Email</label>
        <input type="email" name="email"
               value="{{ old('email', $user->email) }}" required class="fi">

        <label class="fl">Mot de passe <span class="td-muted">(vide = inchangé)</span></label>
        <input type="password" name="password" class="fi" autocomplete="new-password">

        {{-- champ confirmation ajouté --}}
        <label class="fl">Confirmer le mot de passe <span class="td-muted">(si changé)</span></label>
        <input type="password" name="password_confirmation" class="fi" autocomplete="new-password">

        <label class="fl">Téléphone</label>
        <input type="text" name="phone"
               value="{{ old('phone', $user->phone) }}" class="fi">

        <div class="form-grid-2">

            <div>
                <label class="fl">Rôle</label>
                {{-- display_name chargé correctement --}}
                <select name="role_id" required class="fi">
                    <option value="" disabled>-- Choisir un rôle --</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}"
                            {{ old('role_id', $user->role_id) == $r->id ? 'selected' : '' }}>
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
                            {{ old('etablissement_id', $user->etablissement_id) == $e->id ? 'selected' : '' }}>
                            {{ $e->name }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        <button type="submit" class="btn-lg"> Mettre à jour</button>

    </form>
</div>

@endsection
