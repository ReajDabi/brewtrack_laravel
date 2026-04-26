@extends('layouts.app')

@section('title', 'Edit Menu Item')

@section('content')

<div class="page-header">
    <h1>Edit Menu Item</h1>
    <p>Update item details and recipe</p>
</div>

<div class="card" style="max-width:720px;">
    <form method="POST" action="{{ route('admin.menu.update', $menu) }}">
        @csrf
        @method('PUT')

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">

            <div class="form-group">
                <label>Item Name <span style="color:red;">*</span></label>
                <input type="text" name="name"
                       class="form-control"
                       value="{{ old('name', $menu->name) }}" required>
                @error('name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Category <span style="color:red;">*</span></label>
                <select name="category_id" class="form-control" required>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}"
                            {{ old('category_id', $menu->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Price (&#8369;) <span style="color:red;">*</span></label>
                <input type="number" name="price"
                       class="form-control"
                       value="{{ old('price', $menu->price) }}"
                       step="0.01" min="0" required>
            </div>

            <div class="form-group">
                <label>Image URL</label>
                <input type="url" name="image_url"
                       class="form-control"
                       value="{{ old('image_url', $menu->image_url) }}">
            </div>

            <div class="form-group" style="grid-column: span 2;">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="2">{{ old('description', $menu->description) }}</textarea>
            </div>

            <div class="form-group" style="grid-column: span 2;">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="is_available" value="1"
                           {{ old('is_available', $menu->is_available) ? 'checked' : '' }}
                           style="width:16px; height:16px; accent-color:#6F4E37;">
                    <span>Available for sale (show in POS)</span>
                </label>
            </div>

        </div>

        {{-- Ingredients section --}}
        <div style="margin-top:8px; border-top:2px solid #f3f4f6; padding-top:20px;">
            <div style="display:flex; align-items:center;
                        justify-content:space-between; margin-bottom:14px;">
                <div>
                    <h3 style="font-size:15px; font-weight:600;">
                        <i class="fas fa-flask" style="color:#6F4E37;"></i>
                        Recipe / Ingredients
                    </h3>
                </div>
                <button type="button" onclick="addIngredient()"
                        class="btn btn-secondary btn-sm">
                    <i class="fas fa-plus"></i> Add Ingredient
                </button>
            </div>

            <div id="ingredientsList"></div>

            <p id="noIngredients"
               style="color:#9ca3af; font-size:13px; text-align:center;
                      padding:16px; background:#f9fafb; border-radius:8px;
                      display:{{ $menu->ingredients->count() > 0 ? 'none' : 'block' }}">
                No ingredients added.
            </p>
        </div>

        <div style="display:flex; gap:10px; margin-top:20px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Item
            </button>
            <a href="{{ route('admin.menu.index') }}" class="btn btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
    // Build inventory options from PHP
    var inventoryOptions = [];
    @foreach($ingredients as $ing)
        inventoryOptions.push({
            id:   {{ $ing->id }},
            name: "{{ addslashes($ing->item_name) }}",
            unit: "{{ $ing->unit_of_measure }}"
        });
    @endforeach

    // Existing ingredients for this menu item
    var existingIngredients = [];
    @foreach($menu->ingredients as $ing)
        existingIngredients.push({
            id:  {{ $ing->id }},
            qty: "{{ $ing->pivot->quantity_needed }}"
        });
    @endforeach

    var ingredientCount = 0;

    function addIngredient(selectedId, quantity) {
        selectedId = selectedId || '';
        quantity   = quantity   || '';
        ingredientCount++;

        document.getElementById('noIngredients').style.display = 'none';

        var options = '<option value="">Select ingredient...</option>';
        for (var i = 0; i < inventoryOptions.length; i++) {
            var item     = inventoryOptions[i];
            var selected = (item.id == selectedId) ? 'selected' : '';
            options += '<option value="' + item.id + '" ' + selected + '>'
                     + item.name + ' (' + item.unit + ')</option>';
        }

        var row       = document.createElement('div');
        row.id        = 'ingredient_' + ingredientCount;
        row.style.cssText = 'display:flex; gap:10px; align-items:center; margin-bottom:10px;';

        var n = ingredientCount;
        row.innerHTML =
            '<select name="ingredients[' + n + '][inventory_id]" '
                + 'class="form-control" style="flex:2;">'
                + options
            + '</select>'
            + '<input type="number" '
                + 'name="ingredients[' + n + '][quantity_needed]" '
                + 'class="form-control" style="flex:1; width:120px;" '
                + 'value="' + quantity + '" '
                + 'step="0.001" min="0.001" '
                + 'placeholder="Qty needed">'
            + '<button type="button" '
                + 'onclick="removeIngredient(' + n + ')" '
                + 'class="btn btn-sm btn-delete">'
                + '<i class="fas fa-times"></i>'
            + '</button>';

        document.getElementById('ingredientsList').appendChild(row);
    }

    function removeIngredient(id) {
        var row = document.getElementById('ingredient_' + id);
        if (row) row.remove();

        var rows = document.getElementById('ingredientsList').children;
        if (rows.length === 0) {
            document.getElementById('noIngredients').style.display = 'block';
        }
    }

    // Load existing ingredients when the page opens
    for (var i = 0; i < existingIngredients.length; i++) {
        addIngredient(existingIngredients[i].id, existingIngredients[i].qty);
    }
</script>
@endpush