<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockNotification extends Model
{
    public $timestamps = false;

    protected $table = 'notifications';

    protected $fillable = [
        'inventory_id',
        'notification_type',
        'message',
        'is_read',
        'email_sent',
    ];

    protected $casts = [
        'is_read'    => 'boolean',
        'email_sent' => 'boolean',
    ];

    // A notification belongs to one inventory item
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}