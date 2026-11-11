@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">
    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="pie-chart" class="text-indigo-600 w-7 h-7"></i> Laporan Umur Piutang
            </h1>
            <p class="text-slate-500 mt-1">Account Receivable (AR) Aging - Ringkasan Tagihan Pelanggan</p>
        </div>
        <button onclick="window.print()" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-colors flex items-center gap-2 no-print">
            <i data-feather="printer" class="w-4 h-4"></i> Cetak Laporan Penagihan
        </button>
    </div>

    <!-- DASBOR METRIK (KOTAK WARNA-WARNI) -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-xl shadow-sm text-center">
            <h4 class="text-emerald-800 font-bold text-[10px] sm:text-xs uppercase mb-1">Belum Jatuh Tempo</h4>
            <p class="text-lg sm:text-xl font-black text-emerald-600">Rp {{ number_format($agingData['belum_jatuh_tempo'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-amber-50 border border-amber-200 p-4 rounded-xl shadow-sm text-center">
            <h4 class="text-amber-800 font-bold text-[10px] sm:text-xs uppercase mb-1">Menunggak 1-30 Hari</h4>
            <p class="text-lg sm:text-xl font-black text-amber-600">Rp {{ number_format($agingData['1_30_hari'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-orange-50 border border-orange-200 p-4 rounded-xl shadow-sm text-center">
            <h4 class="text-orange-800 font-bold text-[10px] sm:text-xs uppercase mb-1">Menunggak 31-60 Hari</h4>
            <p class="text-lg sm:text-xl font-black text-orange-600">Rp {{ number_format($agingData['31_60_hari'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-rose-50 border border-rose-200 p-4 rounded-xl shadow-sm text-center">
            <h4 class="text-rose-800 font-bold text-[10px] sm:text-xs uppercase mb-1">Menunggak 61-90 Hari</h4>
            <p class="text-lg sm:text-xl font-black text-rose-600">Rp {{ number_format($agingData['61_90_hari'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-red-50 border border-red-200 p-4 rounded-xl shadow-sm text-center">
            <h4 class="text-red-900 font-bold text-[10px] sm:text-xs uppercase mb-1">Macet > 90 Hari</h4>
            <p class="text-lg sm:text-xl font-black text-red-700">Rp {{ number_format($agingData['lebih_90_hari'], 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- PROGRESS BAR VISUALISASI KESEHATAN PIUTANG -->
    @php
        $p1 = $totalPiutang > 0 ? ($agingData['belum_jatuh_tempo'] / $totalPiutang) * 100 : 0;
        $p2 = $totalPiutang > 0 ? ($agingData['1_30_hari'] / $totalPiutang) * 100 : 0;
        $p3 = $totalPiutang > 0 ? ($agingData['31_60_hari'] / $totalPiutang) * 100 : 0;
        $p4 = $totalPiutang > 0 ? ($agingData['61_90_hari'] / $totalPiutang) * 100 : 0;
        $p5 = $totalPiutang > 0 ? ($agingData['lebih_90_hari'] / $totalPiutang) * 100 : 0;
    @endphp
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm print:hidden">
        <div class="flex justify-between items-center mb-3">
            <h3 class="font-bold text-slate-700">Rasio Kesehatan Piutang Usaha (AR Health)</h3>
            <span class="font-black text-indigo-700 text-lg">Total: Rp {{ number_format($totalPiutang, 0, ',', '.') }}</span>
        </div>
        <div class="w-full h-8 flex rounded-full overflow-hidden border border-slate-200">
            @if($totalPiutang > 0)
                <div style="width: {{ $p1 }}%" class="bg-emerald-500 h-full flex items-center justify-center text-[10px] text-white font-bold truncate" title="Aman ({{ round($p1, 1) }}%)">
                    @if($p1 > 10) {{ round($p1) }}% @endif
                </div>
                <div style="width: {{ $p2 }}%" class="bg-amber-400 h-full flex items-center justify-center text-[10px] text-amber-900 font-bold truncate" title="Telat 1-30 Hari ({{ round($p2, 1) }}%)">
                    @if($p2 > 10) {{ round($p2) }}% @endif
                </div>
                <div style="width: {{ $p3 }}%" class="bg-orange-500 h-full flex items-center justify-center text-[10px] text-white font-bold truncate" title="Telat 31-60 Hari ({{ round($p3, 1) }}%)">
                    @if($p3 > 10) {{ round($p3) }}% @endif
                </div>
                <div style="width: {{ $p4 }}%" class="bg-rose-500 h-full flex items-center justify-center text-[10px] text-white font-bold truncate" title="Telat 61-90 Hari ({{ round($p4, 1) }}%)">
                    @if($p4 > 10) {{ round($p4) }}% @endif
                </div>
                <div style="width: {{ $p5 }}%" class="bg-red-700 h-full flex items-center justify-center text-[10px] text-white font-bold truncate" title="Macet >90 Hari ({{ round($p5, 1) }}%)">
                    @if($p5 > 10) {{ round($p5) }}% @endif
                </div>
            @else
                <div class="w-full bg-slate-100 h-full flex items-center justify-center text-xs font-bold text-slate-400">SELURUH PIUTANG TELAH LUNAS</div>
            @endif
        </div>
    </div>

    <!-- TABEL RINCIAN (DAFTAR PRIORITAS PENAGIHAN UNTUK DEBT COLLECTOR) -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden print:border-none print:shadow-none">
        <div class="p-4 border-b border-slate-200 bg-slate-50 print:hidden">
            <h3 class="font-bold text-slate-800">Daftar Tagihan Belum Lunas (Prioritas Penagihan)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse print:text-xs">
                <thead class="bg-slate-100 border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600">Pelanggan</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Telepon / Kontak</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">No. Referensi (SO)</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Jatuh Tempo</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Status & Hari Keterlambatan</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Sisa Tagihan (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($details as $row)
                    <tr class="border-b border-slate-50 hover:bg-slate-50">
                        <td class="p-4 text-sm font-bold text-slate-800">{{ $row['customer'] }}</td>
                        <td class="p-4 text-sm text-slate-600 font-medium">{{ $row['phone'] ?: 'Tidak ada nomor' }}</td>
                        <td class="p-4 text-sm font-bold text-indigo-700">{{ $row['reference_no'] }}</td>
                        <td class="p-4 text-sm text-slate-600">{{ \Carbon\Carbon::parse($row['due_date'])->format('d/m/Y') }}</td>
                        <td class="p-4 text-center">
                            @if($row['category'] == 'belum_jatuh_tempo')
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold">Aman (Tunggu)</span>
                            @elseif($row['category'] == '1_30_hari')
                                <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold">Telat {{ $row['days_overdue'] }} Hari</span>
                            @elseif($row['category'] == '31_60_hari')
                                <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-bold">Telat {{ $row['days_overdue'] }} Hari</span>
                            @elseif($row['category'] == '61_90_hari')
                                <span class="px-3 py-1 bg-rose-100 text-rose-700 rounded-full text-xs font-bold">Telat {{ $row['days_overdue'] }} Hari</span>
                            @else
                                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">MACET ({{ $row['days_overdue'] }} Hari)</span>
                            @endif
                        </td>
                        <td class="p-4 text-sm font-black text-slate-800 text-right">{{ number_format($row['total'], 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="p-10 text-center text-slate-500 font-bold">Hebat! Tidak ada piutang yang nyangkut atau belum tertagih saat ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Sembunyikan elemen sidebar/navbar saat di-print (Opsional tergantung CSS layout Anda) -->
<style>
    @media print {
        aside, header, nav { display: none !important; }
        main { padding: 0 !important; margin: 0 !important; width: 100% !important; }
    }
</style>
@endsection
