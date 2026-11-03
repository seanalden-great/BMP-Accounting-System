<?php

// namespace App\Http\Controllers;

// use App\Models\Expense;
// use App\Models\Journal;
// use App\Models\JournalEntry;
// use App\Models\Contact;
// use App\Models\Account;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Carbon\Carbon;

// class ExpenseController extends Controller
// {
//     // Menampilkan daftar biaya
//     public function index()
//     {
//         $expenses = Expense::with(['contact', 'account', 'paymentAccount'])
//             ->orderBy('date', 'desc')
//             ->orderBy('id', 'desc')
//             ->get();

//         $contacts = Contact::orderBy('name', 'asc')->get();
//         // Akun Biaya Operasional (Awalan 7)
//         $expenseAccounts = Account::where('code', 'LIKE', '7%')->where('is_header', false)->get();
//         // Akun Kas/Bank (Awalan 10/11)
//         $paymentAccounts = Account::where(function($query) {
//             $query->where('code', 'LIKE', '10%')->orWhere('code', 'LIKE', '11%');
//         })->where('is_header', false)->get();

//         return view('expenses.index', compact('expenses', 'contacts', 'expenseAccounts', 'paymentAccounts'));
//     }

//     // Menyimpan Biaya dan Jurnal
//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'date'         => 'required|date',
//             'reference_no' => 'nullable|string|max:50',
//             'contact_id'   => 'nullable|exists:contacts,id',
//             'account_code' => 'required|exists:accounts,code',
//             'payment_code' => 'required|exists:accounts,code',
//             'amount'       => 'required|numeric|min:1',
//             'description'  => 'required|string',
//         ]);

//         DB::beginTransaction();

//         try {
//             // 1. Simpan Data Pengeluaran Biaya
//             $expense = Expense::create([
//                 'date'         => $validated['date'],
//                 'reference_no' => $validated['reference_no'],
//                 'contact_id'   => $validated['contact_id'],
//                 'account_code' => $validated['account_code'],
//                 'payment_code' => $validated['payment_code'],
//                 'amount'       => $validated['amount'],
//                 'description'  => $validated['description'],
//             ]);

//             // 2. Buat Jurnal Otomatis (Debit: Akun Beban, Kredit: Akun Kas/Bank)
//             $jDesc = "Biaya: " . $validated['description'];
//             if (!empty($validated['reference_no'])) {
//                 $jDesc .= " (Ref: " . $validated['reference_no'] . ")";
//             }

//             $journalCode = "EXP-" . Carbon::now()->format('Ymd') . "-" . $expense->id;

//             $journal = Journal::create([
//                 'date'         => $validated['date'],
//                 'description'  => $jDesc,
//                 'reference_no' => $journalCode,
//             ]);

//             // Debit: Beban
//             JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $validated['account_code'], 'debit' => $validated['amount'], 'credit' => 0]);
//             // Kredit: Kas/Bank
//             JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $validated['payment_code'], 'debit' => 0, 'credit' => $validated['amount']]);

//             DB::commit();
//             return redirect()->route('expenses.index')->with('success', 'Biaya berhasil dicatat beserta Jurnalnya!');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return redirect()->back()->withInput()->with('error', 'Gagal mencatat pengeluaran biaya.');
//         }
//     }

//     // Menghapus Biaya
//     public function destroy($id)
//     {
//         try {
//             $expense = Expense::findOrFail($id);
//             $expense->delete();

//             // Catatan: Jurnal tidak ikut terhapus otomatis demi keamanan jejak audit (Audit Trail).
//             // Di sistem ERP sungguhan, Jurnal harus di-reverse, bukan dihapus.

//             return redirect()->route('expenses.index')->with('success', 'Catatan biaya berhasil dihapus.');
//         } catch (\Exception $e) {
//             return redirect()->back()->with('error', 'Gagal menghapus catatan biaya.');
//         }
//     }
// }

// namespace App\Http\Controllers;

// use App\Models\Expense;
// use App\Models\Journal;
// use App\Models\JournalEntry;
// use App\Models\Contact;
// use App\Models\Account;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Carbon\Carbon;

// class ExpenseController extends Controller
// {
//     // Menampilkan daftar biaya
//     public function index()
//     {
//         $expenses = Expense::with(['contact', 'account', 'paymentAccount'])
//             ->orderBy('date', 'desc')
//             ->orderBy('id', 'desc')
//             ->get();

//         $contacts = Contact::orderBy('name', 'asc')->get();
//         // Akun Biaya Operasional (Awalan 7)
//         $expenseAccounts = Account::where('code', 'LIKE', '7%')->where('is_header', false)->get();
//         // Akun Kas/Bank (Awalan 10/11)
//         $paymentAccounts = Account::where(function($query) {
//             $query->where('code', 'LIKE', '10%')->orWhere('code', 'LIKE', '11%');
//         })->where('is_header', false)->get();

//         return view('expenses.index', compact('expenses', 'contacts', 'expenseAccounts', 'paymentAccounts'));
//     }

//     // Menyimpan Biaya dan Jurnal
//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'date'         => 'required|date',
//             'reference_no' => 'nullable|string|max:50',
//             'contact_id'   => 'nullable|exists:contacts,id',
//             'account_code' => 'required|exists:accounts,code',
//             'payment_code' => 'required|exists:accounts,code',
//             'amount'       => 'required|numeric|min:1',
//             'description'  => 'required|string',
//         ]);

//         DB::beginTransaction();

//         try {
//             // 1. Simpan Data Pengeluaran Biaya (Gunakan ?? agar aman saat field opsional kosong)
//             $expense = Expense::create([
//                 'date'         => $validated['date'],
//                 'reference_no' => $validated['reference_no'] ?? '',
//                 'contact_id'   => $validated['contact_id'] ?? null,
//                 'account_code' => $validated['account_code'],
//                 'payment_code' => $validated['payment_code'],
//                 'amount'       => $validated['amount'],
//                 'description'  => $validated['description'],
//             ]);

//             // 2. Buat Jurnal Otomatis (Debit: Akun Beban, Kredit: Akun Kas/Bank)
//             $jDesc = "Biaya: " . $validated['description'];
//             if (!empty($validated['reference_no'])) {
//                 $jDesc .= " (Ref: " . $validated['reference_no'] . ")";
//             }

//             $journalCode = "EXP-" . Carbon::now()->format('Ymd') . "-" . $expense->id;

//             $journal = Journal::create([
//                 'date'         => $validated['date'],
//                 'description'  => $jDesc,
//                 'reference_no' => $journalCode,
//             ]);

//             // Debit: Beban
//             JournalEntry::create([
//                 'journal_id'   => $journal->id,
//                 'account_code' => $validated['account_code'],
//                 'debit'        => $validated['amount'],
//                 'credit'       => 0
//             ]);
//             // Kredit: Kas/Bank
//             JournalEntry::create([
//                 'journal_id'   => $journal->id,
//                 'account_code' => $validated['payment_code'],
//                 'debit'        => 0,
//                 'credit'       => $validated['amount']
//             ]);

//             DB::commit();
//             return redirect()->route('expenses.index')->with('success', 'Biaya berhasil dicatat beserta Jurnalnya!');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return redirect()->back()->withInput()->with('error', 'Gagal mencatat pengeluaran biaya: ' . $e->getMessage());
//         }
//     }

//     // Menghapus Biaya
//     public function destroy($id)
//     {
//         try {
//             $expense = Expense::findOrFail($id);
//             $expense->delete();

//             return redirect()->route('expenses.index')->with('success', 'Catatan biaya berhasil dihapus.');
//         } catch (\Exception $e) {
//             return redirect()->back()->with('error', 'Gagal menghapus catatan biaya.');
//         }
//     }
// }

// namespace App\Http\Controllers;

// use App\Models\Expense;
// use App\Models\Journal;
// use App\Models\JournalEntry;
// use App\Models\Contact;
// use App\Models\Account;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Carbon\Carbon;
// use Illuminate\Support\Facades\Auth;

// class ExpenseController extends Controller
// {
//     public function index()
//     {
//         $expenses = Expense::with(['contact', 'account', 'paymentAccount'])
//             ->orderBy('date', 'desc')
//             ->orderBy('id', 'desc')
//             ->get();

//         $contacts = Contact::orderBy('name', 'asc')->get();
//         $expenseAccounts = Account::where('code', 'LIKE', '7%')->where('is_header', false)->get();
//         $paymentAccounts = Account::where(function($query) {
//             $query->where('code', 'LIKE', '10%')->orWhere('code', 'LIKE', '11%');
//         })->where('is_header', false)->get();

//         return view('expenses.index', compact('expenses', 'contacts', 'expenseAccounts', 'paymentAccounts'));
//     }

//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'date'         => 'required|date',
//             'reference_no' => 'nullable|string|max:50',
//             'contact_id'   => 'nullable|exists:contacts,id',
//             'account_code' => 'required|exists:accounts,code',
//             'payment_code' => 'required|exists:accounts,code',
//             'amount'       => 'required|numeric|min:1',
//             'description'  => 'required|string',
//         ]);

//         DB::beginTransaction();

//         try {
//             $expense = Expense::create([
//                 'date'         => $validated['date'],
//                 'reference_no' => $validated['reference_no'] ?? '',
//                 'contact_id'   => $validated['contact_id'] ?? null,
//                 'account_code' => $validated['account_code'],
//                 'payment_code' => $validated['payment_code'],
//                 'amount'       => $validated['amount'],
//                 'description'  => $validated['description'],
//             ]);

//             $jDesc = "Biaya: " . $validated['description'];
//             if (!empty($validated['reference_no'])) {
//                 $jDesc .= " (Ref: " . $validated['reference_no'] . ")";
//             }

//             $journalCode = "EXP-" . Carbon::now()->format('Ymd') . "-" . $expense->id;

//             $journal = Journal::create([
//                 'date'         => $validated['date'],
//                 'description'  => $jDesc,
//                 'reference_no' => $journalCode,
//             ]);

//             JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $validated['account_code'], 'debit' => $validated['amount'], 'credit' => 0]);
//             JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $validated['payment_code'], 'debit' => 0, 'credit' => $validated['amount']]);

//             DB::commit();
//             return redirect()->route('expenses.index')->with('success', 'Biaya berhasil dicatat beserta Jurnalnya!');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return redirect()->back()->withInput()->with('error', 'Gagal mencatat pengeluaran biaya: ' . $e->getMessage());
//         }
//     }

//     public function destroy($id)
//     {
//         try {
//             $expense = Expense::findOrFail($id);
//             $expense->delete();
//             return redirect()->route('expenses.index')->with('success', 'Catatan biaya berhasil dihapus.');
//         } catch (\Exception $e) {
//             return redirect()->back()->with('error', 'Gagal menghapus catatan biaya.');
//         }
//     }

//     // --- LOGIKA BARU UNTUK KBD (Anggaran Dana) ---
//     private function generateKbdNumber()
//     {
//         $now = Carbon::now();
//         $month = $now->month;
//         $year = $now->year;

//         $romanMonths = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];
//         $romanMonth = $romanMonths[$month];

//         // Menghitung jumlah dokumen KBD unik yang sudah pernah dicetak pada bulan ini
//         $existingKbdCount = Expense::whereYear('date', $year)
//                                 ->whereMonth('date', $month)
//                                 ->whereNotNull('kbd_no')
//                                 ->distinct('kbd_no')
//                                 ->count('kbd_no');

//         $nextSeq = $existingKbdCount + 1;
//         $seqStr = str_pad($nextSeq, 3, '0', STR_PAD_LEFT);

//         return "{$seqStr}/KBD-BMP/{$romanMonth}/{$year}";
//     }

//     public function exportKbd(Request $request)
//     {
//         $ids = explode(',', $request->query('ids'));
//         $format = $request->query('format'); // 'print' atau 'csv'

//         $expenses = Expense::whereIn('id', $ids)->orderBy('date', 'asc')->get();

//         if ($expenses->isEmpty()) {
//             return redirect()->back()->with('error', 'Pilih minimal 1 pengeluaran untuk dicetak.');
//         }

//         // Cek apakah item yang dipilih sudah pernah di-print KBD sebelumnya (punya nomor).
//         // Jika belum, buatkan nomor KBD baru dan simpan ke database.
//         $kbdNo = $expenses->first()->kbd_no;
//         if (!$kbdNo) {
//             $kbdNo = $this->generateKbdNumber();
//             Expense::whereIn('id', $ids)->update(['kbd_no' => $kbdNo]);

//             // Segarkan data setelah update
//             $expenses = Expense::whereIn('id', $ids)->orderBy('date', 'asc')->get();
//         }

//         $total = $expenses->sum('amount');

//         // Nama pencetak diambil dari user login. Jika kosong, default 'Nita' (atau nama Anda)
//         $creatorName = Auth::user()->full_name ?? (Auth::user()->name ?? 'Nita');

//         // Tanggal pembuatan KBD (Bahasa Indonesia)
//         Carbon::setLocale('id');
//         $dateStr = Carbon::now()->translatedFormat('d F Y');

//         // FORMAT 1: Ekspor ke CSV / Excel / Spreadsheet
//         if ($format === 'csv') {
//             $headers = [
//                 "Content-type"        => "text/csv",
//                 "Content-Disposition" => "attachment; filename=KBD_{$kbdNo}.csv",
//                 "Pragma"              => "no-cache",
//                 "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
//                 "Expires"             => "0"
//             ];

//             $callback = function() use ($expenses, $kbdNo, $total, $dateStr, $creatorName) {
//                 $file = fopen('php://output', 'w');
//                 // Tulis Header seperti gambar
//                 fputcsv($file, ['PT. BERLIAN MANDIRI PERKASA']);
//                 fputcsv($file, ['ANGGARAN DANA']);
//                 fputcsv($file, ['No. ' . $kbdNo]);
//                 fputcsv($file, []);
//                 fputcsv($file, ['Keperluan :']);

//                 // Tulis Item
//                 $i = 1;
//                 foreach ($expenses as $exp) {
//                     fputcsv($file, [$i++, $exp->description, 'Rp', $exp->amount]);
//                 }

//                 fputcsv($file, []);
//                 fputcsv($file, ['', 'TOTAL', 'Rp', $total]);
//                 fputcsv($file, []);

//                 // Tulis Footer Tanda Tangan
//                 fputcsv($file, ['Surabaya, ' . $dateStr]);
//                 fputcsv($file, ['Dibuat Oleh', 'Mengetahui', 'Menyetujui']);
//                 fputcsv($file, []);
//                 fputcsv($file, []);
//                 fputcsv($file, [$creatorName, 'Melisa', 'HARIJANTO']);
//                 fclose($file);
//             };

//             return response()->stream($callback, 200, $headers);
//         }

//         // FORMAT 2: Cetak Layar HTML (Untuk Print PDF)
//         return view('expenses.kbd_print', compact('expenses', 'kbdNo', 'total', 'dateStr', 'creatorName'));
//     }
// }

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\Contact;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Tarik data dengan Filter Tanggal
        $query = Expense::with(['contact', 'account', 'paymentAccount']);

        if ($startDate) {
            $query->whereDate('date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('date', '<=', $endDate);
        }

        $expenses = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->get();

        $contacts = Contact::orderBy('name', 'asc')->get();
        $expenseAccounts = Account::where('code', 'LIKE', '7%')->where('is_header', false)->get();
        $paymentAccounts = Account::where(function($query) {
            $query->where('code', 'LIKE', '10%')->orWhere('code', 'LIKE', '11%');
        })->where('is_header', false)->get();

        return view('expenses.index', compact('expenses', 'contacts', 'expenseAccounts', 'paymentAccounts', 'startDate', 'endDate'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date'         => 'required|date',
            'reference_no' => 'nullable|string|max:50',
            'contact_id'   => 'nullable|exists:contacts,id',
            'account_code' => 'required|exists:accounts,code',
            'payment_code' => 'required|exists:accounts,code',
            'amount'       => 'required|numeric|min:1',
            'description'  => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $expense = Expense::create([
                'date'         => $validated['date'],
                'reference_no' => $validated['reference_no'] ?? '',
                'contact_id'   => $validated['contact_id'] ?? null,
                'account_code' => $validated['account_code'],
                'payment_code' => $validated['payment_code'],
                'amount'       => $validated['amount'],
                'description'  => $validated['description'],
            ]);

            $jDesc = "Biaya: " . $validated['description'];
            if (!empty($validated['reference_no'])) {
                $jDesc .= " (Ref: " . $validated['reference_no'] . ")";
            }

            $journalCode = "EXP-" . Carbon::now()->format('Ymd') . "-" . $expense->id;

            $journal = Journal::create([
                'date'         => $validated['date'],
                'description'  => $jDesc,
                'reference_no' => $journalCode,
            ]);

            JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $validated['account_code'], 'debit' => $validated['amount'], 'credit' => 0]);
            JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $validated['payment_code'], 'debit' => 0, 'credit' => $validated['amount']]);

            DB::commit();
            return redirect()->route('expenses.index')->with('success', 'Biaya berhasil dicatat beserta Jurnalnya!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal mencatat pengeluaran biaya: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $expense = Expense::findOrFail($id);
            $expense->delete();
            return redirect()->route('expenses.index')->with('success', 'Catatan biaya berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus catatan biaya.');
        }
    }

    // --- LOGIKA KBD (Anggaran Dana) ---
    private function generateKbdNumber()
    {
        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;

        $romanMonths = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];
        $romanMonth = $romanMonths[$month];

        $existingKbdCount = Expense::whereYear('date', $year)
                                ->whereMonth('date', $month)
                                ->whereNotNull('kbd_no')
                                ->distinct('kbd_no')
                                ->count('kbd_no');

        $nextSeq = $existingKbdCount + 1;
        $seqStr = str_pad($nextSeq, 3, '0', STR_PAD_LEFT);

        return "{$seqStr}/KBD-BMP/{$romanMonth}/{$year}";
    }

    public function exportKbd(Request $request)
    {
        $ids = explode(',', $request->query('ids'));
        $format = $request->query('format');

        $expenses = Expense::whereIn('id', $ids)->orderBy('date', 'asc')->get();

        if ($expenses->isEmpty()) {
            return redirect()->back()->with('error', 'Pilih minimal 1 pengeluaran untuk dicetak.');
        }

        $kbdNo = $expenses->first()->kbd_no;
        if (!$kbdNo) {
            $kbdNo = $this->generateKbdNumber();
            Expense::whereIn('id', $ids)->update(['kbd_no' => $kbdNo]);
            $expenses = Expense::whereIn('id', $ids)->orderBy('date', 'asc')->get();
        }

        $total = $expenses->sum('amount');
        $creatorName = Auth::user()->full_name ?? (Auth::user()->name ?? 'Nita');
        Carbon::setLocale('id');
        $dateStr = Carbon::now()->translatedFormat('d F Y');

        if ($format === 'csv') {
            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=KBD_{$kbdNo}.csv",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function() use ($expenses, $kbdNo, $total, $dateStr, $creatorName) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['PT. BERLIAN MANDIRI PERKASA']);
                fputcsv($file, ['ANGGARAN DANA']);
                fputcsv($file, ['No. ' . $kbdNo]);
                fputcsv($file, []);
                fputcsv($file, ['Keperluan :']);

                $i = 1;
                foreach ($expenses as $exp) {
                    fputcsv($file, [$i++, $exp->description, 'Rp', $exp->amount]);
                }

                fputcsv($file, []);
                fputcsv($file, ['', 'TOTAL', 'Rp', $total]);
                fputcsv($file, []);

                fputcsv($file, ['Surabaya, ' . $dateStr]);
                fputcsv($file, ['Dibuat Oleh', 'Mengetahui', 'Menyetujui']);
                fputcsv($file, []);
                fputcsv($file, []);
                fputcsv($file, [$creatorName, 'Melisa', 'HARIJANTO']);
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return view('expenses.kbd_print', compact('expenses', 'kbdNo', 'total', 'dateStr', 'creatorName'));
    }
}
