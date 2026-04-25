@extends('layouts.app')

@section('title', 'Orders')

@section('content')

{{-- Page header --}}
<div class="page-header">
    <h1>Orders</h1>
    <p>View and manage all orders</p>
</div>

{{-- Stat cards --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon icon-gray">
            <i class="fas fa-receipt"></i>
        </div>
        <div>
            <div class="stat-value">{{ $totalOrders }}</div>
            <div class="stat-label">Total Orders</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon icon-brown">
            <i class="fas fa-peso-sign"></i>
        </div>
        <div>
            <div class="stat-value">&#8369;{{ number_format($totalSales, 2) }}</div>
            <div class="stat-label">Total Sales</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon icon-yellow">
            <i class="fas fa-clock"></i>
        </div>
        <div>
            <div class="stat-value">{{ $pending }}</div>
            <div class="stat-label">Pending</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon icon-green">
            <i class="fas fa-check"></i>
        </div>
        <div>
            <div class="stat-value">{{ $completed }}</div>
            <div class="stat-label">Completed</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card" style="margin-bottom:16px;">
    <form method="GET" style="display:flex; align-items:flex-end; gap:12px; flex-wrap:wrap;">

        <div>
            <label style="font-size:12px; font-weight:600; color:#6b7280;
                          display:block; margin-bottom:5px;">Status</label>
            <select name="status" class="form-control" style="width:160px;">
                <option value="">All Status</option>
                <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
                <option value="preparing" {{ request('status') === 'preparing' ? 'selected' : '' }}>Preparing</option>
                <option value="ready"     {{ request('status') === 'ready'     ? 'selected' : '' }}>Ready</option>
                <option value="served"    {{ request('status') === 'served'    ? 'selected' : '' }}>Served</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

        <div>
            <label style="font-size:12px; font-weight:600; color:#6b7280;
                          display:block; margin-bottom:5px;">From Date</label>
            <input type="date" name="date_from"
                   class="form-control" style="width:160px;"
                   value="{{ $dateFrom }}">
        </div>

        <div>
            <label style="font-size:12px; font-weight:600; color:#6b7280;
                          display:block; margin-bottom:5px;">To Date</label>
            <input type="date" name="date_to"
                   class="form-control" style="width:160px;"
                   value="{{ $dateTo }}">
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-filter"></i> Apply Filters
        </button>

        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
            <i class="fas fa-times"></i> Clear
        </a>
    </form>
</div>

{{-- Orders table --}}
<div class="card">
    <div class="card-title">
        <i class="fas fa-list"></i> Order List
    </div>

    <table>
        <thead>
            <tr>
                <th>Order #</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Cashier</th>
                <th>Total</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td style="font-weight:700; color:#6F4E37;">
                        {{ $order->order_number }}
                    </td>
                    <td style="font-size:12px; color:#6b7280;">
                        {{ $order->created_at->format('M d, Y') }}<br>
                        {{ $order->created_at->format('h:i A') }}
                    </td>
                    <td>{{ $order->customer_name ?? 'Walk-in' }}</td>
                    <td>{{ $order->items->count() }} item(s)</td>
                    <td>{{ $order->cashier->full_name ?? '—' }}</td>
                    <td style="font-weight:600;">
                        &#8369;{{ number_format($order->total_amount, 2) }}
                    </td>
                    <td>
                        <span class="badge badge-{{ $order->status }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex; gap:6px;">
                            {{-- View button --}}
                            <a href="{{ route('admin.orders.show', $order) }}"
                               class="btn btn-sm btn-edit">
                                <i class="fas fa-eye"></i>
                            </a>

                            {{-- Quick status update --}}
                            @if($order->status !== 'served' && $order->status !== 'cancelled')
                                <form method="POST"
                                      action="{{ route('admin.orders.status', $order) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status"
                                            class="form-control"
                                            style="width:110px; padding:4px 8px; font-size:12px;"
                                            onchange="this.form.submit()">
                                        <option value="">Update...</option>
                                        <option value="pending">Pending</option>
                                        <option value="preparing">Preparing</option>
                                        <option value="ready">Ready</option>
                                        <option value="served">Served</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8"
                        style="text-align:center; padding:60px; color:#9ca3af;">
                        <i class="fas fa-receipt"
                           style="font-size:40px; display:block; margin-bottom:12px; opacity:0.3;"></i>
                        No orders found for the selected criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    <div style="margin-top:16px;">
        {{ $orders->links() }}
    </div>
</div>

@endsection