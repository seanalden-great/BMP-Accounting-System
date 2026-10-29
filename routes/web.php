<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FixedAssetController;
use App\Http\Controllers\ExpenseController;

/*
|--------------------------------------------------------------------------
| Rute Terbuka (Public)
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


/*
|--------------------------------------------------------------------------
| Rute Terproteksi (Hanya Bisa Diakses Setelah Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Halaman Utama / Dashboard
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    // Pengaturan
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

    // Inventori / Stok
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/adjust', [InventoryController::class, 'adjustStock'])->name('inventory.adjust');

    // Kas & Bank dan Laporan
    Route::get('/journals', [JournalController::class, 'index'])->name('journals.index');
    Route::post('/journals', [JournalController::class, 'store'])->name('journals.store');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Transaksi Pembelian Khusus
    Route::post('purchases/{id}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');
    Route::post('purchases/{id}/bill', [PurchaseController::class, 'bill'])->name('purchases.bill');

    // Transaksi Penjualan Khusus
    Route::post('sales/{id}/deliver', [SaleController::class, 'deliver'])->name('sales.deliver');
    Route::post('sales/{id}/invoice', [SaleController::class, 'invoice'])->name('sales.invoice');

    // Aset Tetap Khusus
    Route::post('fixed-assets/{id}/depreciate', [FixedAssetController::class, 'depreciate'])->name('fixed-assets.depreciate');

    // Resource Routes Standar (Otomatis generate index, store, update, destroy)
    Route::resource('products', ProductController::class)->except(['create', 'show']);
    Route::resource('categories', CategoryController::class)->except(['create', 'show']);
    Route::resource('contacts', ContactController::class)->except(['create', 'show']);
    Route::resource('faqs', FaqController::class)->except(['create', 'show']);
    Route::resource('accounts', AccountController::class)->except(['create', 'show', 'edit']);
    Route::resource('purchases', PurchaseController::class)->except(['create', 'show', 'edit', 'update', 'destroy']);
    Route::get('purchases/deliveries', [PurchaseController::class, 'showDeliveries'])->name('purchases.deliveries');
    Route::get('purchases/bills', [PurchaseController::class, 'showBills'])->name('purchases.bills');
    Route::resource('sales', SaleController::class)->except(['create', 'show', 'edit', 'update', 'destroy']);
    Route::get('sales/deliveries', [SaleController::class, 'showDeliveries'])->name('sales.deliveries');
    Route::get('sales/invoices', [SaleController::class, 'showInvoices'])->name('sales.invoices');
    Route::resource('fixed-assets', FixedAssetController::class)->except(['create', 'show', 'edit', 'update', 'destroy']);
    Route::resource('expenses', ExpenseController::class)->except(['create', 'show', 'edit', 'update']);


});
