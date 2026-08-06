@extends('layouts.app')
@section('title', 'Certificat')
@section('page-title', ' Certificat')

@section('content')

<div class="cyber-card certificate-card">

    <div class="certificate-icon">📜</div>
    <div class="certificate-title">Certificat de Compétences</div>
    <div class="certificate-user">{{ $certificate->user->name }}</div>
    <div class="certificate-text">a complété avec succès le module</div>
    <div class="certificate-module">{{ $certificate->module->title }}</div>
    <div class="certificate-info">
        Score : <strong>{{ $certificate->final_score }}%</strong>
        · {{ $certificate->issued_at->format('d/m/Y') }}
    </div>
    <div class="certificate-number">N° {{ $certificate->certificate_number }}</div>

    {{-- ✅ Télécharger seulement si propriétaire ou admin --}}
    @can('download', $certificate)
        <a href="{{ route('certificate.download', $certificate) }}"
           class="btn-cyber btn-sm certificate-download">
            📥 Télécharger PDF
        </a>
    @endcan

    {{-- ✅ Message clair si pas autorisé --}}
    @cannot('download', $certificate)
        <div class="cert-locked">
            🔒 Vous ne pouvez télécharger que vos propres certificats.
        </div>
    @endcannot

</div>

@endsection
