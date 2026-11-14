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
        // Tambahkan kolom 'hari absen' di tabel karyawan
        Schema::table('employees', function (Blueprint $table) {
            $table->integer('absent_days')->default(0)->after('has_bpjs');
        });

        // Tambahkan kolom 'nominal potongan absen' di tabel rincian slip gaji
        Schema::table('payroll_details', function (Blueprint $table) {
            $table->decimal('absence_deduction', 15, 2)->default(0)->after('allowance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees_and_payroll', function (Blueprint $table) {
            //
        });
    }
};
