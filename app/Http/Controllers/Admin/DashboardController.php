<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Inventory;
use App\Models\MenuItem;
use App\Models\StockNotification;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // This shows the main admin dashboard (GET /admin/dashboard)
    public function index()
    {
        $today = now()->toDateString();

        // === STAT CARDS ===

        // Card 1: Today's sales total + order count
        $todaySales = Order::whereDate('created_at', $today)
            ->where('status', '!=', 'cancelled')
            ->selectRaw('COUNT(*) as order_count, COALESCE(SUM(total_amount), 0) as revenue')
            ->first();

        // Card 2: Low stock item count
        $lowStockCount = Inventory::where('is_active', true)
            ->whereColumn('quantity_in_stock', '<=', 'reorder_level')
            ->count();

        // Card 3: Total active menu items
        $menuItemCount = MenuItem::where('is_active', true)->where('is_available', true)->count();

        // === SALES CHART (last 7 days) ===
        // Gets daily totals to draw the bar/line chart
        $salesByDay = Order::where('status', '!=', 'cancelled')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date'); // Index by date for easy lookup

        // Build a full 7-day array (fill missing days with zeros)
        $chartLabels  = [];
        $chartSales   = [];
        $chartOrders  = [];
        for ($i = 6; $i >= 0; $i--) {
            $date           = now()->subDays($i)->toDateString();
            $chartLabels[]  = now()->subDays($i)->format('D'); // "Mon", "Tue", etc.
            $chartSales[]   = $salesByDay->get($date)?->total  ?? 0;
            $chartOrders[]  = $salesByDay->get($date)?->orders ?? 0;
        }

        // === TOP SELLING ITEMS (this month) ===
        $topItems = DB::table('order_items')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->join('orders',     'order_items.order_id',     '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled')
            ->whereYear('orders.created_at',  now()->year)
            ->whereMonth('orders.created_at', now()->month)
            ->selectRaw('menu_items.name, SUM(order_items.quantity) as qty, SUM(order_items.total_price) as revenue')
            ->groupBy('menu_items.id', 'menu_items.name')
            ->orderByDesc('qty')
            ->limit(5)
            ->get();

        // === RECENT ORDERS ===
        $recentOrders = Order::with('cashier')
            ->latest()
            ->limit(10)
            ->get();

        // === UNREAD NOTIFICATIONS ===
        $notifications = \App\Models\StockNotification::where('is_read', false)
            ->with('inventory')
            ->latest()
            ->limit(5)
            ->get();

        // Pass all data to the view
        return view('admin.dashboard', compact(
            'todaySales', 'lowStockCount', 'menuItemCount',
            'chartLabels', 'chartSales', 'chartOrders',
            'topItems', 'recentOrders', 'notifications'
        ));
    }
}