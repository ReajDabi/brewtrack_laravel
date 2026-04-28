<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Cashier\PosController;
use App\Http\Controllers\Cashier\OrderController as CashierOrderController;

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

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('inventory', InventoryController::class);
        Route::post('inventory/{inventory}/adjust', [InventoryController::class, 'adjust'])
            ->name('inventory.adjust');

        Route::get('/orders',                  [OrderController::class, 'index'])
            ->name('orders.index');
        Route::get('/orders/{order}',          [OrderController::class, 'show'])
            ->name('orders.show');
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])
            ->name('orders.status');

        Route::resource('menu', MenuController::class);
        Route::patch('menu/{menu}/toggle', [MenuController::class, 'toggleAvailability'])
            ->name('menu.toggle');

        Route::resource('categories', CategoryController::class);

        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle', [UserController::class, 'toggleActive'])
            ->name('users.toggle');

        Route::get('/reports',        [ReportController::class, 'index'])
            ->name('reports.index');
        Route::get('/reports/export', [ReportController::class, 'export'])
            ->name('reports.export');

        Route::get('/settings', [SettingsController::class, 'index'])
            ->name('settings.index');
        Route::put('/settings', [SettingsController::class, 'update'])
            ->name('settings.update');
    });
// Cashier routes
Route::prefix('cashier')
    ->name('cashier.')
    ->middleware(['auth', 'role:cashier,admin'])
    ->group(function () {

        // POS screen
        Route::get('/pos', [PosController::class, 'index'])
            ->name('pos');

        // History MUST be before {order}/receipt
        Route::get('/orders/history', [CashierOrderController::class, 'history'])
            ->name('orders.history');

        // Receipt comes after history
        Route::get('/orders/{order}/receipt', [CashierOrderController::class, 'receipt'])
            ->name('orders.receipt');

        // Place order
        Route::post('/orders', [CashierOrderController::class, 'store'])
            ->name('orders.store');
    });