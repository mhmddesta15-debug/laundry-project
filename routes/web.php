<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Customer;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

// Halaman publik: beranda dan pelacakan pesanan tanpa perlu masuk.
// ---------- PUBLIK ----------
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/lacak', [TrackingController::class, 'search'])
    ->middleware('throttle:20,1')->name('tracking.search');

// Arahkan admin dan pelanggan ke dashboard sesuai perannya setelah login.
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

// Semua operasi admin/kasir dilindungi autentikasi dan pemeriksaan peran.
// ---------- ADMIN / KASIR ----------
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('finance', [Admin\FinanceController::class, 'index'])->name('finance.index');
    Route::post('finance/expenses', [Admin\FinanceController::class, 'storeExpense'])->name('finance.expenses.store');
    Route::delete('finance/expenses/{expense}', [Admin\FinanceController::class, 'destroyExpense'])->name('finance.expenses.destroy');
    Route::get('customers', [Admin\CustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/lookup', [Admin\CustomerController::class, 'lookup'])->name('customers.lookup');
    Route::get('payment-settings', [Admin\PaymentSettingController::class, 'index'])->name('payment-settings.index');
    Route::put('payment-settings', [Admin\PaymentSettingController::class, 'update'])->name('payment-settings.update');
    Route::get('payment-settings/qris', [Admin\PaymentSettingController::class, 'qrisImage'])->name('payment-settings.qris');
    Route::resource('services', Admin\ServiceController::class)->except(['show']);
    Route::get('orders/{order}/nota', [Admin\OrderController::class, 'nota'])->name('orders.nota');
    Route::patch('orders/{order}/status', [Admin\OrderController::class, 'updateStatus'])->name('orders.status');
    Route::patch('orders/{order}/pay', [Admin\OrderController::class, 'markPaid'])->name('orders.pay');
    Route::patch('orders/{order}/reject-payment', [Admin\OrderController::class, 'rejectPayment'])->name('orders.reject-payment');
    Route::get('orders/{order}/payment-proof', [Admin\OrderController::class, 'paymentProof'])->name('orders.payment-proof');
    Route::resource('orders', Admin\OrderController::class)->only(['index', 'create', 'store', 'show']);
});

// Area akun pelanggan; akses pesanan dan pembayaran dibatasi ke pemilik akun.
// ---------- PELANGGAN ----------
Route::middleware(['auth', 'role:pelanggan'])->prefix('akun')->name('customer.')->group(function () {
    Route::get('/', [Customer\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/pesanan/{order}', [Customer\DashboardController::class, 'show'])->name('orders.show');
    Route::post('/pesanan/{order}/bukti-pembayaran', [Customer\DashboardController::class, 'uploadPaymentProof'])->name('orders.payment-proof');
    Route::get('/metode-pembayaran/qris', [Admin\PaymentSettingController::class, 'qrisImage'])->name('payment-qris');
});

require __DIR__.'/auth.php';
