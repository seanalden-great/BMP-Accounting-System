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

    <!-- Ringkasan HRD (Sekarang Terhubung dengan Database) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 border-l-4 border-l-blue-500">
            <p class="text-sm text-slate-500 font-bold uppercase mb-2">Total Karyawan Aktif</p>
            <h3 class="text-3xl font-bold text-slate-800">{{ $totalEmployees }} <span class="text-sm font-normal text-slate-400">Orang</span></h3>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 border-l-4 border-l-teal-500">
            <p class="text-sm text-slate-500 font-bold uppercase mb-2">Estimasi Beban Gaji Pokok</p>
            <h3 class="text-3xl font-bold text-teal-600">Rp {{ number_format($estimasiGajiPokok, 0, ',', '.') }}</h3>
            <p class="text-xs text-slate-400 mt-2">Bulan berjalan</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 border-l-4 border-l-rose-500">
            <p class="text-sm text-slate-500 font-bold uppercase mb-2">Estimasi Potongan (BPJS 3%)</p>
            <h3 class="text-3xl font-bold text-rose-600">Rp {{ number_format($estimasiPotongan, 0, ',', '.') }}</h3>
            <p class="text-xs text-slate-400 mt-2">Dipotong dari gaji kotor</p>
        </div>
    </div>

    <!-- Tabel Data Karyawan (Sekarang Menggunakan Loop Data Asli) -->
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
                    @forelse($employees as $emp)
                    <tr class="border-b hover:bg-slate-50 transition-colors">
                        <td class="p-4 font-mono font-bold text-slate-500">{{ $emp->nik }}</td>
                        <td class="p-4 font-bold text-slate-800">{{ $emp->name }}</td>
                        <td class="p-4"><span class="px-2 py-1 bg-slate-100 text-slate-700 rounded text-xs font-bold border border-slate-200">{{ $emp->position }}</span></td>
                        <td class="p-4 text-right font-bold text-teal-600">{{ number_format($emp->basic_salary, 0, ',', '.') }}</td>
                        <td class="p-4 text-center">
                            @if($emp->has_bpjs)
                                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded text-xs font-bold">Terdaftar</span>
                            @else
                                <span class="px-2 py-1 bg-rose-100 text-rose-700 rounded text-xs font-bold">Tidak Terdaftar</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            <button class="text-blue-500 hover:bg-blue-50 p-2 rounded transition-colors" title="Edit Karyawan"><i data-feather="edit" class="w-4 h-4"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-500 font-medium">Belum ada data Karyawan yang tersimpan di sistem. Silakan tekan tombol 'Tambah Karyawan'.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
