<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'Courier New', monospace; /* Monospace looks best on thermal */
        font-size: 12px;
        background: #f5f0e8;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .receipt {
        background: white;
        width: 300px; /* 80mm paper width */
        padding: 10px 8px;
        border-radius: 8px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.1);
    }

    .receipt-header { text-align: center; margin-bottom: 8px; }
    .receipt-header h1 {
        font-size: 16px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 2px;
    }
    .receipt-header p { font-size: 11px; color: #444; margin-top: 2px; }

    .divider-solid  { border-top: 1px solid #000; margin: 6px 0; }
    .divider-dashed { border-top: 1px dashed #555; margin: 6px 0; }

    .info-row {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
        margin-bottom: 3px;
    }

    /* Items table */
    .items-table { width: 100%; font-size: 11px; }
    .items-table th {
        text-align: left;
        border-bottom: 1px dashed #555;
        padding: 3px 0;
        font-weight: bold;
    }
    .items-table td { padding: 3px 0; vertical-align: top; }
    .text-right { text-align: right; }

    /* Totals */
    .total-line {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        padding: 2px 0;
    }
    .total-line.grand {
        font-size: 14px;
        font-weight: bold;
        border-top: 1px solid #000;
        border-bottom: 1px solid #000;
        padding: 4px 0;
        margin: 4px 0;
    }
    .total-line.change { font-weight: bold; }

    .receipt-footer {
        text-align: center;
        font-size: 11px;
        margin-top: 10px;
    }

    /* Barcode-style order number */
    .order-number {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        letter-spacing: 3px;
        margin: 6px 0;
    }

    /* Action buttons — hidden when printing */
    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 16px;
    }
    .btn-print {
        flex: 1; padding: 10px;
        background: #6F4E37; color: white;
        border: none; border-radius: 8px;
        font-size: 13px; font-weight: 500;
        cursor: pointer;
        font-family: 'Poppins', sans-serif;
    }
    .btn-new {
        flex: 1; padding: 10px;
        background: #e5e7eb; color: #374151;
        border: none; border-radius: 8px;
        font-size: 13px; font-weight: 500;
        text-decoration: none; text-align: center;
        display: flex; align-items: center;
        justify-content: center; gap: 6px;
        font-family: 'Poppins', sans-serif;
    }

    /* =============================================
       THERMAL PRINT STYLES
       These only apply when Ctrl+P or Print is used
    ============================================= */
    @media print {
        /* Hide everything except the receipt */
        body {
            background: white;
            padding: 0;
            display: block;
        }

        .receipt {
            width: 72mm;       /* 80mm paper - margins = ~72mm printable */
            box-shadow: none;
            border-radius: 0;
            padding: 2mm;
            margin: 0;
        }

        /* Hide buttons */
        .action-buttons { display: none !important; }

        /* Force black text — no gray on thermal */
        * { color: black !important; }

        /* No page breaks inside receipt */
        .receipt { page-break-inside: avoid; }

        /* Remove browser header/footer when printing */
        @page {
            size: 80mm auto;   /* 80mm wide, auto height */
            margin: 3mm;
        }
    }
</style>
<div class="receipt">

    {{-- Shop header --}}
    <div class="receipt-header">
        <h1>{{ $settings->get('shop_name', 'BrewTrack') }}</h1>
        @if($settings->get('shop_address'))
            <p>{{ $settings->get('shop_address') }}</p>
        @endif
        @if($settings->get('shop_contact'))
            <p>Tel: {{ $settings->get('shop_contact') }}</p>
        @endif
        @if($settings->get('receipt_header'))
            <p style="margin-top:4px;">
                {{ $settings->get('receipt_header') }}
            </p>
        @endif
    </div>

    <div class="divider-solid"></div>

    {{-- Order number --}}
    <div class="order-number">{{ $order->order_number }}</div>

    {{-- Order details --}}
    <div class="info-row">
        <span>Date:</span>
        <span>{{ $order->created_at->format('m/d/Y') }}</span>
    </div>
    <div class="info-row">
        <span>Time:</span>
        <span>{{ $order->created_at->format('h:i A') }}</span>
    </div>
    <div class="info-row">
        <span>Cashier:</span>
        <span>{{ $order->cashier->full_name ?? '—' }}</span>
    </div>
    @if($order->customer_name)
        <div class="info-row">
            <span>Customer:</span>
            <span>{{ $order->customer_name }}</span>
        </div>
    @endif

    <div class="divider-dashed"></div>

    {{-- Items --}}
    <table class="items-table">
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Amt</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>
                        {{ $item->menuItem->name ?? 'Item' }}
                        @if($item->customization)
                            <br>
                            <span style="font-size:10px;">
                                * {{ $item->customization }}
                            </span>
                        @endif
                        <br>
                        <span style="font-size:10px;">
                            @&#8369;{{ number_format($item->unit_price, 2) }}
                        </span>
                    </td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">
                        &#8369;{{ number_format($item->total_price, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider-dashed"></div>

    {{-- Totals --}}
    <div class="total-line">
        <span>Subtotal</span>
        <span>&#8369;{{ number_format($order->subtotal, 2) }}</span>
    </div>
    @if($order->discount_amount > 0)
        <div class="total-line">
            <span>Discount</span>
            <span>-&#8369;{{ number_format($order->discount_amount, 2) }}</span>
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
            <span>Cash</span>
            <span>&#8369;{{ number_format($order->amount_tendered, 2) }}</span>
        </div>
        <div class="total-line change">
            <span>Change</span>
            <span>&#8369;{{ number_format($order->change_amount, 2) }}</span>
        </div>
    @endif
    <div class="total-line" style="margin-top:4px; font-size:11px;">
        <span>Payment:</span>
        <span style="text-transform:uppercase; font-weight:bold;">
            {{ $order->payment_method }}
        </span>
    </div>

    <div class="divider-dashed"></div>

    {{-- Footer --}}
    <div class="receipt-footer">
        <p>{{ $settings->get('receipt_footer', 'Thank you! Please come again.') }}</p>
        <p style="margin-top:4px; font-size:10px;">
            Powered by BrewTrack
        </p>
    </div>

    {{-- Action buttons - hidden when printing --}}
    <div class="action-buttons">
        <button class="btn-print" onclick="printReceipt()">
            <i class="fas fa-print"></i> Print Receipt
        </button>
        <a href="{{ route('cashier.pos') }}" class="btn-new">
            <i class="fas fa-plus"></i> New Order
        </a>
    </div>
</div>

<script>
    function printReceipt() {
        window.print();
    }
// Automatically open print dialog when receipt page loads
window.addEventListener('load', function() {
    // Small delay to let the page render fully first
    setTimeout(function() {
        window.print();
    }, 500);
});
</script>