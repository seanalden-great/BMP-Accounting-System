<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\ProductStock;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    // Menampilkan halaman daftar gudang
    public function index()
    {
        $warehouses = Warehouse::orderBy('code', 'asc')->get();
        return view('inventory.warehouses', compact('warehouses'));
    }

    // Menyimpan data gudang baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'     => 'required|string|unique:warehouses,code',
            'name'     => 'required|string|max:100',
            'location' => 'nullable|string',
        ]);

        try {
            Warehouse::create($validated);
            return redirect()->back()->with('success', 'Gudang berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan gudang.');
        }
    }

    // Memperbarui data gudang yang sudah ada
    public function update(Request $request, $id)
    {
        $warehouse = Warehouse::findOrFail($id);

        $validated = $request->validate([
            // Pengecualian unik untuk ID gudang yang sedang diedit
            'code'     => 'required|string|unique:warehouses,code,' . $id,
            'name'     => 'required|string|max:100',
            'location' => 'nullable|string',
        ]);

        try {
            $warehouse->update($validated);
            return redirect()->back()->with('success', 'Data gudang berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data gudang.');
        }
    }

    // Menghapus data gudang
    public function destroy($id)
    {
        try {
            $warehouse = Warehouse::findOrFail($id);
            
            // Proteksi 1: Gudang ID 1 (Gudang Utama bawaan sistem) tidak boleh dihapus
            if ($warehouse->id == 1) {
                return redirect()->back()->with('error', 'Gudang Utama tidak dapat dihapus dari sistem.');
            }

            // Proteksi 2: Jangan izinkan hapus jika masih ada stok fisik (> 0) di dalamnya
            $stockCount = ProductStock::where('warehouse_id', $id)->where('quantity', '>', 0)->count();
            if ($stockCount > 0) {
                return redirect()->back()->with('error', 'Gudang tidak dapat dihapus karena masih menyimpan sisa stok barang fisik.');
            }

            $warehouse->delete();
            return redirect()->back()->with('success', 'Gudang berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus gudang karena masih terkait dengan data riwayat penyesuaian atau transaksi.');
        }
    }
}