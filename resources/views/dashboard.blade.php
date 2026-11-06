{{-- <!-- resources/views/dashboard.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">
    <!-- Header Dashboard -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-3xl font-bold text-slate-800">Dashboard Keuangan</h2>
            <p class="text-slate-500 mt-1">Ringkasan bisnis PT Berlian Mandiri Perkasa (Bulan Ini)</p>
        </div>
    </div>

    <!-- Kartu Ringkasan Atas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <p class="text-sm text-slate-500 font-semibold mb-2">Total Pendapatan</p>
            <h3 class="text-2xl font-bold text-green-600">Rp 345.000.000</h3>
            <p class="text-xs text-slate-400 mt-2 flex items-center gap-1">
                <i data-feather="trending-up" class="w-3 h-3 text-green-500"></i> +12.5% dari bulan lalu
            </p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <p class="text-sm text-slate-500 font-semibold mb-2">Total Pengeluaran</p>
            <h3 class="text-2xl font-bold text-red-600">Rp 185.000.000</h3>
            <p class="text-xs text-slate-400 mt-2 flex items-center gap-1">
                <i data-feather="trending-down" class="w-3 h-3 text-red-500"></i> -2.4% dari bulan lalu
            </p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <p class="text-sm text-slate-500 font-semibold mb-2">Laba Bersih</p>
            <h3 class="text-2xl font-bold text-blue-600">Rp 160.000.000</h3>
            <p class="text-xs text-slate-400 mt-2">Periode berjalan</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 bg-gradient-to-br from-slate-900 to-slate-800 text-white">
            <p class="text-sm text-slate-300 font-semibold mb-2 flex items-center gap-2">
                <i data-feather="dollar-sign" class="w-4 h-4"></i> Saldo Kas & Bank
            </p>
            <h3 class="text-2xl font-bold">Rp 215.000.000</h3>
            <p class="text-xs text-slate-400 mt-2">Siap digunakan</p>
        </div>
    </div>

    <!-- Bagian Tengah: Grafik & Rekening -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Area Grafik Arus Kas -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex flex-col">
            <h3 class="text-lg font-bold mb-6 flex items-center gap-2 text-slate-800">
                <i data-feather="activity" class="w-5 h-5 text-blue-600"></i> Arus Kas (6 Bulan Terakhir)
            </h3>
            <!-- Canvas untuk menampung Chart.js -->
            <div class="relative w-full h-72 flex-1">
                <canvas id="cashFlowChart"></canvas>
            </div>
        </div>

        <!-- Hutang Piutang & Bank -->
        <div class="space-y-6">
            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200 flex flex-col gap-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <span class="text-slate-500 font-medium text-sm">Piutang Belum Dibayar</span>
                    <span class="font-bold text-slate-800">Rp 45.000.000</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 font-medium text-sm">Hutang Jatuh Tempo</span>
                    <span class="font-bold text-red-600">Rp 12.000.000</span>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <i data-feather="briefcase" class="w-4 h-4 text-blue-600"></i> Rekening Bank
                    </h3>
                    <button class="text-blue-600 text-sm font-medium hover:underline">Lihat Semua</button>
                </div>
                <div class="p-0">
                    <div class="p-4 border-b border-slate-50 hover:bg-slate-50 flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-slate-700 text-sm">BCA Utama</p>
                            <p class="text-xs text-slate-400 font-mono mt-0.5">5193336999</p>
                        </div>
                        <p class="font-bold text-slate-800 text-sm">Rp 155.000.000</p>
                    </div>
                    <div class="p-4 border-b border-slate-50 hover:bg-slate-50 flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-slate-700 text-sm">BRI Operasional</p>
                            <p class="text-xs text-slate-400 font-mono mt-0.5">02110200014</p>
                        </div>
                        <p class="font-bold text-slate-800 text-sm">Rp 45.000.000</p>
                    </div>
                    <div class="p-4 hover:bg-slate-50 flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-slate-700 text-sm">Kas Kecil</p>
                            <p class="text-xs text-slate-400 font-mono mt-0.5">KAS-01</p>
                        </div>
                        <p class="font-bold text-slate-800 text-sm">Rp 15.000.000</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Skrip Inisialisasi Chart.js -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('cashFlowChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul'],
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: [40000000, 30000000, 20000000, 27800000, 18900000, 23900000, 34900000],
                        backgroundColor: '#22c55e', // Warna Hijau
                        borderRadius: 4,
                        maxBarSize: 35
                    },
                    {
                        label: 'Pengeluaran',
                        data: [24000000, 13980000, 48000000, 39080000, 48000000, 38000000, 43000000],
                        backgroundColor: '#ef4444', // Warna Merah
                        borderRadius: 4,
                        maxBarSize: 35
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 12,
                            font: { family: 'sans-serif', size: 12 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let value = context.raw || 0;
                                return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000000) + 'M';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection --}}

{{-- <!-- resources/views/dashboard.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">

    <!-- ALERT JATUH TEMPO H-7 -->
    @if($totalAlerts > 0)
    <div class="bg-red-50 border border-red-200 p-5 rounded-xl shadow-sm flex flex-col md:flex-row items-start md:items-center gap-4">
        <div class="p-3 bg-red-100 rounded-full text-red-600 shrink-0">
            <i data-feather="alert-triangle" class="w-6 h-6"></i>
        </div>
        <div class="flex-1">
            <h4 class="font-bold text-red-800 text-lg">Perhatian! Terdapat {{ $totalAlerts }} Tagihan Mendekati Jatuh Tempo</h4>
            <p class="text-sm text-red-700 mt-1 font-medium">Batas waktu pelunasan kurang dari 7 hari. Segera periksa daftar selengkapnya melalui <b>Ikon Lonceng Notifikasi</b> di sudut kanan atas layar Anda.</p>
        </div>
    </div>
    @endif

    <!-- Header Dashboard -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-3xl font-bold text-slate-800">Dashboard Keuangan</h2>
            <p class="text-slate-500 mt-1">Ringkasan bisnis PT Berlian Mandiri Perkasa (Bulan {{ \Carbon\Carbon::now()->translatedFormat('F Y') }})</p>
        </div>
    </div>

    <!-- Kartu Ringkasan Atas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <p class="text-sm text-slate-500 font-semibold mb-2">Total Pendapatan</p>
            <h3 class="text-2xl font-bold text-green-600">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</h3>
            <p class="text-xs text-slate-400 mt-2 flex items-center gap-1">Penjualan bulan ini</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <p class="text-sm text-slate-500 font-semibold mb-2">Total Pengeluaran</p>
            <h3 class="text-2xl font-bold text-red-600">Rp {{ number_format($pengeluaranBulanIni, 0, ',', '.') }}</h3>
            <p class="text-xs text-slate-400 mt-2 flex items-center gap-1">Pembelian & Biaya operasional</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <p class="text-sm text-slate-500 font-semibold mb-2">Laba Bersih Estimasi</p>
            <h3 class="text-2xl font-bold {{ $labaBersih >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                Rp {{ number_format($labaBersih, 0, ',', '.') }}
            </h3>
            <p class="text-xs text-slate-400 mt-2">Periode berjalan</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 bg-gradient-to-br from-slate-900 to-slate-800 text-white">
            <p class="text-sm text-slate-300 font-semibold mb-2 flex items-center gap-2">
                <i data-feather="dollar-sign" class="w-4 h-4"></i> Saldo Kas & Bank
            </p>
            <h3 class="text-2xl font-bold">Rp {{ number_format($totalKasBank, 0, ',', '.') }}</h3>
            <p class="text-xs text-slate-400 mt-2">Total aset likuid aktif</p>
        </div>
    </div>

    <!-- Bagian Tengah: Grafik & Rekening -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Area Grafik Arus Kas -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex flex-col">
            <h3 class="text-lg font-bold mb-6 flex items-center gap-2 text-slate-800">
                <i data-feather="activity" class="w-5 h-5 text-blue-600"></i> Arus Kas (6 Bulan Terakhir)
            </h3>
            <div class="relative w-full h-72 flex-1">
                <canvas id="cashFlowChart"></canvas>
            </div>
        </div>

        <!-- Hutang Piutang & Bank -->
        <div class="space-y-6">

            <!-- RINCIAN PIUTANG PELANGGAN -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col max-h-72">
                <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-blue-50/50">
                    <span class="text-blue-800 font-bold text-sm flex items-center gap-2"><i data-feather="file-text" class="w-4 h-4"></i> Piutang Pelanggan</span>
                    <span class="font-bold text-blue-700">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</span>
                </div>
                <div class="overflow-y-auto custom-scrollbar p-0 bg-white flex-1">
                    @forelse($piutangList as $piutang)
                        <div class="p-3 border-b border-slate-50 hover:bg-slate-50 flex justify-between items-center px-4">
                            <div class="overflow-hidden pr-2">
                                <p class="font-semibold text-slate-700 text-xs truncate">{{ $piutang->contact->name }}</p>
                                <p class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $piutang->reference_no }}</p>
                            </div>
                            <p class="font-bold text-slate-800 text-xs shrink-0">Rp {{ number_format($piutang->total, 0, ',', '.') }}</p>
                        </div>
                    @empty
                        <div class="p-4 text-center text-xs text-slate-400 font-medium">Bagus! Tidak ada piutang pelanggan.</div>
                    @endforelse
                </div>
            </div>

            <!-- RINCIAN HUTANG SUPPLIER -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col max-h-72">
                <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-red-50/50">
                    <span class="text-red-800 font-bold text-sm flex items-center gap-2"><i data-feather="alert-circle" class="w-4 h-4"></i> Hutang Pemasok</span>
                    <span class="font-bold text-red-600">Rp {{ number_format($totalHutang, 0, ',', '.') }}</span>
                </div>
                <div class="overflow-y-auto custom-scrollbar p-0 bg-white flex-1">
                    @forelse($hutangList as $hutang)
                        <div class="p-3 border-b border-slate-50 hover:bg-slate-50 flex justify-between items-center px-4">
                            <div class="overflow-hidden pr-2">
                                <p class="font-semibold text-slate-700 text-xs truncate">{{ $hutang->contact->name }}</p>
                                <p class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $hutang->reference_no }}</p>
                            </div>
                            <p class="font-bold text-slate-800 text-xs shrink-0">Rp {{ number_format($hutang->total, 0, ',', '.') }}</p>
                        </div>
                    @empty
                        <div class="p-4 text-center text-xs text-slate-400 font-medium">Luar biasa! Tidak ada hutang ke pemasok.</div>
                    @endforelse
                </div>
            </div>

            <!-- SALDO MASING-MASING BANK -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2 text-sm">
                        <i data-feather="briefcase" class="w-4 h-4 text-slate-600"></i> Rekening Bank Aktif
                    </h3>
                </div>
                <div class="p-0 overflow-y-auto max-h-48 custom-scrollbar">
                    @forelse($rekeningDetails as $rek)
                        <div class="p-3 px-4 border-b border-slate-50 hover:bg-slate-50 flex justify-between items-center">
                            <div>
                                <p class="font-semibold text-slate-700 text-xs">{{ $rek['name'] }}</p>
                                <p class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $rek['code'] }}</p>
                            </div>
                            <p class="font-bold text-slate-800 text-xs">Rp {{ number_format($rek['balance'], 0, ',', '.') }}</p>
                        </div>
                    @empty
                        <div class="p-4 text-center text-xs text-slate-400">Belum ada saldo bank.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Skrip Inisialisasi Chart.js Dinamis -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('cashFlowChart').getContext('2d');

        // Data dari Controller
        const labels = @json($chartLabels);
        const dataInflows = @json($chartInflows);
        const dataOutflows = @json($chartOutflows);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Uang Masuk (Kas)',
                        data: dataInflows,
                        backgroundColor: '#22c55e', // Warna Hijau
                        borderRadius: 4,
                        maxBarSize: 35
                    },
                    {
                        label: 'Uang Keluar (Kas)',
                        data: dataOutflows,
                        backgroundColor: '#ef4444', // Warna Merah
                        borderRadius: 4,
                        maxBarSize: 35
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { boxWidth: 12, font: { family: 'sans-serif', size: 12 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let value = context.raw || 0;
                                return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return 'Rp ' + (value / 1000000) + 'M';
                                }
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection --}}

<!-- resources/views/dashboard.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">

    <!-- ALERT JATUH TEMPO H-7 -->
    @if($totalAlerts > 0)
    <div class="bg-red-50 border border-red-200 p-5 rounded-xl shadow-sm flex flex-col md:flex-row items-start md:items-center gap-4">
        <div class="p-3 bg-red-100 rounded-full text-red-600 shrink-0">
            <i data-feather="alert-triangle" class="w-6 h-6"></i>
        </div>
        <div class="flex-1">
            <h4 class="font-bold text-red-800 text-lg">Perhatian! Terdapat {{ $totalAlerts }} Tagihan Mendekati Jatuh Tempo</h4>
            <p class="text-sm text-red-700 mt-1 font-medium">Batas waktu pelunasan kurang dari 7 hari. Segera periksa daftar selengkapnya melalui <b>Ikon Lonceng Notifikasi</b> di sudut kanan atas layar Anda.</p>
        </div>
    </div>
    @endif

    <!-- Header Dashboard -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-3xl font-bold text-slate-800">Dashboard Keuangan</h2>
            <p class="text-slate-500 mt-1">Ringkasan bisnis PT Berlian Mandiri Perkasa (Bulan {{ \Carbon\Carbon::now()->translatedFormat('F Y') }})</p>
        </div>
    </div>

    <!-- Kartu Ringkasan Atas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <p class="text-sm text-slate-500 font-semibold mb-2">Total Pendapatan</p>
            <h3 class="text-2xl font-bold text-green-600">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</h3>
            <p class="text-xs text-slate-400 mt-2 flex items-center gap-1">Penjualan bulan ini</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <p class="text-sm text-slate-500 font-semibold mb-2">Total Pengeluaran</p>
            <h3 class="text-2xl font-bold text-red-600">Rp {{ number_format($pengeluaranBulanIni, 0, ',', '.') }}</h3>
            <p class="text-xs text-slate-400 mt-2 flex items-center gap-1">Pembelian & Biaya operasional</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <p class="text-sm text-slate-500 font-semibold mb-2">Laba Bersih Estimasi</p>
            <h3 class="text-2xl font-bold {{ $labaBersih >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                Rp {{ number_format($labaBersih, 0, ',', '.') }}
            </h3>
            <p class="text-xs text-slate-400 mt-2">Periode berjalan</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 bg-gradient-to-br from-slate-900 to-slate-800 text-white">
            <p class="text-sm text-slate-300 font-semibold mb-2 flex items-center gap-2">
                <i data-feather="dollar-sign" class="w-4 h-4"></i> Saldo Kas & Bank
            </p>
            <h3 class="text-2xl font-bold">Rp {{ number_format($totalKasBank, 0, ',', '.') }}</h3>
            <p class="text-xs text-slate-400 mt-2">Total aset likuid aktif</p>
        </div>
    </div>

    <!-- KARTU RINGKASAN OPERASIONAL (DATA MASTER & TRANSAKSI) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <!-- Produk -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-lg shrink-0">
                <i data-feather="package" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-bold uppercase mb-0.5">Total Master Produk</p>
                <h4 class="text-lg font-bold text-slate-800 leading-none">
                    {{ $totalProduk }} <span class="text-[11px] font-medium text-slate-400 ml-1">dalam {{ $totalKategori }} kategori</span>
                </h4>
            </div>
        </div>

        <!-- Pelanggan -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-lg shrink-0">
                <i data-feather="users" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-bold uppercase mb-0.5">Pelanggan (Customer)</p>
                <h4 class="text-lg font-bold text-slate-800 leading-none">
                    {{ $totalPelanggan }} <span class="text-[11px] font-medium text-slate-400 ml-1">orang/badan</span>
                </h4>
            </div>
        </div>

        <!-- Supplier -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="p-3 bg-orange-50 text-orange-600 rounded-lg shrink-0">
                <i data-feather="truck" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-bold uppercase mb-0.5">Pemasok (Supplier)</p>
                <h4 class="text-lg font-bold text-slate-800 leading-none">
                    {{ $totalPemasok }} <span class="text-[11px] font-medium text-slate-400 ml-1">orang/badan</span>
                </h4>
            </div>
        </div>

        <!-- Total Transaksi -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex flex-col justify-center hover:shadow-md transition-shadow">
            <p class="text-[11px] text-slate-500 font-bold uppercase mb-1.5 flex items-center gap-1">
                <i data-feather="file-text" class="w-3 h-3"></i> Total Dokumen Transaksi
            </p>
            <div class="flex justify-between items-center text-[13px] font-bold text-slate-700 bg-slate-50 px-2 py-1 rounded">
                <span title="Sales Order (Penjualan)">SO: <span class="text-indigo-600">{{ $totalSO }}</span></span>
                <span class="text-slate-300">|</span>
                <span title="Purchase Order (Pembelian)">PO: <span class="text-orange-600">{{ $totalPO }}</span></span>
                <span class="text-slate-300">|</span>
                <span title="Kas Bon Dana (Biaya)">KBD: <span class="text-emerald-600">{{ $totalExpense }}</span></span>
            </div>
        </div>
    </div>

    <!-- Bagian Tengah: Grafik & Rekening -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Area Grafik Arus Kas -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex flex-col">
            <h3 class="text-lg font-bold mb-6 flex items-center gap-2 text-slate-800">
                <i data-feather="activity" class="w-5 h-5 text-blue-600"></i> Arus Kas (6 Bulan Terakhir)
            </h3>
            <div class="relative w-full h-72 flex-1">
                <canvas id="cashFlowChart"></canvas>
            </div>
        </div>

        <!-- Hutang Piutang & Bank -->
        <div class="space-y-6">

            <!-- RINCIAN PIUTANG PELANGGAN -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col max-h-72">
                <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-blue-50/50">
                    <span class="text-blue-800 font-bold text-sm flex items-center gap-2"><i data-feather="file-text" class="w-4 h-4"></i> Piutang Pelanggan</span>
                    <span class="font-bold text-blue-700">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</span>
                </div>
                <div class="overflow-y-auto custom-scrollbar p-0 bg-white flex-1">
                    @forelse($piutangList as $piutang)
                        <div class="p-3 border-b border-slate-50 hover:bg-slate-50 flex justify-between items-center px-4">
                            <div class="overflow-hidden pr-2">
                                <p class="font-semibold text-slate-700 text-xs truncate">{{ $piutang->contact->name }}</p>
                                <!-- Penambahan Tanggal Jatuh Tempo Piutang -->
                                <p class="text-[10px] text-slate-400 font-mono mt-0.5">
                                    {{ $piutang->reference_no }}
                                    <span class="text-slate-300 mx-1">|</span>
                                    <span class="text-red-500 font-semibold">Tempo: {{ \Carbon\Carbon::parse($piutang->due_date)->translatedFormat('d M Y') }}</span>
                                </p>
                            </div>
                            <p class="font-bold text-slate-800 text-xs shrink-0">Rp {{ number_format($piutang->total, 0, ',', '.') }}</p>
                        </div>
                    @empty
                        <div class="p-4 text-center text-xs text-slate-400 font-medium">Bagus! Tidak ada piutang pelanggan.</div>
                    @endforelse
                </div>
            </div>

            <!-- RINCIAN HUTANG SUPPLIER -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col max-h-72">
                <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-red-50/50">
                    <span class="text-red-800 font-bold text-sm flex items-center gap-2"><i data-feather="alert-circle" class="w-4 h-4"></i> Hutang Pemasok</span>
                    <span class="font-bold text-red-600">Rp {{ number_format($totalHutang, 0, ',', '.') }}</span>
                </div>
                <div class="overflow-y-auto custom-scrollbar p-0 bg-white flex-1">
                    @forelse($hutangList as $hutang)
                        <div class="p-3 border-b border-slate-50 hover:bg-slate-50 flex justify-between items-center px-4">
                            <div class="overflow-hidden pr-2">
                                <p class="font-semibold text-slate-700 text-xs truncate">{{ $hutang->contact->name }}</p>
                                <!-- Penambahan Tanggal Jatuh Tempo Hutang -->
                                <p class="text-[10px] text-slate-400 font-mono mt-0.5">
                                    {{ $hutang->reference_no }}
                                    <span class="text-slate-300 mx-1">|</span>
                                    <span class="text-red-500 font-semibold">Tempo: {{ \Carbon\Carbon::parse($hutang->due_date)->translatedFormat('d M Y') }}</span>
                                </p>
                            </div>
                            <p class="font-bold text-slate-800 text-xs shrink-0">Rp {{ number_format($hutang->total, 0, ',', '.') }}</p>
                        </div>
                    @empty
                        <div class="p-4 text-center text-xs text-slate-400 font-medium">Luar biasa! Tidak ada hutang ke pemasok.</div>
                    @endforelse
                </div>
            </div>

            <!-- SALDO MASING-MASING BANK -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2 text-sm">
                        <i data-feather="briefcase" class="w-4 h-4 text-slate-600"></i> Rekening Bank Aktif
                    </h3>
                </div>
                <div class="p-0 overflow-y-auto max-h-48 custom-scrollbar">
                    @forelse($rekeningDetails as $rek)
                        <div class="p-3 px-4 border-b border-slate-50 hover:bg-slate-50 flex justify-between items-center">
                            <div>
                                <p class="font-semibold text-slate-700 text-xs">{{ $rek['name'] }}</p>
                                <p class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $rek['code'] }}</p>
                            </div>
                            <p class="font-bold text-slate-800 text-xs">Rp {{ number_format($rek['balance'], 0, ',', '.') }}</p>
                        </div>
                    @empty
                        <div class="p-4 text-center text-xs text-slate-400">Belum ada saldo bank.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    <!-- BAGIAN BAWAH: ANALITIK PROPORSI (DIAGRAM LINGKARAN) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Pie Chart: Struktur Pengeluaran -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex flex-col hover:shadow-md transition-shadow">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2 text-slate-800">
                <i data-feather="pie-chart" class="w-5 h-5 text-rose-600"></i> Struktur Pengeluaran (Bulan Ini)
            </h3>
            <div class="relative w-full h-64 flex-1 flex justify-center">
                <canvas id="expensePieChart"></canvas>
            </div>
            <p class="text-center text-xs text-slate-400 mt-4">Perbandingan belanja stok barang vs biaya operasional</p>
        </div>

        <!-- Pie Chart: Distribusi Saldo Bank -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex flex-col hover:shadow-md transition-shadow">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2 text-slate-800">
                <i data-feather="pie-chart" class="w-5 h-5 text-teal-600"></i> Distribusi Saldo Kas & Bank
            </h3>
            <div class="relative w-full h-64 flex-1 flex justify-center">
                <canvas id="bankPieChart"></canvas>
            </div>
            <p class="text-center text-xs text-slate-400 mt-4">Persentase sebaran likuiditas perusahaan di berbagai rekening</p>
        </div>
    </div>
</div>

<!-- Skrip Inisialisasi Chart.js Dinamis -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // ==========================================
        // 1. GRAFIK BATANG: ARUS KAS (CASH FLOW)
        // ==========================================
        const ctxFlow = document.getElementById('cashFlowChart').getContext('2d');
        const labels = @json($chartLabels);
        const dataInflows = @json($chartInflows);
        const dataOutflows = @json($chartOutflows);

        new Chart(ctxFlow, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    { label: 'Uang Masuk (Kas)', data: dataInflows, backgroundColor: '#22c55e', borderRadius: 4, maxBarSize: 35 },
                    { label: 'Uang Keluar (Kas)', data: dataOutflows, backgroundColor: '#ef4444', borderRadius: 4, maxBarSize: 35 }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { boxWidth: 12, font: { family: 'sans-serif', size: 12 } } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let value = context.raw || 0;
                                return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) return 'Rp ' + (value / 1000000) + 'M';
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                }
            }
        });

        // ==========================================
        // 2. GRAFIK DOUGHNUT: STRUKTUR PENGELUARAN
        // ==========================================
        const pmbBulanIni = {{ $pembelianBulanIni ?? 0 }};
        const byaBulanIni = {{ $biayaBulanIni ?? 0 }};

        const ctxExpense = document.getElementById('expensePieChart').getContext('2d');
        new Chart(ctxExpense, {
            type: 'doughnut',
            data: {
                labels: ['Pembelian Brg (PO)', 'Biaya Operasional (KBD)'],
                datasets: [{
                    data: [pmbBulanIni, byaBulanIni],
                    backgroundColor: ['#f97316', '#e11d48'], // Orange untuk Beli, Merah untuk Biaya
                    borderWidth: 2,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '65%',
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 20, font: { family: 'sans-serif', size: 11 } } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let value = context.raw || 0;
                                return ' ' + context.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                }
            }
        });

        // ==========================================
        // 3. GRAFIK DOUGHNUT: DISTRIBUSI SALDO BANK
        // ==========================================
        const bankDetails = @json($rekeningDetails);
        const bankLabels = bankDetails.map(b => b.name);
        const bankData = bankDetails.map(b => b.balance);

        // Warna dinamis jika banknya banyak
        const bankColors = ['#0ea5e9', '#10b981', '#8b5cf6', '#f59e0b', '#ec4899', '#64748b'];

        const ctxBank = document.getElementById('bankPieChart').getContext('2d');
        new Chart(ctxBank, {
            type: 'doughnut',
            data: {
                labels: bankLabels.length > 0 ? bankLabels : ['Belum ada saldo'],
                datasets: [{
                    data: bankData.length > 0 ? bankData : [1],
                    backgroundColor: bankData.length > 0 ? bankColors : ['#e2e8f0'],
                    borderWidth: 2,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '65%',
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 20, font: { family: 'sans-serif', size: 11 } } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (bankData.length === 0) return ' Belum ada data';
                                let value = context.raw || 0;
                                return ' ' + context.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
