<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    // Menampilkan halaman daftar kontak (GET /contacts?type=Customer)
    public function index(Request $request)
    {
        $query = Contact::orderBy('name', 'asc');

        // Tangkap query param jika ada filter tipe (Customer/Supplier)
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        $contacts = $query->get();
        
        return view('contacts.index', compact('contacts'));
    }

    // Menyimpan kontak baru (POST /contacts)
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'name'    => 'required|string|max:150',
    //         'type'    => 'required|in:Customer,Supplier',
    //         'phone'   => 'nullable|string|max:20',
    //         'email'   => 'nullable|email|max:100', // Laravel otomatis memvalidasi format email
    //         'address' => 'nullable|string',
    //     ]);

    //     try {
    //         Contact::create($validated);
    //         return redirect()->route('contacts.index')
    //             ->with('success', 'Kontak berhasil ditambahkan.');
    //     } catch (\Exception $e) {
    //         return redirect()->back()
    //             ->withInput()
    //             ->with('error', 'Gagal menyimpan kontak.');
    //     }
    // }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:150',
            'type'    => 'required|in:Customer,Supplier',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:100',
            'address' => 'nullable|string',
        ]);

        try {
            $contact = Contact::create($validated);
            
            // Jika request datang dari AJAX (Fetch API) di form transaksi, kembalikan JSON
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kontak berhasil ditambahkan.',
                    'contact' => $contact
                ]);
            }

            return redirect()->route('contacts.index')
                ->with('success', 'Kontak berhasil ditambahkan.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan kontak: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan kontak.');
        }
    }

    // Memperbarui data kontak (PUT /contacts/{id})
    public function update(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);

        $validated = $request->validate([
            'name'    => 'required|string|max:150',
            'type'    => 'required|in:Customer,Supplier',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:100',
            'address' => 'nullable|string',
        ]);

        try {
            $contact->update($validated);
            return redirect()->route('contacts.index')
                ->with('success', 'Kontak berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate kontak.');
        }
    }

    // Menghapus kontak (DELETE /contacts/{id})
    public function destroy($id)
    {
        try {
            $contact = Contact::findOrFail($id);
            $contact->delete();
            
            return redirect()->route('contacts.index')
                ->with('success', 'Kontak berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus kontak. Pastikan kontak ini belum dipakai di transaksi Pembelian/Penjualan.');
        }
    }
}