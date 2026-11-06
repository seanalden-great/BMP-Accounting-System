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
                $journals = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->get();
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
    public function export(Request $request)
    {
        $accountCode = $request->query('account_code');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $type = $request->query('type', 'internal'); // 'internal' atau 'pajak'
        $format = $request->query('format', 'csv'); // 'csv' atau 'pdf'

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

        // Judul laporan dibedakan sesuai tipe yang di-klik
        $reportName = $type === 'pajak' ? 'LAPORAN MUTASI BANK (PAJAK)' : 'LAPORAN MUTASI BANK (INTERNAL)';

        // FORMAT 1: CSV / EXCEL
        // if ($format === 'csv') {
        //     $headers = [
        //         "Content-type"        => "text/csv",
        //         "Content-Disposition" => "attachment; filename=Mutasi_{$type}_{$bank->name}.csv",
        //         "Pragma"              => "no-cache",
        //         "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        //         "Expires"             => "0"
        //     ];

        //     $callback = function() use ($journals, $bank, $saldoAwal, $reportName, $startDate, $endDate) {
        //         $file = fopen('php://output', 'w');
        //         fputcsv($file, ['PT. BERLIAN MANDIRI PERKASA']);
        //         fputcsv($file, [$reportName]);
        //         fputcsv($file, ['Rekening:', $bank->name . ' (' . $bank->code . ')']);
        //         fputcsv($file, ['Periode:', ($startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Awal') . ' s/d ' . ($endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Akhir')]);
        //         fputcsv($file, []);
        //         fputcsv($file, ['No', 'Tanggal', 'Uraian', 'Akun Lawan', 'Keterangan', 'Debet', 'Kredit', 'Saldo']);

        //         $runningBalance = $saldoAwal;
        //         $row = 1;

        //         if ($startDate) {
        //             fputcsv($file, ['-', $startDate, 'SALDO AWAL', '', '', '', '', $runningBalance]);
        //         }

        //         foreach ($journals as $journal) {
        //             foreach ($journal->entries as $entry) {
        //                 if ($entry->account_code == $bank->code) {
        //                     $runningBalance += ($entry->debit - $entry->credit);

        //                     $lawan = $journal->entries->where('account_code', '!=', $bank->code)->first();
        //                     $lawanName = $lawan ? $lawan->account->name : 'Multiple';
        //                     $lawanCode = $lawan ? $lawan->account_code : '';

        //                     fputcsv($file, [
        //                         $row++,
        //                         $journal->date,
        //                         $journal->description . ($journal->reference_no ? ' - '.$journal->reference_no : ''),
        //                         $lawanCode,
        //                         $lawanName,
        //                         $entry->debit,
        //                         $entry->credit,
        //                         $runningBalance
        //                     ]);
        //                 }
        //             }
        //         }
        //         fclose($file);
        //     };
        //     return response()->stream($callback, 200, $headers);
        // }

        // FORMAT 1: CSV / EXCEL
        if ($format === 'csv') {
            $headers = [
                "Content-type"        => "text/csv; charset=UTF-8", // Pastikan encoding UTF-8
                "Content-Disposition" => "attachment; filename=Mutasi_{$type}_{$bank->name}.csv",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function() use ($journals, $bank, $saldoAwal, $reportName, $startDate, $endDate) {
                $file = fopen('php://output', 'w');

                // Tambahkan BOM (Byte Order Mark) agar Excel otomatis mendeteksi format secara rapi
                fputs($file, "\xEF\xBB\xBF");

                // KUNCI PERBAIKAN: Gunakan pemisah Titik Koma (;) khusus untuk Excel region Indonesia
                $delimiter = ';';

                // Tulis Header Info
                fputcsv($file, ['PT. BERLIAN MANDIRI PERKASA'], $delimiter);
                fputcsv($file, [$reportName], $delimiter);
                fputcsv($file, ['Rekening:', $bank->name . ' (' . $bank->code . ')'], $delimiter);

                $tglAwal = $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Awal';
                $tglAkhir = $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Akhir';
                fputcsv($file, ['Periode:', $tglAwal . ' s/d ' . $tglAkhir], $delimiter);

                fputcsv($file, [], $delimiter); // Baris kosong pemisah

                // Tulis Header Tabel (SUSUNAN PERSIS SEPERTI DI WEB)
                fputcsv($file, [
                    'No.',
                    'Tanggal',
                    'Uraian / Keterangan',
                    'No. AKUN',
                    'DEBET',
                    'KREDIT',
                    'SISA SALDO',
                    '', // Celah kolom kosong pemisah seperti di web
                    'KETERANGAN / LAWAN'
                ], $delimiter);

                $runningBalance = $saldoAwal;
                $rowNumber = 1;

                // Tulis Baris Saldo Awal (Jika Ada)
                if ($startDate) {
                    fputcsv($file, [
                        '-',
                        \Carbon\Carbon::parse($startDate)->format('d-M-Y'),
                        'SALDO AWAL (Pindahan)',
                        '',
                        '',
                        '',
                        $runningBalance, // Angka asli agar bisa di-sum oleh Excel
                        '',
                        'Akumulasi historis'
                    ], $delimiter);
                }

                // Tulis Rincian Baris Transaksi
                foreach ($journals as $journal) {
                    foreach ($journal->entries as $entry) {
                        if ($entry->account_code == $bank->code) {
                            $runningBalance += ($entry->debit - $entry->credit);

                            $lawan = $journal->entries->where('account_code', '!=', $bank->code)->first();
                            $lawanName = $lawan ? $lawan->account->name : 'Tanpa Keterangan';

                            $desc = $journal->description;
                            if ($journal->reference_no) {
                                $desc .= ' - ' . $journal->reference_no;
                            }

                            fputcsv($file, [
                                $rowNumber++,
                                \Carbon\Carbon::parse($journal->date)->format('d-M-Y'),
                                $desc,
                                $entry->account_code,
                                $entry->debit > 0 ? $entry->debit : '',   // Biarkan berupa angka mentah
                                $entry->credit > 0 ? $entry->credit : '', // tanpa format rupiah agar bisa
                                $runningBalance,                          // dijumlahkan oleh rumus SUM Excel
                                '', // Celah kolom kosong
                                $lawanName
                            ], $delimiter);
                        }
                    }
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // FORMAT 2: CETAK DOKUMEN HTML / PDF
        return view('journals.export_print', compact('journals', 'bank', 'saldoAwal', 'reportName', 'startDate', 'endDate'));
    }
}
