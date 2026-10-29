<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    public $timestamps = false;
    protected $table = 'company_settings'; // Menyesuaikan snake_case

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'tax_rate'
    ];
}