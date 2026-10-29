<?php

// namespace App\Http\Controllers;

// use App\Models\Purchase;
// use App\Models\PurchaseItem;
// use App\Models\Product;
// use App\Models\Account;
// use App\Models\Journal;
// use App\Models\JournalEntry;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Carbon\Carbon;

// class PurchaseController extends Controller
// {
//     public function index(Request $request)
//     {
//         $query = Purchase::with(['contact', 'items.product', 'paymentAccount'])->orderBy('date', 'desc')->orderBy('id', 'desc');

//         if ($request->has('status') && $request->status != '') {
//             $query->where('status', $request->status);
//         }

//         $purchases = $query->get();
//         return view('purchases.index', compact('purchases'));
//     }

//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'date'         => 'required|date',
//             'due_date'     => 'nullable|date',
//             'reference_no' => 'nullable|string|max:50',
//             'contact_id'   => 'required|exists:contacts,id',
//             'notes'        => 'nullable|string',
//             'items'        => 'required|array|min:1',
//             'items.*.product_sku' => 'required|exists:products,sku',
//             'items.*.quantity'    => 'required|integer|min:1',
//             'items.*.price'       => 'required|numeric|min:0',
//         ]);

//         DB::beginTransaction();

//         try {
//             $purchase = Purchase::create([
//                 'date'         => $validated['date'],
//                 'due_date'     => $validated['due_date'] ?? $validated['date'],
//                 'reference_no' => $validated['reference_no'],
//                 'contact_id'   => $validated['contact_id'],
//                 'status'       => 'Pesanan',
//                 'notes'        => $validated['notes'] ?? '',
//                 'total'        => 0,
//             ]);

//             $grandTotal = 0;

//             foreach ($validated['items'] as $item) {
//                 $subTotal = $item['quantity'] * $item['price'];
//                 $grandTotal += $subTotal;

//                 PurchaseItem::create([
//                     'purchase_id' => $purchase->id,
//                     'product_sku' => $item['product_sku'],
//                     'quantity'    => $item['quantity'],
//                     'price'       => $item['price'],
//                     'sub_total'   => $subTotal,
//                 ]);
//             }

//             $purchase->update(['total' => $grandTotal]);

//             DB::commit();
//             return redirect()->route('purchases.index')->with('success', 'Pesanan Pembelian (PO) berhasil dibuat');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pesanan pembelian');
//         }
//     }

//     public function receive($id)
//     {
//         DB::beginTransaction();

//         try {
//             $purchase = Purchase::with('items')->findOrFail($id);

//             if ($purchase->status != 'Pesanan') {
//                 return redirect()->back()->with('error', 'Pesanan ini sudah diproses atau dibatalkan');
//             }

//             $purchase->update(['status' => 'Pengiriman']);

//             foreach ($purchase->items as $item) {
//                 $product = Product::where('sku', $item->product_sku)->lockForUpdate()->first();

//                 // Rumus Moving Average HPP
//                 $oldTotalValue = $product->stock * $product->hpp;
//                 $newIncomingValue = $item->quantity * $item->price;
//                 $newTotalStock = $product->stock + $item->quantity;

//                 $newHPP = $newTotalStock > 0 ? ($oldTotalValue + $newIncomingValue) / $newTotalStock : 0;

//                 $product->update([
//                     'stock'     => $newTotalStock,
//                     'hpp'       => $newHPP,
//                     'buy_price' => $item->price,
//                 ]);
//             }

//             DB::commit();
//             return redirect()->back()->with('success', 'Barang berhasil diterima, Stok & HPP telah diperbarui otomatis!');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return redirect()->back()->with('error', 'Gagal memproses penerimaan barang');
//         }
//     }

//     public function bill(Request $request, $id)
//     {
//         $validated = $request->validate([
//             'due_date'        => 'required|date',
//             'payment_account' => 'required|exists:accounts,code',
//         ]);

//         DB::beginTransaction();

//         try {
//             $purchase = Purchase::findOrFail($id);

//             if ($purchase->status != 'Pengiriman') {
//                 return redirect()->back()->with('error', 'Hanya dokumen berstatus Pengiriman yang dapat ditagih');
//             }

//             $purchase->update([
//                 'status'               => 'Tagihan',
//                 'due_date'             => $validated['due_date'],
//                 'payment_account_code' => $validated['payment_account']
//             ]);

//             // Cari Akun Persediaan (13%)
//             $inventoryAccount = Account::where('code', 'LIKE', '13%')->where('is_header', false)->first();
            
//             // Cari Akun Bank
//             $bankAccount = Account::where('code', $validated['payment_account'])->where('is_header', false)->first();

//             if (!$inventoryAccount || !$bankAccount) {
//                 DB::rollBack();
//                 return redirect()->back()->with('error', 'Akun Persediaan (13) atau Akun Bank tidak valid di sistem');
//             }

//             $journalCode = "BILL-" . Carbon::now()->format('Ymd') . "-" . $purchase->id;
            
//             $journal = Journal::create([
//                 'date'         => Carbon::now(),
//                 'description'  => "Pembayaran Tunai PO #" . $purchase->id . " (Ref: " . $purchase->reference_no . ") - " . $journalCode,
//                 'reference_no' => $journalCode,
//             ]);

//             JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $inventoryAccount->code, 'debit' => $purchase->total, 'credit' => 0]);
//             JournalEntry::create(['journal_id' => $journal->id, 'account_code' => $bankAccount->code, 'debit' => 0, 'credit' => $purchase->total]);

//             DB::commit();
//             return redirect()->back()->with('success', 'Pembelian berhasil dilunasi & Jurnal Kas Keluar otomatis tercatat!');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return redirect()->back()->with('error', 'Gagal membuat tagihan & jurnal');
//         }
//     }

//     public function showDeliveries()
//     {
//         $deliveries = Purchase::with('contact')->where('status', 'Pengiriman')->orderBy('date', 'desc')->get();
//         $pendingOrders = Purchase::with(['contact', 'items.product'])->where('status', 'Pesanan')->get();
//         return view('purchases.delivery', compact('deliveries', 'pendingOrders'));
//     }

//     public function showBills()
//     {
//         $bills = Purchase::with(['contact', 'paymentAccount'])->where('status', 'Tagihan')->orderBy('date', 'desc')->get();
//         $readyDeliveries = Purchase::with(['contact', 'items.product'])->where('status', 'Pengiriman')->get();
//         $banks = Account::where(function($query) { $query->where('code', 'LIKE', '10%')->orWhere('code', 'LIKE', '11%'); })->where('is_header', false)->get();
//         return view('purchases.bill', compact('bills', 'readyDeliveries', 'banks'));
//     }
// }

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Account;
use App\Models\Journal;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with(['contact', 'items.product', 'paymentAccount'])->orderBy('date', 'desc')->orderBy('id', 'desc');

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $purchases = $query->get();
        return view('purchases.index', compact('purchases'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date'         => 'required|date',
            'due_date'     => 'nullable|date',
            'reference_no' => 'nullable|string|max:50',
            'contact_id'   => 'required|exists:contacts,id',
            'notes'        => 'nullable|string',
            'items'        => 'required|array|min:1',
            'items.*.product_sku' => 'required|exists:products,sku',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.price'       => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $purchase = Purchase::create([
                'date'         => $validated['date'],
                'due_date'     => $validated['due_date'] ?? $validated['date'],
                'reference_no' => $validated['reference_no'],
                'contact_id'   => $validated['contact_id'],
                'status'       => 'Pesanan',
                'notes'        => $validated['notes'] ?? '',
                'total'        => 0,
            ]);

            $grandTotal = 0;

            foreach ($validated['items'] as $item) {
                $subTotal = $item['quantity'] * $item['price'];
                $grandTotal += $subTotal;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_sku' => $item['product_sku'],
                    'quantity'    => $item['quantity'],
                    'price'       => $item['price'],
                    'sub_total'   => $subTotal,
                ]);
            }

            $purchase->update(['total' => $grandTotal]);

            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'Pesanan Pembelian (PO) berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pesanan pembelian');
        }
    }

    // TAHAP 2: TERIMA BARANG -> STOK BERTAMBAH & MUNCUL HUTANG USAHA
    public function receive($id)
    {
        DB::beginTransaction();

        try {
            $purchase = Purchase::with('items')->findOrFail($id);

            if ($purchase->status != 'Pesanan') {
                return redirect()->back()->with('error', 'Pesanan ini sudah diproses atau dibatalkan');
            }

            $purchase->update(['status' => 'Pengiriman']);

            foreach ($purchase->items as $item) {
                $product = Product::where('sku', $item->product_sku)->lockForUpdate()->first();

                // Rumus Moving Average HPP
                $oldTotalValue = $product->stock * $product->hpp;
                $newIncomingValue = $item->quantity * $item->price;
                $newTotalStock = $product->stock + $item->quantity;

                $newHPP = $newTotalStock > 0 ? ($oldTotalValue + $newIncomingValue) / $newTotalStock : 0;

                $product->update([
                    'stock'     => $newTotalStock,
                    'hpp'       => $newHPP,
                    'buy_price' => $item->price,
                ]);
            }

            // Cari Akun Persediaan (13%) dan Akun Hutang Usaha (30% / 3%)
            $inventoryAccount = Account::where('code', 'LIKE', '13%')->where('is_header', false)->first();
            
            $payableAccount = Account::where('code', 'LIKE', '30%')->where('is_header', false)->first();
            if (!$payableAccount) {
                $payableAccount = Account::where('code', 'LIKE', '3%')->where('is_header', false)->first();
            }

            if (!$inventoryAccount || !$payableAccount) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Akun Persediaan (13) atau Akun Hutang Usaha (awalan 3) belum ditemukan di CoA!');
            }

            // Buat Jurnal Penerimaan Barang (Debit: Persediaan, Kredit: Hutang Usaha)
            $journalCode = "RCV-P-" . Carbon::now()->format('Ymd') . "-" . $purchase->id;
            $journal = Journal::create([
                'date'         => Carbon::now(),
                'description'  => "Penerimaan Barang PO #" . $purchase->id . " (Ref: " . $purchase->reference_no . ") - Muncul Hutang",
                'reference_no' => $journalCode,
            ]);

            JournalEntry::create([
                'journal_id'   => $journal->id,
                'account_code' => $inventoryAccount->code,
                'debit'        => $purchase->total,
                'credit'       => 0
            ]);
            JournalEntry::create([
                'journal_id'   => $journal->id,
                'account_code' => $payableAccount->code,
                'debit'        => 0,
                'credit'       => $purchase->total
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Barang diterima! Stok, HPP, dan Jurnal Hutang Usaha berhasil dicatat di CoA.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses penerimaan barang');
        }
    }

    // TAHAP 3: BAYAR TAGIHAN -> HUTANG USAHA BERKURANG (LUNAS) & BANK BERKURANG
    public function bill(Request $request, $id)
    {
        $validated = $request->validate([
            'due_date'        => 'required|date',
            'payment_account' => 'required|exists:accounts,code',
        ]);

        DB::beginTransaction();

        try {
            $purchase = Purchase::findOrFail($id);

            if ($purchase->status != 'Pengiriman') {
                return redirect()->back()->with('error', 'Hanya dokumen berstatus Pengiriman yang dapat dilunasi');
            }

            $purchase->update([
                'status'               => 'Tagihan',
                'due_date'             => $validated['due_date'],
                'payment_account_code' => $validated['payment_account']
            ]);

            // Cari Akun Hutang Usaha (30% / 3%) untuk didebit (dilunasi)
            $payableAccount = Account::where('code', 'LIKE', '30%')->where('is_header', false)->first();
            if (!$payableAccount) {
                $payableAccount = Account::where('code', 'LIKE', '3%')->where('is_header', false)->first();
            }

            // Cari Akun Bank pembayar
            $bankAccount = Account::where('code', $validated['payment_account'])->where('is_header', false)->first();

            if (!$payableAccount || !$bankAccount) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Akun Hutang Usaha (awalan 3) atau Akun Bank tidak valid di sistem');
            }

            $journalCode = "BILL-" . Carbon::now()->format('Ymd') . "-" . $purchase->id;
            
            $journal = Journal::create([
                'date'         => $validated['due_date'],
                'description'  => "Pelunasan Hutang PO #" . $purchase->id . " (Ref: " . $purchase->reference_no . ") - " . $journalCode,
                'reference_no' => $journalCode,
            ]);

            // Debit: Hutang Usaha (Hutang Berkurang/Lunas) | Kredit: Kas/Bank (Uang Keluar)
            JournalEntry::create([
                'journal_id'   => $journal->id,
                'account_code' => $payableAccount->code,
                'debit'        => $purchase->total,
                'credit'       => 0
            ]);
            JournalEntry::create([
                'journal_id'   => $journal->id,
                'account_code' => $bankAccount->code,
                'debit'        => 0,
                'credit'       => $purchase->total
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Tagihan Pembelian lunas! Saldo Hutang telah ditutup dan dipotong dari Bank.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membuat tagihan & jurnal');
        }
    }

    public function showDeliveries()
    {
        $deliveries = Purchase::with('contact')->where('status', 'Pengiriman')->orderBy('date', 'desc')->get();
        $pendingOrders = Purchase::with(['contact', 'items.product'])->where('status', 'Pesanan')->get();
        return view('purchases.delivery', compact('deliveries', 'pendingOrders'));
    }

    public function showBills()
    {
        $bills = Purchase::with(['contact', 'paymentAccount'])->where('status', 'Tagihan')->orderBy('date', 'desc')->get();
        $readyDeliveries = Purchase::with(['contact', 'items.product'])->where('status', 'Pengiriman')->get();
        $banks = Account::where(function($query) { $query->where('code', 'LIKE', '10%')->orWhere('code', 'LIKE', '11%'); })->where('is_header', false)->get();
        return view('purchases.bill', compact('bills', 'readyDeliveries', 'banks'));
    }
}