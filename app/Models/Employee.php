<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['nik', 'name', 'position', 'basic_salary', 'has_bpjs', 'bank_account'];
}
