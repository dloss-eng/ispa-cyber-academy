@extends('layouts.landing')

@section('title', 'API Documentation')

@section('content')

<div class="api-container">

    <div class="section-title api-title">
        📡 Documentation <span class="highlight">API</span>
    </div>

    <div class="api-info">
        Base URL :
        <code class="api-code">/api/v1</code>
        · Auth : Bearer Token (Sanctum)
        · 30 endpoints
    </div>

    <div class="cyber-card api-card">
        <p class="api-text">
            Consultez la documentation complète des endpoints :
            authentification, cours, quiz, forum, signalements, administration.
            Tous les endpoints protégés nécessitent un token Bearer obtenu via
            POST /api/v1/login.
        </p>
    </div>

</div>

@endsection