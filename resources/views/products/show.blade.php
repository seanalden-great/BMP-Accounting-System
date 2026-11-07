@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">
    <!-- HEADER KEMBALI -->
    <div class="flex items-center gap-4 border-b border-slate-200 pb-4">
        <a href="{{ route('products.index') }}" class="text-slate-400 hover:text-blue-600 bg-white p-2 rounded-lg shadow-sm border border-slate-200 transition-colors">
            <i data-feather="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="box" class="text-blue-600 w-6 h-6"></i> Detail Kartu Stok: {{ $product->name }}
            </h1>
            <p class="text-slate-500 text-sm mt-0.5">SKU: <span class="font-mono font-bold">{{ $product->sku }}</span> | Kategori: {{ $product->category }}</p>
        </div>
    </div>

    <!-- KARTU RINGKASAN METRIK -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
            <p class="text-xs text-slate-500 font-bold uppercase mb-1">Total Stok Beredar</p>
            <h3 class="text-2xl font-black {{ ($product->total_stock ?? 0) <= 0 ? 'text-red-500' : 'text-emerald-600' }}">
                {{ $product->total_stock ?? 0 }} <span class="text-sm font-medium text-slate-400">{{ $product->unit }}</span>
            </h3>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200 border-l-4 border-l-orange-500">
            <p class="text-xs text-slate-500 font-bold uppercase mb-1">HPP (Moving Average)</p>
            <h3 class="text-xl font-bold text-orange-600">Rp {{ number_format($product->hpp, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
            <p class="text-xs text-slate-500 font-bold uppercase mb-1">Harga Jual Acuan</p>
            <h3 class="text-xl font-bold text-slate-700">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
            <p class="text-xs text-slate-500 font-bold uppercase mb-1">Harga Beli Akhir</p>
            <h3 class="text-xl font-bold text-slate-700">Rp {{ number_format($product->buy_price, 0, ',', '.') }}</h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- PANEL KIRI: LOKASI GUDANG -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-4 border-b border-slate-100 bg-slate-50 font-bold text-slate-700 flex items-center gap-2 text-sm uppercase tracking-wide">
                    <i data-feather="map-pin" class="w-4 h-4"></i> Peta Lokasi Gudang
                </div>
                <div class="p-0">
                    @forelse($stocks as $st)
                    <div class="flex justify-between items-center p-4 border-b border-slate-50 hover:bg-slate-50">
                        <span class="font-bold text-slate-700 text-sm">{{ $st->warehouse->name ?? 'Gudang Utama' }}</span>
                        <span class="font-bold text-emerald-700 bg-emerald-100 border border-emerald-200 px-2.5 py-1 rounded text-xs">{{ $st->quantity }} {{ $product->unit }}</span>
                    </div>
                    @empty
                    <div class="p-8 text-center text-slate-500 text-sm">Belum ada alokasi stok di gudang manapun.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- PANEL KANAN: RIWAYAT BUKU BESAR BARANG -->
        <div class="lg:col-span-2 space-y-6">

            <!-- RIWAYAT PENJUALAN (KELUAR) -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-4 border-b border-indigo-100 bg-indigo-50 font-bold text-indigo-800 flex items-center gap-2 text-sm uppercase tracking-wide">
                    <i data-feather="shopping-bag" class="w-4 h-4"></i> Histori Penjualan (Barang Keluar)
                </div>
                <div class="overflow-x-auto max-h-64 custom-scrollbar">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-white sticky top-0 shadow-sm">
                            <tr>
                                <th class="p-3 border-b text-slate-500">Tanggal</th>
                                <th class="p-3 border-b text-slate-500">Customer (Pelanggan)</th>
                                <th class="p-3 border-b text-slate-500 text-center">Qty Keluar</th>
                                <th class="p-3 border-b text-slate-500 text-right">Harga Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($saleHistory as $sh)
                            <tr class="hover:bg-slate-50 border-b border-slate-50">
                                <td class="p-3">{{ \Carbon\Carbon::parse($sh->sale->date)->format('d/m/Y') }}</td>
                                <td class="p-3 font-bold text-slate-700">{{ $sh->sale->contact->name ?? '-' }} <br><span class="text-[10px] text-slate-400 font-mono">{{ $sh->sale->reference_no ?: 'SO #'.$sh->sale->id }}</span></td>
                                <td class="p-3 text-center font-black text-indigo-600 text-lg">{{ $sh->quantity }}</td>
                                <td class="p-3 text-right font-semibold">Rp {{ number_format($sh->price, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="p-6 text-center text-slate-500">Belum ada riwayat penjualan produk ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- RIWAYAT PEMBELIAN (MASUK) -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-4 border-b border-orange-100 bg-orange-50 font-bold text-orange-800 flex items-center gap-2 text-sm uppercase tracking-wide">
                    <i data-feather="shopping-cart" class="w-4 h-4"></i> Histori Pembelian (Barang Masuk)
                </div>
                <div class="overflow-x-auto max-h-64 custom-scrollbar">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-white sticky top-0 shadow-sm">
                            <tr>
                                <th class="p-3 border-b text-slate-500">Tanggal</th>
                                <th class="p-3 border-b text-slate-500">Supplier (Pemasok)</th>
                                <th class="p-3 border-b text-slate-500 text-center">Qty Masuk</th>
                                <th class="p-3 border-b text-slate-500 text-right">Harga Beli</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchaseHistory as $ph)
                            <tr class="hover:bg-slate-50 border-b border-slate-50">
                                <td class="p-3">{{ \Carbon\Carbon::parse($ph->purchase->date)->format('d/m/Y') }}</td>
                                <td class="p-3 font-bold text-slate-700">{{ $ph->purchase->contact->name ?? '-' }} <br><span class="text-[10px] text-slate-400 font-mono">{{ $ph->purchase->reference_no ?: 'PO #'.$ph->purchase->id }}</span></td>
                                <td class="p-3 text-center font-black text-orange-600 text-lg">{{ $ph->quantity }}</td>
                                <td class="p-3 text-right font-semibold">Rp {{ number_format($ph->price, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="p-6 text-center text-slate-500">Belum ada riwayat kulakan produk ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- RIWAYAT PENYESUAIAN STOK -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-4 border-b border-teal-100 bg-teal-50 font-bold text-teal-800 flex items-center gap-2 text-sm uppercase tracking-wide">
                    <i data-feather="settings" class="w-4 h-4"></i> Histori Stock Opname (Penyesuaian)
                </div>
                <div class="overflow-x-auto max-h-64 custom-scrollbar">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-white sticky top-0 shadow-sm">
                            <tr>
                                <th class="p-3 border-b text-slate-500">Tanggal</th>
                                <th class="p-3 border-b text-slate-500">Gudang Tempat Mutasi</th>
                                <th class="p-3 border-b text-slate-500 text-center">Mutasi</th>
                                <th class="p-3 border-b text-slate-500">Catatan/Alasan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($adjustments as $adj)
                            <tr class="hover:bg-slate-50 border-b border-slate-50">
                                <td class="p-3">{{ \Carbon\Carbon::parse($adj->date)->format('d/m/Y') }}</td>
                                <td class="p-3 font-bold text-slate-700">{{ $adj->warehouse->name ?? '-' }}</td>
                                <td class="p-3 text-center font-black text-lg {{ $adj->type == 'Penambahan' ? 'text-teal-600' : 'text-red-600' }}">
                                    {{ $adj->type == 'Penambahan' ? '+' : '-' }}{{ $adj->quantity }}
                                </td>
                                <td class="p-3 text-xs text-slate-600 italic">{{ $adj->reason }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="p-6 text-center text-slate-500">Belum ada riwayat kerusakan/penyesuaian stok.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
