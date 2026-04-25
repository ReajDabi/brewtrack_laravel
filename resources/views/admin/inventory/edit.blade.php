@extends('layouts.app')

@section('title', 'Edit Inventory Item')

@section('content')

<div class="page-header">
    <h1>Edit Inventory Item</h1>
    <p>Update item details</p>
</div>

{{-- Current stock info box --}}
<div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px;
            padding:12px 16px; margin-bottom:20px; max-width:680px;
            font-size:13px; color:#065f46;">
    <i class="fas fa-info-circle"></i>
    Current stock: <strong>{{ $inventory->quantity_in_stock }} {{ $inventory->unit_of_measure }}</strong>
    — To change the stock level, use the
    <strong>Adjust</strong> button on the inventory list.
</div>

<div class="card" style="max-width:680px;">
    <form method="POST" action="{{ route('admin.inventory.update', $inventory) }}">
        @csrf
        @method('PUT')

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">

            <div class="form-group">
                <label>Item Name <span style="color:red;">*</span></label>
                <input type="text" name="item_name"
                       class="form-control"
                       value="{{ old('item_name', $inventory->item_name) }}" required>
                @error('item_name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Item Code</label>
                <input type="text" name="item_code"
                       class="form-control"
                       value="{{ old('item_code', $inventory->item_code) }}">
            </div>

            <div class="form-group">
                <label>Unit of Measure <span style="color:red;">*</span></label>
                <select name="unit_of_measure" class="form-control" required>
                    @foreach(['pcs','kg','g','liter','ml','bottle','can','box','sleeve','pack','bag'] as $unit)
                        <option value="{{ $unit }}"
                            {{ old('unit_of_measure', $inventory->unit_of_measure) === $unit ? 'selected' : '' }}>
                            {{ $unit }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Unit Cost (&#8369;)</label>
                <input type="number" name="unit_cost"
                       class="form-control"
                       value="{{ old('unit_cost', $inventory->unit_cost) }}"
                       step="0.01" min="0">
            </div>

            <div class="form-group">
                <label>Reorder Level <span style="color:red;">*</span></label>
                <input type="number" name="reorder_level"
                       class="form-control"
                       value="{{ old('reorder_level', $inventory->reorder_level) }}"
                       min="0" required>
            </div>

            <div class="form-group">
                <label>Critical Level <span style="color:red;">*</span></label>
                <input type="number" name="critical_level"
                       class="form-control"
                       value="{{ old('critical_level', $inventory->critical_level) }}"
                       min="0" required>
            </div>

            <div class="form-group" style="grid-column: span 2;">
                <label>Supplier Info</label>
                <textarea name="supplier_info" class="form-control" rows="2">{{ old('supplier_info', $inventory->supplier_info) }}</textarea>
            </div>

            <div class="form-group" style="grid-column: span 2;">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="2">{{ old('description', $inventory->description) }}</textarea>
            </div>

        </div>

        <div style="display:flex; gap:10px; margin-top:8px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Item
            </button>
            <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>

@endsection