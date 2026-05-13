@extends('layouts.app')

{{-- 🏷️ Titre navigateur --}}
@section('title', 'Paiements')

{{-- 📌 Titre page --}}
@section('page-title', '💰 Paiements')

@section('content')

{{-- 📊 Statistiques --}}
<div class="kr">

    {{-- Actifs --}}
    <div class="kc kc-g">
        <div class="kv stat-color" style="--stat-color: var(--gr)">
            {{ $stats['active'] }}
        </div>
        <div class="kl">Actifs</div>
    </div>

    {{-- Revenus --}}
    <div class="kc kc-o">
        <div class="kv stat-color" style="--stat-color: var(--or)">
            {{ number_format($stats['revenue']) }} F
        </div>
        <div class="kl">Revenus</div>
    </div>

    {{-- Total --}}
    <div class="kc kc-y">
        <div class="kv stat-color" style="--stat-color: var(--ye)">
            {{ $stats['total'] }}
        </div>
        <div class="kl">Total</div>
    </div>

</div>

{{-- 📦 Tableau des paiements --}}
<div class="cyber-card table-wrapper">

    <table class="tbl">

        {{-- 📌 En-tête --}}
        <thead>
            <tr>
                <th>Établissement</th>
                <th>Plan</th>
                <th>Montant</th>
                <th>Méthode</th>
                <th>Début</th>
                <th>Fin</th>
                <th>Statut</th>
            </tr>
        </thead>

        {{-- 📄 Données --}}
        <tbody>

            @foreach($subscriptions as $s)

                <tr>

                    {{-- 🏫 Nom établissement --}}
                    <td class="td-strong">
                        {{ $s->etablissement->name }}
                    </td>

                    {{-- 📦 Plan --}}
                    <td>{{ $s->plan_label }}</td>

                    {{-- 💰 Montant --}}
                    <td class="td-amount">
                        {{ number_format($s->amount) }} FCFA
                    </td>

                    {{-- 💳 Méthode --}}
                    <td>{{ $s->payment_label }}</td>

                    {{-- 📅 Début --}}
                    <td class="td-date">
                        {{ $s->start_date?->format('d/m/Y') }}
                    </td>

                    {{-- 📅 Fin --}}
                    <td class="td-date">
                        {{ $s->end_date?->format('d/m/Y') }}
                    </td>

                    {{-- 🏷️ Statut --}}
                    <td>
                        <span class="tag {{ $s->isActive() ? 'tag-g' : 'tag-r' }}">
                            {{ $s->isActive() ? 'Actif' : 'Expiré' }}
                        </span>
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

</div>

@endsection