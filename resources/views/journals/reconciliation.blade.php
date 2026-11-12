@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10 max-w-6xl mx-auto md:mx-0">
    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="check-square" class="text-teal-600 w-7 h-7"></i> Rekonsiliasi Bank
            </h1>
            <p class="text-slate-500 mt-1">Cocokkan transaksi di sistem ERP dengan mutasi rekening koran asli</p>
        </div>

        @if($selectedBank)
            <!-- Cek Hak Akses Create (C) untuk Penyesuaian -->
            @if(\App\Models\CompanySetting::canAction('C', 'Kas & Bank'))
            <button onclick="document.getElementById('adjustModal').classList.remove('hidden')" class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2.5 rounded-lg font-bold transition-colors shadow-sm flex items-center gap-2">
                <i data-feather="plus" class="w-4 h-4"></i> Catat Admin / Bunga
            </button>
            @endif
        @endif
    </div>

    <!-- FILTER PENCARIAN -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
        <form action="{{ route('journals.reconciliation') }}" method="GET" class="flex flex-col sm:flex-row items-end gap-4">
            <div class="flex-1 w-full">
                <label class="block text-sm font-bold text-slate-700 mb-1">Pilih Rekening Transaksi</label>
                <select name="account_code" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-teal-500 font-semibold text-slate-700">
                    <option value="" disabled {{ !$selectedBank ? 'selected' : '' }}>-- Pilih Kas / Bank --</option>
                    @foreach($banks as $bank)
                        <option value="{{ $bank->code }}" {{ $selectedBank == $bank->code ? 'selected' : '' }}>[{{ $bank->code }}] {{ $bank->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-64">
                <label class="block text-sm font-bold text-slate-700 mb-1">Bulan & Tahun (Periode)</label>
                <input type="month" name="month" required value="{{ $selectedMonth }}" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-teal-500 font-semibold text-slate-700">
            </div>
            <button type="submit" class="w-full sm:w-auto bg-slate-800 hover:bg-slate-900 text-white px-6 py-2.5 rounded-lg font-bold transition-colors shadow-sm">
                Tampilkan Data
            </button>
        </form>
    </div>

    @if($selectedBank)
        <!-- DASBOR KALKULASI SALDO LIVE -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-slate-50 border border-slate-200 p-5 rounded-xl shadow-sm">
                <p class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1">Saldo Sistem ERP (Akhir Bulan)</p>
                <h3 class="text-3xl font-black text-slate-800">Rp {{ number_format($systemBalance, 0, ',', '.') }}</h3>
                <p class="text-xs font-medium text-slate-500 mt-2">Total murni berdasarkan seluruh jurnal yang tercatat.</p>
            </div>
            <div class="bg-teal-50 border border-teal-200 p-5 rounded-xl shadow-sm">
                <p class="text-sm font-bold text-teal-700 uppercase tracking-wider mb-1 flex justify-between">
                    <span>Saldo Terekonsiliasi (Cocok)</span>
                    <i data-feather="check-circle" class="w-4 h-4"></i>
                </p>
                <h3 id="displayReconciled" class="text-3xl font-black text-teal-700">Rp 0</h3>
                <p class="text-xs font-medium text-teal-600 mt-2">Saldo ini bergerak otomatis sesuai centang Anda. Samakan angka ini dengan saldo akhir di Rekening Koran fisik Anda.</p>
            </div>
        </div>

        <!-- TABEL PENCOCOKAN (CHECKLIST) -->
        <form action="{{ route('journals.reconciliation.save') }}" method="POST">
            @csrf
            <input type="hidden" name="account_code" value="{{ $selectedBank }}">
            <input type="hidden" name="month" value="{{ $selectedMonth }}">

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-4 bg-white flex justify-between items-center border-b border-slate-200">
                    <h3 class="font-bold text-slate-800">Daftar Transaksi Bulan Ini</h3>
                    <!-- Cek Hak Akses Update (U) -->
                    @if(\App\Models\CompanySetting::canAction('U', 'Kas & Bank'))
                        <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2 rounded-lg font-bold text-sm shadow-sm transition-colors">
                            Simpan Hasil Rekonsiliasi
                        </button>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="p-4 text-center w-12"><i data-feather="check-square" class="w-4 h-4 text-slate-400 mx-auto"></i></th>
                                <th class="p-4 text-sm font-semibold text-slate-600">Tanggal</th>
                                <th class="p-4 text-sm font-semibold text-slate-600">No. Bukti / Referensi</th>
                                <th class="p-4 text-sm font-semibold text-slate-600">Keterangan Jurnal</th>
                                <th class="p-4 text-sm font-semibold text-slate-600 text-right">Uang Masuk (Debit)</th>
                                <th class="p-4 text-sm font-semibold text-slate-600 text-right">Uang Keluar (Kredit)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($entries as $entry)
                            <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors {{ $entry->is_reconciled ? 'bg-teal-50/30' : '' }}">
                                <td class="p-4 text-center">
                                    <input type="checkbox" name="entry_ids[]" value="{{ $entry->id }}"
                                        class="recon-cb w-5 h-5 rounded border-slate-300 text-teal-600 focus:ring-teal-500 cursor-pointer"
                                        data-debit="{{ $entry->debit }}" data-credit="{{ $entry->credit }}"
                                        onchange="calculateReconciliation()"
                                        {{ $entry->is_reconciled ? 'checked' : '' }}
                                        {{ !\App\Models\CompanySetting::canAction('U', 'Kas & Bank') ? 'disabled' : '' }}>
                                </td>
                                <td class="p-4 text-sm font-medium text-slate-700">{{ \Carbon\Carbon::parse($entry->journal->date)->format('d/m/Y') }}</td>
                                <td class="p-4 text-sm font-bold text-slate-800 font-mono">{{ $entry->journal->reference_no }}</td>
                                <td class="p-4 text-sm text-slate-600">{{ $entry->journal->description }}</td>
                                <td class="p-4 text-sm font-bold text-emerald-600 text-right">{{ $entry->debit > 0 ? 'Rp '.number_format($entry->debit, 0, ',', '.') : '-' }}</td>
                                <td class="p-4 text-sm font-bold text-rose-600 text-right">{{ $entry->credit > 0 ? 'Rp '.number_format($entry->credit, 0, ',', '.') : '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-10 text-center text-slate-500 font-medium">Belum ada pergerakan transaksi di rekening ini pada bulan yang Anda pilih.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </form>

        <!-- SCRIPT PERHITUNGAN LIVE SALDO TEREKONSILIASI -->
        <script>
            let baseBalance = {{ $beginningReconciled }};

            function calculateReconciliation() {
                let totalCheckedDebit = 0;
                let totalCheckedCredit = 0;

                document.querySelectorAll('.recon-cb:checked').forEach(cb => {
                    totalCheckedDebit += parseFloat(cb.dataset.debit);
                    totalCheckedCredit += parseFloat(cb.dataset.credit);
                });

                let currentReconciled = baseBalance + totalCheckedDebit - totalCheckedCredit;
                document.getElementById('displayReconciled').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(currentReconciled);
            }

            // Jalankan perhitungan saat halaman pertama kali dimuat
            document.addEventListener('DOMContentLoaded', function() {
                calculateReconciliation();
            });
        </script>
    @endif
</div>

<!-- MODAL PENYESUAIAN CEPAT (ADMIN BANK / BUNGA) -->
<div id="adjustModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col border-t-4 border-teal-600">
        <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-bold text-slate-800">Catat Biaya / Bunga Bank Cepat</h3>
            <button onclick="document.getElementById('adjustModal').classList.add('hidden')" class="text-slate-400 hover:text-red-500 bg-white rounded-full p-1 shadow-sm"><i data-feather="x" class="w-5 h-5"></i></button>
        </div>

        <form action="{{ route('journals.reconciliation.adjust') }}" method="POST" class="p-5 space-y-4" onsubmit="unformatRibuan(this)">
            @csrf
            <input type="hidden" name="bank_code" value="{{ $selectedBank }}">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Jenis Transaksi</label>
                    <select name="type" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-teal-500 font-bold text-slate-800">
                        <option value="out">➖ Uang Keluar (Biaya Admin / Pajak)</option>
                        <option value="in">➕ Uang Masuk (Pendapatan Bunga)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Tanggal Transaksi</label>
                    <input type="date" name="date" required value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-teal-500 font-semibold">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Pilih Akun Lawan (Biaya / Pendapatan)</label>
                <select name="adj_account_code" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-teal-500 font-semibold text-slate-700">
                    <option value="" disabled selected>-- Pilih Akun --</option>
                    <optgroup label="Akun Pengeluaran (Beban)">
                        @foreach($expenseAccounts as $acc)
                            <option value="{{ $acc->code }}">[{{ $acc->code }}] {{ $acc->name }}</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Akun Pemasukan (Pendapatan)">
                        @foreach($incomeAccounts as $acc)
                            <option value="{{ $acc->code }}">[{{ $acc->code }}] {{ $acc->name }}</option>
                        @endforeach
                    </optgroup>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Nominal (Rp)</label>
                <input type="text" name="amount" required placeholder="0" oninput="formatRibuan(this)" class="format-ribuan w-full border border-slate-300 rounded-lg p-2.5 font-bold outline-none focus:ring-2 focus:ring-teal-500 text-xl">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Keterangan Mutasi</label>
                <input type="text" name="description" required placeholder="Cth: Biaya Admin Bank BCA Bulan Ini" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-teal-500">
            </div>

            <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('adjustModal').classList.add('hidden')" class="px-4 py-2 text-slate-600 font-bold hover:bg-slate-100 rounded-lg transition-colors">Batal</button>
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2.5 rounded-lg font-bold shadow-sm transition-colors">Simpan Penyesuaian</button>
            </div>
        </form>
    </div>
</div>

<script>
    function formatRibuan(inputElement) {
        let value = inputElement.value.replace(/[^0-9]/g, '');
        if (value !== '') { inputElement.value = parseInt(value, 10).toLocaleString('id-ID'); } else { inputElement.value = ''; }
    }
    function unformatRibuan(formElement) {
        let formattedInputs = formElement.querySelectorAll('.format-ribuan');
        formattedInputs.forEach(input => { input.value = input.value.replace(/\./g, ''); });
    }
</script>
@endsection
