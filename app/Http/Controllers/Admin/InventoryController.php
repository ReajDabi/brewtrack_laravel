<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    // Show the inventory list (GET /admin/inventory)
    public function index(Request $request)
    {
        // Start building the query
        $query = Inventory::where('is_active', true);

        // If user typed in the search box, filter by name or code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('item_code', 'like', "%{$search}%");
            });
        }

        // Get total count BEFORE pagination (for the stat cards)
        $totalItems    = Inventory::where('is_active', true)->count();
        $lowStockCount = Inventory::where('is_active', true)
            ->whereColumn('quantity_in_stock', '<=', 'reorder_level')
            ->count();

        // Get paginated results (20 per page)
        $items = $query->orderBy('item_name')->paginate(20)->withQueryString();

        return view('admin.inventory.index', compact('items', 'totalItems', 'lowStockCount'));
    }

    // Show the "Add Item" form (GET /admin/inventory/create)
    public function create()
    {
        return view('admin.inventory.create');
    }

    // Save a new item (POST /admin/inventory)
    public function store(Request $request)
    {
        // Validate all the form fields before saving
        $validated = $request->validate([
            'item_name'         => 'required|string|max:100',
            'item_code'         => 'nullable|string|max:50|unique:inventory',
            'unit_of_measure'   => 'required|string|max:20',
            'quantity_in_stock' => 'required|numeric|min:0',
            'reorder_level'     => 'required|integer|min:0',
            'critical_level'    => 'required|integer|min:0',
            'unit_cost'         => 'nullable|numeric|min:0',
            'supplier_info'     => 'nullable|string',
            'description'       => 'nullable|string',
        ]);

        Inventory::create($validated);

        // Redirect back with a success message
        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory item added successfully!');
    }

    // Show the edit form (GET /admin/inventory/{id}/edit)
    public function edit(Inventory $inventory)
    {
        return view('admin.inventory.edit', compact('inventory'));
    }

    // Save changes to an item (PUT /admin/inventory/{id})
    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'item_name'       => 'required|string|max:100',
            'item_code'       => 'nullable|string|max:50|unique:inventory,item_code,' . $inventory->id,
            'unit_of_measure' => 'required|string|max:20',
            'reorder_level'   => 'required|integer|min:0',
            'critical_level'  => 'required|integer|min:0',
            'unit_cost'       => 'nullable|numeric|min:0',
            'supplier_info'   => 'nullable|string',
            'description'     => 'nullable|string',
        ]);

        $inventory->update($validated);

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Item updated successfully!');
    }

    // Soft-delete (deactivate) an item (DELETE /admin/inventory/{id})
    public function destroy(Inventory $inventory)
    {
        $inventory->update(['is_active' => false]);

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Item removed from inventory.');
    }

    // Adjust stock levels (POST /admin/inventory/{id}/adjust)
    public function adjust(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'transaction_type' => 'required|in:in,out,adjustment,waste',
            'quantity'         => 'required|numeric|min:0.01',
            'notes'            => 'nullable|string',
        ]);

        $previousStock = $inventory->quantity_in_stock;

        // Calculate new stock based on transaction type
        $newStock = match($validated['transaction_type']) {
            'in'         => $previousStock + $validated['quantity'],
            'out','waste'=> max(0, $previousStock - $validated['quantity']),
            'adjustment' => $validated['quantity'],
        };

        // Update the stock level
        $inventory->update(['quantity_in_stock' => $newStock]);

        // Record this transaction in the history log
        InventoryTransaction::create([
            'inventory_id'     => $inventory->id,
            'transaction_type' => $validated['transaction_type'],
            'quantity'         => $validated['quantity'],
            'previous_stock'   => $previousStock,
            'new_stock'        => $newStock,
            'reference_type'   => 'adjustment',
            'notes'            => $validated['notes'],
            'performed_by'     => auth()->id(),
        ]);

        return back()->with('success', 'Stock adjusted successfully!');
    }
}