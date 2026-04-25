<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    public $timestamps = false; // Order items don't need created_at/updated_at

    protected $fillable = [
        'order_id', 'menu_item_id',
        'quantity', 'unit_price', 'total_price', 'customization',
    ];

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }
}