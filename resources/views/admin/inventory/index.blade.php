@extends('layouts.app')

@section('title', 'Inventory')

@section('content')

{{-- Mobile Responsive Styles specifically for this view --}}
<style>
    .inventory-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }
    .stats-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        max-width: 500px;
        margin-bottom: 20px;
    }
    .search-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
        flex-wrap: wrap;
        gap: 15px;
    }
    .search-form {
        display: flex;
        gap: 8px;
        width: 100%;
        max-width: 300px;
    }
    .search-form input {
        flex: 1; /* Makes the search bar stretch to fill space */
    }

    /* Mobile Breakpoint */
    @media (max-width: 768px) {
        .inventory-header {
            flex-direction: column;
            align-items: stretch;
        }
        .page-header {
            text-align: center;
            margin-bottom: 5px !important;
        }
        .inventory-header .btn {
            width: 100%;
            display: flex;
            justify-content: center;
        }
        .stats-container {
            grid-template-columns: 1fr; /* Stacks the stat cards 1 per row */
        }
        .search-section {
            flex-direction: column;
            align-items: stretch;
        }
        .search-form {
            max-width: 100%; /* Search bar takes full width on mobile */
        }
    }
</style>

{{-- Page header with Add button --}}
<div class="inventory-header">
    <div class="page-header" style="margin-bottom:0;">
        <h1>Inventory Management</h1>
        <p>Manage stock levels and track inventory</p>
    </div>
    <a href="{{ route('admin.inventory.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Item
    </a>
</div>

{{-- Stat cards --}}
<div class="stats-container">
    <div class="stat-card">
        <div class="stat-icon icon-brown">
            <i class="fas fa-boxes"></i>
        </div>
        <div>
            <div class="stat-value">{{ $totalItems }}</div>
            <div class="stat-label">Total Items</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-yellow">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div>
            <div class="stat-value">{{ $lowStockCount }}</div>
            <div class="stat-label">Low Stock Items</div>
        </div>
    </div>
</div>

{{-- Search + Table --}}
<div class="card">
    <div class="search-section">
        <div class="card-title" style="margin-bottom:0;">
            <i class="fas fa-list"></i> Inventory Items
        </div>

        {{-- Search form --}}
        <form method="GET" class="search-form">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search items..."
                class="form-control"
            >
            <button type="submit" class="btn btn-secondary btn-sm">
                <i class="fas fa-search"></i>
            </button>
            @if(request('search'))
                <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Item Code</th>
                    <th>Item Name</th>
                    <th>Unit</th>
                    <th>In Stock</th>
                    <th>Reorder Level</th>
                    <th>Unit Cost</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    @php
                        $status = $item->stock_status;
                    @endphp
                    <tr>
                        <td style="color:#9ca3af; font-size:12px;">
                            {{ $item->item_code ?? '—' }}
                        </td>
                        <td style="font-weight:500;">{{ $item->item_name }}</td>
                        <td>{{ $item->unit_of_measure }}</td>
                        <td>
                            <span style="font-weight:600; color:{{ $status === 'critical' ? '#ef4444' : ($status === 'low' ? '#f59e0b' : '#10b981') }}">
                                {{ number_format($item->quantity_in_stock, 2) }}
                            </span>
                        </td>
                        <td>{{ $item->reorder_level }}</td>
                        <td>
                            @if($item->unit_cost)
                                &#8369;{{ number_format($item->unit_cost, 2) }}
                            @else
                                <span style="color:#9ca3af;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($status === 'critical')
                                <span class="badge" style="background:#fee2e2; color:#991b1b;">
                                    Critical
                                </span>
                            @elseif($status === 'low')
                                <span class="badge" style="background:#fef3c7; color:#92400e;">
                                    Low Stock
                                </span>
                            @else
                                <span class="badge" style="background:#d1fae5; color:#065f46;">
                                    In Stock
                                </span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                {{-- Adjust stock button --}}
                                <button
                                    class="btn btn-sm"
                                    style="background:#d1fae5; color:#065f46;"
                                    onclick="openAdjust({{ $item->id }}, '{{ addslashes($item->item_name) }}', {{ $item->quantity_in_stock }})">
                                    <i class="fas fa-balance-scale"></i>
                                </button>

                                {{-- Edit button --}}
                                <a href="{{ route('admin.inventory.edit', $item) }}"
                                   class="btn btn-sm btn-edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- Delete button --}}
                                <form method="POST"
                                      action="{{ route('admin.inventory.destroy', $item) }}"
                                      onsubmit="return confirm('Remove this item from inventory?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8"
                            style="text-align:center; padding:40px; color:#9ca3af;">
                            No inventory items found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div style="margin-top:16px;">
        {{ $items->links() }}
    </div>
</div>

{{-- ADJUST STOCK MODAL --}}
<div id="adjustModal"
     style="display:none; position:fixed; inset:0;
            background:rgba(0,0,0,0.5); z-index:999;
            align-items:center; justify-content:center;">
    <div style="background:white; border-radius:14px; padding:28px;
                width:420px; max-width:95vw;
                box-shadow: 0 20px 60px rgba(0,0,0,0.2);">

        <h3 style="margin-bottom:4px; font-size:17px;">
            <i class="fas fa-balance-scale" style="color:#6F4E37;"></i>
            Adjust Stock
        </h3>
        <p id="adjustItemName"
           style="color:#6b7280; font-size:13px; margin-bottom:20px;"></p>

        <form id="adjustForm" method="POST">
            @csrf

            <div class="form-group">
                <label>Transaction Type</label>
                <select name="transaction_type" class="form-control" required>
                    <option value="in">Stock In — Add to inventory</option>
                    <option value="out">Stock Out — Remove from inventory</option>
                    <option value="adjustment">Set Exact Amount</option>
                    <option value="waste">Waste / Spoilage</option>
                </select>
            </div>

            <div class="form-group">
                <label>Quantity</label>
                <input type="number" name="quantity"
                       class="form-control"
                       step="0.01" min="0.01"
                       placeholder="Enter amount" required>
            </div>

            <div class="form-group">
                <label>Notes <span style="color:#9ca3af;">(optional)</span></label>
                <textarea name="notes" class="form-control" rows="2"
                          placeholder="Reason for adjustment..."></textarea>
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px;">
                <button type="button"
                        onclick="closeAdjust()"
                        class="btn btn-secondary">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Open the adjust modal
    function openAdjust(id, name, stock) {
        document.getElementById('adjustItemName').textContent =
            name + ' — Current stock: ' + stock;
        document.getElementById('adjustForm').action =
            '/admin/inventory/' + id + '/adjust';
        document.getElementById('adjustModal').style.display = 'flex';
    }

    // Close the modal
    function closeAdjust() {
        document.getElementById('adjustModal').style.display = 'none';
    }

    // Close when clicking the dark background
    document.getElementById('adjustModal').addEventListener('click', function(e) {
        if (e.target === this) closeAdjust();
    });
</script>
@endpush