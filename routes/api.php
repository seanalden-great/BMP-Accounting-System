<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\ApprovalController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfileController;

// Rute Publik (Otentikasi)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Rute Terlindungi (Wajib Bawa Token Sanctum di Header Authorization: Bearer <token>)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Modul Inventori
    Route::get('/inventory/master-data', [InventoryController::class, 'getMasterData']);
    Route::get('/inventory/history', [InventoryController::class, 'getHistory']);
    Route::post('/inventory/adjust', [InventoryController::class, 'adjustStock']);
    Route::post('/inventory/transfer', [InventoryController::class, 'transferStock']); // <--- Tambahkan Ini

    // Modul Dashboard Eksekutif
    Route::get('/dashboard', [DashboardController::class, 'index']);
    // Modul Pusat Persetujuan Eksekutif
    Route::get('/approvals', [ApprovalController::class, 'index']);

    // Modul Profil
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::put('/profile/update', [ProfileController::class, 'update']);

    // Modul Master Produk & Kartu Stok
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
});
