<?php

// namespace App\Http\Controllers;

// use App\Models\FixedAsset;
// use App\Models\Journal;
// use App\Models\JournalEntry;
// use App\Models\Account;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Carbon\Carbon;

// class FixedAssetController extends Controller
// {
//     // Menampilkan halaman Aset Tetap
//     public function index()
//     {
//         $assets = FixedAsset::orderBy('purchase_date', 'desc')
//                             ->orderBy('id', 'desc')
//                             ->get();

//         // Ambil data akun Kas/Bank (untuk payment) dan Akun Aktiva Tetap (untuk asset)
//         $banks = Account::where(function($query) {
//             $query->where('code', 'LIKE', '10%')->orWhere('code', 'LIKE', '11%');
//         })->where('is_header', false)->get();

//         $assetAccounts = Account::where('code', 'LIKE', '20%')->where('is_header', false)->get();

//         return view('fixed_assets.index', compact('assets', 'banks', 'assetAccounts'));
//     }

//     // Mencatat Pembelian Aset Tetap Baru
//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'name'            => 'required|string|max:100',
//             'purchase_date'   => 'required|date',
//             'purchase_price'  => 'required|numeric|min:1',
//             'useful_life'     => 'required|integer|min:1',
//             'payment_account' => 'required|exists:accounts,code',
//             'asset_account'   => 'required|exists:accounts,code',
//         ]);

//         DB::beginTransaction();

//         try {
//             $asset = FixedAsset::create([
//                 'name'           => $validated['name'],
//                 'purchase_date'  => $validated['purchase_date'],
//                 'purchase_price' => $validated['purchase_price'],
//                 'useful_life'    => $validated['useful_life'],
//             ]);

//             // Jurnal Pembelian Aset Tetap (Debit: Aset Tetap, Kredit: Kas/Bank)
//             $journalCode = "AST-" . Carbon::now()->format('Ymd') . "-" . $asset->id;

//             $journal = Journal::create([
//                 'date'         => $validated['purchase_date'],
//                 'description'  => "Pembelian Aset Tetap: " . $validated['name'] . " (" . $journalCode . ")",
//                 'reference_no' => $journalCode,
//             ]);

//             JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $validated['asset_account'], 'debit' => $validated['purchase_price'], 'credit' => 0]);
//             JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $validated['payment_account'], 'debit' => 0, 'credit' => $validated['purchase_price']]);

//             DB::commit();
//             return redirect()->route('fixed-assets.index')->with('success', 'Aset berhasil didaftarkan dan dibayar!');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return redirect()->back()->withInput()->with('error', 'Gagal menyimpan aset.');
//         }
//     }

//     // Memproses Penyusutan Aset 1 Bulan
//     public function depreciate($id)
//     {
//         DB::beginTransaction();

//         try {
//             $asset = FixedAsset::findOrFail($id);

//             if ($asset->status != 'Aktif') {
//                 return redirect()->back()->with('error', 'Aset sudah lunas disusutkan atau tidak aktif.');
//             }

//             // Hitung penyusutan per bulan (Garis Lurus)
//             $deprAmount = $asset->purchase_price / $asset->useful_life;

//             // Cek apakah bulan ini akan melunasi aset
//             if ($asset->accum_depreciation + $deprAmount >= $asset->purchase_price) {
//                 $deprAmount = $asset->purchase_price - $asset->accum_depreciation;
//                 $asset->status = 'Lunas Disusutkan';
//             }

//             $asset->accum_depreciation += $deprAmount;
//             $asset->save();

//             // === PERBAIKAN PENCARIAN AKUN (Sesuaikan dengan CoA Excel) ===
//             // Cari Beban Penyusutan (770)
//             $deprExpAcc = Account::where('code', 'LIKE', '77%')->where('is_header', false)->first();
//             // Cari Akumulasi Penyusutan (210)
//             $accumAcc = Account::where('code', 'LIKE', '21%')->where('is_header', false)->first();

//             if (!$deprExpAcc || !$accumAcc) {
//                 DB::rollBack();
//                 return redirect()->back()->with('error', 'Konfigurasi Akun Beban Penyusutan (77) atau Akumulasi (21) belum ada di sistem!');
//             }

//             $journalCode = "DPR-" . Carbon::now()->format('His') . "-" . $asset->id;

//             $journal = Journal::create([
//                 'date'         => Carbon::now(),
//                 'description'  => "Penyusutan 1 Bln: " . $asset->name . " (" . $journalCode . ")",
//                 'reference_no' => $journalCode,
//             ]);

//             JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $deprExpAcc->code, 'debit' => $deprAmount, 'credit' => 0]);
//             JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $accumAcc->code, 'debit' => 0, 'credit' => $deprAmount]);

//             DB::commit();
//             return redirect()->back()->with('success', 'Penyusutan 1 bulan berhasil diproses!');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return redirect()->back()->with('error', 'Gagal memproses penyusutan.');
//         }
//     }
// }

namespace App\Http\Controllers;

use App\Models\FixedAsset;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FixedAssetController extends Controller
{
    // Menampilkan halaman Aset Tetap
    public function index()
    {
        $assets = FixedAsset::orderBy('purchase_date', 'desc')
                            ->orderBy('id', 'desc')
                            ->get();

        // Ambil data akun Kas/Bank (untuk payment) dan Akun Aktiva Tetap (untuk asset)
        $banks = Account::where(function($query) {
            $query->where('code', 'LIKE', '10%')->orWhere('code', 'LIKE', '11%');
        })->where('is_header', false)->get();

        $assetAccounts = Account::where('code', 'LIKE', '20%')->where('is_header', false)->get();

        return view('fixed_assets.index', compact('assets', 'banks', 'assetAccounts'));
    }

    // Mencatat Pembelian Aset Tetap Baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:100',
            'purchase_date'   => 'required|date',
            'purchase_price'  => 'required|numeric|min:1',
            'useful_life'     => 'required|integer|min:1',
            'payment_account' => 'required|exists:accounts,code',
            'asset_account'   => 'required|exists:accounts,code',
        ]);

        DB::beginTransaction();

        try {
            $asset = FixedAsset::create([
                'name'           => $validated['name'],
                'purchase_date'  => $validated['purchase_date'],
                'purchase_price' => $validated['purchase_price'],
                'useful_life'    => $validated['useful_life'],
            ]);

            // Jurnal Pembelian Aset Tetap (Debit: Aset Tetap, Kredit: Kas/Bank)
            $journalCode = "AST-" . Carbon::now()->format('Ymd') . "-" . $asset->id;

            $journal = Journal::create([
                'date'         => $validated['purchase_date'],
                'description'  => "Pembelian Aset Tetap: " . $validated['name'] . " (" . $journalCode . ")",
                'reference_no' => $journalCode,
            ]);

            JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $validated['asset_account'], 'debit' => $validated['purchase_price'], 'credit' => 0]);
            JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $validated['payment_account'], 'debit' => 0, 'credit' => $validated['purchase_price']]);

            DB::commit();
            return redirect()->route('fixed-assets.index')->with('success', 'Aset berhasil didaftarkan dan dibayar!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan aset.');
        }
    }

    // Memproses Penyusutan Aset 1 Bulan (REVISI ALGORITMA)
    public function depreciate($id)
    {
        DB::beginTransaction();

        try {
            $asset = FixedAsset::findOrFail($id);

            if ($asset->status != 'Aktif') {
                return redirect()->back()->with('error', 'Aset sudah lunas disusutkan atau tidak aktif.');
            }

            // Hitung penyusutan per bulan (Garis Lurus)
            $deprAmount = $asset->purchase_price / $asset->useful_life;

            // Cek apakah bulan ini akan melunasi aset
            if ($asset->accum_depreciation + $deprAmount >= $asset->purchase_price) {
                $deprAmount = $asset->purchase_price - $asset->accum_depreciation;
                $asset->status = 'Lunas Disusutkan';
            }

            $asset->accum_depreciation += $deprAmount;
            $asset->save();

            // === PERBAIKAN PENCARIAN AKUN DINAMIS BERDASARKAN KELOMPOK ASET ===

            // 1. Lacak Akun Aset Tetap (20x) yang digunakan saat pertama kali aset ini dibeli
            $assetKeyword = '';
            $buyJournal = Journal::where('reference_no', 'LIKE', 'AST-%-' . $asset->id)->first();

            if ($buyJournal) {
                $buyEntry = JournalEntry::where('journal_id', $buyJournal->id)->where('account_code', 'LIKE', '20%')->first();
                if ($buyEntry && $buyEntry->account) {
                    $assetKeyword = $buyEntry->account->name; // Hasilnya misal: "Kendaraan" atau "Sarana & Instalasi"
                }
            }

            // 2. Cari Akun Akumulasi Penyusutan (21x) yang namanya MENGANDUNG kata kunci aset tersebut
            $accumAcc = null;
            if ($assetKeyword != '') {
                $accumAcc = Account::where('code', 'LIKE', '21%')
                    ->where('is_header', false)
                    ->where('name', 'LIKE', '%' . trim($assetKeyword) . '%')
                    ->first();
            }

            // Fallback (Jaga-jaga jika admin mengetik nama akun CoA tidak standar)
            if (!$accumAcc) {
                $accumAcc = Account::where('code', 'LIKE', '21%')->where('is_header', false)->first();
            }

            // 3. Cari Beban Penyusutan (77x) yang namanya mengandung kata kunci aset tersebut
            $deprExpAcc = Account::where('code', 'LIKE', '77%')
                    ->where('is_header', false)
                    ->where('name', 'LIKE', '%' . trim($assetKeyword) . '%')
                    ->first();

            // Fallback (Biasanya akun beban penyusutan di ERP digabung menjadi 1 akun global 770)
            if (!$deprExpAcc) {
                $deprExpAcc = Account::where('code', 'LIKE', '77%')->where('is_header', false)->first();
            }

            if (!$deprExpAcc || !$accumAcc) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Konfigurasi Akun Beban Penyusutan (77x) atau Akumulasi (21x) tidak cocok atau belum ada di sistem!');
            }

            // Pembuatan Jurnal Penyusutan Bulanan
            $journalCode = "DPR-" . Carbon::now()->format('His') . "-" . $asset->id;

            $journal = Journal::create([
                'date'         => Carbon::now(),
                'description'  => "Penyusutan 1 Bln: " . $asset->name . " (" . $journalCode . ")",
                'reference_no' => $journalCode,
            ]);

            JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $deprExpAcc->code, 'debit' => $deprAmount, 'credit' => 0]);
            JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $accumAcc->code, 'debit' => 0, 'credit' => $deprAmount]);

            DB::commit();
            return redirect()->back()->with('success', 'Penyusutan 1 bulan berhasil diproses secara otomatis ke akun ' . $accumAcc->name . ' (' . $accumAcc->code . ')!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses penyusutan.');
        }
    }
    
    // --- FITUR EKSPOR EXCEL & PDF ---
    public function export(Request $request)
    {
        $type = $request->query('type', 'internal');
        $format = $request->query('format', 'csv');

        // Tarik data aset yang sama dengan index
        $assets = FixedAsset::orderBy('purchase_date', 'desc')->orderBy('id', 'desc')->get();

        $reportName = $type === 'pajak' ? 'DAFTAR ASET TETAP & PENYUSUTAN (PAJAK)' : 'DAFTAR ASET TETAP & PENYUSUTAN (INTERNAL)';

        // EXCEL EXPORT (HTML-TO-EXCEL SPOOFING)
        if ($format === 'csv') {
            $headers = [
                "Content-type"        => "application/vnd.ms-excel",
                "Content-Disposition" => "attachment; filename=AsetTetap_{$type}.xls",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function() use ($assets, $reportName) {
                echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>';

                echo '<table border="0" style="font-family: Arial, sans-serif;">';
                echo '<tr><th colspan="7" style="font-size:16px; font-weight:bold; text-align:center;">PT. BERLIAN MANDIRI PERKASA</th></tr>';
                echo '<tr><th colspan="7" style="font-size:14px; font-weight:bold; text-align:center;">' . $reportName . '</th></tr>';
                echo '<tr><td colspan="7"></td></tr></table>';

                echo '<table border="1" style="font-family: Arial, sans-serif; font-size: 12px; border-collapse: collapse;">';
                echo '<thead style="background-color: #e2e8f0;">';
                echo '<tr>';
                echo '<th style="padding:8px; text-align:center; width:50px;">No.</th>';
                echo '<th style="padding:8px; text-align:center; width:250px;">Nama Aset / Inventaris</th>';
                echo '<th style="padding:8px; text-align:center; width:120px;">Tgl Beli</th>';
                echo '<th style="padding:8px; text-align:center; width:100px;">Umur (Bln)</th>';
                echo '<th style="padding:8px; text-align:center; width:150px;">Harga Beli Awal</th>';
                echo '<th style="padding:8px; text-align:center; width:150px;">Akumulasi Susut</th>';
                echo '<th style="padding:8px; text-align:center; width:150px;">Nilai Buku (Sisa)</th>';
                echo '</tr></thead><tbody>';

                $no = 1;
                $totalBeli = 0; $totalSusut = 0; $totalBuku = 0;

                foreach ($assets as $ast) {
                    $bookValue = $ast->purchase_price - $ast->accum_depreciation;
                    $totalBeli += $ast->purchase_price;
                    $totalSusut += $ast->accum_depreciation;
                    $totalBuku += $bookValue;

                    echo '<tr>';
                    echo '<td style="padding:8px; text-align:center;">' . $no++ . '</td>';
                    echo '<td style="padding:8px; font-weight:bold;">' . $ast->name . '</td>';
                    echo '<td style="padding:8px; text-align:center;">' . \Carbon\Carbon::parse($ast->purchase_date)->format('d/m/Y') . '</td>';
                    echo '<td style="padding:8px; text-align:center;">' . $ast->useful_life . '</td>';
                    echo '<td style="padding:8px; text-align:right;">Rp ' . number_format($ast->purchase_price, 0, ',', '.') . '</td>';
                    echo '<td style="padding:8px; text-align:right; color: #dc2626;">Rp ' . number_format($ast->accum_depreciation, 0, ',', '.') . '</td>';
                    echo '<td style="padding:8px; text-align:right; font-weight:bold; color: #047857;">Rp ' . number_format($bookValue, 0, ',', '.') . '</td>';
                    echo '</tr>';
                }

                echo '<tr style="background-color: #f8fafc; font-weight: bold;">';
                echo '<td colspan="4" style="padding:8px; text-align:right;">TOTAL KESELURUHAN</td>';
                echo '<td style="padding:8px; text-align:right;">Rp ' . number_format($totalBeli, 0, ',', '.') . '</td>';
                echo '<td style="padding:8px; text-align:right; color: #dc2626;">Rp ' . number_format($totalSusut, 0, ',', '.') . '</td>';
                echo '<td style="padding:8px; text-align:right; color: #047857;">Rp ' . number_format($totalBuku, 0, ',', '.') . '</td>';
                echo '</tr>';

                echo '</tbody></table></body></html>';
            };
            return response()->stream($callback, 200, $headers);
        }

        // PDF EXPORT (Cetak Layar HTML)
        return view('fixed_assets.export_print', compact('assets', 'reportName'));
    }
}
