<?php

// namespace App\Http\Controllers;

// use App\Models\Sale;
// use App\Models\SaleItem;
// use App\Models\Product;
// use App\Models\Account;
// use App\Models\Journal;
// use App\Models\JournalEntry;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Carbon\Carbon;

// class SaleController extends Controller
// {
//     public function index(Request $request)
//     {
//         $query = Sale::with(['contact', 'items.product', 'paymentAccount'])->orderBy('date', 'desc')->orderBy('id', 'desc');

//         if ($request->has('status') && $request->status != '') {
//             $query->where('status', $request->status);
//         }

//         $sales = $query->get();
//         return view('sales.index', compact('sales'));
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
//             $sale = Sale::create([
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

//                 SaleItem::create([
//                     'sale_id'     => $sale->id,
//                     'product_sku' => $item['product_sku'],
//                     'quantity'    => $item['quantity'],
//                     'price'       => $item['price'],
//                     'sub_total'   => $subTotal,
//                 ]);
//             }

//             $sale->update(['total' => $grandTotal]);

//             DB::commit();
//             return redirect()->route('sales.index')->with('success', 'Pesanan Penjualan berhasil dibuat');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pesanan penjualan');
//         }
//     }

//     public function deliver($id)
//     {
//         DB::beginTransaction();

//         try {
//             $sale = Sale::with('items')->findOrFail($id);

//             if ($sale->status != 'Pesanan') {
//                 return redirect()->back()->with('error', 'Status pesanan tidak valid untuk dikirim');
//             }

//             $sale->update(['status' => 'Pengiriman']);

//             foreach ($sale->items as $item) {
//                 $product = Product::where('sku', $item->product_sku)->lockForUpdate()->first();

//                 if ($product->stock < $item->quantity) {
//                     DB::rollBack();
//                     return redirect()->back()->with('error', 'Stok tidak mencukupi untuk produk: ' . $product->name);
//                 }

//                 $product->decrement('stock', $item->quantity);
//             }

//             DB::commit();
//             return redirect()->back()->with('success', 'Barang berhasil dikirim dan stok gudang dikurangi!');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return redirect()->back()->with('error', 'Gagal memproses pengiriman barang');
//         }
//     }

//     public function invoice(Request $request, $id)
//     {
//         $validated = $request->validate([
//             'payment_account' => 'required|exists:accounts,code',
//         ]);

//         DB::beginTransaction();

//         try {
//             $sale = Sale::with('items')->findOrFail($id);

//             if ($sale->status != 'Pengiriman') {
//                 return redirect()->back()->with('error', 'Hanya dokumen berstatus Pengiriman yang dapat ditagih');
//             }

//             $sale->update([
//                 'status'               => 'Tagihan',
//                 'payment_account_code' => $validated['payment_account']
//             ]);

//             // Hitung Total HPP
//             $totalHPP = 0;
//             foreach ($sale->items as $item) {
//                 $prod = Product::where('sku', $item->product_sku)->first();
//                 if($prod) {
//                     $totalHPP += ($item->quantity * $prod->hpp);
//                 }
//             }

//             // Cari Akun-akun yang diperlukan
//             $bankAcc = Account::where('code', $validated['payment_account'])->where('is_header', false)->first();

//             $revAcc = Account::where('code', 'LIKE', '50%')->where('is_header', false)->first();
//             if (!$revAcc) $revAcc = Account::where('code', 'LIKE', '4%')->where('is_header', false)->first();

//             $cogsAcc = Account::where('code', 'LIKE', '60%')->where('is_header', false)->first();
//             if (!$cogsAcc) $cogsAcc = Account::where('code', 'LIKE', '5%')->where('is_header', false)->first();

//             $invAcc = Account::where('code', 'LIKE', '13%')->where('is_header', false)->first();
//             if (!$invAcc) $invAcc = Account::where('code', 'LIKE', '16%')->where('is_header', false)->first();

//             if (!$bankAcc || !$revAcc || !$cogsAcc || !$invAcc) {
//                 DB::rollBack();
//                 return redirect()->back()->with('error', 'Konfigurasi Akun Standar (Bank, Pendapatan, HPP, Persediaan) belum lengkap di CoA');
//             }

//             $journalCode = "INV-S-" . Carbon::now()->format('Ymd') . "-" . $sale->id;

//             $j1 = Journal::create([
//                 'date'         => Carbon::now(),
//                 'description'  => "Penerimaan Dana Penjualan SO #" . $sale->id . " (" . $journalCode . ")",
//                 'reference_no' => $journalCode,
//             ]);

//             // Kas Masuk (D) vs Pendapatan (K)
//             JournalEntry::create(['journal_id' => $j1->id, 'account_code' => $bankAcc->code, 'debit' => $sale->total, 'credit' => 0]);
//             JournalEntry::create(['journal_id' => $j1->id, 'account_code' => $revAcc->code, 'debit' => 0, 'credit' => $sale->total]);

//             // HPP (D) vs Persediaan (K)
//             if ($totalHPP > 0) {
//                 $j2 = Journal::create([
//                     'date'         => Carbon::now(),
//                     'description'  => "HPP Penjualan SO #" . $sale->id . " (" . $journalCode . ")",
//                     'reference_no' => $journalCode,
//                 ]);
//                 JournalEntry::create(['journal_id' => $j2->id, 'account_code' => $cogsAcc->code, 'debit' => $totalHPP, 'credit' => 0]);
//                 JournalEntry::create(['journal_id' => $j2->id, 'account_code' => $invAcc->code, 'debit' => 0, 'credit' => $totalHPP]);
//             }

//             DB::commit();
//             return redirect()->back()->with('success', 'Tagihan lunas! Dana masuk ke Bank & Jurnal berhasil diterbitkan.');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return redirect()->back()->with('error', 'Gagal memproses Invoice');
//         }
//     }

//     public function showDeliveries()
//     {
//         $deliveries = Sale::with('contact')->where('status', 'Pengiriman')->orderBy('date', 'desc')->get();
//         $pendingOrders = Sale::with(['contact', 'items.product'])->where('status', 'Pesanan')->get();
//         return view('sales.delivery', compact('deliveries', 'pendingOrders'));
//     }

//     public function showInvoices()
//     {
//         $invoices = Sale::with(['contact', 'paymentAccount'])->where('status', 'Tagihan')->orderBy('date', 'desc')->get();
//         $readyDeliveries = Sale::with(['contact', 'items.product'])->where('status', 'Pengiriman')->get();
//         $banks = Account::where(function($query) { $query->where('code', 'LIKE', '10%')->orWhere('code', 'LIKE', '11%'); })->where('is_header', false)->get();
//         return view('sales.invoice', compact('invoices', 'readyDeliveries', 'banks'));
//     }
// }

// namespace App\Http\Controllers;

// use App\Models\Sale;
// use App\Models\SaleItem;
// use App\Models\Product;
// use App\Models\Account;
// use App\Models\Journal;
// use App\Models\JournalEntry;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Carbon\Carbon;

// class SaleController extends Controller
// {
//     public function index(Request $request)
//     {
//         $query = Sale::with(['contact', 'items.product', 'paymentAccount'])->orderBy('date', 'desc')->orderBy('id', 'desc');

//         if ($request->has('status') && $request->status != '') {
//             $query->where('status', $request->status);
//         }

//         $sales = $query->get();
//         return view('sales.index', compact('sales'));
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
//             $sale = Sale::create([
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

//                 SaleItem::create([
//                     'sale_id'     => $sale->id,
//                     'product_sku' => $item['product_sku'],
//                     'quantity'    => $item['quantity'],
//                     'price'       => $item['price'],
//                     'sub_total'   => $subTotal,
//                 ]);
//             }

//             $sale->update(['total' => $grandTotal]);

//             DB::commit();
//             return redirect()->route('sales.index')->with('success', 'Pesanan Penjualan berhasil dibuat');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pesanan penjualan');
//         }
//     }

//     // TAHAP 2: KIRIM BARANG -> STOK BERKURANG, MUNCUL PIUTANG USAHA, PENDAPATAN & HPP
//     public function deliver($id)
//     {
//         DB::beginTransaction();

//         try {
//             $sale = Sale::with('items')->findOrFail($id);

//             if ($sale->status != 'Pesanan') {
//                 return redirect()->back()->with('error', 'Status pesanan tidak valid untuk dikirim');
//             }

//             $sale->update(['status' => 'Pengiriman']);

//             $totalHPP = 0;

//             foreach ($sale->items as $item) {
//                 $product = Product::where('sku', $item->product_sku)->lockForUpdate()->first();

//                 if ($product->stock < $item->quantity) {
//                     DB::rollBack();
//                     return redirect()->back()->with('error', 'Stok tidak mencukupi untuk produk: ' . $product->name);
//                 }

//                 $totalHPP += ($item->quantity * $product->hpp);
//                 $product->decrement('stock', $item->quantity);
//             }

//             // Cari Akun Piutang Usaha (12%), Pendapatan (50%), HPP (60%), dan Persediaan (13%)
//             $arAcc = Account::where('code', 'LIKE', '12%')->where('is_header', false)->first();

//             $revAcc = Account::where('code', 'LIKE', '50%')->where('is_header', false)->first();
//             if (!$revAcc) $revAcc = Account::where('code', 'LIKE', '4%')->where('is_header', false)->first();

//             $cogsAcc = Account::where('code', 'LIKE', '60%')->where('is_header', false)->first();
//             if (!$cogsAcc) $cogsAcc = Account::where('code', 'LIKE', '5%')->where('is_header', false)->first();

//             $invAcc = Account::where('code', 'LIKE', '13%')->where('is_header', false)->first();
//             if (!$invAcc) $invAcc = Account::where('code', 'LIKE', '16%')->where('is_header', false)->first();

//             if (!$arAcc || !$revAcc || !$cogsAcc || !$invAcc) {
//                 DB::rollBack();
//                 return redirect()->back()->with('error', 'Konfigurasi Akun (Piutang 12%, Persediaan 13%, Pendapatan 50%, HPP 60%) belum lengkap di CoA!');
//             }

//             $journalCode = "DLV-S-" . Carbon::now()->format('Ymd') . "-" . $sale->id;

//             // 1. Jurnal Pengakuan Piutang & Pendapatan (Debit: Piutang Usaha, Kredit: Pendapatan)
//             $j1 = Journal::create([
//                 'date'         => Carbon::now(),
//                 'description'  => "Pengiriman Penjualan SO #" . $sale->id . " (Ref: " . $sale->reference_no . ") - Muncul Piutang",
//                 'reference_no' => $journalCode,
//             ]);
//             JournalEntry::create(['journal_id' => $j1->id, 'account_code' => $arAcc->code, 'debit' => $sale->total, 'credit' => 0]);
//             JournalEntry::create(['journal_id' => $j1->id, 'account_code' => $revAcc->code, 'debit' => 0, 'credit' => $sale->total]);

//             // 2. Jurnal HPP & Pengurangan Nilai Persediaan (Debit: HPP, Kredit: Persediaan)
//             if ($totalHPP > 0) {
//                 $j2 = Journal::create([
//                     'date'         => Carbon::now(),
//                     'description'  => "HPP Pengiriman SO #" . $sale->id . " (" . $journalCode . ")",
//                     'reference_no' => $journalCode,
//                 ]);
//                 JournalEntry::create(['journal_id' => $j2->id, 'account_code' => $cogsAcc->code, 'debit' => $totalHPP, 'credit' => 0]);
//                 JournalEntry::create(['journal_id' => $j2->id, 'account_code' => $invAcc->code, 'debit' => 0, 'credit' => $totalHPP]);
//             }

//             DB::commit();
//             return redirect()->back()->with('success', 'Barang dikirim! Stok dikurangi & Jurnal Piutang Usaha otomatis tercatat di CoA.');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return redirect()->back()->with('error', 'Gagal memproses pengiriman barang');
//         }
//     }

//     // TAHAP 3: PELUNASAN INVOICE -> KAS/BANK BERTAMBAH & PIUTANG USAHA BERKURANG (LUNAS)
//     public function invoice(Request $request, $id)
//     {
//         $validated = $request->validate([
//             'payment_account' => 'required|exists:accounts,code',
//         ]);

//         DB::beginTransaction();

//         try {
//             $sale = Sale::with('items')->findOrFail($id);

//             if ($sale->status != 'Pengiriman') {
//                 return redirect()->back()->with('error', 'Hanya dokumen berstatus Pengiriman yang dapat dilunasi');
//             }

//             $sale->update([
//                 'status'               => 'Tagihan',
//                 'payment_account_code' => $validated['payment_account']
//             ]);

//             // Cari Akun Bank Penerima & Akun Piutang Usaha (12%)
//             $bankAcc = Account::where('code', $validated['payment_account'])->where('is_header', false)->first();
//             $arAcc   = Account::where('code', 'LIKE', '12%')->where('is_header', false)->first();

//             if (!$bankAcc || !$arAcc) {
//                 DB::rollBack();
//                 return redirect()->back()->with('error', 'Akun Kas/Bank atau Akun Piutang Usaha (12%) tidak ditemukan di CoA');
//             }

//             $journalCode = "INV-S-" . Carbon::now()->format('Ymd') . "-" . $sale->id;

//             $j1 = Journal::create([
//                 'date'         => Carbon::now(),
//                 'description'  => "Pelunasan Piutang SO #" . $sale->id . " (Ref: " . $sale->reference_no . ") - " . $journalCode,
//                 'reference_no' => $journalCode,
//             ]);

//             // Debit: Kas/Bank (Uang Masuk) | Kredit: Piutang Usaha (Piutang Lunas/Berkurang)
//             JournalEntry::create(['journal_id' => $j1->id, 'account_code' => $bankAcc->code, 'debit' => $sale->total, 'credit' => 0]);
//             JournalEntry::create(['journal_id' => $j1->id, 'account_code' => $arAcc->code, 'debit' => 0, 'credit' => $sale->total]);

//             DB::commit();
//             return redirect()->back()->with('success', 'Invoice lunas! Dana masuk ke Bank dan saldo Piutang Usaha telah ditutup.');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return redirect()->back()->with('error', 'Gagal memproses Invoice');
//         }
//     }

//     public function showDeliveries()
//     {
//         $deliveries = Sale::with('contact')->where('status', 'Pengiriman')->orderBy('date', 'desc')->get();
//         $pendingOrders = Sale::with(['contact', 'items.product'])->where('status', 'Pesanan')->get();
//         return view('sales.delivery', compact('deliveries', 'pendingOrders'));
//     }

//     public function showInvoices()
//     {
//         $invoices = Sale::with(['contact', 'paymentAccount'])->where('status', 'Tagihan')->orderBy('date', 'desc')->get();
//         $readyDeliveries = Sale::with(['contact', 'items.product'])->where('status', 'Pengiriman')->get();
//         $banks = Account::where(function($query) { $query->where('code', 'LIKE', '10%')->orWhere('code', 'LIKE', '11%'); })->where('is_header', false)->get();
//         return view('sales.invoice', compact('invoices', 'readyDeliveries', 'banks'));
//     }
// }

// namespace App\Http\Controllers;

// use App\Models\Sale;
// use App\Models\SaleItem;
// use App\Models\Product;
// use App\Models\ProductStock;
// use App\Models\Account;
// use App\Models\Journal;
// use App\Models\JournalEntry;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Carbon\Carbon;

// class SaleController extends Controller
// {
//     public function index(Request $request)
//     {
//         $query = Sale::with(['contact', 'items.product', 'paymentAccount'])->orderBy('date', 'desc')->orderBy('id', 'desc');

//         if ($request->has('status') && $request->status != '') {
//             $query->where('status', $request->status);
//         }

//         $sales = $query->get();
//         return view('sales.index', compact('sales'));
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
//             $sale = Sale::create([
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

//                 SaleItem::create([
//                     'sale_id'     => $sale->id,
//                     'product_sku' => $item['product_sku'],
//                     'quantity'    => $item['quantity'],
//                     'price'       => $item['price'],
//                     'sub_total'   => $subTotal,
//                 ]);
//             }

//             $sale->update(['total' => $grandTotal]);

//             DB::commit();
//             return redirect()->route('sales.index')->with('success', 'Pesanan Penjualan berhasil dibuat');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pesanan penjualan');
//         }
//     }

//     // TAHAP 2: KIRIM BARANG -> STOK BERKURANG (OTOMATIS FIFO DARI GUDANG TERTUA), MUNCUL PIUTANG, PENDAPATAN & HPP
//     public function deliver($id)
//     {
//         DB::beginTransaction();

//         try {
//             $sale = Sale::with('items')->findOrFail($id);

//             if ($sale->status != 'Pesanan') {
//                 return redirect()->back()->with('error', 'Status pesanan tidak valid untuk dikirim');
//             }

//             $totalHPP = 0;
//             $deliveryLog = []; // Log untuk mencatat barang diambil dari gudang mana saja

//             foreach ($sale->items as $item) {
//                 $product = Product::where('sku', $item->product_sku)->lockForUpdate()->first();
//                 $neededQty = $item->quantity;

//                 // LOGIKA AUTO-FIFO: Ambil stok per gudang, diurutkan dari id terkecil (pertama kali masuk)
//                 $availableStocks = ProductStock::with('warehouse')
//                     ->where('product_sku', $item->product_sku)
//                     ->where('quantity', '>', 0)
//                     ->orderBy('id', 'asc')
//                     ->lockForUpdate()
//                     ->get();

//                 $totalAvailable = $availableStocks->sum('quantity');

//                 if ($totalAvailable < $neededQty) {
//                     DB::rollBack();
//                     return redirect()->back()->with('error', 'Stok gabungan seluruh gudang tidak mencukupi untuk produk: ' . $product->name);
//                 }

//                 $totalHPP += ($neededQty * $product->hpp);
//                 $takenNotes = [];

//                 // Lakukan pengurangan stok secara sekuensial (FIFO)
//                 foreach ($availableStocks as $stock) {
//                     if ($neededQty <= 0) break;

//                     $take = min($stock->quantity, $neededQty);

//                     // Kurangi stok di tabel product_stocks (Gudang Spesifik)
//                     $stock->quantity -= $take;
//                     $stock->save();

//                     $neededQty -= $take;

//                     $warehouseName = $stock->warehouse ? $stock->warehouse->name : 'Gudang Unknown';
//                     $takenNotes[] = "{$take} unit dari {$warehouseName}";
//                 }

//                 $deliveryLog[] = "{$product->name} (" . implode(', ', $takenNotes) . ")";
//             }

//             // Cari Akun Piutang Usaha (12%), Pendapatan (50%), HPP (60%), dan Persediaan (13%)
//             $arAcc = Account::where('code', 'LIKE', '12%')->where('is_header', false)->first();

//             $revAcc = Account::where('code', 'LIKE', '50%')->where('is_header', false)->first();
//             if (!$revAcc) $revAcc = Account::where('code', 'LIKE', '4%')->where('is_header', false)->first();

//             $cogsAcc = Account::where('code', 'LIKE', '60%')->where('is_header', false)->first();
//             if (!$cogsAcc) $cogsAcc = Account::where('code', 'LIKE', '5%')->where('is_header', false)->first();

//             $invAcc = Account::where('code', 'LIKE', '13%')->where('is_header', false)->first();
//             if (!$invAcc) $invAcc = Account::where('code', 'LIKE', '16%')->where('is_header', false)->first();

//             if (!$arAcc || !$revAcc || !$cogsAcc || !$invAcc) {
//                 DB::rollBack();
//                 return redirect()->back()->with('error', 'Konfigurasi Akun (Piutang 12%, Persediaan 13%, Pendapatan 50%, HPP 60%) belum lengkap di CoA!');
//             }

//             // Update status dan tempelkan log FIFO ke dalam kolom 'notes' agar staf bisa membacanya
//             $newNotes = trim($sale->notes . "\n[AUTO-FIFO] " . implode(' | ', $deliveryLog));
//             $sale->update([
//                 'status' => 'Pengiriman',
//                 'notes'  => $newNotes
//             ]);

//             $journalCode = "DLV-S-" . Carbon::now()->format('Ymd') . "-" . $sale->id;

//             // 1. Jurnal Pengakuan Piutang & Pendapatan (Debit: Piutang Usaha, Kredit: Pendapatan)
//             $j1 = Journal::create([
//                 'date'         => Carbon::now(),
//                 'description'  => "Pengiriman Penjualan SO #" . $sale->id . " (Ref: " . $sale->reference_no . ") - Muncul Piutang",
//                 'reference_no' => $journalCode,
//             ]);
//             JournalEntry::create(['journal_id' => $j1->id, 'account_code' => $arAcc->code, 'debit' => $sale->total, 'credit' => 0]);
//             JournalEntry::create(['journal_id' => $j1->id, 'account_code' => $revAcc->code, 'debit' => 0, 'credit' => $sale->total]);

//             // 2. Jurnal HPP & Pengurangan Nilai Persediaan (Debit: HPP, Kredit: Persediaan)
//             if ($totalHPP > 0) {
//                 $j2 = Journal::create([
//                     'date'         => Carbon::now(),
//                     'description'  => "HPP Pengiriman SO #" . $sale->id . " (" . $journalCode . ")",
//                     'reference_no' => $journalCode,
//                 ]);
//                 JournalEntry::create(['journal_id' => $j2->id, 'account_code' => $cogsAcc->code, 'debit' => $totalHPP, 'credit' => 0]);
//                 JournalEntry::create(['journal_id' => $j2->id, 'account_code' => $invAcc->code, 'debit' => 0, 'credit' => $totalHPP]);
//             }

//             DB::commit();
//             return redirect()->back()->with('success', 'Barang dikirim secara FIFO! Stok gudang dikurangi & Jurnal Piutang Usaha otomatis tercatat.');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return redirect()->back()->with('error', 'Gagal memproses pengiriman barang: ' . $e->getMessage());
//         }
//     }

//     public function invoice(Request $request, $id)
//     {
//         $validated = $request->validate([
//             'payment_account' => 'required|exists:accounts,code',
//         ]);

//         DB::beginTransaction();

//         try {
//             $sale = Sale::with('items')->findOrFail($id);

//             if ($sale->status != 'Pengiriman') {
//                 return redirect()->back()->with('error', 'Hanya dokumen berstatus Pengiriman yang dapat dilunasi');
//             }

//             $sale->update([
//                 'status'               => 'Tagihan',
//                 'payment_account_code' => $validated['payment_account']
//             ]);

//             $bankAcc = Account::where('code', $validated['payment_account'])->where('is_header', false)->first();
//             $arAcc   = Account::where('code', 'LIKE', '12%')->where('is_header', false)->first();

//             if (!$bankAcc || !$arAcc) {
//                 DB::rollBack();
//                 return redirect()->back()->with('error', 'Akun Kas/Bank atau Akun Piutang Usaha (12%) tidak ditemukan di CoA');
//             }

//             $journalCode = "INV-S-" . Carbon::now()->format('Ymd') . "-" . $sale->id;

//             $j1 = Journal::create([
//                 'date'         => Carbon::now(),
//                 'description'  => "Pelunasan Piutang SO #" . $sale->id . " (Ref: " . $sale->reference_no . ") - " . $journalCode,
//                 'reference_no' => $journalCode,
//             ]);

//             JournalEntry::create(['journal_id' => $j1->id, 'account_code' => $bankAcc->code, 'debit' => $sale->total, 'credit' => 0]);
//             JournalEntry::create(['journal_id' => $j1->id, 'account_code' => $arAcc->code, 'debit' => 0, 'credit' => $sale->total]);

//             DB::commit();
//             return redirect()->back()->with('success', 'Invoice lunas! Dana masuk ke Bank dan saldo Piutang Usaha telah ditutup.');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return redirect()->back()->with('error', 'Gagal memproses Invoice');
//         }
//     }

//     public function showDeliveries()
//     {
//         $deliveries = Sale::with('contact')->where('status', 'Pengiriman')->orderBy('date', 'desc')->get();
//         $pendingOrders = Sale::with(['contact', 'items.product'])->where('status', 'Pesanan')->get();
//         return view('sales.delivery', compact('deliveries', 'pendingOrders'));
//     }

//     public function showInvoices()
//     {
//         $invoices = Sale::with(['contact', 'paymentAccount'])->where('status', 'Tagihan')->orderBy('date', 'desc')->get();
//         $readyDeliveries = Sale::with(['contact', 'items.product'])->where('status', 'Pengiriman')->get();
//         $banks = Account::where(function($query) { $query->where('code', 'LIKE', '10%')->orWhere('code', 'LIKE', '11%'); })->where('is_header', false)->get();
//         return view('sales.invoice', compact('invoices', 'readyDeliveries', 'banks'));
//     }
// }

// namespace App\Http\Controllers;

// use App\Models\Sale;
// use App\Models\SaleItem;
// use App\Models\Product;
// use App\Models\ProductStock;
// use App\Models\Account;
// use App\Models\Journal;
// use App\Models\JournalEntry;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Carbon\Carbon;

// class SaleController extends Controller
// {
//     // Fungsi Bantuan (Helper) untuk Auto-Generate Nomor Invoice (GKP) & Surat Jalan (SJ)
//     private function generateDocNumber($type, $dateString)
//     {
//         $date = Carbon::parse($dateString);
//         $month = $date->month;
//         $year = $date->year;

//         $romanMonths = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];
//         $romanMonth = $romanMonths[$month];

//         // Akhiran dokumen (Contoh: /SJ/BMP/XI/2026 atau /GKP/BMP/XI/2026)
//         $suffix = "/{$type}/BMP/{$romanMonth}/{$year}";

//         // Karena Surat Jalan & Invoice masuk ke tabel Jurnal, kita cari urutan terakhir di sana
//         $latestDoc = Journal::whereYear('date', $year)
//             ->whereMonth('date', $month)
//             ->where('reference_no', 'LIKE', '%'.$suffix)
//             ->orderBy('id', 'desc')
//             ->first();

//         $nextSeq = 1;
//         if ($latestDoc && $latestDoc->reference_no) {
//             $parts = explode('/', $latestDoc->reference_no);
//             if (isset($parts[0]) && is_numeric($parts[0])) {
//                 $nextSeq = intval($parts[0]) + 1;
//             }
//         }

//         return str_pad($nextSeq, 3, '0', STR_PAD_LEFT) . $suffix;
//     }

//     public function index(Request $request)
//     {
//         $query = Sale::with(['contact', 'items.product', 'paymentAccount'])->orderBy('date', 'desc')->orderBy('id', 'desc');

//         if ($request->has('status') && $request->status != '') {
//             $query->where('status', $request->status);
//         }

//         $sales = $query->get();
//         return view('sales.index', compact('sales'));
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
//             $sale = Sale::create([
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

//                 SaleItem::create([
//                     'sale_id'     => $sale->id,
//                     'product_sku' => $item['product_sku'],
//                     'quantity'    => $item['quantity'],
//                     'price'       => $item['price'],
//                     'sub_total'   => $subTotal,
//                 ]);
//             }

//             $sale->update(['total' => $grandTotal]);

//             DB::commit();
//             return redirect()->route('sales.index')->with('success', 'Pesanan Penjualan berhasil dibuat');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pesanan penjualan');
//         }
//     }

//     // TAHAP 2: KIRIM BARANG (PENERBITAN SURAT JALAN / SJ)
//     public function deliver($id)
//     {
//         DB::beginTransaction();

//         try {
//             $sale = Sale::with('items')->findOrFail($id);

//             if ($sale->status != 'Pesanan') {
//                 return redirect()->back()->with('error', 'Status pesanan tidak valid untuk dikirim');
//             }

//             $totalHPP = 0;
//             $deliveryLog = [];

//             foreach ($sale->items as $item) {
//                 $product = Product::where('sku', $item->product_sku)->lockForUpdate()->first();
//                 $neededQty = $item->quantity;

//                 $availableStocks = ProductStock::with('warehouse')
//                     ->where('product_sku', $item->product_sku)
//                     ->where('quantity', '>', 0)
//                     ->orderBy('id', 'asc')
//                     ->lockForUpdate()
//                     ->get();

//                 $totalAvailable = $availableStocks->sum('quantity');

//                 if ($totalAvailable < $neededQty) {
//                     DB::rollBack();
//                     return redirect()->back()->with('error', 'Stok gabungan seluruh gudang tidak mencukupi untuk produk: ' . $product->name);
//                 }

//                 $totalHPP += ($neededQty * $product->hpp);
//                 $takenNotes = [];

//                 foreach ($availableStocks as $stock) {
//                     if ($neededQty <= 0) break;

//                     $take = min($stock->quantity, $neededQty);

//                     $stock->quantity -= $take;
//                     $stock->save();

//                     $neededQty -= $take;

//                     $warehouseName = $stock->warehouse ? $stock->warehouse->name : 'Gudang Unknown';
//                     $takenNotes[] = "{$take} unit dari {$warehouseName}";
//                 }

//                 $deliveryLog[] = "{$product->name} (" . implode(', ', $takenNotes) . ")";
//             }

//             $arAcc = Account::where('code', 'LIKE', '12%')->where('is_header', false)->first();
//             $revAcc = Account::where('code', 'LIKE', '50%')->where('is_header', false)->first() ?? Account::where('code', 'LIKE', '4%')->where('is_header', false)->first();
//             $cogsAcc = Account::where('code', 'LIKE', '60%')->where('is_header', false)->first() ?? Account::where('code', 'LIKE', '5%')->where('is_header', false)->first();
//             $invAcc = Account::where('code', 'LIKE', '13%')->where('is_header', false)->first() ?? Account::where('code', 'LIKE', '16%')->where('is_header', false)->first();

//             if (!$arAcc || !$revAcc || !$cogsAcc || !$invAcc) {
//                 DB::rollBack();
//                 return redirect()->back()->with('error', 'Konfigurasi Akun (Piutang 12%, Persediaan 13%, Pendapatan 50%, HPP 60%) belum lengkap di CoA!');
//             }

//             // AUTO-GENERATE KODE SURAT JALAN (SJ)
//             $now = Carbon::now();
//             $journalCode = $this->generateDocNumber('SJ', $now);

//             // Tempelkan Surat Jalan ke Catatan agar tampil di tabel UI
//             $newNotes = trim($sale->notes . "\n[No. SJ: {$journalCode}] [AUTO-FIFO] " . implode(' | ', $deliveryLog));
//             $sale->update([
//                 'status' => 'Pengiriman',
//                 'notes'  => $newNotes
//             ]);

//             $j1 = Journal::create([
//                 'date'         => $now,
//                 'description'  => "Pengiriman Penjualan SO #" . $sale->id . " - Surat Jalan: " . $journalCode,
//                 'reference_no' => $journalCode,
//             ]);
//             JournalEntry::create(['journal_id' => $j1->id, 'account_code' => $arAcc->code, 'debit' => $sale->total, 'credit' => 0]);
//             JournalEntry::create(['journal_id' => $j1->id, 'account_code' => $revAcc->code, 'debit' => 0, 'credit' => $sale->total]);

//             if ($totalHPP > 0) {
//                 $j2 = Journal::create([
//                     'date'         => $now,
//                     'description'  => "HPP Pengiriman SO #" . $sale->id . " (Surat Jalan: " . $journalCode . ")",
//                     'reference_no' => $journalCode,
//                 ]);
//                 JournalEntry::create(['journal_id' => $j2->id, 'account_code' => $cogsAcc->code, 'debit' => $totalHPP, 'credit' => 0]);
//                 JournalEntry::create(['journal_id' => $j2->id, 'account_code' => $invAcc->code, 'debit' => 0, 'credit' => $totalHPP]);
//             }

//             DB::commit();
//             return redirect()->back()->with('success', 'Barang berhasil dikirim dengan Surat Jalan Nomor: ' . $journalCode);
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return redirect()->back()->with('error', 'Gagal memproses pengiriman barang: ' . $e->getMessage());
//         }
//     }

//     // TAHAP 3: PELUNASAN (PENERBITAN INVOICE / GKP)
//     public function invoice(Request $request, $id)
//     {
//         $validated = $request->validate([
//             'payment_account' => 'required|exists:accounts,code',
//         ]);

//         DB::beginTransaction();

//         try {
//             $sale = Sale::with('items')->findOrFail($id);

//             if ($sale->status != 'Pengiriman') {
//                 return redirect()->back()->with('error', 'Hanya dokumen berstatus Pengiriman yang dapat dilunasi');
//             }

//             $bankAcc = Account::where('code', $validated['payment_account'])->where('is_header', false)->first();
//             $arAcc   = Account::where('code', 'LIKE', '12%')->where('is_header', false)->first();

//             if (!$bankAcc || !$arAcc) {
//                 DB::rollBack();
//                 return redirect()->back()->with('error', 'Akun Kas/Bank atau Akun Piutang Usaha (12%) tidak ditemukan di CoA');
//             }

//             // AUTO-GENERATE KODE INVOICE (GKP)
//             $now = Carbon::now();
//             $journalCode = $this->generateDocNumber('GKP', $now);

//             $sale->update([
//                 'status'               => 'Tagihan',
//                 'payment_account_code' => $validated['payment_account'],
//                 'notes'                => trim($sale->notes . "\n[Lunas] Diterbitkan Invoice Nomor: " . $journalCode)
//             ]);

//             $j1 = Journal::create([
//                 'date'         => $now,
//                 'description'  => "Pelunasan Piutang SO #" . $sale->id . " - Invoice: " . $journalCode,
//                 'reference_no' => $journalCode,
//             ]);

//             JournalEntry::create(['journal_id' => $j1->id, 'account_code' => $bankAcc->code, 'debit' => $sale->total, 'credit' => 0]);
//             JournalEntry::create(['journal_id' => $j1->id, 'account_code' => $arAcc->code, 'debit' => 0, 'credit' => $sale->total]);

//             DB::commit();
//             return redirect()->back()->with('success', 'Pembayaran berhasil! Invoice bernomor ' . $journalCode . ' telah diterbitkan.');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return redirect()->back()->with('error', 'Gagal memproses Invoice');
//         }
//     }

//     public function showDeliveries()
//     {
//         $deliveries = Sale::with('contact')->where('status', 'Pengiriman')->orderBy('date', 'desc')->get();
//         $pendingOrders = Sale::with(['contact', 'items.product'])->where('status', 'Pesanan')->get();
//         return view('sales.delivery', compact('deliveries', 'pendingOrders'));
//     }

//     public function showInvoices()
//     {
//         $invoices = Sale::with(['contact', 'paymentAccount'])->where('status', 'Tagihan')->orderBy('date', 'desc')->get();
//         $readyDeliveries = Sale::with(['contact', 'items.product'])->where('status', 'Pengiriman')->get();
//         $banks = Account::where(function($query) { $query->where('code', 'LIKE', '10%')->orWhere('code', 'LIKE', '11%'); })->where('is_header', false)->get();
//         return view('sales.invoice', compact('invoices', 'readyDeliveries', 'banks'));
//     }
// }

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Account;
use App\Models\Journal;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaleController extends Controller
{
    // Fungsi Bantuan (Helper) untuk Auto-Generate Nomor SO, Invoice (GKP) & Surat Jalan (SJ)
    private function generateDocNumber($type, $dateString)
    {
        $date = Carbon::parse($dateString);
        $month = $date->month;
        $year = $date->year;

        $romanMonths = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];
        $romanMonth = $romanMonths[$month];

        // Akhiran dokumen (Contoh: /SO/BMP/XI/2026, /SJ/BMP/XI/2026 atau /GKP/BMP/XI/2026)
        $suffix = "/{$type}/BMP/{$romanMonth}/{$year}";

        if ($type === 'SO') {
            // Pencarian untuk dokumen SO (Pesanan Penjualan)
            $latestDoc = Sale::whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('reference_no', 'LIKE', '%'.$suffix)
                ->orderBy('id', 'desc')
                ->first();
        } else {
            // Pencarian untuk Surat Jalan (SJ) & Invoice (GKP) yang dicatat di tabel Jurnal
            $latestDoc = Journal::whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('reference_no', 'LIKE', '%'.$suffix)
                ->orderBy('id', 'desc')
                ->first();
        }

        $nextSeq = 1;
        if ($latestDoc && $latestDoc->reference_no) {
            $parts = explode('/', $latestDoc->reference_no);
            if (isset($parts[0]) && is_numeric($parts[0])) {
                $nextSeq = intval($parts[0]) + 1;
            }
        }

        return str_pad($nextSeq, 3, '0', STR_PAD_LEFT) . $suffix;
    }

    // public function index(Request $request)
    // {
    //     $query = Sale::with(['contact', 'items.product', 'paymentAccount'])->orderBy('date', 'desc')->orderBy('id', 'desc');

    //     if ($request->has('status') && $request->status != '') {
    //         $query->where('status', $request->status);
    //     }

    //     $sales = $query->get();
    //     return view('sales.index', compact('sales'));
    // }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'date'         => 'required|date',
    //         'due_date'     => 'nullable|date',
    //         'reference_no' => 'nullable|string|max:50',
    //         'contact_id'   => 'required|exists:contacts,id',
    //         'notes'        => 'nullable|string',
    //         'items'        => 'required|array|min:1',
    //         'items.*.product_sku' => 'required|exists:products,sku',
    //         'items.*.quantity'    => 'required|integer|min:1',
    //         'items.*.price'       => 'required|numeric|min:0',
    //     ]);

    //     DB::beginTransaction();

    //     try {
    //         // Jika user membiarkan kosong, auto-generate kode SO
    //         $refNo = $validated['reference_no'] ?: $this->generateDocNumber('SO', $validated['date']);

    //         $sale = Sale::create([
    //             'date'         => $validated['date'],
    //             'due_date'     => $validated['due_date'] ?? $validated['date'],
    //             'reference_no' => $refNo,
    //             'contact_id'   => $validated['contact_id'],
    //             'status'       => 'Pesanan',
    //             'notes'        => $validated['notes'] ?? '',
    //             'total'        => 0,
    //         ]);

    //         $grandTotal = 0;

    //         foreach ($validated['items'] as $item) {
    //             $subTotal = $item['quantity'] * $item['price'];
    //             $grandTotal += $subTotal;

    //             SaleItem::create([
    //                 'sale_id'     => $sale->id,
    //                 'product_sku' => $item['product_sku'],
    //                 'quantity'    => $item['quantity'],
    //                 'price'       => $item['price'],
    //                 'sub_total'   => $subTotal,
    //             ]);
    //         }

    //         $sale->update(['total' => $grandTotal]);

    //         DB::commit();
    //         return redirect()->route('sales.index')->with('success', 'Pesanan Penjualan berhasil dibuat dengan nomor: ' . $refNo);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pesanan penjualan');
    //     }
    // }

    public function index(Request $request)
    {
        $query = Sale::with(['contact', 'items.product', 'paymentAccount'])->orderBy('date', 'desc')->orderBy('id', 'desc');

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $sales = $query->get();

        // Kalkulasi nomor SO selanjutnya untuk preview di form Modal
        $nextDocNumber = $this->generateDocNumber('SO', Carbon::now()->toDateString());

        return view('sales.index', compact('sales', 'nextDocNumber'));
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'date'         => 'required|date',
    //         'due_date'     => 'nullable|date',
    //         // reference_no dihapus dari validasi form karena murni digenerate oleh sistem
    //         'contact_id'   => 'required|exists:contacts,id',
    //         'notes'        => 'nullable|string',
    //         'items'        => 'required|array|min:1',
    //         'items.*.product_sku' => 'required|exists:products,sku',
    //         'items.*.quantity'    => 'required|integer|min:1',
    //         'items.*.price'       => 'required|numeric|min:0',
    //     ]);

    //     DB::beginTransaction();

    //     try {
    //         // PAKSA auto-generate di backend untuk mencegah duplikasi jika 2 user submit bersamaan
    //         // Kalkulasi ini dihitung akurat berdasarkan inputan 'Tanggal SO'
    //         $refNo = $this->generateDocNumber('SO', $validated['date']);

    //         $sale = Sale::create([
    //             'date'         => $validated['date'],
    //             'due_date'     => $validated['due_date'] ?? $validated['date'],
    //             'reference_no' => $refNo,
    //             'contact_id'   => $validated['contact_id'],
    //             'status'       => 'Pesanan',
    //             'notes'        => $validated['notes'] ?? '',
    //             'total'        => 0,
    //         ]);

    //         $grandTotal = 0;

    //         foreach ($validated['items'] as $item) {
    //             $subTotal = $item['quantity'] * $item['price'];
    //             $grandTotal += $subTotal;

    //             SaleItem::create([
    //                 'sale_id'     => $sale->id,
    //                 'product_sku' => $item['product_sku'],
    //                 'quantity'    => $item['quantity'],
    //                 'price'       => $item['price'],
    //                 'sub_total'   => $subTotal,
    //             ]);
    //         }

    //         $sale->update(['total' => $grandTotal]);

    //         DB::commit();
    //         return redirect()->route('sales.index')->with('success', 'Pesanan Penjualan berhasil dibuat dengan nomor: ' . $refNo);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pesanan penjualan');
    //     }
    // }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date'         => 'required|date',
            'due_date'     => 'nullable|date',
            'contact_id'   => 'required|exists:contacts,id',
            'notes'        => 'nullable|string',
            'items'        => 'required|array|min:1',
            'items.*.product_sku' => 'required|exists:products,sku',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.price'       => 'required|numeric|min:0',
            'items.*.tax_rate'    => 'required|integer|in:0,11,12', // Validasi input pajak dari user
        ]);

        DB::beginTransaction();

        // try {
        //     $refNo = $this->generateDocNumber('SO', $validated['date']);

        //     // $sale = Sale::create([
        //     //     'date'         => $validated['date'],
        //     //     'due_date'     => $validated['due_date'] ?? $validated['date'],
        //     //     'reference_no' => $refNo,
        //     //     'contact_id'   => $validated['contact_id'],
        //     //     'status'       => 'Pesanan',
        //     //     'notes'        => $validated['notes'] ?? '',
        //     //     'total'        => 0,
        //     //     'total_tax'    => 0, // Inisialisasi awal
        //     // ]);

        //     $sale = Sale::create([
        //         'date'         => $validated['date'],
        //         'due_date'     => $validated['due_date'] ?? $validated['date'],
        //         'reference_no' => $refNo,
        //         'contact_id'   => $validated['contact_id'],
        //         // UBAH BARIS INI:
        //         'status'       => 'Menunggu Persetujuan',
        //         'notes'        => $validated['notes'] ?? '',
        //         'total'        => 0,
        //         'total_tax'    => 0,
        //     ]);

        //     $grandTotal = 0;
        //     $grandTax = 0;

        //     foreach ($validated['items'] as $item) {
        //         // Kalkulasi DPP (Dasar Pengenaan Pajak) dan PPN
        //         $dpp = $item['quantity'] * $item['price'];
        //         $taxAmount = $dpp * ($item['tax_rate'] / 100);
        //         $subTotal = $dpp + $taxAmount;

        //         $grandTax += $taxAmount;
        //         $grandTotal += $subTotal;

        //         SaleItem::create([
        //             'sale_id'     => $sale->id,
        //             'product_sku' => $item['product_sku'],
        //             'quantity'    => $item['quantity'],
        //             'price'       => $item['price'],
        //             'tax_rate'    => $item['tax_rate'],
        //             'tax_amount'  => $taxAmount,
        //             'sub_total'   => $subTotal,
        //         ]);
        //     }

        //     $sale->update(['total' => $grandTotal, 'total_tax' => $grandTax]);

        //     DB::commit();
        //     return redirect()->route('sales.index')->with('success', 'Pesanan Penjualan berhasil dibuat. Nilai Pajak telah dikalkulasi.');
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pesanan.');
        // }

        try {
            // Deteksi jenis tombol yang ditekan di formulir HTML
            $action = $request->input('action', 'so');
            $prefix = ($action === 'quotation') ? 'QT' : 'SO';
            $status = ($action === 'quotation') ? 'Penawaran' : 'Menunggu Persetujuan';

            $refNo = $this->generateDocNumber($prefix, $validated['date']);

            $sale = Sale::create([
                'date'         => $validated['date'],
                'due_date'     => $validated['due_date'] ?? $validated['date'],
                'reference_no' => $refNo,
                'contact_id'   => $validated['contact_id'],
                'status'       => $status, // 'Penawaran' atau 'Menunggu Persetujuan'
                'notes'        => $validated['notes'] ?? '',
                'total'        => 0,
                'total_tax'    => 0,
            ]);

            $grandTotal = 0;
            $grandTax = 0;

            foreach ($validated['items'] as $item) {
                $dpp = $item['quantity'] * $item['price'];
                $taxAmount = $dpp * ($item['tax_rate'] / 100);
                $subTotal = $dpp + $taxAmount;

                $grandTax += $taxAmount;
                $grandTotal += $subTotal;

                SaleItem::create([
                    'sale_id'     => $sale->id,
                    'product_sku' => $item['product_sku'],
                    'quantity'    => $item['quantity'],
                    'price'       => $item['price'],
                    'tax_rate'    => $item['tax_rate'],
                    'tax_amount'  => $taxAmount,
                    'sub_total'   => $subTotal,
                ]);

                // --- TAMBAHAN BARU: UPDATE HARGA JUAL MASTER PRODUK ---
                // Memaksa harga di Master Produk berubah mengikuti harga jual transaksi terakhir ini
                Product::where('sku', $item['product_sku'])->update([
                    'sell_price' => $item['price']
                ]);
            }

            $sale->update(['total' => $grandTotal, 'total_tax' => $grandTax]);

            DB::commit();
            $msg = ($action === 'quotation') ? 'Draf Penawaran (Quotation)' : 'Pesanan Penjualan (SO)';
            return redirect()->route('sales.index')->with('success', "Dokumen {$msg} berhasil dibuat dengan nomor: {$refNo}");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan dokumen.');
        }
    }

    // TAHAP 2: KIRIM BARANG (PENERBITAN SURAT JALAN / SJ)
    public function deliver($id)
    {
        DB::beginTransaction();

        try {
            $sale = Sale::with('items')->findOrFail($id);

            if ($sale->status != 'Pesanan') {
                return redirect()->back()->with('error', 'Status pesanan tidak valid untuk dikirim');
            }

            $totalHPP = 0;
            $deliveryLog = [];

            foreach ($sale->items as $item) {
                $product = Product::where('sku', $item->product_sku)->lockForUpdate()->first();
                $neededQty = $item->quantity;

                $availableStocks = ProductStock::with('warehouse')
                    ->where('product_sku', $item->product_sku)
                    ->where('quantity', '>', 0)
                    ->orderBy('id', 'asc')
                    ->lockForUpdate()
                    ->get();

                $totalAvailable = $availableStocks->sum('quantity');

                if ($totalAvailable < $neededQty) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Stok gabungan seluruh gudang tidak mencukupi untuk produk: ' . $product->name);
                }

                $totalHPP += ($neededQty * $product->hpp);
                $takenNotes = [];

                foreach ($availableStocks as $stock) {
                    if ($neededQty <= 0) break;

                    $take = min($stock->quantity, $neededQty);

                    $stock->quantity -= $take;
                    $stock->save();

                    $neededQty -= $take;

                    $warehouseName = $stock->warehouse ? $stock->warehouse->name : 'Gudang Unknown';
                    $takenNotes[] = "{$take} unit dari {$warehouseName}";
                }

                $deliveryLog[] = "{$product->name} (" . implode(', ', $takenNotes) . ")";
            }

            $arAcc = Account::where('code', 'LIKE', '12%')->where('is_header', false)->first();
            $revAcc = Account::where('code', 'LIKE', '50%')->where('is_header', false)->first() ?? Account::where('code', 'LIKE', '4%')->where('is_header', false)->first();
            $cogsAcc = Account::where('code', 'LIKE', '60%')->where('is_header', false)->first() ?? Account::where('code', 'LIKE', '5%')->where('is_header', false)->first();
            $invAcc = Account::where('code', 'LIKE', '13%')->where('is_header', false)->first() ?? Account::where('code', 'LIKE', '16%')->where('is_header', false)->first();

            if (!$arAcc || !$revAcc || !$cogsAcc || !$invAcc) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Konfigurasi Akun (Piutang 12%, Persediaan 13%, Pendapatan 50%, HPP 60%) belum lengkap di CoA!');
            }

            // AUTO-GENERATE KODE SURAT JALAN (SJ)
            $now = Carbon::now();
            $journalCode = $this->generateDocNumber('SJ', $now);

            $newNotes = trim($sale->notes . "\n[No. SJ: {$journalCode}] [AUTO-FIFO] " . implode(' | ', $deliveryLog));
            $sale->update([
                'status' => 'Pengiriman',
                'notes'  => $newNotes
            ]);

            $j1 = Journal::create([
                'date'         => $now,
                'description'  => "Pengiriman Penjualan SO #" . $sale->id . " - Surat Jalan: " . $journalCode,
                'reference_no' => $journalCode,
            ]);
            JournalEntry::create(['journal_id' => $j1->id, 'account_code' => $arAcc->code, 'debit' => $sale->total, 'credit' => 0]);
            JournalEntry::create(['journal_id' => $j1->id, 'account_code' => $revAcc->code, 'debit' => 0, 'credit' => $sale->total]);

            if ($totalHPP > 0) {
                $j2 = Journal::create([
                    'date'         => $now,
                    'description'  => "HPP Pengiriman SO #" . $sale->id . " (Surat Jalan: " . $journalCode . ")",
                    'reference_no' => $journalCode,
                ]);
                JournalEntry::create(['journal_id' => $j2->id, 'account_code' => $cogsAcc->code, 'debit' => $totalHPP, 'credit' => 0]);
                JournalEntry::create(['journal_id' => $j2->id, 'account_code' => $invAcc->code, 'debit' => 0, 'credit' => $totalHPP]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Barang berhasil dikirim dengan Surat Jalan Nomor: ' . $journalCode);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses pengiriman barang: ' . $e->getMessage());
        }
    }

    // TAHAP 3: PELUNASAN (PENERBITAN INVOICE / GKP)
    public function invoice(Request $request, $id)
    {
        $validated = $request->validate([
            'payment_account' => 'required|exists:accounts,code',
        ]);

        DB::beginTransaction();

        try {
            $sale = Sale::with('items')->findOrFail($id);

            if ($sale->status != 'Pengiriman') {
                return redirect()->back()->with('error', 'Hanya dokumen berstatus Pengiriman yang dapat dilunasi');
            }

            $bankAcc = Account::where('code', $validated['payment_account'])->where('is_header', false)->first();
            $arAcc   = Account::where('code', 'LIKE', '12%')->where('is_header', false)->first();

            if (!$bankAcc || !$arAcc) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Akun Kas/Bank atau Akun Piutang Usaha (12%) tidak ditemukan di CoA');
            }

            // AUTO-GENERATE KODE INVOICE (GKP)
            $now = Carbon::now();
            $journalCode = $this->generateDocNumber('GKP', $now);

            $sale->update([
                'status'               => 'Tagihan',
                'payment_account_code' => $validated['payment_account'],
                'notes'                => trim($sale->notes . "\n[Lunas] Diterbitkan Invoice Nomor: " . $journalCode)
            ]);

            $j1 = Journal::create([
                'date'         => $now,
                'description'  => "Pelunasan Piutang SO #" . $sale->id . " - Invoice: " . $journalCode,
                'reference_no' => $journalCode,
            ]);

            JournalEntry::create(['journal_id' => $j1->id, 'account_code' => $bankAcc->code, 'debit' => $sale->total, 'credit' => 0]);
            JournalEntry::create(['journal_id' => $j1->id, 'account_code' => $arAcc->code, 'debit' => 0, 'credit' => $sale->total]);

            DB::commit();
            return redirect()->back()->with('success', 'Pembayaran berhasil! Invoice bernomor ' . $journalCode . ' telah diterbitkan.');
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

    // FITUR ANALITIK: OVERVIEW PENJUALAN & PREDIKSI REGRESI LINEAR
    public function overview()
    {
        // 1. History & Detail Alur Stok
        $sales = Sale::with(['contact', 'items.product'])->orderBy('date', 'desc')->get();

        // 2. Daftar 5 Produk Terlaris
        $topProducts = SaleItem::select('product_sku', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(sub_total) as total_revenue'))
            ->groupBy('product_sku')
            ->orderBy('total_qty', 'desc')
            ->take(5)
            ->with('product')
            ->get();

        // 3. Mesin Analitik: Algoritma Linear Regression (Y = a + bX)
        $predictions = [];
        if ($topProducts->isNotEmpty()) {
            $topProductSku = $topProducts->first()->product_sku;
            $historicalData = [];

            // Ekstrak mundur data 6 bulan terakhir
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $qty = SaleItem::where('product_sku', $topProductSku)
                    ->whereHas('sale', function($q) use ($date) {
                        $q->whereMonth('date', $date->month)->whereYear('date', $date->year);
                    })->sum('quantity');
                $historicalData[] = ['month' => $date->translatedFormat('M Y'), 'qty' => $qty];
            }

            // Kalkulasi Matematika Regresi
            $n = count($historicalData);
            $sumX = 0; $sumY = 0; $sumXY = 0; $sumX2 = 0;
            foreach ($historicalData as $i => $data) {
                $x = $i + 1; // Sumbu X (Waktu/Bulan)
                $y = $data['qty']; // Sumbu Y (Volume)
                $sumX += $x; $sumY += $y; $sumXY += ($x * $y); $sumX2 += ($x * $x);
            }

            $denominator = ($n * $sumX2) - ($sumX * $sumX);
            $b = $denominator != 0 ? (($n * $sumXY) - ($sumX * $sumY)) / $denominator : 0; // Slope (Kemiringan)
            $a = ($sumY - ($b * $sumX)) / $n; // Intercept

            $nextX = $n + 1;
            $predictionValue = max(0, round($a + ($b * $nextX))); // Mencegah nilai negatif

            $predictions = [
                'product' => $topProducts->first()->product->name ?? $topProductSku,
                'history' => $historicalData,
                'next_month_prediction' => $predictionValue,
                'trend' => $b > 0 ? 'Meningkat Tajam 📈' : ($b < 0 ? 'Menurun 📉' : 'Cenderung Stabil ➖')
            ];
        }

        return view('sales.overview', compact('sales', 'topProducts', 'predictions'));
    }

    // FITUR EMAS: EXPORT E-FAKTUR (Sesuai Skema DJP Pemerintah)
    public function exportEfaktur(Request $request)
    {
        // Tarik semua penjualan yang ada pajaknya
        $sales = Sale::with(['contact', 'items.product'])->where('status', '!=', 'Pesanan')->where('total_tax', '>', 0)->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=eFaktur_Keluaran_" . Carbon::now()->format('Ymd') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($sales) {
            $file = fopen('php://output', 'w');

            // Cetak Format Header DJP e-Faktur
            fputcsv($file, ['FK', 'KD_JENIS_TRANSAKSI', 'FG_PENGGANTI', 'NOMOR_FAKTUR', 'MASA_PAJAK', 'TAHUN_PAJAK', 'TANGGAL_FAKTUR', 'NPWP', 'NAMA', 'ALAMAT_LENGKAP', 'JUMLAH_DPP', 'JUMLAH_PPN', 'JUMLAH_PPNBM', 'ID_KETERANGAN_TAMBAHAN', 'FG_UANG_MUKA', 'UANG_MUKA_DPP', 'UANG_MUKA_PPN', 'UANG_MUKA_PPNBM', 'REFERENSI']);
            fputcsv($file, ['OF', 'KODE_OBJEK', 'NAMA', 'HARGA_SATUAN', 'JUMLAH_BARANG', 'TOTAL_HARGA', 'DISKON', 'DPP', 'PPN', 'TARIF_PPNBM', 'PPNBM']);

            foreach ($sales as $sale) {
                $date = Carbon::parse($sale->date);
                $npwp = '00.000.000.0-000.000'; // Harus diganti dengan NPWP Customer di sistem asli
                $dppTotal = $sale->total - $sale->total_tax;

                // Baris FK (Faktur Keluaran) -> Data Induk
                fputcsv($file, [
                    'FK', '01', '0', str_replace('/', '', $sale->reference_no), $date->month, $date->year, $date->format('d/m/Y'),
                    $npwp, $sale->contact->name, $sale->contact->address ?? '-',
                    $dppTotal, $sale->total_tax, 0, '', '0', 0, 0, 0, $sale->reference_no
                ]);

                // Baris OF (Objek Faktur) -> Rincian Barang
                foreach ($sale->items as $item) {
                    if ($item->tax_rate > 0) {
                        $dppItem = $item->quantity * $item->price;
                        fputcsv($file, [
                            'OF', $item->product_sku, $item->product->name ?? $item->product_sku,
                            $item->price, $item->quantity, $dppItem, 0, $dppItem, $item->tax_amount, 0, 0
                        ]);
                    }
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // --- FITUR BARU: SISTEM PERSETUJUAN (MAKER-CHECKER) PENJUALAN ---
    public function approve(Request $request, $id)
    {
        $sale = Sale::findOrFail($id);

        // Cek hak akses (Hanya Direktur atau Admin yang boleh Setuju)
        $userRole = auth()->user()->role;
        if (!in_array($userRole, ['Direktur', 'Admin'])) {
            return redirect()->back()->with('error', 'Akses Ditolak! Hanya Direktur atau Admin yang berhak menyetujui dokumen ini.');
        }

        if ($sale->status !== 'Menunggu Persetujuan') {
            return redirect()->back()->with('error', 'Dokumen tidak dalam status Menunggu Persetujuan.');
        }

        // Setujui dan lepaskan ke Gudang (Status menjadi Pesanan)
        $sale->update([
            'status' => 'Pesanan',
            'notes' => trim($sale->notes . "\n[DISETUJUI] Oleh: " . auth()->user()->name . " (" . Carbon::now()->format('d/m/Y H:i') . ")")
        ]);

        return redirect()->back()->with('success', 'Pesanan Penjualan (SO) berhasil DISETUJUI dan siap dikirim oleh Gudang.');
    }

    public function reject(Request $request, $id)
    {
        $sale = Sale::findOrFail($id);

        $userRole = auth()->user()->role;
        if (!in_array($userRole, ['Direktur', 'Admin'])) {
            return redirect()->back()->with('error', 'Akses Ditolak! Hanya Direktur atau Admin yang berhak menolak dokumen.');
        }

        $sale->update([
            'status' => 'Ditolak',
            'notes' => trim($sale->notes . "\n[DITOLAK] Oleh: " . auth()->user()->name . " (" . Carbon::now()->format('d/m/Y H:i') . ")")
        ]);

        return redirect()->back()->with('success', 'Pesanan Penjualan (SO) telah DITOLAK secara permanen.');
    }

    // --- FITUR BARU: CETAK SURAT JALAN (DELIVERY NOTE) ---
    public function printDeliveryNote($id)
    {
        $sale = Sale::with(['contact', 'items.product'])->findOrFail($id);

        // Validasi: Surat Jalan hanya boleh dicetak setelah barang diproses kirim
        if (!in_array($sale->status, ['Pengiriman', 'Tagihan', 'Selesai'])) {
            return redirect()->back()->with('error', 'Surat Jalan hanya bisa dicetak jika status pesanan sudah masuk tahap Pengiriman.');
        }

        return view('sales.print_delivery_note', compact('sale'));
    }

    // --- FITUR BARU: RETUR PENJUALAN (CREDIT NOTE) ---
    public function processReturn(Request $request, $id)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'reason'       => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $sale = Sale::with('items')->findOrFail($id);

            // Validasi: Hanya pesanan yang sudah dikirim atau ditagih yang bisa diretur
            if (!in_array($sale->status, ['Pengiriman', 'Tagihan', 'Selesai'])) {
                return redirect()->back()->with('error', 'Hanya pesanan yang sudah terkirim yang dapat diproses retur.');
            }

            // 1. Tarik Kembali Stok Fisik ke Gudang yang Dipilih
            $totalHPP = 0;
            foreach ($sale->items as $item) {
                $product = Product::where('sku', $item->product_sku)->lockForUpdate()->first();
                $stock = ProductStock::firstOrCreate(
                    ['warehouse_id' => $validated['warehouse_id'], 'product_sku' => $item->product_sku],
                    ['quantity' => 0]
                );

                $stock->quantity += $item->quantity; // Kembalikan ke gudang
                $stock->save();

                $totalHPP += ($item->quantity * $product->hpp);
            }

            // 2. Buat Dokumen & Jurnal Pembalikan (Credit Note)
            $now = Carbon::now();
            $retCode = $this->generateDocNumber('RET', $now->toDateString()); // Auto-generate No. Surat Retur

            $arAcc = Account::where('code', 'LIKE', '12%')->where('is_header', false)->first();
            $revAcc = Account::where('code', 'LIKE', '50%')->where('is_header', false)->first() ?? Account::where('code', 'LIKE', '4%')->where('is_header', false)->first();
            $cogsAcc = Account::where('code', 'LIKE', '60%')->where('is_header', false)->first() ?? Account::where('code', 'LIKE', '5%')->where('is_header', false)->first();
            $invAcc = Account::where('code', 'LIKE', '13%')->where('is_header', false)->first() ?? Account::where('code', 'LIKE', '16%')->where('is_header', false)->first();

            // Jurnal 1: Membalik Pendapatan dan Piutang
            $j1 = Journal::create([
                'date'         => $now,
                'description'  => "Retur Penjualan (Credit Note) SO #" . $sale->id . " - " . $validated['reason'],
                'reference_no' => $retCode,
            ]);
            JournalEntry::create(['journal_id' => $j1->id, 'account_code' => $revAcc->code, 'debit' => $sale->total, 'credit' => 0]);
            JournalEntry::create(['journal_id' => $j1->id, 'account_code' => $arAcc->code, 'debit' => 0, 'credit' => $sale->total]);

            // Jurnal 2: Membalik HPP dan Persediaan Barang
            if ($totalHPP > 0) {
                $j2 = Journal::create([
                    'date'         => $now,
                    'description'  => "Pembalikan HPP Retur SO #" . $sale->id,
                    'reference_no' => $retCode,
                ]);
                JournalEntry::create(['journal_id' => $j2->id, 'account_code' => $invAcc->code, 'debit' => $totalHPP, 'credit' => 0]);
                JournalEntry::create(['journal_id' => $j2->id, 'account_code' => $cogsAcc->code, 'debit' => 0, 'credit' => $totalHPP]);
            }

            // 3. Ubah status pesanan menjadi 'Diretur'
            $sale->update([
                'status' => 'Diretur',
                'notes'  => trim($sale->notes . "\n[RETUR] " . $validated['reason'] . " (No. Dokumen: " . $retCode . ")")
            ]);

            // 4. Lacak aktivitas keamanan
            \App\Models\AuditLog::record('MENGEMBALIKAN', 'Penjualan', 'Memproses Retur Penjualan penuh untuk SO #' . $sale->id);

            DB::commit();
            return redirect()->back()->with('success', 'Retur Penjualan (Credit Note) berhasil diproses! Stok fisik telah dikembalikan ke gudang dan Jurnal pembalik otomatis dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses retur: ' . $e->getMessage());
        }
    }

    // ==========================================================
    // FITUR EMAS: LAPORAN UMUR PIUTANG (ACCOUNT RECEIVABLE AGING)
    // ==========================================================
    public function receivableAging()
    {
        // Ambil semua pesanan berstatus 'Pengiriman' (Barang terkirim, Piutang ada, tapi belum Lunas)
        $unpaidSales = Sale::with('contact')
            ->where('status', 'Pengiriman')
            ->orderBy('due_date', 'asc')
            ->get();

        $now = Carbon::now()->startOfDay();

        // Siapkan 'Ember' penampung data (Aging Buckets)
        $agingData = [
            'belum_jatuh_tempo' => 0,
            '1_30_hari' => 0,
            '31_60_hari' => 0,
            '61_90_hari' => 0,
            'lebih_90_hari' => 0,
        ];

        $details = [];

        foreach ($unpaidSales as $sale) {
            $dueDate = Carbon::parse($sale->due_date)->startOfDay();

            // Hitung selisih hari: Positif = Menunggak (Overdue), Negatif = Aman
            $diff = $dueDate->diffInDays($now, false);

            $category = '';
            $daysOverdue = 0;

            if ($diff <= 0) {
                $category = 'belum_jatuh_tempo';
                $agingData['belum_jatuh_tempo'] += $sale->total;
            } else {
                $daysOverdue = intval($diff);
                if ($daysOverdue <= 30) {
                    $category = '1_30_hari';
                    $agingData['1_30_hari'] += $sale->total;
                } elseif ($daysOverdue <= 60) {
                    $category = '31_60_hari';
                    $agingData['31_60_hari'] += $sale->total;
                } elseif ($daysOverdue <= 90) {
                    $category = '61_90_hari';
                    $agingData['61_90_hari'] += $sale->total;
                } else {
                    $category = 'lebih_90_hari';
                    $agingData['lebih_90_hari'] += $sale->total;
                }
            }

            $details[] = [
                'id' => $sale->id,
                'reference_no' => $sale->reference_no ?: 'SO #'.$sale->id,
                'customer' => $sale->contact->name,
                'phone' => $sale->contact->phone,
                'due_date' => $sale->due_date,
                'total' => $sale->total,
                'category' => $category,
                'days_overdue' => $daysOverdue
            ];
        }

        $totalPiutang = array_sum($agingData);

        return view('sales.receivable_aging', compact('agingData', 'details', 'totalPiutang'));
    }

    // --- FITUR BARU: QUOTATION ---
    public function convertToSo($id)
    {
        $sale = Sale::findOrFail($id);

        if ($sale->status !== 'Penawaran') {
            return redirect()->back()->with('error', 'Akses Ditolak: Hanya dokumen draf Penawaran yang dapat diubah ke SO.');
        }

        // Generate nomor seri SO baru untuk menggantikan nomor QT
        $refNo = $this->generateDocNumber('SO', Carbon::now()->toDateString());

        $sale->update([
            'status' => 'Menunggu Persetujuan',
            'reference_no' => $refNo,
            'notes' => trim($sale->notes . "\n[Sistem] Klien Setuju. Diubah dari Draf Penawaran ke SO pada " . Carbon::now()->format('d/m/Y'))
        ]);

        return redirect()->back()->with('success', 'Klien telah setuju! Penawaran berhasil diubah menjadi Pesanan Penjualan resmi (Menunggu Persetujuan Manajer).');
    }

    public function printQuotation($id)
    {
        $sale = Sale::with(['contact', 'items.product'])->findOrFail($id);

        if ($sale->status !== 'Penawaran') {
            return redirect()->back()->with('error', 'Dokumen ini bukan draf Penawaran Harga.');
        }

        return view('sales.print_quotation', compact('sale'));
    }
}
