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
}
