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
}
