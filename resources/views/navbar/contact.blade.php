@extends('layouts.landing')
@section('title', 'Contact')

{{-- ─────────────────────────────────────────────
     CSS : chargé depuis navbar.css (aucun style inline)
     ───────────────────────────────────────────── --}}

@section('content')

<div class="page-wrapper">

    {{-- ── EN-TÊTE ── --}}
    <div class="page-header">
        <div class="page-label">✉️ Contactez-nous</div>
        <h1 class="page-title">
            Parlons <span class="accent-green">cybersécurité</span>
        </h1>
        <p class="page-subtitle">
            Une question, un partenariat, une inscription en masse ?
            Notre équipe vous répond dans les 24 h.
        </p>
    </div>

    {{-- ── CORPS : infos + formulaire ── --}}
    <div class="contact-layout">

        {{-- Colonne gauche : coordonnées --}}
        <div>
            <h2 class="contact-info-title">Nos coordonnées</h2>
            <p class="contact-info-sub">
                Que vous soyez un particulier, un établissement scolaire ou
                une entreprise, nous sommes là pour vous accompagner.
            </p>

            <div class="contact-cards">

                {{-- Téléphones --}}
                <div class="contact-card">
                    <div class="contact-card-icon">📞</div>
                    <div class="contact-card-body">
                        <div class="contact-card-label">Téléphone</div>
                        <div class="contact-card-value">
                            (+225) 05 84 78 69 77<br>
                            (+225) 27 22 20 05 30
                        </div>
                        <div class="contact-card-note">Lun – Ven, 8 h – 18 h</div>
                    </div>
                </div>

                {{-- E-mail --}}
                <div class="contact-card">
                    <div class="contact-card-icon orange">📧</div>
                    <div class="contact-card-body">
                        <div class="contact-card-label">E-mail</div>
                        <div class="contact-card-value orange">
                            info@ispapolytech.fr
                        </div>
                        <div class="contact-card-note">Réponse sous 24 h ouvrées</div>
                    </div>
                </div>

                {{-- Adresse --}}
                <div class="contact-card">
                    <div class="contact-card-icon blue">📍</div>
                    <div class="contact-card-body">
                        <div class="contact-card-label">Localisation</div>
                        <div class="contact-card-value">8e tranche, Angré</div>
                        <div class="contact-card-note">Après le stade d'Angré — Abidjan, CI</div>
                    </div>
                </div>

            </div>

            {{-- Indicateur de disponibilité --}}
            <div class="contact-availability">
                <div class="contact-availability-dot"></div>
                <p class="contact-availability-text">
                    <strong>En ligne</strong> · Disponible du lundi au vendredi
                    de 8 h à 18 h (GMT)
                </p>
            </div>
        </div>

        {{-- Colonne droite : formulaire --}}
        <div class="contact-form-box">

            <h2 class="contact-form-title">Envoyer un message</h2>
            <p class="contact-form-sub">
                Décrivez votre besoin et nous vous recontactons rapidement.
            </p>

            {{-- Affichage erreurs/succès Laravel --}}
            @if(session('success'))
                <div class="contact-success show">
                    <div class="contact-success-icon">✅</div>
                    <div class="contact-success-text">Message envoyé !</div>
                    <div class="contact-success-sub">
                        Notre équipe vous répondra dans les 24 h.
                    </div>
                </div>
            @else

            <form class="contact-form"
                  action="{{ route('contact.send') }}"
                  method="POST">
                @csrf

                {{-- Nom / Prénom --}}
                <div class="contact-form-row">
                    <div class="contact-form-group">
                        <label for="prenom">Prénom</label>
                        <input type="text"
                               id="prenom"
                               name="prenom"
                               placeholder="Ex : Konan"
                               value="{{ old('prenom') }}"
                               required>
                        @error('prenom')
                            <span style="color:var(--re);font-size:11px">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="contact-form-group">
                        <label for="nom">Nom</label>
                        <input type="text"
                               id="nom"
                               name="nom"
                               placeholder="Ex : Yao"
                               value="{{ old('nom') }}"
                               required>
                        @error('nom')
                            <span style="color:var(--re);font-size:11px">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- E-mail --}}
                <div class="contact-form-group">
                    <label for="email">Adresse e-mail</label>
                    <input type="email"
                           id="email"
                           name="email"
                           placeholder="vous@exemple.com"
                           value="{{ old('email') }}"
                           required>
                    @error('email')
                        <span style="color:var(--re);font-size:11px">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Sujet --}}
                <div class="contact-form-group">
                    <label for="sujet">Sujet</label>
                    <select id="sujet" name="sujet" required>
                        <option value="" disabled {{ old('sujet') ? '' : 'selected' }}>
                            Choisir un sujet…
                        </option>
                        <option value="inscription"   {{ old('sujet') == 'inscription'   ? 'selected' : '' }}>Inscription / Accès</option>
                        <option value="etablissement" {{ old('sujet') == 'etablissement' ? 'selected' : '' }}>Partenariat établissement</option>
                        <option value="entreprise"    {{ old('sujet') == 'entreprise'    ? 'selected' : '' }}>Collaboration entreprise</option>
                        <option value="technique"     {{ old('sujet') == 'technique'     ? 'selected' : '' }}>Support technique</option>
                        <option value="autre"         {{ old('sujet') == 'autre'         ? 'selected' : '' }}>Autre</option>
                    </select>
                </div>

                {{-- Message --}}
                <div class="contact-form-group">
                    <label for="message">Message</label>
                    <textarea id="message"
                              name="message"
                              placeholder="Décrivez votre demande en quelques lignes…"
                              required>{{ old('message') }}</textarea>
                    @error('message')
                        <span style="color:var(--re);font-size:11px">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn-cyber contact-form-submit">
                     Envoyer le message
                </button>

            </form>
            @endif

        </div>
    </div>

</div>

@endsection
