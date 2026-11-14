<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Siapa pelakunya
            $table->string('action'); // MEMBUAT, MENGUBAH, MENGHAPUS, MENYETUJUI
            $table->string('module'); // Pesanan Penjualan, Master Produk, dsb
            $table->text('description'); // Penjelasan spesifik
            $table->string('ip_address')->nullable(); // Lacak dari jaringan mana
            $table->timestamps(); // Kapan kejadiannya
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
