@extends('layouts.app')

@section('title', 'Certificats élèves')
@section('page-title', ' Certificats de mes élèves')

@section('content')

@forelse($certificates as $c)

    <div class="cyber-card certificate-item">

        {{--  Icône --}}
        <div class="certificate-icon">
            📜
        </div>

        {{--  Infos --}}
        <div class="certificate-content">

            {{--  Nom --}}
            <div class="certificate-name">
                {{ $c->user->name }}
            </div>

            {{--  Module --}}
            <div class="certificate-module">
                {{ $c->module->title }} · {{ $c->final_score }}%
            </div>

            {{--  Détails --}}
            <div class="certificate-meta">
                N° {{ $c->certificate_number }} · {{ $c->issued_at->format('d/m/Y') }}
            </div>

        </div>

    </div>

@empty

    <div class="empty-state">
        Aucun certificat délivré à vos élèves pour le moment.
    </div>

@endforelse

@endsection