<?php

namespace App\Http\Controllers\Cashier;

use App\Services\PrintService;
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'         => 'nullable|string|max:100',
            'payment_method'        => 'required|in:cash,card,gcash,maya',
            'amount_tendered'       => 'nullable|numeric|min:0',
            'discount_amount'       => 'nullable|numeric|min:0',
            'notes'                 => 'nullable|string|max:500',
            'items'                 => 'required|array|min:1',
            'items.*.menu_item_id'  => 'required|exists:menu_items,id',
            'items.*.quantity'      => 'required|integer|min:1',
            'items.*.customization' => 'nullable|string|max:255',
        ]);

        $taxRate = (float) Setting::get('tax_rate', 0.12);

        // ── SAVE ORDER ──────────────────────────────────────
        DB::transaction(function () use ($validated, $taxRate) {

            $subtotal  = 0;
            $lineItems = [];

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

            $discountAmount = $validated['discount_amount'] ?? 0;
            $taxableAmount  = $subtotal - $discountAmount;
            $taxAmount      = round($taxableAmount * $taxRate, 2);
            $totalAmount    = $taxableAmount + $taxAmount;
            $changeAmount   = isset($validated['amount_tendered'])
                ? max(0, $validated['amount_tendered'] - $totalAmount)
                : 0;

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

            foreach ($lineItems as $lineItem) {
                OrderItem::create(array_merge($lineItem, ['order_id' => $order->id]));
            }

            foreach ($validated['items'] as $cartItem) {
                $menuItem = MenuItem::with('ingredients')->find($cartItem['menu_item_id']);

                foreach ($menuItem->ingredients as $ingredient) {
                    $deductQty = $ingredient->pivot->quantity_needed * $cartItem['quantity'];
                    $inv       = Inventory::find($ingredient->id);

                    if ($inv) {
                        $previousStock = $inv->quantity_in_stock;
                        $newStock      = max(0, $previousStock - $deductQty);
                        $inv->update(['quantity_in_stock' => $newStock]);

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

            session(['last_order_id' => $order->id]);
        });
        // ── END SAVE ORDER ───────────────────────────────────

        $orderId     = session('last_order_id');
        $printResult = ['success' => false];

        // ── AUTO PRINT ───────────────────────────────────────
        if (Setting::get('auto_print', '1') === '1') {
            try {
                $printService = new PrintService();
                $printResult  = $printService->printReceipt(Order::find($orderId));

                if (!$printResult['success']) {
                    \Log::warning('Auto-print failed: ' . ($printResult['message'] ?? 'Unknown'));
                }
            } catch (\Exception $e) {
                \Log::error('PrintService error: ' . $e->getMessage());
                $printResult = ['success' => false, 'message' => $e->getMessage()];
            }
        }

        return response()->json([
            'success'     => true,
            'receipt_url' => route('cashier.orders.receipt', $orderId),
            'printed'     => $printResult['success'],
        ]);
    }

    public function receipt(Order $order)
    {
        $order->load('items.menuItem', 'cashier');
        $settings = Setting::all()->pluck('setting_value', 'setting_key');
        return view('cashier.receipt', compact('order', 'settings'));
    }

    public function history(Request $request)
    {
        $query = Order::where('cashier_id', auth()->id())->with('items');

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        } else {
            $query->whereDate('created_at', now()->toDateString());
        }

        $orders = $query->latest()->paginate(20)->withQueryString();
        return view('cashier.history', compact('orders'));
    }
}