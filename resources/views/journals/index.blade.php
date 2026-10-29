<!-- resources/views/journals/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">
    <h1 class="text-3xl font-bold mb-6 text-slate-800 flex items-center gap-3">
        <i data-feather="dollar-sign" class="w-7 h-7 text-green-600"></i> Mutasi Rekening Bank
    </h1>

    <div class="space-y-6">
        <!-- Header Dropdown Pemilihan Bank -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex flex-col sm:flex-row justify-between items-center gap-4">
            <form action="{{ route('journals.index') }}" method="GET" class="flex items-center gap-3 w-full sm:w-auto" id="bankForm">
                <div class="p-2 bg-blue-100 text-blue-600 rounded-lg"><i data-feather="briefcase" class="w-5 h-5"></i></div>
                <label class="font-semibold text-slate-700 whitespace-nowrap">Pilih Buku Bank:</label>
                <select 
                    name="account_code" 
                    onchange="document.getElementById('bankForm').submit()"
                    class="w-full sm:w-64 border border-slate-300 rounded-lg p-2 outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-medium"
                >
                    <option value="" disabled {{ !$accountCode ? 'selected' : '' }}>-- Pilih Rekening --</option>
                    @foreach($banks as $bank)
                        <option value="{{ $bank->code }}" {{ $accountCode == $bank->code ? 'selected' : '' }}>
                            {{ $bank->name }} ({{ $bank->code }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        @if($accountCode)
            @php 
                $selectedBank = $banks->firstWhere('code', $accountCode);
            @endphp
            
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-300 overflow-x-auto">
                <div class="mb-4">
                    <h2 class="text-xl font-bold font-sans uppercase text-slate-800">{{ $selectedBank->name }}</h2>
                    <p class="text-sm font-semibold mt-1 text-slate-600">Saldo Mutasi Bank - Akun: {{ $selectedBank->code }}</p>
                </div>

                <!-- Form untuk Simpan Mutasi Baru -->
                <form action="{{ route('journals.store') }}" method="POST" id="journalForm">
                    @csrf
                    <input type="hidden" name="reference_no" value="">
                    
                    <table class="w-full text-sm border-collapse border border-slate-400 min-w-[900px]">
                        <thead class="bg-slate-100">
                            <tr>
                                <th class="border border-slate-400 p-2 text-center w-12">No.</th>
                                <th class="border border-slate-400 p-2 text-center w-32">Tanggal</th>
                                <th class="border border-slate-400 p-2 text-center">Uraian / Keterangan</th>
                                <th class="border border-slate-400 p-2 text-center w-24">No. AKUN</th>
                                <th class="border border-slate-400 p-2 text-center w-32">DEBET</th>
                                <th class="border border-slate-400 p-2 text-center w-32">KREDIT</th>
                                <th class="border border-slate-400 p-2 text-center w-36">SISA SALDO</th>
                                <th class="border-y border-slate-400 p-2 w-4 bg-white"></th>
                                <th class="border border-slate-400 p-2 text-center w-40">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $runningBalance = 0; 
                                $rowNumber = 1;
                                // Balik urutan jurnal menjadi dari terlama ke terbaru agar running balance benar (atas ke bawah)
                                $sortedJournals = $journals->sortBy('date')->values();
                            @endphp

                            @forelse($sortedJournals as $journal)
                                @foreach($journal->entries as $entry)
                                    @php 
                                        $isBankRow = ($entry->account_code == $accountCode);
                                        if ($isBankRow) {
                                            $runningBalance += ($entry->debit - $entry->credit);
                                        }
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="border border-slate-400 p-2 text-center">{{ $rowNumber++ }}</td>
                                        <td class="border border-slate-400 p-2 text-center">{{ \Carbon\Carbon::parse($journal->date)->format('d-M-Y') }}</td>
                                        <td class="border border-slate-400 p-2">{{ $journal->description }} {{ $journal->reference_no ? '- '.$journal->reference_no : '' }}</td>
                                        <td class="border border-slate-400 p-2 text-center font-semibold">{{ $entry->account_code }}</td>
                                        <td class="border border-slate-400 p-2 text-right font-medium">
                                            {{ $entry->debit > 0 ? 'Rp ' . number_format($entry->debit, 0, ',', '.') : '' }}
                                        </td>
                                        <td class="border border-slate-400 p-2 text-right font-medium">
                                            {{ $entry->credit > 0 ? 'Rp ' . number_format($entry->credit, 0, ',', '.') : '' }}
                                        </td>
                                        <td class="border border-slate-400 p-2 text-right {{ $isBankRow ? 'font-bold text-blue-600' : '' }}">
                                            @if($isBankRow)
                                                @if($runningBalance < 0)
                                                    <span class="text-red-600">(Rp {{ number_format(abs($runningBalance), 0, ',', '.') }})</span>
                                                @else
                                                    Rp {{ number_format($runningBalance, 0, ',', '.') }}
                                                @endif
                                            @endif
                                        </td>
                                        <td class="border-y border-slate-400 p-2 bg-white"></td>
                                        <td class="border border-slate-400 p-2 text-slate-600 font-medium text-center text-xs">
                                            {{ $entry->account->name ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr><td colspan="9" class="border border-slate-400 p-4 text-center text-slate-500 font-medium">Belum ada riwayat transaksi pada rekening ini.</td></tr>
                            @endforelse

                            <!-- --- FORM BARIS BARU (JURNAL MANUAL) --- -->
                            <tr class="bg-blue-50/30">
                                <td class="border border-slate-400 p-2 text-center text-slate-400 font-bold">{{ $rowNumber }}</td>
                                <td class="border border-slate-400 p-0">
                                    <input type="date" name="date" required class="w-full p-2 outline-none bg-transparent focus:bg-white text-sm text-center">
                                </td>
                                <td class="border border-slate-400 p-0">
                                    <input type="text" name="description" required placeholder="Ketik uraian mutasi..." class="w-full p-2 outline-none bg-transparent focus:bg-white text-sm">
                                </td>
                                <td class="border border-slate-400 p-0">
                                    <!-- Field Lawan Akun -->
                                    <input type="text" id="targetAccount" required placeholder="Misal: 509" class="w-full p-2 outline-none bg-transparent focus:bg-white text-center font-semibold text-sm">
                                </td>
                                <td class="border border-slate-400 p-0">
                                    <input type="number" id="inpDebit" min="0" placeholder="0" class="w-full p-2 outline-none bg-transparent focus:bg-white text-right text-sm" onchange="document.getElementById('inpCredit').value = ''">
                                </td>
                                <td class="border border-slate-400 p-0">
                                    <input type="number" id="inpCredit" min="0" placeholder="0" class="w-full p-2 outline-none bg-transparent focus:bg-white text-right text-sm" onchange="document.getElementById('inpDebit').value = ''">
                                </td>
                                <td class="border border-slate-400 p-2 bg-slate-100"></td>
                                <td class="border-y border-slate-400 p-2 bg-white"></td>
                                <td class="border border-slate-400 p-1 text-center">
                                    <button type="button" onclick="submitJournal()" class="flex items-center justify-center gap-1 w-full bg-blue-600 hover:bg-blue-700 text-white px-2 py-1.5 rounded transition-colors text-xs font-semibold">
                                        <i data-feather="save" class="w-3 h-3"></i> Simpan
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Hidden inputs untuk Controller Double Entry -->
                    <div id="hiddenEntries"></div>
                </form>
            </div>
        @else
            <div class="bg-white p-10 rounded-xl shadow-sm border border-slate-300 text-center">
                <i data-feather="book-open" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                <h3 class="text-lg font-bold text-slate-700">Pilih Rekening Bank</h3>
                <p class="text-slate-500">Silakan pilih rekening pada dropdown di atas untuk melihat riwayat mutasinya.</p>
            </div>
        @endif
    </div>
</div>

<script>
    function submitJournal() {
        const date = document.querySelector('input[name="date"]').value;
        const desc = document.querySelector('input[name="description"]').value;
        const targetAcc = document.getElementById('targetAccount').value;
        const debit = parseFloat(document.getElementById('inpDebit').value) || 0;
        const credit = parseFloat(document.getElementById('inpCredit').value) || 0;

        if (!date || !desc || !targetAcc || (debit === 0 && credit === 0)) {
            alert("Harap lengkapi Tanggal, Uraian, No. Akun lawan, dan nominal (Debit atau Kredit)!");
            return;
        }

        if (debit > 0 && credit > 0) {
            alert("Isi salah satu nominal saja: Debit ATAU Kredit.");
            return;
        }

        // Logic Double-Entry: 
        // Jika form Debit diisi -> Rekening Bank Debit (Bertambah), Akun Lawan Kredit
        // Jika form Kredit diisi -> Rekening Bank Kredit (Berkurang), Akun Lawan Debit
        
        let bankDebit = debit > 0 ? debit : 0;
        let bankCredit = credit > 0 ? credit : 0;
        
        let targetDebit = credit > 0 ? credit : 0;
        let targetCredit = debit > 0 ? debit : 0;

        const bankCode = "{{ $accountCode }}";

        const hiddenDiv = document.getElementById('hiddenEntries');
        hiddenDiv.innerHTML = `
            <!-- Entry 1: Bank -->
            <input type="hidden" name="entries[0][account_code]" value="${bankCode}">
            <input type="hidden" name="entries[0][debit]" value="${bankDebit}">
            <input type="hidden" name="entries[0][credit]" value="${bankCredit}">
            
            <!-- Entry 2: Akun Lawan -->
            <input type="hidden" name="entries[1][account_code]" value="${targetAcc}">
            <input type="hidden" name="entries[1][debit]" value="${targetDebit}">
            <input type="hidden" name="entries[1][credit]" value="${targetCredit}">
        `;

        document.getElementById('journalForm').submit();
    }
</script>
@endsection