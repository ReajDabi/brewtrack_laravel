@extends('layouts.app')

@section('title', 'Menu Items')

@section('content')

{{-- Mobile Responsive Styles specifically for this view --}}
<style>
    .menu-header {
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
    .filter-form {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: flex-end;
    }
    
    /* Mobile Breakpoint */
    @media (max-width: 768px) {
        .menu-header {
            flex-direction: column;
            align-items: stretch;
        }
        .page-header {
            text-align: center;
            margin-bottom: 5px !important;
        }
        .menu-header .btn {
            width: 100%;
            display: flex;
            justify-content: center;
        }
        .stats-container {
            grid-template-columns: 1fr; /* Stack stat cards 1 per row */
        }
        .filter-form {
            flex-direction: column;
            align-items: stretch;
        }
        .filter-form > div {
            width: 100%;
        }
        .filter-form input, .filter-form select {
            width: 100% !important; /* Make inputs stretch to screen edges */
        }
        .filter-form .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

{{-- Header --}}
<div class="menu-header">
    <div class="page-header" style="margin-bottom:0;">
        <h1>Menu Management</h1>
        <p>Manage menu items and recipes</p>
    </div>
    <a href="{{ route('admin.menu.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Item
    </a>
</div>

{{-- Stat cards --}}
<div class="stats-container">
    <div class="stat-card">
        <div class="stat-icon icon-gray">
            <i class="fas fa-utensils"></i>
        </div>
        <div>
            <div class="stat-value">{{ $totalItems }}</div>
            <div class="stat-label">Total Items</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <div class="stat-value">{{ $availableItems }}</div>
            <div class="stat-label">Available</div>
        </div>
    </div>
</div>

{{-- Search and filter --}}
<div class="card" style="margin-bottom:16px;">
    <form method="GET" class="filter-form">
        <div>
            <label style="font-size:12px; font-weight:600; color:#6b7280; display:block; margin-bottom:5px;">Search</label>
            <input type="text" name="search"
                   class="form-control" style="width:200px;"
                   value="{{ request('search') }}"
                   placeholder="Search by name...">
        </div>

        <div>
            <label style="font-size:12px; font-weight:600; color:#6b7280; display:block; margin-bottom:5px;">Category</label>
            <select name="category_id" class="form-control" style="width:180px;">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}"
                        {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Search
        </button>

        @if(request('search') || request('category_id'))
            <a href="{{ route('admin.menu.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Clear
            </a>
        @endif
    </form>
</div>

{{-- Menu items table --}}
<div class="card">
    <div class="card-title">
        <i class="fas fa-list"></i> Menu Items
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Ingredients</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td style="font-weight:600;">{{ $item->name }}</td>
                        <td style="color:#6b7280; font-size:13px;">
                            {{ $item->category->name ?? '—' }}
                        </td>
                        <td style="color:#6b7280; font-size:13px; max-width:200px;">
                            {{ Str::limit($item->description, 40) }}
                        </td>
                        <td style="font-weight:600;">
                            &#8369;{{ number_format($item->price, 2) }}
                        </td>
                        <td style="font-size:13px; color:#6b7280;">
                            {{ $item->ingredients->count() }} ingredients
                        </td>
                        <td>
                            @if($item->is_available)
                                <span class="badge" style="background:#d1fae5; color:#065f46;">
                                    ✓ Available
                                </span>
                            @else
                                <span class="badge" style="background:#fee2e2; color:#991b1b;">
                                    ✗ Unavailable
                                </span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                {{-- Toggle available --}}
                                <form method="POST" action="{{ route('admin.menu.toggle', $item) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="btn btn-sm"
                                            style="background:#fef3c7; color:#92400e;"
                                            title="{{ $item->is_available ? 'Mark Unavailable' : 'Mark Available' }}">
                                        <i class="fas fa-toggle-{{ $item->is_available ? 'on' : 'off' }}"></i>
                                    </button>
                                </form>

                                {{-- Edit --}}
                                <a href="{{ route('admin.menu.edit', $item) }}" class="btn btn-sm btn-edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- Delete --}}
                                <form method="POST" action="{{ route('admin.menu.destroy', $item) }}" onsubmit="return confirm('Remove {{ $item->name }}?')">
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
                        <td colspan="7" style="text-align:center; padding:60px; color:#9ca3af;">
                            <i class="fas fa-utensils" style="font-size:40px; display:block; margin-bottom:12px; opacity:0.3;"></i>
                            No menu items found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">
        {{ $items->links() }}
    </div>
</div>

@endsection