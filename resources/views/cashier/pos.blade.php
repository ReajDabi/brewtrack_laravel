<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt — {{ $order->order_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f0e8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .receipt {
            background: white;
            width: 320px;
            border-radius: 12px;
            padding: 28px 24px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.1);
        }
        .receipt-header { text-align: center; margin-bottom: 16px; }
        .receipt-header h1 { font-size: 20px; color: #6F4E37; font-weight: 700; }
        .receipt-header p  { font-size: 12px; color: #6b7280; margin-top: 2px; }
        .divider { border: none; border-top: 1px dashed #d1d5db; margin: 14px 0; }
        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 5px;
            color: #374151;
        }
        .info-row span:last-child { font-weight: 500; }
        .items-table { width: 100%; font-size: 12px; margin-top: 8px; }
        .items-table th {
            text-align: left;
            padding: 5px 0;
            border-bottom: 1px solid #e5e7eb;
            color: #6b7280;
            font-weight: 500;
        }
        .items-table td {
            padding: 6px 0;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: top;
        }
        .items-table .text-right { text-align: right; }
        .totals-section { margin-top: 12px; font-size: 13px; }
        .total-line {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            color: #374151;
        }
        .total-line.grand {
            font-size: 16px;
            font-weight: 700;
            color: #1a1a2e;
            border-top: 2px solid #1a1a2e;
            margin-top: 6px;
            padding-top: 8px;
        }
        .total-line.change { color: #10b981; font-weight: 600; }
        .receipt-footer {
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            margin-top: 18px;
            font-style: italic;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .btn-print {
            flex: 1; padding: 10px;
            background: #6F4E37; color: white;
            border: none; border-radius: 8px;
            font-size: 13px; font-weight: 500;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
        }
        .btn-new {
            flex: 1; padding: 10px;
            background: #e5e7eb; color: #374151;
            border: none; border-radius: 8px;
            font-size: 13px; font-weight: 500;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        /* Hide buttons when printing */
        @media print {
            body { background: white; padding: 0; }
            .receipt { box-shadow: none; }
            .action-buttons { display: none; }
        }
    </style>
</head>
<body>
    <div class="receipt">

        {{-- Shop header --}}
        <div class="receipt-header">
            <h1>
                <i class="fas fa-coffee"></i>
                {{ $settings->get('shop_name', 'BrewTrack') }}
            </h1>
            @if($settings->get('shop_address'))
                <p>{{ $settings->get('shop_address') }}</p>
            @endif
            @if($settings->get('shop_contact'))
                <p>{{ $settings->get('shop_contact') }}</p>
            @endif
            @if($settings->get('receipt_header'))
                <p style="margin-top:6px; font-style:italic;">
                    {{ $settings->get('receipt_header') }}
                </p>
            @endif
        </div>

        <hr class="divider">

        {{-- Order info --}}
        <div class="info-row">
            <span>Order #</span>
            <span>{{ $order->order_number }}</span>
        </div>
        <div class="info-row">
            <span>Date</span>
            <span>{{ $order->created_at->format('M d, Y') }}</span>
        </div>
        <div class="info-row">
            <span>Time</span>
            <span>{{ $order->created_at->format('h:i A') }}</span>
        </div>
        <div class="info-row">
            <span>Cashier</span>
            <span>{{ $order->cashier->full_name ?? '—' }}</span>
        </div>
        @if($order->customer_name)
            <div class="info-row">
                <span>Customer</span>
                <span>{{ $order->customer_name }}</span>
            </div>
        @endif

        <hr class="divider">

        {{-- Items --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>
                            {{ $item->menuItem->name ?? 'Item' }}
                            @if($item->customization)
                                <br>
                                <span style="font-size:10px; color:#9ca3af;">
                                    {{ $item->customization }}
                                </span>
                            @endif
                        </td>
                        <td class="text-right">{{ $item->quantity }}</td>
                        <td class="text-right">
                            &#8369;{{ number_format($item->unit_price, 2) }}
                        </td>
                        <td class="text-right">
                            &#8369;{{ number_format($item->total_price, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <hr class="divider">

        {{-- Totals --}}
        <div class="totals-section">
            <div class="total-line">
                <span>Subtotal</span>
                <span>&#8369;{{ number_format($order->subtotal, 2) }}</span>
            </div>
            @if($order->discount_amount > 0)
                <div class="total-line">
                    <span>Discount</span>
                    <span style="color:#ef4444;">
                        -&#8369;{{ number_format($order->discount_amount, 2) }}
                    </span>
                </div>
            @endif
            <div class="total-line">
                <span>VAT (12%)</span>
                <span>&#8369;{{ number_format($order->tax_amount, 2) }}</span>
            </div>
            <div class="total-line grand">
                <span>TOTAL</span>
                <span>&#8369;{{ number_format($order->total_amount, 2) }}</span>
            </div>
            @if($order->amount_tendered)
                <div class="total-line" style="margin-top:8px;">
                    <span>Cash</span>
                    <span>&#8369;{{ number_format($order->amount_tendered, 2) }}</span>
                </div>
                <div class="total-line change">
                    <span>Change</span>
                    <span>&#8369;{{ number_format($order->change_amount, 2) }}</span>
                </div>
            @endif
            <div class="total-line" style="margin-top:6px; color:#6b7280;">
                <span>Payment</span>
                <span style="text-transform:uppercase; font-weight:600;">
                    {{ $order->payment_method }}
                </span>
            </div>
        </div>

        {{-- Footer --}}
        <div class="receipt-footer">
            {{ $settings->get('receipt_footer', 'Thank you! Please come again.') }}
        </div>

        {{-- Action buttons --}}
        <div class="action-buttons">
            <button class="btn-print" onclick="window.print()">
                <i class="fas fa-print"></i> Print
            </button>
            <a href="{{ route('cashier.pos') }}" class="btn-new">
                <i class="fas fa-plus"></i> New Order
            </a>
        </div>

    </div>
</body>
</html>