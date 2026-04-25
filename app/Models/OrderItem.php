<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'menu_item_id',
        'quantity',
        'unit_price',
        'total_price',
        'customization',
    ];

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }
}