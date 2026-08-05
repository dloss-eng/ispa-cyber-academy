@extends('layouts.app')
@section('title', 'Établissements')
@section('page-title', '🏫 Établissements')

@section('content')

<div class="etab-header">
    {{--  Créer seulement si autorisé --}}
    @can('create', \App\Models\Etablissement::class)
        <a href="{{ route('admin.etablissements.create') }}" class="btn-cyber btn-sm">+ Ajouter</a>
    @endcan
</div>

@foreach($etablissements as $e)
    <div class="cyber-card etab-card">

        @if($e->logo_path)
            <img src="{{ \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'))->url($e->logo_path) }}" class="etab-logo">
        @else
            <div class="etab-icon">{{ $e->type === 'lycee' ? '🏫' : '🎓' }}</div>
        @endif

        <div class="etab-info">
            <div class="etab-name">{{ $e->name }}</div>
            <div class="etab-meta">{{ ucfirst($e->type) }} · {{ $e->city }} · {{ $e->users_count }} utilisateurs</div>
        </div>

        {{--  Modifier seulement si autorisé --}}
        @can('update', $e)
            <a href="{{ route('admin.etablissements.edit', $e) }}" class="etab-edit">Modifier</a>
        @endcan

        {{--  Supprimer seulement admin --}}
        @can('delete', $e)
            <form action="{{ route('admin.etablissements.destroy', $e) }}"
                  method="POST" class="etab-delete-form"
                  onsubmit="return confirm('Supprimer cet établissement ?')">
                @csrf
                @method('DELETE')
                <button class="etab-delete-btn">Supprimer</button>
            </form>
        @endcan

    </div>
@endforeach

@endsection
