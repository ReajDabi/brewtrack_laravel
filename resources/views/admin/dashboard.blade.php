{{-- This extends the main layout — content goes inside @yield('content') --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="page-header">
    <h1>Dashboard</h1>
    <p>Overview of your coffee shop performance</p>
</div>

{{-- STAT CARDS --}}
<div class="stats-grid">
    {{-- Card 1: Today's Sales --}}
    <div class="stat-card">
        <div class="stat-icon brown">
            <i class="fas fa-peso-sign"></i>
        </div>
        <div>
            {{-- number_format adds commas and 2 decimal places --}}
            <div class="stat-value">&#8369;{{ number_format($todaySales->revenue, 2) }}</div>
            <div class="stat-label">Today's Sales</div>
            <div class="stat-sub">{{ $todaySales->order_count }} orders</div>
        </div>
    </div>

    {{-- Card 2: Low Stock --}}
    <div class="stat-card">
        <div class="stat-icon yellow">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div>
            <div class="stat-value">{{ $lowStockCount }}</div>
            <div class="stat-label">Low Stock Items</div>
            @if($lowStockCount > 0)
                <div class="stat-sub danger">Needs attention</div>
            @else
                <div class="stat-sub success">All stocked</div>
            @endif
        </div>
    </div>

    {{-- Card 3: Menu Items --}}
    <div class="stat-card">
        <div class="stat-icon gray">
            <i class="fas fa-utensils"></i>
        </div>
        <div>
            <div class="stat-value">{{ $menuItemCount }}</div>
            <div class="stat-label">Menu Items</div>
            <div class="stat-sub success">Active</div>
        </div>
    </div>
</div>

{{-- TWO-COLUMN GRID --}}
<div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:20px;">

    {{-- Top Selling Items --}}
    <div class="card">
        <div class="card-title">
            <i class="fas fa-trophy"></i> Top Selling Items
        </div>
        @if($topItems->isEmpty())
            <p style="color:#9ca3af; text-align:center; padding:20px 0; font-size:13px;">
                No sales data available for this period
            </p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty Sold</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Loop through each top item --}}
                    @foreach($topItems as $item)
                        <tr>
                            <td style="font-weight:500;">{{ $item->name }}</td>
                            <td>{{ $item->qty }}</td>
                            <td>&#8369;{{ number_format($item->revenue, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Recent Orders --}}
    <div class="card">
        <div class="card-title">
            <i class="fas fa-receipt"></i> Recent Orders
            <a href="{{ route('admin.orders.index') }}"
               style="margin-left:auto; font-size:12px; color:#6F4E37; text-decoration:none;">
                View All
            </a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('admin.orders.show', $order) }}"
                               style="color:#6F4E37; font-weight:500; text-decoration:none;">
                                {{ $order->order_number }}
                            </a>
                        </td>
                        <td>&#8369;{{ number_format($order->total_amount, 2) }}</td>
                        <td>
                            <span class="badge badge-{{ $order->status }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>{{ $order->created_at->format('h:i A') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center; color:#9ca3af; padding:20px;">
                            No orders today
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
{{-- Simple chart using Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// The data comes from the controller, encoded as JSON
const labels  = @json($chartLabels);
const sales   = @json($chartSales);
const orders  = @json($chartOrders);
</script>
@endpush