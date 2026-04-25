<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    // Important: tell Laravel the table name since it doesn't follow plural convention
    protected $table = 'inventory';

    protected $fillable = [
        'item_name', 'item_code', 'description', 'unit_of_measure',
        'quantity_in_stock', 'reorder_level', 'critical_level',
        'unit_cost', 'supplier_info', 'is_active',
    ];

    protected $casts = [
        'quantity_in_stock' => 'decimal:2',
        'unit_cost'         => 'decimal:2',
        'is_active'         => 'boolean',
    ];

    // Helper: is this item at or below the critical threshold?
    public function isCriticalStock(): bool
    {
        return $this->quantity_in_stock <= $this->critical_level;
    }

    // Helper: is this item at or below the reorder threshold?
    public function isLowStock(): bool
    {
        return $this->quantity_in_stock <= $this->reorder_level;
    }

    // Returns 'critical', 'low', or 'ok' — used to show colored badges in UI
    public function getStockStatusAttribute(): string
    {
        if ($this->isCriticalStock()) return 'critical';
        if ($this->isLowStock())      return 'low';
        return 'ok';
    }
}