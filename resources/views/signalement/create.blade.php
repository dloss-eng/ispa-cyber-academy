@extends('layouts.app')
@section('title', 'Signaler')
@section('page-title', '🚨 Signaler une arnaque')

@section('content')

@if($errors->any())
    <div style="background:rgba(255,59,92,.08);border:1px solid rgba(255,59,92,.2);border-radius:10px;padding:14px;margin-bottom:16px;font-size:13px;color:var(--re)">
        @foreach($errors->all() as $e) <div>✕ {{ $e }}</div> @endforeach
    </div>
@endif

<div class="cyber-card" style="padding:24px;max-width:600px">

    <div style="background:rgba(255,107,53,0.06);border:1px solid rgba(255,107,53,0.2);border-radius:10px;padding:14px;margin-bottom:20px;font-size:12px;color:var(--or)">
        ⚠️ Signalez toute arnaque ou tentative suspecte. Notre IA analysera automatiquement votre signalement.
    </div>

    <form method="POST" action="{{ route('signalements.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- ✅ Valeurs correspondant exactement à l'ENUM de la base de données --}}
        <label class="fl" style="margin-top:0">Type d'arnaque</label>
        <select name="type" required class="fi">
            <option value="">Sélectionnez...</option>
            <option value="sms_frauduleux"       {{ old('type') === 'sms_frauduleux'       ? 'selected' : '' }}>📱 SMS Frauduleux</option>
            <option value="phishing_whatsapp"    {{ old('type') === 'phishing_whatsapp'    ? 'selected' : '' }}>💬 Phishing WhatsApp</option>
            <option value="phishing_email"       {{ old('type') === 'phishing_email'       ? 'selected' : '' }}>📧 Phishing Email</option>
            <option value="faux_site"            {{ old('type') === 'faux_site'            ? 'selected' : '' }}>🌐 Faux Site Web</option>
            <option value="arnaque_mobile_money" {{ old('type') === 'arnaque_mobile_money' ? 'selected' : '' }}>💰 Arnaque Mobile Money</option>
            <option value="cyberharcèlement"     {{ old('type') === 'cyberharcèlement'     ? 'selected' : '' }}>😢 Cyberharcèlement</option>
            <option value="autre"                {{ old('type') === 'autre'                ? 'selected' : '' }}>❓ Autre</option>
        </select>

        <label class="fl">Description détaillée</label>
        <textarea name="description" rows="5" required class="fi"
                  placeholder="Décrivez ce qui s'est passé...">{{ old('description') }}</textarea>

        <label class="fl">Numéro ou lien suspect</label>
        <input type="text" name="suspect_contact" class="fi"
               value="{{ old('suspect_contact') }}"
               placeholder="Ex: 0700000000 ou http://...">

        <label class="fl">Date de l'incident</label>
        <input type="date" name="incident_date" class="fi"
               value="{{ old('incident_date') }}"
               max="{{ date('Y-m-d') }}">

        <label class="fl">Capture d'écran (optionnel)</label>
        <input type="file" name="screenshot" accept="image/*" class="fi" style="padding:10px">

        <button type="submit" class="btn-lg">🚨 Envoyer le signalement</button>

    </form>
</div>

@endsection
