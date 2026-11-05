<!-- resources/views/payroll/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="credit-card" class="w-7 h-7 text-teal-600"></i> Penggajian (Payroll) & HR
            </h1>
            <p class="text-slate-500 mt-1">Kelola data karyawan, komponen gaji, hitung BPJS, dan proses slip gaji.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <button class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-lg font-bold text-sm transition-colors shadow-sm flex items-center gap-2">
                <i data-feather="user-plus" class="w-4 h-4 text-blue-600"></i> Tambah Karyawan
            </button>
            <button class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm transition-colors shadow-sm flex items-center gap-2">
                <i data-feather="play-circle" class="w-4 h-4"></i> Proses Gaji Bulan Ini
            </button>
        </div>
    </div>

    <!-- Ringkasan HRD -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 border-l-4 border-l-blue-500">
            <p class="text-sm text-slate-500 font-bold uppercase mb-2">Total Karyawan Aktif</p>
            <h3 class="text-3xl font-bold text-slate-800">12 <span class="text-sm font-normal text-slate-400">Orang</span></h3>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 border-l-4 border-l-teal-500">
            <p class="text-sm text-slate-500 font-bold uppercase mb-2">Estimasi Beban Gaji Pokok</p>
            <h3 class="text-3xl font-bold text-teal-600">Rp 45.500.000</h3>
            <p class="text-xs text-slate-400 mt-2">Bulan berjalan</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 border-l-4 border-l-rose-500">
            <p class="text-sm text-slate-500 font-bold uppercase mb-2">Estimasi Potongan (BPJS/Pajak)</p>
            <h3 class="text-3xl font-bold text-rose-600">Rp 3.250.000</h3>
            <p class="text-xs text-slate-400 mt-2">Dipotong dari gaji kotor</p>
        </div>
    </div>

    <!-- Tabel Data Karyawan -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50 font-bold text-slate-700 flex justify-between items-center">
            <span class="flex items-center gap-2"><i data-feather="users" class="w-5 h-5 text-slate-500"></i> Database Karyawan (Master Data)</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse min-w-[900px]">
                <thead class="bg-white border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-slate-600 w-24">NIK</th>
                        <th class="p-4 text-slate-600">Nama Lengkap</th>
                        <th class="p-4 text-slate-600">Jabatan</th>
                        <th class="p-4 text-slate-600 text-right">Gaji Pokok (Rp)</th>
                        <th class="p-4 text-slate-600 text-center">Status BPJS</th>
                        <th class="p-4 text-slate-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Contoh Data Statis untuk Tampilan Awal -->
                    <tr class="border-b hover:bg-slate-50 transition-colors">
                        <td class="p-4 font-mono font-bold text-slate-500">EMP-001</td>
                        <td class="p-4 font-bold text-slate-800">Melisa Harijanto</td>
                        <td class="p-4"><span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-bold border border-blue-200">Direktur</span></td>
                        <td class="p-4 text-right font-bold text-teal-600">15.000.000</td>
                        <td class="p-4 text-center">
                            <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded text-xs font-bold">Terdaftar</span>
                        </td>
                        <td class="p-4 text-center">
                            <button class="text-blue-500 hover:bg-blue-50 p-2 rounded transition-colors"><i data-feather="edit" class="w-4 h-4"></i></button>
                        </td>
                    </tr>
                    <tr class="border-b hover:bg-slate-50 transition-colors">
                        <td class="p-4 font-mono font-bold text-slate-500">EMP-002</td>
                        <td class="p-4 font-bold text-slate-800">Nita</td>
                        <td class="p-4"><span class="px-2 py-1 bg-slate-100 text-slate-700 rounded text-xs font-bold border border-slate-200">Staf Administrasi</span></td>
                        <td class="p-4 text-right font-bold text-teal-600">4.500.000</td>
                        <td class="p-4 text-center">
                            <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded text-xs font-bold">Terdaftar</span>
                        </td>
                        <td class="p-4 text-center">
                            <button class="text-blue-500 hover:bg-blue-50 p-2 rounded transition-colors"><i data-feather="edit" class="w-4 h-4"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
