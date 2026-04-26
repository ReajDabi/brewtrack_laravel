<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // Show the reports page
    public function index(Request $request)
    {
        // Default date range — current month
        $dateFrom = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->get('date_to',   now()->toDateString());
        $type     = $request->get('type', 'sales');

        // === SALES SUMMARY ===
        $summary = Order::where('status', '!=', 'cancelled')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->selectRaw('
                COUNT(*) as total_orders,
                COALESCE(SUM(subtotal), 0)         as subtotal,
                COALESCE(SUM(tax_amount), 0)       as total_tax,
                COALESCE(SUM(discount_amount), 0)  as total_discount,
                COALESCE(SUM(total_amount), 0)     as total_sales
            ')
            ->first();

        // === SALES BY DAY ===
        $salesByDay = Order::where('status', '!=', 'cancelled')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->selectRaw('
                DATE(created_at) as date,
                COUNT(*) as orders,
                COALESCE(SUM(total_amount), 0) as sales,
                COALESCE(SUM(tax_amount), 0)   as tax
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // === TOP SELLING ITEMS ===
        $topItems = DB::table('order_items')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->join('orders',     'order_items.order_id',     '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled')
            ->whereDate('orders.created_at', '>=', $dateFrom)
            ->whereDate('orders.created_at', '<=', $dateTo)
            ->selectRaw('
                menu_items.name,
                SUM(order_items.quantity)    as total_qty,
                SUM(order_items.total_price) as total_revenue
            ')
            ->groupBy('menu_items.id', 'menu_items.name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        // === SALES BY PAYMENT METHOD ===
        $byPayment = Order::where('status', '!=', 'cancelled')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->selectRaw('
                payment_method,
                COUNT(*) as orders,
                COALESCE(SUM(total_amount), 0) as total
            ')
            ->groupBy('payment_method')
            ->get();

        // === INVENTORY REPORT ===
        $inventoryItems = Inventory::where('is_active', true)
            ->orderBy('item_name')
            ->get();

        return view('admin.reports.index', compact(
            'summary',
            'salesByDay',
            'topItems',
            'byPayment',
            'inventoryItems',
            'dateFrom',
            'dateTo',
            'type'
        ));
    }

    // Export sales as CSV download
    public function export(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->get('date_to',   now()->toDateString());

        $orders = Order::with('items.menuItem', 'cashier')
            ->where('status', '!=', 'cancelled')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->orderBy('created_at')
            ->get();

        $filename = 'brewtrack-sales-' . $dateFrom . '-to-' . $dateTo . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');

            // CSV Header row
            fputcsv($file, [
                'Order #', 'Date', 'Time', 'Cashier',
                'Customer', 'Items', 'Subtotal',
                'Discount', 'Tax', 'Total', 'Payment'
            ]);

            // CSV Data rows
            foreach ($orders as $order) {
                $itemNames = $order->items
                    ->map(fn($i) => $i->quantity . 'x ' . ($i->menuItem->name ?? '?'))
                    ->implode(', ');

                fputcsv($file, [
                    $order->order_number,
                    $order->created_at->format('Y-m-d'),
                    $order->created_at->format('H:i'),
                    $order->cashier->full_name ?? '—',
                    $order->customer_name ?? 'Walk-in',
                    $itemNames,
                    $order->subtotal,
                    $order->discount_amount,
                    $order->tax_amount,
                    $order->total_amount,
                    strtoupper($order->payment_method),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}