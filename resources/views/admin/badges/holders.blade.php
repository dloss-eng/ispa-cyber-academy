@extends('layouts.app')

{{-- 🏷️ Titre navigateur --}}
@section('title', 'Badge: ' . $badge->name)

{{-- 📌 Titre affiché --}}
@section('page-title', $badge->icon . ' ' . $badge->name)

@section('content')

{{-- 🔙 Retour à la liste des badges --}}
<a href="{{ route('admin.badges.index') }}" class="back-link">
    ← Badges
</a>

{{-- 📊 Description du badge + nombre d'obtentions --}}
<div class="badge-meta">
    {{ $badge->description }} · {{ $users->total() }} obtentions
</div>

{{-- 🔁 Liste des utilisateurs ayant obtenu le badge --}}
@forelse($users as $u)

    <div class="cyber-card user-card">

        {{-- 👤 Avatar (initiale du nom) --}}
        <div class="user-avatar">
            {{ strtoupper(substr($u->name, 0, 1)) }}
        </div>

        {{-- 📄 Infos utilisateur --}}
        <div class="user-info">
            <div class="user-name">{{ $u->name }}</div>
            <div class="user-email">{{ $u->email }}</div>
        </div>

        {{-- 📅 Date d'obtention --}}
        <span class="user-date">
            {{ $u->pivot->earned_at 
                ? \Carbon\Carbon::parse($u->pivot->earned_at)->format('d/m/Y') 
                : '' }}
        </span>

    </div>

{{-- ❌ Aucun utilisateur --}}
@empty
    <div class="empty-state">
        Aucun étudiant n'a obtenu ce badge.
    </div>
@endforelse

{{-- 🔢 Pagination --}}
<div class="pagination-wrapper">
    {{ $users->links() }}
</div>

@endsection