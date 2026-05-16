<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;

class PrintService
{
    // 58mm paper = 32 characters wide
    private const WIDTH = 32;

    // =========================================================
    // CONNECTOR
    // =========================================================
    private function getConnector()
    {
        $type        = Setting::get('printer_connection', 'linux_usb');
        $printerName = Setting::get('printer_name', 'Xprinter');
        $ip          = Setting::get('printer_ip', '192.168.1.100');
        $port        = (int) Setting::get('printer_port', 9100);

        return match($type) {
            'linux_usb' => new FilePrintConnector('/dev/usb/lp1'),
            'network'   => new NetworkPrintConnector($ip, $port),
            'windows'   => new WindowsPrintConnector($printerName),
            default     => new FilePrintConnector('/dev/usb/lp1'),
        };
    }

    // =========================================================
    // GET CASHIER NAME — fixes "System" issue
    // =========================================================
    private function getCashierName(Order $order): string
    {
        if (!$order->cashier) {
            return 'N/A';
        }

        // Try full_name first, then username, then name
        return $order->cashier->full_name
            ?? $order->cashier->username
            ?? $order->cashier->name
            ?? 'N/A';
    }

    // =========================================================
    // PRINT CUSTOMER COPY
    // =========================================================
    public function printCustomerCopy(Order $order): array
    {
        $order->load('items.menuItem', 'cashier');

        $printer = null;
        try {
            $connector = $this->getConnector();
            $printer   = new Printer($connector);

            $this->buildCustomerCopy($printer, $order);

            $printer->cut();
            $printer->close();

            \Log::info('Customer copy printed: ' . $order->order_number);
            return ['success' => true];

        } catch (\Exception $e) {
            if ($printer) {
                try { $printer->close(); } catch (\Exception $ignored) {}
            }
            \Log::error('Customer copy failed: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // =========================================================
    // PRINT STORE COPY
    // =========================================================
    public function printStoreCopy(Order $order): array
    {
        $order->load('items.menuItem', 'cashier');

        $printer = null;
        try {
            $connector = $this->getConnector();
            $printer   = new Printer($connector);

            $this->buildStoreCopy($printer, $order);

            $printer->cut();
            $printer->close();

            \Log::info('Store copy printed: ' . $order->order_number);
            return ['success' => true];

        } catch (\Exception $e) {
            if ($printer) {
                try { $printer->close(); } catch (\Exception $ignored) {}
            }
            \Log::error('Store copy failed: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // =========================================================
    // CUSTOMER COPY CONTENT
    // Cleaner, simpler — for the customer to keep
    // =========================================================
    private function buildCustomerCopy(Printer $printer, Order $order): void
    {
        $shopName    = Setting::get('shop_name',      'BREWTRACK');
        $shopAddress = Setting::get('shop_address',   '');
        $shopContact = Setting::get('shop_contact',   '');
        $header      = Setting::get('receipt_header', 'Thank you!');
        $footer      = Setting::get('receipt_footer', 'Please come again!');
        $w           = self::WIDTH;
        $cashierName = $this->getCashierName($order);
        $orderLabel  = $order->order_type === 'dine_out' ? 'Take Out' : 'Dine In';

        // ── HEADER ─────────────────────────────────────────
        $printer->setJustification(Printer::JUSTIFY_CENTER);

        $printer->setEmphasis(true);
        $printer->setTextSize(2, 2);
        $printer->text(strtoupper($shopName) . "\n");
        $printer->setTextSize(1, 1);
        $printer->setEmphasis(false);

        if ($shopAddress) $printer->text($shopAddress . "\n");
        if ($shopContact) $printer->text('Tel: ' . $shopContact . "\n");

        $printer->feed(1);

        // Copy label
        $printer->setEmphasis(true);
        $printer->text("** CUSTOMER COPY **\n");
        $printer->setEmphasis(false);

        // Order type badge
        $printer->setTextSize(1, 2);
        $printer->setEmphasis(true);
        $printer->text($orderLabel . "\n");
        $printer->setTextSize(1, 1);
        $printer->setEmphasis(false);

        $printer->text(str_repeat('=', $w) . "\n");

        // Order number
        $printer->setEmphasis(true);
        $printer->setTextSize(1, 2);
        $printer->text($order->order_number . "\n");
        $printer->setTextSize(1, 1);
        $printer->setEmphasis(false);

        $printer->text(str_repeat('-', $w) . "\n");

        // ── ORDER INFO ─────────────────────────────────────
        $printer->setJustification(Printer::JUSTIFY_LEFT);

        $printer->text($this->row('Date',
            $order->created_at->format('m/d/Y'), $w) . "\n");
        $printer->text($this->row('Time',
            $order->created_at->format('h:i A'), $w) . "\n");
        $printer->text($this->row('Cashier',
            $cashierName, $w) . "\n");
        $printer->text($this->row('Type',
            $orderLabel, $w) . "\n");

        if ($order->customer_name) {
            $printer->text($this->row('Customer',
                $order->customer_name, $w) . "\n");
        }

        $printer->text(str_repeat('-', $w) . "\n");

        // ── ITEMS ──────────────────────────────────────────
        $printer->setEmphasis(true);
        $printer->text($this->itemRow('ITEM', 'QTY', 'AMT', $w) . "\n");
        $printer->setEmphasis(false);
        $printer->text(str_repeat('-', $w) . "\n");

        foreach ($order->items as $item) {
            $name  = strtoupper($item->menuItem->name ?? 'ITEM');
            $qty   = (string) $item->quantity;
            $total = 'P' . number_format($item->total_price, 2);

            $printer->text($this->itemRow($name, $qty, $total, $w) . "\n");

            if ($item->customization) {
                $printer->text('  *' . $item->customization . "\n");
            }
        }

        $printer->text(str_repeat('-', $w) . "\n");

        // ── TOTALS ─────────────────────────────────────────
        $printer->text($this->row('Subtotal',
            'P' . number_format($order->subtotal, 2), $w) . "\n");

        if ($order->discount_amount > 0) {
            $printer->text($this->row('Discount',
                '-P' . number_format($order->discount_amount, 2), $w) . "\n");
        }

        $printer->text($this->row('VAT 12%',
            'P' . number_format($order->tax_amount, 2), $w) . "\n");

        $printer->text(str_repeat('=', $w) . "\n");

        $printer->setEmphasis(true);
        $printer->setTextSize(1, 2);
        $printer->text($this->row('TOTAL',
            'P' . number_format($order->total_amount, 2), $w) . "\n");
        $printer->setTextSize(1, 1);
        $printer->setEmphasis(false);

        $printer->text(str_repeat('=', $w) . "\n");

        if ($order->amount_tendered) {
            $printer->text($this->row('Cash',
                'P' . number_format($order->amount_tendered, 2), $w) . "\n");
            $printer->setEmphasis(true);
            $printer->text($this->row('Change',
                'P' . number_format($order->change_amount, 2), $w) . "\n");
            $printer->setEmphasis(false);
        }

        $printer->text($this->row('Payment',
            strtoupper($order->payment_method), $w) . "\n");

        // ── FOOTER ─────────────────────────────────────────
        $printer->feed(1);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->text(strtoupper($footer) . "\n");
        $printer->setEmphasis(false);

        if ($header) $printer->text($header . "\n");

        $printer->feed(1);
        $printer->text(now()->format('Y') . ' ' . $shopName . "\n");
        $printer->feed(3);
    }

    // =========================================================
    // STORE COPY CONTENT
    // More detailed — kept by the store for records
    // =========================================================
    private function buildStoreCopy(Printer $printer, Order $order): void
    {
        $shopName    = Setting::get('shop_name', 'BREWTRACK');
        $w           = self::WIDTH;
        $cashierName = $this->getCashierName($order);
        $orderLabel  = $order->order_type === 'dine_out' ? 'Take Out' : 'Dine In';

        // ── HEADER ─────────────────────────────────────────
        $printer->setJustification(Printer::JUSTIFY_CENTER);

        $printer->setEmphasis(true);
        $printer->setTextSize(2, 1);
        $printer->text(strtoupper($shopName) . "\n");
        $printer->setTextSize(1, 1);
        $printer->setEmphasis(false);

        $printer->feed(1);

        // Copy label
        $printer->setEmphasis(true);
        $printer->text("** STORE COPY **\n");
        $printer->setEmphasis(false);

        // Order type
        $printer->setTextSize(1, 2);
        $printer->setEmphasis(true);
        $printer->text($orderLabel . "\n");
        $printer->setTextSize(1, 1);
        $printer->setEmphasis(false);

        $printer->text(str_repeat('=', $w) . "\n");

        // Order number
        $printer->setEmphasis(true);
        $printer->text($order->order_number . "\n");
        $printer->setEmphasis(false);

        $printer->text(str_repeat('-', $w) . "\n");

        // ── ORDER INFO ─────────────────────────────────────
        $printer->setJustification(Printer::JUSTIFY_LEFT);

        $printer->text($this->row('Date',
            $order->created_at->format('m/d/Y'), $w) . "\n");
        $printer->text($this->row('Time',
            $order->created_at->format('h:i A'), $w) . "\n");
        $printer->setEmphasis(true);
        $printer->text($this->row('Cashier',
            $cashierName, $w) . "\n");
        $printer->setEmphasis(false);
        $printer->text($this->row('Type',
            $orderLabel, $w) . "\n");

        if ($order->customer_name) {
            $printer->text($this->row('Customer',
                $order->customer_name, $w) . "\n");
        }

        $printer->text(str_repeat('-', $w) . "\n");

        // ── ITEMS (with unit price for store reference) ────
        $printer->setEmphasis(true);
        $printer->text($this->itemRow('ITEM', 'QTY', 'AMT', $w) . "\n");
        $printer->setEmphasis(false);
        $printer->text(str_repeat('-', $w) . "\n");

        foreach ($order->items as $item) {
            $name  = strtoupper($item->menuItem->name ?? 'ITEM');
            $qty   = (string) $item->quantity;
            $total = 'P' . number_format($item->total_price, 2);

            $printer->text($this->itemRow($name, $qty, $total, $w) . "\n");

            // Store copy shows unit price per item
            $printer->text('  @P' . number_format($item->unit_price, 2)
                . ' each' . "\n");

            if ($item->customization) {
                $printer->text('  *' . $item->customization . "\n");
            }
        }

        $printer->text(str_repeat('-', $w) . "\n");

        // ── TOTALS (full breakdown for store) ─────────────
        $printer->text($this->row('Subtotal',
            'P' . number_format($order->subtotal, 2), $w) . "\n");

        if ($order->discount_amount > 0) {
            $printer->text($this->row('Discount',
                '-P' . number_format($order->discount_amount, 2), $w) . "\n");
        }

        $printer->text($this->row('VAT 12%',
            'P' . number_format($order->tax_amount, 2), $w) . "\n");

        $printer->text(str_repeat('=', $w) . "\n");

        $printer->setEmphasis(true);
        $printer->setTextSize(1, 2);
        $printer->text($this->row('TOTAL',
            'P' . number_format($order->total_amount, 2), $w) . "\n");
        $printer->setTextSize(1, 1);
        $printer->setEmphasis(false);

        $printer->text(str_repeat('=', $w) . "\n");

        if ($order->amount_tendered) {
            $printer->text($this->row('Cash',
                'P' . number_format($order->amount_tendered, 2), $w) . "\n");
            $printer->setEmphasis(true);
            $printer->text($this->row('Change',
                'P' . number_format($order->change_amount, 2), $w) . "\n");
            $printer->setEmphasis(false);
        }

        $printer->text($this->row('Payment',
            strtoupper($order->payment_method), $w) . "\n");

        // ── STORE FOOTER ───────────────────────────────────
        $printer->feed(1);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text(str_repeat('-', $w) . "\n");
        $printer->text("FOR STORE USE ONLY\n");
        $printer->text(str_repeat('-', $w) . "\n");
        $printer->feed(3);
    }

    // =========================================================
    // HELPERS
    // =========================================================

    private function row(string $left, string $right, int $width): string
    {
        $spaces = $width - strlen($left) - strlen($right);
        if ($spaces < 1) $spaces = 1;
        return $left . str_repeat(' ', $spaces) . $right;
    }

    private function itemRow(
        string $name,
        string $qty,
        string $total,
        int $width
    ): string {
        $maxName = $width - strlen($qty) - strlen($total) - 4;
        if ($maxName < 1) $maxName = 1;
        if (strlen($name) > $maxName) {
            $name = substr($name, 0, $maxName - 1) . '.';
        }
        $spaces = $width - strlen($name) - strlen($qty) - strlen($total) - 2;
        if ($spaces < 1) $spaces = 1;
        return $name . str_repeat(' ', $spaces) . $qty . '  ' . $total;
    }
}