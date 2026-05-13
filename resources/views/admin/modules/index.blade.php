@extends('layouts.app')
@section('title', 'Modules')
@section('page-title', '📚 Modules & Cours')

@section('content')

<div class="modules-header">
    <div class="modules-count">{{ $modules->total() }} modules</div>

    {{-- ✅ Bouton créer seulement si admin peut créer --}}
    @can('create', \App\Models\Module::class)
        <a href="{{ route('admin.modules.create') }}" class="btn-cyber btn-sm">+ Nouveau</a>
    @endcan
</div>

@foreach($modules as $m)
    <div class="cyber-card module-card">

        <div class="module-order">{{ $m->order }}</div>

        <div class="module-info">
            <div class="module-title">{{ $m->title }}</div>
            <div class="module-meta">{{ $m->lessons_count }} leçons</div>
        </div>

        <span class="tag {{ $m->is_published ? 'tag-g' : 'tag-y' }}">
            {{ $m->is_published ? 'Publié' : 'Brouillon' }}
        </span>

        {{-- ✅ Modifier seulement si autorisé --}}
        @can('update', $m)
            <a href="{{ route('admin.modules.edit', $m) }}" class="module-edit">Modifier</a>
        @endcan

        {{-- ✅ Supprimer seulement si autorisé --}}
        @can('delete', $m)
            <form action="{{ route('admin.modules.destroy', $m) }}"
                  method="POST" class="inline-form"
                  onsubmit="return confirm('Supprimer ce module et toutes ses leçons ?')">
                @csrf
                @method('DELETE')
                <button class="btn-delete">Supprimer</button>
            </form>
        @endcan

        {{-- ✅ Lecture seule si pas admin --}}
        @cannot('update', $m)
            <span class="td-muted" title="Lecture seule">🔒 Lecture seule</span>
        @endcannot

    </div>
@endforeach

<div class="pagination-wrapper">{{ $modules->links() }}</div>

@endsection
