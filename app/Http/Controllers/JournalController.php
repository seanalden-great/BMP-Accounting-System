<?php

// namespace App\Http\Controllers;

// use App\Models\Journal;
// use App\Models\JournalEntry;
// use App\Models\Account;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Carbon\Carbon;

// class JournalController extends Controller
// {
//     // Menampilkan halaman Mutasi Jurnal / Buku Besar
//     public function index(Request $request)
//     {
//         // Ambil daftar akun bank/kas (Awalan 10 atau 11) untuk filter dropdown
//         $banks = Account::where(function($query) {
//             $query->where('code', 'LIKE', '10%')
//                   ->orWhere('code', 'LIKE', '11%');
//         })->where('is_header', false)->get();

//         $journals = collect(); // Koleksi kosong

//         $accountCode = $request->query('account_code');

//         if ($accountCode) {
//             // Cari ID Jurnal yang memiliki mutasi di akun yang dipilih
//             $journalIDs = JournalEntry::where('account_code', $accountCode)
//                 ->pluck('journal_id')
//                 ->unique();

//             if ($journalIDs->isNotEmpty()) {
//                 // Tarik Induk Jurnal dan Detail Barisnya
//                 $journals = Journal::with(['entries.account'])
//                     ->whereIn('id', $journalIDs)
//                     ->orderBy('date', 'desc')
//                     ->orderBy('id', 'desc')
//                     ->get();
//             }
//         }

//         // Return tampilan (bisa digunakan untuk halaman Kas & Bank)
//         return view('journals.index', compact('banks', 'journals', 'accountCode'));
//     }

//     // Menyimpan Jurnal Manual Baru
//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'date'           => 'required|date',
//             'description'    => 'required|string',
//             'reference_no'   => 'nullable|string',
//             'entries'        => 'required|array|min:2',
//             'entries.*.account_code' => 'required|exists:accounts,code',
//             'entries.*.debit'        => 'nullable|numeric|min:0',
//             'entries.*.credit'       => 'nullable|numeric|min:0',
//         ]);

//         // Validasi Akuntansi: Debit == Kredit
//         $totalDebit = 0;
//         $totalCredit = 0;
//         foreach ($validated['entries'] as $entry) {
//             $totalDebit += $entry['debit'] ?? 0;
//             $totalCredit += $entry['credit'] ?? 0;
//         }

//         if ($totalDebit != $totalCredit) {
//             return redirect()->back()
//                 ->withInput()
//                 ->with('error', 'Gagal: Total Debit harus sama dengan Total Kredit!');
//         }

//         DB::beginTransaction();

//         try {
//             // Insert Header Jurnal
//             $journal = Journal::create([
//                 'date'         => $validated['date'],
//                 'description'  => $validated['description'],
//                 'reference_no' => $validated['reference_no'],
//             ]);

//             // Insert Detail Entries
//             foreach ($validated['entries'] as $entry) {
//                 // Jangan insert baris jika debit dan kredit sama-sama 0 (kosong)
//                 if (($entry['debit'] ?? 0) > 0 || ($entry['credit'] ?? 0) > 0) {
//                     JournalEntry::create([
//                         'journal_id'   => $journal->id,
//                         'account_code' => $entry['account_code'],
//                         'debit'        => $entry['debit'] ?? 0,
//                         'credit'       => $entry['credit'] ?? 0,
//                     ]);
//                 }
//             }

//             DB::commit();
//             return redirect()->back()->with('success', 'Jurnal Manual berhasil disimpan.');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return redirect()->back()->withInput()->with('error', 'Gagal menyimpan transaksi jurnal.');
//         }
//     }
// }

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JournalController extends Controller
{
    // Menampilkan halaman Mutasi Jurnal / Buku Besar dengan Fitur Filter Tanggal
    public function index(Request $request)
    {
        $banks = Account::where(function($query) {
            $query->where('code', 'LIKE', '10%')
                  ->orWhere('code', 'LIKE', '11%');
        })->where('is_header', false)->get();

        $journals = collect();

        $accountCode = $request->query('account_code');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $saldoAwal = 0;

        if ($accountCode) {
            // Cari ID Jurnal yang memiliki mutasi di akun yang dipilih
            $journalIDs = JournalEntry::where('account_code', $accountCode)
                ->pluck('journal_id')
                ->unique();

            if ($journalIDs->isNotEmpty()) {
                $query = Journal::with(['entries.account'])
                    ->whereIn('id', $journalIDs);

                // Jika Filter Tanggal Awal diisi
                if ($startDate) {
                    $query->whereDate('date', '>=', $startDate);

                    // Kalkulasi SALDO AWAL (Total mutasi sebelum start_date) agar running balance tetap akurat
                    $openingDebits = JournalEntry::where('account_code', $accountCode)
                        ->whereHas('journal', function($q) use ($startDate) {
                            $q->whereDate('date', '<', $startDate);
                        })->sum('debit');

                    $openingCredits = JournalEntry::where('account_code', $accountCode)
                        ->whereHas('journal', function($q) use ($startDate) {
                            $q->whereDate('date', '<', $startDate);
                        })->sum('credit');

                    $saldoAwal = $openingDebits - $openingCredits;
                }

                // Jika Filter Tanggal Akhir diisi
                if ($endDate) {
                    $query->whereDate('date', '<=', $endDate);
                }

                // Tarik Induk Jurnal yang sudah terfilter
                // $journals = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->get();
                $journals = $query->orderBy('date', 'asc')->orderBy('id', 'asc')->get();
            }
        }

        return view('journals.index', compact('banks', 'journals', 'accountCode', 'startDate', 'endDate', 'saldoAwal'));
    }

    // Menyimpan Jurnal Manual Baru (Biarkan fungsi store Anda ini utuh seperti sebelumnya)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date'           => 'required|date',
            'description'    => 'required|string',
            'reference_no'   => 'nullable|string',
            'entries'        => 'required|array|min:2',
            'entries.*.account_code' => 'required|exists:accounts,code',
            'entries.*.debit'        => 'nullable|numeric|min:0',
            'entries.*.credit'       => 'nullable|numeric|min:0',
        ]);

        $totalDebit = 0;
        $totalCredit = 0;
        foreach ($validated['entries'] as $entry) {
            $totalDebit += $entry['debit'] ?? 0;
            $totalCredit += $entry['credit'] ?? 0;
        }

        if ($totalDebit != $totalCredit) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal: Total Debit harus sama dengan Total Kredit!');
        }

        DB::beginTransaction();

        try {
            $journal = Journal::create([
                'date'         => $validated['date'],
                'description'  => $validated['description'],
                'reference_no' => $validated['reference_no'],
            ]);

            foreach ($validated['entries'] as $entry) {
                if (($entry['debit'] ?? 0) > 0 || ($entry['credit'] ?? 0) > 0) {
                    JournalEntry::create([
                        'journal_id'   => $journal->id,
                        'account_code' => $entry['account_code'],
                        'debit'        => $entry['debit'] ?? 0,
                        'credit'       => $entry['credit'] ?? 0,
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Jurnal Manual berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan transaksi jurnal.');
        }
    }

    // --- FITUR EKSPOR EXCEL & PDF ---
    // public function export(Request $request)
    // {
    //     $accountCode = $request->query('account_code');
    //     $startDate = $request->query('start_date');
    //     $endDate = $request->query('end_date');
    //     $type = $request->query('type', 'internal'); // 'internal' atau 'pajak'
    //     $format = $request->query('format', 'csv'); // 'csv' atau 'pdf'

    //     if (!$accountCode) {
    //         return redirect()->back()->with('error', 'Pilih rekening bank terlebih dahulu.');
    //     }

    //     $bank = Account::where('code', $accountCode)->first();
    //     $saldoAwal = 0;
    //     $journalIDs = JournalEntry::where('account_code', $accountCode)->pluck('journal_id')->unique();
    //     $journals = collect();

    //     if ($journalIDs->isNotEmpty()) {
    //         $query = Journal::with(['entries.account'])->whereIn('id', $journalIDs);
    //         if ($startDate) {
    //             $query->whereDate('date', '>=', $startDate);
    //             $saldoAwal = JournalEntry::where('account_code', $accountCode)
    //                 ->whereHas('journal', function($q) use ($startDate) { $q->whereDate('date', '<', $startDate); })
    //                 ->selectRaw('SUM(debit) - SUM(credit) as balance')
    //                 ->value('balance') ?? 0;
    //         }
    //         if ($endDate) {
    //             $query->whereDate('date', '<=', $endDate);
    //         }
    //         $journals = $query->orderBy('date', 'asc')->orderBy('id', 'asc')->get();
    //     }

    //     // Judul laporan dibedakan sesuai tipe yang di-klik
    //     $reportName = $type === 'pajak' ? 'LAPORAN MUTASI BANK (PAJAK)' : 'LAPORAN MUTASI BANK (INTERNAL)';

    //     // FORMAT 1: CSV / EXCEL
    //     // if ($format === 'csv') {
    //     //     $headers = [
    //     //         "Content-type"        => "text/csv",
    //     //         "Content-Disposition" => "attachment; filename=Mutasi_{$type}_{$bank->name}.csv",
    //     //         "Pragma"              => "no-cache",
    //     //         "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
    //     //         "Expires"             => "0"
    //     //     ];

    //     //     $callback = function() use ($journals, $bank, $saldoAwal, $reportName, $startDate, $endDate) {
    //     //         $file = fopen('php://output', 'w');
    //     //         fputcsv($file, ['PT. BERLIAN MANDIRI PERKASA']);
    //     //         fputcsv($file, [$reportName]);
    //     //         fputcsv($file, ['Rekening:', $bank->name . ' (' . $bank->code . ')']);
    //     //         fputcsv($file, ['Periode:', ($startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Awal') . ' s/d ' . ($endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Akhir')]);
    //     //         fputcsv($file, []);
    //     //         fputcsv($file, ['No', 'Tanggal', 'Uraian', 'Akun Lawan', 'Keterangan', 'Debet', 'Kredit', 'Saldo']);

    //     //         $runningBalance = $saldoAwal;
    //     //         $row = 1;

    //     //         if ($startDate) {
    //     //             fputcsv($file, ['-', $startDate, 'SALDO AWAL', '', '', '', '', $runningBalance]);
    //     //         }

    //     //         foreach ($journals as $journal) {
    //     //             foreach ($journal->entries as $entry) {
    //     //                 if ($entry->account_code == $bank->code) {
    //     //                     $runningBalance += ($entry->debit - $entry->credit);

    //     //                     $lawan = $journal->entries->where('account_code', '!=', $bank->code)->first();
    //     //                     $lawanName = $lawan ? $lawan->account->name : 'Multiple';
    //     //                     $lawanCode = $lawan ? $lawan->account_code : '';

    //     //                     fputcsv($file, [
    //     //                         $row++,
    //     //                         $journal->date,
    //     //                         $journal->description . ($journal->reference_no ? ' - '.$journal->reference_no : ''),
    //     //                         $lawanCode,
    //     //                         $lawanName,
    //     //                         $entry->debit,
    //     //                         $entry->credit,
    //     //                         $runningBalance
    //     //                     ]);
    //     //                 }
    //     //             }
    //     //         }
    //     //         fclose($file);
    //     //     };
    //     //     return response()->stream($callback, 200, $headers);
    //     // }

    //     // FORMAT 1: CSV / EXCEL
    //     // if ($format === 'csv') {
    //     //     $headers = [
    //     //         "Content-type"        => "text/csv; charset=UTF-8", // Pastikan encoding UTF-8
    //     //         "Content-Disposition" => "attachment; filename=Mutasi_{$type}_{$bank->name}.csv",
    //     //         "Pragma"              => "no-cache",
    //     //         "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
    //     //         "Expires"             => "0"
    //     //     ];

    //     //     $callback = function() use ($journals, $bank, $saldoAwal, $reportName, $startDate, $endDate) {
    //     //         $file = fopen('php://output', 'w');

    //     //         // Tambahkan BOM (Byte Order Mark) agar Excel otomatis mendeteksi format secara rapi
    //     //         fputs($file, "\xEF\xBB\xBF");

    //     //         // KUNCI PERBAIKAN: Gunakan pemisah Titik Koma (;) khusus untuk Excel region Indonesia
    //     //         $delimiter = ';';

    //     //         // Tulis Header Info
    //     //         fputcsv($file, ['PT. BERLIAN MANDIRI PERKASA'], $delimiter);
    //     //         fputcsv($file, [$reportName], $delimiter);
    //     //         fputcsv($file, ['Rekening:', $bank->name . ' (' . $bank->code . ')'], $delimiter);

    //     //         $tglAwal = $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Awal';
    //     //         $tglAkhir = $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Akhir';
    //     //         fputcsv($file, ['Periode:', $tglAwal . ' s/d ' . $tglAkhir], $delimiter);

    //     //         fputcsv($file, [], $delimiter); // Baris kosong pemisah

    //     //         // Tulis Header Tabel (SUSUNAN PERSIS SEPERTI DI WEB)
    //     //         fputcsv($file, [
    //     //             'No.',
    //     //             'Tanggal',
    //     //             'Uraian / Keterangan',
    //     //             'No. AKUN',
    //     //             'DEBET',
    //     //             'KREDIT',
    //     //             'SISA SALDO',
    //     //             '', // Celah kolom kosong pemisah seperti di web
    //     //             'KETERANGAN / LAWAN'
    //     //         ], $delimiter);

    //     //         $runningBalance = $saldoAwal;
    //     //         $rowNumber = 1;

    //     //         // Tulis Baris Saldo Awal (Jika Ada)
    //     //         if ($startDate) {
    //     //             fputcsv($file, [
    //     //                 '-',
    //     //                 \Carbon\Carbon::parse($startDate)->format('d-M-Y'),
    //     //                 'SALDO AWAL (Pindahan)',
    //     //                 '',
    //     //                 '',
    //     //                 '',
    //     //                 $runningBalance, // Angka asli agar bisa di-sum oleh Excel
    //     //                 '',
    //     //                 'Akumulasi historis'
    //     //             ], $delimiter);
    //     //         }

    //     //         // Tulis Rincian Baris Transaksi
    //     //         foreach ($journals as $journal) {
    //     //             foreach ($journal->entries as $entry) {
    //     //                 if ($entry->account_code == $bank->code) {
    //     //                     $runningBalance += ($entry->debit - $entry->credit);

    //     //                     $lawan = $journal->entries->where('account_code', '!=', $bank->code)->first();
    //     //                     $lawanName = $lawan ? $lawan->account->name : 'Tanpa Keterangan';

    //     //                     $desc = $journal->description;
    //     //                     if ($journal->reference_no) {
    //     //                         $desc .= ' - ' . $journal->reference_no;
    //     //                     }

    //     //                     fputcsv($file, [
    //     //                         $rowNumber++,
    //     //                         \Carbon\Carbon::parse($journal->date)->format('d-M-Y'),
    //     //                         $desc,
    //     //                         $entry->account_code,
    //     //                         $entry->debit > 0 ? $entry->debit : '',   // Biarkan berupa angka mentah
    //     //                         $entry->credit > 0 ? $entry->credit : '', // tanpa format rupiah agar bisa
    //     //                         $runningBalance,                          // dijumlahkan oleh rumus SUM Excel
    //     //                         '', // Celah kolom kosong
    //     //                         $lawanName
    //     //                     ], $delimiter);
    //     //                 }
    //     //             }
    //     //         }
    //     //         fclose($file);
    //     //     };

    //     //     return response()->stream($callback, 200, $headers);
    //     // }

    //     // FORMAT 1: CSV / EXCEL (MENGGUNAKAN TRIK HTML-TO-EXCEL AGAR TAMPILAN 100% PERSIS WEB)
    //     if ($format === 'csv') {
    //         // Kita ubah extension menjadi .xls agar Excel memprioritaskannya
    //         $headers = [
    //             "Content-type"        => "application/vnd.ms-excel",
    //             "Content-Disposition" => "attachment; filename=Mutasi_{$type}_{$bank->name}.xls",
    //             "Pragma"              => "no-cache",
    //             "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
    //             "Expires"             => "0"
    //         ];

    //         $callback = function() use ($journals, $bank, $saldoAwal, $reportName, $startDate, $endDate) {
    //             // Tuliskan Format HTML lengkap dengan Inline CSS
    //             echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
    //             echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>';
    //             echo '<body>';

    //             // --- BAGIAN 1: HEADER INFORMASI PERUSAHAAN ---
    //             echo '<table border="0" style="font-family: Arial, sans-serif;">';
    //             echo '<tr><th colspan="9" style="font-size:16px; font-weight:bold; text-align:center;">PT. BERLIAN MANDIRI PERKASA</th></tr>';
    //             echo '<tr><th colspan="9" style="font-size:14px; font-weight:bold; text-align:center;">' . $reportName . '</th></tr>';
    //             echo '<tr><td colspan="9"></td></tr>'; // Jeda Kosong

    //             echo '<tr><td colspan="2"><strong>Buku Besar Akun:</strong></td><td colspan="7">' . $bank->name . ' (' . $bank->code . ')</td></tr>';
    //             $tglAwal = $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Awal';
    //             $tglAkhir = $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Akhir';
    //             echo '<tr><td colspan="2"><strong>Periode:</strong></td><td colspan="7">' . $tglAwal . ' s/d ' . $tglAkhir . '</td></tr>';
    //             echo '<tr><td colspan="9"></td></tr>'; // Jeda Kosong
    //             echo '</table>';

    //             // --- BAGIAN 2: HEADER TABEL (Desain Rapi Persis Web) ---
    //             echo '<table border="1" style="font-family: Arial, sans-serif; font-size: 12px; border-collapse: collapse;">';
    //             echo '<thead style="background-color: #f1f5f9;">'; // bg-slate-100
    //             echo '<tr>';
    //             echo '<th style="padding:8px; text-align:center;">No.</th>';
    //             echo '<th style="padding:8px; text-align:center;">Tanggal</th>';
    //             echo '<th style="padding:8px; text-align:center;">Uraian / Keterangan</th>';
    //             echo '<th style="padding:8px; text-align:center;">No. AKUN</th>';
    //             echo '<th style="padding:8px; text-align:center;">DEBET</th>';
    //             echo '<th style="padding:8px; text-align:center;">KREDIT</th>';
    //             echo '<th style="padding:8px; text-align:center;">SISA SALDO</th>';
    //             echo '<th style="padding:8px; background-color: #ffffff; border:none;"></th>'; // Kolom Spasi Pemisah
    //             echo '<th style="padding:8px; text-align:center;">KETERANGAN / LAWAN</th>';
    //             echo '</tr>';
    //             echo '</thead>';
    //             echo '<tbody>';

    //             $runningBalance = $saldoAwal;
    //             $rowNumber = 1;

    //             // --- BAGIAN 3: BARIS SALDO AWAL (Jika Ada Filter) ---
    //             if ($startDate) {
    //                 echo '<tr style="background-color: #eff6ff;">'; // bg-blue-50
    //                 echo '<td style="padding:8px; text-align:center; color: #94a3b8;">-</td>';
    //                 echo '<td style="padding:8px; text-align:center; color: #475569;">' . \Carbon\Carbon::parse($startDate)->format('d-M-Y') . '</td>';
    //                 echo '<td style="padding:8px; font-weight:bold; color: #1e40af;">SALDO AWAL (Pindahan)</td>';
    //                 echo '<td></td><td></td><td></td>';

    //                 // Logika Warna: Merah jika minus (Kredit), Biru jika positif (Debit)
    //                 $color = $runningBalance < 0 ? '#dc2626' : '#1d4ed8';
    //                 $salStr = $runningBalance < 0 ? '(Rp ' . number_format(abs($runningBalance), 0, ',', '.') . ')' : 'Rp ' . number_format($runningBalance, 0, ',', '.');
    //                 echo '<td style="padding:8px; text-align:right; font-weight:bold; color: ' . $color . ';">' . $salStr . '</td>';
    //                 echo '<td style="background-color: #ffffff; border:none;"></td>'; // Spasi
    //                 echo '<td style="padding:8px; text-align:center; color: #64748b; font-style: italic;">Akumulasi historis</td>';
    //                 echo '</tr>';
    //             }

    //             // --- BAGIAN 4: BARIS PERULANGAN MUTASI (Jurnal) ---
    //             foreach ($journals as $journal) {
    //                 foreach ($journal->entries as $entry) {
    //                     if ($entry->account_code == $bank->code) {
    //                         $runningBalance += ($entry->debit - $entry->credit);

    //                         $lawan = $journal->entries->where('account_code', '!=', $bank->code)->first();
    //                         $lawanName = $lawan ? $lawan->account->name : 'Tanpa Keterangan';

    //                         $desc = $journal->description;
    //                         if ($journal->reference_no) {
    //                             $desc .= ' - ' . $journal->reference_no;
    //                         }

    //                         $debit = $entry->debit > 0 ? 'Rp ' . number_format($entry->debit, 0, ',', '.') : '';
    //                         $credit = $entry->credit > 0 ? 'Rp ' . number_format($entry->credit, 0, ',', '.') : '';

    //                         $balColor = $runningBalance < 0 ? '#dc2626' : '#2563eb';
    //                         $balStr = $runningBalance < 0 ? '(Rp ' . number_format(abs($runningBalance), 0, ',', '.') . ')' : 'Rp ' . number_format($runningBalance, 0, ',', '.');

    //                         echo '<tr>';
    //                         echo '<td style="padding:8px; text-align:center;">' . $rowNumber++ . '</td>';
    //                         echo '<td style="padding:8px; text-align:center;">' . \Carbon\Carbon::parse($journal->date)->format('d-M-Y') . '</td>';
    //                         echo '<td style="padding:8px;">' . $desc . '</td>';
    //                         echo '<td style="padding:8px; text-align:center; font-weight:bold;">' . $entry->account_code . '</td>';
    //                         echo '<td style="padding:8px; text-align:right; font-weight:500;">' . $debit . '</td>';
    //                         echo '<td style="padding:8px; text-align:right; font-weight:500;">' . $credit . '</td>';
    //                         echo '<td style="padding:8px; text-align:right; font-weight:bold; color: ' . $balColor . ';">' . $balStr . '</td>';
    //                         echo '<td style="background-color: #ffffff; border:none;"></td>'; // Spasi
    //                         echo '<td style="padding:8px; text-align:center; color: #475569; font-weight:500;">' . $lawanName . '</td>';
    //                         echo '</tr>';
    //                     }
    //                 }
    //             }

    //             // Tutup dokumen HTML
    //             echo '</tbody></table></body></html>';
    //         };

    //         return response()->stream($callback, 200, $headers);
    //     }

    //     // FORMAT 2: CETAK DOKUMEN HTML / PDF
    //     return view('journals.export_print', compact('journals', 'bank', 'saldoAwal', 'reportName', 'startDate', 'endDate'));
    // }

    // --- FITUR EKSPOR EXCEL & PDF ---
    public function export(Request $request)
    {
        $accountCode = $request->query('account_code');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $type = $request->query('type', 'internal');
        $format = $request->query('format', 'csv');

        if (!$accountCode) {
            return redirect()->back()->with('error', 'Pilih rekening bank terlebih dahulu.');
        }

        $bank = Account::where('code', $accountCode)->first();
        $saldoAwal = 0;
        $journalIDs = JournalEntry::where('account_code', $accountCode)->pluck('journal_id')->unique();
        $journals = collect();

        if ($journalIDs->isNotEmpty()) {
            $query = Journal::with(['entries.account'])->whereIn('id', $journalIDs);
            if ($startDate) {
                $query->whereDate('date', '>=', $startDate);
                $saldoAwal = JournalEntry::where('account_code', $accountCode)
                    ->whereHas('journal', function($q) use ($startDate) { $q->whereDate('date', '<', $startDate); })
                    ->selectRaw('SUM(debit) - SUM(credit) as balance')
                    ->value('balance') ?? 0;
            }
            if ($endDate) {
                $query->whereDate('date', '<=', $endDate);
            }
            $journals = $query->orderBy('date', 'asc')->orderBy('id', 'asc')->get();
        }

        $reportName = $type === 'pajak' ? 'LAPORAN MUTASI BANK (PAJAK)' : 'LAPORAN MUTASI BANK (INTERNAL)';

        // FORMAT 1: EXCEL (HTML-TO-EXCEL SPOOFING) SAMA PERSIS DENGAN WEB
        if ($format === 'csv') {
            $headers = [
                "Content-type"        => "application/vnd.ms-excel",
                "Content-Disposition" => "attachment; filename=Mutasi_{$type}_{$bank->name}.xls",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function() use ($journals, $bank, $saldoAwal, $reportName, $startDate, $endDate) {
                echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
                echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>';
                echo '<body>';

                echo '<table border="0" style="font-family: Arial, sans-serif;">';
                echo '<tr><th colspan="9" style="font-size:16px; font-weight:bold; text-align:center;">PT. BERLIAN MANDIRI PERKASA</th></tr>';
                echo '<tr><th colspan="9" style="font-size:14px; font-weight:bold; text-align:center;">' . $reportName . '</th></tr>';
                echo '<tr><td colspan="9"></td></tr>';
                echo '<tr><td colspan="2"><strong>Buku Besar Akun:</strong></td><td colspan="7">' . $bank->name . ' (' . $bank->code . ')</td></tr>';
                $tglAwal = $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Awal';
                $tglAkhir = $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Akhir';
                echo '<tr><td colspan="2"><strong>Periode:</strong></td><td colspan="7">' . $tglAwal . ' s/d ' . $tglAkhir . '</td></tr>';
                echo '<tr><td colspan="9"></td></tr>';
                echo '</table>';

                echo '<table border="1" style="font-family: Arial, sans-serif; font-size: 12px; border-collapse: collapse;">';
                echo '<thead style="background-color: #f1f5f9;">';
                echo '<tr>';
                echo '<th style="padding:8px; text-align:center;">No.</th>';
                echo '<th style="padding:8px; text-align:center;">Tanggal</th>';
                echo '<th style="padding:8px; text-align:center;">Uraian / Keterangan</th>';
                echo '<th style="padding:8px; text-align:center;">No. AKUN</th>';
                echo '<th style="padding:8px; text-align:center;">DEBET</th>';
                echo '<th style="padding:8px; text-align:center;">KREDIT</th>';
                echo '<th style="padding:8px; text-align:center;">SISA SALDO</th>';
                echo '<th style="padding:8px; background-color: #ffffff; border:none;"></th>';
                echo '<th style="padding:8px; text-align:center;">KETERANGAN / LAWAN</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                $runningBalance = $saldoAwal;
                $rowNumber = 1;

                if ($startDate) {
                    echo '<tr style="background-color: #eff6ff;">';
                    echo '<td style="padding:8px; text-align:center; color: #94a3b8;">-</td>';
                    echo '<td style="padding:8px; text-align:center; color: #475569;">' . \Carbon\Carbon::parse($startDate)->format('d-M-Y') . '</td>';
                    echo '<td style="padding:8px; font-weight:bold; color: #1e40af;">SALDO AWAL (Pindahan)</td>';
                    echo '<td></td><td></td><td></td>';

                    $color = $runningBalance < 0 ? '#dc2626' : '#1d4ed8';
                    $salStr = $runningBalance < 0 ? '(Rp ' . number_format(abs($runningBalance), 0, ',', '.') . ')' : 'Rp ' . number_format($runningBalance, 0, ',', '.');
                    echo '<td style="padding:8px; text-align:right; font-weight:bold; color: ' . $color . ';">' . $salStr . '</td>';
                    echo '<td style="background-color: #ffffff; border:none;"></td>';
                    echo '<td style="padding:8px; text-align:center; color: #64748b; font-style: italic;">Akumulasi historis</td>';
                    echo '</tr>';
                }

                // PERBAIKAN: LOOPING SEMUA BARIS (DOUBLE ENTRY) AGAR PERSIS SEPERTI WEB
                foreach ($journals as $journal) {
                    foreach ($journal->entries as $entry) {
                        $isBankRow = ($entry->account_code == $bank->code);

                        if ($isBankRow) {
                            $runningBalance += ($entry->debit - $entry->credit);
                        }

                        // $desc = $journal->description;
                        // if ($journal->reference_no) { $desc .= ' - ' . $journal->reference_no; }

                        $desc = $journal->description;
                        // Logika Cerdas: Tambahkan reference_no ke desc HANYA JIKA belum tertulis di dalamnya
                        if ($journal->reference_no && strpos($journal->description, $journal->reference_no) === false) {
                            $desc .= ' - ' . $journal->reference_no;
                        }

                        $debit = $entry->debit > 0 ? 'Rp ' . number_format($entry->debit, 0, ',', '.') : '';
                        $credit = $entry->credit > 0 ? 'Rp ' . number_format($entry->credit, 0, ',', '.') : '';

                        $balColor = $runningBalance < 0 ? '#dc2626' : '#2563eb';
                        $balStr = '';
                        if ($isBankRow) {
                            $balStr = $runningBalance < 0 ? '(Rp ' . number_format(abs($runningBalance), 0, ',', '.') . ')' : 'Rp ' . number_format($runningBalance, 0, ',', '.');
                        }

                        $lawanName = $entry->account ? $entry->account->name : 'Tanpa Keterangan';

                        echo '<tr>';
                        echo '<td style="padding:8px; text-align:center;">' . $rowNumber++ . '</td>';
                        echo '<td style="padding:8px; text-align:center;">' . \Carbon\Carbon::parse($journal->date)->format('d-M-Y') . '</td>';
                        echo '<td style="padding:8px;">' . $desc . '</td>';
                        echo '<td style="padding:8px; text-align:center; font-weight:bold;">' . $entry->account_code . '</td>';
                        echo '<td style="padding:8px; text-align:right; font-weight:500;">' . $debit . '</td>';
                        echo '<td style="padding:8px; text-align:right; font-weight:500;">' . $credit . '</td>';
                        echo '<td style="padding:8px; text-align:right; font-weight:bold; color: ' . $balColor . ';">' . $balStr . '</td>';
                        echo '<td style="background-color: #ffffff; border:none;"></td>';
                        echo '<td style="padding:8px; text-align:center; color: #475569; font-weight:500;">' . $lawanName . '</td>';
                        echo '</tr>';
                    }
                }
                echo '</tbody></table></body></html>';
            };

            return response()->stream($callback, 200, $headers);
        }

        // FORMAT 2: CETAK DOKUMEN HTML / PDF
        return view('journals.export_print', compact('journals', 'bank', 'saldoAwal', 'reportName', 'startDate', 'endDate'));
    }

    // ==========================================================
    // FITUR EMAS: REKONSILIASI BANK (Pencocokan Rekening Koran)
    // ==========================================================
    public function reconciliation(Request $request)
    {
        // Ambil daftar Rekening Kas & Bank (awalan 10 atau 11)
        $banks = Account::where(function($query) {
            $query->where('code', 'LIKE', '10%')->orWhere('code', 'LIKE', '11%');
        })->where('is_header', false)->get();

        $selectedBank = $request->query('account_code');
        $selectedMonth = $request->query('month', date('Y-m'));

        $entries = collect();
        $systemBalance = 0;
        $beginningReconciled = 0;

        if ($selectedBank) {
            $year = substr($selectedMonth, 0, 4);
            $month = substr($selectedMonth, 5, 2);

            $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfDay();
            $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth();

            // 1. Ambil transaksi khusus di bulan yang dipilih
            $entries = JournalEntry::with('journal')
                ->where('account_code', $selectedBank)
                ->whereHas('journal', function($q) use ($year, $month) {
                    $q->whereYear('date', $year)->whereMonth('date', $month);
                })
                ->get();

            // 2. Hitung total saldo sistem (seharusnya) sampai akhir bulan pilihan
            $systemBalance = JournalEntry::where('account_code', $selectedBank)
                ->whereHas('journal', function($q) use ($endOfMonth) {
                    $q->whereDate('date', '<=', $endOfMonth);
                })
                ->selectRaw('SUM(debit) - SUM(credit) as balance')
                ->value('balance') ?? 0;

            // 3. Hitung saldo yang sudah COCOK (reconciled) HINGGA BULAN LALU sebagai Saldo Awal Pencocokan
            $beginningReconciled = JournalEntry::where('account_code', $selectedBank)
                ->where('is_reconciled', 1)
                ->whereHas('journal', function($q) use ($startOfMonth) {
                    $q->whereDate('date', '<', $startOfMonth);
                })
                ->selectRaw('SUM(debit) - SUM(credit) as balance')
                ->value('balance') ?? 0;
        }

        // Akun untuk Penyesuaian Cepat (Admin Bank / Bunga)
        $expenseAccounts = Account::where('code', 'LIKE', '7%')->orWhere('code', 'LIKE', '8%')->where('is_header', false)->get();
        $incomeAccounts = Account::where('code', 'LIKE', '4%')->orWhere('code', 'LIKE', '9%')->where('is_header', false)->get();

        return view('journals.reconciliation', compact('banks', 'selectedBank', 'selectedMonth', 'entries', 'systemBalance', 'beginningReconciled', 'expenseAccounts', 'incomeAccounts'));
    }

    public function saveReconciliation(Request $request)
    {
        $validated = $request->validate([
            'account_code' => 'required|exists:accounts,code',
            'month'        => 'required', // Format YYYY-MM
            'entry_ids'    => 'nullable|array',
        ]);

        $year = substr($validated['month'], 0, 4);
        $month = substr($validated['month'], 5, 2);

        DB::beginTransaction();
        try {
            // 1. Reset (Un-check) semua status rekonsiliasi di bulan ini terlebih dahulu
            $entriesInMonth = JournalEntry::where('account_code', $validated['account_code'])
                ->whereHas('journal', function($q) use ($year, $month) {
                    $q->whereYear('date', $year)->whereMonth('date', $month);
                })->pluck('id');

            JournalEntry::whereIn('id', $entriesInMonth)->update(['is_reconciled' => 0]);

            // 2. Centang kembali (Reconcile) khusus ID yang dikirim dari form
            if (!empty($validated['entry_ids'])) {
                JournalEntry::whereIn('id', $validated['entry_ids'])->update(['is_reconciled' => 1]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Hebat! Data Rekonsiliasi Bank bulan ini berhasil dicocokkan dan disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan rekonsiliasi: ' . $e->getMessage());
        }
    }

    public function reconciliationAdjust(Request $request)
    {
        $validated = $request->validate([
            'date'             => 'required|date',
            'bank_code'        => 'required|exists:accounts,code',
            'adj_account_code' => 'required|exists:accounts,code',
            'type'             => 'required|in:in,out',
            'amount'           => 'required|numeric|min:1',
            'description'      => 'required|string'
        ]);

        DB::beginTransaction();
        try {
            $jCode = "ADJ-BNK-" . Carbon::parse($validated['date'])->format('YmdHis');
            $journal = Journal::create([
                'date'         => $validated['date'],
                'description'  => "[Rekonsiliasi] " . $validated['description'],
                'reference_no' => $jCode
            ]);

            // Transaksi dari penyesuaian otomatis berstatus "is_reconciled = 1" agar langsung cocok
            if ($validated['type'] === 'in') {
                // Bank Bertambah (Pendapatan Bunga)
                JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $validated['bank_code'], 'debit' => $validated['amount'], 'credit' => 0, 'is_reconciled' => 1]);
                JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $validated['adj_account_code'], 'debit' => 0, 'credit' => $validated['amount']]);
            } else {
                // Bank Berkurang (Biaya Admin)
                JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $validated['adj_account_code'], 'debit' => $validated['amount'], 'credit' => 0]);
                JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $validated['bank_code'], 'debit' => 0, 'credit' => $validated['amount'], 'is_reconciled' => 1]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Jurnal Penyesuaian berhasil ditambahkan dan otomatis terekonsiliasi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambah penyesuaian: ' . $e->getMessage());
        }
    }

    // ==========================================================
    // FITUR BARU: BUKU BESAR (GENERAL LEDGER)
    // ==========================================================
    public function ledger(Request $request)
    {
        // 1. Ambil daftar semua akun aktif (bukan header) untuk dropdown
        $accounts = \App\Models\Account::where('is_header', false)->orderBy('code', 'asc')->get();

        // 2. Tangkap filter dari input user (Default: Bulan ini)
        $selectedAccountCode = $request->input('account_code');
        $startDate = $request->input('start_date', \Carbon\Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', \Carbon\Carbon::now()->endOfMonth()->toDateString());

        $entries = collect();
        $beginningBalance = 0;
        $selectedAccount = null;

        // 3. Jika user sudah memilih Akun, lakukan kalkulasi
        if ($selectedAccountCode) {
            $selectedAccount = \App\Models\Account::where('code', $selectedAccountCode)->first();

            if ($selectedAccount) {
                // A. HITUNG SALDO AWAL (Semua transaksi SEBELUM start_date)
                $prevEntries = \App\Models\JournalEntry::where('account_code', $selectedAccountCode)
                    ->whereHas('journal', function($q) use ($startDate) {
                        $q->whereDate('date', '<', $startDate);
                    })
                    ->get();

                $prevDebit = $prevEntries->sum('debit');
                $prevKredit = $prevEntries->sum('credit');

                // Logika Saldo Normal Akuntansi
                if ($selectedAccount->normal_balance == 'K') {
                    $beginningBalance = $prevKredit - $prevDebit;
                } else {
                    $beginningBalance = $prevDebit - $prevKredit;
                }

                // B. AMBIL MUTASI PERIODE BERJALAN (Antara start_date & end_date)
                $entries = \App\Models\JournalEntry::with('journal')
                    ->where('account_code', $selectedAccountCode)
                    ->whereHas('journal', function($q) use ($startDate, $endDate) {
                        $q->whereBetween('date', [$startDate, $endDate]);
                    })
                    ->get()
                    // Urutkan berdasarkan tanggal jurnal dan ID jurnal
                    ->sortBy(function($entry) {
                        return $entry->journal->date . '-' . str_pad($entry->journal->id, 10, '0', STR_PAD_LEFT);
                    })
                    ->values(); // Reset array index
            }
        }

        return view('journals.ledger', compact(
            'accounts', 'selectedAccountCode', 'startDate', 'endDate',
            'entries', 'beginningBalance', 'selectedAccount'
        ));
    }

}
