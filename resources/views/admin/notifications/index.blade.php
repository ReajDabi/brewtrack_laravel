@extends('layouts.app')

@section('title', 'Notifications')

@section('content')

{{-- Mobile Responsive Styles specifically for this view --}}
<style>
    .notifications-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    /* Mobile Breakpoint */
    @media (max-width: 768px) {
        .notifications-header {
            flex-direction: column;
            align-items: stretch;
        }
        .page-header {
            text-align: center;
            margin-bottom: 5px !important;
        }
        .notifications-header form, 
        .notifications-header .btn {
            width: 100%;
            display: flex;
            justify-content: center;
        }
    }
</style>

{{-- Header --}}
<div class="notifications-header">
    <div class="page-header" style="margin-bottom:0;">
        <h1>Notifications</h1>
        <p>Stock alerts and system notifications</p>
    </div>

    @if($unreadCount > 0)
        <form method="POST" action="{{ route('admin.notifications.read-all') }}">
            @csrf
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-check-double"></i>
                Mark All Read ({{ $unreadCount }})
            </button>
        </form>
    @endif
</div>

<div class="card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Item</th>
                    <th>Message</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notifications as $notif)
                    <tr style="{{ !$notif->is_read ? 'background:#fffbeb;' : '' }}">
                        <td>
                            @if($notif->notification_type === 'critical_stock')
                                <span class="badge" style="background:#fee2e2; color:#991b1b;">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Critical
                                </span>
                            @else
                                <span class="badge" style="background:#fef3c7; color:#92400e;">
                                    <i class="fas fa-exclamation-circle"></i>
                                    Low Stock
                                </span>
                            @endif
                        </td>
                        <td style="font-weight:600;">
                            {{ $notif->inventory->item_name ?? '—' }}
                        </td>
                        <td style="font-size:12px; color:#6b7280; max-width:280px;">
                            {{ $notif->message }}
                        </td>
                        <td style="font-size:12px; color:#9ca3af; white-space:nowrap;">
                            {{ $notif->created_at->diffForHumans() }}
                        </td>
                        <td>
                            @if($notif->is_read)
                                <span style="font-size:12px; color:#9ca3af;">
                                    Read
                                </span>
                            @else
                                <span class="badge" style="background:#dbeafe; color:#1e40af;">
                                    New
                                </span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <a href="{{ route('admin.inventory.edit', $notif->inventory_id) }}" class="btn btn-sm btn-edit">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(!$notif->is_read)
                                    <form method="POST" action="{{ route('admin.notifications.markRead', $notif) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding:60px; color:#9ca3af;">
                            <i class="fas fa-bell-slash" style="font-size:36px; display:block; margin-bottom:12px; opacity:0.3;"></i>
                            No notifications
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">
        {{ $notifications->links() }}
    </div>
</div>

@endsection