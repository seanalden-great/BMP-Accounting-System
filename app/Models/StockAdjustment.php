<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'date',
        'warehouse_id', // Tambahan baru untuk menyimpan ID Gudang
        'product_sku',
        'type',
        'quantity',
        'reason',
        'total_value',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_sku', 'sku');
    }

    // Relasi baru ke model Warehouse
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
}