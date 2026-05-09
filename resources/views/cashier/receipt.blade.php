<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt — {{ $order->order_number }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>

        /* =============================================
           SCREEN STYLES (how it looks in browser)
        ============================================= */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
            background: #f5f0e8;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        /* Action buttons shown on screen only */
        .screen-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
            width: 320px;
        }
        .btn-print {
            flex: 1; padding: 12px;
            background: #6F4E37; color: white;
            border: none; border-radius: 8px;
            font-size: 14px; font-weight: 600;
            cursor: pointer;
            font-family: 'Courier New', monospace;
            display: flex; align-items: center;
            justify-content: center; gap: 8px;
        }
        .btn-print:hover { background: #5a3d2b; }

        .btn-new {
            flex: 1; padding: 12px;
            background: #e5e7eb; color: #374151;
            border: none; border-radius: 8px;
            font-size: 14px; font-weight: 600;
            cursor: pointer;
            font-family: 'Courier New', monospace;
            text-decoration: none;
            display: flex; align-items: center;
            justify-content: center; gap: 8px;
        }
        .btn-new:hover { background: #d1d5db; }

        /* The receipt card on screen */
        .receipt-wrapper {
            background: white;
            width: 320px;
            padding: 16px 14px;
            border-radius: 10px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.12);
        }

        /* =============================================
           RECEIPT CONTENT STYLES
           (used for both screen and print)
        ============================================= */
        .receipt-header { text-align: center; margin-bottom: 8px; }

        .shop-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 3px;
        }

        .shop-info {
            font-size: 11px;
            color: #444;
            line-height: 1.5;
        }

        .receipt-tagline {
            font-size: 11px;
            font-style: italic;
            margin-top: 4px;
        }

        /* Dividers */
        .divider-solid  {
            border: none;
            border-top: 1px solid black;
            margin: 8px 0;
        }
        .divider-dashed {
            border: none;
            border-top: 1px dashed #555;
            margin: 6px 0;
        }

        /* Order number display */
        .order-number-box {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 3px;
            padding: 4px 0;
        }

        /* Info rows like "Date: 04/25/2026" */
        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-bottom: 3px;
        }

        /* Items table */
        .items-table { width: 100%; border-collapse: collapse; font-size: 11px; }
        .items-table th {
            text-align: left;
            padding: 4px 0;
            border-bottom: 1px dashed #555;
            font-weight: bold;
        }
        .items-table th.text-right,
        .items-table td.text-right { text-align: right; }
        .items-table td { padding: 4px 0; vertical-align: top; }
        .item-unit-price { font-size: 10px; color: #555; }

        /* Total lines */
        .total-line {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            padding: 2px 0;
        }
        .total-line.grand {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid black;
            border-bottom: 2px solid black;
            padding: 5px 0;
            margin: 4px 0;
        }
        .total-line.change-line {
            font-weight: bold;
            font-size: 13px;
        }

        /* Footer */
        .receipt-footer {
            text-align: center;
            margin-top: 10px;
            font-size: 11px;
            line-height: 1.6;
        }

        .thank-you {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        /* =============================================
           PRINT STYLES
           These override screen styles when printing
        ============================================= */
        @media print {

            /* Set thermal paper size */
            @page {
                size: 80mm auto;
                margin: 3mm 2mm;
            }

            /* Reset body for printing */
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
                display: block !important;
                min-height: 0 !important;
            }

            /* Hide the screen buttons */
            .screen-buttons { display: none !important; }

            /* Make receipt fill the paper */
            .receipt-wrapper {
                width: 100% !important;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                margin: 0 !important;
            }

            /* Force everything to black */
            * { color: black !important; }

            /* Prevent page breaks inside receipt */
            .receipt-wrapper { page-break-inside: avoid; }

            /* Remove background colors */
            .divider-solid  { border-top-color: black !important; }
            .divider-dashed { border-top-color: black !important; }
        }
    </style>
</head>
<body>

    {{-- Screen-only buttons (hidden when printing) --}}
    <div class="screen-buttons">
        <button class="btn-print" onclick="window.print()">
            <i class="fas fa-print"></i> Print
        </button>
        <a href="{{ route('cashier.pos') }}" class="btn-new">
            <i class="fas fa-plus"></i> New Order
        </a>
    </div>

    {{-- Receipt content (printed on thermal paper) --}}
    <div class="receipt-wrapper">

        {{-- Shop name and info --}}
        <div class="receipt-header">
            <div class="shop-name">
                {{ $settings->get('shop_name', 'BREWTRACK') }}
            </div>
            @if($settings->get('shop_address'))
                <div class="shop-info">{{ $settings->get('shop_address') }}</div>
            @endif
            @if($settings->get('shop_contact'))
                <div class="shop-info">Tel: {{ $settings->get('shop_contact') }}</div>
            @endif
            @if($settings->get('receipt_header'))
                <div class="receipt-tagline">
                    {{ $settings->get('receipt_header') }}
                </div>
            @endif
        </div>

        <hr class="divider-solid">

        {{-- Order number --}}
        <div class="order-number-box">
            {{ $order->order_number }}
        </div>

        <hr class="divider-dashed">

        {{-- Order details --}}
        <div class="info-row">
            <span>Date</span>
            <span>{{ $order->created_at->format('m/d/Y') }}</span>
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

        <hr class="divider-dashed">

        {{-- Ordered items --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th>ITEM</th>
                    <th class="text-right">QTY</th>
                    <th class="text-right">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>
                            <div style="font-weight:bold;">
                                {{ strtoupper($item->menuItem->name ?? 'ITEM') }}
                            </div>
                            <div class="item-unit-price">
                                @ &#8369;{{ number_format($item->unit_price, 2) }} each
                            </div>
                            @if($item->customization)
                                <div class="item-unit-price">
                                    * {{ $item->customization }}
                                </div>
                            @endif
                        </td>
                        <td class="text-right">{{ $item->quantity }}</td>
                        <td class="text-right">
                            &#8369;{{ number_format($item->total_price, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <hr class="divider-dashed">

        {{-- Totals --}}
        <div class="total-line">
            <span>Subtotal</span>
            <span>&#8369;{{ number_format($order->subtotal, 2) }}</span>
        </div>

        @if($order->discount_amount > 0)
            <div class="total-line">
                <span>Discount</span>
                <span>- &#8369;{{ number_format($order->discount_amount, 2) }}</span>
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
            <div class="total-line">
                <span>Cash Tendered</span>
                <span>&#8369;{{ number_format($order->amount_tendered, 2) }}</span>
            </div>
            <div class="total-line change-line">
                <span>Change</span>
                <span>&#8369;{{ number_format($order->change_amount, 2) }}</span>
            </div>
        @endif

        <div class="total-line" style="margin-top:4px; font-size:11px;">
            <span>Payment Method</span>
            <span style="font-weight:bold; text-transform:uppercase;">
                {{ $order->payment_method }}
            </span>
        </div>

        <hr class="divider-solid">

        {{-- Footer message --}}
        <div class="receipt-footer">
            <div class="thank-you">
                {{ $settings->get('receipt_footer', 'Thank You!') }}
            </div>
            <div>Please come again!</div>
            <div style="margin-top:6px; font-size:10px; color:#555;">
                {{ now()->format('Y') }} {{ $settings->get('shop_name', 'BrewTrack') }}
            </div>
        </div>

    </div>


</body>
</html>