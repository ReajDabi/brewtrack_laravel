<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // Save a new order from the POS
    public function store(Request $request)
    {
        // Validate what the POS sends
        $validated = $request->validate([
            'customer_name'   => 'nullable|string|max:100',
            'payment_method'  => 'required|in:cash,card,gcash,maya',
            'amount_tendered' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes'           => 'nullable|string|max:500',
            'items'           => 'required|array|min:1',
            'items.*.menu_item_id'  => 'required|exists:menu_items,id',
            'items.*.quantity'      => 'required|integer|min:1',
            'items.*.customization' => 'nullable|string|max:255',
        ]);

        // Get tax rate from settings (default 12%)
        $taxRate = (float) Setting::get('tax_rate', 0.12);

        // Wrap everything in a transaction
        // If anything fails, ALL changes are undone
        DB::transaction(function () use ($validated, $taxRate, $request) {

            $subtotal = 0;
            $lineItems = [];

            // Step 1: Calculate subtotal from cart items
            foreach ($validated['items'] as $cartItem) {
                $menuItem  = MenuItem::findOrFail($cartItem['menu_item_id']);
                $lineTotal = $menuItem->price * $cartItem['quantity'];
                $subtotal += $lineTotal;

                $lineItems[] = [
                    'menu_item_id'  => $menuItem->id,
                    'quantity'      => $cartItem['quantity'],
                    'unit_price'    => $menuItem->price,
                    'total_price'   => $lineTotal,
                    'customization' => $cartItem['customization'] ?? null,
                ];
            }

            // Step 2: Calculate totals
            $discountAmount = $validated['discount_amount'] ?? 0;
            $taxableAmount  = $subtotal - $discountAmount;
            $taxAmount      = round($taxableAmount * $taxRate, 2);
            $totalAmount    = $taxableAmount + $taxAmount;
            $changeAmount   = isset($validated['amount_tendered'])
                ? max(0, $validated['amount_tendered'] - $totalAmount)
                : 0;

            // Step 3: Create the order
            $order = Order::create([
                'order_number'    => Order::generateOrderNumber(),
                'cashier_id'      => auth()->id(),
                'customer_name'   => $validated['customer_name'],
                'subtotal'        => $subtotal,
                'tax_amount'      => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount'    => $totalAmount,
                'payment_method'  => $validated['payment_method'],
                'amount_tendered' => $validated['amount_tendered'] ?? null,
                'change_amount'   => $changeAmount,
                'notes'           => $validated['notes'] ?? null,
                'status'          => 'pending',
            ]);

            // Step 4: Save each order item
            foreach ($lineItems as $lineItem) {
                OrderItem::create(array_merge(
                    $lineItem,
                    ['order_id' => $order->id]
                ));
            }

            // Step 5: Deduct inventory for each item ordered
            foreach ($validated['items'] as $cartItem) {
                $menuItem = MenuItem::with('ingredients')
                    ->find($cartItem['menu_item_id']);

                foreach ($menuItem->ingredients as $ingredient) {
                    // How much to deduct from stock
                    $deductQty = $ingredient->pivot->quantity_needed
                               * $cartItem['quantity'];

                    $inv = Inventory::find($ingredient->id);

                    if ($inv) {
                        $previousStock = $inv->quantity_in_stock;
                        $newStock      = max(0, $previousStock - $deductQty);

                        // Update stock
                        $inv->update(['quantity_in_stock' => $newStock]);

                        // Record the transaction
                        InventoryTransaction::create([
                            'inventory_id'     => $inv->id,
                            'transaction_type' => 'out',
                            'quantity'         => $deductQty,
                            'previous_stock'   => $previousStock,
                            'new_stock'        => $newStock,
                            'reference_type'   => 'order',
                            'reference_id'     => $order->id,
                            'performed_by'     => auth()->id(),
                        ]);
                    }
                }
            }

            // Save order ID to session for redirect
            session(['last_order_id' => $order->id]);
        });

        // Return JSON response to the POS JavaScript
        return response()->json([
            'success'     => true,
            'receipt_url' => route('cashier.orders.receipt',
                                   session('last_order_id')),
        ]);
    }

    // Show the receipt page
    // Show the receipt page
public function receipt(Order $order)
{
    // Allow any logged-in cashier or admin to view any receipt
    // This is needed so the receipt loads right after placing an order
    $order->load('items.menuItem', 'cashier');

    $settings = Setting::all()->pluck('setting_value', 'setting_key');

    return view('cashier.receipt', compact('order', 'settings'));
}

    // Show order history for cashier
    public function history(Request $request)
    {
        $query = Order::where('cashier_id', auth()->id())
                      ->with('items');

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        } else {
            $query->whereDate('created_at', now()->toDateString());
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        return view('cashier.history', compact('orders'));
    }
}