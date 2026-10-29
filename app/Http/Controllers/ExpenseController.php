<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\Contact;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    // Menampilkan daftar biaya
    public function index()
    {
        $expenses = Expense::with(['contact', 'account', 'paymentAccount'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
            
        $contacts = Contact::orderBy('name', 'asc')->get();
        // Akun Biaya Operasional (Awalan 7)
        $expenseAccounts = Account::where('code', 'LIKE', '7%')->where('is_header', false)->get();
        // Akun Kas/Bank (Awalan 10/11)
        $paymentAccounts = Account::where(function($query) {
            $query->where('code', 'LIKE', '10%')->orWhere('code', 'LIKE', '11%');
        })->where('is_header', false)->get();

        return view('expenses.index', compact('expenses', 'contacts', 'expenseAccounts', 'paymentAccounts'));
    }

    // Menyimpan Biaya dan Jurnal
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
            // 1. Simpan Data Pengeluaran Biaya
            $expense = Expense::create([
                'date'         => $validated['date'],
                'reference_no' => $validated['reference_no'],
                'contact_id'   => $validated['contact_id'],
                'account_code' => $validated['account_code'],
                'payment_code' => $validated['payment_code'],
                'amount'       => $validated['amount'],
                'description'  => $validated['description'],
            ]);

            // 2. Buat Jurnal Otomatis (Debit: Akun Beban, Kredit: Akun Kas/Bank)
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

            // Debit: Beban
            JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $validated['account_code'], 'debit' => $validated['amount'], 'credit' => 0]);
            // Kredit: Kas/Bank
            JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $validated['payment_code'], 'debit' => 0, 'credit' => $validated['amount']]);

            DB::commit();
            return redirect()->route('expenses.index')->with('success', 'Biaya berhasil dicatat beserta Jurnalnya!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal mencatat pengeluaran biaya.');
        }
    }

    // Menghapus Biaya
    public function destroy($id)
    {
        try {
            $expense = Expense::findOrFail($id);
            $expense->delete();
            
            // Catatan: Jurnal tidak ikut terhapus otomatis demi keamanan jejak audit (Audit Trail).
            // Di sistem ERP sungguhan, Jurnal harus di-reverse, bukan dihapus.
            
            return redirect()->route('expenses.index')->with('success', 'Catatan biaya berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus catatan biaya.');
        }
    }
}