<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Cashier\DashboardController;
use App\Http\Controllers\Cashier\OrderController as CashierOrderController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Kitchen\DashboardController as KitchenController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome-customer');
})->name('welcome.customer');

Route::get('/kiosk', [CustomerOrderController::class, 'index'])->name('customer.orders.index');

// Guest customer routes (no authentication required)
Route::get('/kiosk/guest/order', [CustomerOrderController::class, 'create'])->name('customer.orders.create.guest');
Route::post('/kiosk/guest/checkout', [CustomerOrderController::class, 'checkout'])->name('customer.orders.checkout.guest');
Route::post('/kiosk/guest/order', [CustomerOrderController::class, 'storeGuest'])->name('customer.orders.store.guest');
Route::get('/kiosk/guest/order/{order}', [CustomerOrderController::class, 'show'])->name('customer.orders.show.guest');
Route::get('/kiosk/guest/order/{order}/status', [CustomerOrderController::class, 'status'])->name('customer.orders.status.guest');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:cashier'])->group(function () {
    Route::get('/cashier', [DashboardController::class, 'index'])->name('cashier.dashboard');
    Route::resource('orders', CashierOrderController::class)->names('cashier.orders');
});

Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/kiosk/order', [CustomerOrderController::class, 'create'])->name('customer.orders.create');
    Route::post('/kiosk/checkout', [CustomerOrderController::class, 'checkout'])->name('customer.orders.checkout');
    Route::post('/kiosk/order', [CustomerOrderController::class, 'store'])->name('customer.orders.store');
    Route::get('/kiosk/order/{order}', [CustomerOrderController::class, 'show'])->name('customer.orders.show');
    Route::get('/kiosk/order/{order}/status', [CustomerOrderController::class, 'status'])->name('customer.orders.status');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('menus', MenuController::class);
});

require __DIR__.'/auth.php';
