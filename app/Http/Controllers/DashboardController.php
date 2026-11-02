<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense; // <-- INILAH BARIS YANG TERLEWAT SEBELUMNYA
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

        // 1. TOTAL PENDAPATAN (Penjualan yang sudah dikirim/SJ terbit bulan ini)
        $pendapatanBulanIni = Sale::whereIn('status', ['Pengiriman', 'Tagihan'])
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('total');

        // 2. TOTAL PENGELUARAN (Pembelian yang sudah diterima + Pengeluaran Biaya KBD bulan ini)
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

        // 4. SALDO KAS & BANK (Akun berawalan 10 atau 11)
        $bankAccounts = Account::where(function($query) {
            $query->where('code', 'LIKE', '10%')->orWhere('code', 'LIKE', '11%');
        })->where('is_header', false)->get();

        $bankCodes = $bankAccounts->pluck('code')->toArray();

        $totalKasBank = JournalEntry::whereIn('account_code', $bankCodes)
            ->selectRaw('SUM(debit) - SUM(credit) as balance')
            ->value('balance') ?? 0;

        // Rincian masing-masing rekening bank untuk UI
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

        // 5. RINCIAN PIUTANG (Penjualan yang sudah dikirim tapi belum lunas / belum jadi Tagihan)
        $piutangList = Sale::with('contact')
            ->where('status', 'Pengiriman') // Pengiriman = SJ sudah terbit tapi belum dibayar
            ->orderBy('date', 'asc')
            ->get();
        $totalPiutang = $piutangList->sum('total');

        // 6. RINCIAN HUTANG (Pembelian yang sudah diterima tapi belum dibayar)
        $hutangList = Purchase::with('contact')
            ->where('status', 'Pengiriman') // Pengiriman = Barang sudah diterima di gudang tapi belum dibayar
            ->orderBy('date', 'asc')
            ->get();
        $totalHutang = $hutangList->sum('total');

        // 7. GRAFIK ARUS KAS (6 BULAN TERAKHIR)
        $chartLabels = [];
        $chartInflows = [];
        $chartOutflows = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $chartLabels[] = $date->translatedFormat('M Y');

            // Uang Masuk = Total Debit pada akun Kas/Bank di bulan tersebut
            $inflow = JournalEntry::whereIn('account_code', $bankCodes)
                ->whereHas('journal', function($q) use ($date) {
                    $q->whereMonth('date', $date->month)->whereYear('date', $date->year);
                })->sum('debit');

            // Uang Keluar = Total Kredit pada akun Kas/Bank di bulan tersebut
            $outflow = JournalEntry::whereIn('account_code', $bankCodes)
                ->whereHas('journal', function($q) use ($date) {
                    $q->whereMonth('date', $date->month)->whereYear('date', $date->year);
                })->sum('credit');

            $chartInflows[] = $inflow;
            $chartOutflows[] = $outflow;
        }

        return view('dashboard', compact(
            'pendapatanBulanIni', 'pengeluaranBulanIni', 'labaBersih', 'totalKasBank',
            'rekeningDetails', 'piutangList', 'totalPiutang', 'hutangList', 'totalHutang',
            'chartLabels', 'chartInflows', 'chartOutflows'
        ));
    }
}
