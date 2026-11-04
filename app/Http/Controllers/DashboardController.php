<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Models\Sale;
// use App\Models\Purchase;
// use App\Models\Expense; // <-- INILAH BARIS YANG TERLEWAT SEBELUMNYA
// use App\Models\Account;
// use App\Models\JournalEntry;
// use Carbon\Carbon;
// use Illuminate\Support\Facades\DB;

// class DashboardController extends Controller
// {
//     public function index()
//     {
//         $currentMonth = Carbon::now()->month;
//         $currentYear = Carbon::now()->year;

//         // 1. TOTAL PENDAPATAN (Penjualan yang sudah dikirim/SJ terbit bulan ini)
//         $pendapatanBulanIni = Sale::whereIn('status', ['Pengiriman', 'Tagihan'])
//             ->whereMonth('date', $currentMonth)
//             ->whereYear('date', $currentYear)
//             ->sum('total');

//         // 2. TOTAL PENGELUARAN (Pembelian yang sudah diterima + Pengeluaran Biaya KBD bulan ini)
//         $pembelianBulanIni = Purchase::whereIn('status', ['Pengiriman', 'Tagihan'])
//             ->whereMonth('date', $currentMonth)
//             ->whereYear('date', $currentYear)
//             ->sum('total');

//         $biayaBulanIni = Expense::whereMonth('date', $currentMonth)
//             ->whereYear('date', $currentYear)
//             ->sum('amount');

//         $pengeluaranBulanIni = $pembelianBulanIni + $biayaBulanIni;

//         // 3. LABA BERSIH
//         $labaBersih = $pendapatanBulanIni - $pengeluaranBulanIni;

//         // 4. SALDO KAS & BANK (Akun berawalan 10 atau 11)
//         $bankAccounts = Account::where(function($query) {
//             $query->where('code', 'LIKE', '10%')->orWhere('code', 'LIKE', '11%');
//         })->where('is_header', false)->get();

//         $bankCodes = $bankAccounts->pluck('code')->toArray();

//         $totalKasBank = JournalEntry::whereIn('account_code', $bankCodes)
//             ->selectRaw('SUM(debit) - SUM(credit) as balance')
//             ->value('balance') ?? 0;

//         // Rincian masing-masing rekening bank untuk UI
//         $rekeningDetails = [];
//         foreach ($bankAccounts as $bank) {
//             $bal = JournalEntry::where('account_code', $bank->code)
//                 ->selectRaw('SUM(debit) - SUM(credit) as balance')
//                 ->value('balance') ?? 0;

//             if ($bal != 0) {
//                 $rekeningDetails[] = [
//                     'name' => $bank->name,
//                     'code' => $bank->code,
//                     'balance' => $bal
//                 ];
//             }
//         }

//         // 5. RINCIAN PIUTANG (Penjualan yang sudah dikirim tapi belum lunas / belum jadi Tagihan)
//         $piutangList = Sale::with('contact')
//             ->where('status', 'Pengiriman') // Pengiriman = SJ sudah terbit tapi belum dibayar
//             ->orderBy('date', 'asc')
//             ->get();
//         $totalPiutang = $piutangList->sum('total');

//         // 6. RINCIAN HUTANG (Pembelian yang sudah diterima tapi belum dibayar)
//         $hutangList = Purchase::with('contact')
//             ->where('status', 'Pengiriman') // Pengiriman = Barang sudah diterima di gudang tapi belum dibayar
//             ->orderBy('date', 'asc')
//             ->get();
//         $totalHutang = $hutangList->sum('total');

//         // 7. GRAFIK ARUS KAS (6 BULAN TERAKHIR)
//         $chartLabels = [];
//         $chartInflows = [];
//         $chartOutflows = [];

//         for ($i = 5; $i >= 0; $i--) {
//             $date = Carbon::now()->subMonths($i);
//             $chartLabels[] = $date->translatedFormat('M Y');

//             // Uang Masuk = Total Debit pada akun Kas/Bank di bulan tersebut
//             $inflow = JournalEntry::whereIn('account_code', $bankCodes)
//                 ->whereHas('journal', function($q) use ($date) {
//                     $q->whereMonth('date', $date->month)->whereYear('date', $date->year);
//                 })->sum('debit');

//             // Uang Keluar = Total Kredit pada akun Kas/Bank di bulan tersebut
//             $outflow = JournalEntry::whereIn('account_code', $bankCodes)
//                 ->whereHas('journal', function($q) use ($date) {
//                     $q->whereMonth('date', $date->month)->whereYear('date', $date->year);
//                 })->sum('credit');

//             $chartInflows[] = $inflow;
//             $chartOutflows[] = $outflow;
//         }

//         return view('dashboard', compact(
//             'pendapatanBulanIni', 'pengeluaranBulanIni', 'labaBersih', 'totalKasBank',
//             'rekeningDetails', 'piutangList', 'totalPiutang', 'hutangList', 'totalHutang',
//             'chartLabels', 'chartInflows', 'chartOutflows'
//         ));
//     }
// }

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Account;
use App\Models\JournalEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // --- SISTEM PERINGATAN DINI (H-7 JATUH TEMPO) ---
        $sevenDays = Carbon::now()->addDays(7)->toDateString();
        // Cari Pembelian (Hutang) yang belum lunas (Pesanan/Pengiriman)
        $poAlerts = Purchase::with('contact')->whereIn('status', ['Pesanan', 'Pengiriman'])
            ->whereDate('due_date', '<=', $sevenDays)->orderBy('due_date', 'asc')->get();
        // Cari Penjualan (Piutang) yang belum lunas
        $soAlerts = Sale::with('contact')->whereIn('status', ['Pesanan', 'Pengiriman'])
            ->whereDate('due_date', '<=', $sevenDays)->orderBy('due_date', 'asc')->get();
        $totalAlerts = $poAlerts->count() + $soAlerts->count();

        // 1. TOTAL PENDAPATAN
        $pendapatanBulanIni = Sale::whereIn('status', ['Pengiriman', 'Tagihan'])
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('total');

        // 2. TOTAL PENGELUARAN
        $pembelianBulanIni = Purchase::whereIn('status', ['Pengiriman', 'Tagihan'])
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('total');

        $biayaBulanIni = Expense::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');

        $pengeluaranBulanIni = $pembelianBulanIni + $biayaBulanIni;

        // 3. LABA BERSIH
        $labaBersih = $pendapatanBulanIni - $pengeluaranBulanIni;

        // 4. SALDO KAS & BANK
        $bankAccounts = Account::where(function($query) {
            $query->where('code', 'LIKE', '10%')->orWhere('code', 'LIKE', '11%');
        })->where('is_header', false)->get();

        $bankCodes = $bankAccounts->pluck('code')->toArray();

        $totalKasBank = JournalEntry::whereIn('account_code', $bankCodes)
            ->selectRaw('SUM(debit) - SUM(credit) as balance')
            ->value('balance') ?? 0;

        $rekeningDetails = [];
        foreach ($bankAccounts as $bank) {
            $bal = JournalEntry::where('account_code', $bank->code)
                ->selectRaw('SUM(debit) - SUM(credit) as balance')
                ->value('balance') ?? 0;

            if ($bal != 0) {
                $rekeningDetails[] = [
                    'name' => $bank->name,
                    'code' => $bank->code,
                    'balance' => $bal
                ];
            }
        }

        // 5. RINCIAN PIUTANG
        $piutangList = Sale::with('contact')->where('status', 'Pengiriman')->orderBy('date', 'asc')->get();
        $totalPiutang = $piutangList->sum('total');

        // 6. RINCIAN HUTANG
        $hutangList = Purchase::with('contact')->where('status', 'Pengiriman')->orderBy('date', 'asc')->get();
        $totalHutang = $hutangList->sum('total');

        // 7. GRAFIK ARUS KAS
        $chartLabels = [];
        $chartInflows = [];
        $chartOutflows = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $chartLabels[] = $date->translatedFormat('M Y');

            $inflow = JournalEntry::whereIn('account_code', $bankCodes)
                ->whereHas('journal', function($q) use ($date) {
                    $q->whereMonth('date', $date->month)->whereYear('date', $date->year);
                })->sum('debit');

            $outflow = JournalEntry::whereIn('account_code', $bankCodes)
                ->whereHas('journal', function($q) use ($date) {
                    $q->whereMonth('date', $date->month)->whereYear('date', $date->year);
                })->sum('credit');

            $chartInflows[] = $inflow;
            $chartOutflows[] = $outflow;
        }

        // 8. DATA TAMBAHAN (STATISTIK MASTER & OPERASIONAL)
        $totalProduk = \App\Models\Product::count();
        $totalKategori = \App\Models\Category::count();
        $totalPelanggan = \App\Models\Contact::where('type', 'Customer')->count();
        $totalPemasok = \App\Models\Contact::where('type', 'Supplier')->count();

        $totalSO = Sale::count();
        $totalPO = Purchase::count();
        $totalExpense = Expense::count();

        // return view('dashboard', compact(
        //     'pendapatanBulanIni', 'pengeluaranBulanIni', 'labaBersih', 'totalKasBank',
        //     'rekeningDetails', 'piutangList', 'totalPiutang', 'hutangList', 'totalHutang',
        //     'chartLabels', 'chartInflows', 'chartOutflows',
        //     'poAlerts', 'soAlerts', 'totalAlerts' // <-- Meneruskan data alert ke view
        // ));

        // Pastikan variabel baru tersebut dimasukkan ke dalam compact()
        return view('dashboard', compact(
            'pendapatanBulanIni', 'pengeluaranBulanIni', 'labaBersih', 'totalKasBank',
            'rekeningDetails', 'piutangList', 'totalPiutang', 'hutangList', 'totalHutang',
            'chartLabels', 'chartInflows', 'chartOutflows',
            'poAlerts', 'soAlerts', 'totalAlerts',
            'totalProduk', 'totalKategori', 'totalPelanggan', 'totalPemasok', 'totalSO', 'totalPO', 'totalExpense'
        ));
    }
}
