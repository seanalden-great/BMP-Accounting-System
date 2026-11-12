<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    // Menampilkan Chart of Accounts beserta saldonya
    public function index()
    {
        $accounts = Account::orderBy('code', 'asc')->get();

        // Hitung total mutasi Debit & Kredit per akun
        $sums = JournalEntry::select('account_code', DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))
            ->groupBy('account_code')
            ->get()
            ->keyBy('account_code');

        $response = [];

        // Variabel penampung Total Neraca Saldo (hanya menjumlahkan akun detail/anak)
        $grandTotalDebit = 0;
        $grandTotalKredit = 0;

        foreach ($accounts as $i => $acc) {
            $debit = 0;
            $kredit = 0;
            $sisa = 0;

            if ($acc->is_header) {
                // Jumlahkan nilai dari akun anak berdasarkan LEVEL
                for ($j = $i + 1; $j < count($accounts); $j++) {
                    $child = $accounts[$j];

                    if ($child->level <= $acc->level) {
                        break;
                    }

                    if (!$child->is_header && isset($sums[$child->code])) {
                        $debit += $sums[$child->code]->total_debit;
                        $kredit += $sums[$child->code]->total_credit;
                    }
                }
            } else {
                if (isset($sums[$acc->code])) {
                    $debit = $sums[$acc->code]->total_debit;
                    $kredit = $sums[$acc->code]->total_credit;
                }

                // Tambahkan ke Grand Total (HANYA untuk akun detail agar tidak double-counting)
                $grandTotalDebit += $debit;
                $grandTotalKredit += $kredit;
            }

            if ($acc->normal_balance == 'D') {
                $sisa = $debit - $kredit;
            } elseif ($acc->normal_balance == 'K') {
                $sisa = $kredit - $debit;
            } else {
                // Jika Pos '-' (Akun Header/Induk), ikuti saldo normal kelompok kode depannya:
                // Kelompok 3 (Kewajiban), 4 (Modal), 5 (Pendapatan), dan 21 (Akum. Penyusutan) bersaldo normal Kredit
                if (
                    str_starts_with($acc->code, '3') ||
                    str_starts_with($acc->code, '4') ||
                    str_starts_with($acc->code, '5') ||
                    str_starts_with($acc->code, '21')
                ) {
                    $sisa = $kredit - $debit;
                } else {
                    $sisa = $debit - $kredit;
                }
            }

            // OPSI ALTERNATIF: Jika hanya ingin menampilkan Saldo Akhir di salah satu sisi (D atau K saja)
            $isCreditNormal = ($acc->normal_balance == 'K') || (
                $acc->normal_balance == '-' && (
                    str_starts_with($acc->code, '3') ||
                    str_starts_with($acc->code, '4') ||
                    str_starts_with($acc->code, '5') ||
                    str_starts_with($acc->code, '21')
                )
            );

            $netDebit  = (!$isCreditNormal && $sisa > 0) || ($isCreditNormal && $sisa < 0) ? abs($sisa) : null;
            $netKredit = ($isCreditNormal && $sisa > 0) || (!$isCreditNormal && $sisa < 0) ? abs($sisa) : null;

            $item = [
                'code'     => $acc->code,
                'name'     => $acc->name,
                'isHeader' => $acc->is_header,
                'pos'      => $acc->normal_balance,
                'level'    => $acc->level,
                'debit'    => $debit > 0 ? $debit : null,
                'kredit'   => $kredit > 0 ? $kredit : null,
                'sisa'     => $sisa,
            ];

            $response[] = $item;
        }

        // Return ke view blade beserta Grand Total
        return view('accounts.index', compact('response', 'grandTotalDebit', 'grandTotalKredit'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'           => 'required|string|unique:accounts,code',
            'name'           => 'required|string',
            'is_header'      => 'boolean',
            'normal_balance' => 'required|in:D,K,-',
            'level'          => 'required|integer',
        ]);

        try {
            $account = Account::create($validated);

            // Jika request datang dari AJAX (Fetch API), kembalikan JSON
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Akun berhasil ditambahkan.',
                    'account' => $account
                ]);
            }

            return redirect()->route('accounts.index')->with('success', 'Akun berhasil ditambahkan');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan akun: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal menyimpan akun baru');
        }

        \App\Models\AuditLog::record('MEMBUAT', 'Akun COA', 'Membuat akun COA baru');
    }

    public function update(Request $request, $code)
    {
        $account = Account::findOrFail($code);

        $validated = $request->validate([
            'name'           => 'required|string',
            'is_header'      => 'boolean',
            'normal_balance' => 'required|in:D,K,-',
            'level'          => 'required|integer',
        ]);

        try {
            $account->update($validated);
            return redirect()->route('accounts.index')->with('success', 'Akun berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui akun');
        }
    }

    public function destroy($code)
    {
        // Proteksi jika akun sudah dipakai di Jurnal
        $count = JournalEntry::where('account_code', $code)->count();
        if ($count > 0) {
            return redirect()->back()->with('error', 'Akun tidak bisa dihapus karena memiliki riwayat jurnal!');
        }

        try {
            $account = Account::findOrFail($code);
            $account->delete();
            return redirect()->route('accounts.index')->with('success', 'Akun berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus akun');
        }
    }

    // --- FITUR EKSPOR EXCEL & PDF ---
    public function export(Request $request)
    {
        $type = $request->query('type', 'internal');
        $format = $request->query('format', 'csv');

        // 1. AMBIL & KALKULASI DATA (Sama persis dengan logika index)
        $accounts = Account::orderBy('code', 'asc')->get();
        $sums = JournalEntry::select('account_code', DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))
            ->groupBy('account_code')->get()->keyBy('account_code');

        $response = [];
        $grandTotalDebit = 0; $grandTotalKredit = 0;

        foreach ($accounts as $i => $acc) {
            $debit = 0; $kredit = 0; $sisa = 0;

            if ($acc->is_header) {
                for ($j = $i + 1; $j < count($accounts); $j++) {
                    $child = $accounts[$j];
                    if ($child->level <= $acc->level) break;
                    if (!$child->is_header && isset($sums[$child->code])) {
                        $debit += $sums[$child->code]->total_debit;
                        $kredit += $sums[$child->code]->total_credit;
                    }
                }
            } else {
                if (isset($sums[$acc->code])) {
                    $debit = $sums[$acc->code]->total_debit;
                    $kredit = $sums[$acc->code]->total_credit;
                }
                $grandTotalDebit += $debit;
                $grandTotalKredit += $kredit;
            }

            if ($acc->normal_balance == 'D') {
                $sisa = $debit - $kredit;
            } elseif ($acc->normal_balance == 'K') {
                $sisa = $kredit - $debit;
            } else {
                if (str_starts_with($acc->code, '3') || str_starts_with($acc->code, '4') || str_starts_with($acc->code, '5') || str_starts_with($acc->code, '21')) {
                    $sisa = $kredit - $debit;
                } else {
                    $sisa = $debit - $kredit;
                }
            }

            $response[] = [
                'code' => $acc->code, 'name' => $acc->name, 'isHeader' => $acc->is_header,
                'pos' => $acc->normal_balance, 'level' => $acc->level,
                'debit' => $debit > 0 ? $debit : null, 'kredit' => $kredit > 0 ? $kredit : null,
                'sisa' => $sisa,
            ];
        }

        $reportName = $type === 'pajak' ? 'CHART OF ACCOUNTS (PAJAK)' : 'CHART OF ACCOUNTS (INTERNAL)';

        // 2. EXCEL EXPORT (HTML-TO-EXCEL SPOOFING)
        if ($format === 'csv') {
            $headers = [
                "Content-type" => "application/vnd.ms-excel",
                "Content-Disposition" => "attachment; filename=CoA_{$type}.xls",
                "Pragma" => "no-cache", "Cache-Control" => "must-revalidate, post-check=0, pre-check=0", "Expires" => "0"
            ];

            $callback = function() use ($response, $reportName, $grandTotalDebit, $grandTotalKredit) {
                echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>';
                echo '<table border="0"><tr><th colspan="6" style="font-size:16px; font-weight:bold; text-align:center;">PT. BERLIAN MANDIRI PERKASA</th></tr>';
                echo '<tr><th colspan="6" style="font-size:14px; font-weight:bold; text-align:center;">' . $reportName . '</th></tr>';
                echo '<tr><td colspan="6"></td></tr></table>';

                echo '<table border="1" style="font-family: Arial, sans-serif; font-size: 12px; border-collapse: collapse;">';
                echo '<thead style="background-color: #e2e8f0;">';
                echo '<tr><th rowspan="2" style="padding:8px; text-align:center;">CoA</th><th rowspan="2" style="padding:8px; text-align:center;">Nama Account</th><th rowspan="2" style="padding:8px; text-align:center;">Pos</th><th colspan="2" style="padding:8px; text-align:center;">Saldo</th><th rowspan="2" style="padding:8px; text-align:center;">SISA SALDO</th></tr>';
                echo '<tr><th style="padding:8px; text-align:center; background-color: #f1f5f9;">D</th><th style="padding:8px; text-align:center; background-color: #f1f5f9;">K</th></tr></thead><tbody>';

                foreach ($response as $acc) {
                    $fontWeight = $acc['isHeader'] ? 'font-weight:bold;' : 'font-weight:normal;';
                    $indent = ($acc['level'] * 15) . 'px';
                    $debit = $acc['debit'] ? 'Rp ' . number_format($acc['debit'], 0, ',', '.') : '';
                    $kredit = $acc['kredit'] ? 'Rp ' . number_format($acc['kredit'], 0, ',', '.') : '';

                    $balColor = $acc['sisa'] < 0 ? '#dc2626' : '#1d4ed8';
                    $balStr = '';
                    if ($acc['sisa'] > 0) $balStr = 'Rp ' . number_format($acc['sisa'], 0, ',', '.');
                    elseif ($acc['sisa'] < 0) $balStr = '(Rp ' . number_format(abs($acc['sisa']), 0, ',', '.') . ')';

                    echo '<tr><td style="padding:8px; ' . $fontWeight . '">' . $acc['code'] . '</td>';
                    echo '<td style="padding:8px; padding-left:' . $indent . '; ' . $fontWeight . '">' . $acc['name'] . '</td>';
                    echo '<td style="padding:8px; text-align:center;">' . $acc['pos'] . '</td>';
                    echo '<td style="padding:8px; text-align:right;">' . $debit . '</td><td style="padding:8px; text-align:right;">' . $kredit . '</td>';
                    echo '<td style="padding:8px; text-align:right; font-weight:bold; color:' . $balColor . ';">' . $balStr . '</td></tr>';
                }

                $isBalanced = (round($grandTotalDebit, 2) === round($grandTotalKredit, 2));
                $status = $isBalanced ? "SEIMBANG (BALANCE)" : "TIDAK SEIMBANG";
                $statusColor = $isBalanced ? "#10b981" : "#ef4444";

                echo '<tr style="background-color: #1e293b; color: white; font-weight: bold;">';
                echo '<td colspan="3" style="padding:8px; text-align:right;">TOTAL NERACA SALDO (MUTASI DETAIL)</td>';
                echo '<td style="padding:8px; text-align:right;">Rp ' . number_format($grandTotalDebit, 0, ',', '.') . '</td>';
                echo '<td style="padding:8px; text-align:right;">Rp ' . number_format($grandTotalKredit, 0, ',', '.') . '</td>';
                echo '<td style="padding:8px; text-align:center; color: ' . $statusColor . ';">' . $status . '</td>';
                echo '</tr></tbody></table></body></html>';
            };
            return response()->stream($callback, 200, $headers);
        }

        // 3. PDF EXPORT (Cetak Layar HTML)
        return view('accounts.export_print', compact('response', 'reportName', 'grandTotalDebit', 'grandTotalKredit'));
    }
}
