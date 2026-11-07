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

    // --- FITUR EKSPOR EXCEL & PDF ---
    public function export(Request $request)
    {
        $contactType = $request->query('contact_type'); // 'Customer' atau 'Supplier' (opsional)
        $type = $request->query('type', 'internal'); // 'internal' atau 'pajak'
        $format = $request->query('format', 'csv'); // 'csv' atau 'pdf'

        // 1. Tarik Data Sesuai Filter
        $query = Contact::orderBy('name', 'asc');
        if ($contactType) {
            $query->where('type', $contactType);
        }
        $contacts = $query->get();

        // 2. Tentukan Judul Laporan
        $reportName = $type === 'pajak' ? 'DAFTAR KONTAK / RELASI BUKU (PAJAK)' : 'DAFTAR KONTAK / RELASI (INTERNAL)';
        if ($contactType) {
            $reportName .= ' - ' . strtoupper($contactType);
        }

        // 3. EXCEL EXPORT (HTML-TO-EXCEL SPOOFING)
        if ($format === 'csv') {
            $headers = [
                "Content-type"        => "application/vnd.ms-excel",
                "Content-Disposition" => "attachment; filename=Kontak_{$type}.xls",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function() use ($contacts, $reportName) {
                echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>';

                echo '<table border="0" style="font-family: Arial, sans-serif;">';
                echo '<tr><th colspan="6" style="font-size:16px; font-weight:bold; text-align:center;">PT. BERLIAN MANDIRI PERKASA</th></tr>';
                echo '<tr><th colspan="6" style="font-size:14px; font-weight:bold; text-align:center;">' . $reportName . '</th></tr>';
                echo '<tr><td colspan="6"></td></tr></table>';

                echo '<table border="1" style="font-family: Arial, sans-serif; font-size: 12px; border-collapse: collapse;">';
                echo '<thead style="background-color: #e2e8f0;">';
                echo '<tr>';
                echo '<th style="padding:8px; text-align:center; width:50px;">No.</th>';
                echo '<th style="padding:8px; text-align:center; width:200px;">Nama Kontak</th>';
                echo '<th style="padding:8px; text-align:center; width:100px;">Tipe</th>';
                echo '<th style="padding:8px; text-align:center; width:150px;">No. Telepon</th>';
                echo '<th style="padding:8px; text-align:center; width:200px;">Email</th>';
                echo '<th style="padding:8px; text-align:center; width:300px;">Alamat Lengkap</th>';
                echo '</tr></thead><tbody>';

                $no = 1;
                foreach ($contacts as $contact) {
                    echo '<tr>';
                    echo '<td style="padding:8px; text-align:center;">' . $no++ . '</td>';
                    echo '<td style="padding:8px; font-weight:bold;">' . $contact->name . '</td>';
                    echo '<td style="padding:8px; text-align:center;">' . $contact->type . '</td>';
                    echo '<td style="padding:8px;">' . ($contact->phone ?: '-') . '</td>';
                    echo '<td style="padding:8px;">' . ($contact->email ?: '-') . '</td>';
                    echo '<td style="padding:8px;">' . ($contact->address ?: '-') . '</td>';
                    echo '</tr>';
                }

                echo '</tbody></table></body></html>';
            };
            return response()->stream($callback, 200, $headers);
        }

        // 4. PDF EXPORT (Cetak Layar HTML)
        return view('contacts.export_print', compact('contacts', 'reportName'));
    }
}
