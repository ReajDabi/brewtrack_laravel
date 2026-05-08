<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use App\Services\StockAlertService;

class InventoryController extends Controller
{
    // Show inventory list
    public function index(Request $request)
    {
        // Start the query
        $query = Inventory::where('is_active', true);

        // If user typed in search box, filter results
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                    ->orWhere('item_code', 'like', "%{$search}%");
            });
        }

        // Stat card counts
        $totalItems = Inventory::where('is_active', true)->count();
        $lowStockCount = Inventory::where('is_active', true)
            ->whereColumn('quantity_in_stock', '<=', 'reorder_level')
            ->count();

        // Get paginated results — 20 per page
        $items = $query->orderBy('item_name')->paginate(20)->withQueryString();

        return view('admin.inventory.index', compact('items', 'totalItems', 'lowStockCount'));
    }

    // Show the Add Item form
    public function create()
    {
        return view('admin.inventory.create');
    }

    // Save new item to database
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:100',
            'item_code' => 'nullable|string|max:50|unique:inventory',
            'unit_of_measure' => 'required|string|max:20',
            'quantity_in_stock' => 'required|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
            'critical_level' => 'required|integer|min:0',
            'unit_cost' => 'nullable|numeric|min:0',
            'supplier_info' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        Inventory::create($validated);

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Item added successfully!');
    }

    // Show the Edit form
    public function edit(Inventory $inventory)
    {
        return view('admin.inventory.edit', compact('inventory'));
    }

    // Save edited item
    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:100',
            'item_code' => 'nullable|string|max:50|unique:inventory,item_code,' . $inventory->id,
            'unit_of_measure' => 'required|string|max:20',
            'reorder_level' => 'required|integer|min:0',
            'critical_level' => 'required|integer|min:0',
            'unit_cost' => 'nullable|numeric|min:0',
            'supplier_info' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $inventory->update($validated);

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Item updated successfully!');
    }

    // Soft delete — just marks as inactive
    public function destroy(Inventory $inventory)
    {
        $inventory->update(['is_active' => false]);

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Item removed from inventory.');
    }

    // Adjust stock levels (add/remove/set)
    public function adjust(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'transaction_type' => 'required|in:in,out,adjustment,waste',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        $previousStock = $inventory->quantity_in_stock;

        $newStock = match ($validated['transaction_type']) {
            'in' => $previousStock + $validated['quantity'],
            'out', 'waste' => max(0, $previousStock - $validated['quantity']),
            'adjustment' => $validated['quantity'],
        };

        $inventory->update(['quantity_in_stock' => $newStock]);

        InventoryTransaction::create([
            'inventory_id' => $inventory->id,
            'transaction_type' => $validated['transaction_type'],
            'quantity' => $validated['quantity'],
            'previous_stock' => $previousStock,
            'new_stock' => $newStock,
            'reference_type' => 'adjustment',
            'notes' => $validated['notes'],
            'performed_by' => auth()->id(),
        ]);

        // ← ADD THIS: Check if stock is now low after adjustment
        $inventory->refresh();
        (new StockAlertService())->checkAndAlert($inventory);

        return back()->with('success', 'Stock adjusted successfully!');
    }
}