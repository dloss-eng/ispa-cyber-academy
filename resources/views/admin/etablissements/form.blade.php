@extends('layouts.app')

{{-- 🏷️ Titre navigateur --}}
@section('title', isset($etablissement) ? 'Modifier' : 'Nouvel établissement')

{{-- 📌 Titre affiché --}}
@section('page-title', isset($etablissement) ? '✏️ Modifier' : '➕ Nouvel établissement')

@section('content')

{{-- 🔙 Retour --}}
<a href="{{ route('admin.etablissements.index') }}" class="back-link">
    ← Retour
</a>

{{-- 📦 Carte formulaire --}}
<div class="cyber-card etab-form-card">

    {{-- 📝 Formulaire (create / update) --}}
    <form method="POST"
          action="{{ isset($etablissement) ? route('admin.etablissements.update', $etablissement) : route('admin.etablissements.store') }}"
          enctype="multipart/form-data">

        @csrf

        {{-- 🔄 Méthode PUT si modification --}}
        @if(isset($etablissement))
            @method('PUT')
        @endif

        {{-- 🏷️ Nom --}}
        <label class="fl no-margin-top">Nom</label>
        <input type="text"
               name="name"
               value="{{ old('name', $etablissement->name ?? '') }}"
               required
               class="fi">

        {{-- 🧩 Ligne Type + Ville --}}
        <div class="form-grid-2">

            {{-- 📂 Type --}}
            <div>
                <label class="fl">Type</label>
                <select name="type" required class="fi">
                    <option value="lycee"
                        {{ old('type', $etablissement->type ?? '') === 'lycee' ? 'selected' : '' }}>
                        Lycée
                    </option>
                    <option value="universite"
                        {{ old('type', $etablissement->type ?? '') === 'universite' ? 'selected' : '' }}>
                        Université
                    </option>
                </select>
            </div>

            {{-- 🌍 Ville --}}
            <div>
                <label class="fl">Ville</label>
                <input type="text"
                       name="city"
                       value="{{ old('city', $etablissement->city ?? '') }}"
                       required
                       class="fi">
            </div>

        </div>

        {{-- 📞 Téléphone --}}
        <label class="fl">Téléphone</label>
        <input type="text"
               name="phone"
               value="{{ old('phone', $etablissement->phone ?? '') }}"
               class="fi">

        {{-- 📧 Email --}}
        <label class="fl">Email</label>
        <input type="email"
               name="email"
               value="{{ old('email', $etablissement->email ?? '') }}"
               class="fi">

        {{-- 🔐 Mot de passe (uniquement à la création ou si on veut changer) --}}
        @if(!isset($etablissement))
            {{-- Création : mot de passe obligatoire --}}
            <label class="fl">Mot de passe</label>
            <input type="password"
                   name="password"
                   required
                   placeholder="Minimum 8 caractères"
                   class="fi">

            <label class="fl">Confirmer le mot de passe</label>
            <input type="password"
                   name="password_confirmation"
                   required
                   placeholder="Répétez le mot de passe"
                   class="fi">
        @else
            {{-- Modification : mot de passe optionnel --}}
            <div style="border:1px solid var(--bd);border-radius:10px;padding:16px;margin:16px 0;">
                <p style="font-size:12px;color:var(--t2);margin:0 0 12px 0;">
                    🔐 Laisser vide pour conserver le mot de passe actuel
                </p>

                <label class="fl no-margin-top">Nouveau mot de passe</label>
                <input type="password"
                       name="password"
                       placeholder="Laisser vide pour ne pas changer"
                       class="fi">

                <label class="fl">Confirmer le nouveau mot de passe</label>
                <input type="password"
                       name="password_confirmation"
                       placeholder="Laisser vide pour ne pas changer"
                       class="fi">
            </div>
        @endif

        {{-- 🖼️ Logo --}}
        <label class="fl">Logo (image)</label>
        <input type="file"
               name="logo_file"
               accept="image/*"
               class="fi file-input">

        {{-- 👁️ Aperçu logo existant --}}
        @if(isset($etablissement) && $etablissement->logo_path)
            <div class="logo-preview-wrapper">
                <img src="{{ asset('storage/' . $etablissement->logo_path) }}"
                     class="logo-preview">
            </div>
        @endif

        {{-- 🚀 Bouton --}}
        <button type="submit" class="btn-lg">
            {{ isset($etablissement) ? 'Mettre à jour' : 'Créer' }}
        </button>

    </form>
</div>

@endsection
