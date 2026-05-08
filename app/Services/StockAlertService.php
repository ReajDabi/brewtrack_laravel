<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\StockNotification;

class StockAlertService
{
    /**
     * Check a single inventory item and create
     * a notification if stock is low or critical.
     * Called after every stock deduction or adjustment.
     */
    public function checkAndAlert(Inventory $item): void
    {
        // Determine alert type
        if ($item->quantity_in_stock <= $item->critical_level) {
            $type = 'critical_stock';
        } elseif ($item->quantity_in_stock <= $item->reorder_level) {
            $type = 'low_stock';
        } else {
            // Stock is fine — no alert needed
            return;
        }

        // Check if an unread notification of this type already exists
        // Avoid spamming duplicate alerts
        $exists = StockNotification::where('inventory_id', $item->id)
            ->where('notification_type', $type)
            ->where('is_read', false)
            ->exists();

        if ($exists) {
            return; // Already alerted, don't create duplicate
        }

        // Build the message
        if ($type === 'critical_stock') {
            $message = "CRITICAL: {$item->item_name} is critically low! "
                     . "Only {$item->quantity_in_stock} {$item->unit_of_measure} left. "
                     . "Reorder immediately!";
        } else {
            $message = "LOW STOCK: {$item->item_name} has reached reorder level. "
                     . "Current stock: {$item->quantity_in_stock} {$item->unit_of_measure}. "
                     . "Consider restocking soon.";
        }

        // Create the notification
        StockNotification::create([
            'inventory_id'      => $item->id,
            'notification_type' => $type,
            'message'           => $message,
        ]);
    }

    /**
     * Check ALL inventory items at once.
     * Useful for a scheduled/cron check.
     */
    public function checkAll(): int
    {
        $items   = Inventory::where('is_active', true)->get();
        $created = 0;

        foreach ($items as $item) {
            $before = StockNotification::where('is_read', false)->count();
            $this->checkAndAlert($item);
            $after  = StockNotification::where('is_read', false)->count();

            if ($after > $before) $created++;
        }

        return $created;
    }
}