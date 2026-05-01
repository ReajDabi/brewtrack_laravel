<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\EscposImage;

class PrintService
{
    // Name of your printer exactly as shown in Windows Devices and Printers
    // Change this to match YOUR printer name
    private string $printerName;

    public function __construct()
    {
        // Get printer name from settings or use default
        $this->printerName = Setting::get('printer_name', 'XPrinter');
    }

    /**
     * Print a receipt for the given order
     * Called automatically after order is placed
     */
    public function printReceipt(Order $order): array
    {
        // Load order relationships
        $order->load('items.menuItem', 'cashier');

        try {
            // Connect to the printer
            $connector = $this->getConnector();
            $printer   = new Printer($connector);

            // Build and print the receipt
            $this->buildReceipt($printer, $order);

            // Cut the paper
            $printer->cut();

            // Close the connection
            $printer->close();

            return ['success' => true, 'message' => 'Receipt printed successfully'];

        } catch (\Exception $e) {
            // Log the error but don't crash the app
            \Log::error('Print error: ' . $e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
private function getConnector()
{
    $type        = Setting::get('printer_connection', 'linux_usb');
    $printerName = Setting::get('printer_name',       'Xprinter');
    $ip          = Setting::get('printer_ip',         '192.168.1.100');
    $port        = (int) Setting::get('printer_port', 9100);

    return match($type) {

        // Linux USB direct access (works on Debian automatically)
        'linux_usb' => new \Mike42\Escpos\PrintConnectors\FilePrintConnector(
            '/dev/usb/lp0'
        ),

        // Linux using CUPS print system
        'cups' => new \Mike42\Escpos\PrintConnectors\CupsPrintConnector(
            $printerName
        ),

        // Network printer
        'network' => new \Mike42\Escpos\PrintConnectors\NetworkPrintConnector(
            $ip, $port
        ),

        // Windows USB
        'windows' => new \Mike42\Escpos\PrintConnectors\WindowsPrintConnector(
            $printerName
        ),

        default => new \Mike42\Escpos\PrintConnectors\FilePrintConnector(
            '/dev/usb/lp0'
        ),
    };
}

    /**
     * Build the receipt content using ESC/POS commands
     */
    private function buildReceipt(Printer $printer, Order $order): void
    {
        $shopName    = Setting::get('shop_name', 'BREWTRACK');
        $shopAddress = Setting::get('shop_address', '');
        $shopContact = Setting::get('shop_contact', '');
        $header      = Setting::get('receipt_header', 'Thank you for visiting!');
        $footer      = Setting::get('receipt_footer', 'Please come again!');

        // Paper width for 80mm printer = 48 characters
        $width = 48;

        // =============================================
        // HEADER — Shop name and info
        // =============================================

        // Center align
        $printer->setJustification(Printer::JUSTIFY_CENTER);

        // Shop name — large and bold
        $printer->setTextSize(2, 2);
        $printer->setBold(true);
        $printer->text(strtoupper($shopName) . "\n");

        // Reset size
        $printer->setTextSize(1, 1);
        $printer->setBold(false);

        if ($shopAddress) {
            $printer->text($shopAddress . "\n");
        }
        if ($shopContact) {
            $printer->text("Tel: " . $shopContact . "\n");
        }
        if ($header) {
            $printer->text($header . "\n");
        }

        $printer->feed(1);

        // =============================================
        // ORDER INFO
        // =============================================

        // Divider line
        $printer->text(str_repeat('-', $width) . "\n");

        // Order number — centered and bold
        $printer->setBold(true);
        $printer->setTextSize(1, 2); // tall text
        $printer->text($order->order_number . "\n");
        $printer->setTextSize(1, 1);
        $printer->setBold(false);

        $printer->text(str_repeat('-', $width) . "\n");

        // Left align for details
        $printer->setJustification(Printer::JUSTIFY_LEFT);

        // Date and time
        $printer->text(
            $this->twoColumns('Date:', $order->created_at->format('m/d/Y'), $width) . "\n"
        );
        $printer->text(
            $this->twoColumns('Time:', $order->created_at->format('h:i A'), $width) . "\n"
        );
        $printer->text(
            $this->twoColumns('Cashier:', $order->cashier->full_name ?? '—', $width) . "\n"
        );

        if ($order->customer_name) {
            $printer->text(
                $this->twoColumns('Customer:', $order->customer_name, $width) . "\n"
            );
        }

        // =============================================
        // ORDER ITEMS
        // =============================================

        $printer->text(str_repeat('-', $width) . "\n");

        // Column headers
        $printer->setBold(true);
        $printer->text(
            $this->itemRow('ITEM', 'QTY', 'TOTAL', $width) . "\n"
        );
        $printer->setBold(false);

        $printer->text(str_repeat('-', $width) . "\n");

        // Each item
        foreach ($order->items as $item) {
            $name     = strtoupper($item->menuItem->name ?? 'ITEM');
            $quantity = (string) $item->quantity;
            $total    = '₱' . number_format($item->total_price, 2);

            // Item name row
            $printer->text(
                $this->itemRow($name, $quantity, $total, $width) . "\n"
            );

            // Unit price below item name
            $printer->text(
                '  @ ₱' . number_format($item->unit_price, 2) . " each\n"
            );

            // Customization if any
            if ($item->customization) {
                $printer->text('  * ' . $item->customization . "\n");
            }
        }

        // =============================================
        // TOTALS
        // =============================================

        $printer->text(str_repeat('-', $width) . "\n");

        $printer->text(
            $this->twoColumns('Subtotal:', '₱' . number_format($order->subtotal, 2), $width) . "\n"
        );

        if ($order->discount_amount > 0) {
            $printer->text(
                $this->twoColumns('Discount:', '-₱' . number_format($order->discount_amount, 2), $width) . "\n"
            );
        }

        $printer->text(
            $this->twoColumns('VAT (12%):', '₱' . number_format($order->tax_amount, 2), $width) . "\n"
        );

        // Total — bold and large
        $printer->text(str_repeat('=', $width) . "\n");

        $printer->setBold(true);
        $printer->setTextSize(1, 2);
        $printer->text(
            $this->twoColumns('TOTAL:', '₱' . number_format($order->total_amount, 2), $width) . "\n"
        );
        $printer->setTextSize(1, 1);
        $printer->setBold(false);

        $printer->text(str_repeat('=', $width) . "\n");

        // Cash and change
        if ($order->amount_tendered) {
            $printer->text(
                $this->twoColumns('Cash:', '₱' . number_format($order->amount_tendered, 2), $width) . "\n"
            );
            $printer->setBold(true);
            $printer->text(
                $this->twoColumns('Change:', '₱' . number_format($order->change_amount, 2), $width) . "\n"
            );
            $printer->setBold(false);
        }

        // Payment method
        $printer->text(
            $this->twoColumns('Payment:', strtoupper($order->payment_method), $width) . "\n"
        );

        // =============================================
        // FOOTER
        // =============================================

        $printer->feed(1);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setBold(true);
        $printer->text(strtoupper($footer) . "\n");
        $printer->setBold(false);
        $printer->text("Thank you for your purchase!\n");
        $printer->feed(1);
        $printer->text(
            now()->format('Y') . ' ' . $shopName . "\n"
        );
        $printer->text("Powered by BrewTrack\n");

        // Feed extra lines before cutting
        $printer->feed(3);
    }

    /**
     * Helper: create a two-column line
     * Left text and right text with spaces in between
     * Example: "Total:          ₱150.00"
     */
    private function twoColumns(string $left, string $right, int $width): string
    {
        $spaces = $width - strlen($left) - strlen($right);
        if ($spaces < 1) $spaces = 1;
        return $left . str_repeat(' ', $spaces) . $right;
    }

    /**
     * Helper: create a three-column item row
     * Example: "CAPPUCCINO         2     ₱240.00"
     */
    private function itemRow(string $name, string $qty, string $total, int $width): string
    {
        // Truncate name if too long
        $maxNameLen = $width - strlen($qty) - strlen($total) - 4;
        if (strlen($name) > $maxNameLen) {
            $name = substr($name, 0, $maxNameLen - 2) . '..';
        }

        $spaces1 = $width - strlen($name) - strlen($qty) - strlen($total) - 2;
        if ($spaces1 < 1) $spaces1 = 1;

        return $name
             . str_repeat(' ', $spaces1)
             . $qty
             . '  '
             . $total;
    }
}