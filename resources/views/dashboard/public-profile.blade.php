@extends('layouts.app')

@section('title', $user->name)
@section('page-title', '👤 Profil')

@section('content')

{{-- 🔙 Retour --}}
<a href="{{ url()->previous() }}" class="back-link">
    ← Retour
</a>

{{--  Carte profil --}}
<div class="cyber-card profile-public-card">

    {{--  Avatar --}}
    <div class="profile-public-avatar">

        @if($user->avatar)

            <img src="{{ asset('storage/'.$user->avatar) }}"
                 class="profile-public-avatar-img">

        @else

            {{ strtoupper(substr($user->name,0,1)) }}

        @endif

    </div>

    {{--  Nom --}}
    <div class="profile-public-name">
        {{ $user->name }}
    </div>

    {{--  Bio --}}
    @if($user->bio)
        <div class="profile-public-bio">
            {{ $user->bio }}
        </div>
    @endif

    {{--  Infos --}}
    <div class="profile-public-meta">
        {{ $user->role_display }} · {{ $user->etablissement->name ?? '' }}
    </div>

    {{--  Stats --}}
    <div class="profile-public-stats">

        <div class="stat-item">
            <div class="stat-value stat-green">{{ $user->points }}</div>
            <div class="stat-label">Points XP</div>
        </div>

        <div class="stat-item">
            <div class="stat-value stat-orange">Niv. {{ $user->level }}</div>
            <div class="stat-label">Niveau</div>
        </div>

        <div class="stat-item">
            <div class="stat-value stat-yellow">{{ $userBadges->count() }}</div>
            <div class="stat-label">Badges</div>
        </div>

        <div class="stat-item">
            <div class="stat-value stat-blue">{{ $userCertificates->count() }}</div>
            <div class="stat-label">Certificats</div>
        </div>

    </div>

</div>

{{--  Badges --}}
@if($userBadges->count() > 0)

<div class="cyber-card section-card">

    <div class="section-title-yellow">
        🏅 BADGES
    </div>

    <div class="badges-container">

        @foreach($userBadges as $b)

            <div class="badge-item">

                <div class="badge-icon">
                    {{ $b->icon }}
                </div>

                <div class="badge-name">
                    {{ $b->name }}
                </div>

            </div>

        @endforeach

    </div>

</div>

@endif

{{--  Certificats --}}
@if($userCertificates->count() > 0)

<div class="cyber-card section-card">

    <div class="section-title-green">
        📜 CERTIFICATS
    </div>

    @foreach($userCertificates as $cert)

        <div class="certificate-row">

            <div class="certificate-icon">📜</div>

            <div class="certificate-info">

                <div class="certificate-title">
                    {{ $cert->module->title }}
                </div>

                <div class="certificate-meta">
                    {{ $cert->final_score }}% · {{ $cert->issued_at->format('d/m/Y') }}
                </div>

            </div>

        </div>

    @endforeach

</div>

@endif

@endsection