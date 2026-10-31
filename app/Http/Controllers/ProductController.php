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

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Menampilkan halaman daftar produk
    public function index()
    {
        // Ambil data produk sekaligus menjumlahkan kolom 'quantity' dari relasi 'stocks'
        $products = Product::withSum('stocks as total_stock', 'quantity')->orderBy('id', 'desc')->get();
        
        $categories = Category::orderBy('name', 'asc')->get(); 
        
        return view('products.index', compact('products', 'categories'));
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
}