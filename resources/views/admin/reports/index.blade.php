@extends('layouts.app')

@section('title', 'Reports')

@section('content')

{{-- Header --}}
<div style="display:flex; align-items:center;
            justify-content:space-between; margin-bottom:20px;">
    <div class="page-header" style="margin-bottom:0;">
        <h1>Reports</h1>
        <p>Generate and view reports</p>
    </div>

    {{-- Print buttons --}}
    <div style="display:flex; gap:10px;">

        {{-- Print Sales Report --}}
        <form method="POST" action="{{ route('admin.reports.print.sales') }}">
            @csrf
            <input type="hidden" name="date_from" id="print_date_from"
                   value="{{ $dateFrom }}">
            <input type="hidden" name="date_to" id="print_date_to"
                   value="{{ $dateTo }}">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-print"></i> Print Sales
            </button>
        </form>

        {{-- Print Inventory Report --}}
        <form method="POST" action="{{ route('admin.reports.print.inventory') }}">
            @csrf
            <button type="submit" class="btn btn-primary"
                    style="background:#10b981;">
                <i class="fas fa-print"></i> Print Inventory
            </button>
        </form>

      {{-- Export CSV (existing button) --}}
        <a href="{{ route('admin.reports.export', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="btn btn-secondary">
            <i class="fas fa-download"></i> Export CSV
        </a>
    </div>
</div>

{{-- ===================== SALES REPORT ===================== --}}
@if($type === 'sales')

    {{-- Summary stat cards --}}
    <div class="stats-grid" style="margin-bottom:20px;">
        <div class="stat-card">
            <div class="stat-icon icon-gray">
                <i class="fas fa-receipt"></i>
            </div>
            <div>
                <div class="stat-value">{{ $summary->total_orders }}</div>
                <div class="stat-label">Total Orders</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon icon-brown">
                <i class="fas fa-peso-sign"></i>
            </div>
            <div>
                <div class="stat-value">
                    &#8369;{{ number_format($summary->total_sales ?? 0, 2) }}
                </div>
                <div class="stat-label">Total Sales</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon icon-yellow">
                <i class="fas fa-percentage"></i>
            </div>
            <div>
                <div class="stat-value">
                    &#8369;{{ number_format($summary->total_tax ?? 0, 2) }}
                </div>
                <div class="stat-label">Total Tax</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon icon-green">
                <i class="fas fa-tag"></i>
            </div>
            <div>
                <div class="stat-value">
                    &#8369;{{ number_format($summary->total_discount ?? 0, 2) }}
                </div>
                <div class="stat-label">Total Discount</div>
            </div>
        </div>
    </div>

    {{-- Two column layout --}}
    <div class="grid-2" style="margin-bottom:20px;">

        {{-- Top selling items --}}
        <div class="card">
            <div class="card-title">
                <i class="fas fa-trophy"></i> Top Selling Items
            </div>

            @if($topItems->isEmpty())
                <p style="text-align:center; color:#9ca3af;
                           padding:30px 0; font-size:13px;">
                    No sales data for this period
                </p>
            @else
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th>Qty Sold</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topItems as $i => $item)
                                <tr>
                                    <td style="color:#9ca3af; font-size:12px;">
                                        {{ $i + 1 }}
                                    </td>
                                    <td style="font-weight:500;">{{ $item->name }}</td>
                                    <td>{{ $item->total_qty }}</td>
                                    <td style="font-weight:600; color:#6F4E37;">
                                        &#8369;{{ number_format($item->total_revenue, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Sales by payment method --}}
        <div class="card">
            <div class="card-title">
                <i class="fas fa-credit-card"></i> Sales by Payment Method
            </div>

            @if($byPayment->isEmpty())
                <p style="text-align:center; color:#9ca3af;
                           padding:30px 0; font-size:13px;">
                    No payment data for this period
                </p>
            @else
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Method</th>
                                <th>Orders</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($byPayment as $payment)
                                <tr>
                                    <td>
                                        <span class="badge"
                                              style="background:#e0f2fe; color:#075985;
                                                     text-transform:uppercase;">
                                            {{ $payment->payment_method }}
                                        </span>
                                    </td>
                                    <td>{{ $payment->orders }}</td>
                                    <td style="font-weight:600;">
                                        &#8369;{{ number_format($payment->total, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Daily sales breakdown --}}
    <div class="card" style="margin-bottom:20px;">
        <div class="card-title">
            <i class="fas fa-calendar-alt"></i>
            Sales Report
            <span style="font-weight:400; color:#9ca3af; font-size:13px;">
                ({{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }}
                — {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }})
            </span>
        </div>

        @if($salesByDay->isEmpty())
            <div style="text-align:center; padding:60px; color:#9ca3af;">
                <i class="fas fa-chart-bar"
                   style="font-size:40px; display:block;
                          margin-bottom:12px; opacity:0.3;"></i>
                No data available for the selected period.
            </div>
        @else
            {{-- Chart --}}
            <canvas id="salesChart" height="80" style="margin-bottom:20px;"></canvas>

            {{-- Table --}}
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Orders</th>
                            <th>Sales</th>
                            <th>Tax</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesByDay as $day)
                            <tr>
                                <td style="font-weight:500;">
                                    {{ \Carbon\Carbon::parse($day->date)->format('M d, Y (D)') }}
                                </td>
                                <td>{{ $day->orders }}</td>
                                <td style="font-weight:600; color:#6F4E37;">
                                    &#8369;{{ number_format($day->sales, 2) }}
                                </td>
                                <td style="color:#6b7280;">
                                    &#8369;{{ number_format($day->tax, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background:#f9fafb; font-weight:700;">
                            <td>TOTAL</td>
                            <td>{{ $salesByDay->sum('orders') }}</td>
                            <td style="color:#6F4E37;">
                                &#8369;{{ number_format($salesByDay->sum('sales'), 2) }}
                            </td>
                            <td>
                                &#8369;{{ number_format($salesByDay->sum('tax'), 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>

{{-- ===================== INVENTORY REPORT ===================== --}}
@else

    <div class="card">
        <div class="card-title">
            <i class="fas fa-boxes"></i> Inventory Status Report
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Item Name</th>
                        <th>Unit</th>
                        <th>In Stock</th>
                        <th>Reorder Level</th>
                        <th>Critical Level</th>
                        <th>Unit Cost</th>
                        <th>Stock Value</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inventoryItems as $item)
                        @php
                            $status = $item->stock_status;
                            $stockValue = $item->quantity_in_stock * ($item->unit_cost ?? 0);
                        @endphp
                        <tr>
                           <td style="font-weight:500;">{{ $item->item_name }}</td>
                            <td>{{ $item->unit_of_measure }}</td>
                            <td style="font-weight:600; color:{{ $status === 'critical' ? '#ef4444' : ($status === 'low' ? '#f59e0b' : '#10b981') }}">
                                {{ number_format($item->quantity_in_stock, 2) }}
                            </td>
                            <td>{{ $item->reorder_level }}</td>
                            <td>{{ $item->critical_level }}</td>
                            <td>
                                @if($item->unit_cost)
                                    &#8369;{{ number_format($item->unit_cost, 2) }}
                                @else
                                    <span style="color:#9ca3af;">—</span>
                                @endif
                            </td>
                            <td style="font-weight:600;">
                                @if($item->unit_cost)
                                    &#8369;{{ number_format($stockValue, 2) }}
                                @else
                                    <span style="color:#9ca3af;">—</span>
                                @endif
                            </td>
                            <td>
                                @if($status === 'critical')
                                    <span class="badge"
                                          style="background:#fee2e2; color:#991b1b;">
                                        Critical
                                    </span>
                                @elseif($status === 'low')
                                    <span class="badge"
                                          style="background:#fef3c7; color:#92400e;">
                                        Low Stock
                                    </span>
                                @else
                                    <span class="badge"
                                          style="background:#d1fae5; color:#065f46;">
                                        In Stock
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:#f9fafb; font-weight:700;">
                        <td colspan="7" style="text-align:right; padding-right:16px;">
                            Total Stock Value:
                        </td>
                        <td style="color:#6F4E37;">
                            &#8369;{{ number_format($inventoryItems->sum(fn($i) => $i->quantity_in_stock * ($i->unit_cost ?? 0) ), 2) }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

@endif

@endsection

@push('scripts')
@if($type === 'sales' && $salesByDay->isNotEmpty())
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Build chart data from PHP
    var chartLabels = [];
    var chartSales  = [];

    @foreach($salesByDay as $day)
        chartLabels.push("{{ \Carbon\Carbon::parse($day->date)->format('M d') }}");
        chartSales.push({{ $day->sales }});
    @endforeach

    new Chart(document.getElementById('salesChart'), {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Sales (₱)',
                data: chartSales,
                backgroundColor: 'rgba(111, 78, 55, 0.8)',
                borderColor: '#6F4E37',
                borderWidth: 1,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' },
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
      // Keep print form dates in sync with filter form dates
    document.querySelector('[name="date_from"]').addEventListener('change', function() {
        document.getElementById('print_date_from').value = this.value;
    });
    document.querySelector('[name="date_to"]').addEventListener('change', function() {
        document.getElementById('print_date_to').value = this.value;
    });
</script>

@endif
@endpush