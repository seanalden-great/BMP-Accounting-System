<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Account;
use App\Models\Journal;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['contact', 'items.product', 'paymentAccount'])->orderBy('date', 'desc')->orderBy('id', 'desc');

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $sales = $query->get();
        return view('sales.index', compact('sales'));
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
            $sale = Sale::create([
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

                SaleItem::create([
                    'sale_id'     => $sale->id,
                    'product_sku' => $item['product_sku'],
                    'quantity'    => $item['quantity'],
                    'price'       => $item['price'],
                    'sub_total'   => $subTotal,
                ]);
            }

            $sale->update(['total' => $grandTotal]);

            DB::commit();
            return redirect()->route('sales.index')->with('success', 'Pesanan Penjualan berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pesanan penjualan');
        }
    }

    public function deliver($id)
    {
        DB::beginTransaction();

        try {
            $sale = Sale::with('items')->findOrFail($id);

            if ($sale->status != 'Pesanan') {
                return redirect()->back()->with('error', 'Status pesanan tidak valid untuk dikirim');
            }

            $sale->update(['status' => 'Pengiriman']);

            foreach ($sale->items as $item) {
                $product = Product::where('sku', $item->product_sku)->lockForUpdate()->first();

                if ($product->stock < $item->quantity) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Stok tidak mencukupi untuk produk: ' . $product->name);
                }

                $product->decrement('stock', $item->quantity);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Barang berhasil dikirim dan stok gudang dikurangi!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses pengiriman barang');
        }
    }

    public function invoice(Request $request, $id)
    {
        $validated = $request->validate([
            'payment_account' => 'required|exists:accounts,code',
        ]);

        DB::beginTransaction();

        try {
            $sale = Sale::with('items')->findOrFail($id);

            if ($sale->status != 'Pengiriman') {
                return redirect()->back()->with('error', 'Hanya dokumen berstatus Pengiriman yang dapat ditagih');
            }

            $sale->update([
                'status'               => 'Tagihan',
                'payment_account_code' => $validated['payment_account']
            ]);

            // Hitung Total HPP
            $totalHPP = 0;
            foreach ($sale->items as $item) {
                $prod = Product::where('sku', $item->product_sku)->first();
                if($prod) {
                    $totalHPP += ($item->quantity * $prod->hpp);
                }
            }

            // Cari Akun-akun yang diperlukan
            $bankAcc = Account::where('code', $validated['payment_account'])->where('is_header', false)->first();
            
            $revAcc = Account::where('code', 'LIKE', '50%')->where('is_header', false)->first();
            if (!$revAcc) $revAcc = Account::where('code', 'LIKE', '4%')->where('is_header', false)->first();

            $cogsAcc = Account::where('code', 'LIKE', '60%')->where('is_header', false)->first();
            if (!$cogsAcc) $cogsAcc = Account::where('code', 'LIKE', '5%')->where('is_header', false)->first();
            
            $invAcc = Account::where('code', 'LIKE', '13%')->where('is_header', false)->first();
            if (!$invAcc) $invAcc = Account::where('code', 'LIKE', '16%')->where('is_header', false)->first();

            if (!$bankAcc || !$revAcc || !$cogsAcc || !$invAcc) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Konfigurasi Akun Standar (Bank, Pendapatan, HPP, Persediaan) belum lengkap di CoA');
            }

            $journalCode = "INV-S-" . Carbon::now()->format('Ymd') . "-" . $sale->id;
            
            $j1 = Journal::create([
                'date'         => Carbon::now(),
                'description'  => "Penerimaan Dana Penjualan SO #" . $sale->id . " (" . $journalCode . ")",
                'reference_no' => $journalCode,
            ]);

            // Kas Masuk (D) vs Pendapatan (K)
            JournalEntry::create(['journal_id' => $j1->id, 'account_code' => $bankAcc->code, 'debit' => $sale->total, 'credit' => 0]);
            JournalEntry::create(['journal_id' => $j1->id, 'account_code' => $revAcc->code, 'debit' => 0, 'credit' => $sale->total]);

            // HPP (D) vs Persediaan (K)
            if ($totalHPP > 0) {
                $j2 = Journal::create([
                    'date'         => Carbon::now(),
                    'description'  => "HPP Penjualan SO #" . $sale->id . " (" . $journalCode . ")",
                    'reference_no' => $journalCode,
                ]);
                JournalEntry::create(['journal_id' => $j2->id, 'account_code' => $cogsAcc->code, 'debit' => $totalHPP, 'credit' => 0]);
                JournalEntry::create(['journal_id' => $j2->id, 'account_code' => $invAcc->code, 'debit' => 0, 'credit' => $totalHPP]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Tagihan lunas! Dana masuk ke Bank & Jurnal berhasil diterbitkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses Invoice');
        }
    }

    public function showDeliveries()
    {
        $deliveries = Sale::with('contact')->where('status', 'Pengiriman')->orderBy('date', 'desc')->get();
        $pendingOrders = Sale::with(['contact', 'items.product'])->where('status', 'Pesanan')->get();
        return view('sales.delivery', compact('deliveries', 'pendingOrders'));
    }

    public function showInvoices()
    {
        $invoices = Sale::with(['contact', 'paymentAccount'])->where('status', 'Tagihan')->orderBy('date', 'desc')->get();
        $readyDeliveries = Sale::with(['contact', 'items.product'])->where('status', 'Pengiriman')->get();
        $banks = Account::where(function($query) { $query->where('code', 'LIKE', '10%')->orWhere('code', 'LIKE', '11%'); })->where('is_header', false)->get();
        return view('sales.invoice', compact('invoices', 'readyDeliveries', 'banks'));
    }
}