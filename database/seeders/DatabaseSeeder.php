<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---- USERS ----
        // Creates default admin and cashier accounts
        DB::table('users')->insert([
            [
                'username'   => 'admin',
                'password'   => Hash::make('admin123'),  // CHANGE THIS IN PRODUCTION!
                'full_name'  => 'System Administrator',
                'email'      => 'admin@brewtrack.com',
                'role'       => 'admin',
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username'   => 'cashier',
                'password'   => Hash::make('cashier123'), // CHANGE THIS IN PRODUCTION!
                'full_name'  => 'Default Cashier',
                'email'      => 'cashier@brewtrack.com',
                'role'       => 'cashier',
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ---- CATEGORIES ----
        $categories = [
            ['Hot Coffee',  'Freshly brewed hot coffee beverages', 1],
            ['Iced Coffee', 'Refreshing cold coffee drinks',       2],
            ['Frappuccino', 'Blended iced beverages',              3],
            ['Tea',         'Hot and iced tea selections',         4],
            ['Pastries',    'Fresh baked goods',                   5],
            ['Snacks',      'Light snacks and treats',             6],
        ];

        foreach ($categories as [$name, $desc, $order]) {
            DB::table('categories')->insert([
                'name' => $name, 'description' => $desc,
                'display_order' => $order, 'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // ---- MENU ITEMS ----
        $items = [
            [1, 'Espresso',               'Rich and bold single shot espresso',   85.00],
            [1, 'Americano',              'Espresso with hot water',               95.00],
            [1, 'Cappuccino',             'Espresso with steamed milk and foam',  120.00],
            [1, 'Cafe Latte',             'Espresso with steamed milk',           130.00],
            [1, 'Caramel Macchiato',      'Espresso with vanilla and caramel',    150.00],
            [2, 'Iced Americano',         'Chilled espresso with cold water',     105.00],
            [2, 'Iced Latte',             'Espresso with cold milk over ice',     140.00],
            [2, 'Iced Caramel Macchiato', 'Iced espresso with caramel',           160.00],
            [3, 'Java Chip Frappuccino',  'Coffee and chocolate chips blended',   180.00],
            [3, 'Caramel Frappuccino',    'Blended caramel coffee drink',         170.00],
            [3, 'Mocha Frappuccino',      'Chocolate and coffee blended',         175.00],
            [4, 'Green Tea',              'Premium Japanese green tea',            90.00],
            [4, 'Chai Tea Latte',         'Spiced tea with steamed milk',         140.00],
            [5, 'Croissant',              'Buttery flaky pastry',                  75.00],
            [5, 'Chocolate Muffin',       'Rich chocolate muffin',                 85.00],
            [6, 'Chocolate Chip Cookie',  'Fresh baked cookie',                    55.00],
        ];

        foreach ($items as [$catId, $name, $desc, $price]) {
            DB::table('menu_items')->insert([
                'category_id' => $catId, 'name' => $name,
                'description' => $desc, 'price' => $price,
                'is_available' => true, 'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // ---- INVENTORY ----
        $inventory = [
            ['INV-001', 'Espresso Beans',        'kg',     25.5, 5,  2,  450.00],
            ['INV-002', 'Coffee Beans (Regular)', 'kg',     18.0, 5,  2,  380.00],
            ['INV-003', 'Fresh Milk',             'liter',  12.5, 10, 5,   85.00],
            ['INV-004', 'Non-Fat Milk',           'liter',   8.0, 8,  4,   95.00],
            ['INV-005', 'Whipped Cream',          'can',    15,   5,  2,  120.00],
            ['INV-006', 'Caramel Syrup',          'bottle',  8,   3,  1,  180.00],
            ['INV-007', 'Vanilla Syrup',          'bottle',  6,   3,  1,  170.00],
            ['INV-008', 'Chocolate Syrup',        'bottle', 10,   3,  1,  160.00],
            ['INV-009', 'Sugar',                  'kg',     20.0, 5,  2,   45.00],
            ['INV-010', 'Green Tea Bags',         'box',    12,   5,  2,  250.00],
            ['INV-011', 'Chai Tea Mix',           'kg',      5.5, 3,  1,  320.00],
            ['INV-012', 'Chocolate Chips',        'kg',      4.0, 2,  1,  280.00],
            ['INV-013', 'Croissants',             'pcs',    24,  10,  5,   35.00],
            ['INV-014', 'Muffins',                'pcs',    18,   8,  4,   40.00],
            ['INV-015', 'Cookies',                'pcs',    30,  12,  6,   25.00],
            ['INV-016', 'Paper Cups (Hot)',       'sleeve', 45,  20, 10,   85.00],
            ['INV-017', 'Paper Cups (Cold)',      'sleeve', 38,  20, 10,   90.00],
            ['INV-018', 'Lids',                   'sleeve', 40,  20, 10,   45.00],
            ['INV-019', 'Straws',                 'box',    25,  10,  5,   65.00],
            ['INV-020', 'Napkins',                'pack',   50,  20, 10,   35.00],
        ];

        foreach ($inventory as [$code, $name, $unit, $qty, $reorder, $critical, $cost]) {
            DB::table('inventory')->insert([
                'item_code' => $code, 'item_name' => $name,
                'unit_of_measure' => $unit, 'quantity_in_stock' => $qty,
                'reorder_level' => $reorder, 'critical_level' => $critical,
                'unit_cost' => $cost, 'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // ---- DEFAULT SETTINGS ----
        $settings = [
            ['shop_name',    'BrewTrack Coffee Shop',    'Shop name'],
            ['shop_address', '123 Main St, Davao City',  'Address'],
            ['shop_contact', '09914893620',              'Contact number'],
            ['tax_rate',     '0.12',                     'VAT rate (12%)'],
            ['currency',     'PHP',                      'Currency'],
        ];

        foreach ($settings as [$key, $value, $desc]) {
            DB::table('settings')->insert([
                'setting_key' => $key, 'setting_value' => $value,
                'description' => $desc, 'updated_at' => now(),
            ]);
        }
    }
}