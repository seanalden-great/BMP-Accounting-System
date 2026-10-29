<!-- resources/views/settings/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10 max-w-4xl mx-auto md:mx-0">
    <!-- HEADER -->
    <div class="flex justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="settings" class="text-slate-600 w-7 h-7"></i> Pengaturan Sistem
            </h1>
            <p class="text-slate-500 mt-1">Kelola profil perusahaan dan konfigurasi dasar aplikasi ERP Anda</p>
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
@endsection