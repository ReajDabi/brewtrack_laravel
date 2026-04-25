<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';

    protected $fillable = [
        'item_name',
        'item_code',
        'description',
        'unit_of_measure',
        'quantity_in_stock',
        'reorder_level',
        'critical_level',
        'unit_cost',
        'supplier_info',
        'is_active',
    ];

    protected $casts = [
        'quantity_in_stock' => 'decimal:2',
        'unit_cost'         => 'decimal:2',
        'is_active'         => 'boolean',
    ];

    public function isCriticalStock(): bool
    {
        return $this->quantity_in_stock <= $this->critical_level;
    }

    public function isLowStock(): bool
    {
        return $this->quantity_in_stock <= $this->reorder_level;
    }

    // Returns 'critical', 'low', or 'ok'
    public function getStockStatusAttribute(): string
    {
        if ($this->isCriticalStock()) return 'critical';
        if ($this->isLowStock())      return 'low';
        return 'ok';
    }
}