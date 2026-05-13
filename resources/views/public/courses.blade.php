@extends('layouts.landing')
@section('title', 'Catalogue des cours')

@push('styles')
<style>
/* ══════════════════════════════════════════════════
   PAGE CATALOGUE — Affichage en grille carrée
   ══════════════════════════════════════════════════ */

.catalog-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 80px 32px 100px;
}

/* ── En-tête ── */
.catalog-hero {
    text-align: center;
    margin-bottom: 64px;
}

.catalog-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(0,229,160,.08);
    border: 1px solid rgba(0,229,160,.22);
    border-radius: 20px;
    padding: 5px 18px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: var(--gr);
    margin-bottom: 20px;
}

.catalog-hero-title {
    font-family: 'Orbitron', sans-serif;
    font-size: clamp(28px, 4vw, 52px);
    font-weight: 900;
    color: var(--t);
    line-height: 1.1;
    margin-bottom: 14px;
}

.catalog-hero-title span { color: var(--gr); }

.catalog-hero-sub {
    font-size: 15px;
    color: var(--t2);
    max-width: 480px;
    margin: 0 auto 32px;
    line-height: 1.6;
}

/* Stats rapides */
.catalog-stats {
    display: flex;
    justify-content: center;
    gap: 40px;
    flex-wrap: wrap;
}

.cstat {
    text-align: center;
}

.cstat-num {
    font-family: 'Orbitron', sans-serif;
    font-size: 28px;
    font-weight: 900;
    color: var(--gr);
    line-height: 1;
}

.cstat-num.orange { color: var(--or); }
.cstat-num.blue   { color: var(--bl); }

.cstat-label {
    font-size: 11px;
    color: var(--t3);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-top: 4px;
}

/* ── Grille carrée ── */
.catalog-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}

@media (max-width: 1024px) {
    .catalog-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 600px) {
    .catalog-grid { grid-template-columns: 1fr; }
    .catalog-page { padding: 60px 16px 80px; }
}

/* ── Carte de module ── */
.pub-module-card {
    background: var(--card);
    border: 1px solid var(--bd);
    border-radius: 20px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: transform 0.3s, border-color 0.3s, box-shadow 0.3s;
    position: relative;
}

.pub-module-card:hover {
    transform: translateY(-8px);
    border-color: rgba(0,229,160,.35);
    box-shadow: 0 20px 60px rgba(0,229,160,.08);
}

/* Bande colorée en haut de chaque carte */
.pub-card-top {
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 56px;
    position: relative;
    overflow: hidden;
    flex-shrink: 0;
}

/* Dégradés par catégorie */
.pub-card-top.green  { background: linear-gradient(135deg, rgba(0,229,160,.15), rgba(0,229,160,.04)); }
.pub-card-top.orange { background: linear-gradient(135deg, rgba(255,107,53,.15), rgba(255,107,53,.04)); }
.pub-card-top.blue   { background: linear-gradient(135deg, rgba(75,123,255,.15), rgba(75,123,255,.04)); }
.pub-card-top.red    { background: linear-gradient(135deg, rgba(255,71,87,.15),  rgba(255,71,87,.04)); }
.pub-card-top.yellow { background: linear-gradient(135deg, rgba(255,211,42,.15), rgba(255,211,42,.04)); }
.pub-card-top.purple { background: linear-gradient(135deg, rgba(162,89,255,.15), rgba(162,89,255,.04)); }

/* Numéro de module discret */
.module-num {
    position: absolute;
    top: 10px;
    left: 14px;
    font-family: 'Orbitron', sans-serif;
    font-size: 10px;
    font-weight: 700;
    color: rgba(255,255,255,.2);
    letter-spacing: 1px;
}

/* Badge niveau */
.module-level-badge {
    position: absolute;
    top: 10px;
    right: 12px;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    padding: 3px 10px;
    border-radius: 20px;
}
.module-level-badge.debutant     { background: rgba(0,229,160,.15); color: var(--gr); border: 1px solid rgba(0,229,160,.3); }
.module-level-badge.intermediaire{ background: rgba(255,211,42,.15); color: var(--ye); border: 1px solid rgba(255,211,42,.3); }
.module-level-badge.avance       { background: rgba(255,107,53,.15); color: var(--or); border: 1px solid rgba(255,107,53,.3); }

/* Corps de la carte */
.pub-card-body {
    padding: 20px 22px 24px;
    display: flex;
    flex-direction: column;
    flex: 1;
    gap: 10px;
}

.pub-card-title {
    font-family: 'Orbitron', sans-serif;
    font-size: 13px;
    font-weight: 700;
    color: var(--t);
    line-height: 1.4;
}

.pub-card-desc {
    font-size: 12px;
    color: var(--t2);
    line-height: 1.6;
    flex: 1;
    /* Tronquer à 3 lignes */
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Méta-infos horizontales */
.pub-card-meta {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
}

.module-meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    color: var(--t3);
    font-weight: 500;
}

.module-meta-item svg {
    width: 13px;
    height: 13px;
    flex-shrink: 0;
}

/* Séparateur */
.pub-card-sep {
    height: 1px;
    background: var(--bd);
    margin: 0;
}

/* Footer de la carte */
.pub-card-footer {
    padding: 14px 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.pub-card-price {
    font-family: 'Orbitron', sans-serif;
    font-size: 11px;
    font-weight: 700;
    color: var(--gr);
}

.btn-card {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--gr);
    color: #000;
    font-family: 'Orbitron', sans-serif;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.5px;
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: opacity 0.2s, transform 0.15s;
    white-space: nowrap;
}

.btn-card:hover {
    opacity: 0.85;
    transform: scale(1.03);
}

/* ── Barre de progression (si connecté) ── */
.module-progress-bar {
    height: 3px;
    background: var(--bd);
    border-radius: 0 0 20px 20px;
    overflow: hidden;
}

.module-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--gr), var(--bl));
    border-radius: 2px;
    transition: width 0.5s ease;
}

/* ── CTA bas de page ── */
.catalog-cta {
    margin-top: 72px;
    background: linear-gradient(135deg, rgba(0,229,160,.07), rgba(75,123,255,.05));
    border: 1px solid rgba(0,229,160,.18);
    border-radius: 24px;
    padding: 56px 40px;
    text-align: center;
}

.catalog-cta-title {
    font-family: 'Orbitron', sans-serif;
    font-size: clamp(22px, 3vw, 36px);
    font-weight: 900;
    color: var(--t);
    margin-bottom: 12px;
}

.catalog-cta-sub {
    font-size: 15px;
    color: var(--t2);
    margin-bottom: 30px;
    line-height: 1.6;
}

.catalog-cta-btns {
    display: flex;
    gap: 16px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-ghost {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: transparent;
    color: var(--t);
    font-family: 'Orbitron', sans-serif;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.5px;
    padding: 12px 24px;
    border-radius: 10px;
    border: 1px solid var(--bd);
    text-decoration: none;
    transition: border-color 0.2s, color 0.2s;
}

.btn-ghost:hover {
    border-color: rgba(0,229,160,.4);
    color: var(--gr);
}
</style>
@endpush

@section('content')

<div class="catalog-page">

    {{-- ── EN-TÊTE ── --}}
    <div class="catalog-hero">
        <div class="catalog-label">📚 Catalogue</div>
        <h1 class="catalog-hero-title">
            Apprends la <span>cybersécurité</span><br>module par module
        </h1>
        <p class="catalog-hero-sub">
            Des formations 100 % adaptées à la réalité ivoirienne — arnaques Mobile Money,
            phishing WhatsApp, ingénierie sociale locale.
        </p>

        {{-- Stats rapides --}}
        <div class="catalog-stats">
            <div class="cstat">
                <div class="cstat-num">{{ $modules->count() }}</div>
                <div class="cstat-label">Modules</div>
            </div>
            <div class="cstat">
                <div class="cstat-num orange">{{ $modules->sum('lessons_count') }}</div>
                <div class="cstat-label">Leçons</div>
            </div>
            <div class="cstat">
                <div class="cstat-num blue">100%</div>
                <div class="cstat-label">Gratuit</div>
            </div>
        </div>
    </div>

    {{-- ── GRILLE DE MODULES ── --}}

    @php
        // Icônes et couleurs par ordre cyclique
        $icons  = ['🛡️','🎣','📱','🔐','💳','🤖','🌐','⚠️','🔍','🕵️'];
        $colors = ['green','orange','blue','red','yellow','purple','green','orange','blue','red'];

        // Mapping niveau → classe CSS
        $levelMap = [
            'debutant'      => 'debutant',
            'débutant'      => 'debutant',
            'intermediaire' => 'intermediaire',
            'intermédiaire' => 'intermediaire',
            'avance'        => 'avance',
            'avancé'        => 'avance',
        ];
    @endphp

    @if($modules->isEmpty())
        <div style="text-align:center;padding:80px 20px;color:var(--t2)">
            <div style="font-size:48px;margin-bottom:16px">📭</div>
            <div style="font-family:'Orbitron',sans-serif;font-size:16px;color:var(--t)">
                Aucun module publié pour l'instant
            </div>
            <p style="margin-top:8px">Revenez bientôt — du contenu est en cours de création.</p>
        </div>
    @else
        <div class="catalog-grid">
            @foreach($modules as $i => $m)
                @php
                    $icon     = $icons[$i % count($icons)];
                    $color    = $colors[$i % count($colors)];
                    $levelKey = strtolower($m->level ?? 'debutant');
                    $levelClass = $levelMap[$levelKey] ?? 'debutant';
                    $levelLabel = match($levelClass) {
                        'debutant'      => 'Débutant',
                        'intermediaire' => 'Intermédiaire',
                        'avance'        => 'Avancé',
                        default         => ucfirst($m->level ?? 'Débutant'),
                    };
                @endphp

                <div class="pub-module-card">

                    {{-- Haut de carte coloré --}}
                    <div class="pub-card-top {{ $color }}">
                        <span class="module-num">M{{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}</span>
                        <span style="font-size:52px">{{ $icon }}</span>
                        <span class="module-level-badge {{ $levelClass }}">{{ $levelLabel }}</span>
                    </div>

                    {{-- Corps --}}
                    <div class="pub-card-body">

                        <div class="pub-card-title">{{ $m->title }}</div>

                        <p class="pub-card-desc">
                            {{ $m->description ?? 'Formation complète sur ce module de cybersécurité. Cas réels, exercices pratiques et quiz de validation.' }}
                        </p>

                        {{-- Méta : leçons + durée --}}
                        <div class="pub-card-meta">
                            <div class="module-meta-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 20h9M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/>
                                </svg>
                                {{ $m->lessons_count }} leçon{{ $m->lessons_count > 1 ? 's' : '' }}
                            </div>

                            @if($m->duration_hours ?? 0)
                            <div class="module-meta-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                </svg>
                                {{ $m->duration_hours }}h
                            </div>
                            @endif

                            <div class="module-meta-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 00-3-3.87m-4-12a4 4 0 010 7.75"/>
                                </svg>
                                Tout niveau
                            </div>
                        </div>

                    </div>

                    <div class="pub-card-sep"></div>

                    {{-- Footer --}}
                    <div class="pub-card-footer">
                        <span class="pub-card-price">🎓 Gratuit</span>

                        @auth
                            <a href="{{ route('courses.show', $m) }}" class="btn-card">
                                Commencer →
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-card">
                                Se connecter →
                            </a>
                        @endauth
                    </div>

                    {{-- Barre de progression (si connecté et progression existante) --}}
                    @auth
                        @php
                            $pct = 0;
                            if ($m->lessons_count > 0 && isset($m->userProgress)) {
                                $done = $m->userProgress->where('completed', true)->count();
                                $pct  = round($done / $m->lessons_count * 100);
                            }
                        @endphp
                        @if($pct > 0)
                            <div class="module-progress-bar">
                                <div class="module-progress-fill" style="width:{{ $pct }}%"></div>
                            </div>
                        @endif
                    @endauth

                </div>
            @endforeach
        </div>
    @endif

    {{-- ── CTA BAS ── --}}
    <div class="catalog-cta">
        <div class="catalog-cta-title">Prêt à te former ?</div>
        <p class="catalog-cta-sub">
            Crée ton compte gratuitement et commence dès aujourd'hui.<br>
            Certifications vérifiables par QR code à la clé.
        </p>
        <div class="catalog-cta-btns">
            @guest
                <a href="{{ route('login') }}" class="btn-cyber">🚀 Commencer maintenant</a>
                <a href="{{ route('contact') }}" class="btn-ghost">✉️ Nous contacter</a>
            @else
                <a href="{{ route('courses.index') }}" class="btn-cyber">📚 Mes cours</a>
                <a href="{{ route('contact') }}"       class="btn-ghost">✉️ Nous contacter</a>
            @endguest
        </div>
    </div>

</div>

@endsection
