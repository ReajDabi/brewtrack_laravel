<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'cashier_id', 'customer_name',
        'subtotal', 'tax_amount', 'discount_amount', 'total_amount',
        'payment_method', 'amount_tendered', 'change_amount',
        'status', 'kitchen_queue_printed', 'notes',
    ];

    protected $casts = [
        'total_amount'          => 'decimal:2',
        'kitchen_queue_printed' => 'boolean',
    ];

    // The cashier who made this order
    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    // All the line items in this order
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Auto-generate order number like "ORD-20260421-0001"
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD-' . now()->format('Ymd') . '-';
        $last   = static::where('order_number', 'like', $prefix . '%')
                        ->latest('id')->first();
        $next   = $last ? (int) substr($last->order_number, -4) + 1 : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}