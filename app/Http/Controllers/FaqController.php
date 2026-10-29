<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    // Menampilkan halaman daftar FAQ (GET /faqs)
    public function index()
    {
        $faqs = Faq::orderBy('category', 'asc')
                   ->orderBy('id', 'desc')
                   ->get();
                   
        return view('faqs.index', compact('faqs'));
    }

    // Menyimpan FAQ baru (POST /faqs)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:50',
            'question' => 'required|string',
            'answer'   => 'required|string',
        ]);

        try {
            Faq::create($validated);
            return redirect()->route('faqs.index')
                ->with('success', 'FAQ berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan FAQ.');
        }
    }

    // Memperbarui data FAQ (PUT /faqs/{id}) --> (Tambahan ekstra untuk kemudahan Anda)
    public function update(Request $request, $id)
    {
        $faq = Faq::findOrFail($id);

        $validated = $request->validate([
            'category' => 'required|string|max:50',
            'question' => 'required|string',
            'answer'   => 'required|string',
        ]);

        try {
            $faq->update($validated);
            return redirect()->route('faqs.index')
                ->with('success', 'FAQ berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate FAQ.');
        }
    }

    // Menghapus FAQ (DELETE /faqs/{id})
    public function destroy($id)
    {
        try {
            $faq = Faq::findOrFail($id);
            $faq->delete();
            
            return redirect()->route('faqs.index')
                ->with('success', 'FAQ berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus FAQ.');
        }
    }
}