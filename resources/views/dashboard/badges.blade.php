@extends('layouts.app')

@section('title', 'Badges')
@section('page-title', '🏅 Mes Badges')

@section('content')

{{--  Grid badges --}}
<div class="badges-grid">

    @foreach($allBadges as $b)

        @php
            $earned = in_array($b->id, $earnedIds);
        @endphp

        <div class="cyber-card badge-card {{ $earned ? 'badge-earned' : 'badge-locked' }}">

            {{--  Icône --}}
            <div class="badge-icon">
                {{ $b->icon }}
            </div>

            {{--  Nom --}}
            <div class="badge-name">
                {{ $b->name }}
            </div>

            {{--  Description --}}
            <div class="badge-description">
                {{ $b->description }}
            </div>

            {{--  Statut --}}
            <div class="badge-status">
                <span class="tag {{ $earned ? 'tag-y' : 'tag-b' }}">
                    {{ $earned ? 'Obtenu ✓' : 'Non obtenu' }}
                </span>
            </div>

        </div>

    @endforeach

</div>

@endsection