@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')

<div class="page-header">
    <h1>Edit Category</h1>
    <p>Update category details</p>
</div>

<div class="card" style="max-width:500px;">
    <form method="POST" action="{{ route('admin.categories.update', $category) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Category Name <span style="color:red;">*</span></label>
            <input type="text" name="name"
                   class="form-control"
                   value="{{ old('name', $category->name) }}" required>
            @error('name')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description"
                      class="form-control" rows="2">{{ old('description', $category->description) }}</textarea>
        </div>

        <div class="form-group">
            <label>Display Order <span style="color:red;">*</span></label>
            <input type="number" name="display_order"
                   class="form-control"
                   value="{{ old('display_order', $category->display_order) }}"
                   min="0" required>
            <small style="color:#9ca3af; font-size:12px;">
                Lower number = appears first in the menu
            </small>
        </div>

        {{-- Show item count info --}}
        <div style="background:#f9fafb; border-radius:8px;
                    padding:12px 14px; margin-bottom:16px;
                    font-size:13px; color:#6b7280;">
            <i class="fas fa-info-circle"></i>
            This category has
            <strong style="color:#1a1a2e;">
                {{ $category->menuItems()->where('is_active', true)->count() }}
            </strong>
            active menu items.
        </div>

        <div style="display:flex; gap:10px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Category
            </button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>

@endsection