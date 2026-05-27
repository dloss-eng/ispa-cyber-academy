@extends('layouts.app')
@section('title', 'Certificats')
@section('page-title', '🏆 Mes Certificats')

@section('content')

@if($certificates->isEmpty())
    <div class="cert-empty">
        <div class="cert-empty-icon">📜</div>
        <div class="cert-empty-text">
            Aucun certificat. Complétez un module pour en obtenir un.
        </div>
    </div>
@else
    @foreach($certificates as $c)
        <div class="cyber-card cert-item">

            <div class="cert-icon">📜</div>

            <div class="cert-info">
                <div class="cert-title">{{ $c->module->title }}</div>
                <div class="cert-meta">
                    {{ $c->final_score }}% · {{ $c->issued_at->format('d/m/Y') }}
                </div>
                <div class="cert-number">N° {{ $c->certificate_number }}</div>
            </div>

            {{--  Voir le certificat --}}
            @can('view', $c)
                <a href="{{ route('certificate.show', $c) }}"
                   class="btn-cyber btn-sm" style="margin-right:6px">
                     Voir
                </a>
            @endcan

            {{--  Télécharger seulement si propriétaire --}}
            @can('download', $c)
                <a href="{{ route('certificate.download', $c) }}"
                   class="btn-cyber btn-sm">
                    📥 PDF
                </a>
            @endcan

            {{--  Accès refusé si pas propriétaire --}}
            @cannot('download', $c)
                <button class="btn-cyber btn-sm" disabled title="Non autorisé">
                    🔒 PDF
                </button>
            @endcannot

        </div>
    @endforeach
@endif

@endsection
