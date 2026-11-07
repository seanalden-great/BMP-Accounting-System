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
                    @if ($isProfitLoss)
                        <i data-feather="bar-chart-2" class="text-blue-600 w-8 h-8"></i> Laporan Laba Rugi
                    @else
                        <i data-feather="minimize-2" class="text-emerald-600 w-8 h-8"></i> Neraca Keuangan (Balance Sheet)
                    @endif
                </h1>
                <p class="text-slate-500 mt-1">PT Berlianmandiri Perkasa</p>
            </div>
        </div>

        @if ($isProfitLoss)
            <!-- ==============================
                 TAMPILAN LABA RUGI
            =============================== -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 max-w-4xl">
                <div class="space-y-6">

                    <!-- PENDAPATAN -->
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 border-b pb-2 mb-3">Pendapatan</h3>
                        @foreach ($revenues as $a)
                            <div class="flex justify-between py-1.5 text-sm hover:bg-slate-50">
                                <span class="text-slate-600">[{{ $a['code'] }}] {{ $a['name'] }}</span>
                                <span class="font-medium text-slate-800">Rp
                                    {{ number_format($a['balance'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        {{-- <div class="flex justify-between py-2 mt-2 font-bold bg-slate-50 px-3 rounded">
                        <span>Total Pendapatan</span><span class="text-blue-700">Rp {{ number_format($totalRevenues, 0, ',', '.') }}</span>
                    </div> --}}
                        <div class="flex justify-between items-center py-2 mt-2 font-bold bg-slate-50 px-3 rounded">
                            <div class="flex items-center gap-2">
                                <span>Total Pendapatan</span>
                                <button onclick="document.getElementById('modalPendapatan').classList.remove('hidden')"
                                    class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded hover:bg-blue-200 flex items-center gap-1 transition-colors shadow-sm">
                                    <i data-feather="search" class="w-3 h-3"></i> Detail
                                </button>
                            </div>
                            <span class="text-blue-700">Rp {{ number_format($totalRevenues, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- HPP -->
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 border-b pb-2 mb-3">Harga Pokok Penjualan (HPP)</h3>
                        @foreach ($cogs as $a)
                            <div class="flex justify-between py-1.5 text-sm hover:bg-slate-50">
                                <span class="text-slate-600">[{{ $a['code'] }}] {{ $a['name'] }}</span>
                                <span class="font-medium text-slate-800">Rp
                                    {{ number_format($a['balance'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        {{-- <div class="flex justify-between py-2 mt-2 font-bold bg-slate-50 px-3 rounded">
                        <span>Total HPP</span><span class="text-orange-700">Rp {{ number_format($totalCogs, 0, ',', '.') }}</span>
                    </div> --}}
                        <div class="flex justify-between items-center py-2 mt-2 font-bold bg-slate-50 px-3 rounded">
                            <div class="flex items-center gap-2">
                                <span>Total HPP</span>
                                <button onclick="document.getElementById('modalHPP').classList.remove('hidden')"
                                    class="text-xs bg-orange-100 text-orange-700 px-2 py-1 rounded hover:bg-orange-200 flex items-center gap-1 transition-colors shadow-sm">
                                    <i data-feather="search" class="w-3 h-3"></i> Detail
                                </button>
                            </div>
                            <span class="text-orange-700">Rp {{ number_format($totalCogs, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- LABA KOTOR -->
                    <div class="flex justify-between py-3 border-y-2 border-slate-800 font-bold text-lg">
                        <span>LABA KOTOR</span><span>Rp {{ number_format($grossProfit, 0, ',', '.') }}</span>
                    </div>

                    <!-- BEBAN OPERASIONAL -->
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 border-b pb-2 mb-3">Beban Operasional</h3>
                        @foreach ($expenses as $a)
                            <div class="flex justify-between py-1.5 text-sm hover:bg-slate-50">
                                <span class="text-slate-600">[{{ $a['code'] }}] {{ $a['name'] }}</span>
                                <span class="font-medium text-slate-800">Rp
                                    {{ number_format($a['balance'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between py-2 mt-2 font-bold bg-slate-50 px-3 rounded">
                            <span>Total Beban</span><span class="text-red-700">Rp
                                {{ number_format($totalExpenses, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- LABA BERSIH -->
                    <div
                        class="flex justify-between py-4 rounded-lg px-4 font-black text-xl {{ $netProfit >= 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
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
                    <h3
                        class="font-black text-xl text-slate-800 border-b-2 border-slate-800 pb-3 mb-4 flex items-center gap-2">
                        AKTIVA (Harta)
                    </h3>
                    <div class="flex-1 space-y-2 mb-6">
                        @foreach ($assets as $a)
                            <div class="flex justify-between py-1.5 text-sm hover:bg-slate-50">
                                <span class="text-slate-600">[{{ $a['code'] }}] {{ $a['name'] }}</span>
                                <span class="font-medium text-slate-800">
                                    {{ $a['balance'] < 0 ? '(Rp ' . number_format(abs($a['balance']), 0, ',', '.') . ')' : 'Rp ' . number_format($a['balance'], 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                    <div
                        class="flex justify-between py-4 border-t-2 border-slate-800 font-black text-lg bg-slate-50 px-3 rounded-b mt-auto">
                        <span>TOTAL AKTIVA</span><span class="text-emerald-700">Rp
                            {{ number_format($totalAssets, 0, ',', '.') }}</span>
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
                            @foreach ($liabilities as $a)
                                <div class="flex justify-between py-1.5 text-sm hover:bg-slate-50">
                                    <span class="text-slate-600">[{{ $a['code'] }}] {{ $a['name'] }}</span>
                                    <span class="font-medium text-slate-800">Rp
                                        {{ number_format($a['balance'], 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Modal Ekuitas -->
                        <div>
                            <h4 class="font-bold text-slate-700 mb-2 border-b pb-1">Modal Ekuitas</h4>
                            @foreach ($equities as $a)
                                <div class="flex justify-between py-1.5 text-sm hover:bg-slate-50">
                                    <span class="text-slate-600">[{{ $a['code'] }}] {{ $a['name'] }}</span>
                                    <span class="font-medium text-slate-800">Rp
                                        {{ number_format($a['balance'], 0, ',', '.') }}</span>
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
                        $isBalanced = $totalAssets === $liabilitiesAndEquity;
                    @endphp

                    <div
                        class="flex justify-between py-4 border-t-2 border-slate-800 font-black text-lg bg-slate-50 px-3 rounded-b mt-6">
                        <span>TOTAL PASIVA</span>
                        <span class="{{ $isBalanced ? 'text-emerald-700' : 'text-red-600' }}"
                            title="{{ !$isBalanced ? 'Terdapat selisih saldo yang belum seimbang!' : '' }}">
                            Rp {{ number_format($liabilitiesAndEquity, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <!-- MODAL DETAIL PENDAPATAN (REVENUE) -->
    <div id="modalPendapatan" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <i data-feather="trending-up" class="w-5 h-5 text-blue-600"></i> Rincian Transaksi Pendapatan
                </h3>
                <button onclick="document.getElementById('modalPendapatan').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-700 transition-colors"><i data-feather="x"
                        class="w-5 h-5"></i></button>
            </div>
            <div class="p-0 overflow-y-auto custom-scrollbar flex-1">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-100/80 sticky top-0 shadow-sm backdrop-blur-sm">
                        <tr>
                            <th class="p-3 border-b border-slate-200 text-center w-32">Tanggal</th>
                            <th class="p-3 border-b border-slate-200">Uraian Transaksi Lunas</th>
                            <th class="p-3 border-b border-slate-200 text-center w-28">No. Akun</th>
                            <th class="p-3 border-b border-slate-200 text-right w-36">Nominal Diakui</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($detailPendapatan as $trx)
                            <tr class="border-b border-slate-50 hover:bg-blue-50/50 transition-colors">
                                <td class="p-3 text-center">
                                    {{ \Carbon\Carbon::parse($trx->journal->date)->format('d/m/Y') }}</td>
                                <td class="p-3 font-medium text-slate-700">
                                    {{ $trx->journal->description }}
                                    @if ($trx->journal->reference_no && strpos($trx->journal->description, $trx->journal->reference_no) === false)
                                        <br><span
                                            class="text-[10px] text-slate-400 font-mono">{{ $trx->journal->reference_no }}</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center"><span
                                        class="px-2 py-1 bg-slate-100 rounded text-xs font-bold text-slate-600 border border-slate-200">{{ $trx->account_code }}</span>
                                </td>
                                <td class="p-3 text-right font-bold text-blue-700">Rp
                                    {{ number_format(abs($trx->credit - $trx->debit), 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-slate-500 font-medium">Belum ada rincian
                                    transaksi pendapatan yang tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL DETAIL HARGA POKOK PENJUALAN (HPP) -->
    <div id="modalHPP" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <i data-feather="package" class="w-5 h-5 text-orange-600"></i> Rincian Pemotongan HPP (Barang Keluar)
                </h3>
                <button onclick="document.getElementById('modalHPP').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-700 transition-colors"><i data-feather="x"
                        class="w-5 h-5"></i></button>
            </div>
            <div class="p-0 overflow-y-auto custom-scrollbar flex-1">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-100/80 sticky top-0 shadow-sm backdrop-blur-sm">
                        <tr>
                            <th class="p-3 border-b border-slate-200 text-center w-32">Tanggal</th>
                            <th class="p-3 border-b border-slate-200">Uraian Pengiriman Surat Jalan</th>
                            <th class="p-3 border-b border-slate-200 text-center w-28">No. Akun</th>
                            <th class="p-3 border-b border-slate-200 text-right w-36">Beban HPP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($detailHPP as $trx)
                            <tr class="border-b border-slate-50 hover:bg-orange-50/50 transition-colors">
                                <td class="p-3 text-center">
                                    {{ \Carbon\Carbon::parse($trx->journal->date)->format('d/m/Y') }}</td>
                                <td class="p-3 font-medium text-slate-700">
                                    {{ $trx->journal->description }}
                                    @if ($trx->journal->reference_no && strpos($trx->journal->description, $trx->journal->reference_no) === false)
                                        <br><span
                                            class="text-[10px] text-slate-400 font-mono">{{ $trx->journal->reference_no }}</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center"><span
                                        class="px-2 py-1 bg-slate-100 rounded text-xs font-bold text-slate-600 border border-slate-200">{{ $trx->account_code }}</span>
                                </td>
                                <td class="p-3 text-right font-bold text-orange-700">Rp
                                    {{ number_format(abs($trx->debit - $trx->credit), 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-slate-500 font-medium">Belum ada rincian
                                    pemotongan HPP yang tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
