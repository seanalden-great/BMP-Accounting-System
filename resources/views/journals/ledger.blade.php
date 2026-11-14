@extends('layouts.app')
    
@section('content')
<div class="space-y-6 pb-10 relative">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="book-open" class="w-7 h-7 text-indigo-600"></i> Buku Besar (General Ledger)
            </h1>
            <p class="text-slate-500 mt-1">Lacak mutasi detail, saldo awal, dan saldo akhir per Akun (CoA)</p>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <button type="button" onclick="validateAndExport()" class="w-full sm:w-auto bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 text-emerald-700 px-5 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-colors flex items-center justify-center gap-2">
                <i data-feather="printer" class="w-4 h-4"></i> Ekspor Buku Besar (PDF)
            </button>
        </div>
    </div>

    <!-- FILTER PENCARIAN -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
        <form action="{{ route('journals.ledger') }}" method="GET" id="filterForm" class="flex flex-col sm:flex-row items-end gap-4">

            <!-- Pilih Akun -->
            <div class="flex-1 w-full">
                <label class="block text-sm font-bold text-slate-700 mb-1">Pilih Akun (CoA)</label>
                <select name="account_code" id="accountDropdown" required class="w-full border border-indigo-200 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-indigo-500 font-semibold text-slate-700 shadow-sm bg-slate-50">
                    <option value="" disabled {{ empty($selectedAccountCode) ? 'selected' : '' }}>-- Cari & Pilih Akun --</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->code }}" {{ $selectedAccountCode == $acc->code ? 'selected' : '' }}>
                            [{{ $acc->code }}] {{ $acc->name }} (Normal: {{ $acc->normal_balance }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Periode Awal -->
            <div class="w-full sm:w-40">
                <label class="block text-sm font-bold text-slate-700 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" id="startDate" required value="{{ $startDate }}" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-indigo-500 text-sm font-medium">
            </div>

            <!-- Periode Akhir -->
            <div class="w-full sm:w-40">
                <label class="block text-sm font-bold text-slate-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" id="endDate" required value="{{ $endDate }}" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-indigo-500 text-sm font-medium">
            </div>

            <!-- Tombol Filter -->
            <div class="w-full sm:w-auto">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-bold shadow-sm transition-colors flex items-center justify-center gap-2">
                    <i data-feather="search" class="w-4 h-4"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>

    <!-- TABEL BUKU BESAR -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-300 overflow-hidden">

        <!-- Header Identitas Halaman -->
        @if($selectedAccount)
        <div class="p-5 border-b border-slate-300 bg-slate-50 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-black text-slate-800 tracking-tight uppercase">
                    [{{ $selectedAccount->code }}] {{ $selectedAccount->name }}
                </h2>
                <p class="text-sm font-semibold text-slate-500 mt-1">
                    Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
                </p>
            </div>
            <div class="text-right hidden sm:block">
                <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-xs font-bold border border-indigo-200">
                    Pos Saldo Normal: {{ $selectedAccount->normal_balance == 'D' ? 'DEBIT' : 'KREDIT' }}
                </span>
            </div>
        </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse min-w-[900px]">
                <thead class="bg-slate-200 border-b border-slate-300">
                    <tr>
                        <th class="p-3 text-center w-28 font-bold text-slate-700 border-r border-slate-300">Tanggal</th>
                        <th class="p-3 text-left w-36 font-bold text-slate-700 border-r border-slate-300">No. Referensi</th>
                        <th class="p-3 text-left font-bold text-slate-700 border-r border-slate-300">Keterangan / Uraian</th>
                        <th class="p-3 text-right w-36 font-bold text-slate-700 border-r border-slate-300">Debit (Rp)</th>
                        <th class="p-3 text-right w-36 font-bold text-slate-700 border-r border-slate-300">Kredit (Rp)</th>
                        <th class="p-3 text-right w-40 font-black text-indigo-900 bg-indigo-100">Saldo Berjalan (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @if($selectedAccount)

                        @php
                            $runningBalance = $beginningBalance;
                            $totalDebitMutasi = 0;
                            $totalKreditMutasi = 0;
                        @endphp

                        <!-- BARIS SALDO AWAL -->
                        <tr class="bg-amber-50/50 border-b border-slate-200">
                            <td class="p-3 text-center font-medium text-slate-500">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</td>
                            <td class="p-3 font-semibold text-slate-400 italic">-</td>
                            <td class="p-3 font-bold text-slate-700 uppercase">Saldo Awal Sebelum Periode</td>
                            <td class="p-3 text-right text-slate-400">-</td>
                            <td class="p-3 text-right text-slate-400">-</td>
                            <td class="p-3 text-right font-black text-slate-800 bg-indigo-50/30">
                                {{ number_format($beginningBalance, 0, ',', '.') }}
                            </td>
                        </tr>

                        <!-- LOOPING TRANSAKSI -->
                        @forelse($entries as $entry)
                            @php
                                $totalDebitMutasi += $entry->debit;
                                $totalKreditMutasi += $entry->credit;

                                // Logika penambahan/pengurangan berdasarkan Saldo Normal
                                if ($selectedAccount->normal_balance == 'K') {
                                    $runningBalance += ($entry->credit - $entry->debit);
                                } else {
                                    $runningBalance += ($entry->debit - $entry->credit);
                                }
                            @endphp
                            <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                <td class="p-3 text-center font-medium text-slate-600">
                                    {{ \Carbon\Carbon::parse($entry->journal->date)->format('d/m/Y') }}
                                </td>
                                <td class="p-3 font-bold text-indigo-600 text-xs">
                                    {{ $entry->journal->reference_no ?: 'JRN-'.$entry->journal_id }}
                                </td>
                                <td class="p-3 font-medium text-slate-800">
                                    {{ $entry->journal->description }}
                                </td>
                                <td class="p-3 text-right font-semibold text-slate-700">
                                    {{ $entry->debit > 0 ? number_format($entry->debit, 0, ',', '.') : '-' }}
                                </td>
                                <td class="p-3 text-right font-semibold text-slate-700">
                                    {{ $entry->credit > 0 ? number_format($entry->credit, 0, ',', '.') : '-' }}
                                </td>
                                <td class="p-3 text-right font-bold text-slate-800 bg-indigo-50/30">
                                    {{ $runningBalance < 0 ? '(' . number_format(abs($runningBalance), 0, ',', '.') . ')' : number_format($runningBalance, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-500 font-medium">Tidak ada mutasi transaksi pada rentang tanggal ini.</td>
                            </tr>
                        @endforelse

                        <!-- BARIS FOOTER SALDO AKHIR -->
                        <tr class="bg-slate-800 text-white">
                            <td colspan="3" class="p-4 text-right font-black uppercase tracking-wider border-r border-slate-700">
                                Mutasi Berjalan & Saldo Akhir
                            </td>
                            <td class="p-4 text-right font-bold text-emerald-400 border-r border-slate-700">
                                Rp {{ number_format($totalDebitMutasi, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-right font-bold text-emerald-400 border-r border-slate-700">
                                Rp {{ number_format($totalKreditMutasi, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-right font-black text-amber-400 text-lg">
                                Rp {{ number_format($runningBalance, 0, ',', '.') }}
                            </td>
                        </tr>

                    @else
                        <tr>
                            <td colspan="6" class="p-16 text-center">
                                <i data-feather="search" class="w-12 h-12 text-slate-300 mx-auto mb-4"></i>
                                <p class="text-slate-500 font-bold text-lg">Silakan pilih Akun (CoA) dari filter di atas.</p>
                                <p class="text-slate-400 text-sm mt-1">Sistem akan mengkalkulasi saldo awal dan seluruh mutasi jurnal yang terkait.</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // --- SWEETALERT2: VALIDASI EKSPOR BUKU BESAR ---
    function validateAndExport() {
        const accCode = document.getElementById('accountDropdown').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        if (!accCode) {
            Swal.fire({
                icon: 'warning',
                title: 'Akun Belum Dipilih!',
                text: 'Anda harus memilih salah satu Akun (CoA) terlebih dahulu untuk bisa diekspor.',
                confirmButtonColor: '#4f46e5',
                confirmButtonText: 'Mengerti'
            });
            return;
        }

        // Tampilkan pesan Loading
        Swal.fire({
            title: 'Mempersiapkan Dokumen...',
            text: 'Sedang mengekstrak mutasi Buku Besar ke format PDF.',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });

        // Karena ini export PDF di tab baru, kita tutup Swal setelah beberapa detik
        setTimeout(() => {
            Swal.close();
        }, 2000);

        // Jika rute cetak PDF sudah Anda buat, arahkan ke rutenya.
        // Contoh: window.open(`/ledger/export?account_code=${accCode}&start_date=${startDate}&end_date=${endDate}`, '_blank');

        // Peringatan sementara:
        Swal.fire('Fitur Ekspor PDF', 'Endpoint cetak PDF bisa disambungkan oleh developer selanjutnya di sini.', 'info');
    }

    // --- SWEETALERT2: ANIMASI LOADING SAAT FILTER DATA ---
    document.getElementById('filterForm').addEventListener('submit', function() {
        const accCode = document.getElementById('accountDropdown').value;
        if (accCode) {
            Swal.fire({
                title: 'Mengkalkulasi Saldo...',
                text: 'Sistem sedang membaca seluruh mutasi di Buku Besar (Ledger).',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); }
            });
        }
    });
</script>
@endsection
