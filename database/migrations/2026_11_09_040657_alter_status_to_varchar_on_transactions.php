<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Ubah kolom status di tabel Pembelian menjadi teks fleksibel
        DB::statement("ALTER TABLE purchases MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'Menunggu Persetujuan'");

        // Ubah kolom status di tabel Penjualan menjadi teks fleksibel
        DB::statement("ALTER TABLE sales MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'Menunggu Persetujuan'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('varchar_on_transactions', function (Blueprint $table) {
            //
        });
    }
};
