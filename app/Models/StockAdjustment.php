<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'date',
        'product_sku',
        'type', // 'Penambahan' atau 'Pengurangan'
        'quantity',
        'reason',
        'total_value'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_sku', 'sku');
    }
}