<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\MenuController;

// Root redirect
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('cashier.pos');
    }
    return redirect()->route('login');
});

// Login / Logout
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Admin routes
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Inventory
        Route::resource('inventory', InventoryController::class);
        Route::post('inventory/{inventory}/adjust', [InventoryController::class, 'adjust'])
            ->name('inventory.adjust');

        // Orders
        Route::get('/orders',                  [OrderController::class, 'index'])
            ->name('orders.index');
        Route::get('/orders/{order}',          [OrderController::class, 'show'])
            ->name('orders.show');
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])
            ->name('orders.status');

        // Menu Items
        Route::resource('menu', MenuController::class);
        Route::patch('menu/{menu}/toggle',     [MenuController::class, 'toggleAvailability'])
            ->name('menu.toggle');

        // Placeholders
        Route::get('/categories', function () { return 'Categories coming soon'; })
            ->name('categories.index');
        Route::get('/users',      function () { return 'Users coming soon'; })
            ->name('users.index');
        Route::get('/reports',    function () { return 'Reports coming soon'; })
            ->name('reports.index');
        Route::get('/settings',   function () { return 'Settings coming soon'; })
            ->name('settings.index');
    });

// Cashier placeholder
Route::prefix('cashier')
    ->name('cashier.')
    ->middleware(['auth', 'role:cashier,admin'])
    ->group(function () {
        Route::get('/pos', function () {
            return 'POS coming soon';
        })->name('pos');

        Route::get('/orders/history', function () {
            return 'History coming soon';
        })->name('orders.history');
    });