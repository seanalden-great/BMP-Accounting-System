<?php

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

    // Memproses Penyusutan Aset 1 Bulan
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

            // === PERBAIKAN PENCARIAN AKUN (Sesuaikan dengan CoA Excel) ===
            // Cari Beban Penyusutan (770)
            $deprExpAcc = Account::where('code', 'LIKE', '77%')->where('is_header', false)->first(); 
            // Cari Akumulasi Penyusutan (210)
            $accumAcc = Account::where('code', 'LIKE', '21%')->where('is_header', false)->first(); 

            if (!$deprExpAcc || !$accumAcc) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Konfigurasi Akun Beban Penyusutan (77) atau Akumulasi (21) belum ada di sistem!');
            }

            $journalCode = "DPR-" . Carbon::now()->format('His') . "-" . $asset->id;
            
            $journal = Journal::create([
                'date'         => Carbon::now(),
                'description'  => "Penyusutan 1 Bln: " . $asset->name . " (" . $journalCode . ")",
                'reference_no' => $journalCode,
            ]);

            JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $deprExpAcc->code, 'debit' => $deprAmount, 'credit' => 0]);
            JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $accumAcc->code, 'debit' => 0, 'credit' => $deprAmount]);

            DB::commit();
            return redirect()->back()->with('success', 'Penyusutan 1 bulan berhasil diproses!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses penyusutan.');
        }
    }
}