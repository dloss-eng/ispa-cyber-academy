@extends('layouts.landing')
@section('title', 'À propos')

{{-- ─────────────────────────────────────────────
     CSS : chargé depuis navbar.css (aucun style inline)
     ───────────────────────────────────────────── --}}

@section('content')

<div class="page-wrapper">

    {{-- ── EN-TÊTE ── --}}
    <div class="page-header">
        <div class="page-label">🛡️ À propos</div>
        <h1 class="page-title">
            Qui sommes-<span class="accent-green">nous</span> ?
        </h1>
        <p class="page-subtitle">
            ISPA Cyber Academy est la première plateforme d'éducation à la
            cybersécurité conçue spécifiquement pour la Côte d'Ivoire et
            l'Afrique de l'Ouest.
        </p>
    </div>

    {{-- ── PRÉSENTATION + CHIFFRES CLÉS ── --}}
    <div class="about-intro-grid">

        {{-- Texte de présentation --}}
        <div>
            <p class="about-intro-text">
                Née au sein de l'<strong>ISPA Polytechnique</strong>, notre académie
                répond à un constat alarmant : les jeunes Ivoiriens sont massivement
                exposés aux cybermenaces sans jamais avoir été formés pour s'en protéger.
            </p>
            <p class="about-intro-text">
                Grâce à une pédagogie interactive, des modules en français et en nouchi,
                et une intelligence artificielle formée sur des arnaques locales réelles,
                nous rendons la cybersécurité <strong>accessible, engageante et utile</strong>
                au quotidien.
            </p>

            <ul class="about-list">
                <li>Arnaques Mobile Money (MTN MoMo, Orange Money, Wave)</li>
                <li>Phishing WhatsApp & Facebook contextualisé CI</li>
                <li>Formation des établissements scolaires et universitaires</li>
                <li>Certification vérifiable par QR code</li>
                <li>IA locale : analyse en français ET en nouchi</li>
            </ul>
        </div>

        {{-- Chiffres clés --}}
        <div class="about-kpi-box">
            <div class="about-kpi-item">
                <div class="about-kpi-num">{{ $stats['modules'] ?? '15' }}+</div>
                <div class="about-kpi-label">Modules de formation</div>
            </div>
            <div class="about-kpi-item">
                <div class="about-kpi-num orange">{{ $stats['students'] ?? '0' }}</div>
                <div class="about-kpi-label">Apprenants inscrits</div>
            </div>
            <div class="about-kpi-item">
                <div class="about-kpi-num blue">{{ $stats['certificates'] ?? '0' }}</div>
                <div class="about-kpi-label">Certificats délivrés</div>
            </div>
            <div class="about-kpi-item">
                <div class="about-kpi-num yellow">94%</div>
                <div class="about-kpi-label">Précision IA (F1-Score)</div>
            </div>
        </div>

    </div>

    {{-- ── MISSION / VISION / VALEURS ── --}}
    <div class="page-header">
        <div class="page-label">🎯 Notre ADN</div>
        <h2 class="page-title">
            Mission, Vision &amp; <span class="accent-orange">Valeurs</span>
        </h2>
    </div>

    <div class="about-mvv-grid">

        <div class="mvv-card green">
            <div class="mvv-icon">🎯</div>
            <div class="mvv-tag green">Mission</div>
            <div class="mvv-title">Former pour protéger</div>
            <p class="mvv-text">
                Rendre la cybersécurité accessible à chaque Ivoirien, quel que soit
                son niveau, grâce à une pédagogie contextualisée, interactive et
                ancrée dans la réalité locale.
            </p>
        </div>

        <div class="mvv-card orange">
            <div class="mvv-icon">🔭</div>
            <div class="mvv-tag orange">Vision</div>
            <div class="mvv-title">L'Afrique cyber-résiliente</div>
            <p class="mvv-text">
                Devenir la référence panafricaine de l'éducation à la cybersécurité
                d'ici 2027, en couvrant 10 pays et en certifiant 100 000 apprenants.
            </p>
        </div>

        <div class="mvv-card blue">
            <div class="mvv-icon">💎</div>
            <div class="mvv-tag blue">Valeurs</div>
            <div class="mvv-title">Accessibilité · Rigueur · Impact</div>
            <p class="mvv-text">
                Nous croyons que la sécurité numérique est un droit fondamental.
                Chaque module est conçu avec rigueur scientifique et validé par
                des experts en cybersécurité africains.
            </p>
        </div>

    </div>

    {{-- ── ÉQUIPE ── --}}
    <div class="page-header">
        <div class="page-label">👥 L'équipe</div>
        <h2 class="page-title">
            Ceux qui <span class="accent-green">construisent</span> l'académie
        </h2>
    </div>

    <div class="about-team-grid">

        @foreach([
            ['🧑‍💻', 'Directeur Technique',      'Expert en cybersécurité & architecture logicielle. 8 ans d\'expérience en sécurité des SI.'],
            ['👩‍🏫', 'Responsable Pédagogique',   'Spécialiste en ingénierie pédagogique numérique et gamification de l\'apprentissage.'],
            ['🤖', 'Lead Data Scientist',         'Développe les modèles NLP pour la détection d\'arnaques en français et en nouchi.'],
            ['🎨', 'UX / UI Designer',            'Conçoit des expériences utilisateurs accessibles et engageantes pour le contexte africain.'],
            ['📊', 'Responsable Partenariats',    'Développe les relations avec les établissements scolaires et les institutions.'],
            ['🔐', 'Analyste Cybersécurité',      'Veille sur les nouvelles menaces locales et met à jour le contenu des modules.'],
        ] as $member)
            <div class="team-card">
                <div class="team-avatar">{{ $member[0] }}</div>
                <div class="team-role">{{ $member[1] }}</div>
                <p class="team-bio">{{ $member[2] }}</p>
            </div>
        @endforeach

    </div>

    {{-- ── PARTENAIRES ── --}}
    <div class="page-header">
        <div class="page-label">🤝 Partenaires</div>
        <h2 class="page-title">
            Ils nous <span class="accent-orange">font confiance</span>
        </h2>
    </div>

    <div class="about-partners-grid">

        @foreach([
            ['🏛️', 'CI-CERT',      'Cert. national CI'],
            ['📡', 'ARTCI',        'Autorité TIC CI'],
            ['🏫', 'ISPA Polytech','Établissement fondateur'],
            ['🌍', 'ECOWAS Cyber', 'CEDEAO Cybersécurité'],
        ] as $partner)
            <div class="partner-card">
                <div class="partner-icon">{{ $partner[0] }}</div>
                <div class="partner-name">{{ $partner[1] }}</div>
                <div class="partner-type">{{ $partner[2] }}</div>
            </div>
        @endforeach

    </div>

    {{-- ── CTA FINAL ── --}}
    <div class="about-cta-box">
        <h2 class="about-cta-title">
            Prêt à rejoindre<br>l'académie ?
        </h2>
        <p class="about-cta-text">
            Que vous soyez étudiant, enseignant ou responsable d'établissement,
            ISPA Cyber Academy a une solution pour vous.
        </p>
        <div class="about-cta-actions">
            <a href="{{ route('public.courses') }}" class="btn-cyber">
                📚 Voir les modules
            </a>
            <a href="{{ route('contact') }}" class="btn-cyber-outline">
                ✉️ Nous contacter
            </a>
        </div>
    </div>

</div>

@endsection
