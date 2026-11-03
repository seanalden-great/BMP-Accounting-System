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
}
