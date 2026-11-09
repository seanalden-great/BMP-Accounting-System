<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = ['user_id', 'action', 'module', 'description', 'ip_address'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    // Fungsi canggih agar Anda bisa memanggil: AuditLog::record('HAPUS', 'Produk', 'Menghapus barang X');
    public static function record($action, $module, $description)
    {
        self::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'module'      => $module,
            'description' => $description,
            'ip_address'  => request()->ip()
        ]);
    }
}
