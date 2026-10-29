<!-- resources/views/fixed_assets/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">
    <div class="flex justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="briefcase" class="text-slate-700 w-7 h-7"></i> Aset Tetap (Fixed Assets)
            </h1>
            <p class="text-slate-500 mt-1">Kelola pembelian inventaris perusahaan dan jurnal penyusutan otomatis</p>
        </div>
        <button onclick="document.getElementById('assetModal').classList.remove('hidden')" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
            <i data-feather="plus" class="w-4 h-4"></i> Beli Aset Baru
        </button>
    </div>

    <!-- Tabel Daftar Aset -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead class="bg-white border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600">Nama Aset</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Tgl Beli</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Harga Beli</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Akumulasi Susut</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Nilai Buku</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $ast)
                    @php $bookValue = $ast->purchase_price - $ast->accum_depreciation; @endphp
                    <tr class="border-b border-slate-50 hover:bg-slate-50">
                        <td class="p-4 text-sm font-bold text-slate-800">
                            {{ $ast->name }} <br/>
                            <span class="text-xs font-normal text-slate-400">Umur: {{ $ast->useful_life }} Bln</span>
                        </td>
                        <td class="p-4 text-sm text-slate-600">{{ \Carbon\Carbon::parse($ast->purchase_date)->format('d/m/Y') }}</td>
                        <td class="p-4 text-sm text-right text-slate-800 font-medium">Rp {{ number_format($ast->purchase_price, 0, ',', '.') }}</td>
                        <td class="p-4 text-sm text-right text-red-600 font-medium">Rp {{ number_format($ast->accum_depreciation, 0, ',', '.') }}</td>
                        <td class="p-4 text-sm text-right text-emerald-700 font-bold">Rp {{ number_format($bookValue, 0, ',', '.') }}</td>
                        <td class="p-4 text-center">
                            @if($ast->status === 'Aktif')
                                <form action="{{ route('fixed-assets.depreciate', $ast->id) }}" method="POST" onsubmit="return confirm('Proses penyusutan 1 bulan untuk aset ini? Jurnal akan otomatis dibuat.');">
                                    @csrf
                                    <button type="submit" class="bg-orange-100 hover:bg-orange-200 text-orange-700 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center justify-center gap-1 w-full transition-colors">
                                        <i data-feather="trending-down" class="w-3 h-3"></i> Susutkan
                                    </button>
                                </form>
                            @else
                                <span class="bg-slate-100 text-slate-500 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center justify-center gap-1 w-full">
                                    <i data-feather="check-circle" class="w-3 h-3"></i> Lunas
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="p-8 text-center text-slate-500">Belum ada aset tetap yang didaftarkan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Beli Aset -->
    <div id="assetModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 class="text-xl font-bold text-slate-800">Daftarkan Aset Tetap Baru</h3>
                <button onclick="document.getElementById('assetModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">
                    <i data-feather="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <form action="{{ route('fixed-assets.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Aset / Inventaris</label>
                    <input type="text" name="name" required placeholder="Cth: MacBook Pro M3, Mobil Operasional..." class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-slate-500">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal Pembelian</label>
                        <input type="date" name="purchase_date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Umur Ekonomis (Bulan)</label>
                        <div class="relative">
                            <input type="number" name="useful_life" required min="1" placeholder="48" class="w-full pr-14 pl-4 py-2.5 border border-slate-300 rounded-lg outline-none focus:ring-2 focus:ring-slate-500">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 font-medium text-slate-400">Bln</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Harga Beli Aset</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-slate-500">Rp</span>
                        <input type="number" name="purchase_price" required min="1" class="w-full pl-10 pr-4 py-3 border-2 border-slate-300 rounded-lg font-bold text-lg outline-none focus:border-slate-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-lg border border-slate-200">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Akun Aset (Debit)</label>
                        <select name="asset_account" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-slate-500">
                            <option value="" disabled selected>-- Pilih Akun 121 --</option>
                            @foreach($assetAccounts as $a) <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option> @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Bayar Dari (Kredit)</label>
                        <select name="payment_account" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-slate-500">
                            <option value="" disabled selected>-- Pilih Kas/Bank --</option>
                            @foreach($banks as $a) <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option> @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="document.getElementById('assetModal').classList.add('hidden')" class="px-5 py-2 text-slate-600 hover:bg-slate-100 rounded-lg font-bold">Batal</button>
                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-6 py-2.5 rounded-lg font-bold flex items-center gap-2">
                        Simpan & Buat Jurnal Pembelian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection