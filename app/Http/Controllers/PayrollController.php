<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PayrollController extends Controller
{
    // Menampilkan halaman payroll
    public function index()
    {
        return view("payroll.index");
    }
}
