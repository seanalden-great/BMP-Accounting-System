<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    // Menampilkan Halaman Pengaturan (GET /settings)
    public function index()
    {
        // Selalu ambil record ID 1. Jika tidak ada, buatkan otomatis.
        $setting = CompanySetting::firstOrCreate(
            ['id' => 1],
            ['name' => 'PT Berlianmandiri Perkasa', 'tax_rate' => 11.0]
        );

        return view('settings.index', compact('setting'));
    }

    // Memperbarui Pengaturan (PUT /settings)
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'address'  => 'nullable|string',
            'phone'    => 'nullable|string|max:20',
            'email'    => 'nullable|email|max:100',
            'tax_rate' => 'required|numeric|min:0',
        ]);

        try {
            $setting = CompanySetting::findOrFail(1);
            $setting->update($validated);

            return redirect()->route('settings.index')->with('success', 'Profil Perusahaan berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pengaturan.');
        }
    }
}