<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Category;

class PosController extends Controller
{
    // Show the POS screen
    public function index()
    {
        // Load all active categories with their available menu items
        $categories = Category::where('is_active', true)
            ->with(['menuItems' => function ($query) {
                $query->where('is_active', true)
                      ->where('is_available', true)
                      ->orderBy('name');
            }])
            ->orderBy('display_order')
            ->get();

        return view('cashier.pos', compact('categories'));
    }
}