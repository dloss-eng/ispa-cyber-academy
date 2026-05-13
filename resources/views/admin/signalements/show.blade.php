@extends('layouts.app')

{{-- 🏷️ Titre navigateur --}}
@section('title', 'Signalement')

{{-- 📌 Titre page --}}
@section('page-title', '🚨 Signalement ' . $signalement->ticket_number)

@section('content')

{{-- 🔙 Retour --}}
<a href="{{ route('admin.signalements.index') }}" class="back-link">
    ← Retour
</a>

{{-- 📦 Détails du signalement --}}
<div class="cyber-card signalement-detail-card">

    {{-- 📊 Infos principales --}}
    <div class="signalement-meta-grid">

        {{-- Type --}}
        <div>
            <div class="meta-label">Type</div>
            <div class="meta-value">{{ $signalement->type_label }}</div>
        </div>

        {{-- Auteur --}}
        <div>
            <div class="meta-label">Auteur</div>
            <div class="meta-value">{{ $signalement->user->name }}</div>
        </div>

        {{-- Date --}}
        <div>
            <div class="meta-label">Date</div>
            <div class="meta-value">
                {{ $signalement->created_at->format('d/m/Y H:i') }}
            </div>
        </div>

        {{-- 🤖 IA --}}
        @if($signalement->ai_category)
        <div>
            <div class="meta-label">Classification IA</div>
            <div class="meta-ai">
                {{ $signalement->ai_category }} ({{ $signalement->ai_confidence }}%)
            </div>
        </div>
        @endif

    </div>

    {{-- 📝 Description --}}
    <div class="signalement-description">
        {{ $signalement->description }}
    </div>

    {{-- ⚠️ Contact suspect --}}
    @if($signalement->suspect_contact)
        <div class="signalement-contact">
            Contact suspect :
            <strong>{{ $signalement->suspect_contact }}</strong>
        </div>
    @endif

    {{-- 🖼️ Screenshot --}}
    @if($signalement->screenshot_path)
        <div class="signalement-image-wrapper">
            <img src="{{ asset('storage/'.$signalement->screenshot_path) }}"
                 class="signalement-image">
        </div>
    @endif

</div>

{{-- 🛠️ Formulaire admin --}}
<div class="cyber-card signalement-form-card">

    <form method="POST"
          action="{{ route('admin.signalements.update', $signalement) }}">

        @csrf
        @method('PUT')

        {{-- 📊 Statut --}}
        <label class="fl no-margin-top">Statut</label>
        <select name="status" class="fi">

            <option value="nouveau" {{ $signalement->status === 'nouveau' ? 'selected' : '' }}>Nouveau</option>
            <option value="en_cours" {{ $signalement->status === 'en_cours' ? 'selected' : '' }}>En cours</option>
            <option value="traite" {{ $signalement->status === 'traite' ? 'selected' : '' }}>Traité</option>
            <option value="rejete" {{ $signalement->status === 'rejete' ? 'selected' : '' }}>Rejeté</option>

        </select>

        {{-- 📝 Notes admin --}}
        <label class="fl">Notes admin</label>
        <textarea name="admin_notes" rows="3" class="fi">
            {{ $signalement->admin_notes }}
        </textarea>

        {{-- 🚀 Submit --}}
        <button type="submit" class="btn-lg">
            Mettre à jour
        </button>

    </form>

</div>

@endsection