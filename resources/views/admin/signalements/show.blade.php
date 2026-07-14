@extends('layouts.app')

@section('title', 'Signalement')
@section('page-title', '🚨 Signalement ' . $signalement->ticket_number)

@section('content')

{{--  Succès --}}
@if(session('success'))
    <div style="background:rgba(0,229,160,.08);border:1px solid rgba(0,229,160,.25);border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:var(--gr)">
        ✓ {{ session('success') }}
    </div>
@endif

{{-- ❌ Erreurs --}}
@if($errors->any())
    <div style="background:rgba(255,59,92,.08);border:1px solid rgba(255,59,92,.2);border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:var(--re)">
        @foreach($errors->all() as $e) <div>✕ {{ $e }}</div> @endforeach
    </div>
@endif

{{--  Retour --}}
<a href="{{ route('admin.signalements.index') }}" class="back-link">
    ← Retour
</a>

{{--  Détails du signalement --}}
<div class="cyber-card signalement-detail-card">

    <div class="signalement-meta-grid">

        <div>
            <div class="meta-label">Type</div>
            <div class="meta-value">{{ $signalement->type_label }}</div>
        </div>

        <div>
            <div class="meta-label">Auteur</div>
            <div class="meta-value">{{ $signalement->user->name }}</div>
        </div>

        <div>
            <div class="meta-label">Date</div>
            <div class="meta-value">{{ $signalement->created_at->format('d/m/Y H:i') }}</div>
        </div>

        @if($signalement->ai_category)
        <div>
            <div class="meta-label">Classification IA</div>
            <div class="meta-ai">{{ $signalement->ai_category }} ({{ $signalement->ai_confidence }}%)</div>
        </div>
        @endif

    </div>

    <div class="signalement-description">{{ $signalement->description }}</div>

    @if($signalement->suspect_contact)
        <div class="signalement-contact">
            Contact suspect : <strong>{{ $signalement->suspect_contact }}</strong>
        </div>
    @endif

    @if($signalement->screenshot_path)
        <div class="signalement-image-wrapper">
            <img src="{{ asset('storage/'.$signalement->screenshot_path) }}" class="signalement-image">
        </div>
    @endif

</div>

{{-- 🛠️ Formulaire admin --}}
<div class="cyber-card signalement-form-card">

    {{--  Avertissement si déjà traité --}}
    @if($signalement->status === 'traite')
        <div style="background:rgba(255,215,0,.08);border:1px solid rgba(255,215,0,.2);border-radius:8px;padding:12px 14px;margin-bottom:16px;font-size:12px;color:var(--ye)">
             Ce signalement est marqué comme traité. Vous pouvez quand même modifier le statut.
        </div>
    @endif

    <form method="POST" action="{{ route('admin.signalements.update', $signalement) }}">

        @csrf
        @method('PUT')

        <label class="fl no-margin-top">Statut</label>
        <select name="status" class="fi">
            <option value="nouveau"   {{ $signalement->status === 'nouveau'   ? 'selected' : '' }}>Nouveau</option>
            <option value="en_cours"  {{ $signalement->status === 'en_cours'  ? 'selected' : '' }}>En cours</option>
            <option value="traite"    {{ $signalement->status === 'traite'    ? 'selected' : '' }}>Traité</option>
            <option value="rejete"    {{ $signalement->status === 'rejete'    ? 'selected' : '' }}>Rejeté</option>
        </select>

        <label class="fl">Notes admin</label>
        <textarea name="admin_notes" rows="3" class="fi">{{ old('admin_notes', $signalement->admin_notes) }}</textarea>

        <button type="submit" class="btn-lg">Mettre à jour</button>

    </form>

</div>

@endsection
