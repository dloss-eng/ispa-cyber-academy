@extends('layouts.app')

{{-- 🏷️ Titre navigateur --}}
@section('title', 'Signalements')

{{-- 📌 Titre page --}}
@section('page-title', '🚨 Signalements')

@section('content')

{{--  Statistiques --}}
<div class="kr">

    <div class="kc kc-o">
        <div class="kv stat-color" style="--stat-color: var(--or)">
            {{ $stats['total'] }}
        </div>
        <div class="kl">Total</div>
    </div>

    <div class="kc kc-y">
        <div class="kv stat-color" style="--stat-color: var(--ye)">
            {{ $stats['nouveau'] }}
        </div>
        <div class="kl">Nouveaux</div>
    </div>

    <div class="kc kc-g">
        <div class="kv stat-color" style="--stat-color: var(--gr)">
            {{ $stats['traite'] }}
        </div>
        <div class="kl">Traités</div>
    </div>

</div>

{{--  Liste des signalements --}}
@foreach($signalements as $s)

    {{-- 🔗 Carte cliquable --}}
    <a href="{{ route('admin.signalements.show', $s) }}" class="signalement-link">

        <div class="cyber-card signalement-card">

            {{--  Icône (premier mot du type) --}}
            <div class="signalement-icon">
                {{ explode(' ', $s->type_label)[0] }}
            </div>

            {{--  Infos --}}
            <div class="signalement-info">

                {{--  Type + utilisateur --}}
                <div class="signalement-title">
                    {{ $s->type_label }}
                    <span class="signalement-user">
                        — {{ $s->user->name }}
                    </span>
                </div>

                {{--  Description courte --}}
                <div class="signalement-desc">
                    {{ Str::limit($s->description, 80) }}
                </div>

                {{--  Ticket + date --}}
                <div class="signalement-meta">
                    {{ $s->ticket_number }} · {{ $s->created_at->diffForHumans() }}
                </div>

            </div>

            {{--  Statut --}}
            <span class="tag 
                {{ $s->status === 'traite' ? 'tag-g' : ($s->status === 'en_cours' ? 'tag-y' : 'tag-o') }}">
                {{ ucfirst(str_replace('_', ' ', $s->status)) }}
            </span>

            {{--  Score IA --}}
            @if($s->ai_confidence)
                <div class="signalement-ai">
                    <div class="ai-score">
                        {{ $s->ai_confidence }}%
                    </div>
                    <div class="ai-label">IA</div>
                </div>
            @endif

        </div>

    </a>

@endforeach

{{--  Pagination --}}
<div class="pagination-wrapper">
    {{ $signalements->links() }}
</div>

@endsection