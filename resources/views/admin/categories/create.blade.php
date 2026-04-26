@extends('layouts.app')

@section('title', 'Add Category')

@section('content')

<div class="page-header">
    <h1>Add Category</h1>
    <p>Create a new menu category</p>
</div>

<div class="card" style="max-width:500px;">
    <form method="POST" action="{{ route('admin.categories.store') }}">
        @csrf

        <div class="form-group">
            <label>Category Name <span style="color:red;">*</span></label>
            <input type="text" name="name"
                   class="form-control"
                   value="{{ old('name') }}"
                   placeholder="e.g. Hot Coffee" required>
            @error('name')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="2"
                      placeholder="Short description...">{{ old('description') }}</textarea>
        </div>

        <div class="form-group">
            <label>Display Order <span style="color:red;">*</span></label>
            <input type="number" name="display_order"
                   class="form-control"
                   value="{{ old('display_order', 1) }}"
                   min="0" required>
            <small style="color:#9ca3af; font-size:12px;">
                Lower number = appears first in the menu
            </small>
        </div>

        <div style="display:flex; gap:10px; margin-top:8px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Add Category
            </button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>

@endsection