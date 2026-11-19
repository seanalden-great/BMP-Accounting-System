<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    // GET /api/settings/roles
    public function index(Request $request)
    {
        $setting = CompanySetting::firstOrCreate(
            ['id' => 1],
            ['name' => 'PT Berlianmandiri Perkasa', 'tax_rate' => 11.0]
        );

        $permissions = $setting->role_permissions
            ? json_decode($setting->role_permissions, true)
            : [];

        // Daftar Role & Menu Mobile (Profil selalu bisa diakses semua role)
        $roles = ['Admin', 'Direktur', 'Gudang', 'Developer', 'Staff'];
        $mobileMenus = ['Dashboard', 'Inventori', 'Produk', 'Persetujuan'];

        return response()->json([
            'success' => true,
            'data' => [
                'current_role' => $request->user()->role ?? 'Staff',
                'roles'        => $roles,
                'menus'        => $mobileMenus,
                'permissions'  => $permissions,
            ]
        ], 200);
    }

    // PUT /api/settings/roles
    public function update(Request $request)
    {
        // Hanya Admin, Direktur, atau Developer yang boleh mengubah matriks
        if (!in_array($request->user()->role, ['Admin', 'Direktur', 'Developer'])) {
            return response()->json([
                'success' => false,
                'message' => 'Akses Ditolak: Anda tidak memiliki izin mengubah hak akses.'
            ], 403);
        }

        $setting = CompanySetting::firstOrCreate(
            ['id' => 1],
            ['name' => 'PT Berlianmandiri Perkasa', 'tax_rate' => 11.0]
        );

        // Gabungkan dengan permissions web yang sudah ada agar data web tidak tertimpa hilang
        $existing = $setting->role_permissions
            ? json_decode($setting->role_permissions, true)
            : [];

        $incoming = $request->input('permissions', []);

        foreach ($incoming as $role => $menus) {
            if (!isset($existing[$role]) || !is_array($existing[$role])) {
                $existing[$role] = [];
            }
            foreach ($menus as $menu => $actions) {
                $existing[$role][$menu] = $actions;
            }
        }

        $setting->role_permissions = json_encode($existing);
        $setting->save();

        return response()->json([
            'success' => true,
            'message' => 'Matriks Hak Akses berhasil diperbarui!',
            'data'    => $existing
        ], 200);
    }
}
