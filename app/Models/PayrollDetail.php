<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollDetail extends Model
{
    protected $fillable = ['payroll_id', 'employee_id', 'basic_salary', 'allowance', 'bpjs_deduction', 'tax_deduction', 'net_salary'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
