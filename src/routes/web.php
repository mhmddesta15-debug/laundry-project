<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Customer;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

// ---------- PUBLIK ----------
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/lacak', [TrackingController::class, 'search'])
    ->middleware('throttle:20,1')->name('tracking.search');

// ---------- REDIRECT SETELAH LOGIN (dipakai Breeze) ----------
Route::get('/dashboard', function () {
    return auth()->user()->role === 'admin'
        ? redirect()->route('admin.orders.index')
        : redirect()->route('customer.dashboard');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ---------- ADMIN / KASIR ----------
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('customers/lookup', [Admin\CustomerController::class, 'lookup'])->name('customers.lookup');
    Route::get('orders/{order}/nota', [Admin\OrderController::class, 'nota'])->name('orders.nota');
    Route::patch('orders/{order}/status', [Admin\OrderController::class, 'updateStatus'])->name('orders.status');
    Route::patch('orders/{order}/pay', [Admin\OrderController::class, 'markPaid'])->name('orders.pay');
    Route::resource('orders', Admin\OrderController::class)->only(['index', 'create', 'store', 'show']);
});

// ---------- PELANGGAN ----------
Route::middleware(['auth', 'role:pelanggan'])->prefix('akun')->name('customer.')->group(function () {
    Route::get('/', [Customer\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/pesanan/{order}', [Customer\DashboardController::class, 'show'])->name('orders.show');
});

require __DIR__.'/auth.php';
