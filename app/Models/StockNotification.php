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
        'created_at' => 'datetime', // ← add this line
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}