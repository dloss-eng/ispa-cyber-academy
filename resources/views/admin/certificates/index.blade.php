@extends('layouts.app')

{{-- 🏷️ Titre navigateur --}}
@section('title', 'Certificats')

{{-- 📌 Titre affiché --}}
@section('page-title', '📜 Tous les Certificats')

@section('content')

{{-- 📊 Nombre total de certificats --}}
<div class="cert-meta">
    {{ $certificates->total() }} certificats délivrés
</div>

{{-- 🔁 Liste des certificats --}}
@forelse($certificates as $c)

    <div class="cyber-card cert-card">

        {{-- 📜 Icône --}}
        <div class="cert-icon">
            📜
        </div>

        {{-- 📄 Infos certificat --}}
        <div class="cert-info">

            {{-- 👤 Nom utilisateur --}}
            <div class="cert-user">
                {{ $c->user->name }}
            </div>

            {{-- 📘 Module + score --}}
            <div class="cert-module">
                {{ $c->module->title }} · {{ $c->final_score }}%
            </div>

            {{-- 🆔 Numéro + date --}}
            <div class="cert-meta-small">
                N° {{ $c->certificate_number }} · {{ $c->issued_at->format('d/m/Y') }}
            </div>

        </div>

    </div>

{{-- ❌ Aucun certificat --}}
@empty
    <div class="empty-state">
        Aucun certificat délivré.
    </div>
@endforelse

{{-- 🔢 Pagination --}}
<div class="pagination-wrapper">
    {{ $certificates->links() }}
</div>

@endsection