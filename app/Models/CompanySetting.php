<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Models\Auth;

class CompanySetting extends Model
{
    public $timestamps = false;
    protected $table = 'company_settings'; // Menyesuaikan snake_case

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'tax_rate',
        'role_permissions'
    ];

    // ==============================================================
    // FUNGSI HELPER GLOBAL UNTUK CEK HAK AKSES (BISA DIPANGGIL DARI VIEW MANAPUN)
    // ==============================================================
    public static function hasAccess($menuName)
    {
        $userRole = Auth::user()->role ?? 'Staff';
        $setting = self::first();
        $perms = $setting && $setting->role_permissions ? json_decode($setting->role_permissions, true) : [];

        if (empty($perms)) return in_array($userRole, ['Admin', 'Direktur']);
        if (isset($perms[$userRole][0]) && is_string($perms[$userRole][0])) return in_array($menuName, $perms[$userRole]);

        return isset($perms[$userRole][$menuName]) && in_array('R', $perms[$userRole][$menuName]);
    }

    public static function canAction($actionLetter, $menuName)
    {
        $userRole = Auth::user()->role ?? 'Staff';
        // Admin dan Direktur secara default kebal dan bisa melakukan semua aksi jika matriks kosong
        if (in_array($userRole, ['Admin', 'Direktur']) && self::count() == 0) return true;

        $setting = self::first();
        $perms = $setting && $setting->role_permissions ? json_decode($setting->role_permissions, true) : [];

        if (empty($perms)) return in_array($userRole, ['Admin', 'Direktur']);
        // Jika masih pakai database versi lama (Array 1D), bebaskan aksi
        if (isset($perms[$userRole][0]) && is_string($perms[$userRole][0])) return true;

        return isset($perms[$userRole][$menuName]) && in_array($actionLetter, $perms[$userRole][$menuName]);
    }
}
