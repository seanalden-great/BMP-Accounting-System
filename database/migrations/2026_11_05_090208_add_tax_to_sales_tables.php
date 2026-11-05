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
        // Tambahkan total pajak di Induk SO
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('total_tax', 15, 2)->default(0)->after('total');
        });

        // Tambahkan rincian pajak di tiap item barang
        Schema::table('sale_items', function (Blueprint $table) {
            $table->integer('tax_rate')->default(0)->after('price'); // Misal: 0, 11, atau 12
            $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_tables', function (Blueprint $table) {
            //
        });
    }
};
