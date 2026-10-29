<!-- resources/views/reports/index.blade.php -->
@extends('layouts.app')

@section('content')
@php
    // Cek tipe laporan dari URL (Default: Laba Rugi)
    $isProfitLoss = request('type', 'profit-loss') === 'profit-loss';
    
    // Ubah array biasa menjadi Laravel Collection agar mudah di-filter
    $reportCollection = collect($reportData);

    // Filter Akun Laba Rugi
    $revenues = $reportCollection->where('type', 'Pendapatan');
    $cogs = $reportCollection->where('type', 'HPP');
    $expenses = $reportCollection->where('type', 'Beban');

    // Filter Akun Neraca
    $assets = $reportCollection->where('type', 'Harta');
    $liabilities = $reportCollection->where('type', 'Kewajiban');
    $equities = $reportCollection->where('type', 'Modal');

    // Kalkulasi Total
    $totalRevenues = $revenues->sum('balance');
    $totalCogs = $cogs->sum('balance');
    $totalExpenses = $expenses->sum('balance');
    $grossProfit = $totalRevenues - $totalCogs;

    $totalAssets = $assets->sum('balance');
    $totalLiabilities = $liabilities->sum('balance');
    $totalEquities = $equities->sum('balance');
    $liabilitiesAndEquity = $totalLiabilities + $totalEquities + $netProfit;
@endphp

<div class="space-y-6 pb-10">
    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                @if($isProfitLoss)
                    <i data-feather="bar-chart-2" class="text-blue-600 w-8 h-8"></i> Laporan Laba Rugi
                @else
                    <i data-feather="minimize-2" class="text-emerald-600 w-8 h-8"></i> Neraca Keuangan (Balance Sheet)
                @endif
            </h1>
            <p class="text-slate-500 mt-1">PT Berlianmandiri Perkasa</p>
        </div>
    </div>

    @if($isProfitLoss)
        <!-- ==============================
             TAMPILAN LABA RUGI 
        =============================== -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 max-w-4xl">
            <div class="space-y-6">
                
                <!-- PENDAPATAN -->
                <div>
                    <h3 class="font-bold text-lg text-slate-800 border-b pb-2 mb-3">Pendapatan</h3>
                    @foreach($revenues as $a)
                        <div class="flex justify-between py-1.5 text-sm hover:bg-slate-50">
                            <span class="text-slate-600">[{{ $a['code'] }}] {{ $a['name'] }}</span>
                            <span class="font-medium text-slate-800">Rp {{ number_format($a['balance'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                    <div class="flex justify-between py-2 mt-2 font-bold bg-slate-50 px-3 rounded">
                        <span>Total Pendapatan</span><span class="text-blue-700">Rp {{ number_format($totalRevenues, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- HPP -->
                <div>
                    <h3 class="font-bold text-lg text-slate-800 border-b pb-2 mb-3">Harga Pokok Penjualan (HPP)</h3>
                    @foreach($cogs as $a)
                        <div class="flex justify-between py-1.5 text-sm hover:bg-slate-50">
                            <span class="text-slate-600">[{{ $a['code'] }}] {{ $a['name'] }}</span>
                            <span class="font-medium text-slate-800">Rp {{ number_format($a['balance'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                    <div class="flex justify-between py-2 mt-2 font-bold bg-slate-50 px-3 rounded">
                        <span>Total HPP</span><span class="text-orange-700">Rp {{ number_format($totalCogs, 0, ',', '.') }}</span>
                    </div>
                </div>
                
                <!-- LABA KOTOR -->
                <div class="flex justify-between py-3 border-y-2 border-slate-800 font-bold text-lg">
                    <span>LABA KOTOR</span><span>Rp {{ number_format($grossProfit, 0, ',', '.') }}</span>
                </div>

                <!-- BEBAN OPERASIONAL -->
                <div>
                    <h3 class="font-bold text-lg text-slate-800 border-b pb-2 mb-3">Beban Operasional</h3>
                    @foreach($expenses as $a)
                        <div class="flex justify-between py-1.5 text-sm hover:bg-slate-50">
                            <span class="text-slate-600">[{{ $a['code'] }}] {{ $a['name'] }}</span>
                            <span class="font-medium text-slate-800">Rp {{ number_format($a['balance'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                    <div class="flex justify-between py-2 mt-2 font-bold bg-slate-50 px-3 rounded">
                        <span>Total Beban</span><span class="text-red-700">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- LABA BERSIH -->
                <div class="flex justify-between py-4 rounded-lg px-4 font-black text-xl {{ $netProfit >= 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                    <span>LABA (RUGI) BERSIH</span>
                    <span>Rp {{ number_format($netProfit, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

    @else
        <!-- ==============================
             TAMPILAN NERACA KEUANGAN 
        =============================== -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-6xl">
            
            <!-- KOLOM AKTIVA (HARTA) -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col">
                <h3 class="font-black text-xl text-slate-800 border-b-2 border-slate-800 pb-3 mb-4 flex items-center gap-2">
                    AKTIVA (Harta)
                </h3>
                <div class="flex-1 space-y-2 mb-6">
                    @foreach($assets as $a)
                        <div class="flex justify-between py-1.5 text-sm hover:bg-slate-50">
                            <span class="text-slate-600">[{{ $a['code'] }}] {{ $a['name'] }}</span>
                            <span class="font-medium text-slate-800">
                                {{ $a['balance'] < 0 ? '(Rp ' . number_format(abs($a['balance']), 0, ',', '.') . ')' : 'Rp ' . number_format($a['balance'], 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-between py-4 border-t-2 border-slate-800 font-black text-lg bg-slate-50 px-3 rounded-b mt-auto">
                    <span>TOTAL AKTIVA</span><span class="text-emerald-700">Rp {{ number_format($totalAssets, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- KOLOM PASIVA (KEWAJIBAN & MODAL) -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col">
                <h3 class="font-black text-xl text-slate-800 border-b-2 border-slate-800 pb-3 mb-4">
                    PASIVA (Kewajiban & Modal)
                </h3>
                
                <div class="flex-1 space-y-6">
                    <!-- Kewajiban / Utang -->
                    <div>
                        <h4 class="font-bold text-slate-700 mb-2 border-b pb-1">Kewajiban (Utang)</h4>
                        @foreach($liabilities as $a)
                            <div class="flex justify-between py-1.5 text-sm hover:bg-slate-50">
                                <span class="text-slate-600">[{{ $a['code'] }}] {{ $a['name'] }}</span>
                                <span class="font-medium text-slate-800">Rp {{ number_format($a['balance'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Modal Ekuitas -->
                    <div>
                        <h4 class="font-bold text-slate-700 mb-2 border-b pb-1">Modal Ekuitas</h4>
                        @foreach($equities as $a)
                            <div class="flex justify-between py-1.5 text-sm hover:bg-slate-50">
                                <span class="text-slate-600">[{{ $a['code'] }}] {{ $a['name'] }}</span>
                                <span class="font-medium text-slate-800">Rp {{ number_format($a['balance'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        
                        <!-- Laba Ditahan / Laba Berjalan -->
                        <div class="flex justify-between py-1.5 text-sm mt-2 font-bold text-blue-700">
                            <span>Laba Berjalan (Tahun Ini)</span>
                            <span>Rp {{ number_format($netProfit, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Pemeriksaan Keseimbangan (Balance Check) -->
                @php
                    $isBalanced = ($totalAssets === $liabilitiesAndEquity);
                @endphp
                
                <div class="flex justify-between py-4 border-t-2 border-slate-800 font-black text-lg bg-slate-50 px-3 rounded-b mt-6">
                    <span>TOTAL PASIVA</span>
                    <span class="{{ $isBalanced ? 'text-emerald-700' : 'text-red-600' }}" title="{{ !$isBalanced ? 'Terdapat selisih saldo yang belum seimbang!' : '' }}">
                        Rp {{ number_format($liabilitiesAndEquity, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection