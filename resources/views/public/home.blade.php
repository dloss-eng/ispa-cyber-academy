@extends('layouts.landing')
@section('title', 'Accueil')



@section('content')

{{-- ══ HERO ══ --}}
<div id="hero">
    <div class="hgrid">
        <div style="position:relative;z-index:1">
            <div class="badge-top">Côte d'Ivoire — Afrique de l'Ouest</div>
            <h1 class="htitle">
                <span style="display:block;color:var(--t)">Protège-toi.</span>
                <span style="display:block;color:var(--or)">Apprends.</span>
                <span style="display:block;color:var(--gr);font-size:0.7em;margin-top:8px">Deviens Cyber-Conscient.</span>
            </h1>
            <p class="hdesc"><strong>ISPA Cyber Academy</strong> est la première plateforme intelligente d'éducation à la cybersécurité adaptée au contexte ivoirien — <strong>Mobile Money, phishing local, arnaques du quotidien</strong>.</p>
            <div style="display:flex;gap:16px;flex-wrap:wrap">
                <a href="{{ route('about') }}" class="btn-cyber">🚀 Découvrir la plateforme</a>
                <a href="{{ route('public.courses') }}" class="btn-cyber-outline">📚 Voir les modules</a>
            </div>
            <div class="hstats">
                <div><span class="sn">{{ $stats['modules'] }}+</span><span class="sl">Modules</span></div>
                <div><span class="sn">{{ $stats['students'] }}</span><span class="sl">Apprenants</span></div>
                <div><span class="sn">{{ $stats['certificates'] }}</span><span class="sl">Certificats</span></div>
            </div>
        </div>
        <div style="position:relative;display:flex;justify-content:center;align-items:center">
            <div class="cshield">
                <div class="souter"><div class="sinner">
                    <img src="{{ asset('images/logo.png') }}" alt="ISPA" style="width:80px;height:80px;object-fit:contain" onerror="this.outerHTML='<div style=\'font-size:60px\'>🛡️</div>'">
                    <div style="font-family:'Orbitron',sans-serif;font-size:10px;color:var(--gr);letter-spacing:3px;text-transform:uppercase">CYBER SAFE CI</div>
                </div></div>
            </div>
            <div class="fc" style="top:10%;right:-10%;animation:float 5s ease-in-out infinite"><span style="color:var(--gr)">✅</span> Phishing détecté</div>
            <div class="fc" style="bottom:20%;left:-15%;animation:float 7s ease-in-out infinite 1s"><span style="color:var(--or)">🏅</span> Badge Expert obtenu !</div>
            <div class="fc" style="bottom:5%;right:5%;animation:float 6s ease-in-out infinite .5s"><span style="color:var(--ye)">⚡</span> +250 XP gagnés</div>
        </div>
    </div>
</div>
@endsection
