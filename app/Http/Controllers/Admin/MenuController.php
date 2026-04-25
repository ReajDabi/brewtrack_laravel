<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Category;
use App\Models\Inventory;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    // Show menu items list
    public function index(Request $request)
    {
        $query = MenuItem::with('category')->where('is_active', true);

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Stat cards
        $totalItems     = MenuItem::where('is_active', true)->count();
        $availableItems = MenuItem::where('is_active', true)
            ->where('is_available', true)->count();

        // Categories for filter dropdown
        $categories = Category::where('is_active', true)
            ->orderBy('display_order')->get();

        $items = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.menu.index', compact(
            'items', 'totalItems', 'availableItems', 'categories'
        ));
    }

    // Show Add form
    public function create()
    {
        $categories  = Category::where('is_active', true)
            ->orderBy('display_order')->get();
        $ingredients = Inventory::where('is_active', true)
            ->orderBy('item_name')->get();

        return view('admin.menu.create', compact('categories', 'ingredients'));
    }

    // Save new menu item
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'  => 'required|integer',
            'name'         => 'required|string|max:100',
            'description'  => 'nullable|string',
            'price'        => 'required|numeric|min:0',
            'image_url'    => 'nullable|url|max:255',
            'is_available' => 'boolean',
        ]);

        // Handle checkbox — if unchecked it won't be in the request
        $validated['is_available'] = $request->has('is_available');

        $item = MenuItem::create($validated);

        // Save ingredients/recipe if provided
        if ($request->filled('ingredients')) {
            $syncData = [];
            foreach ($request->ingredients as $ingredient) {
                if (!empty($ingredient['inventory_id']) && !empty($ingredient['quantity_needed'])) {
                    $syncData[$ingredient['inventory_id']] = [
                        'quantity_needed' => $ingredient['quantity_needed'],
                    ];
                }
            }
            $item->ingredients()->sync($syncData);
        }

        return redirect()->route('admin.menu.index')
            ->with('success', $validated['name'] . ' added successfully!');
    }

    // Show edit form
    public function edit(MenuItem $menu)
    {
        $categories  = Category::where('is_active', true)
            ->orderBy('display_order')->get();
        $ingredients = Inventory::where('is_active', true)
            ->orderBy('item_name')->get();

        // Load current ingredients for this item
        $menu->load('ingredients');

        return view('admin.menu.edit', compact('menu', 'categories', 'ingredients'));
    }

    // Save edited item
    public function update(Request $request, MenuItem $menu)
    {
        $validated = $request->validate([
            'category_id'  => 'required|integer',
            'name'         => 'required|string|max:100',
            'description'  => 'nullable|string',
            'price'        => 'required|numeric|min:0',
            'image_url'    => 'nullable|url|max:255',
            'is_available' => 'boolean',
        ]);

        $validated['is_available'] = $request->has('is_available');

        $menu->update($validated);

        // Update ingredients
        if ($request->filled('ingredients')) {
            $syncData = [];
            foreach ($request->ingredients as $ingredient) {
                if (!empty($ingredient['inventory_id']) && !empty($ingredient['quantity_needed'])) {
                    $syncData[$ingredient['inventory_id']] = [
                        'quantity_needed' => $ingredient['quantity_needed'],
                    ];
                }
            }
            $menu->ingredients()->sync($syncData);
        } else {
            // If no ingredients submitted, remove all
            $menu->ingredients()->detach();
        }

        return redirect()->route('admin.menu.index')
            ->with('success', $menu->name . ' updated successfully!');
    }

    // Soft delete
    public function destroy(MenuItem $menu)
    {
        $menu->update(['is_active' => false]);

        return redirect()->route('admin.menu.index')
            ->with('success', 'Menu item removed.');
    }

    // Toggle available/unavailable
    public function toggleAvailability(MenuItem $menu)
    {
        $menu->update(['is_available' => !$menu->is_available]);

        $status = $menu->is_available ? 'available' : 'unavailable';

        return back()->with('success', $menu->name . ' marked as ' . $status . '.');
    }
}