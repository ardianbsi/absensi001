@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
    <x-breadcrumb :items="[['name' => 'Notifications']]" />

    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Notifications</h2>
                <div class="text-muted mt-1">{{ $unreadCount ?? 0 }} unread notifications</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    @if($unreadCount > 0)
                        <form action="{{ route('notifications.read-all') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="ti ti-check-all"></i> Mark All Read
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="list-group list-group-flush">
            @forelse($notifications as $notification)
                <div class="list-group-item {{ $notification->read_at ? '' : 'bg-blue-lt' }}">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <i class="ti ti-bell-ringing text-primary fs-2"></i>
                        </div>
                        <div class="col text-truncate">
                            <p class="mb-0">{{ $notification->data['message'] ?? 'No message' }}</p>
                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            @if(!empty($notification->data['details']))
                                <div class="text-muted small">{{ $notification->data['details'] }}</div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <div class="btn-group btn-group-sm">
                                @if(!$notification->read_at)
                                    <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success" title="Mark as read">
                                            <i class="ti ti-check"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('notifications.delete', $notification->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this notification?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="list-group-item text-center text-muted py-5">
                    <i class="ti ti-bell-off fs-1 mb-2 d-block"></i>
                    <p class="mb-0">No notifications</p>
                </div>
            @endforelse
        </div>
    </div>
    <div class="mt-3">{{ $notifications->links() }}</div>
@endsection
