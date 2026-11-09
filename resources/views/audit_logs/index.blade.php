<!-- resources/views/audit_logs/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10 relative">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="shield" class="w-7 h-7 text-rose-600"></i> Audit Trail (Log Aktivitas)
            </h1>
            <p class="text-slate-500 mt-1">Pantau seluruh rekam jejak perubahan data krusial di dalam sistem ERP</p>
        </div>
    </div>

    <!-- FILTER PENCARIAN BERDASARKAN MODUL -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <form method="GET" action="{{ route('audit.index') }}" class="flex flex-col sm:flex-row items-center gap-4">
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="p-2 bg-rose-50 text-rose-600 rounded-lg shrink-0">
                    <i data-feather="filter" class="w-5 h-5"></i>
                </div>
                <select name="module" onchange="this.form.submit()" class="w-full sm:w-64 border border-slate-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-rose-500 text-slate-700 font-medium transition-all">
                    <option value="">-- Semua Modul Sistem --</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod }}" {{ request('module') == $mod ? 'selected' : '' }}>{{ $mod }}</option>
                    @endforeach
                </select>
            </div>

            @if(request('module'))
                <a href="{{ route('audit.index') }}" class="text-slate-500 hover:text-rose-600 text-sm font-bold flex items-center gap-1 bg-slate-100 hover:bg-rose-50 px-4 py-2.5 rounded-lg transition-colors">
                    <i data-feather="x" class="w-4 h-4"></i> Reset Filter
                </a>
            @endif
        </form>
    </div>

    <!-- TABEL REKAM JEJAK -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1000px]">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600 w-40">Waktu Kejadian</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 w-48">User Pelaku</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center w-32">Aksi</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 w-40">Modul</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Rincian Aktivitas</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center w-32">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="p-4 text-sm text-slate-600 font-mono">{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}</td>
                        <td class="p-4">
                            <p class="text-sm font-bold text-slate-800">{{ $log->user->name ?? 'Sistem / Guest' }}</p>
                            <p class="text-[10px] text-slate-400 font-medium uppercase mt-0.5">{{ $log->user->role ?? 'SISTEM' }}</p>
                        </td>
                        <td class="p-4 text-center">
                            @php
                                $action = strtoupper($log->action);
                                // Logika Label Warna Otomatis berdasarkan Kata Kunci
                                $color = 'bg-slate-100 text-slate-700 border-slate-200';

                                if (str_contains($action, 'BUAT') || str_contains($action, 'TAMBAH')) {
                                    $color = 'bg-emerald-100 text-emerald-700 border-emerald-200';
                                } elseif (str_contains($action, 'UBAH') || str_contains($action, 'EDIT')) {
                                    $color = 'bg-blue-100 text-blue-700 border-blue-200';
                                } elseif (str_contains($action, 'HAPUS') || str_contains($action, 'DELETE')) {
                                    $color = 'bg-red-100 text-red-700 border-red-200';
                                } elseif (str_contains($action, 'SETUJU') || str_contains($action, 'APPROVE')) {
                                    $color = 'bg-indigo-100 text-indigo-700 border-indigo-200';
                                } elseif (str_contains($action, 'TOLAK') || str_contains($action, 'REJECT')) {
                                    $color = 'bg-orange-100 text-orange-700 border-orange-200';
                                }
                            @endphp
                            <span class="px-2.5 py-1.5 rounded text-[10px] font-black uppercase tracking-wider border {{ $color }}">
                                {{ $action }}
                            </span>
                        </td>
                        <td class="p-4 text-sm font-semibold text-slate-600">{{ $log->module }}</td>
                        <td class="p-4 text-sm text-slate-700 leading-relaxed">{{ $log->description }}</td>
                        <td class="p-4 text-xs text-center text-slate-400 font-mono bg-slate-50/50">{{ $log->ip_address ?? 'Tidak Terdeteksi' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-10 text-center">
                            <i data-feather="shield-off" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
                            <p class="text-slate-500 font-medium">Belum ada aktivitas krusial yang terekam di sistem.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION (Link Halaman Selanjutnya) -->
        @if($logs->hasPages())
        <div class="p-4 border-t border-slate-200 bg-slate-50">
            {{ $logs->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
