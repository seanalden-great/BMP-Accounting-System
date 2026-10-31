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
        // 1. Buat Tabel Gudang (Warehouses)
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 100);
            $table->text('location')->nullable();
            
            // Note: Uncomment baris di bawah jika ingin menambahkan created_at & updated_at
            // $table->timestamps(); 
        });

        // Masukkan 1 Gudang Utama sebagai default untuk data yang sudah ada
        // (Kita set 'id' => 1 secara eksplisit agar sinkron dengan script insert di bawahnya)
        DB::table('warehouses')->insert([
            'id'       => 1,
            'code'     => 'WH-001',
            'name'     => 'Gudang Utama',
            'location' => 'Pusat',
        ]);

        // 2. Buat Tabel Stok Per Gudang (Product Stocks)
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_id');
            $table->string('product_sku', 50);
            $table->integer('quantity')->default(0);

            // Membuat unique index
            $table->unique(['warehouse_id', 'product_sku'], 'unique_stock');

            // Opsional (Sangat Disarankan): Menambahkan Foreign Key
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
        });

        // 3. Pindahkan (Migrasi) data stok lama dari tabel products ke tabel product_stocks
        // Menggunakan DB::statement karena ini adalah operasi bulk insert yang paling efisien
        DB::statement('
            INSERT INTO product_stocks (warehouse_id, product_sku, quantity)
            SELECT 1, sku, stock FROM products
        ');

        // 4. Modifikasi Tabel Stock Adjustments agar mencatat Gudang
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->unsignedBigInteger('warehouse_id')->default(1)->after('date');
            
            // Opsional (Sangat Disarankan): Menambahkan Foreign Key
            // $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback step 4: Hapus kolom warehouse_id
        Schema::table('stock_adjustments', function (Blueprint $table) {
            // Jika Anda meng-uncomment foreign key di atas, Anda harus drop foreign key-nya dulu:
            // $table->dropForeign(['warehouse_id']);
            $table->dropColumn('warehouse_id');
        });

        // Rollback step 2 & 1: Hapus tabel (urutan dibalik untuk menghindari foreign key constraint error)
        Schema::dropIfExists('product_stocks');
        Schema::dropIfExists('warehouses');
    }
};