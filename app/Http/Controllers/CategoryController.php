<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Menampilkan halaman daftar kategori (GET /categories)
    public function index()
    {
        $categories = Category::orderBy('name', 'asc')->get();
        return view('categories.index', compact('categories'));
    }

    // Menyimpan kategori baru (POST /categories)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        try {
            Category::create($validated);
            return redirect()->route('categories.index')
                ->with('success', 'Kategori berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Kategori dengan nama tersebut mungkin sudah ada.');
        }
    }

    // Memperbarui data kategori (PUT /categories/{id})
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        try {
            $category->update($validated);
            return redirect()->route('categories.index')
                ->with('success', 'Kategori berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate kategori.');
        }
    }

    // Menghapus kategori (DELETE /categories/{id})
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();
            
            return redirect()->route('categories.index')
                ->with('success', 'Kategori berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus kategori. Data ini mungkin terikat dengan tabel produk.');
        }
    }
}