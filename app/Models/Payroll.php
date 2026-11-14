<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = ['period_month', 'period_year', 'total_gross', 'total_deduction', 'total_net', 'status', 'journal_id'];

    public function details()
    {
        return $this->hasMany(PayrollDetail::class);
    }
}
