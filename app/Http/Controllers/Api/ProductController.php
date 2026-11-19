<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductStock;
use App\Models\PurchaseItem;
use App\Models\SaleItem;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // GET /api/products?search=...
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = Product::withSum('stocks as total_stock', 'quantity');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('sku', 'LIKE', '%' . $search . '%')
                  ->orWhere('category', 'LIKE', '%' . $search . '%');
            });
        }

        $products = $query->orderBy('id', 'desc')->get();
        $categories = Category::orderBy('name', 'asc')->pluck('name');

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $products,
                'categories' => $categories,
            ]
        ], 200);
    }

    // GET /api/products/{id}
    public function show($id)
    {
        $product = Product::withSum('stocks as total_stock', 'quantity')->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan.'
            ], 404);
        }

        $stocks = ProductStock::with('warehouse:id,code,name')
            ->where('product_sku', $product->sku)
            ->get();

        $saleHistory = SaleItem::with(['sale:id,date,reference_no,contact_id', 'sale.contact:id,name'])
            ->where('product_sku', $product->sku)
            ->get()
            ->sortByDesc(fn($item) => $item->sale->date ?? '')
            ->values();

        $purchaseHistory = PurchaseItem::with(['purchase:id,date,reference_no,contact_id', 'purchase.contact:id,name'])
            ->where('product_sku', $product->sku)
            ->get()
            ->sortByDesc(fn($item) => $item->purchase->date ?? '')
            ->values();

        $adjustments = StockAdjustment::with('warehouse:id,name')
            ->where('product_sku', $product->sku)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'product' => $product,
                'stocks' => $stocks,
                'sale_history' => $saleHistory,
                'purchase_history' => $purchaseHistory,
                'adjustments' => $adjustments,
            ]
        ], 200);
    }

    // POST /api/products
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku'      => 'required|string|max:50|unique:products,sku',
            'name'     => 'required|string|max:150',
            'category' => 'nullable|string|max:50',
            'unit'     => 'nullable|string|max:20',
        ]);

        try {
            $validated['sku']        = strtoupper($validated['sku']);
            $validated['buy_price']  = 0;
            $validated['sell_price'] = 0;
            $validated['hpp']        = 0;
            $validated['stock']      = 0;

            $product = Product::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Master Produk berhasil ditambahkan.',
                'data'    => $product
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan produk. Pastikan SKU unik.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // PUT /api/products/{id}
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'sku'      => 'required|string|max:50|unique:products,sku,' . $product->id,
            'name'     => 'required|string|max:150',
            'category' => 'nullable|string|max:50',
            'unit'     => 'nullable|string|max:20',
        ]);

        try {
            $validated['sku'] = strtoupper($validated['sku']);
            $product->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Master Produk berhasil diperbarui.',
                'data'    => $product
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui produk.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // DELETE /api/products/{id}
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus produk. Produk mungkin sudah dipakai di transaksi atau masih memiliki stok.'
            ], 400);
        }
    }
}
