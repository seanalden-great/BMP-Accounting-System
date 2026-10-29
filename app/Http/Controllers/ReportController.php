<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
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
            // Hanya ambil akun detail (bukan header)
            if ($acc->is_header) continue;

            $sum = $summaries->get($acc->code);
            if (!$sum) continue; // Lewati jika tidak ada transaksi

            $totalDebit = $sum->total_debit;
            $totalCredit = $sum->total_credit;
            $balance = 0;
            $accType = "";

            // LOGIKA SALDO BERDASARKAN COA EXCEL BARU (AWALAN KODE)
            if (str_starts_with($acc->code, '1') || str_starts_with($acc->code, '2')) {
                // 1 = Aktiva Lancar, 2 = Aktiva Tetap & Penyusutan
                $accType = "Harta";
                // Saldo normal Harta adalah Debit, KECUALI Akumulasi Penyusutan (21) yang bersaldo Kredit
                if (str_starts_with($acc->code, '21')) {
                    $balance = $totalCredit - $totalDebit;
                } else {
                    $balance = $totalDebit - $totalCredit;
                }
            } 
            elseif (str_starts_with($acc->code, '3')) {
                $accType = "Kewajiban";
                $balance = $totalCredit - $totalDebit;
            } 
            elseif (str_starts_with($acc->code, '4')) {
                $accType = "Modal";
                $balance = $totalCredit - $totalDebit;
            } 
            elseif (str_starts_with($acc->code, '5')) {
                $accType = "Pendapatan";
                $balance = $totalCredit - $totalDebit;
                $netProfit += $balance; // Pendapatan menambah laba bersih
            } 
            elseif (str_starts_with($acc->code, '6')) {
                $accType = "HPP";
                $balance = $totalDebit - $totalCredit;
                $netProfit -= $balance; // HPP mengurangi laba bersih
            } 
            elseif (str_starts_with($acc->code, '7')) {
                $accType = "Beban";
                $balance = $totalDebit - $totalCredit;
                $netProfit -= $balance; // Beban operasional mengurangi laba bersih
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

        // Return ke view Laporan
        return view('reports.index', compact('reportData', 'netProfit'));
    }
}