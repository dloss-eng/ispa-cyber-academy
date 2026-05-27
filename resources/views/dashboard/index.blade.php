@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', '📊 Dashboard')

@section('content')

{{--  Stats --}}
<div class="kr">

    <div class="kc kc-g">
        <div class="kv stat-green">{{ $stats['points'] }}</div>
        <div class="kl">Points XP</div>
    </div>

    <div class="kc kc-o">
        <div class="kv stat-orange">Niv. {{ $stats['level'] }}</div>
        <div class="kl">Niveau</div>
    </div>

    <div class="kc kc-b">
        <div class="kv stat-blue">{{ $stats['badges_count'] }}</div>
        <div class="kl">Badges</div>
    </div>

    <div class="kc kc-y">
        <div class="kv stat-yellow">{{ $stats['certificates_count'] }}</div>
        <div class="kl">Certificats</div>
    </div>

</div>

{{--  Layout --}}
<div class="dashboard-grid">

    {{--  Modules commencés --}}
    <div>

        <div class="section-title" style="font-size:13px;color:var(--t);display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:18px">
            <div style="display:flex;align-items:center;gap:8px">
                📚 <span style="font-family:'Orbitron',sans-serif;letter-spacing:2px">MES COURS EN COURS</span>
            </div>
            {{--  Lien vers tous les cours --}}
            <a href="{{ route('courses.index') }}"
               style="font-size:11px;color:var(--gr);text-decoration:none;font-weight:600;white-space:nowrap">
                @if($availableCount > 0)
                    + {{ $availableCount }} cours disponible{{ $availableCount > 1 ? 's' : '' }} →
                @else
                    Voir mes cours →
                @endif
            </a>
        </div>

        <div style="display:flex;flex-direction:column;gap:12px">

            @forelse($startedModules as $module)

                <a href="{{ route('courses.show', $module) }}" class="dashboard-link" style="text-decoration:none">

                    <div class="cyber-card module-card" style="padding:16px 20px;transition:transform .2s,box-shadow .2s;border-left:3px solid {{
                        $module->user_progress >= 100 ? 'var(--gr)' : 'var(--or)'
                    }}">

                        <div class="module-top" style="margin-bottom:10px">

                            <div style="display:flex;align-items:center;gap:10px">

                                {{-- Icône statut --}}
                                <div style="font-size:20px;line-height:1">
                                    @if($module->user_progress >= 100) ✅
                                    @else ⏳
                                    @endif
                                </div>

                                <div>
                                    <div class="module-name" style="font-size:13px;font-weight:700;margin-bottom:2px">
                                        {{ $module->title }}
                                    </div>
                                    <div class="module-meta" style="font-size:10px;color:var(--t3);display:flex;gap:10px">
                                        <span>📖 {{ $module->total_lessons }} leçons</span>
                                        <span>⏱️ {{ $module->duration_hours }}h</span>
                                    </div>
                                </div>

                            </div>

                            {{-- Pourcentage --}}
                            <div style="text-align:right">
                                <div class="{{ $module->user_progress >= 100 ? 'percent-success' : 'percent-warning' }}"
                                     style="font-family:'Orbitron';font-size:14px;font-weight:900">
                                    {{ $module->user_progress }}%
                                </div>
                                <div style="font-size:10px;margin-top:2px;color:{{ $module->user_progress >= 100 ? 'var(--gr)' : 'var(--or)' }}">
                                    @if($module->user_progress >= 100) Terminé
                                    @else En cours
                                    @endif
                                </div>
                            </div>

                        </div>

                        {{-- Barre de progression --}}
                        <div class="pb" style="height:6px;border-radius:999px;background:var(--bg2,rgba(255,255,255,0.06))">
                            <div class="pf {{ $module->user_progress >= 100 ? 'pfg' : 'pf-gradient' }}"
                                 style="width:{{ $module->user_progress }}%;height:100%;border-radius:999px;transition:width .5s ease">
                            </div>
                        </div>

                    </div>

                </a>

            @empty

                {{--  État vide — aucun cours commencé --}}
                <div class="cyber-card" style="padding:36px 24px;text-align:center">
                    <div style="font-size:40px;margin-bottom:14px">🚀</div>
                    <div style="font-size:14px;font-weight:700;margin-bottom:8px">
                        Vous n'avez pas encore commencé de cours
                    </div>
                    <div style="font-size:12px;color:var(--t3);margin-bottom:20px">
                        Choisissez votre premier module et lancez-vous !
                    </div>
                    <a href="{{ route('courses.index') }}" class="btn-cyber" style="display:inline-flex">
                        📚 Découvrir les cours
                    </a>
                </div>

            @endforelse

        </div>

    </div>

    {{--  Sidebar --}}
    <div class="dashboard-sidebar">

        {{--  Classement --}}
        <div class="cyber-card sidebar-card">

            <div class="section-title-yellow">
                🏆 Classement
            </div>

            <div class="rank-position">
                Position :
                <span class="rank-value">{{ $userRank }}</span>
            </div>

            @foreach($leaderboard as $i => $p)

                <div class="leaderboard-item {{ $p->id === $user->id ? 'leaderboard-me' : '' }}">

                    <span class="leader-rank {{ $i < 3 ? 'rank-top' : 'rank-normal' }}">
                        {{ $i===0?'🥇':($i===1?'🥈':($i===2?'🥉':$i+1)) }}
                    </span>

                    <span class="leader-name">
                        {{ $p->name }}
                        @if($p->bio)
                            <br><span class="leader-bio">{{ $p->bio }}</span>
                        @endif
                    </span>

                    <span class="leader-points">{{ $p->points }}</span>

                </div>

            @endforeach

            <a href="{{ route('leaderboard') }}" class="link-center link-green">
                Voir tout →
            </a>

        </div>

        {{--  Badges --}}
        <div class="cyber-card sidebar-card">

            <div class="section-title-orange">
                🏅 Mes Badges
            </div>

            @if($recentBadges->count() > 0)

                <div class="badges-mini">
                    @foreach($recentBadges as $b)
                        <div class="badge-mini" title="{{ $b->description }}">
                            <div class="badge-mini-icon">{{ $b->icon }}</div>
                            <div class="badge-mini-name">{{ $b->name }}</div>
                        </div>
                    @endforeach
                </div>

            @else

                <div class="empty-text">
                    Complétez des quiz pour obtenir des badges !
                </div>

            @endif

            <a href="{{ route('badges') }}" class="link-center link-orange">
                Tous les badges →
            </a>

        </div>

    </div>

</div>

@endsection
