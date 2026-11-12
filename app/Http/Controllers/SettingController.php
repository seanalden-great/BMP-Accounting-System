<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    // Menampilkan Halaman Pengaturan (GET /settings)
    // public function index()
    // {
    //     // Selalu ambil record ID 1. Jika tidak ada, buatkan otomatis.
    //     $setting = CompanySetting::firstOrCreate(
    //         ['id' => 1],
    //         ['name' => 'PT Berlianmandiri Perkasa', 'tax_rate' => 11.0]
    //     );

    //     return view('settings.index', compact('setting'));
    // }

    // Menampilkan Halaman Pengaturan (GET /settings)
    public function index()
    {
        // Selalu ambil record ID 1. Jika tidak ada, buatkan otomatis.
        $setting = CompanySetting::firstOrCreate(
            ['id' => 1],
            ['name' => 'PT Berlianmandiri Perkasa', 'tax_rate' => 11.0]
        );

        // --- TAMBAHAN: Tarik data statistik untuk dasbor mini pengaturan ---
        $totalUsers = \App\Models\User::count();
        $adminCount = \App\Models\User::whereIn('role', ['Admin', 'Direktur'])->count();
        $staffCount = $totalUsers - $adminCount;

        $dbSize = 0;
        try {
            // Coba ambil ukuran database (Hanya jalan di MySQL/MariaDB)
            $dbName = env('DB_DATABASE');
            $sizeResult = \Illuminate\Support\Facades\DB::select("SELECT SUM(data_length + index_length) / 1024 / 1024 AS size FROM information_schema.tables WHERE table_schema = '$dbName'");
            $dbSize = round($sizeResult[0]->size ?? 0, 2);
        } catch (\Exception $e) {
            $dbSize = 'N/A'; // Abaikan jika gagal (misal pakai SQLite)
        }

        return view('settings.index', compact('setting', 'totalUsers', 'adminCount', 'staffCount', 'dbSize'));
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

    // --- FITUR BARU: MANAJEMEN HAK AKSES ROLE (RBAC) ---
    public function rolePermissions()
    {
        $setting = CompanySetting::firstOrCreate(['id' => 1], ['name' => 'PT Berlianmandiri Perkasa', 'tax_rate' => 11.0]);

        // Decode data JSON dari database menjadi Array PHP
        $permissions = $setting->role_permissions ? json_decode($setting->role_permissions, true) : [];

        // Definisikan Role dan Menu yang ada di ERP Anda
        $roles = ['Admin', 'Gudang', 'Developer', 'Direktur'];
        $menus = [
            'Dashboard', 'Penjualan', 'Pembelian', 'Biaya', 'Produk',
            'Inventori', 'Aset Tetap', 'Kas & Bank', 'Akun CoA',
            'Kontak', 'Laporan Keuangan', 'Pengaturan', 'Penggajian'
        ];

        return view('settings.roles', compact('roles', 'menus', 'permissions'));
    }

    public function updateRolePermissions(Request $request)
    {
        $setting = CompanySetting::first();

        // $request->permissions berisi array 2D hasil centangan HTML
        $setting->role_permissions = json_encode($request->permissions ?? []);
        $setting->save();

        return redirect()->route('settings.roles')->with('success', 'Matriks Hak Akses (Role) berhasil diperbarui dan diterapkan!');
    }

    // =========================================================
    // FITUR BARU: MANAJEMEN PENGGUNA TERDAFTAR (USER MANAGEMENT)
    // =========================================================
    public function users()
    {
        // Ambil semua data user, urutkan berdasarkan nama
        $users = \App\Models\User::orderBy('name', 'asc')->get();
        // Daftar jabatan standar di perusahaan
        $roles = ['Admin', 'Direktur', 'Gudang', 'Developer', 'Staff'];

        return view('settings.users', compact('users', 'roles'));
    }

    public function updateUser(Request $request, $id)
    {
        $request->validate(['role' => 'required|string']);
        $user = \App\Models\User::findOrFail($id);

        // Pengaman: Cegah eksekutif menurunkan jabatannya sendiri saat sedang login
        if ($user->id === auth()->id() && !in_array($request->role, ['Admin', 'Direktur'])) {
            return redirect()->back()->with('error', 'Akses Ditolak: Anda tidak diizinkan menurunkan jabatan akun Anda sendiri (Anti-Lockout).');
        }

        $user->update(['role' => $request->role]);
        return redirect()->back()->with('success', "Hak akses untuk {$user->name} berhasil diperbarui menjadi {$request->role}!");
    }

    public function deleteUser($id)
    {
        $user = \App\Models\User::findOrFail($id);

        // Pengaman bunuh diri: Tidak boleh menghapus akun yang sedang dipakai
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Akses Ditolak: Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'Akun pengguna berhasil dihapus permanen dari sistem ERP.');
    }
}
