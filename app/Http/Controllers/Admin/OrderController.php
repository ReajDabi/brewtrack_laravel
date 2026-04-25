<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Show orders list with filters
    public function index(Request $request)
    {
        // Start the query
        $query = Order::with('cashier')->latest();

        // Filter by status if selected
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Default date range — last 7 days
        $dateFrom = $request->get('date_from', now()->subDays(7)->toDateString());
        $dateTo   = $request->get('date_to',   now()->toDateString());

        // Stat cards — totals for the filtered period
        $statsQuery = Order::query();

        if ($request->filled('status')) {
            $statsQuery->where('status', $request->status);
        }

        $statsQuery->whereDate('created_at', '>=', $dateFrom)
                   ->whereDate('created_at', '<=', $dateTo);

        $totalOrders = (clone $statsQuery)->count();
        $totalSales  = (clone $statsQuery)
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');
        $pending     = (clone $statsQuery)->where('status', 'pending')->count();
        $completed   = (clone $statsQuery)->where('status', 'served')->count();

        // Get paginated orders
        $orders = $query->whereDate('created_at', '>=', $dateFrom)
                        ->whereDate('created_at', '<=', $dateTo)
                        ->paginate(20)
                        ->withQueryString();

        return view('admin.orders.index', compact(
            'orders',
            'totalOrders',
            'totalSales',
            'pending',
            'completed',
            'dateFrom',
            'dateTo'
        ));
    }

    // Show a single order detail
    public function show(Order $order)
    {
        // Load the order with its items and each item's menu item
        $order->load('items.menuItem', 'cashier');

        return view('admin.orders.show', compact('order'));
    }

    // Update order status
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,ready,served,cancelled',
        ]);

        $order->update(['status' => $validated['status']]);

        return back()->with('success', 'Order status updated to ' . ucfirst($validated['status']) . '.');
    }
}