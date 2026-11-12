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
    @if ($totalAlerts > 0)
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
        {{-- @if ($totalAlerts > 0)
    <div class="bg-red-50 border border-red-200 p-5 rounded-xl shadow-sm flex flex-col md:flex-row items-start md:items-center gap-4">
        <div class="p-3 bg-red-100 rounded-full text-red-600 shrink-0">
            <i data-feather="alert-triangle" class="w-6 h-6"></i>
        </div>
        <div class="flex-1">
            <h4 class="font-bold text-red-800 text-lg">Perhatian! Terdapat {{ $totalAlerts }} Tagihan Mendekati Jatuh Tempo</h4>
            <p class="text-sm text-red-700 mt-1 font-medium">Batas waktu pelunasan kurang dari 7 hari. Segera periksa daftar selengkapnya melalui <b>Ikon Lonceng Notifikasi</b> di sudut kanan atas layar Anda.</p>
        </div>
    </div>
    @endif --}}

        {{-- <!-- ALERT 1: SUDAH LEWAT JATUH TEMPO (OVERDUE) -->
    @if ($totalOverdue > 0)
    <div class="bg-red-50 border border-red-200 p-5 rounded-xl shadow-sm flex flex-col md:flex-row items-start md:items-center gap-4">
        <div class="p-3 bg-red-100 rounded-full text-red-600 shrink-0">
            <i data-feather="x-octagon" class="w-6 h-6"></i>
        </div>
        <div class="flex-1">
            <h4 class="font-bold text-red-800 text-lg">Peringatan Kritis! Terdapat {{ $totalOverdue }} Tagihan Lewat Jatuh Tempo</h4>
            <p class="text-sm text-red-700 mt-1 font-medium">Tagihan ini sudah menunggak. Segera periksa daftar selengkapnya pada ikon lonceng untuk segera ditindaklanjuti.</p>
        </div>
    </div>
    @endif

    <!-- ALERT 2: JATUH TEMPO HARI INI -->
    @if ($totalToday > 0)
    <div class="bg-orange-50 border border-orange-200 p-5 rounded-xl shadow-sm flex flex-col md:flex-row items-start md:items-center gap-4">
        <div class="p-3 bg-orange-100 rounded-full text-orange-600 shrink-0">
            <i data-feather="alert-circle" class="w-6 h-6"></i>
        </div>
        <div class="flex-1">
            <h4 class="font-bold text-orange-800 text-lg">Hari Ini! Terdapat {{ $totalToday }} Tagihan Jatuh Tempo</h4>
            <p class="text-sm text-orange-700 mt-1 font-medium">Harap segera selesaikan tagihan/piutang yang memiliki batas waktu hari ini.</p>
        </div>
    </div>
    @endif

    <!-- ALERT 3: MENDEKATI JATUH TEMPO (H-7) -->
    @if ($totalUpcoming > 0)
    <div class="bg-yellow-50 border border-yellow-200 p-5 rounded-xl shadow-sm flex flex-col md:flex-row items-start md:items-center gap-4">
        <div class="p-3 bg-yellow-100 rounded-full text-yellow-600 shrink-0">
            <i data-feather="clock" class="w-6 h-6"></i>
        </div>
        <div class="flex-1">
            <h4 class="font-bold text-yellow-800 text-lg">Perhatian! Terdapat {{ $totalUpcoming }} Tagihan Mendekati Jatuh Tempo</h4>
            <p class="text-sm text-yellow-700 mt-1 font-medium">Batas waktu pelunasan kurang dari 7 hari ke depan. Mohon dipersiapkan.</p>
        </div>
    </div>
    @endif --}}

        <!-- ALERT 1: SUDAH LEWAT JATUH TEMPO (OVERDUE) -->
        @if ($totalOverdue > 0)
            <div onclick="document.getElementById('modalOverdue').classList.remove('hidden')"
                class="cursor-pointer bg-red-50 border border-red-200 p-5 rounded-xl shadow-sm flex flex-col md:flex-row items-start md:items-center gap-4 hover:shadow-md hover:bg-red-100 transition-all">
                <div class="p-3 bg-red-100 rounded-full text-red-600 shrink-0">
                    <i data-feather="x-octagon" class="w-6 h-6"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-red-800 text-lg">Peringatan Kritis! Terdapat {{ $totalOverdue }} Tagihan Lewat
                        Jatuh Tempo</h4>
                    <p class="text-sm text-red-700 mt-1 font-medium">Tagihan ini sudah menunggak. <b>Klik kotak ini</b> untuk
                        melihat rincian dan menindaklanjutinya.</p>
                </div>
            </div>
        @endif

        <!-- ALERT 2: JATUH TEMPO HARI INI -->
        @if ($totalToday > 0)
            <div onclick="document.getElementById('modalToday').classList.remove('hidden')"
                class="cursor-pointer bg-orange-50 border border-orange-200 p-5 rounded-xl shadow-sm flex flex-col md:flex-row items-start md:items-center gap-4 hover:shadow-md hover:bg-orange-100 transition-all">
                <div class="p-3 bg-orange-100 rounded-full text-orange-600 shrink-0">
                    <i data-feather="alert-circle" class="w-6 h-6"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-orange-800 text-lg">Hari Ini! Terdapat {{ $totalToday }} Tagihan Jatuh Tempo
                    </h4>
                    <p class="text-sm text-orange-700 mt-1 font-medium">Harap segera selesaikan tagihan yang memiliki batas
                        waktu hari ini. <b>Klik di sini</b> untuk rincian.</p>
                </div>
            </div>
        @endif

        <!-- ALERT 3: MENDEKATI JATUH TEMPO (H-7) -->
        @if ($totalUpcoming > 0)
            <div onclick="document.getElementById('modalUpcoming').classList.remove('hidden')"
                class="cursor-pointer bg-yellow-50 border border-yellow-200 p-5 rounded-xl shadow-sm flex flex-col md:flex-row items-start md:items-center gap-4 hover:shadow-md hover:bg-yellow-100 transition-all">
                <div class="p-3 bg-yellow-100 rounded-full text-yellow-600 shrink-0">
                    <i data-feather="clock" class="w-6 h-6"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-yellow-800 text-lg">Perhatian! Terdapat {{ $totalUpcoming }} Tagihan Mendekati
                        Jatuh Tempo</h4>
                    <p class="text-sm text-yellow-700 mt-1 font-medium">Batas waktu pelunasan kurang dari 7 hari ke depan.
                        <b>Klik di sini</b> untuk bersiap.
                    </p>
                </div>
            </div>
        @endif

        <!-- Header Dashboard -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-3xl font-bold text-slate-800">Dashboard Keuangan</h2>
                <p class="text-slate-500 mt-1">Ringkasan bisnis PT Berlian Mandiri Perkasa (Bulan
                    {{ \Carbon\Carbon::now()->translatedFormat('F Y') }})</p>
            </div>
        </div>

        <!-- AKSES CEPAT (QUICK ACTIONS) -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-2">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Jalan Pintas Operasional</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <a href="{{ route('sales.index') }}" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 p-3 rounded-lg flex flex-col items-center justify-center gap-2 transition-colors border border-indigo-100 shadow-sm hover:shadow">
                    <i data-feather="shopping-bag" class="w-5 h-5"></i>
                    <span class="text-xs font-bold text-center">Buat Penjualan (SO)</span>
                </a>
                <a href="{{ route('purchases.index') }}" class="bg-orange-50 hover:bg-orange-100 text-orange-700 p-3 rounded-lg flex flex-col items-center justify-center gap-2 transition-colors border border-orange-100 shadow-sm hover:shadow">
                    <i data-feather="shopping-cart" class="w-5 h-5"></i>
                    <span class="text-xs font-bold text-center">Buat Pembelian (PO)</span>
                </a>
                <a href="{{ route('expenses.index') }}" class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 p-3 rounded-lg flex flex-col items-center justify-center gap-2 transition-colors border border-emerald-100 shadow-sm hover:shadow">
                    <i data-feather="file-text" class="w-5 h-5"></i>
                    <span class="text-xs font-bold text-center">Catat Biaya (KBD)</span>
                </a>
                <a href="{{ route('inventory.index') }}" class="bg-teal-50 hover:bg-teal-100 text-teal-700 p-3 rounded-lg flex flex-col items-center justify-center gap-2 transition-colors border border-teal-100 shadow-sm hover:shadow">
                    <i data-feather="archive" class="w-5 h-5"></i>
                    <span class="text-xs font-bold text-center">Stok & Mutasi Gudang</span>
                </a>
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
            <div
                class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 bg-gradient-to-br from-slate-900 to-slate-800 text-white">
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
            <div
                class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex items-center gap-4 hover:shadow-md transition-shadow">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-lg shrink-0">
                    <i data-feather="package" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-bold uppercase mb-0.5">Total Master Produk</p>
                    <h4 class="text-lg font-bold text-slate-800 leading-none">
                        {{ $totalProduk }} <span class="text-[11px] font-medium text-slate-400 ml-1">dalam
                            {{ $totalKategori }} kategori</span>
                    </h4>
                </div>
            </div>

            <!-- Pelanggan -->
            <div
                class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex items-center gap-4 hover:shadow-md transition-shadow">
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
            <div
                class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex items-center gap-4 hover:shadow-md transition-shadow">
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
            <div
                class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex flex-col justify-center hover:shadow-md transition-shadow">
                <p class="text-[11px] text-slate-500 font-bold uppercase mb-1.5 flex items-center gap-1">
                    <i data-feather="file-text" class="w-3 h-3"></i> Total Dokumen Transaksi
                </p>
                <div
                    class="flex justify-between items-center text-[13px] font-bold text-slate-700 bg-slate-50 px-2 py-1 rounded">
                    <span title="Sales Order (Penjualan)">SO: <span
                            class="text-indigo-600">{{ $totalSO }}</span></span>
                    <span class="text-slate-300">|</span>
                    <span title="Purchase Order (Pembelian)">PO: <span
                            class="text-orange-600">{{ $totalPO }}</span></span>
                    <span class="text-slate-300">|</span>
                    <span title="Kas Bon Dana (Biaya)">KBD: <span
                            class="text-emerald-600">{{ $totalExpense }}</span></span>
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
                        <span class="text-blue-800 font-bold text-sm flex items-center gap-2"><i data-feather="file-text"
                                class="w-4 h-4"></i> Piutang Pelanggan</span>
                        <span class="font-bold text-blue-700">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</span>
                    </div>
                    <div class="overflow-y-auto custom-scrollbar p-0 bg-white flex-1">
                        @forelse($piutangList as $piutang)
                            <div
                                class="p-3 border-b border-slate-50 hover:bg-slate-50 flex justify-between items-center px-4">
                                <div class="overflow-hidden pr-2">
                                    <p class="font-semibold text-slate-700 text-xs truncate">{{ $piutang->contact->name }}
                                    </p>
                                    <!-- Penambahan Tanggal Jatuh Tempo Piutang -->
                                    <p class="text-[10px] text-slate-400 font-mono mt-0.5">
                                        {{ $piutang->reference_no }}
                                        <span class="text-slate-300 mx-1">|</span>
                                        <span class="text-red-500 font-semibold">Tempo:
                                            {{ \Carbon\Carbon::parse($piutang->due_date)->translatedFormat('d M Y') }}</span>
                                    </p>
                                </div>
                                <p class="font-bold text-slate-800 text-xs shrink-0">Rp
                                    {{ number_format($piutang->total, 0, ',', '.') }}</p>
                            </div>
                        @empty
                            <div class="p-4 text-center text-xs text-slate-400 font-medium">Bagus! Tidak ada piutang
                                pelanggan.</div>
                        @endforelse
                    </div>
                </div>

                <!-- RINCIAN HUTANG SUPPLIER -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col max-h-72">
                    <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-red-50/50">
                        <span class="text-red-800 font-bold text-sm flex items-center gap-2"><i
                                data-feather="alert-circle" class="w-4 h-4"></i> Hutang Pemasok</span>
                        <span class="font-bold text-red-600">Rp {{ number_format($totalHutang, 0, ',', '.') }}</span>
                    </div>
                    <div class="overflow-y-auto custom-scrollbar p-0 bg-white flex-1">
                        @forelse($hutangList as $hutang)
                            <div
                                class="p-3 border-b border-slate-50 hover:bg-slate-50 flex justify-between items-center px-4">
                                <div class="overflow-hidden pr-2">
                                    <p class="font-semibold text-slate-700 text-xs truncate">{{ $hutang->contact->name }}
                                    </p>
                                    <!-- Penambahan Tanggal Jatuh Tempo Hutang -->
                                    <p class="text-[10px] text-slate-400 font-mono mt-0.5">
                                        {{ $hutang->reference_no }}
                                        <span class="text-slate-300 mx-1">|</span>
                                        <span class="text-red-500 font-semibold">Tempo:
                                            {{ \Carbon\Carbon::parse($hutang->due_date)->translatedFormat('d M Y') }}</span>
                                    </p>
                                </div>
                                <p class="font-bold text-slate-800 text-xs shrink-0">Rp
                                    {{ number_format($hutang->total, 0, ',', '.') }}</p>
                            </div>
                        @empty
                            <div class="p-4 text-center text-xs text-slate-400 font-medium">Luar biasa! Tidak ada hutang ke
                                pemasok.</div>
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
                            <div
                                class="p-3 px-4 border-b border-slate-50 hover:bg-slate-50 flex justify-between items-center">
                                <div>
                                    <p class="font-semibold text-slate-700 text-xs">{{ $rek['name'] }}</p>
                                    <p class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $rek['code'] }}</p>
                                </div>
                                <p class="font-bold text-slate-800 text-xs">Rp
                                    {{ number_format($rek['balance'], 0, ',', '.') }}</p>
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
            <div
                class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex flex-col hover:shadow-md transition-shadow">
                <h3 class="text-lg font-bold mb-4 flex items-center gap-2 text-slate-800">
                    <i data-feather="pie-chart" class="w-5 h-5 text-rose-600"></i> Struktur Pengeluaran (Bulan Ini)
                </h3>
                <div class="relative w-full h-64 flex-1 flex justify-center">
                    <canvas id="expensePieChart"></canvas>
                </div>
                <p class="text-center text-xs text-slate-400 mt-4">Perbandingan belanja stok barang vs biaya operasional
                </p>
            </div>

            <!-- Pie Chart: Distribusi Saldo Bank -->
            <div
                class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex flex-col hover:shadow-md transition-shadow">
                <h3 class="text-lg font-bold mb-4 flex items-center gap-2 text-slate-800">
                    <i data-feather="pie-chart" class="w-5 h-5 text-teal-600"></i> Distribusi Saldo Kas & Bank
                </h3>
                <div class="relative w-full h-64 flex-1 flex justify-center">
                    <canvas id="bankPieChart"></canvas>
                </div>
                <p class="text-center text-xs text-slate-400 mt-4">Persentase sebaran likuiditas perusahaan di berbagai
                    rekening</p>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- BAGIAN BAWAH 2: AKTIVITAS & PRODUK TERLARIS    -->
        <!-- ============================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-2">

            <!-- Top 5 Produk Terlaris -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2 text-sm">
                        <i data-feather="award" class="w-4 h-4 text-amber-500"></i> 5 Produk Terlaris (Bulan Ini)
                    </h3>
                </div>
                <div class="p-0 flex-1">
                    @forelse($topProducts as $idx => $tp)
                    <div class="p-3 px-4 border-b border-slate-50 hover:bg-slate-50 flex items-center gap-3">
                        <div class="w-6 h-6 rounded bg-gradient-to-br from-amber-200 to-amber-400 text-amber-900 flex items-center justify-center font-black text-xs shrink-0 shadow-sm">{{ $idx + 1 }}</div>
                        <div class="flex-1 overflow-hidden">
                            <p class="font-semibold text-slate-700 text-xs truncate">{{ $tp->product->name ?? $tp->product_sku }}</p>
                            <p class="text-[10px] text-slate-400 font-mono mt-0.5">SKU: {{ $tp->product_sku }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="font-black text-slate-800 text-sm">{{ number_format($tp->total_qty, 0, ',', '.') }}</p>
                            <p class="text-[9px] text-slate-400 uppercase font-bold">Terjual</p>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 flex flex-col items-center justify-center text-center">
                        <i data-feather="package" class="w-8 h-8 text-slate-200 mb-2"></i>
                        <p class="text-xs text-slate-400 font-medium">Belum ada data penjualan<br>di bulan ini.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Aktivitas Transaksi Terakhir (Log / Live Feed) -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2 text-sm">
                        <i data-feather="list" class="w-4 h-4 text-slate-600"></i> Riwayat Aktivitas Jurnal Terakhir
                    </h3>
                    <a href="{{ route('journals.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline transition-colors">Lihat Buku Besar &rarr;</a>
                </div>
                <div class="p-0 flex-1 relative">
                    <!-- Garis Vertikal Timeline -->
                    <div class="absolute left-[27px] top-0 bottom-0 w-0.5 bg-slate-100"></div>

                    @forelse($recentActivities as $act)
                    <div class="p-3 px-4 hover:bg-slate-50 flex gap-4 items-start relative z-10">
                        <div class="mt-1 w-3 h-3 rounded-full bg-blue-500 shrink-0 border-2 border-white shadow-sm ring-2 ring-blue-100"></div>
                        <div class="flex-1 pb-2 {{ !$loop->last ? 'border-b border-slate-50' : '' }}">
                            <p class="text-sm font-semibold text-slate-700 leading-snug">{{ $act->description }}</p>
                            <div class="flex gap-3 mt-1.5 items-center">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-500 rounded text-[10px] font-bold font-mono">{{ $act->reference_no }}</span>
                                <span class="text-[10px] font-medium text-slate-400 flex items-center gap-1"><i data-feather="clock" class="w-3 h-3"></i> {{ \Carbon\Carbon::parse($act->created_at)->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-6 text-center text-xs text-slate-400">Belum ada aktivitas jurnal tercatat.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    <!-- Skrip Inisialisasi Chart.js Dinamis -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
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
                    datasets: [{
                            label: 'Uang Masuk (Kas)',
                            data: dataInflows,
                            backgroundColor: '#22c55e',
                            borderRadius: 4,
                            maxBarSize: 35
                        },
                        {
                            label: 'Uang Keluar (Kas)',
                            data: dataOutflows,
                            backgroundColor: '#ef4444',
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
                                font: {
                                    family: 'sans-serif',
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let value = context.raw || 0;
                                    return context.dataset.label + ': Rp ' + new Intl.NumberFormat(
                                        'id-ID').format(value);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            grid: {
                                color: '#f1f5f9'
                            },
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

            // // ==========================================
            // // 2. GRAFIK DOUGHNUT: STRUKTUR PENGELUARAN
            // // ==========================================
            // const pmbBulanIni = {{ $pembelianBulanIni ?? 0 }};
            // const byaBulanIni = {{ $biayaBulanIni ?? 0 }};

            // const ctxExpense = document.getElementById('expensePieChart').getContext('2d');
            // new Chart(ctxExpense, {
            //     type: 'doughnut',
            //     data: {
            //         labels: ['Pembelian Brg (PO)', 'Biaya Operasional (KBD)'],
            //         datasets: [{
            //             data: [pmbBulanIni, byaBulanIni],
            //             backgroundColor: ['#f97316', '#e11d48'], // Orange untuk Beli, Merah untuk Biaya
            //             borderWidth: 2,
            //             hoverOffset: 6
            //         }]
            //     },
            //     options: {
            //         responsive: true, maintainAspectRatio: false, cutout: '65%',
            //         plugins: {
            //             legend: { position: 'bottom', labels: { padding: 20, font: { family: 'sans-serif', size: 11 } } },
            //             tooltip: {
            //                 callbacks: {
            //                     label: function(context) {
            //                         let value = context.raw || 0;
            //                         return ' ' + context.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(value);
            //                     }
            //                 }
            //             }
            //         }
            //     }
            // });

            // ==========================================
            // 2. GRAFIK DOUGHNUT: STRUKTUR PENGELUARAN
            // ==========================================
            const pmbBulanIni = {{ $pembelianBulanIni ?? 0 }};
            const byaBulanIni = {{ $biayaBulanIni ?? 0 }};

            // KUNCI PERBAIKAN: Logika Cadangan (Fallback) jika nominal = 0
            let expData = [pmbBulanIni, byaBulanIni];
            let expColors = ['#f97316', '#e11d48']; // Orange, Merah
            let expLabels = ['Pembelian Brg (PO)', 'Biaya Operasional (KBD)'];

            if (pmbBulanIni === 0 && byaBulanIni === 0) {
                expData = [1]; // Beri nilai 1 murni untuk memancing Chart.js menggambar lingkaran utuh
                expColors = ['#e2e8f0']; // Warna abu-abu pucat (Netral)
                expLabels = ['Belum ada pengeluaran bulan ini'];
            }

            const ctxExpense = document.getElementById('expensePieChart').getContext('2d');
            new Chart(ctxExpense, {
                type: 'doughnut',
                data: {
                    labels: expLabels,
                    datasets: [{
                        data: expData,
                        backgroundColor: expColors,
                        borderWidth: 2,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                font: {
                                    family: 'sans-serif',
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    // Jika tidak ada pengeluaran, ubah teks tooltipnya
                                    if (pmbBulanIni === 0 && byaBulanIni === 0)
                                        return ' Belum ada data transaksi';

                                    let value = context.raw || 0;
                                    return ' ' + context.label + ': Rp ' + new Intl.NumberFormat(
                                        'id-ID').format(value);
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
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                font: {
                                    family: 'sans-serif',
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    if (bankData.length === 0) return ' Belum ada data';
                                    let value = context.raw || 0;
                                    return ' ' + context.label + ': Rp ' + new Intl.NumberFormat(
                                        'id-ID').format(value);
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    <!-- ============================================== -->
    <!-- MODAL 1: RINCIAN OVERDUE (LEWAT JATUH TEMPO) -->
    <!-- ============================================== -->
    <div id="modalOverdue" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="flex justify-between items-center p-5 border-b border-red-200 bg-red-50">
                <h3 class="text-lg font-bold text-red-800 flex items-center gap-2">
                    <i data-feather="x-octagon" class="w-5 h-5 text-red-600"></i> Rincian Tagihan Menunggak (Overdue)
                </h3>
                <button onclick="document.getElementById('modalOverdue').classList.add('hidden')"
                    class="text-slate-400 hover:text-red-700 transition-colors"><i data-feather="x"
                        class="w-5 h-5"></i></button>
            </div>
            <div class="overflow-y-auto custom-scrollbar p-0 bg-slate-50 flex-1">
                @foreach ($poOverdue as $po)
                    {{-- <a href="{{ route('purchases.bills') }}"
                        class="block p-5 border-b border-slate-200 bg-white hover:bg-red-50/30 transition-colors group"> --}}
                    <button type="button"
                        onclick="openQuickPay('po', {{ $po->id }}, '{{ $po->reference_no ?: 'PO #' . $po->id }}', {{ $po->total }}, false)"
                        class="w-full text-left block p-5 border-b border-slate-200 bg-white hover:bg-red-50 transition-colors group">
                        <div class="flex justify-between items-center">
                            <div>
                                <span
                                    class="px-2 py-1 bg-red-100 text-red-700 rounded text-[10px] font-bold uppercase tracking-wider mb-2 inline-block">Hutang
                                    Pemasok (PO)</span>
                                <h4 class="text-base font-bold text-slate-800 group-hover:text-red-700 transition-colors">
                                    {{ $po->reference_no ?: 'PO #' . $po->id }} - {{ $po->contact->name }}</h4>
                                <p class="text-sm text-red-600 font-bold mt-1"><i data-feather="clock"
                                        class="w-3.5 h-3.5 inline mr-1"></i> Lewat Sejak:
                                    {{ \Carbon\Carbon::parse($po->due_date)->translatedFormat('d M Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-black text-slate-800">Rp
                                    {{ number_format($po->total, 0, ',', '.') }}</p>
                                <p
                                    class="text-xs font-bold text-blue-600 mt-1 flex items-center justify-end gap-1 group-hover:translate-x-1 transition-transform">
                                    Bayar &rarr;</p>
                            </div>
                        </div>
                    </button>
                @endforeach
                @foreach ($soOverdue as $so)
                    {{-- <a href="{{ route('sales.invoices') }}"
                        class="block p-5 border-b border-slate-200 bg-white hover:bg-blue-50/30 transition-colors group"> --}}
                    <button type="button" onclick="openQuickPay('so', {{ $so->id }}, '{{ $so->reference_no ?: 'SO #'.$so->id }}', {{ $so->total }}, true)" class="w-full text-left block p-5 border-b border-slate-200 bg-white hover:bg-blue-50 transition-colors group">
                        <div class="flex justify-between items-center">
                            <div>
                                <span
                                    class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-[10px] font-bold uppercase tracking-wider mb-2 inline-block">Piutang
                                    Pelanggan (SO)</span>
                                <h4 class="text-base font-bold text-slate-800 group-hover:text-blue-700 transition-colors">
                                    {{ $so->reference_no ?: 'SO #' . $so->id }} - {{ $so->contact->name }}</h4>
                                <p class="text-sm text-red-600 font-bold mt-1"><i data-feather="clock"
                                        class="w-3.5 h-3.5 inline mr-1"></i> Lewat Sejak:
                                    {{ \Carbon\Carbon::parse($so->due_date)->translatedFormat('d M Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-black text-slate-800">Rp
                                    {{ number_format($so->total, 0, ',', '.') }}</p>
                                <p
                                    class="text-xs font-bold text-blue-600 mt-1 flex items-center justify-end gap-1 group-hover:translate-x-1 transition-transform">
                                    Tagih &rarr;</p>
                            </div>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- ============================================== -->
    <!-- MODAL 2: RINCIAN HARI INI (TODAY) -->
    <!-- ============================================== -->
    <div id="modalToday" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="flex justify-between items-center p-5 border-b border-orange-200 bg-orange-50">
                <h3 class="text-lg font-bold text-orange-800 flex items-center gap-2">
                    <i data-feather="alert-circle" class="w-5 h-5 text-orange-600"></i> Rincian Jatuh Tempo Hari Ini
                </h3>
                <button onclick="document.getElementById('modalToday').classList.add('hidden')"
                    class="text-slate-400 hover:text-orange-700 transition-colors"><i data-feather="x"
                        class="w-5 h-5"></i></button>
            </div>
            <div class="overflow-y-auto custom-scrollbar p-0 bg-slate-50 flex-1">
                @foreach ($poToday as $po)
                    {{-- <a href="{{ route('purchases.bills') }}"
                        class="block p-5 border-b border-slate-200 bg-white hover:bg-orange-50/30 transition-colors group"> --}}
                    <button type="button"
                        onclick="openQuickPay('po', {{ $po->id }}, '{{ $po->reference_no ?: 'PO #' . $po->id }}', {{ $po->total }}, false)"
                        class="w-full text-left block p-5 border-b border-slate-200 bg-white hover:bg-red-50 transition-colors group">
                        <div class="flex justify-between items-center">
                            <div>
                                <span
                                    class="px-2 py-1 bg-red-100 text-red-700 rounded text-[10px] font-bold uppercase tracking-wider mb-2 inline-block">Hutang
                                    Pemasok (PO)</span>
                                <h4
                                    class="text-base font-bold text-slate-800 group-hover:text-orange-600 transition-colors">
                                    {{ $po->reference_no ?: 'PO #' . $po->id }} - {{ $po->contact->name }}</h4>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-black text-slate-800">Rp
                                    {{ number_format($po->total, 0, ',', '.') }}</p>
                                <p
                                    class="text-xs font-bold text-blue-600 mt-1 flex items-center justify-end gap-1 group-hover:translate-x-1 transition-transform">
                                    Bayar &rarr;</p>
                            </div>
                        </div>
                    </button>
                @endforeach
                @foreach ($soToday as $so)
                    <a href="{{ route('sales.invoices') }}"
                        class="block p-5 border-b border-slate-200 bg-white hover:bg-blue-50/30 transition-colors group">
                        <div class="flex justify-between items-center">
                            <div>
                                <span
                                    class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-[10px] font-bold uppercase tracking-wider mb-2 inline-block">Piutang
                                    Pelanggan (SO)</span>
                                <h4 class="text-base font-bold text-slate-800 group-hover:text-blue-600 transition-colors">
                                    {{ $so->reference_no ?: 'SO #' . $so->id }} - {{ $so->contact->name }}</h4>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-black text-slate-800">Rp
                                    {{ number_format($so->total, 0, ',', '.') }}</p>
                                <p
                                    class="text-xs font-bold text-blue-600 mt-1 flex items-center justify-end gap-1 group-hover:translate-x-1 transition-transform">
                                    Tagih &rarr;</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- ============================================== -->
    <!-- MODAL 3: RINCIAN MENDEKATI TEMPO (UPCOMING) -->
    <!-- ============================================== -->
    <div id="modalUpcoming" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="flex justify-between items-center p-5 border-b border-yellow-200 bg-yellow-50">
                <h3 class="text-lg font-bold text-yellow-800 flex items-center gap-2">
                    <i data-feather="clock" class="w-5 h-5 text-yellow-600"></i> Rincian Mendekati Jatuh Tempo (H-7)
                </h3>
                <button onclick="document.getElementById('modalUpcoming').classList.add('hidden')"
                    class="text-slate-400 hover:text-yellow-700 transition-colors"><i data-feather="x"
                        class="w-5 h-5"></i></button>
            </div>
            <div class="overflow-y-auto custom-scrollbar p-0 bg-slate-50 flex-1">
                @foreach ($poUpcoming as $po)
                    {{-- <a href="{{ route('purchases.bills') }}"
                        class="block p-5 border-b border-slate-200 bg-white hover:bg-yellow-50/30 transition-colors group"> --}}
                    <button type="button" onclick="openQuickPay('po', {{ $po->id }}, '{{ $po->reference_no ?: 'PO #'.$po->id }}', {{ $po->total }}, false)" class="w-full text-left block p-5 border-b border-slate-200 bg-white hover:bg-red-50 transition-colors group">
                        <div class="flex justify-between items-center">
                            <div>
                                <span
                                    class="px-2 py-1 bg-red-100 text-red-700 rounded text-[10px] font-bold uppercase tracking-wider mb-2 inline-block">Hutang
                                    Pemasok (PO)</span>
                                <h4
                                    class="text-base font-bold text-slate-800 group-hover:text-yellow-600 transition-colors">
                                    {{ $po->reference_no ?: 'PO #' . $po->id }} - {{ $po->contact->name }}</h4>
                                <p class="text-sm text-yellow-600 font-bold mt-1"><i data-feather="calendar"
                                        class="w-3.5 h-3.5 inline mr-1"></i> Tanggal:
                                    {{ \Carbon\Carbon::parse($po->due_date)->translatedFormat('d M Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-black text-slate-800">Rp
                                    {{ number_format($po->total, 0, ',', '.') }}</p>
                                <p
                                    class="text-xs font-bold text-blue-600 mt-1 flex items-center justify-end gap-1 group-hover:translate-x-1 transition-transform">
                                    Lihat Tagihan &rarr;</p>
                            </div>
                        </div>
                    </button>
                @endforeach
                @foreach ($soUpcoming as $so)
                    <a href="{{ route('sales.invoices') }}"
                        class="block p-5 border-b border-slate-200 bg-white hover:bg-blue-50/30 transition-colors group">
                        <div class="flex justify-between items-center">
                            <div>
                                <span
                                    class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-[10px] font-bold uppercase tracking-wider mb-2 inline-block">Piutang
                                    Pelanggan (SO)</span>
                                <h4 class="text-base font-bold text-slate-800 group-hover:text-blue-600 transition-colors">
                                    {{ $so->reference_no ?: 'SO #' . $so->id }} - {{ $so->contact->name }}</h4>
                                <p class="text-sm text-yellow-600 font-bold mt-1"><i data-feather="calendar"
                                        class="w-3.5 h-3.5 inline mr-1"></i> Tanggal:
                                    {{ \Carbon\Carbon::parse($so->due_date)->translatedFormat('d M Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-black text-slate-800">Rp
                                    {{ number_format($so->total, 0, ',', '.') }}</p>
                                <p
                                    class="text-xs font-bold text-blue-600 mt-1 flex items-center justify-end gap-1 group-hover:translate-x-1 transition-transform">
                                    Lihat Tagihan &rarr;</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- ============================================== -->
    <!-- MODAL 4: QUICK PAY (PELUNASAN CEPAT)           -->
    <!-- ============================================== -->
    <div id="quickPayModal"
        class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-[100] p-4 backdrop-blur-sm transition-all">
        <div
            class="bg-white rounded-xl w-full max-w-md p-6 shadow-2xl overflow-hidden flex flex-col border-t-4 border-blue-600">
            <div class="flex justify-between items-center mb-5">
                <h3 id="qpTitle" class="text-lg font-bold text-slate-800">Proses Pelunasan Cepat</h3>
                <button onclick="document.getElementById('quickPayModal').classList.add('hidden')"
                    class="text-slate-400 hover:text-red-500 bg-slate-100 hover:bg-red-50 rounded-full p-1 transition-colors">
                    <i data-feather="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="#" method="POST" id="quickPayForm">
                @csrf
                <div class="bg-slate-50 border border-slate-200 p-4 rounded-lg mb-5 text-center">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Total Tagihan</p>
                    <p id="qpAmount" class="text-2xl font-black text-slate-800"></p>
                    <p class="text-sm font-medium text-slate-600 mt-1">Ref: <b id="qpRef" class="text-blue-700"></b>
                    </p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Pilih Rekening Transaksi</label>
                        <select name="payment_account" required
                            class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500 font-semibold text-slate-700 shadow-sm">
                            <option value="" disabled selected>-- Pilih Kas atau Bank --</option>
                            @foreach ($bankAccounts as $bank)
                                <option value="{{ $bank->code }}">[{{ $bank->code }}] {{ $bank->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Tanggal Pelunasan</label>
                        <input type="date" name="due_date" required value="{{ date('Y-m-d') }}"
                            class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500 font-medium shadow-sm">
                    </div>
                </div>

                <div
                    class="mt-5 p-3 bg-blue-50 border border-blue-100 rounded-lg text-xs text-blue-800 font-medium text-center">
                    Transaksi ini akan memotong saldo tagihan dan langsung dicatat ke Jurnal Buku Besar Bank terpilih.
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('quickPayModal').classList.add('hidden')"
                        class="px-5 py-2.5 text-slate-600 font-bold hover:bg-slate-100 rounded-lg transition-colors">Batal</button>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-bold transition-colors shadow-md">Lunasi
                        Sekarang</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Skrip Pengendali Quick Pay -->
    <script>
        function openQuickPay(type, id, ref, amount, isSales) {
            // Sembunyikan 3 modal peringatan yang ada di belakangnya agar rapi
            document.getElementById('modalOverdue').classList.add('hidden');
            document.getElementById('modalToday').classList.add('hidden');
            document.getElementById('modalUpcoming').classList.add('hidden');

            // Sesuaikan warna dan judul berdasarkan jenis transaksi
            let title = isSales ? 'Terima Pembayaran Piutang' : 'Bayar Hutang Pemasok';
            let actionUrl = isSales ? `/sales/${id}/invoice` : `/purchases/${id}/bill`;

            document.getElementById('qpTitle').innerText = title;
            document.getElementById('qpRef').innerText = ref;
            document.getElementById('qpAmount').innerText = 'Rp ' + parseInt(amount, 10).toLocaleString('id-ID');

            document.getElementById('quickPayForm').action = actionUrl;
            document.getElementById('quickPayModal').classList.remove('hidden');
        }
    </script>
@endsection
