<!-- resources/views/sales/overview.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">
    <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
        <i data-feather="bar-chart-2" class="w-7 h-7 text-indigo-600"></i> Analitik & Overview Penjualan
    </h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top 5 Produk Terlaris -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-50 font-bold text-slate-700 uppercase flex items-center gap-2">
                <i data-feather="award" class="w-4 h-4 text-amber-500"></i> Top 5 Produk Terlaris (All Time)
            </div>
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-100 border-b border-slate-200">
                    <tr><th class="p-3">Produk</th><th class="p-3 text-center">Total Unit Terjual</th><th class="p-3 text-right">Penghasilan (Omzet)</th></tr>
                </thead>
                <tbody>
                    @forelse($topProducts as $top)
                    <tr class="border-b hover:bg-slate-50 transition-colors">
                        <td class="p-3 font-semibold text-indigo-700">{{ $top->product->name ?? $top->product_sku }}</td>
                        <td class="p-3 text-center font-bold text-slate-800">{{ number_format($top->total_qty, 0, ',', '.') }}</td>
                        <td class="p-3 text-right font-medium text-slate-600">Rp {{ number_format($top->total_revenue, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="p-5 text-center text-slate-400">Belum ada data penjualan tercatat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Kartu Prediksi Mesin (Linear Regression) -->
        <div class="bg-gradient-to-br from-slate-900 to-indigo-900 rounded-xl shadow-md border border-slate-800 p-7 text-white flex flex-col justify-center relative overflow-hidden">
            <!-- Hiasan Background -->
            <i data-feather="trending-up" class="absolute -right-4 -bottom-4 w-40 h-40 text-indigo-500/20"></i>

            <div class="flex items-center gap-2 mb-5 text-indigo-300 relative z-10">
                <i data-feather="cpu" class="w-5 h-5"></i>
                <span class="font-bold tracking-widest text-xs uppercase">Sistem AI Forecasting (Linear Regression)</span>
            </div>

            @if(!empty($predictions))
                <p class="text-slate-300 text-sm mb-2 relative z-10">Prediksi pesanan di bulan depan untuk produk andalan <b>{{ $predictions['product'] }}</b>:</p>
                <h2 class="text-5xl font-extrabold text-white mb-2 relative z-10 drop-shadow-md">
                    {{ $predictions['next_month_prediction'] }} <span class="text-lg font-normal text-indigo-300">Unit</span>
                </h2>
                <p class="text-sm font-bold relative z-10 {{ strpos($predictions['trend'], 'Naik') !== false ? 'text-green-400' : (strpos($predictions['trend'], 'Turun') !== false ? 'text-red-400' : 'text-amber-400') }}">
                    Trend Penjualan: {{ $predictions['trend'] }}
                </p>

                <div class="mt-6 pt-4 border-t border-slate-700 flex gap-2 overflow-x-auto text-xs relative z-10 custom-scrollbar pb-2">
                    @foreach($predictions['history'] as $hist)
                        <div class="bg-slate-800/80 border border-slate-700 px-3 py-2 rounded-lg text-center shrink-0">
                            <div class="text-slate-400 mb-1 font-medium">{{ $hist['month'] }}</div>
                            <div class="font-bold text-white text-sm">{{ $hist['qty'] }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-slate-400 italic relative z-10">Data histori (6 bulan terakhir) belum cukup untuk mengaktifkan modul prediksi sistem.</p>
            @endif
        </div>
    </div>

    <!-- Riwayat Transaksi & Pelacakan Alur Stok (Lacak Kemana-mana) -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mt-6">
        <div class="p-4 border-b border-slate-100 bg-slate-50 font-bold text-slate-700 flex items-center gap-2">
            <i data-feather="box" class="w-4 h-4"></i> Pelacakan Histori & Alur Keluar Stok (Sistem FIFO)
        </div>
        <div class="overflow-x-auto max-h-96 custom-scrollbar">
            <table class="w-full text-left text-sm border-collapse min-w-[900px]">
                <thead class="bg-white sticky top-0 shadow-sm z-10 border-b border-slate-200">
                    <tr>
                        <th class="p-4 w-32">Tanggal</th>
                        <th class="p-4 w-32">No. Dokumen</th>
                        <th class="p-4 w-48">Pelanggan (Customer)</th>
                        <th class="p-4 font-bold text-slate-600">Alur Pengambilan Gudang (Jejak FIFO)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
                    <tr class="border-b hover:bg-slate-50 transition-colors">
                        <td class="p-4 whitespace-nowrap text-slate-500 font-medium">{{ \Carbon\Carbon::parse($sale->date)->format('d M Y') }}</td>
                        <td class="p-4 font-bold text-indigo-700">{{ $sale->reference_no ?: 'SO #'.$sale->id }}</td>
                        <td class="p-4 font-bold text-slate-800">{{ $sale->contact->name }}</td>
                        <td class="p-4 text-xs font-mono text-slate-600 leading-relaxed whitespace-pre-wrap">
                            {{ trim(preg_replace('/\[No\. SJ:.*?\]/', '', $sale->notes)) ?: 'Barang masih dalam pesanan (Belum ada pergerakan stok)' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
