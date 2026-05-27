@extends('layouts.app')

@section('title', 'Classement')
@section('page-title', '🥇 Classement')

@section('content')

<div class="cyber-card leaderboard-card">

    @foreach($users as $i => $p)

        @php
            $rank = $users->firstItem() + $i;
            $isMe = $p->id === auth()->id();
        @endphp

        <div class="leaderboard-row {{ $isMe ? 'leaderboard-me' : '' }}">

            {{--  Rang --}}
            <span class="leaderboard-rank">
                {{ $rank === 1 ? '🥇' : ($rank === 2 ? '🥈' : ($rank === 3 ? '🥉' : $rank)) }}
            </span>

            {{--  Avatar --}}
            <div class="leaderboard-avatar">

                @if($p->avatar)

                    <img src="{{ asset('storage/'.$p->avatar) }}" class="avatar-img">

                @else

                    {{ strtoupper(substr($p->name,0,1)) }}

                @endif

            </div>

            {{--  Infos --}}
            <div class="leaderboard-info">

                <a href="{{ route('profile.public', $p) }}" class="leaderboard-name">
                    {{ $p->name }}
                </a>

                <div class="leaderboard-meta">
                    {{ $p->role_display }} · Niv. {{ $p->level }}
                </div>

            </div>

            {{--  Points --}}
            <span class="leaderboard-points">
                {{ $p->points }} pts
            </span>

        </div>

    @endforeach

</div>

{{--  Pagination --}}
<div class="leaderboard-pagination">
    {{ $users->links() }}
</div>

@endsection