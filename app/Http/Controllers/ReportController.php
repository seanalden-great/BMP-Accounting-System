<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // GET /reports
    // public function index()
    // {
    //     $accounts = Account::all();

    //     // Agregasi mutasi debit dan kredit
    //     $summaries = JournalEntry::select(
    //             'account_code',
    //             DB::raw('COALESCE(SUM(debit), 0) as total_debit'),
    //             DB::raw('COALESCE(SUM(credit), 0) as total_credit')
    //         )
    //         ->groupBy('account_code')
    //         ->get()
    //         ->keyBy('account_code');

    //     $reportData = [];
    //     $netProfit = 0;

    //     foreach ($accounts as $acc) {
    //         // Hanya ambil akun detail (bukan header)
    //         if ($acc->is_header) continue;

    //         $sum = $summaries->get($acc->code);
    //         if (!$sum) continue; // Lewati jika tidak ada transaksi

    //         $totalDebit = $sum->total_debit;
    //         $totalCredit = $sum->total_credit;
    //         $balance = 0;
    //         $accType = "";

    //         // LOGIKA SALDO BERDASARKAN COA EXCEL BARU (AWALAN KODE)
    //         if (str_starts_with($acc->code, '1') || str_starts_with($acc->code, '2')) {
    //             // 1 = Aktiva Lancar, 2 = Aktiva Tetap & Penyusutan
    //             $accType = "Harta";
    //             // Saldo normal Harta adalah Debit, KECUALI Akumulasi Penyusutan (21) yang bersaldo Kredit
    //             if (str_starts_with($acc->code, '21')) {
    //                 $balance = $totalCredit - $totalDebit;
    //             } else {
    //                 $balance = $totalDebit - $totalCredit;
    //             }
    //         }
    //         elseif (str_starts_with($acc->code, '3')) {
    //             $accType = "Kewajiban";
    //             $balance = $totalCredit - $totalDebit;
    //         }
    //         elseif (str_starts_with($acc->code, '4')) {
    //             $accType = "Modal";
    //             $balance = $totalCredit - $totalDebit;
    //         }
    //         elseif (str_starts_with($acc->code, '5')) {
    //             $accType = "Pendapatan";
    //             $balance = $totalCredit - $totalDebit;
    //             $netProfit += $balance; // Pendapatan menambah laba bersih
    //         }
    //         elseif (str_starts_with($acc->code, '6')) {
    //             $accType = "HPP";
    //             $balance = $totalDebit - $totalCredit;
    //             $netProfit -= $balance; // HPP mengurangi laba bersih
    //         }
    //         elseif (str_starts_with($acc->code, '7')) {
    //             $accType = "Beban";
    //             $balance = $totalDebit - $totalCredit;
    //             $netProfit -= $balance; // Beban operasional mengurangi laba bersih
    //         }

    //         if ($balance != 0) {
    //             $reportData[] = [
    //                 'code'    => $acc->code,
    //                 'name'    => $acc->name,
    //                 'type'    => $accType,
    //                 'balance' => $balance,
    //             ];
    //         }
    //     }

    //     // FITUR BARU: Ambil Rincian Transaksi Jurnal untuk Pendapatan (Awalan 5) dan HPP (Awalan 6)
    //     // Kita menggunakan sortByDesc secara dinamis agar urutan waktu transaksi (terbaru ke terlama) akurat
    //     $detailPendapatan = JournalEntry::with(['journal', 'account'])
    //         ->where('account_code', 'LIKE', '5%')
    //         ->get()
    //         ->sortByDesc(fn($entry) => $entry->journal->date ?? $entry->created_at);

    //     $detailHPP = JournalEntry::with(['journal', 'account'])
    //         ->where('account_code', 'LIKE', '6%')
    //         ->get()
    //         ->sortByDesc(fn($entry) => $entry->journal->date ?? $entry->created_at);

    //     // Return ke view Laporan beserta variabel rincian (detail) yang baru dibuat
    //     return view('reports.index', compact('reportData', 'netProfit', 'detailPendapatan', 'detailHPP'));
    // }

    // GET /reports
    public function index()
    {
        $accounts = Account::all();

        // Agregasi mutasi debit dan kredit
        $summaries = JournalEntry::select(
                'account_code',
                DB::raw('COALESCE(SUM(debit), 0) as total_debit'),
                DB::raw('COALESCE(SUM(credit), 0) as total_credit')
            )
            ->groupBy('account_code')
            ->get()
            ->keyBy('account_code');

        $reportData = [];
        $netProfit = 0;

        foreach ($accounts as $acc) {
            if ($acc->is_header) continue;

            $sum = $summaries->get($acc->code);
            if (!$sum) continue;

            $totalDebit = $sum->total_debit;
            $totalCredit = $sum->total_credit;
            $balance = 0;
            $accType = "";

            if (str_starts_with($acc->code, '1') || str_starts_with($acc->code, '2')) {
                $accType = "Harta";
                if (str_starts_with($acc->code, '21')) {
                    $balance = $totalCredit - $totalDebit;
                } else {
                    $balance = $totalDebit - $totalCredit;
                }
            } elseif (str_starts_with($acc->code, '3')) {
                $accType = "Kewajiban";
                $balance = $totalCredit - $totalDebit;
            } elseif (str_starts_with($acc->code, '4')) {
                $accType = "Modal";
                $balance = $totalCredit - $totalDebit;
            } elseif (str_starts_with($acc->code, '5')) {
                $accType = "Pendapatan";
                $balance = $totalCredit - $totalDebit;
                $netProfit += $balance;
            } elseif (str_starts_with($acc->code, '6')) {
                $accType = "HPP";
                $balance = $totalDebit - $totalCredit;
                $netProfit -= $balance;
            } elseif (str_starts_with($acc->code, '7')) {
                $accType = "Beban";
                $balance = $totalDebit - $totalCredit;
                $netProfit -= $balance;
            }

            if ($balance != 0) {
                $reportData[] = [
                    'code'    => $acc->code,
                    'name'    => $acc->name,
                    'type'    => $accType,
                    'balance' => $balance,
                ];
            }
        }

        // ==============================================================
        // FITUR BARU: ALGORITMA LAPORAN ARUS KAS (CASH FLOW - DIRECT)
        // ==============================================================
        $cashFlowData = [
            'operating_in' => 0, 'operating_out' => 0,
            'investing_in' => 0, 'investing_out' => 0,
            'financing_in' => 0, 'financing_out' => 0,
        ];

        // 1. Identifikasi mana saja yang merupakan akun Kas & Bank (Awalan 10 & 11)
        $bankCodes = Account::where(function($q) {
            $q->where('code', 'LIKE', '10%')->orWhere('code', 'LIKE', '11%');
        })->pluck('code')->toArray();

        // 2. Tarik semua jurnal yang memotong Kas/Bank
        $bankEntries = JournalEntry::with('journal.entries')
            ->whereIn('account_code', $bankCodes)
            ->get();

        foreach ($bankEntries as $bankEntry) {
            $journal = $bankEntry->journal;
            // 3. Intip akun lawan dari jurnal tersebut untuk mengetahui ALASAN uang tersebut masuk/keluar
            $lawanEntry = $journal->entries->whereNotIn('account_code', $bankCodes)->first();

            if ($lawanEntry) {
                $lawanCode = $lawanEntry->account_code;
                $isCashIn = $bankEntry->debit > 0; // Jika bank di-debit, artinya ada kas masuk
                $amount = $isCashIn ? $bankEntry->debit : $bankEntry->credit;

                // 4. Pengelompokan Aktivitas berdasar PSAK
                if (str_starts_with($lawanCode, '20')) {
                    // ASET TETAP (20) -> Masuk ke Aktivitas Investasi
                    if ($isCashIn) $cashFlowData['investing_in'] += $amount;
                    else $cashFlowData['investing_out'] += $amount;
                } elseif (str_starts_with($lawanCode, '4') || (str_starts_with($lawanCode, '3') && !str_starts_with($lawanCode, '31'))) {
                    // MODAL (4) ATAU HUTANG BANK (3 SELAIN 31 Hutang Usaha) -> Masuk ke Aktivitas Pendanaan
                    if ($isCashIn) $cashFlowData['financing_in'] += $amount;
                    else $cashFlowData['financing_out'] += $amount;
                } else {
                    // SISA-NYA (Piutang 12, Hutang Usaha 31, Pendapatan 5, HPP 6, Beban 7) -> Masuk ke Aktivitas Operasi
                    if ($isCashIn) $cashFlowData['operating_in'] += $amount;
                    else $cashFlowData['operating_out'] += $amount;
                }
            }
        }

        // Ambil Rincian Transaksi Jurnal untuk Laba Rugi
        $detailPendapatan = JournalEntry::with(['journal', 'account'])
            ->where('account_code', 'LIKE', '5%')->get()->sortByDesc(fn($entry) => $entry->journal->date ?? $entry->created_at);

        $detailHPP = JournalEntry::with(['journal', 'account'])
            ->where('account_code', 'LIKE', '6%')->get()->sortByDesc(fn($entry) => $entry->journal->date ?? $entry->created_at);

        // Jangan lupa lempar variabel $cashFlowData ke View
        return view('reports.index', compact('reportData', 'netProfit', 'detailPendapatan', 'detailHPP', 'cashFlowData'));
    }
}
