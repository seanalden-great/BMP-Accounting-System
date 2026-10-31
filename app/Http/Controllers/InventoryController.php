<?php

// namespace App\Http\Controllers;

// use App\Models\StockAdjustment;
// use App\Models\Product;
// use App\Models\Account;
// use App\Models\Journal;
// use App\Models\JournalEntry;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Carbon\Carbon;

// class InventoryController extends Controller
// {
//     // Menampilkan halaman riwayat penyesuaian stok (GET /inventory)
//     public function index()
//     {
//         // Ambil data penyesuaian beserta relasi produknya
//         $adjustments = StockAdjustment::with('product')
//             ->orderBy('date', 'desc')
//             ->orderBy('id', 'desc')
//             ->get();
            
//         // Ambil data produk untuk dropdown di modal/form penyesuaian
//         $products = Product::orderBy('name', 'asc')->get();

//         return view('inventory.index', compact('adjustments', 'products'));
//     }

//     // Memproses penyesuaian stok (POST /inventory/adjust)
//     public function adjustStock(Request $request)
//     {
//         $validated = $request->validate([
//             'date'        => 'required|date',
//             'product_sku' => 'required|exists:products,sku',
//             'type'        => 'required|in:Penambahan,Pengurangan',
//             'quantity'    => 'required|integer|min:1',
//             'reason'      => 'required|string|max:255',
//         ]);

//         DB::beginTransaction();

//         try {
//             $product = Product::where('sku', $validated['product_sku'])->lockForUpdate()->first();

//             // Validasi jika pengurangan melebihi stok yang ada
//             if ($validated['type'] == 'Pengurangan' && $product->stock < $validated['quantity']) {
//                 return redirect()->back()
//                     ->withInput()
//                     ->with('error', 'Stok tidak mencukupi untuk dikurangi.');
//             }

//             // Nilai penyesuaian dihitung berdasarkan HPP produk saat ini
//             $totalValue = $validated['quantity'] * $product->hpp;

//             // 1. Simpan Riwayat Penyesuaian
//             StockAdjustment::create([
//                 'date'        => $validated['date'],
//                 'product_sku' => $validated['product_sku'],
//                 'type'        => $validated['type'],
//                 'quantity'    => $validated['quantity'],
//                 'reason'      => $validated['reason'],
//                 'total_value' => $totalValue,
//             ]);

//             // 2. Update Master Stok Produk
//             if ($validated['type'] == 'Penambahan') {
//                 $product->stock += $validated['quantity'];
//             } else {
//                 $product->stock -= $validated['quantity'];
//             }
//             $product->save();

//             // 3. Catat Jurnal Akuntansi (Jika total nilai > 0)
//             if ($totalValue > 0) {
//                 // PERBAIKAN PENCARIAN AKUN (Sesuai CoA Excel Anda)
//                 // Cari Persediaan (13% - is_header = false)
//                 $invAcc = Account::where('code', 'LIKE', '13%')->where('is_header', false)->first();
//                 if (!$invAcc) {
//                     $invAcc = Account::where('code', 'LIKE', '16%')->where('is_header', false)->first();
//                 }

//                 // Cari HPP / Beban Penyusutan Stok (60% - is_header = false)
//                 $cogsAcc = Account::where('code', 'LIKE', '60%')->where('is_header', false)->first();
//                 if (!$cogsAcc) {
//                     $cogsAcc = Account::where('code', 'LIKE', '5%')->where('is_header', false)->first();
//                 }

//                 if ($invAcc && $cogsAcc) {
//                     $jCode = "ADJ-" . $product->sku . "-" . Carbon::now()->format('His');
                    
//                     $journal = Journal::create([
//                         'date'         => $validated['date'],
//                         'description'  => "Penyesuaian Stok: " . $validated['reason'] . " (" . $jCode . ")",
//                         'reference_no' => $jCode,
//                     ]);

//                     if ($validated['type'] == 'Penambahan') {
//                         // Jurnal Penambahan: Persediaan Bertambah (D), Beban/HPP Berkurang (K)
//                         JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $invAcc->code, 'debit' => $totalValue, 'credit' => 0]);
//                         JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $cogsAcc->code, 'debit' => 0, 'credit' => $totalValue]);
//                     } else {
//                         // Jurnal Pengurangan: Beban/HPP Bertambah (D), Persediaan Berkurang (K)
//                         JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $cogsAcc->code, 'debit' => $totalValue, 'credit' => 0]);
//                         JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $invAcc->code, 'debit' => 0, 'credit' => $totalValue]);
//                     }
//                 }
//             }

//             DB::commit();
//             return redirect()->route('inventory.index')
//                 ->with('success', 'Stok berhasil disesuaikan dan jurnal telah dicatat.');

//         } catch (\Exception $e) {
//             DB::rollBack();
//             return redirect()->back()
//                 ->withInput()
//                 ->with('error', 'Terjadi kesalahan sistem saat memproses penyesuaian stok.');
//         }
//     }
// }

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use App\Models\Product;
use App\Models\Warehouse; // Tambahkan ini
use App\Models\ProductStock; // Tambahkan ini
use App\Models\Account;
use App\Models\Journal;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryController extends Controller
{
    public function index()
    {
        // Ambil riwayat penyesuaian
        $adjustments = StockAdjustment::with(['product', 'warehouse']) // Relasikan dengan warehouse
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
            
        // Ambil Master Data untuk Form Penyesuaian
        $products = Product::orderBy('name', 'asc')->get();
        $warehouses = Warehouse::orderBy('name', 'asc')->get(); // Ambil data Gudang

        return view('inventory.index', compact('adjustments', 'products', 'warehouses'));
    }

    public function adjustStock(Request $request)
    {
        $validated = $request->validate([
            'date'         => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id', // Tambahkan validasi gudang
            'product_sku'  => 'required|exists:products,sku',
            'type'         => 'required|in:Penambahan,Pengurangan',
            'quantity'     => 'required|integer|min:1',
            'reason'       => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $product = Product::where('sku', $validated['product_sku'])->lockForUpdate()->first();
            
            // Cari stok produk di gudang yang dipilih (jika belum ada, buat baru qty 0)
            $stockRecord = ProductStock::firstOrCreate(
                ['warehouse_id' => $validated['warehouse_id'], 'product_sku' => $validated['product_sku']],
                ['quantity' => 0]
            );

            // Validasi pengurangan stok
            if ($validated['type'] == 'Pengurangan' && $stockRecord->quantity < $validated['quantity']) {
                return redirect()->back()->withInput()->with('error', 'Stok di gudang tersebut tidak mencukupi untuk dikurangi.');
            }

            $totalValue = $validated['quantity'] * $product->hpp;

            // 1. Simpan Riwayat
            StockAdjustment::create([
                'date'         => $validated['date'],
                'warehouse_id' => $validated['warehouse_id'],
                'product_sku'  => $validated['product_sku'],
                'type'         => $validated['type'],
                'quantity'     => $validated['quantity'],
                'reason'       => $validated['reason'],
                'total_value'  => $totalValue,
            ]);

            // 2. Update Stok di Tabel product_stocks (bukan lagi di products)
            if ($validated['type'] == 'Penambahan') {
                $stockRecord->quantity += $validated['quantity'];
            } else {
                $stockRecord->quantity -= $validated['quantity'];
            }
            $stockRecord->save();

            // 3. Catat Jurnal (Kode Jurnal Tetap Sama)
            if ($totalValue > 0) {
                $invAcc = Account::where('code', 'LIKE', '13%')->where('is_header', false)->first() ?? Account::where('code', 'LIKE', '16%')->where('is_header', false)->first();
                $cogsAcc = Account::where('code', 'LIKE', '60%')->where('is_header', false)->first() ?? Account::where('code', 'LIKE', '5%')->where('is_header', false)->first();

                if ($invAcc && $cogsAcc) {
                    $jCode = "ADJ-" . $product->sku . "-" . Carbon::now()->format('His');
                    $journal = Journal::create([
                        'date'         => $validated['date'],
                        'description'  => "Penyesuaian Stok Gudang ID:".$validated['warehouse_id']." - " . $validated['reason'] . " (" . $jCode . ")",
                        'reference_no' => $jCode,
                    ]);

                    if ($validated['type'] == 'Penambahan') {
                        JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $invAcc->code, 'debit' => $totalValue, 'credit' => 0]);
                        JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $cogsAcc->code, 'debit' => 0, 'credit' => $totalValue]);
                    } else {
                        JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $cogsAcc->code, 'debit' => $totalValue, 'credit' => 0]);
                        JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $invAcc->code, 'debit' => 0, 'credit' => $totalValue]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('inventory.index')->with('success', 'Stok berhasil disesuaikan dan jurnal telah dicatat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}