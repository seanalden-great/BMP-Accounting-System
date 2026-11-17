@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 dark:text-white flex items-center gap-3 transition-colors">
                <i data-feather="check-square" class="text-emerald-600 dark:text-emerald-400 w-8 h-8"></i> Pusat Persetujuan
            </h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 transition-colors">Verifikasi dan otorisasi seluruh dokumen transaksional perusahaan di satu tempat.</p>
        </div>

        @if($pendingDocuments->count() > 0)
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <button onclick="processBulk('reject')" class="w-full sm:w-auto bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-900/30 px-5 py-2.5 rounded-lg font-bold text-sm transition-colors shadow-sm flex items-center justify-center gap-2">
                <i data-feather="x-circle" class="w-4 h-4"></i> Tolak Terpilih
            </button>
            <button onclick="processBulk('approve')" class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white px-6 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-colors flex items-center justify-center gap-2">
                <i data-feather="check-circle" class="w-4 h-4"></i> Setujui Terpilih (Approve All)
            </button>
        </div>
        @endif
    </div>

    <!-- MAIN TABLE -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden transition-colors">
        <div class="p-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 flex justify-between items-center transition-colors">
            <h3 class="font-bold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                <i data-feather="inbox" class="w-4 h-4 text-slate-500"></i> Dokumen Menunggu Antrean ({{ $pendingDocuments->count() }})
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[950px]">
                <thead class="bg-white dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700 transition-colors">
                    <tr>
                        <th class="p-4 w-12 text-center">
                            <input type="checkbox" id="checkAll" class="w-5 h-5 rounded border-slate-300 dark:border-slate-600 text-emerald-600 focus:ring-emerald-500 cursor-pointer bg-slate-50 dark:bg-slate-700">
                        </th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300">Tanggal</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300">Tipe Dokumen</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300">No. Referensi</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300">Pihak Terkait</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300 text-right">Nilai Transaksi</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300 text-center w-28">Aksi Kilat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingDocuments as $doc)
                        <tr class="border-b border-slate-50 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="p-4 text-center">
                                <input type="checkbox" class="doc-checkbox w-5 h-5 rounded border-slate-300 dark:border-slate-600 text-emerald-600 focus:ring-emerald-500 cursor-pointer bg-white dark:bg-slate-700"
                                    data-approve="{{ $doc->approve_url }}"
                                    data-reject="{{ $doc->reject_url }}">
                            </td>
                            <td class="p-4 text-sm font-medium text-slate-600 dark:text-slate-400">
                                {{ \Carbon\Carbon::parse($doc->date)->translatedFormat('d M Y') }}
                            </td>
                            <td class="p-4 text-sm">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $doc->type_class }} inline-flex items-center gap-1.5 transition-colors">
                                    <i data-feather="{{ $doc->icon }}" class="w-3 h-3"></i> {{ $doc->doc_type }}
                                </span>
                            </td>
                            <td class="p-4 text-sm font-bold text-slate-800 dark:text-white font-mono">{{ $doc->reference_no ?: '-' }}</td>
                            <td class="p-4 text-sm font-medium text-slate-700 dark:text-slate-300">{{ $doc->person }}</td>
                            <td class="p-4 text-sm font-black text-slate-800 dark:text-white text-right">Rp {{ number_format($doc->amount, 0, ',', '.') }}</td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="processSingle('approve', '{{ $doc->approve_url }}')" class="text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 p-2 rounded-lg transition-colors border border-transparent hover:border-emerald-200 dark:hover:border-emerald-800 shadow-sm" title="Setujui">
                                        <i data-feather="check-circle" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="processSingle('reject', '{{ $doc->reject_url }}')" class="text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 p-2 rounded-lg transition-colors border border-transparent hover:border-red-200 dark:hover:border-red-800 shadow-sm" title="Tolak">
                                        <i data-feather="x-circle" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center flex flex-col items-center justify-center text-slate-500 dark:text-slate-400">
                                <i data-feather="check-circle" class="w-12 h-12 text-emerald-400 mb-3 opacity-50"></i>
                                <p class="font-medium text-lg">Meja kerja bersih!</p>
                                <p class="text-sm mt-1">Tidak ada dokumen yang memerlukan persetujuan Anda saat ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // --- LOGIKA CHECKBOX ---
    const checkAll = document.getElementById('checkAll');
    const checkboxes = document.querySelectorAll('.doc-checkbox');

    if(checkAll) {
        checkAll.addEventListener('change', (e) => {
            checkboxes.forEach(cb => { cb.checked = e.target.checked; });
        });
    }

    // --- LOGIKA PERSETUJUAN TUNGGAL (SINGLE AJAX) ---
    async function processSingle(action, url) {
        const isApprove = action === 'approve';
        const actionText = isApprove ? 'menyetujui' : 'menolak';

        const result = await Swal.fire({
            title: `Konfirmasi`,
            text: `Anda yakin ingin ${actionText} dokumen ini?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: isApprove ? '#059669' : '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: `Ya, ${isApprove ? 'Setujui' : 'Tolak'}`,
            cancelButtonText: 'Batal',
            reverseButtons: true
        });

        if(!result.isConfirmed) return;

        executeAjax(url, isApprove ? 'Menyetujui dokumen...' : 'Menolak dokumen...');
    }

    // --- LOGIKA PERSETUJUAN MASSAL (BULK AJAX) ---
    async function processBulk(actionType) {
        const selected = document.querySelectorAll('.doc-checkbox:checked');
        if(selected.length === 0) {
            Swal.fire('Peringatan', 'Silakan centang minimal satu dokumen terlebih dahulu!', 'warning');
            return;
        }

        const isApprove = actionType === 'approve';
        const actionText = isApprove ? 'menyetujui' : 'menolak';

        const result = await Swal.fire({
            title: `Proses Massal`,
            text: `Anda akan ${actionText} ${selected.length} dokumen sekaligus. Lanjutkan?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: isApprove ? '#059669' : '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Proses Semua',
            cancelButtonText: 'Batal',
            reverseButtons: true
        });

        if(!result.isConfirmed) return;

        Swal.fire({
            title: 'Memproses Massal...',
            html: 'Sistem sedang mengeksekusi dokumen dan mencetak jurnal...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });

        let successCount = 0;
        let failCount = 0;

        // Proses Berurutan (Smart Loop AJAX)
        for (let cb of selected) {
            const url = isApprove ? cb.dataset.approve : cb.dataset.reject;
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                });
                if (response.ok) { successCount++; } else { failCount++; }
            } catch (err) { failCount++; }
        }

        Swal.fire({
            title: 'Selesai!',
            text: `${successCount} dokumen berhasil diproses, ${failCount} gagal.`,
            icon: failCount > 0 ? 'warning' : 'success'
        }).then(() => {
            window.location.reload();
        });
    }

    // Executor untuk Single Request
    async function executeAjax(url, loadingMessage) {
        Swal.fire({
            title: 'Memproses...',
            text: loadingMessage,
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });
            if (response.ok) {
                Swal.fire('Berhasil!', 'Dokumen telah diproses.', 'success').then(() => window.location.reload());
            } else {
                Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error');
            }
        } catch (error) {
            Swal.fire('Error!', 'Tidak dapat terhubung ke server.', 'error');
        }
    }
</script>
@endsection
