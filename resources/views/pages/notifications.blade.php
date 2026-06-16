@extends('layouts.app')

@section('title', 'Notifications — Smart Emergency AI')
@section('page-title', 'Notifications')
@section('page-subtitle', auth()->user()->unreadNotifications->count() . ' non lue(s)')

@section('content')

    @if(auth()->user()->unreadNotifications->count() > 0)
        <form action="{{ route('notifications.read-all') }}" method="POST" class="mb-3">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-check-all me-1"></i> Tout marquer comme lu
            </button>
        </form>
    @endif

    <div class="sea-card p-0 overflow-hidden">
        @forelse($notifications as $notification)
            <a href="{{ route('notifications.read', $notification->id) }}"
               class="recent-item text-decoration-none d-block {{ $notification->read_at ? '' : 'notification-unread' }}">
                <div class="d-flex align-items-start gap-3">
                    <div class="notification-icon {{ $notification->read_at ? 'read' : '' }}">
                        <i class="bi bi-bell-fill"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">{{ $notification->data['message'] ?? 'Notification' }}</div>
                        <small class="text-muted">
                            {{ $notification->data['reference'] ?? '' }} — {{ $notification->created_at->diffForHumans() }}
                        </small>
                    </div>
                    @unless($notification->read_at)
                        <span class="badge bg-primary rounded-pill">Nouveau</span>
                    @endunless
                </div>
            </a>
        @empty
            <div class="text-center py-5">
                <i class="bi bi-bell-slash fs-1 text-muted"></i>
                <p class="text-muted mt-2">Aucune notification pour le moment.</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="mt-4">{{ $notifications->links() }}</div>
    @endif

@endsection
