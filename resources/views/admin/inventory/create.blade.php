@extends('layouts.app')

@section('title', 'Add Inventory Item')

@section('content')

<div class="page-header">
    <h1>Add Inventory Item</h1>
    <p>Add a new item to your inventory</p>
</div>

<div class="card" style="max-width:680px;">
    <form method="POST" action="{{ route('admin.inventory.store') }}">
        @csrf

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">

            <div class="form-group">
                <label>Item Name <span style="color:red;">*</span></label>
                <input type="text" name="item_name"
                       class="form-control"
                       value="{{ old('item_name') }}"
                       placeholder="e.g. Espresso Beans" required>
                @error('item_name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Item Code</label>
                <input type="text" name="item_code"
                       class="form-control"
                       value="{{ old('item_code') }}"
                       placeholder="e.g. INV-001">
                @error('item_code')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Unit of Measure <span style="color:red;">*</span></label>
                <select name="unit_of_measure" class="form-control" required>
                    @foreach(['pcs','kg','g','liter','ml','bottle','can','box','sleeve','pack','bag'] as $unit)
                        <option value="{{ $unit }}" {{ old('unit_of_measure') === $unit ? 'selected' : '' }}>
                            {{ $unit }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Opening Stock <span style="color:red;">*</span></label>
                <input type="number" name="quantity_in_stock"
                       class="form-control"
                       value="{{ old('quantity_in_stock', 0) }}"
                       step="0.01" min="0" required>
                @error('quantity_in_stock')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Reorder Level <span style="color:red;">*</span></label>
                <input type="number" name="reorder_level"
                       class="form-control"
                       value="{{ old('reorder_level', 10) }}"
                       min="0" required>
            </div>

            <div class="form-group">
                <label>Critical Level <span style="color:red;">*</span></label>
                <input type="number" name="critical_level"
                       class="form-control"
                       value="{{ old('critical_level', 5) }}"
                       min="0" required>
            </div>

            <div class="form-group">
                <label>Unit Cost (&#8369;)</label>
                <input type="number" name="unit_cost"
                       class="form-control"
                       value="{{ old('unit_cost') }}"
                       step="0.01" min="0" placeholder="0.00">
            </div>

            <div class="form-group" style="grid-column: span 2;">
                <label>Supplier Info</label>
                <textarea name="supplier_info" class="form-control" rows="2"
                          placeholder="Supplier name, contact number...">{{ old('supplier_info') }}</textarea>
            </div>

            <div class="form-group" style="grid-column: span 2;">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="2"
                          placeholder="Optional description...">{{ old('description') }}</textarea>
            </div>

        </div>

        <div style="display:flex; gap:10px; margin-top:8px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Add Item
            </button>
            <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>

@endsection