@extends('layouts.app')
@section('title', $topic->title)
@section('page-title', '💬 ' . Str::limit($topic->title, 30))

@section('content')

<a href="{{ route('forum.index') }}" class="forum-back">← Communauté</a>

{{-- ── Sujet principal ── --}}
<div class="forum-topic">

    <div class="forum-topic-header">
        <div class="topic-avatar">
            @if($topic->user->avatar)
                <img src="{{ asset('storage/'.$topic->user->avatar) }}" class="avatar-img">
            @else
                {{ strtoupper(substr($topic->user->name, 0, 1)) }}
            @endif
        </div>
        <div>
            <div class="topic-user">{{ $topic->user->name }}</div>

            {{--  Établissement de l'auteur du sujet --}}
            @if($topic->user->etablissement)
                <div class="topic-etab">
                    🏫 {{ $topic->user->etablissement->name }}
                </div>
            @endif

            <div class="topic-date">{{ $topic->created_at->format('d/m/Y H:i') }}</div>
        </div>

        {{-- Actions modération --}}
        <div class="topic-admin-actions" style="margin-left:auto;display:flex;gap:8px;">
            @can('lock', $topic)
                <form method="POST" action="{{ route('admin.forum.lock', $topic) }}" class="inline-form">
                    @csrf
                    <button class="btn-cyber btn-sm" title="{{ $topic->is_locked ? 'Déverrouiller' : 'Verrouiller' }}">
                        {{ $topic->is_locked ? '🔓 Déverrouiller' : '🔒 Verrouiller' }}
                    </button>
                </form>
            @endcan

            @can('delete', $topic)
                <form method="POST" action="{{ route('admin.forum.delete', $topic) }}" class="inline-form"
                      onsubmit="return confirm('Supprimer ce sujet ?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn-delete-text"> Supprimer</button>
                </form>
            @endcan
        </div>
    </div>

    <div class="topic-title">{{ $topic->title }}</div>
    <div class="topic-body">{!! nl2br(e($topic->body)) !!}</div>

</div>

{{-- ── Messages ── --}}
<div class="chat-box" id="chatBox">

    @foreach($messages as $msg)
        <div class="chat-row {{ $msg->user_id === auth()->id() ? 'me' : '' }}">

            <div class="chat-avatar {{ $msg->user_id === auth()->id() ? 'me' : 'other' }}">
                @if($msg->user->avatar)
                    <img src="{{ asset('storage/'.$msg->user->avatar) }}" class="avatar-img">
                @else
                    {{ strtoupper(substr($msg->user->name, 0, 1)) }}
                @endif
            </div>

            <div class="chat-bubble {{ $msg->user_id === auth()->id() ? 'me' : 'other' }}">

                {{-- Nom --}}
                <div class="chat-user">{{ $msg->user->name }}</div>

                {{--  Établissement + rôle de l'auteur du message --}}
                <div class="chat-meta-row">
                    @if($msg->user->etablissement)
                        <span class="chat-etab">
                            🏫 {{ $msg->user->etablissement->name }}
                        </span>
                    @endif
                    @php $roleName = $msg->user->getRoleName(); @endphp
                    @if($roleName)
                        <span class="chat-role chat-role-{{ $roleName }}">
                            {{ match($roleName) {
                                'admin'         => '⚙️ Admin',
                                'enseignant'    => '👨‍🏫 Enseignant',
                                'etudiant'      => '🎓 Étudiant',
                                'eleve'         => '🏫 Élève',
                                'etablissement' => '🏢 Établissement',
                                default         => ucfirst($roleName),
                            } }}
                        </span>
                    @endif
                </div>

                <div class="chat-text">{!! nl2br(e($msg->body)) !!}</div>
                <div class="chat-time">{{ $msg->created_at->diffForHumans() }}</div>

                {{-- Supprimer message --}}
                @if(auth()->id() === $msg->user_id || auth()->user()->isAdmin())
                    <form method="POST"
                          action="{{ route('admin.forum.message.delete', $msg) }}"
                          class="inline-form"
                          onsubmit="return confirm('Supprimer ce message ?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn-delete-text btn-xs">Supprimer</button>
                    </form>
                @endif

            </div>

        </div>
    @endforeach

</div>

{{-- ── Formulaire réponse ── --}}
@if(!$topic->is_locked)
    <form method="POST" action="{{ route('forum.reply', $topic) }}" class="chat-form">
        @csrf
        <textarea name="body" rows="2" required
                  placeholder="Votre message..."
                  class="fi chat-input"></textarea>
        <button type="submit" class="btn-cyber btn-sm chat-send">Envoyer</button>
    </form>
@else
    <div class="chat-locked">🔒 Sujet verrouillé — aucune réponse possible</div>
@endif

@endsection

@push('scripts')
<script>document.getElementById('chatBox')?.scrollTo(0, 999999);</script>
@endpush


