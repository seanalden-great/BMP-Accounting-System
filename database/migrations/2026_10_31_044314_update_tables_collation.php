<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Samakan Collation untuk tabel product_stocks beserta kolom string-nya
        DB::statement('ALTER TABLE `product_stocks` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;');
        DB::statement('ALTER TABLE `product_stocks` MODIFY `product_sku` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL;');

        // 2. Samakan Collation untuk tabel warehouses agar tidak terjadi error serupa di masa depan
        DB::statement('ALTER TABLE `warehouses` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;');
        DB::statement('ALTER TABLE `warehouses` MODIFY `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL;');
        DB::statement('ALTER TABLE `warehouses` MODIFY `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL;');

        // 3. Samakan Collation untuk tabel stock_adjustments
        DB::statement('ALTER TABLE `stock_adjustments` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Catatan: Jika ingin melakukan rollback, Anda bisa mengubah collation-nya 
        // kembali ke default sebelumnya (misal: utf8mb4_unicode_ci), 
        // atau dibiarkan kosong jika tidak diperlukan.
    }
};