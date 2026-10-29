<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FixedAsset extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'purchase_date',
        'purchase_price',
        'useful_life',
        'accum_depreciation',
        'status'
    ];
}