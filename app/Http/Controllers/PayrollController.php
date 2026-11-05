<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index()
    {
        // 1. Tarik semua data karyawan
        $employees = Employee::orderBy('name', 'asc')->get();

        // 2. Hitung statistik dasar untuk kartu Dasbor
        $totalEmployees = $employees->count();
        $estimasiGajiPokok = $employees->sum('basic_salary');

        // 3. Hitung estimasi potongan BPJS (Asumsi 3% JHT & Kes) hanya untuk karyawan yang status BPJS-nya aktif
        $estimasiPotongan = 0;
        foreach ($employees as $emp) {
            if ($emp->has_bpjs) {
                $estimasiPotongan += ($emp->basic_salary * 0.03);
            }
        }

        return view('payroll.index', compact('employees', 'totalEmployees', 'estimasiGajiPokok', 'estimasiPotongan'));
    }
}
