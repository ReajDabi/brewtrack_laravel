<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'cashier_id',
        'customer_name',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'payment_method',
        'amount_tendered',
        'change_amount',
        'status',
        'kitchen_queue_printed',
        'notes',
    ];

    protected $casts = [
        'total_amount'          => 'decimal:2',
        'kitchen_queue_printed' => 'boolean',
    ];

    // The cashier who processed this order
    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    // All items inside this order
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Generate order number like ORD-20260425-0001
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD-' . now()->format('Ymd') . '-';
        $last   = static::where('order_number', 'like', $prefix . '%')
                        ->latest('id')->first();
        $next   = $last ? (int) substr($last->order_number, -4) + 1 : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}