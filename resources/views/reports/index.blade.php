{{-- <!-- resources/views/reports/index.blade.php -->
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
@endsection --}}

{{-- <!-- resources/views/reports/index.blade.php -->
@extends('layouts.app')

@section('content')
    @php
        // PERBAIKAN: Tangkap 3 Tipe Laporan dari URL
        $reportType = request('type', 'profit-loss');
        $isProfitLoss = $reportType === 'profit-loss';
        $isBalanceSheet = $reportType === 'balance-sheet';
        $isCashFlow = $reportType === 'cash-flow';

        $reportCollection = collect($reportData);

        $revenues = $reportCollection->where('type', 'Pendapatan');
        $cogs = $reportCollection->where('type', 'HPP');
        $expenses = $reportCollection->where('type', 'Beban');

        $assets = $reportCollection->where('type', 'Harta');
        $liabilities = $reportCollection->where('type', 'Kewajiban');
        $equities = $reportCollection->where('type', 'Modal');

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
                    @elseif ($isBalanceSheet)
                        <i data-feather="minimize-2" class="text-emerald-600 w-8 h-8"></i> Neraca Keuangan (Balance Sheet)
                    @elseif ($isCashFlow)
                        <i data-feather="activity" class="text-purple-600 w-8 h-8"></i> Laporan Arus Kas (Cash Flow)
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
                                <span class="font-medium text-slate-800">Rp {{ number_format($a['balance'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between items-center py-2 mt-2 font-bold bg-slate-50 px-3 rounded">
                            <div class="flex items-center gap-2">
                                <span>Total Pendapatan</span>
                                <button onclick="document.getElementById('modalPendapatan').classList.remove('hidden')" class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded hover:bg-blue-200 flex items-center gap-1 transition-colors shadow-sm">
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
                                <span class="font-medium text-slate-800">Rp {{ number_format($a['balance'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between items-center py-2 mt-2 font-bold bg-slate-50 px-3 rounded">
                            <div class="flex items-center gap-2">
                                <span>Total HPP</span>
                                <button onclick="document.getElementById('modalHPP').classList.remove('hidden')" class="text-xs bg-orange-100 text-orange-700 px-2 py-1 rounded hover:bg-orange-200 flex items-center gap-1 transition-colors shadow-sm">
                                    <i data-feather="search" class="w-3 h-3"></i> Detail
                                </button>
                            </div>
                            <span class="text-orange-700">Rp {{ number_format($totalCogs, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="flex justify-between py-3 border-y-2 border-slate-800 font-bold text-lg">
                        <span>LABA KOTOR</span><span>Rp {{ number_format($grossProfit, 0, ',', '.') }}</span>
                    </div>

                    <!-- BEBAN OPERASIONAL -->
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 border-b pb-2 mb-3">Beban Operasional</h3>
                        @foreach ($expenses as $a)
                            <div class="flex justify-between py-1.5 text-sm hover:bg-slate-50">
                                <span class="text-slate-600">[{{ $a['code'] }}] {{ $a['name'] }}</span>
                                <span class="font-medium text-slate-800">Rp {{ number_format($a['balance'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between py-2 mt-2 font-bold bg-slate-50 px-3 rounded">
                            <span>Total Beban</span><span class="text-red-700">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="flex justify-between py-4 rounded-lg px-4 font-black text-xl {{ $netProfit >= 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                        <span>LABA (RUGI) BERSIH</span>
                        <span>Rp {{ number_format($netProfit, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

        @elseif ($isBalanceSheet)
            <!-- ==============================
                 TAMPILAN NERACA KEUANGAN
            =============================== -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-6xl">
                <!-- KOLOM AKTIVA -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col">
                    <h3 class="font-black text-xl text-slate-800 border-b-2 border-slate-800 pb-3 mb-4 flex items-center gap-2">AKTIVA (Harta)</h3>
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
                    <div class="flex justify-between py-4 border-t-2 border-slate-800 font-black text-lg bg-slate-50 px-3 rounded-b mt-auto">
                        <span>TOTAL AKTIVA</span><span class="text-emerald-700">Rp {{ number_format($totalAssets, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- KOLOM PASIVA -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col">
                    <h3 class="font-black text-xl text-slate-800 border-b-2 border-slate-800 pb-3 mb-4">PASIVA (Kewajiban & Modal)</h3>
                    <div class="flex-1 space-y-6">
                        <div>
                            <h4 class="font-bold text-slate-700 mb-2 border-b pb-1">Kewajiban (Utang)</h4>
                            @foreach ($liabilities as $a)
                                <div class="flex justify-between py-1.5 text-sm hover:bg-slate-50">
                                    <span class="text-slate-600">[{{ $a['code'] }}] {{ $a['name'] }}</span>
                                    <span class="font-medium text-slate-800">Rp {{ number_format($a['balance'], 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-700 mb-2 border-b pb-1">Modal Ekuitas</h4>
                            @foreach ($equities as $a)
                                <div class="flex justify-between py-1.5 text-sm hover:bg-slate-50">
                                    <span class="text-slate-600">[{{ $a['code'] }}] {{ $a['name'] }}</span>
                                    <span class="font-medium text-slate-800">Rp {{ number_format($a['balance'], 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                            <div class="flex justify-between py-1.5 text-sm mt-2 font-bold text-blue-700">
                                <span>Laba Berjalan (Tahun Ini)</span>
                                <span>Rp {{ number_format($netProfit, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    @php $isBalanced = $totalAssets === $liabilitiesAndEquity; @endphp
                    <div class="flex justify-between py-4 border-t-2 border-slate-800 font-black text-lg bg-slate-50 px-3 rounded-b mt-6">
                        <span>TOTAL PASIVA</span>
                        <span class="{{ $isBalanced ? 'text-emerald-700' : 'text-red-600' }}" title="{{ !$isBalanced ? 'Terdapat selisih saldo yang belum seimbang!' : '' }}">
                            Rp {{ number_format($liabilitiesAndEquity, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

        @elseif ($isCashFlow)
            <!-- ==============================
                 TAMPILAN ARUS KAS (CASH FLOW)
            =============================== -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 max-w-4xl">
                <div class="space-y-6">
                    <!-- AKTIVITAS OPERASI -->
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 border-b pb-2 mb-3">Arus Kas dari Aktivitas Operasi</h3>
                        <div class="flex justify-between py-2 text-sm hover:bg-slate-50">
                            <span class="text-slate-600 font-medium">Penerimaan Kas dari Pelanggan & Pendapatan Lain</span>
                            <span class="font-bold text-blue-700">Rp {{ number_format($cashFlowData['operating_in'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-2 text-sm hover:bg-slate-50">
                            <span class="text-slate-600 font-medium">Pembayaran Kas ke Pemasok & Biaya Operasional</span>
                            <span class="font-bold text-red-600">(Rp {{ number_format($cashFlowData['operating_out'], 0, ',', '.') }})</span>
                        </div>
                        @php $netOperating = $cashFlowData['operating_in'] - $cashFlowData['operating_out']; @endphp
                        <div class="flex justify-between py-3 mt-2 font-bold bg-slate-50 px-3 rounded border border-slate-200">
                            <span>Kas Bersih yang Diperoleh dari Aktivitas Operasi</span>
                            <span class="{{ $netOperating >= 0 ? 'text-blue-700' : 'text-red-700' }}">{{ $netOperating < 0 ? '(Rp ' . number_format(abs($netOperating), 0, ',', '.') . ')' : 'Rp ' . number_format($netOperating, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- AKTIVITAS INVESTASI -->
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 border-b pb-2 mb-3">Arus Kas dari Aktivitas Investasi</h3>
                        <div class="flex justify-between py-2 text-sm hover:bg-slate-50">
                            <span class="text-slate-600 font-medium">Penerimaan dari Penjualan Aset Tetap</span>
                            <span class="font-bold text-blue-700">Rp {{ number_format($cashFlowData['investing_in'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-2 text-sm hover:bg-slate-50">
                            <span class="text-slate-600 font-medium">Pembayaran untuk Pembelian Aset Tetap Baru</span>
                            <span class="font-bold text-red-600">(Rp {{ number_format($cashFlowData['investing_out'], 0, ',', '.') }})</span>
                        </div>
                        @php $netInvesting = $cashFlowData['investing_in'] - $cashFlowData['investing_out']; @endphp
                        <div class="flex justify-between py-3 mt-2 font-bold bg-slate-50 px-3 rounded border border-slate-200">
                            <span>Kas Bersih yang Digunakan untuk Aktivitas Investasi</span>
                            <span class="{{ $netInvesting >= 0 ? 'text-blue-700' : 'text-red-700' }}">{{ $netInvesting < 0 ? '(Rp ' . number_format(abs($netInvesting), 0, ',', '.') . ')' : 'Rp ' . number_format($netInvesting, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- AKTIVITAS PENDANAAN -->
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 border-b pb-2 mb-3">Arus Kas dari Aktivitas Pendanaan</h3>
                        <div class="flex justify-between py-2 text-sm hover:bg-slate-50">
                            <span class="text-slate-600 font-medium">Penerimaan dari Setoran Modal / Pinjaman Bank</span>
                            <span class="font-bold text-blue-700">Rp {{ number_format($cashFlowData['financing_in'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-2 text-sm hover:bg-slate-50">
                            <span class="text-slate-600 font-medium">Pembayaran Dividen / Pelunasan Hutang Bank</span>
                            <span class="font-bold text-red-600">(Rp {{ number_format($cashFlowData['financing_out'], 0, ',', '.') }})</span>
                        </div>
                        @php $netFinancing = $cashFlowData['financing_in'] - $cashFlowData['financing_out']; @endphp
                        <div class="flex justify-between py-3 mt-2 font-bold bg-slate-50 px-3 rounded border border-slate-200">
                            <span>Kas Bersih yang Diperoleh dari Aktivitas Pendanaan</span>
                            <span class="{{ $netFinancing >= 0 ? 'text-blue-700' : 'text-red-700' }}">{{ $netFinancing < 0 ? '(Rp ' . number_format(abs($netFinancing), 0, ',', '.') . ')' : 'Rp ' . number_format($netFinancing, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- TOTAL KENAIKAN/PENURUNAN KAS -->
                    @php $netCashIncrease = $netOperating + $netInvesting + $netFinancing; @endphp
                    <div class="flex justify-between py-4 rounded-lg px-4 font-black text-xl border border-purple-200 shadow-sm {{ $netCashIncrease >= 0 ? 'bg-purple-100 text-purple-900' : 'bg-red-100 text-red-900' }}">
                        <span>KENAIKAN (PENURUNAN) KAS BERSIH</span>
                        <span>{{ $netCashIncrease < 0 ? '(Rp ' . number_format(abs($netCashIncrease), 0, ',', '.') . ')' : 'Rp ' . number_format($netCashIncrease, 0, ',', '.') }}</span>
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
                                <td colspan="4" class="p-8 text-center text-slate-500 font-medium">Belum ada rincian transaksi pendapatan yang tercatat.</td>
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
                                <td colspan="4" class="p-8 text-center text-slate-500 font-medium">Belum ada rincian pemotongan HPP yang tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection --}}

{{-- @extends('layouts.app')

@section('content')
    @php
        // PERBAIKAN: Tangkap 3 Tipe Laporan dari URL
        $reportType = request('type', 'profit-loss');
        $isProfitLoss = $reportType === 'profit-loss';
        $isBalanceSheet = $reportType === 'balance-sheet';
        $isCashFlow = $reportType === 'cash-flow';

        $reportCollection = collect($reportData);

        $revenues = $reportCollection->where('type', 'Pendapatan');
        $cogs = $reportCollection->where('type', 'HPP');
        $expenses = $reportCollection->where('type', 'Beban');

        $assets = $reportCollection->where('type', 'Harta');
        $liabilities = $reportCollection->where('type', 'Kewajiban');
        $equities = $reportCollection->where('type', 'Modal');

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
                <h1 class="text-3xl font-bold text-slate-800 dark:text-white flex items-center gap-3 transition-colors">
                    @if ($isProfitLoss)
                        <i data-feather="bar-chart-2" class="text-blue-600 dark:text-blue-400 w-8 h-8"></i> Laporan Laba Rugi
                    @elseif ($isBalanceSheet)
                        <i data-feather="minimize-2" class="text-emerald-600 dark:text-emerald-400 w-8 h-8"></i> Neraca Keuangan (Balance Sheet)
                    @elseif ($isCashFlow)
                        <i data-feather="activity" class="text-purple-600 dark:text-purple-400 w-8 h-8"></i> Laporan Arus Kas (Cash Flow)
                    @endif
                </h1>
                <p class="text-slate-500 dark:text-slate-400 mt-1 transition-colors">PT Berlianmandiri Perkasa</p>
            </div>
        </div>

        @if ($isProfitLoss)
            <!-- ==============================
                 TAMPILAN LABA RUGI
            =============================== -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 max-w-4xl transition-colors">
                <div class="space-y-6">
                    <!-- PENDAPATAN -->
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white border-b dark:border-slate-700 pb-2 mb-3 transition-colors">Pendapatan</h3>
                        @foreach ($revenues as $a)
                            <div class="flex justify-between py-1.5 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <span class="text-slate-600 dark:text-slate-300">[{{ $a['code'] }}] {{ $a['name'] }}</span>
                                <span class="font-medium text-slate-800 dark:text-white">Rp {{ number_format($a['balance'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between items-center py-2 mt-2 font-bold bg-slate-50 dark:bg-slate-700/50 px-3 rounded transition-colors">
                            <div class="flex items-center gap-2 text-slate-800 dark:text-white">
                                <span>Total Pendapatan</span>
                                <button onclick="document.getElementById('modalPendapatan').classList.remove('hidden')" class="text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 px-2 py-1 rounded hover:bg-blue-200 dark:hover:bg-blue-900/50 flex items-center gap-1 transition-colors shadow-sm">
                                    <i data-feather="search" class="w-3 h-3"></i> Detail
                                </button>
                            </div>
                            <span class="text-blue-700 dark:text-blue-400">Rp {{ number_format($totalRevenues, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- HPP -->
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white border-b dark:border-slate-700 pb-2 mb-3 transition-colors">Harga Pokok Penjualan (HPP)</h3>
                        @foreach ($cogs as $a)
                            <div class="flex justify-between py-1.5 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <span class="text-slate-600 dark:text-slate-300">[{{ $a['code'] }}] {{ $a['name'] }}</span>
                                <span class="font-medium text-slate-800 dark:text-white">Rp {{ number_format($a['balance'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between items-center py-2 mt-2 font-bold bg-slate-50 dark:bg-slate-700/50 px-3 rounded transition-colors">
                            <div class="flex items-center gap-2 text-slate-800 dark:text-white">
                                <span>Total HPP</span>
                                <button onclick="document.getElementById('modalHPP').classList.remove('hidden')" class="text-xs bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 px-2 py-1 rounded hover:bg-orange-200 dark:hover:bg-orange-900/50 flex items-center gap-1 transition-colors shadow-sm">
                                    <i data-feather="search" class="w-3 h-3"></i> Detail
                                </button>
                            </div>
                            <span class="text-orange-700 dark:text-orange-400">Rp {{ number_format($totalCogs, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="flex justify-between py-3 border-y-2 border-slate-800 dark:border-slate-500 font-bold text-lg text-slate-800 dark:text-white transition-colors">
                        <span>LABA KOTOR</span><span>Rp {{ number_format($grossProfit, 0, ',', '.') }}</span>
                    </div>

                    <!-- BEBAN OPERASIONAL -->
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white border-b dark:border-slate-700 pb-2 mb-3 transition-colors">Beban Operasional</h3>
                        @foreach ($expenses as $a)
                            <div class="flex justify-between py-1.5 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <span class="text-slate-600 dark:text-slate-300">[{{ $a['code'] }}] {{ $a['name'] }}</span>
                                <span class="font-medium text-slate-800 dark:text-white">Rp {{ number_format($a['balance'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between py-2 mt-2 font-bold bg-slate-50 dark:bg-slate-700/50 px-3 rounded transition-colors">
                            <span class="text-slate-800 dark:text-white">Total Beban</span><span class="text-red-700 dark:text-red-400">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="flex justify-between py-4 rounded-lg px-4 font-black text-xl transition-colors {{ $netProfit >= 0 ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-400' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400' }}">
                        <span>LABA (RUGI) BERSIH</span>
                        <span>Rp {{ number_format($netProfit, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

        @elseif ($isBalanceSheet)
            <!-- ==============================
                 TAMPILAN NERACA KEUANGAN
            =============================== -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-6xl">
                <!-- KOLOM AKTIVA -->
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 flex flex-col transition-colors">
                    <h3 class="font-black text-xl text-slate-800 dark:text-white border-b-2 border-slate-800 dark:border-slate-500 pb-3 mb-4 flex items-center gap-2 transition-colors">AKTIVA (Harta)</h3>
                    <div class="flex-1 space-y-2 mb-6">
                        @foreach ($assets as $a)
                            <div class="flex justify-between py-1.5 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <span class="text-slate-600 dark:text-slate-300">[{{ $a['code'] }}] {{ $a['name'] }}</span>
                                <span class="font-medium text-slate-800 dark:text-white">
                                    {{ $a['balance'] < 0 ? '(Rp ' . number_format(abs($a['balance']), 0, ',', '.') . ')' : 'Rp ' . number_format($a['balance'], 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between py-4 border-t-2 border-slate-800 dark:border-slate-500 font-black text-lg bg-slate-50 dark:bg-slate-700/50 px-3 rounded-b mt-auto text-slate-800 dark:text-white transition-colors">
                        <span>TOTAL AKTIVA</span><span class="text-emerald-700 dark:text-emerald-400">Rp {{ number_format($totalAssets, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- KOLOM PASIVA -->
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 flex flex-col transition-colors">
                    <h3 class="font-black text-xl text-slate-800 dark:text-white border-b-2 border-slate-800 dark:border-slate-500 pb-3 mb-4 transition-colors">PASIVA (Kewajiban & Modal)</h3>
                    <div class="flex-1 space-y-6">
                        <div>
                            <h4 class="font-bold text-slate-700 dark:text-slate-300 mb-2 border-b dark:border-slate-700 pb-1 transition-colors">Kewajiban (Utang)</h4>
                            @foreach ($liabilities as $a)
                                <div class="flex justify-between py-1.5 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                    <span class="text-slate-600 dark:text-slate-300">[{{ $a['code'] }}] {{ $a['name'] }}</span>
                                    <span class="font-medium text-slate-800 dark:text-white">Rp {{ number_format($a['balance'], 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-700 dark:text-slate-300 mb-2 border-b dark:border-slate-700 pb-1 transition-colors">Modal Ekuitas</h4>
                            @foreach ($equities as $a)
                                <div class="flex justify-between py-1.5 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                    <span class="text-slate-600 dark:text-slate-300">[{{ $a['code'] }}] {{ $a['name'] }}</span>
                                    <span class="font-medium text-slate-800 dark:text-white">Rp {{ number_format($a['balance'], 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                            <div class="flex justify-between py-1.5 text-sm mt-2 font-bold text-blue-700 dark:text-blue-400 transition-colors">
                                <span>Laba Berjalan (Tahun Ini)</span>
                                <span>Rp {{ number_format($netProfit, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    @php $isBalanced = $totalAssets === $liabilitiesAndEquity; @endphp
                    <div class="flex justify-between py-4 border-t-2 border-slate-800 dark:border-slate-500 font-black text-lg bg-slate-50 dark:bg-slate-700/50 px-3 rounded-b mt-6 text-slate-800 dark:text-white transition-colors">
                        <span>TOTAL PASIVA</span>
                        <span class="{{ $isBalanced ? 'text-emerald-700 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}" title="{{ !$isBalanced ? 'Terdapat selisih saldo yang belum seimbang!' : '' }}">
                            Rp {{ number_format($liabilitiesAndEquity, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

        @elseif ($isCashFlow)
            <!-- ==============================
                 TAMPILAN ARUS KAS (CASH FLOW)
            =============================== -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 max-w-4xl transition-colors">
                <div class="space-y-6">
                    <!-- AKTIVITAS OPERASI -->
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white border-b dark:border-slate-700 pb-2 mb-3 transition-colors">Arus Kas dari Aktivitas Operasi</h3>
                        <div class="flex justify-between py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <span class="text-slate-600 dark:text-slate-300 font-medium">Penerimaan Kas dari Pelanggan & Pendapatan Lain</span>
                            <span class="font-bold text-blue-700 dark:text-blue-400">Rp {{ number_format($cashFlowData['operating_in'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <span class="text-slate-600 dark:text-slate-300 font-medium">Pembayaran Kas ke Pemasok & Biaya Operasional</span>
                            <span class="font-bold text-red-600 dark:text-red-400">(Rp {{ number_format($cashFlowData['operating_out'], 0, ',', '.') }})</span>
                        </div>
                        @php $netOperating = $cashFlowData['operating_in'] - $cashFlowData['operating_out']; @endphp
                        <div class="flex justify-between py-3 mt-2 font-bold bg-slate-50 dark:bg-slate-700/50 px-3 rounded border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white transition-colors">
                            <span>Kas Bersih yang Diperoleh dari Aktivitas Operasi</span>
                            <span class="{{ $netOperating >= 0 ? 'text-blue-700 dark:text-blue-400' : 'text-red-700 dark:text-red-400' }}">{{ $netOperating < 0 ? '(Rp ' . number_format(abs($netOperating), 0, ',', '.') . ')' : 'Rp ' . number_format($netOperating, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- AKTIVITAS INVESTASI -->
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white border-b dark:border-slate-700 pb-2 mb-3 transition-colors">Arus Kas dari Aktivitas Investasi</h3>
                        <div class="flex justify-between py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <span class="text-slate-600 dark:text-slate-300 font-medium">Penerimaan dari Penjualan Aset Tetap</span>
                            <span class="font-bold text-blue-700 dark:text-blue-400">Rp {{ number_format($cashFlowData['investing_in'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <span class="text-slate-600 dark:text-slate-300 font-medium">Pembayaran untuk Pembelian Aset Tetap Baru</span>
                            <span class="font-bold text-red-600 dark:text-red-400">(Rp {{ number_format($cashFlowData['investing_out'], 0, ',', '.') }})</span>
                        </div>
                        @php $netInvesting = $cashFlowData['investing_in'] - $cashFlowData['investing_out']; @endphp
                        <div class="flex justify-between py-3 mt-2 font-bold bg-slate-50 dark:bg-slate-700/50 px-3 rounded border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white transition-colors">
                            <span>Kas Bersih yang Digunakan untuk Aktivitas Investasi</span>
                            <span class="{{ $netInvesting >= 0 ? 'text-blue-700 dark:text-blue-400' : 'text-red-700 dark:text-red-400' }}">{{ $netInvesting < 0 ? '(Rp ' . number_format(abs($netInvesting), 0, ',', '.') . ')' : 'Rp ' . number_format($netInvesting, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- AKTIVITAS PENDANAAN -->
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white border-b dark:border-slate-700 pb-2 mb-3 transition-colors">Arus Kas dari Aktivitas Pendanaan</h3>
                        <div class="flex justify-between py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <span class="text-slate-600 dark:text-slate-300 font-medium">Penerimaan dari Setoran Modal / Pinjaman Bank</span>
                            <span class="font-bold text-blue-700 dark:text-blue-400">Rp {{ number_format($cashFlowData['financing_in'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <span class="text-slate-600 dark:text-slate-300 font-medium">Pembayaran Dividen / Pelunasan Hutang Bank</span>
                            <span class="font-bold text-red-600 dark:text-red-400">(Rp {{ number_format($cashFlowData['financing_out'], 0, ',', '.') }})</span>
                        </div>
                        @php $netFinancing = $cashFlowData['financing_in'] - $cashFlowData['financing_out']; @endphp
                        <div class="flex justify-between py-3 mt-2 font-bold bg-slate-50 dark:bg-slate-700/50 px-3 rounded border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white transition-colors">
                            <span>Kas Bersih yang Diperoleh dari Aktivitas Pendanaan</span>
                            <span class="{{ $netFinancing >= 0 ? 'text-blue-700 dark:text-blue-400' : 'text-red-700 dark:text-red-400' }}">{{ $netFinancing < 0 ? '(Rp ' . number_format(abs($netFinancing), 0, ',', '.') . ')' : 'Rp ' . number_format($netFinancing, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- TOTAL KENAIKAN/PENURUNAN KAS -->
                    @php $netCashIncrease = $netOperating + $netInvesting + $netFinancing; @endphp
                    <div class="flex justify-between py-4 rounded-lg px-4 font-black text-xl border shadow-sm transition-colors {{ $netCashIncrease >= 0 ? 'bg-purple-100 dark:bg-purple-900/20 text-purple-900 dark:text-purple-400 border-purple-200 dark:border-purple-800/30' : 'bg-red-100 dark:bg-red-900/30 text-red-900 dark:text-red-400 border-red-200 dark:border-red-800/30' }}">
                        <span>KENAIKAN (PENURUNAN) KAS BERSIH</span>
                        <span>{{ $netCashIncrease < 0 ? '(Rp ' . number_format(abs($netCashIncrease), 0, ',', '.') . ')' : 'Rp ' . number_format($netCashIncrease, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- MODAL DETAIL PENDAPATAN (REVENUE) -->
    <div id="modalPendapatan" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4 backdrop-blur-sm transition-all">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col max-h-[90vh] transition-colors">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 transition-colors">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i data-feather="trending-up" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i> Rincian Transaksi Pendapatan
                </h3>
                <button onclick="document.getElementById('modalPendapatan').classList.add('hidden')"
                    class="text-slate-400 dark:text-slate-500 hover:text-slate-700 dark:hover:text-slate-200 transition-colors"><i data-feather="x"
                        class="w-5 h-5"></i></button>
            </div>
            <div class="p-0 overflow-y-auto custom-scrollbar flex-1">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-100/80 dark:bg-slate-700/80 sticky top-0 shadow-sm backdrop-blur-sm transition-colors text-slate-800 dark:text-slate-200">
                        <tr>
                            <th class="p-3 border-b border-slate-200 dark:border-slate-600 text-center w-32">Tanggal</th>
                            <th class="p-3 border-b border-slate-200 dark:border-slate-600">Uraian Transaksi Lunas</th>
                            <th class="p-3 border-b border-slate-200 dark:border-slate-600 text-center w-28">No. Akun</th>
                            <th class="p-3 border-b border-slate-200 dark:border-slate-600 text-right w-36">Nominal Diakui</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($detailPendapatan as $trx)
                            <tr class="border-b border-slate-50 dark:border-slate-700 hover:bg-blue-50/50 dark:hover:bg-blue-900/20 transition-colors">
                                <td class="p-3 text-center text-slate-800 dark:text-slate-200">
                                    {{ \Carbon\Carbon::parse($trx->journal->date)->format('d/m/Y') }}</td>
                                <td class="p-3 font-medium text-slate-700 dark:text-slate-200">
                                    {{ $trx->journal->description }}
                                    @if ($trx->journal->reference_no && strpos($trx->journal->description, $trx->journal->reference_no) === false)
                                        <br><span
                                            class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">{{ $trx->journal->reference_no }}</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center"><span
                                        class="px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded text-xs font-bold text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 transition-colors">{{ $trx->account_code }}</span>
                                </td>
                                <td class="p-3 text-right font-bold text-blue-700 dark:text-blue-400">Rp
                                    {{ number_format(abs($trx->credit - $trx->debit), 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-slate-500 dark:text-slate-400 font-medium">Belum ada rincian transaksi pendapatan yang tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL DETAIL HARGA POKOK PENJUALAN (HPP) -->
    <div id="modalHPP" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4 backdrop-blur-sm transition-all">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col max-h-[90vh] transition-colors">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 transition-colors">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i data-feather="package" class="w-5 h-5 text-orange-600 dark:text-orange-400"></i> Rincian Pemotongan HPP (Barang Keluar)
                </h3>
                <button onclick="document.getElementById('modalHPP').classList.add('hidden')"
                    class="text-slate-400 dark:text-slate-500 hover:text-slate-700 dark:hover:text-slate-200 transition-colors"><i data-feather="x"
                        class="w-5 h-5"></i></button>
            </div>
            <div class="p-0 overflow-y-auto custom-scrollbar flex-1">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-100/80 dark:bg-slate-700/80 sticky top-0 shadow-sm backdrop-blur-sm transition-colors text-slate-800 dark:text-slate-200">
                        <tr>
                            <th class="p-3 border-b border-slate-200 dark:border-slate-600 text-center w-32">Tanggal</th>
                            <th class="p-3 border-b border-slate-200 dark:border-slate-600">Uraian Pengiriman Surat Jalan</th>
                            <th class="p-3 border-b border-slate-200 dark:border-slate-600 text-center w-28">No. Akun</th>
                            <th class="p-3 border-b border-slate-200 dark:border-slate-600 text-right w-36">Beban HPP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($detailHPP as $trx)
                            <tr class="border-b border-slate-50 dark:border-slate-700 hover:bg-orange-50/50 dark:hover:bg-orange-900/20 transition-colors">
                                <td class="p-3 text-center text-slate-800 dark:text-slate-200">
                                    {{ \Carbon\Carbon::parse($trx->journal->date)->format('d/m/Y') }}</td>
                                <td class="p-3 font-medium text-slate-700 dark:text-slate-200">
                                    {{ $trx->journal->description }}
                                    @if ($trx->journal->reference_no && strpos($trx->journal->description, $trx->journal->reference_no) === false)
                                        <br><span
                                            class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">{{ $trx->journal->reference_no }}</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center"><span
                                        class="px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded text-xs font-bold text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 transition-colors">{{ $trx->account_code }}</span>
                                </td>
                                <td class="p-3 text-right font-bold text-orange-700 dark:text-orange-400">Rp
                                    {{ number_format(abs($trx->debit - $trx->credit), 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-slate-500 dark:text-slate-400 font-medium">Belum ada rincian pemotongan HPP yang tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection --}}

@extends('layouts.app')

@section('content')
    @php
        // PERBAIKAN: Tangkap 3 Tipe Laporan dari URL
        $reportType = request('type', 'profit-loss');
        $isProfitLoss = $reportType === 'profit-loss';
        $isBalanceSheet = $reportType === 'balance-sheet';
        $isCashFlow = $reportType === 'cash-flow';

        $reportCollection = collect($reportData);

        $revenues = $reportCollection->where('type', 'Pendapatan');
        $cogs = $reportCollection->where('type', 'HPP');
        $expenses = $reportCollection->where('type', 'Beban');

        $assets = $reportCollection->where('type', 'Harta');
        $liabilities = $reportCollection->where('type', 'Kewajiban');
        $equities = $reportCollection->where('type', 'Modal');

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
                <h1 class="text-3xl font-bold text-slate-800 dark:text-white flex items-center gap-3 transition-colors">
                    @if ($isProfitLoss)
                        <i data-feather="bar-chart-2" class="text-blue-600 dark:text-blue-400 w-8 h-8"></i> {{ __('Laporan Laba Rugi') }}
                    @elseif ($isBalanceSheet)
                        <i data-feather="minimize-2" class="text-emerald-600 dark:text-emerald-400 w-8 h-8"></i> {{ __('Neraca Keuangan (Balance Sheet)') }}
                    @elseif ($isCashFlow)
                        <i data-feather="activity" class="text-purple-600 dark:text-purple-400 w-8 h-8"></i> {{ __('Laporan Arus Kas (Cash Flow)') }}
                    @endif
                </h1>
                <p class="text-slate-500 dark:text-slate-400 mt-1 transition-colors">PT Berlianmandiri Perkasa</p>
            </div>
        </div>

        @if ($isProfitLoss)
            <!-- ==============================
                 TAMPILAN LABA RUGI
            =============================== -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 max-w-4xl transition-colors">
                <div class="space-y-6">
                    <!-- PENDAPATAN -->
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white border-b dark:border-slate-700 pb-2 mb-3 transition-colors">{{ __('Pendapatan') }}</h3>
                        @foreach ($revenues as $a)
                            <div class="flex justify-between py-1.5 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <span class="text-slate-600 dark:text-slate-300">[{{ $a['code'] }}] {{ $a['name'] }}</span>
                                <span class="font-medium text-slate-800 dark:text-white">Rp {{ number_format($a['balance'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between items-center py-2 mt-2 font-bold bg-slate-50 dark:bg-slate-700/50 px-3 rounded transition-colors">
                            <div class="flex items-center gap-2 text-slate-800 dark:text-white">
                                <span>{{ __('Total Pendapatan') }}</span>
                                <button onclick="document.getElementById('modalPendapatan').classList.remove('hidden')" class="text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 px-2 py-1 rounded hover:bg-blue-200 dark:hover:bg-blue-900/50 flex items-center gap-1 transition-colors shadow-sm">
                                    <i data-feather="search" class="w-3 h-3"></i> {{ __('Detail') }}
                                </button>
                            </div>
                            <span class="text-blue-700 dark:text-blue-400">Rp {{ number_format($totalRevenues, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- HPP -->
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white border-b dark:border-slate-700 pb-2 mb-3 transition-colors">{{ __('Harga Pokok Penjualan (HPP)') }}</h3>
                        @foreach ($cogs as $a)
                            <div class="flex justify-between py-1.5 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <span class="text-slate-600 dark:text-slate-300">[{{ $a['code'] }}] {{ $a['name'] }}</span>
                                <span class="font-medium text-slate-800 dark:text-white">Rp {{ number_format($a['balance'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between items-center py-2 mt-2 font-bold bg-slate-50 dark:bg-slate-700/50 px-3 rounded transition-colors">
                            <div class="flex items-center gap-2 text-slate-800 dark:text-white">
                                <span>{{ __('Total HPP') }}</span>
                                <button onclick="document.getElementById('modalHPP').classList.remove('hidden')" class="text-xs bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 px-2 py-1 rounded hover:bg-orange-200 dark:hover:bg-orange-900/50 flex items-center gap-1 transition-colors shadow-sm">
                                    <i data-feather="search" class="w-3 h-3"></i> {{ __('Detail') }}
                                </button>
                            </div>
                            <span class="text-orange-700 dark:text-orange-400">Rp {{ number_format($totalCogs, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="flex justify-between py-3 border-y-2 border-slate-800 dark:border-slate-500 font-bold text-lg text-slate-800 dark:text-white transition-colors">
                        <span>{{ __('LABA KOTOR') }}</span><span>Rp {{ number_format($grossProfit, 0, ',', '.') }}</span>
                    </div>

                    <!-- BEBAN OPERASIONAL -->
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white border-b dark:border-slate-700 pb-2 mb-3 transition-colors">{{ __('Beban Operasional') }}</h3>
                        @foreach ($expenses as $a)
                            <div class="flex justify-between py-1.5 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <span class="text-slate-600 dark:text-slate-300">[{{ $a['code'] }}] {{ $a['name'] }}</span>
                                <span class="font-medium text-slate-800 dark:text-white">Rp {{ number_format($a['balance'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between py-2 mt-2 font-bold bg-slate-50 dark:bg-slate-700/50 px-3 rounded transition-colors">
                            <span class="text-slate-800 dark:text-white">{{ __('Total Beban') }}</span><span class="text-red-700 dark:text-red-400">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="flex justify-between py-4 rounded-lg px-4 font-black text-xl transition-colors {{ $netProfit >= 0 ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-400' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400' }}">
                        <span>{{ __('LABA (RUGI) BERSIH') }}</span>
                        <span>Rp {{ number_format($netProfit, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

        @elseif ($isBalanceSheet)
            <!-- ==============================
                 TAMPILAN NERACA KEUANGAN
            =============================== -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-6xl">
                <!-- KOLOM AKTIVA -->
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 flex flex-col transition-colors">
                    <h3 class="font-black text-xl text-slate-800 dark:text-white border-b-2 border-slate-800 dark:border-slate-500 pb-3 mb-4 flex items-center gap-2 transition-colors">{{ __('AKTIVA (Harta)') }}</h3>
                    <div class="flex-1 space-y-2 mb-6">
                        @foreach ($assets as $a)
                            <div class="flex justify-between py-1.5 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <span class="text-slate-600 dark:text-slate-300">[{{ $a['code'] }}] {{ $a['name'] }}</span>
                                <span class="font-medium text-slate-800 dark:text-white">
                                    {{ $a['balance'] < 0 ? '(Rp ' . number_format(abs($a['balance']), 0, ',', '.') . ')' : 'Rp ' . number_format($a['balance'], 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between py-4 border-t-2 border-slate-800 dark:border-slate-500 font-black text-lg bg-slate-50 dark:bg-slate-700/50 px-3 rounded-b mt-auto text-slate-800 dark:text-white transition-colors">
                        <span>{{ __('TOTAL AKTIVA') }}</span><span class="text-emerald-700 dark:text-emerald-400">Rp {{ number_format($totalAssets, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- KOLOM PASIVA -->
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 flex flex-col transition-colors">
                    <h3 class="font-black text-xl text-slate-800 dark:text-white border-b-2 border-slate-800 dark:border-slate-500 pb-3 mb-4 transition-colors">{{ __('PASIVA (Kewajiban & Modal)') }}</h3>
                    <div class="flex-1 space-y-6">
                        <div>
                            <h4 class="font-bold text-slate-700 dark:text-slate-300 mb-2 border-b dark:border-slate-700 pb-1 transition-colors">{{ __('Kewajiban (Utang)') }}</h4>
                            @foreach ($liabilities as $a)
                                <div class="flex justify-between py-1.5 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                    <span class="text-slate-600 dark:text-slate-300">[{{ $a['code'] }}] {{ $a['name'] }}</span>
                                    <span class="font-medium text-slate-800 dark:text-white">Rp {{ number_format($a['balance'], 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-700 dark:text-slate-300 mb-2 border-b dark:border-slate-700 pb-1 transition-colors">{{ __('Modal Ekuitas') }}</h4>
                            @foreach ($equities as $a)
                                <div class="flex justify-between py-1.5 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                    <span class="text-slate-600 dark:text-slate-300">[{{ $a['code'] }}] {{ $a['name'] }}</span>
                                    <span class="font-medium text-slate-800 dark:text-white">Rp {{ number_format($a['balance'], 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                            <div class="flex justify-between py-1.5 text-sm mt-2 font-bold text-blue-700 dark:text-blue-400 transition-colors">
                                <span>{{ __('Laba Berjalan (Tahun Ini)') }}</span>
                                <span>Rp {{ number_format($netProfit, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    @php $isBalanced = $totalAssets === $liabilitiesAndEquity; @endphp
                    <div class="flex justify-between py-4 border-t-2 border-slate-800 dark:border-slate-500 font-black text-lg bg-slate-50 dark:bg-slate-700/50 px-3 rounded-b mt-6 text-slate-800 dark:text-white transition-colors">
                        <span>{{ __('TOTAL PASIVA') }}</span>
                        <span class="{{ $isBalanced ? 'text-emerald-700 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}" title="{{ !$isBalanced ? __('Terdapat selisih saldo yang belum seimbang!') : '' }}">
                            Rp {{ number_format($liabilitiesAndEquity, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

        @elseif ($isCashFlow)
            <!-- ==============================
                 TAMPILAN ARUS KAS (CASH FLOW)
            =============================== -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 max-w-4xl transition-colors">
                <div class="space-y-6">
                    <!-- AKTIVITAS OPERASI -->
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white border-b dark:border-slate-700 pb-2 mb-3 transition-colors">{{ __('Arus Kas dari Aktivitas Operasi') }}</h3>
                        <div class="flex justify-between py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <span class="text-slate-600 dark:text-slate-300 font-medium">{{ __('Penerimaan Kas dari Pelanggan & Pendapatan Lain') }}</span>
                            <span class="font-bold text-blue-700 dark:text-blue-400">Rp {{ number_format($cashFlowData['operating_in'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <span class="text-slate-600 dark:text-slate-300 font-medium">{{ __('Pembayaran Kas ke Pemasok & Biaya Operasional') }}</span>
                            <span class="font-bold text-red-600 dark:text-red-400">(Rp {{ number_format($cashFlowData['operating_out'], 0, ',', '.') }})</span>
                        </div>
                        @php $netOperating = $cashFlowData['operating_in'] - $cashFlowData['operating_out']; @endphp
                        <div class="flex justify-between py-3 mt-2 font-bold bg-slate-50 dark:bg-slate-700/50 px-3 rounded border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white transition-colors">
                            <span>{{ __('Kas Bersih yang Diperoleh dari Aktivitas Operasi') }}</span>
                            <span class="{{ $netOperating >= 0 ? 'text-blue-700 dark:text-blue-400' : 'text-red-700 dark:text-red-400' }}">{{ $netOperating < 0 ? '(Rp ' . number_format(abs($netOperating), 0, ',', '.') . ')' : 'Rp ' . number_format($netOperating, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- AKTIVITAS INVESTASI -->
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white border-b dark:border-slate-700 pb-2 mb-3 transition-colors">{{ __('Arus Kas dari Aktivitas Investasi') }}</h3>
                        <div class="flex justify-between py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <span class="text-slate-600 dark:text-slate-300 font-medium">{{ __('Penerimaan dari Penjualan Aset Tetap') }}</span>
                            <span class="font-bold text-blue-700 dark:text-blue-400">Rp {{ number_format($cashFlowData['investing_in'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <span class="text-slate-600 dark:text-slate-300 font-medium">{{ __('Pembayaran untuk Pembelian Aset Tetap Baru') }}</span>
                            <span class="font-bold text-red-600 dark:text-red-400">(Rp {{ number_format($cashFlowData['investing_out'], 0, ',', '.') }})</span>
                        </div>
                        @php $netInvesting = $cashFlowData['investing_in'] - $cashFlowData['investing_out']; @endphp
                        <div class="flex justify-between py-3 mt-2 font-bold bg-slate-50 dark:bg-slate-700/50 px-3 rounded border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white transition-colors">
                            <span>{{ __('Kas Bersih yang Digunakan untuk Aktivitas Investasi') }}</span>
                            <span class="{{ $netInvesting >= 0 ? 'text-blue-700 dark:text-blue-400' : 'text-red-700 dark:text-red-400' }}">{{ $netInvesting < 0 ? '(Rp ' . number_format(abs($netInvesting), 0, ',', '.') . ')' : 'Rp ' . number_format($netInvesting, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- AKTIVITAS PENDANAAN -->
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white border-b dark:border-slate-700 pb-2 mb-3 transition-colors">{{ __('Arus Kas dari Aktivitas Pendanaan') }}</h3>
                        <div class="flex justify-between py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <span class="text-slate-600 dark:text-slate-300 font-medium">{{ __('Penerimaan dari Setoran Modal / Pinjaman Bank') }}</span>
                            <span class="font-bold text-blue-700 dark:text-blue-400">Rp {{ number_format($cashFlowData['financing_in'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <span class="text-slate-600 dark:text-slate-300 font-medium">{{ __('Pembayaran Dividen / Pelunasan Hutang Bank') }}</span>
                            <span class="font-bold text-red-600 dark:text-red-400">(Rp {{ number_format($cashFlowData['financing_out'], 0, ',', '.') }})</span>
                        </div>
                        @php $netFinancing = $cashFlowData['financing_in'] - $cashFlowData['financing_out']; @endphp
                        <div class="flex justify-between py-3 mt-2 font-bold bg-slate-50 dark:bg-slate-700/50 px-3 rounded border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white transition-colors">
                            <span>{{ __('Kas Bersih yang Diperoleh dari Aktivitas Pendanaan') }}</span>
                            <span class="{{ $netFinancing >= 0 ? 'text-blue-700 dark:text-blue-400' : 'text-red-700 dark:text-red-400' }}">{{ $netFinancing < 0 ? '(Rp ' . number_format(abs($netFinancing), 0, ',', '.') . ')' : 'Rp ' . number_format($netFinancing, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- TOTAL KENAIKAN/PENURUNAN KAS -->
                    @php $netCashIncrease = $netOperating + $netInvesting + $netFinancing; @endphp
                    <div class="flex justify-between py-4 rounded-lg px-4 font-black text-xl border shadow-sm transition-colors {{ $netCashIncrease >= 0 ? 'bg-purple-100 dark:bg-purple-900/20 text-purple-900 dark:text-purple-400 border-purple-200 dark:border-purple-800/30' : 'bg-red-100 dark:bg-red-900/30 text-red-900 dark:text-red-400 border-red-200 dark:border-red-800/30' }}">
                        <span>{{ __('KENAIKAN (PENURUNAN) KAS BERSIH') }}</span>
                        <span>{{ $netCashIncrease < 0 ? '(Rp ' . number_format(abs($netCashIncrease), 0, ',', '.') . ')' : 'Rp ' . number_format($netCashIncrease, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- MODAL DETAIL PENDAPATAN (REVENUE) -->
    <div id="modalPendapatan" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4 backdrop-blur-sm transition-all">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col max-h-[90vh] transition-colors">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 transition-colors">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i data-feather="trending-up" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i> {{ __('Rincian Transaksi Pendapatan') }}
                </h3>
                <button onclick="document.getElementById('modalPendapatan').classList.add('hidden')"
                    class="text-slate-400 dark:text-slate-500 hover:text-slate-700 dark:hover:text-slate-200 transition-colors"><i data-feather="x"
                        class="w-5 h-5"></i></button>
            </div>
            <div class="p-0 overflow-y-auto custom-scrollbar flex-1">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-100/80 dark:bg-slate-700/80 sticky top-0 shadow-sm backdrop-blur-sm transition-colors text-slate-800 dark:text-slate-200">
                        <tr>
                            <th class="p-3 border-b border-slate-200 dark:border-slate-600 text-center w-32">{{ __('Tanggal') }}</th>
                            <th class="p-3 border-b border-slate-200 dark:border-slate-600">{{ __('Uraian Transaksi Lunas') }}</th>
                            <th class="p-3 border-b border-slate-200 dark:border-slate-600 text-center w-28">{{ __('No. Akun') }}</th>
                            <th class="p-3 border-b border-slate-200 dark:border-slate-600 text-right w-36">{{ __('Nominal Diakui') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($detailPendapatan as $trx)
                            <tr class="border-b border-slate-50 dark:border-slate-700 hover:bg-blue-50/50 dark:hover:bg-blue-900/20 transition-colors">
                                <td class="p-3 text-center text-slate-800 dark:text-slate-200">
                                    {{ \Carbon\Carbon::parse($trx->journal->date)->format('d/m/Y') }}</td>
                                <td class="p-3 font-medium text-slate-700 dark:text-slate-200">
                                    {{ $trx->journal->description }}
                                    @if ($trx->journal->reference_no && strpos($trx->journal->description, $trx->journal->reference_no) === false)
                                        <br><span
                                            class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">{{ $trx->journal->reference_no }}</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center"><span
                                        class="px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded text-xs font-bold text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 transition-colors">{{ $trx->account_code }}</span>
                                </td>
                                <td class="p-3 text-right font-bold text-blue-700 dark:text-blue-400">Rp
                                    {{ number_format(abs($trx->credit - $trx->debit), 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-slate-500 dark:text-slate-400 font-medium">{{ __('Belum ada rincian transaksi pendapatan yang tercatat.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL DETAIL HARGA POKOK PENJUALAN (HPP) -->
    <div id="modalHPP" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4 backdrop-blur-sm transition-all">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col max-h-[90vh] transition-colors">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 transition-colors">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i data-feather="package" class="w-5 h-5 text-orange-600 dark:text-orange-400"></i> {{ __('Rincian Pemotongan HPP (Barang Keluar)') }}
                </h3>
                <button onclick="document.getElementById('modalHPP').classList.add('hidden')"
                    class="text-slate-400 dark:text-slate-500 hover:text-slate-700 dark:hover:text-slate-200 transition-colors"><i data-feather="x"
                        class="w-5 h-5"></i></button>
            </div>
            <div class="p-0 overflow-y-auto custom-scrollbar flex-1">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-100/80 dark:bg-slate-700/80 sticky top-0 shadow-sm backdrop-blur-sm transition-colors text-slate-800 dark:text-slate-200">
                        <tr>
                            <th class="p-3 border-b border-slate-200 dark:border-slate-600 text-center w-32">{{ __('Tanggal') }}</th>
                            <th class="p-3 border-b border-slate-200 dark:border-slate-600">{{ __('Uraian Pengiriman Surat Jalan') }}</th>
                            <th class="p-3 border-b border-slate-200 dark:border-slate-600 text-center w-28">{{ __('No. Akun') }}</th>
                            <th class="p-3 border-b border-slate-200 dark:border-slate-600 text-right w-36">{{ __('Beban HPP') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($detailHPP as $trx)
                            <tr class="border-b border-slate-50 dark:border-slate-700 hover:bg-orange-50/50 dark:hover:bg-orange-900/20 transition-colors">
                                <td class="p-3 text-center text-slate-800 dark:text-slate-200">
                                    {{ \Carbon\Carbon::parse($trx->journal->date)->format('d/m/Y') }}</td>
                                <td class="p-3 font-medium text-slate-700 dark:text-slate-200">
                                    {{ $trx->journal->description }}
                                    @if ($trx->journal->reference_no && strpos($trx->journal->description, $trx->journal->reference_no) === false)
                                        <br><span
                                            class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">{{ $trx->journal->reference_no }}</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center"><span
                                        class="px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded text-xs font-bold text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 transition-colors">{{ $trx->account_code }}</span>
                                </td>
                                <td class="p-3 text-right font-bold text-orange-700 dark:text-orange-400">Rp
                                    {{ number_format(abs($trx->debit - $trx->credit), 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-slate-500 dark:text-slate-400 font-medium">{{ __('Belum ada rincian pemotongan HPP yang tercatat.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
