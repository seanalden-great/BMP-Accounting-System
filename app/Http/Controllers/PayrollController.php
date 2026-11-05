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

    // FUNGSI UPDATE DATA KARYAWAN (EDIT)
    public function updateEmployee(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'nik'          => 'required|string|max:50|unique:employees,nik,' . $employee->id,
            'name'         => 'required|string|max:150',
            'position'     => 'required|string|max:100',
            'basic_salary' => 'required|string',
            'has_bpjs'     => 'required|boolean',
            'bank_account' => 'nullable|string|max:100',
        ]);

        try {
            $validated['basic_salary'] = str_replace('.', '', $validated['basic_salary']);
            $employee->update($validated);
            return redirect()->route('payroll.index')->with('success', 'Data Karyawan berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data karyawan.');
        }
    }

    // FUNGSI HAPUS DATA KARYAWAN (DELETE)
    public function destroyEmployee($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $employee->delete();
            return redirect()->route('payroll.index')->with('success', 'Data Karyawan berhasil dihapus dari sistem HRD.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus karyawan. Data mungkin sudah terikat dengan riwayat penggajian.');
        }
    }

    // FUNGSI INTI: PROSES PENGGAJIAN & PENJURNALAN OTOMATIS (PAYROLL ENGINE)
    public function processPayroll(Request $request)
    {
        $month = \Carbon\Carbon::now()->month;
        $year = \Carbon\Carbon::now()->year;

        // 1. Validasi: Cegah user memproses gaji 2x di bulan yang sama
        $existing = Payroll::where('period_month', $month)->where('period_year', $year)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Gagal: Penggajian untuk bulan ini sudah diproses dan dijurnal sebelumnya!');
        }

        $employees = Employee::all();
        if ($employees->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data karyawan aktif untuk diproses.');
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $totalGross = 0;
            $totalDeduction = 0;
            $totalNet = 0;

            // Buat Induk Payroll
            $payroll = Payroll::create([
                'period_month' => $month,
                'period_year' => $year,
                'status' => 'Dibayar'
            ]);

            // Loop hitungan per karyawan & buat Slip Gaji (Details)
            foreach ($employees as $emp) {
                $bpjsDeduct = $emp->has_bpjs ? ($emp->basic_salary * 0.03) : 0;
                $net = $emp->basic_salary - $bpjsDeduct;

                \App\Models\PayrollDetail::create([
                    'payroll_id' => $payroll->id,
                    'employee_id' => $emp->id,
                    'basic_salary' => $emp->basic_salary,
                    'allowance' => 0,
                    'bpjs_deduction' => $bpjsDeduct,
                    'tax_deduction' => 0,
                    'net_salary' => $net
                ]);

                $totalGross += $emp->basic_salary;
                $totalDeduction += $bpjsDeduct;
                $totalNet += $net;
            }

            // 2. OTOMASI JURNAL AKUNTANSI (Potong Saldo Bank & Catat Beban)
            // Mencari Akun Beban Gaji (Awalan 6 atau 5) dan Akun Kas Bank Utama (Awalan 10)
            $salaryExpenseAcc = \App\Models\Account::where('code', 'LIKE', '6%')->where('is_header', false)->first() ?? \App\Models\Account::where('code', 'LIKE', '5%')->where('is_header', false)->first();
            $bankAcc = \App\Models\Account::where('code', 'LIKE', '10%')->where('is_header', false)->first() ?? \App\Models\Account::where('code', 'LIKE', '11%')->where('is_header', false)->first();
            $payableAcc = \App\Models\Account::where('code', 'LIKE', '2%')->where('is_header', false)->first(); // Akun Hutang BPJS/Pajak

            if (!$salaryExpenseAcc || !$bankAcc) {
                \Illuminate\Support\Facades\DB::rollBack();
                return redirect()->back()->with('error', 'Konfigurasi Akun Gagal: Akun Beban Gaji (6xx) atau Akun Kas/Bank (10x) tidak ditemukan di CoA.');
            }

            // Catat Jurnalnya
            $journalCode = "PAY-".\Carbon\Carbon::now()->format('Ymd')."-".$payroll->id;
            $journal = \App\Models\Journal::create([
                'date' => \Carbon\Carbon::now(),
                'description' => "Beban Gaji Karyawan Periode " . \Carbon\Carbon::now()->translatedFormat('F Y'),
                'reference_no' => $journalCode
            ]);

            \App\Models\JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $salaryExpenseAcc->code, 'debit' => $totalGross, 'credit' => 0]);
            \App\Models\JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $bankAcc->code, 'debit' => 0, 'credit' => $totalNet]);

            if ($totalDeduction > 0 && $payableAcc) {
                \App\Models\JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $payableAcc->code, 'debit' => 0, 'credit' => $totalDeduction]);
            }

            // Update total di Induk Payroll
            $payroll->update([
                'total_gross' => $totalGross,
                'total_deduction' => $totalDeduction,
                'total_net' => $totalNet,
                'journal_id' => $journal->id
            ]);

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->back()->with('success', 'Penggajian bulan ini berhasil diproses & dijurnal otomatis ke Kas Bank (Kode: ' . $journalCode . ')!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Sistem Gagal memproses penggajian: ' . $e->getMessage());
        }
    }
}
