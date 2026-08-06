@extends('layouts.app')

@section('title', 'Abonnement')
@section('page-title', ' Mon Abonnement')

@section('content')

{{--  Abonnement actif --}}
@if($subscription && $subscription->isActive())

<div class="cyber-card subscription-active">

    <div class="subscription-row">

        <div>

            <div class="subscription-title">
                {{ $subscription->plan_label }} — Actif ✅
            </div>

            <div class="subscription-dates">
                Du {{ $subscription->start_date->format('d/m/Y') }}
                au {{ $subscription->end_date->format('d/m/Y') }}
            </div>

            <div class="subscription-meta">
                Réf: {{ $subscription->transaction_ref }} · {{ $subscription->payment_label }}
            </div>

        </div>

        <div class="subscription-days">

            <div class="subscription-days-value">
                {{ $subscription->daysRemaining() }}
            </div>

            <div class="subscription-days-label">
                jours restants
            </div>

        </div>

    </div>

</div>

@else

{{--  Aucun abonnement --}}
<div class="cyber-card subscription-empty">
    ⚠️ Aucun abonnement actif
</div>

@endif

{{--  Formulaire --}}
<form method="POST" action="{{ route('etablissement.payments.subscribe') }}">
@csrf

{{--  Formules --}}
<div class="section-title">FORMULES</div>

<div class="plan-grid">

@foreach([
['basic','🥉 Basic','50 000 FCFA','3 mois','5 modules, 50 élèves, Forum'],
['premium','🥈 Premium','150 000 FCFA','6 mois','Illimité, 200 élèves, CTF, PDF'],
['enterprise','🥇 Enterprise','350 000 FCFA','1 an','Tout illimité, Support, API']
] as $p)

<label class="cyber-card plan-card">

    <input type="radio"
           name="plan"
           value="{{ $p[0] }}"
           class="plan-radio"
           {{ $loop->index===1?'checked':'' }}>

    <div class="plan-icon">
        {{ explode(' ',$p[1])[0] }}
    </div>

    <div class="plan-name">
        {{ explode(' ',$p[1],2)[1] }}
    </div>

    <div class="plan-price">
        {{ $p[2] }}
    </div>

    <div class="plan-duration">
        {{ $p[3] }}
    </div>

    <div class="plan-desc">
        {{ $p[4] }}
    </div>

</label>

@endforeach

</div>

{{--  Paiement --}}
<div class="section-title">MÉTHODE DE PAIEMENT</div>

<div class="payment-list">

@foreach([
['mtn_momo','📱 MTN MoMo'],
['orange_money','🟠 Orange Money'],
['wave','🌊 Wave'],
['visa','💳 Visa'],
['mastercard','💳 Mastercard']
] as $pm)

<label class="cyber-card payment-card">

    <input type="radio"
           name="payment_method"
           value="{{ $pm[0] }}"
           class="payment-radio"
           {{ $loop->first?'checked':'' }}>

    <span class="payment-label">
        {{ $pm[1] }}
    </span>

</label>

@endforeach

</div>

{{--  Submit --}}
<button type="submit" class="btn-lg subscribe-btn">
     Souscrire
</button>

</form>

{{--  Historique --}}
@if($history->count()>0)

<div class="section-title section-margin">HISTORIQUE</div>

@foreach($history as $h)

<div class="cyber-card history-item">

    <div class="history-content">

        <div class="history-title">
            {{ $h->plan_label }} · {{ number_format($h->amount) }} FCFA
        </div>

        <div class="history-meta">
            {{ $h->payment_label }} · {{ $h->transaction_ref }}
        </div>

    </div>

    <span class="tag {{ $h->isActive()?'tag-g':'tag-r' }}">
        {{ $h->isActive()?'Actif':'Expiré' }}
    </span>

</div>

@endforeach

@endif

@endsection