@extends('layouts.app')

@section('title', 'Order History')

@section('content')

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
    <div class="page-header" style="margin-bottom:0;">
        <h1>Order History</h1>
        <p>Your orders for today</p>
    </div>
    <a href="{{ route('cashier.pos') }}" class="btn btn-primary">
        <i class="fas fa-cash-register"></i> Back to POS
    </a>
</div>

{{-- Date filter --}}
<div class="card" style="margin-bottom:16px;">
    <form method="GET" style="display:flex; gap:10px; align-items:flex-end;">
        <div>
            <label style="font-size:12px; font-weight:600; color:#6b7280; display:block; margin-bottom:5px;">Date</label>
            <input type="date" name="date"
                   class="form-control" style="width:180px;"
                   value="{{ request('date', now()->toDateString()) }}">
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Filter
        </button>
    </form>
</div>

{{-- Orders table --}}
<div class="card">
    <div class="card-title">
        <i class="fas fa-history"></i> My Orders
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Time</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Actions</th> {{-- Changed from Receipt to Actions --}}
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td style="font-weight:700; color:#6F4E37;">
                            {{ $order->order_number }}
                        </td>
                        <td style="color:#6b7280; font-size:12px;">
                            {{ $order->created_at->format('h:i A') }}
                        </td>
                        <td>{{ $order->customer_name ?? 'Walk-in' }}</td>
                        <td>{{ $order->items->count() }} item(s)</td>
                        <td style="font-weight:600;">
                            &#8369;{{ number_format($order->total_amount, 2) }}
                        </td>
                        <td style="text-transform:uppercase; font-size:12px;">
                            {{ $order->payment_method }}
                        </td>
                        <td>
                            <span class="badge badge-{{ $order->status }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex; gap:6px; align-items:center;">
                                {{-- Receipt Button --}}
                                <a href="{{ route('cashier.orders.receipt', $order) }}"
                                   class="btn btn-sm btn-edit"
                                   target="_blank" title="Print Receipt">
                                    <i class="fas fa-receipt"></i>
                                </a>

                                {{-- Quick Status Update Dropdown --}}
                                @if($order->status !== 'served' && $order->status !== 'cancelled')
                                    <form method="POST" action="{{ route('cashier.orders.status', $order) }}">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status"
                                                class="form-control"
                                                style="width:110px; padding:4px 8px; font-size:12px;"
                                                onchange="this.form.submit()">
                                            <option value="">Update...</option>
                                            <option value="pending" {{ $order->status === 'pending' ? 'disabled' : '' }}>Pending</option>
                                            <option value="preparing" {{ $order->status === 'preparing' ? 'disabled' : '' }}>Preparing</option>
                                            <option value="ready" {{ $order->status === 'ready' ? 'disabled' : '' }}>Ready</option>
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
                        <td colspan="8" style="text-align:center; padding:60px; color:#9ca3af;">
                            <i class="fas fa-receipt" style="font-size:40px; display:block; margin-bottom:12px; opacity:0.3;"></i>
                            No orders found for this date.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">
        {{ $orders->links() }}
    </div>
</div>

@endsection