<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;

// class Product extends Model
// {
//     public $timestamps = false;

//     protected $fillable = [
//         'sku',
//         'name',
//         'category',
//         'unit',
//         'buy_price',
//         'sell_price',
//         'hpp',
//         'stock'
//     ];
// }

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $timestamps = false;

    // Biarkan tetap fillable, karena controller Pembelian/Penjualan/Inventori masih butuh meng-update HPP/Stok ke sini
    protected $fillable = [
        'sku',
        'name',
        'category',
        'unit',
        'buy_price',
        'sell_price',
        'hpp',
        'stock'
    ];

    // Relasi untuk mengambil riwayat jumlah stok fisik dari seluruh Gudang
    public function stocks()
    {
        return $this->hasMany(ProductStock::class, 'product_sku', 'sku');
    }
}