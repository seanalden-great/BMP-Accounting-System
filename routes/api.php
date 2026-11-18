<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

// Rute Publik (Otentikasi)
Route::post('/login', [AuthController::class, 'login']);

// Rute Terlindungi (Wajib Bawa Token Sanctum di Header Authorization: Bearer <token>)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Modul Inventori
    Route::get('/inventory/master-data', [InventoryController::class, 'getMasterData']);
    Route::get('/inventory/history', [InventoryController::class, 'getHistory']);
    Route::post('/inventory/adjust', [InventoryController::class, 'adjustStock']);

    // Modul Dashboard Eksekutif
    Route::get('/dashboard', [DashboardController::class, 'index']);
    // Modul Pusat Persetujuan Eksekutif
    Route::get('/approvals', [ApprovalController::class, 'index']);

    // Modul Profil
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::put('/profile/update', [ProfileController::class, 'update']);
});
