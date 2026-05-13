@extends('layouts.app')
@section('title', 'Badges')
@section('page-title', '🏅 Gestion des Badges')

@section('content')

<div class="badge-header">
    {{-- ✅ Créer badge seulement si autorisé --}}
    @can('create', \App\Models\Badge::class)
        <a href="{{ route('admin.badges.create') }}" class="btn-cyber btn-sm">+ Nouveau badge</a>
    @endcan
</div>

<div class="badge-grid">
    @foreach($badges as $b)
        <div class="cyber-card badge-card">

            <div class="badge-icon">{{ $b->icon }}</div>
            <div class="badge-name">{{ $b->name }}</div>
            <div class="badge-description">{{ $b->description }}</div>
            <div class="badge-count">{{ $b->users_count }} obtentions</div>

            <div class="badge-actions">

                {{-- ✅ Voir les porteurs : admin seulement --}}
                @can('viewAny', \App\Models\Badge::class)
                    <a href="{{ route('admin.badges.holders', $b) }}" class="action-view">👥 Voir</a>
                @endcan

                {{-- ✅ Modifier seulement si autorisé --}}
                @can('update', $b)
                    <a href="{{ route('admin.badges.edit', $b) }}" class="action-edit">✏️ Modifier</a>
                @endcan

                {{-- ✅ Supprimer seulement si autorisé --}}
                @can('delete', $b)
                    <form action="{{ route('admin.badges.destroy', $b) }}"
                          method="POST" class="action-delete-form"
                          onsubmit="return confirm('Supprimer ce badge ?')">
                        @csrf
                        @method('DELETE')
                        <button class="action-delete-btn">🗑️ Supprimer</button>
                    </form>
                @endcan

            </div>
        </div>
    @endforeach
</div>

@endsection
