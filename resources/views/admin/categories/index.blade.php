@extends('layouts.app')

@section('title', 'Categories')

@section('content')

{{-- Mobile Responsive Styles specifically for this view --}}
<style>
    .categories-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        flex-wrap: wrap; /* Allows wrapping on small screens */
        gap: 15px;
    }
    
    /* Mobile Breakpoint */
    @media (max-width: 768px) {
        .categories-header {
            flex-direction: column;
            align-items: stretch;
        }
        .page-header {
            text-align: center;
            margin-bottom: 5px !important;
        }
        .categories-header .btn {
            width: 100%;
            display: flex;
            justify-content: center;
        }
    }
</style>

{{-- Header --}}
<div class="categories-header">
    <div class="page-header" style="margin-bottom:0;">
        <h1>Categories</h1>
        <p>Manage menu categories</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Category
    </a>
</div>

{{-- Categories table --}}
<div class="card">
    <div class="card-title">
        <i class="fas fa-tags"></i> Categories
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Menu Items</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td style="text-align:center; font-weight:600; color:#6b7280; width:60px;">
                            {{ $category->display_order }}
                        </td>
                        <td style="font-weight:600;">
                            {{ $category->name }}
                        </td>
                        <td style="color:#6b7280; font-size:13px;">
                            {{ $category->description ?? '—' }}
                        </td>
                        <td style="text-align:center;">
                            <span class="badge" style="background:#e0f2fe; color:#075985;">
                                {{ $category->menu_items_count }} items
                            </span>
                        </td>
                        <td>
                            @if($category->is_active)
                                <span class="badge" style="background:#d1fae5; color:#065f46;">
                                    Active
                                </span>
                            @else
                                <span class="badge" style="background:#fee2e2; color:#991b1b;">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                {{-- Edit --}}
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- Delete --}}
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete {{ $category->name }}?')">
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
                        <td colspan="6" style="text-align:center; padding:60px; color:#9ca3af;">
                            <i class="fas fa-tags" style="font-size:40px; display:block; margin-bottom:12px; opacity:0.3;"></i>
                            No categories found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection