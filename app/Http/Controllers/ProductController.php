<?php

// namespace App\Http\Controllers;

// use App\Models\Product;
// use App\Models\Category; // Digunakan untuk dropdown kategori di halaman produk
// use Illuminate\Http\Request;

// class ProductController extends Controller
// {
//     // Menampilkan halaman daftar produk (GET /products)
//     public function index()
//     {
//         $products = Product::orderBy('id', 'desc')->get();
//         // Ambil kategori untuk dropdown pada modal/form
//         $categories = Category::orderBy('name', 'asc')->get();

//         return view('products.index', compact('products', 'categories'));
//     }

//     // Menyimpan produk baru (POST /products)
//     public function store(Request $request)
//     {
//         // Validasi input dari Form Blade
//         $validated = $request->validate([
//             'sku'        => 'required|string|max:50|unique:products,sku',
//             'name'       => 'required|string|max:150',
//             'category'   => 'nullable|string|max:50',
//             'unit'       => 'nullable|string|max:20',
//             'buy_price'  => 'required|numeric|min:0',
//             'sell_price' => 'required|numeric|min:0',
//             'hpp'        => 'nullable|numeric|min:0',
//             'stock'      => 'nullable|integer|min:0',
//         ]);

//         try {
//             Product::create($validated);
//             return redirect()->route('products.index')
//                 ->with('success', 'Produk berhasil ditambahkan.');
//         } catch (\Exception $e) {
//             return redirect()->back()
//                 ->withInput()
//                 ->with('error', 'Gagal menyimpan produk. Pastikan SKU unik.');
//         }
//     }

//     // Memperbarui data produk (PUT /products/{id})
//     public function update(Request $request, $id)
//     {
//         $product = Product::findOrFail($id);

//         $validated = $request->validate([
//             // SKU diabaikan pengecekan unique-nya untuk ID yang sama
//             'sku'        => 'required|string|max:50|unique:products,sku,' . $product->id,
//             'name'       => 'required|string|max:150',
//             'category'   => 'nullable|string|max:50',
//             'unit'       => 'nullable|string|max:20',
//             'buy_price'  => 'required|numeric|min:0',
//             'sell_price' => 'required|numeric|min:0',
//             'hpp'        => 'nullable|numeric|min:0',
//             'stock'      => 'nullable|integer|min:0',
//         ]);

//         try {
//             $product->update($validated);
//             return redirect()->route('products.index')
//                 ->with('success', 'Produk berhasil diperbarui.');
//         } catch (\Exception $e) {
//             return redirect()->back()
//                 ->withInput()
//                 ->with('error', 'Gagal mengupdate produk.');
//         }
//     }

//     // Menghapus produk (DELETE /products/{id})
//     public function destroy($id)
//     {
//         try {
//             $product = Product::findOrFail($id);
//             $product->delete();

//             return redirect()->route('products.index')
//                 ->with('success', 'Produk berhasil dihapus.');
//         } catch (\Exception $e) {
//             return redirect()->back()
//                 ->with('error', 'Gagal menghapus produk. Mungkin produk ini sudah dipakai di transaksi.');
//         }
//     }
// }

// namespace App\Http\Controllers;

// use App\Models\Product;
// use App\Models\Category;
// use Illuminate\Http\Request;

// class ProductController extends Controller
// {
//     // Menampilkan halaman daftar produk
//     public function index()
//     {
//         // Ambil data produk sekaligus menjumlahkan kolom 'quantity' dari relasi 'stocks'
//         $products = Product::withSum('stocks as total_stock', 'quantity')->orderBy('id', 'desc')->get();

//         $categories = Category::orderBy('name', 'asc')->get();

//         return view('products.index', compact('products', 'categories'));
//     }

//     // Menyimpan produk baru
//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'sku'      => 'required|string|max:50|unique:products,sku',
//             'name'     => 'required|string|max:150',
//             'category' => 'nullable|string|max:50',
//             'unit'     => 'nullable|string|max:20',
//         ]);

//         try {
//             // Default nilai awal untuk harga dan stok adalah 0
//             $validated['buy_price'] = 0;
//             $validated['sell_price'] = 0;
//             $validated['hpp'] = 0;
//             $validated['stock'] = 0;

//             Product::create($validated);
//             return redirect()->route('products.index')->with('success', 'Master Produk berhasil ditambahkan.');
//         } catch (\Exception $e) {
//             return redirect()->back()->withInput()->with('error', 'Gagal menyimpan produk. Pastikan SKU unik.');
//         }
//     }

//     // Memperbarui data produk
//     public function update(Request $request, $id)
//     {
//         $product = Product::findOrFail($id);

//         $validated = $request->validate([
//             'sku'      => 'required|string|max:50|unique:products,sku,' . $product->id,
//             'name'     => 'required|string|max:150',
//             'category' => 'nullable|string|max:50',
//             'unit'     => 'nullable|string|max:20',
//         ]);

//         try {
//             $product->update($validated);
//             return redirect()->route('products.index')->with('success', 'Master Produk berhasil diperbarui.');
//         } catch (\Exception $e) {
//             return redirect()->back()->withInput()->with('error', 'Gagal mengupdate produk.');
//         }
//     }

//     // Menghapus produk
//     public function destroy($id)
//     {
//         try {
//             $product = Product::findOrFail($id);
//             $product->delete();

//             return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
//         } catch (\Exception $e) {
//             return redirect()->back()->with('error', 'Gagal menghapus produk. Mungkin produk ini sudah dipakai di transaksi atau masih memiliki sisa stok di Gudang.');
//         }
//     }
// }

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Menampilkan halaman daftar produk beserta fitur Pencarian
    public function index(Request $request)
    {
        $search = $request->query('search');

        // Tarik data produk & jumlahkan stok dari relasi gudang
        $query = Product::withSum('stocks as total_stock', 'quantity');

        // Jika user mengetikkan kata kunci pencarian
        if ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('sku', 'LIKE', '%' . $search . '%')
                  ->orWhere('category', 'LIKE', '%' . $search . '%');
        }

        $products = $query->orderBy('id', 'desc')->get();
        $categories = Category::orderBy('name', 'asc')->get();

        return view('products.index', compact('products', 'categories', 'search'));
    }

    // Menampilkan detail riwayat produk (Buku Besar Kartu Stok)
    public function show($id)
    {
        $product = Product::withSum('stocks as total_stock', 'quantity')->findOrFail($id);

        // 1. Ambil sebaran stok per gudang
        $stocks = \App\Models\ProductStock::with('warehouse')->where('product_sku', $product->sku)->get();

        // 2. Ambil Riwayat Pembelian (PO) - Barang Masuk
        $purchaseHistory = \App\Models\PurchaseItem::with('purchase.contact')
            ->where('product_sku', $product->sku)
            ->get()
            ->sortByDesc(function($item) { return $item->purchase->date ?? ''; });

        // 3. Ambil Riwayat Penjualan (SO) - Barang Keluar
        $saleHistory = \App\Models\SaleItem::with('sale.contact')
            ->where('product_sku', $product->sku)
            ->get()
            ->sortByDesc(function($item) { return $item->sale->date ?? ''; });

        // 4. Ambil Riwayat Penyesuaian Stok (Stock Opname)
        $adjustments = \App\Models\StockAdjustment::with('warehouse')
            ->where('product_sku', $product->sku)
            ->orderBy('date', 'desc')->orderBy('id', 'desc')
            ->get();

        return view('products.show', compact('product', 'stocks', 'purchaseHistory', 'saleHistory', 'adjustments'));
    }

    // Menyimpan produk baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku'      => 'required|string|max:50|unique:products,sku',
            'name'     => 'required|string|max:150',
            'category' => 'nullable|string|max:50',
            'unit'     => 'nullable|string|max:20',
        ]);

        try {
            // Default nilai awal untuk harga dan stok adalah 0
            $validated['buy_price'] = 0;
            $validated['sell_price'] = 0;
            $validated['hpp'] = 0;
            $validated['stock'] = 0;

            Product::create($validated);
            return redirect()->route('products.index')->with('success', 'Master Produk berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan produk. Pastikan SKU unik.');
        }
    }

    // Memperbarui data produk
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'sku'      => 'required|string|max:50|unique:products,sku,' . $product->id,
            'name'     => 'required|string|max:150',
            'category' => 'nullable|string|max:50',
            'unit'     => 'nullable|string|max:20',
        ]);

        try {
            $product->update($validated);
            return redirect()->route('products.index')->with('success', 'Master Produk berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate produk.');
        }
    }

    // Menghapus produk
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus produk. Mungkin produk ini sudah dipakai di transaksi atau masih memiliki sisa stok di Gudang.');
        }
    }

    // --- FITUR EKSPOR EXCEL & PDF ---
    public function export(Request $request)
    {
        $search = $request->query('search');
        $type = $request->query('type', 'internal');
        $format = $request->query('format', 'csv');

        // 1. Tarik Data Sesuai Filter Pencarian
        $query = Product::withSum('stocks as total_stock', 'quantity');
        if ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('sku', 'LIKE', '%' . $search . '%')
                  ->orWhere('category', 'LIKE', '%' . $search . '%');
        }
        $products = $query->orderBy('name', 'asc')->get();

        // 2. Tentukan Judul Laporan
        $reportName = $type === 'pajak' ? 'DAFTAR MASTER PRODUK (PAJAK)' : 'DAFTAR MASTER PRODUK (INTERNAL)';
        if ($search) {
            $reportName .= ' (Pencarian: ' . strtoupper($search) . ')';
        }

        // 3. EXCEL EXPORT (HTML-TO-EXCEL SPOOFING)
        if ($format === 'csv') {
            $headers = [
                "Content-type"        => "application/vnd.ms-excel",
                "Content-Disposition" => "attachment; filename=Produk_{$type}.xls",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function() use ($products, $reportName) {
                echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>';

                echo '<table border="0" style="font-family: Arial, sans-serif;">';
                echo '<tr><th colspan="7" style="font-size:16px; font-weight:bold; text-align:center;">PT. BERLIAN MANDIRI PERKASA</th></tr>';
                echo '<tr><th colspan="7" style="font-size:14px; font-weight:bold; text-align:center;">' . $reportName . '</th></tr>';
                echo '<tr><td colspan="7"></td></tr></table>';

                echo '<table border="1" style="font-family: Arial, sans-serif; font-size: 12px; border-collapse: collapse;">';
                echo '<thead style="background-color: #e2e8f0;">';
                echo '<tr>';
                echo '<th style="padding:8px; text-align:center; width:50px;">No.</th>';
                echo '<th style="padding:8px; text-align:center; width:100px;">SKU</th>';
                echo '<th style="padding:8px; text-align:center; width:250px;">Nama Produk</th>';
                echo '<th style="padding:8px; text-align:center; width:150px;">Kategori</th>';
                echo '<th style="padding:8px; text-align:center; width:80px;">Satuan</th>';
                echo '<th style="padding:8px; text-align:center; width:120px;">HPP Berjalan</th>';
                echo '<th style="padding:8px; text-align:center; width:100px;">Total Stok</th>';
                echo '</tr></thead><tbody>';

                $no = 1;
                foreach ($products as $p) {
                    $stok = $p->total_stock ?? 0;
                    echo '<tr>';
                    echo '<td style="padding:8px; text-align:center;">' . $no++ . '</td>';
                    echo '<td style="padding:8px; font-weight:bold;">' . $p->sku . '</td>';
                    echo '<td style="padding:8px; font-weight:bold;">' . $p->name . '</td>';
                    echo '<td style="padding:8px; text-align:center;">' . ($p->category ?: '-') . '</td>';
                    echo '<td style="padding:8px; text-align:center;">' . ($p->unit ?: '-') . '</td>';
                    echo '<td style="padding:8px; text-align:right;">Rp ' . number_format($p->hpp, 0, ',', '.') . '</td>';
                    echo '<td style="padding:8px; text-align:center; font-weight:bold;">' . $stok . '</td>';
                    echo '</tr>';
                }

                echo '</tbody></table></body></html>';
            };
            return response()->stream($callback, 200, $headers);
        }

        // 4. PDF EXPORT (Cetak Layar HTML)
        return view('products.export_print', compact('products', 'reportName'));
    }

    // ==========================================================
    // FITUR EMAS: EKSPOR BUKU BESAR BARANG (BIN CARD)
    // ==========================================================
    public function exportLedger(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $format = $request->query('format', 'csv');

        // 1. Ambil Riwayat Pembelian (Masuk)
        $purchases = \App\Models\PurchaseItem::with('purchase.contact')
            ->where('product_sku', $product->sku)
            ->get()
            ->map(function ($item) {
                return [
                    'date' => \Carbon\Carbon::parse($item->purchase->date ?? $item->created_at)->startOfDay(),
                    'type' => 'Barang Masuk (PO)',
                    'ref' => $item->purchase->reference_no ?? '-',
                    'contact' => $item->purchase->contact->name ?? '-',
                    'qty_in' => $item->quantity,
                    'qty_out' => 0,
                    'price' => $item->price
                ];
            });

        // 2. Ambil Riwayat Penjualan (Keluar)
        $sales = \App\Models\SaleItem::with('sale.contact')
            ->where('product_sku', $product->sku)
            ->get()
            ->map(function ($item) {
                return [
                    'date' => \Carbon\Carbon::parse($item->sale->date ?? $item->created_at)->startOfDay(),
                    'type' => 'Barang Keluar (SO)',
                    'ref' => $item->sale->reference_no ?? '-',
                    'contact' => $item->sale->contact->name ?? '-',
                    'qty_in' => 0,
                    'qty_out' => $item->quantity,
                    'price' => $item->price
                ];
            });

        // 3. Ambil Riwayat Penyesuaian (Stock Opname)
        $adjustments = \App\Models\StockAdjustment::with('warehouse')
            ->where('product_sku', $product->sku)
            ->get()
            ->map(function ($item) {
                $isAdd = $item->type == 'Penambahan';
                return [
                    'date' => \Carbon\Carbon::parse($item->date ?? $item->created_at)->startOfDay(),
                    'type' => 'Stock Opname',
                    'ref' => 'ADJ-' . str_pad($item->id, 4, '0', STR_PAD_LEFT),
                    'contact' => $item->warehouse->name ?? 'Gudang',
                    'qty_in' => $isAdd ? $item->quantity : 0,
                    'qty_out' => !$isAdd ? $item->quantity : 0,
                    'price' => 0
                ];
            });

        // Gabungkan, urutkan berdasarkan tanggal, lalu hitung Saldo Akhir Berjalan secara matematis
        $movements = collect()->merge($purchases)->merge($sales)->merge($adjustments)->sortBy('date')->values();

        $runningBalance = 0;
        $history = [];
        foreach ($movements as $m) {
            $runningBalance += $m['qty_in'] - $m['qty_out'];
            $m['balance'] = $runningBalance;
            $history[] = $m;
        }

        $reportTitle = "KARTU STOK (BIN CARD) - " . strtoupper($product->name);
        $dateExport = \Carbon\Carbon::now()->translatedFormat('d F Y H:i');

        // FORMAT EXCEL (CSV)
        if ($format === 'csv') {
            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=Kartu_Stok_{$product->sku}.csv",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function () use ($product, $history, $reportTitle, $dateExport) {
                $file = fopen('php://output', 'w');

                fputcsv($file, ['PT. BERLIAN MANDIRI PERKASA']);
                fputcsv($file, [$reportTitle]);
                fputcsv($file, ['SKU: ' . $product->sku, 'Kategori: ' . ($product->category ?? '-')]);
                fputcsv($file, ['Diekspor pada: ' . $dateExport]);
                fputcsv($file, []);
                fputcsv($file, ['Tanggal', 'No. Referensi', 'Keterangan', 'Kontak / Gudang', 'Masuk (Qty)', 'Keluar (Qty)', 'Saldo Sisa', 'Harga Ref (Rp)']);

                foreach ($history as $row) {
                    fputcsv($file, [
                        $row['date']->format('d/m/Y'),
                        $row['ref'],
                        $row['type'],
                        $row['contact'],
                        $row['qty_in'],
                        $row['qty_out'],
                        $row['balance'],
                        $row['price']
                    ]);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }

        // FORMAT CETAK HTML / PDF LAYAR
        return view('products.print_ledger', compact('product', 'history', 'reportTitle', 'dateExport'));
    }
}
