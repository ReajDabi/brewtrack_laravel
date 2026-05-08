<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockNotification;
use App\Models\Inventory;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Full notifications page
    public function index()
    {
        $notifications = StockNotification::with('inventory')
            ->latest('created_at')
            ->paginate(30);

        $unreadCount = StockNotification::where('is_read', false)->count();

        return view('admin.notifications.index',
            compact('notifications', 'unreadCount'));
    }

    // API endpoint — called by JavaScript every 10 seconds
    public function unread()
    {
        $notifications = StockNotification::where('is_read', false)
            ->with('inventory')
            ->latest('created_at')
            ->limit(10)
            ->get()
            ->map(function ($n) {
                return [
                    'id'      => $n->id,
                    'type'    => $n->notification_type,
                    'item'    => $n->inventory->item_name ?? 'Unknown Item',
                    'stock'   => $n->inventory->quantity_in_stock ?? 0,
                    'unit'    => $n->inventory->unit_of_measure ?? '',
                    'message' => $n->message,
                    'time'    => $n->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'count'         => $notifications->count(),
            'notifications' => $notifications,
        ]);
    }

    // Mark one notification as read
    public function markRead(StockNotification $notification)
    {
        $notification->update(['is_read' => true]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    // Mark ALL notifications as read
    public function markAllRead()
    {
        StockNotification::where('is_read', false)
            ->update(['is_read' => true]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'All notifications cleared.');
    }
}