<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MenuItem extends Model
{
    protected $fillable = [
        'category_id', 'name', 'description',
        'price', 'image_url', 'is_available', 'is_active',
    ];

    protected $casts = [
        'price'        => 'decimal:2',
        'is_available' => 'boolean',
        'is_active'    => 'boolean',
    ];

    // Each menu item belongs to one category
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Many-to-many: a menu item uses many ingredients
    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(
            Inventory::class,
            'menu_item_ingredients', // The pivot/bridge table name
            'menu_item_id',
            'inventory_id'
        )->withPivot('quantity_needed'); // Also get the quantity from the pivot table
    }
}