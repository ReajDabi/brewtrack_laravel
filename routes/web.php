<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Cashier\PosController;
use App\Http\Controllers\Cashier\OrderController as CashierOrderController;


// ============================================================
// ROOT: Redirect to login or dashboard depending on login state
// ============================================================
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('cashier.pos');
    }
    return redirect()->route('login');
});

// ============================================================
// AUTH ROUTES (only accessible when NOT logged in)
// ============================================================
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Logout is always available to authenticated users
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ============================================================
// ADMIN ROUTES (only accessible by admin role)
// middleware('auth')      = must be logged in
// middleware('role:admin') = must be an admin
// ============================================================
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Inventory — resource() creates all 7 standard CRUD routes at once
        Route::resource('inventory', InventoryController::class);
        Route::post('inventory/{inventory}/adjust', [InventoryController::class, 'adjust'])
            ->name('inventory.adjust');

        // Menu Items
        Route::resource('menu', MenuController::class);
        Route::patch('menu/{menu}/toggle', [MenuController::class, 'toggleAvailability'])
            ->name('menu.toggle');

        // Categories
        Route::resource('categories', CategoryController::class);

        // Orders
        Route::get('orders',              [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}',      [AdminOrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');

        // Users
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle', [UserController::class, 'toggleActive'])
            ->name('users.toggle');

        // Reports
        Route::get('reports',         [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/sales',   [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('reports/export',  [ReportController::class, 'export'])->name('reports.export');

        // Settings
        Route::get('settings',  [SettingsController::class, 'index'])->name('settings.index');
        Route::put('settings',  [SettingsController::class, 'update'])->name('settings.update');
    });

// ============================================================
// CASHIER ROUTES (accessible by both cashier and admin)
// ============================================================
Route::prefix('cashier')
    ->name('cashier.')
    ->middleware(['auth', 'role:cashier,admin'])
    ->group(function () {

        Route::get('/pos',              [PosController::class, 'index'])->name('pos');
        Route::get('/pos/menu',         [PosController::class, 'getMenuItems'])->name('pos.menu');
        Route::post('/orders',          [CashierOrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}/receipt', [CashierOrderController::class, 'receipt'])
            ->name('orders.receipt');
        Route::get('/orders/history',   [CashierOrderController::class, 'history'])
            ->name('orders.history');
    });