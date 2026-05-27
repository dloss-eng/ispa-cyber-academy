@extends('layouts.app')
@section('title', 'Communauté')
@section('page-title', '💬 Communauté')

@section('content')

<div class="forum-header">
    {{--  Créer un sujet seulement si policy l'autorise --}}
    @can('create', \App\Models\ForumTopic::class)
        <a href="{{ route('forum.create') }}" class="btn-cyber btn-sm">+ Nouveau sujet</a>
    @endcan

    {{--  Message si l'utilisateur ne peut pas créer --}}
    @cannot('create', \App\Models\ForumTopic::class)
        <span class="td-muted" title="Rôle non autorisé à créer un sujet">🔒 Lecture seule</span>
    @endcannot
</div>

@foreach($topics as $t)
<a href="{{ route('forum.show', $t) }}" class="forum-link">
    <div class="cyber-card forum-item">

        <div class="forum-avatar">
            @if($t->user->avatar)
                <img src="{{ asset('storage/'.$t->user->avatar) }}" class="forum-avatar-img">
            @else
                {{ strtoupper(substr($t->user->name, 0, 1)) }}
            @endif
        </div>

        <div class="forum-content">
            <div class="forum-title">
                {{ $t->title }}
                @if($t->is_locked)
                    <span title="Sujet verrouillé">🔒</span>
                @endif
            </div>
            <div class="forum-meta">
                {{ $t->user->name }} · {{ $t->created_at->diffForHumans() }}
            </div>
        </div>

        <div class="forum-count">
            <div class="forum-count-number">{{ $t->messages_count }}</div>
            <div class="forum-count-label">messages</div>
        </div>

    </div>
</a>
@endforeach

<div class="forum-pagination">{{ $topics->links() }}</div>

@endsection
