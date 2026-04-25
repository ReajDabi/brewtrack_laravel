@extends('layouts.app')

@section('title', 'Inventory')

@section('content')

<div class="page-header" style="display:flex; align-items:flex-end; justify-content:space-between;">
    <div>
        <h1>Inventory Management</h1>
        <p>Manage stock levels and track inventory</p>
    </div>
    {{-- Plus button (top right) --}}
    <a href="{{ route('admin.inventory.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Item
    </a>
</div>

{{-- STAT CARDS --}}
<div class="stats-grid" style="grid-template-columns: 1fr 1fr; max-width: 600px;">
    <div class="stat-card">
        <div class="stat-icon brown"><i class="fas fa-boxes"></i></div>
        <div>
            <div class="stat-value">{{ $totalItems }}</div>
            <div class="stat-label">Total Items</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow"><i class="fas fa-exclamation-triangle"></i></div>
        <div>
            <div class="stat-value">{{ $lowStockCount }}</div>
            <div class="stat-label">Low Stock Items</div>
        </div>
    </div>
</div>

{{-- SEARCH + TABLE --}}
<div class="card">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
        <div class="card-title" style="margin-bottom:0;">
            <i class="fas fa-list"></i> Inventory Items
        </div>
        {{-- Search form --}}
        <form method="GET" style="display:flex; gap:8px;">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search items..."
                   class="form-control" style="width:220px; padding:8px 12px;">
            <button type="submit" class="btn btn-secondary btn-sm">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

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
                <tr>
                    <td style="color:#9ca3af; font-size:12px;">{{ $item->item_code }}</td>
                    <td style="font-weight:500;">{{ $item->item_name }}</td>
                    <td>{{ $item->unit_of_measure }}</td>
                    <td>{{ number_format($item->quantity_in_stock, 2) }}</td>
                    <td>{{ $item->reorder_level }}</td>
                    <td>
                        @if($item->unit_cost)
                            &#8369;{{ number_format($item->unit_cost, 2) }}
                        @else
                            <span style="color:#9ca3af;">—</span>
                        @endif
                    </td>
                    <td>
                        {{-- stock_status is the computed attribute from the model --}}
                        @php $status = $item->stock_status; @endphp
                        <span class="badge badge-{{ $status }}">
                            @if($status === 'ok') In Stock
                            @elseif($status === 'low') Low Stock
                            @else Critical
                            @endif
                        </span>
                    </td>
                    <td style="display:flex; gap:6px;">
                        {{-- Adjust button --}}
                        <button class="btn btn-sm"
                                style="background:#d1fae5; color:#065f46;"
                                onclick="openAdjust({{ $item->id }}, '{{ $item->item_name }}')">
                            <i class="fas fa-balance-scale"></i>
                        </button>

                        {{-- Edit button --}}
                        <a href="{{ route('admin.inventory.edit', $item) }}"
                           class="btn btn-sm btn-edit">
                            <i class="fas fa-edit"></i>
                        </a>

                        {{-- Delete button with confirmation --}}
                        <form method="POST"
                              action="{{ route('admin.inventory.destroy', $item) }}"
                              onsubmit="return confirm('Remove this item?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:40px; color:#9ca3af;">
                        No inventory items found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination links --}}
    <div style="margin-top:16px;">
        {{ $items->links() }}
    </div>
</div>

{{-- ADJUST STOCK MODAL --}}
<div id="adjustModal" style="display:none; position:fixed; inset:0;
     background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:14px; padding:28px; width:400px; max-width:95vw;">
        <h3 style="margin-bottom:6px;"><i class="fas fa-boxes" style="color:#6F4E37;"></i> Adjust Stock</h3>
        <p id="adjustItemName" style="color:#6b7280; font-size:13px; margin-bottom:20px;"></p>

        <form id="adjustForm" method="POST">
            @csrf
            <div class="form-group">
                <label>Transaction Type</label>
                <select name="transaction_type" class="form-control" required>
                    <option value="in">Stock In (Add inventory)</option>
                    <option value="out">Stock Out (Remove inventory)</option>
                    <option value="adjustment">Set Exact Amount</option>
                    <option value="waste">Waste / Spoilage</option>
                </select>
            </div>
            <div class="form-group">
                <label>Quantity</label>
                <input type="number" name="quantity" class="form-control"
                       step="0.01" min="0.01" required>
            </div>
            <div class="form-group">
                <label>Notes (optional)</label>
                <textarea name="notes" class="form-control" rows="2"
                          placeholder="Reason for adjustment..."></textarea>
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px;">
                <button type="button" onclick="closeAdjust()" class="btn btn-secondary">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    Save Adjustment
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Open the adjust modal and set the form action URL
    function openAdjust(id, name) {
        document.getElementById('adjustItemName').textContent = 'Item: ' + name;
        document.getElementById('adjustForm').action = '/admin/inventory/' + id + '/adjust';
        document.getElementById('adjustModal').style.display = 'flex';
    }

    // Close the modal
    function closeAdjust() {
        document.getElementById('adjustModal').style.display = 'none';
    }

    // Close modal if user clicks the dark overlay
    document.getElementById('adjustModal').addEventListener('click', function(e) {
        if (e.target === this) closeAdjust();
    });
</script>
@endpush