{{-- <!-- resources/views/settings/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10 max-w-4xl mx-auto md:mx-0">
    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="settings" class="text-slate-600 w-7 h-7"></i> Pengaturan Sistem
            </h1>
            <p class="text-slate-500 mt-1">Kelola profil perusahaan dan konfigurasi dasar aplikasi ERP Anda</p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
            <!-- TOMBOL DAFTAR PENGGUNA (BARU) -->
            <a href="{{ route('settings.users') }}" class="w-full sm:w-auto bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 px-5 py-2.5 rounded-lg font-bold transition-colors shadow-sm flex items-center justify-center gap-2">
                <i data-feather="users" class="w-4 h-4 text-blue-600"></i> Daftar Pengguna
            </a>

            <!-- TOMBOL HAK AKSES (LAMA) -->
            <a href="{{ route('settings.roles') }}" class="w-full sm:w-auto bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-lg font-bold transition-colors shadow-sm flex items-center justify-center gap-2">
                <i data-feather="shield" class="w-4 h-4 text-emerald-400"></i> Hak Akses (Role)
            </a>
        </div>
    </div>

    <!-- FORM PENGATURAN -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <form action="{{ route('settings.update') }}" method="POST" class="divide-y divide-slate-100">
            @csrf
            @method('PUT')

            <div class="p-6 md:p-8">
                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <i data-feather="monitor" class="w-5 h-5 text-slate-400"></i> Profil Perusahaan
                </h3>

                <div class="space-y-5 max-w-2xl">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Perusahaan</label>
                        <input type="text" name="name" required value="{{ old('name', $setting->name) }}" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-1">
                                <i data-feather="phone" class="w-3.5 h-3.5"></i> Nomor Telepon
                            </label>
                            <input type="text" name="phone" value="{{ old('phone', $setting->phone) }}" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-1">
                                <i data-feather="mail" class="w-3.5 h-3.5"></i> Email Resmi
                            </label>
                            <input type="email" name="email" value="{{ old('email', $setting->email) }}" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-1">
                            <i data-feather="map-pin" class="w-3.5 h-3.5"></i> Alamat Lengkap
                        </label>
                        <textarea name="address" rows="3" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('address', $setting->address) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="p-6 md:p-8 bg-slate-50">
                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <i data-feather="percent" class="w-5 h-5 text-slate-400"></i> Konfigurasi Keuangan
                </h3>
                <div class="max-w-xs">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Pajak Pertambahan Nilai (PPN) %</label>
                    <div class="relative">
                        <input type="number" step="0.1" name="tax_rate" required value="{{ old('tax_rate', $setting->tax_rate) }}" class="w-full pl-4 pr-10 py-2.5 border border-slate-300 rounded-lg font-bold outline-none focus:ring-2 focus:ring-blue-500">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 font-bold text-slate-400">%</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-2">Nilai ini akan dijadikan default saat mencetak tagihan ke pelanggan.</p>
                </div>
            </div>

            <div class="p-5 flex justify-end gap-3 bg-white">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2.5 rounded-lg font-bold flex items-center gap-2 transition-colors">
                    <i data-feather="save" class="w-4 h-4"></i> Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection --}}

@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10 max-w-5xl mx-auto md:mx-0">

    <!-- HEADER & PUSAT KENDALI -->
    <div class="flex flex-col md:flex-row gap-6">
        <!-- Teks Header -->
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="settings" class="text-slate-600 w-8 h-8"></i> Pengaturan Sistem
            </h1>
            <p class="text-slate-500 mt-1 mb-6">Kelola profil perusahaan, konfigurasi finansial, dan otoritas pengguna sistem ERP Anda.</p>

            <!-- Dasbor Mini Statistik Sistem -->
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-lg shrink-0">
                        <i data-feather="users" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-0.5">Total Pengguna</p>
                        <h4 class="text-xl font-black text-slate-800 leading-none">
                            {{ $totalUsers }} <span class="text-[10px] font-medium text-slate-400 ml-1">Akun Terdaftar</span>
                        </h4>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
                    <div class="p-3 bg-emerald-50 text-emerald-600 rounded-lg shrink-0">
                        <i data-feather="shield" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-0.5">Administrator</p>
                        <h4 class="text-xl font-black text-slate-800 leading-none">
                            {{ $adminCount }} <span class="text-[10px] font-medium text-slate-400 ml-1">Memiliki Akses Penuh</span>
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Akses Cepat (Menu Navigasi Pengaturan) -->
        <div class="w-full md:w-72 shrink-0 flex flex-col gap-3">
            <a href="{{ route('settings.users') }}" class="group bg-white border border-slate-200 hover:border-blue-300 p-4 rounded-xl shadow-sm flex justify-between items-center transition-all hover:shadow-md">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-lg"><i data-feather="users" class="w-4 h-4"></i></div>
                    <div>
                        <p class="font-bold text-slate-800 text-sm group-hover:text-blue-700 transition-colors">Daftar Pengguna</p>
                        <p class="text-[10px] text-slate-500">Kelola {{ $totalUsers }} staf terdaftar</p>
                    </div>
                </div>
                <i data-feather="chevron-right" class="w-4 h-4 text-slate-400 group-hover:translate-x-1 transition-transform"></i>
            </a>

            <a href="{{ route('settings.roles') }}" class="group bg-white border border-slate-200 hover:border-emerald-300 p-4 rounded-xl shadow-sm flex justify-between items-center transition-all hover:shadow-md">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg"><i data-feather="lock" class="w-4 h-4"></i></div>
                    <div>
                        <p class="font-bold text-slate-800 text-sm group-hover:text-emerald-700 transition-colors">Matriks Hak Akses</p>
                        <p class="text-[10px] text-slate-500">Otorisasi Role (RBAC)</p>
                    </div>
                </div>
                <i data-feather="chevron-right" class="w-4 h-4 text-slate-400 group-hover:translate-x-1 transition-transform"></i>
            </a>

            <div class="bg-slate-800 p-4 rounded-xl shadow-sm flex justify-between items-center text-white">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-slate-700 text-slate-300 rounded-lg"><i data-feather="database" class="w-4 h-4"></i></div>
                    <div>
                        <p class="font-bold text-sm">Ukuran Database</p>
                        <p class="text-[10px] text-slate-400">Server MySQL Aktif</p>
                    </div>
                </div>
                <span class="font-black text-sm">{{ is_numeric($dbSize) ? $dbSize . ' MB' : $dbSize }}</span>
            </div>
        </div>
    </div>

    <!-- FORM PENGATURAN UTAMA -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mt-2">
        <form action="{{ route('settings.update') }}" method="POST" class="divide-y divide-slate-100">
            @csrf
            @method('PUT')

            <!-- Bagian Identitas Perusahaan -->
            <div class="p-6 md:p-8">
                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <i data-feather="briefcase" class="w-5 h-5 text-slate-400"></i> Identitas & Kontak Perusahaan
                </h3>

                <div class="space-y-5 max-w-3xl">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Perusahaan (Akan tercetak di semua kop surat)</label>
                        <input type="text" name="name" required value="{{ old('name', $setting->name) }}" class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 font-bold text-slate-800">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-1">
                                <i data-feather="phone" class="w-3.5 h-3.5 text-slate-400"></i> Nomor Telepon Resmi
                            </label>
                            <input type="text" name="phone" value="{{ old('phone', $setting->phone) }}" placeholder="Cth: (021) 1234567" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 transition-shadow">
                        </div>
                        <div>
                            <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-1">
                                <i data-feather="mail" class="w-3.5 h-3.5 text-slate-400"></i> Email Utama
                            </label>
                            <input type="email" name="email" value="{{ old('email', $setting->email) }}" placeholder="admin@perusahaan.com" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 transition-shadow">
                        </div>
                    </div>

                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-1">
                            <i data-feather="map-pin" class="w-3.5 h-3.5 text-slate-400"></i> Alamat Lengkap
                        </label>
                        <textarea name="address" rows="3" placeholder="Masukkan alamat lengkap beserta kode pos..." class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500 resize-none transition-shadow">{{ old('address', $setting->address) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Bagian Konfigurasi Finansial -->
            <div class="p-6 md:p-8 bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <i data-feather="percent" class="w-5 h-5 text-slate-400"></i> Parameter Finansial Default
                </h3>
                <div class="max-w-sm bg-white p-5 border border-slate-200 rounded-xl shadow-sm">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Tarif PPN (Pajak Pertambahan Nilai)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-feather="pie-chart" class="w-4 h-4 text-slate-400"></i>
                        </div>
                        <input type="number" step="0.1" name="tax_rate" required value="{{ old('tax_rate', $setting->tax_rate) }}" class="w-full pl-12 pr-10 py-3 border border-slate-300 rounded-lg font-black text-lg text-slate-700 outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 font-black text-slate-400 text-lg">%</span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-3 font-medium leading-relaxed">Nilai ini akan dijadikan *default* saat tim Sales membuat penawaran atau tim Purchasing mencatat draf belanja. Sesuai aturan DJP terbaru.</p>
                </div>
            </div>

            <!-- Footer Simpan -->
            <div class="p-5 flex justify-end gap-3 bg-white">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-bold flex items-center gap-2 transition-all shadow-md hover:shadow-lg">
                    <i data-feather="save" class="w-4 h-4"></i> Simpan Pengaturan Sistem
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
