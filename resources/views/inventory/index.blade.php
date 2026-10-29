<!-- resources/views/inventory/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">
    <div class="flex justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="archive" class="text-teal-600 w-7 h-7"></i> Inventori & Stock Opname
            </h1>
            <p class="text-slate-500 mt-1">Sesuaikan jumlah stok fisik dan otomatis catat selisih persediaan</p>
        </div>
        <button onclick="document.getElementById('invModal').classList.remove('hidden')" class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
            <i data-feather="plus" class="w-4 h-4"></i> Penyesuaian Stok
        </button>
    </div>

    <!-- Tabel Riwayat Penyesuaian Stok -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead class="bg-white border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600">Tanggal</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Produk</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Tipe Penyesuaian</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Selisih Qty</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Nilai Barang</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Alasan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($adjustments as $adj)
                    <tr class="border-b border-slate-50 hover:bg-slate-50">
                        <td class="p-4 text-sm">{{ \Carbon\Carbon::parse($adj->date)->format('d/m/Y') }}</td>
                        <td class="p-4 text-sm font-bold text-slate-700">[{{ $adj->product_sku }}] {{ $adj->product->name }}</td>
                        <td class="p-4 text-center">
                            @if($adj->type === 'Penambahan')
                                <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">
                                    <i data-feather="arrow-up-right" class="w-3 h-3"></i> Penambahan
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">
                                    <i data-feather="arrow-down-right" class="w-3 h-3"></i> Pengurangan
                                </span>
                            @endif
                        </td>
                        <td class="p-4 text-sm font-bold text-center">{{ $adj->quantity }} {{ $adj->product->unit }}</td>
                        <td class="p-4 text-sm font-medium text-slate-600 text-right">Rp {{ number_format($adj->total_value, 0, ',', '.') }}</td>
                        <td class="p-4 text-sm text-slate-500 italic">{{ $adj->reason }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="p-8 text-center text-slate-500">Belum ada riwayat penyesuaian stok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Penyesuaian Stok -->
    <div id="invModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 class="text-xl font-bold text-slate-800">Form Penyesuaian Stok (Stock Opname)</h3>
                <button onclick="document.getElementById('invModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">
                    <i data-feather="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <form action="{{ route('inventory.adjust') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-teal-900 mb-1">Pilih Produk</label>
                        <select name="product_sku" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="" disabled selected>-- Pilih Produk --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->sku }}">[{{ $p->sku }}] {{ $p->name }} (Stok: {{ $p->stock }} {{ $p->unit }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 bg-teal-50 p-4 rounded-lg border border-teal-100">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Tipe Penyesuaian</label>
                        <select name="type" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-teal-500 font-bold">
                            <option value="Penambahan">➕ Penambahan Stok Masuk</option>
                            <option value="Pengurangan">➖ Pengurangan Stok Keluar</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Kuantitas Selisih</label>
                        <input type="number" name="quantity" required min="1" placeholder="Berapa qty?" class="w-full border border-slate-300 rounded-lg p-2.5 text-center font-bold outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Alasan Penyesuaian</label>
                    <textarea name="reason" required rows="2" placeholder="Cth: Barang rusak, salah hitung di gudang, daluwarsa..." class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-teal-500 resize-none"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="document.getElementById('invModal').classList.add('hidden')" class="px-5 py-2 text-slate-600 hover:bg-slate-100 rounded-lg font-bold">Batal</button>
                    <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2.5 rounded-lg font-bold flex items-center gap-2">
                        Sesuaikan Stok & Jurnal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection