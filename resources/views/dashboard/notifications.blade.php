@extends('layouts.app')

@section('title', 'Notifications')
@section('page-title', ' Notifications')

@section('content')

{{--  Header --}}
<div class="notifications-header">

    <div class="notifications-count">
        {{ $notifications->total() }} notifications
    </div>

    <form method="POST" action="{{ route('notifications.readAll') }}">
        @csrf
        <button type="submit" class="btn-cyber-outline btn-sm">
            ✓ Tout marquer lu
        </button>
    </form>

</div>

{{--  Liste --}}
@foreach($notifications as $n)

    <div class="cyber-card notification-item {{ $n->isRead() ? 'notif-read' : 'notif-unread' }}">

        {{--  Icône --}}
        <div class="notification-icon">
            {{ $n->icon }}
        </div>

        {{--  Contenu --}}
        <div class="notification-content">

            <div class="notification-title {{ $n->isRead() ? '' : 'notif-highlight' }}">
                {{ $n->title }}
            </div>

            <div class="notification-message">
                {{ $n->message }}
            </div>

            <div class="notification-time">
                {{ $n->created_at->diffForHumans() }}
            </div>

        </div>

        {{--  Action --}}
        @if(!$n->isRead())

            <form method="POST" action="{{ route('notifications.read',$n) }}">
                @csrf
                <button type="submit" class="notif-read-btn">
                    ✓
                </button>
            </form>

        @endif

    </div>

@endforeach

{{--  Pagination --}}
<div class="notifications-pagination">
    {{ $notifications->links() }}
</div>

@endsection