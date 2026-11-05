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

    // FUNGSI BARU: MENYIMPAN DATA KARYAWAN
    public function storeEmployee(Request $request)
    {
        $validated = $request->validate([
            'nik'          => 'required|string|max:50|unique:employees,nik',
            'name'         => 'required|string|max:150',
            'position'     => 'required|string|max:100',
            'basic_salary' => 'required|string', // String karena akan ada pembersihan titik ribuan
            'has_bpjs'     => 'required|boolean',
            'bank_account' => 'nullable|string|max:100',
        ]);

        try {
            // Bersihkan format titik (contoh: "15.000.000" menjadi "15000000") sebelum masuk ke DB
            $validated['basic_salary'] = str_replace('.', '', $validated['basic_salary']);

            Employee::create($validated);
            return redirect()->route('payroll.index')->with('success', 'Data Karyawan Baru berhasil ditambahkan ke dalam sistem HRD.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan karyawan. Cek kembali data Anda (NIK mungkin duplikat).');
        }
    }
}
