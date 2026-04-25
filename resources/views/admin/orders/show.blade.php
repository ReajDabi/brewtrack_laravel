@extends('layouts.app')

@section('title', 'Order ' . $order->order_number)

@section('content')

{{-- Back button + header --}}
<div style="display:flex; align-items:center; gap:16px; margin-bottom:20px;">
    <a href="{{ route('admin.orders.index') }}"
       class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Back
    </a>
    <div>
        <h1 style="font-size:22px; font-weight:700;">{{ $order->order_number }}</h1>
        <p style="color:#6b7280; font-size:13px;">
            {{ $order->created_at->format('F d, Y h:i A') }}
        </p>
    </div>
    <div style="margin-left:auto;">
        <span class="badge badge-{{ $order->status }}"
              style="font-size:14px; padding:6px 14px;">
            {{ ucfirst($order->status) }}
        </span>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 340px; gap:16px;">

    {{-- Left: Order items --}}
    <div>
        <div class="card">
            <div class="card-title">
                <i class="fas fa-shopping-bag"></i> Order Items
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>
                                <div style="font-weight:500;">
                                    {{ $item->menuItem->name ?? 'Unknown Item' }}
                                </div>
                                @if($item->customization)
                                    <div style="font-size:11px; color:#9ca3af;">
                                        {{ $item->customization }}
                                    </div>
                                @endif
                            </td>
                            <td>{{ $item->quantity }}</td>
                            <td>&#8369;{{ number_format($item->unit_price, 2) }}</td>
                            <td style="font-weight:600;">
                                &#8369;{{ number_format($item->total_price, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Right: Order summary --}}
    <div style="display:flex; flex-direction:column; gap:16px;">

        {{-- Payment summary --}}
        <div class="card">
            <div class="card-title">
                <i class="fas fa-receipt"></i> Summary
            </div>

            <div style="font-size:13px;">
                <div style="display:flex; justify-content:space-between;
                            padding:6px 0; border-bottom:1px solid #f3f4f6;">
                    <span style="color:#6b7280;">Subtotal</span>
                    <span>&#8369;{{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if($order->discount_amount > 0)
                    <div style="display:flex; justify-content:space-between;
                                padding:6px 0; border-bottom:1px solid #f3f4f6;">
                        <span style="color:#6b7280;">Discount</span>
                        <span style="color:#ef4444;">
                            -&#8369;{{ number_format($order->discount_amount, 2) }}
                        </span>
                    </div>
                @endif
                <div style="display:flex; justify-content:space-between;
                            padding:6px 0; border-bottom:1px solid #f3f4f6;">
                    <span style="color:#6b7280;">Tax (12%)</span>
                    <span>&#8369;{{ number_format($order->tax_amount, 2) }}</span>
                </div>
                <div style="display:flex; justify-content:space-between;
                            padding:8px 0; font-weight:700; font-size:16px;">
                    <span>Total</span>
                    <span>&#8369;{{ number_format($order->total_amount, 2) }}</span>
                </div>
                @if($order->amount_tendered)
                    <div style="display:flex; justify-content:space-between;
                                padding:6px 0; border-top:1px solid #f3f4f6;">
                        <span style="color:#6b7280;">Amount Tendered</span>
                        <span>&#8369;{{ number_format($order->amount_tendered, 2) }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between;
                                padding:6px 0; color:#10b981; font-weight:600;">
                        <span>Change</span>
                        <span>&#8369;{{ number_format($order->change_amount, 2) }}</span>
                    </div>
                @endif
            </div>

            <div style="margin-top:12px; padding-top:12px;
                        border-top:1px solid #f3f4f6; font-size:13px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                    <span style="color:#6b7280;">Payment</span>
                    <span style="font-weight:600; text-transform:uppercase;">
                        {{ $order->payment_method }}
                    </span>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:#6b7280;">Cashier</span>
                    <span>{{ $order->cashier->full_name ?? '—' }}</span>
                </div>
                @if($order->customer_name)
                    <div style="display:flex; justify-content:space-between; margin-top:6px;">
                        <span style="color:#6b7280;">Customer</span>
                        <span>{{ $order->customer_name }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Update status --}}
        @if($order->status !== 'served' && $order->status !== 'cancelled')
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-edit"></i> Update Status
                </div>
                <form method="POST"
                      action="{{ route('admin.orders.status', $order) }}">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <select name="status" class="form-control">
                            <option value="pending"   {{ $order->status === 'pending'   ? 'selected' : '' }}>Pending</option>
                            <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                            <option value="ready"     {{ $order->status === 'ready'     ? 'selected' : '' }}>Ready</option>
                            <option value="served"    {{ $order->status === 'served'    ? 'selected' : '' }}>Served</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary"
                            style="width:100%;">
                        <i class="fas fa-save"></i> Update Status
                    </button>
                </form>
            </div>
        @endif

        {{-- Notes --}}
        @if($order->notes)
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-sticky-note"></i> Notes
                </div>
                <p style="font-size:13px; color:#374151;">
                    {{ $order->notes }}
                </p>
            </div>
        @endif

    </div>
</div>

@endsection