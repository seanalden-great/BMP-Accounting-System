<?php

// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\AuthController;
// use App\Http\Controllers\SettingController;
// use App\Http\Controllers\ProductController;
// use App\Http\Controllers\CategoryController;
// use App\Http\Controllers\ContactController;
// use App\Http\Controllers\InventoryController;
// use App\Http\Controllers\FaqController;
// use App\Http\Controllers\AccountController;
// use App\Http\Controllers\DashboardController;
// use App\Http\Controllers\PurchaseController;
// use App\Http\Controllers\SaleController;
// use App\Http\Controllers\JournalController;
// use App\Http\Controllers\ReportController;
// use App\Http\Controllers\FixedAssetController;
// use App\Http\Controllers\ExpenseController;
// use App\Http\Controllers\ProfileController;
// use App\Http\Controllers\WarehouseController;

// /*
// |--------------------------------------------------------------------------
// | Rute Terbuka (Public)
// |--------------------------------------------------------------------------
// */

// Route::middleware('guest')->group(function () {
//     Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
//     Route::post('/login', [AuthController::class, 'login']);
//     Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
//     Route::post('/register', [AuthController::class, 'register']);
// });

// /*
// |--------------------------------------------------------------------------
// | Rute Terproteksi (Hanya Bisa Diakses Setelah Login)
// |--------------------------------------------------------------------------
// */
// Route::middleware('auth')->group(function () {
//     Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//     // Halaman Utama / Dashboard
// Route::get('/', function () {
//     return view('dashboard');
// })->name('dashboard');

//     // Pengaturan
//     Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
//     Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

//     // Inventori / Stok
//     Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
//     Route::post('/inventory/adjust', [InventoryController::class, 'adjustStock'])->name('inventory.adjust');

//     // Kas & Bank dan Laporan
//     Route::get('/journals', [JournalController::class, 'index'])->name('journals.index');
//     Route::post('/journals', [JournalController::class, 'store'])->name('journals.store');
//     Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

//     // Transaksi Pembelian Khusus
//     Route::post('purchases/{id}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');
//     Route::post('purchases/{id}/bill', [PurchaseController::class, 'bill'])->name('purchases.bill');

//     // Transaksi Penjualan Khusus
//     Route::post('sales/{id}/deliver', [SaleController::class, 'deliver'])->name('sales.deliver');
//     Route::post('sales/{id}/invoice', [SaleController::class, 'invoice'])->name('sales.invoice');

//     // Aset Tetap Khusus
//     Route::post('fixed-assets/{id}/depreciate', [FixedAssetController::class, 'depreciate'])->name('fixed-assets.depreciate');

//     // Resource Routes Standar (Otomatis generate index, store, update, destroy)
//     Route::resource('products', ProductController::class)->except(['create', 'show']);
//     Route::resource('categories', CategoryController::class)->except(['create', 'show']);
//     Route::resource('contacts', ContactController::class)->except(['create', 'show']);
//     Route::resource('faqs', FaqController::class)->except(['create', 'show']);
//     Route::resource('accounts', AccountController::class)->except(['create', 'show', 'edit']);
//     Route::resource('purchases', PurchaseController::class)->except(['create', 'show', 'edit', 'update', 'destroy']);
//     Route::get('purchases/deliveries', [PurchaseController::class, 'showDeliveries'])->name('purchases.deliveries');
//     Route::get('purchases/bills', [PurchaseController::class, 'showBills'])->name('purchases.bills');
//     Route::resource('sales', SaleController::class)->except(['create', 'show', 'edit', 'update', 'destroy']);
//     Route::get('sales/deliveries', [SaleController::class, 'showDeliveries'])->name('sales.deliveries');
//     Route::get('sales/invoices', [SaleController::class, 'showInvoices'])->name('sales.invoices');
//     Route::resource('fixed-assets', FixedAssetController::class)->except(['create', 'show', 'edit', 'update', 'destroy']);
//     Route::resource('expenses', ExpenseController::class)->except(['create', 'show', 'edit', 'update']);
//     Route::get('/expenses/export-kbd', [ExpenseController::class, 'exportKbd'])->name('expenses.exportKbd');

//     // Rute Manajemen Gudang
//     Route::get('/warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');
//     Route::post('/warehouses', [WarehouseController::class, 'store'])->name('warehouses.store');
//     Route::put('/warehouses/{id}', [WarehouseController::class, 'update'])->name('warehouses.update');
//     Route::delete('/warehouses/{id}', [WarehouseController::class, 'destroy'])->name('warehouses.destroy');

//     // Rute Pengaturan Profil
//     Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
//     Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

//     Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
// });

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\FixedAssetController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Terbuka (Public)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| Rute Terproteksi (Hanya Bisa Diakses Setelah Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Halaman Utama / Dashboard (SEKARANG SUDAH MENGARAH KE CONTROLLER)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']); // Akses ganda opsional

    // Pengaturan
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::get('/settings/roles', [SettingController::class, 'rolePermissions'])->name('settings.roles');
    Route::put('/settings/roles', [SettingController::class, 'updateRolePermissions'])->name('settings.updateRoles');
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

    Route::get('/products/export', [App\Http\Controllers\ProductController::class, 'export'])->name('products.export');
    // Resource Routes Standar
    Route::resource('products', ProductController::class)->except(['create']);
    Route::resource('categories', CategoryController::class)->except(['create', 'show']);
    Route::get('/contacts/export', [ContactController::class, 'export'])->name('contacts.export');
    Route::resource('contacts', ContactController::class)->except(['create', 'show']);
    Route::resource('faqs', FaqController::class)->except(['create', 'show']);
    Route::get('/accounts/export', [AccountController::class, 'export'])->name('accounts.export');
    Route::resource('accounts', AccountController::class)->except(['create', 'show', 'edit']);
    Route::resource('purchases', PurchaseController::class)->except(['create', 'show', 'edit', 'update', 'destroy']);
    Route::get('purchases/deliveries', [PurchaseController::class, 'showDeliveries'])->name('purchases.deliveries');
    Route::get('purchases/bills', [PurchaseController::class, 'showBills'])->name('purchases.bills');
    Route::get('/purchases/overview', [PurchaseController::class, 'overview'])->name('purchases.overview');
    // Transaksi Pembelian Khusus
    Route::post('purchases/{id}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');
    Route::post('purchases/{id}/bill', [PurchaseController::class, 'bill'])->name('purchases.bill');

    // TAMBAHKAN DUA BARIS INI UNTUK SISTEM APPROVAL
    Route::post('purchases/{id}/approve', [PurchaseController::class, 'approve'])->name('purchases.approve');
    Route::post('purchases/{id}/reject', [PurchaseController::class, 'reject'])->name('purchases.reject');
    Route::resource('sales', SaleController::class)->except(['create', 'show', 'edit', 'update', 'destroy']);
    Route::get('sales/deliveries', [SaleController::class, 'showDeliveries'])->name('sales.deliveries');
    Route::get('sales/invoices', [SaleController::class, 'showInvoices'])->name('sales.invoices');
    Route::get('/sales/overview', [SaleController::class, 'overview'])->name('sales.overview');
    Route::get('/sales/efaktur', [SaleController::class, 'exportEfaktur'])->name('sales.efaktur');
    // Transaksi Penjualan Khusus
    Route::post('sales/{id}/deliver', [SaleController::class, 'deliver'])->name('sales.deliver');
    Route::post('sales/{id}/invoice', [SaleController::class, 'invoice'])->name('sales.invoice');

    // TAMBAHKAN DUA BARIS INI UNTUK SISTEM APPROVAL PENJUALAN
    Route::post('sales/{id}/approve', [SaleController::class, 'approve'])->name('sales.approve');
    Route::post('sales/{id}/reject', [SaleController::class, 'reject'])->name('sales.reject');
    Route::resource('fixed-assets', FixedAssetController::class)->except(['create', 'show', 'edit', 'update', 'destroy']);
    // Route::resource('expenses', ExpenseController::class)->except(['create', 'show', 'edit', 'update']);
    Route::resource('expenses', ExpenseController::class)->except(['create', 'show']);
    Route::get('/expenses/export-kbd', [ExpenseController::class, 'exportKbd'])->name('expenses.exportKbd');

    Route::get('/journals/export', [JournalController::class, 'export'])->name('journals.export');

    // Rute Manajemen Gudang
    Route::get('/warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');
    Route::post('/warehouses', [WarehouseController::class, 'store'])->name('warehouses.store');
    Route::put('/warehouses/{id}', [WarehouseController::class, 'update'])->name('warehouses.update');
    Route::delete('/warehouses/{id}', [WarehouseController::class, 'destroy'])->name('warehouses.destroy');

    // Rute Pengaturan Profil
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::post('/payroll/employee', [PayrollController::class, 'storeEmployee'])->name('payroll.storeEmployee');
    // 3 RUTE BARU YANG HARUS DITAMBAHKAN:
    Route::put('/payroll/employee/{id}', [PayrollController::class, 'updateEmployee'])->name('payroll.updateEmployee');
    Route::delete('/payroll/employee/{id}', [PayrollController::class, 'destroyEmployee'])->name('payroll.destroyEmployee');
    Route::post('/payroll/process', [PayrollController::class, 'processPayroll'])->name('payroll.process');
    Route::get('/payroll/slip/{id}/{employee_id}', [PayrollController::class, 'printSlip'])->name('payroll.printSlip');
});
