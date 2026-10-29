<!-- resources/views/dashboard.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">
    <!-- Header Dashboard -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-3xl font-bold text-slate-800">Dashboard Keuangan</h2>
            <p class="text-slate-500 mt-1">Ringkasan bisnis PT Berlian Mandiri Perkasa (Bulan Ini)</p>
        </div>
        <div class="flex gap-2">
            <button class="flex items-center gap-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 px-4 py-2 rounded-lg font-medium transition-colors shadow-sm">
                <i data-feather="file-text" class="w-4 h-4"></i> Tagihan
            </button>
            <button class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors shadow-sm">
                <i data-feather="plus" class="w-4 h-4"></i> Buat Invoice
            </button>
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
@endsection