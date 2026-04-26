<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Show all categories
    public function index()
    {
        $categories = Category::withCount('menuItems')
            ->orderBy('display_order')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    // Show Add form
    public function create()
    {
        return view('admin.categories.create');
    }

    // Save new category
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:50|unique:categories',
            'description'   => 'nullable|string',
            'display_order' => 'required|integer|min:0',
        ]);

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', $validated['name'] . ' category added!');
    }

    // Show edit form
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    // Save edited category
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:50|unique:categories,name,' . $category->id,
            'description'   => 'nullable|string',
            'display_order' => 'required|integer|min:0',
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', $category->name . ' updated successfully!');
    }

    // Soft delete
    public function destroy(Category $category)
    {
        // Check if category has active menu items
        $itemCount = $category->menuItems()->where('is_active', true)->count();

        if ($itemCount > 0) {
            return back()->with('error',
                'Cannot delete "' . $category->name . '" — it still has ' . $itemCount . ' active menu items.'
            );
        }

        $category->update(['is_active' => false]);

        return redirect()->route('admin.categories.index')
            ->with('success', $category->name . ' removed.');
    }
}