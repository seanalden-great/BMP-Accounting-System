<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StockAdjustment;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\ProductStock;
use App\Models\Account;
use App\Models\Journal;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryController extends Controller
{
    // Mengambil data master (Gudang & Produk) untuk form mobile
    public function getMasterData()
    {
        $products = Product::orderBy('name', 'asc')->get(['sku', 'name', 'hpp', 'unit']);
        $warehouses = Warehouse::orderBy('name', 'asc')->get(['id', 'code', 'name']);

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $products,
                'warehouses' => $warehouses
            ]
        ], 200);
    }

    // Mengambil riwayat penyesuaian untuk ditampilkan di list mobile
    public function getHistory()
    {
        $adjustments = StockAdjustment::with(['product:sku,name,unit', 'warehouse:id,name'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->take(50) // Batasi 50 terakhir agar mobile tidak berat
            ->get();

        return response()->json([
            'success' => true,
            'data' => $adjustments
        ], 200);
    }

    // Proses Submit Penyesuaian Stok dari Mobile
    public function adjustStock(Request $request)
    {
        $validated = $request->validate([
            'date'         => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_sku'  => 'required|exists:products,sku',
            'type'         => 'required|in:Penambahan,Pengurangan',
            'quantity'     => 'required|integer|min:1',
            'reason'       => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $product = Product::where('sku', $validated['product_sku'])->lockForUpdate()->first();
            $stockRecord = ProductStock::firstOrCreate(
                ['warehouse_id' => $validated['warehouse_id'], 'product_sku' => $validated['product_sku']],
                ['quantity' => 0]
            );

            if ($validated['type'] == 'Pengurangan' && $stockRecord->quantity < $validated['quantity']) {
                return response()->json(['success' => false, 'message' => 'Stok gudang tidak mencukupi.'], 400);
            }

            $totalValue = $validated['quantity'] * $product->hpp;

            // 1. Simpan Riwayat
            $adj = StockAdjustment::create([
                'date'         => $validated['date'],
                'warehouse_id' => $validated['warehouse_id'],
                'product_sku'  => $validated['product_sku'],
                'type'         => $validated['type'],
                'quantity'     => $validated['quantity'],
                'reason'       => '[MOBILE] ' . $validated['reason'], // Tandai bahwa ini dari HP
                'total_value'  => $totalValue,
            ]);

            // 2. Update Stok
            if ($validated['type'] == 'Penambahan') { $stockRecord->quantity += $validated['quantity']; }
            else { $stockRecord->quantity -= $validated['quantity']; }
            $stockRecord->save();

            // 3. Catat Jurnal
            if ($totalValue > 0) {
                $invAcc = Account::where('code', 'LIKE', '13%')->where('is_header', false)->first() ?? Account::where('code', 'LIKE', '16%')->where('is_header', false)->first();
                $cogsAcc = Account::where('code', 'LIKE', '60%')->where('is_header', false)->first() ?? Account::where('code', 'LIKE', '5%')->where('is_header', false)->first();

                if ($invAcc && $cogsAcc) {
                    $jCode = "ADJ-" . $product->sku . "-" . Carbon::now()->format('His');
                    $journal = Journal::create([
                        'date'         => $validated['date'],
                        'description'  => "Stok Opname (Mobile) ID:".$validated['warehouse_id']." - ".$validated['reason']." (".$jCode.")",
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
            return response()->json(['success' => true, 'message' => 'Stok berhasil disesuaikan secara real-time.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem.', 'error' => $e->getMessage()], 500);
        }
    }

    // --- TAMBAHKAN FUNGSI INI DI DALAM Api/InventoryController.php ---
    public function transferStock(Request $request)
    {
        $validated = $request->validate([
            'date'              => 'required|date',
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id'   => 'required|exists:warehouses,id|different:from_warehouse_id',
            'product_sku'       => 'required|exists:products,sku',
            'quantity'          => 'required|integer|min:1',
            'notes'             => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $product = Product::where('sku', $validated['product_sku'])->lockForUpdate()->first();

            // 1. Cek & Potong Stok Gudang Asal
            $sourceStock = ProductStock::firstOrCreate(
                ['warehouse_id' => $validated['from_warehouse_id'], 'product_sku' => $validated['product_sku']],
                ['quantity' => 0]
            );
            if ($sourceStock->quantity < $validated['quantity']) {
                return response()->json(['success' => false, 'message' => 'Gagal: Stok di Gudang Asal tidak mencukupi.'], 400);
            }
            $sourceStock->quantity -= $validated['quantity'];
            $sourceStock->save();

            // 2. Tambah Stok Gudang Tujuan
            $destStock = ProductStock::firstOrCreate(
                ['warehouse_id' => $validated['to_warehouse_id'], 'product_sku' => $validated['product_sku']],
                ['quantity' => 0]
            );
            $destStock->quantity += $validated['quantity'];
            $destStock->save();

            // 3. Catat Riwayat Mutasi Fisik (Tanpa Jurnal / Nilai 0)
            $sjCode = "TRF-" . Carbon::now()->format('His');
            $reasonOut = "[MUTASI KELUAR] Ke Gudang ID: {$validated['to_warehouse_id']} (SJ: {$sjCode}) - " . ($validated['notes'] ?? '');
            $reasonIn  = "[MUTASI MASUK] Dari Gudang ID: {$validated['from_warehouse_id']} (SJ: {$sjCode}) - " . ($validated['notes'] ?? '');

            StockAdjustment::create([
                'date'         => $validated['date'],
                'warehouse_id' => $validated['from_warehouse_id'],
                'product_sku'  => $validated['product_sku'],
                'type'         => 'Pengurangan',
                'quantity'     => $validated['quantity'],
                'reason'       => '[MOBILE] ' . $reasonOut,
                'total_value'  => 0,
            ]);

            StockAdjustment::create([
                'date'         => $validated['date'],
                'warehouse_id' => $validated['to_warehouse_id'],
                'product_sku'  => $validated['product_sku'],
                'type'         => 'Penambahan',
                'quantity'     => $validated['quantity'],
                'reason'       => '[MOBILE] ' . $reasonIn,
                'total_value'  => 0,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Mutasi antar gudang berhasil dicatat!'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal memproses mutasi.', 'error' => $e->getMessage()], 500);
        }
    }
}
