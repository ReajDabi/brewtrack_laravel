<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'inventory_id',
        'transaction_type',
        'quantity',
        'previous_stock',
        'new_stock',
        'reference_type',
        'reference_id',
        'notes',
        'performed_by',
    ];
}