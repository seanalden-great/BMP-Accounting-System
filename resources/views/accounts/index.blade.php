{{-- <!-- resources/views/accounts/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="space-y-6 pb-10">
        <!-- HEADER -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                    <i data-feather="book" class="w-7 h-7 text-blue-600"></i> Chart of Accounts
                </h1>
                <p class="text-slate-500 mt-1">Kelola master data akun keuangan</p>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto">
                <!-- Dropdown Laporan Internal & Pajak -->
                <div class="relative group">
                    <button class="bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 text-emerald-700 px-4 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-colors flex items-center gap-2">
                        <i data-feather="printer" class="w-4 h-4"></i> Ekspor Laporan CoA
                    </button>
                    <div class="absolute right-0 mt-1 w-56 bg-white border border-slate-200 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 overflow-hidden">
                        <div class="px-3 py-2 bg-slate-50 border-b font-bold text-xs text-slate-400 uppercase">Lap. Internal (Asli)</div>
                        <a href="{{ route('accounts.export', ['type' => 'internal', 'format' => 'csv']) }}" class="block px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700">Unduh Excel (CSV)</a>
                        <a href="{{ route('accounts.export', ['type' => 'internal', 'format' => 'pdf']) }}" target="_blank" class="block px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700 border-b">Cetak PDF / Layar</a>

                        <div class="px-3 py-2 bg-purple-50 border-b font-bold text-xs text-purple-500 uppercase">Lap. Pajak (Tax)</div>
                        <a href="{{ route('accounts.export', ['type' => 'pajak', 'format' => 'csv']) }}" class="block px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700">Unduh Excel (CSV)</a>
                        <a href="{{ route('accounts.export', ['type' => 'pajak', 'format' => 'pdf']) }}" target="_blank" class="block px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700">Cetak PDF / Layar</a>
                    </div>
                </div>

                <!-- CEK HAK AKSES CREATE (C) -->
                @if(\App\Models\CompanySetting::canAction('C', 'Akun CoA'))
                <button onclick="openModal('create')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
                    <i data-feather="plus" class="w-4 h-4"></i> Tambah Akun
                </button>
                @endif
            </div>
        </div>

        <!-- TABEL COA -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-300 overflow-x-auto">
            <div class="mb-6 text-center sm:text-left">
                <h2 class="text-xl font-bold font-sans">PT. BERLIAN MANDIRI PERKASA</h2>
                <p class="text-lg font-semibold mt-1">Chart of Account</p>
            </div>

            <table class="w-full text-sm border-collapse border border-slate-400 min-w-[900px]">
                <thead class="bg-slate-200">
                    <tr>
                        <th rowspan="2" class="border border-slate-400 p-2 text-center w-24">CoA</th>
                        <th rowspan="2" class="border border-slate-400 p-2 text-center">Nama Account</th>
                        <th rowspan="2" class="border border-slate-400 p-2 text-center w-16">Pos</th>
                        <th colspan="2" class="border border-slate-400 p-2 text-center">Saldo</th>
                        <th rowspan="2" class="border border-slate-400 p-2 text-center w-40">SISA SALDO</th>
                        <th rowspan="2" class="border border-slate-400 p-2 text-center w-24">Aksi</th>
                    </tr>
                    <tr>
                        <th class="border border-slate-400 p-2 text-center w-36 bg-slate-100">D</th>
                        <th class="border border-slate-400 p-2 text-center w-36 bg-slate-100">K</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($response as $acc)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="border border-slate-400 p-2 {{ $acc['isHeader'] ? 'font-bold' : '' }}">
                                {{ $acc['code'] }}
                            </td>
                            <td class="border border-slate-400 p-2 {{ $acc['isHeader'] ? 'font-bold' : '' }}"
                                style="padding-left: {{ ($acc['level'] * 1.5) + 0.5 }}rem">
                                {{ $acc['name'] }}
                            </td>
                            <td class="border border-slate-400 p-2 text-center font-medium">{{ $acc['pos'] }}</td>
                            <td class="border border-slate-400 p-2 text-right">
                                {{ isset($acc['debit']) && $acc['debit'] > 0 ? 'Rp ' . number_format($acc['debit'], 0, ',', '.') : '' }}
                            </td>
                            <td class="border border-slate-400 p-2 text-right">
                                {{ isset($acc['kredit']) && $acc['kredit'] > 0 ? 'Rp ' . number_format($acc['kredit'], 0, ',', '.') : '' }}
                            </td>
                            <td
                                class="border border-slate-400 p-2 text-right
                                {{ $acc['sisa'] > 0 ? 'font-semibold text-blue-700' : ($acc['sisa'] < 0 ? 'font-bold text-red-600' : '') }}">
                                @if($acc['sisa'] > 0)
                                    Rp {{ number_format($acc['sisa'], 0, ',', '.') }}
                                @elseif($acc['sisa'] < 0)
                                    (Rp {{ number_format(abs($acc['sisa']), 0, ',', '.') }})
                                @endif
                            </td>
                            <td class="border border-slate-400 p-2 text-center">
                                <div class="flex items-center justify-center gap-2">

                                    <!-- CEK HAK AKSES UPDATE (U) -->
                                    @if(\App\Models\CompanySetting::canAction('U', 'Akun CoA'))
                                    <!-- Passing data akun sebagai JSON object ke JS -->
                                    <button onclick="openModal('edit', {{ json_encode($acc) }})"
                                        class="text-slate-400 hover:text-blue-600 transition-colors" title="Edit Akun">
                                        <i data-feather="edit" class="w-4 h-4"></i>
                                    </button>
                                    @endif

                                    <!-- CEK HAK AKSES DELETE (D) -->
                                    @if(\App\Models\CompanySetting::canAction('D', 'Akun CoA'))
                                    <form action="{{ route('accounts.destroy', $acc['code']) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Yakin ingin menghapus akun {{ $acc['code'] }}? Pastikan belum ada transaksi di dalamnya.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-slate-400 hover:text-red-600 transition-colors"
                                            title="Hapus Akun">
                                            <i data-feather="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center p-8 text-slate-500">Belum ada data akun. Silakan tambah data.
                            </td>
                        </tr>
                    @endforelse

                    <!-- BARIS TOTAL NERACA SALDO -->
                    @if(count($response) > 0)
                            @php
                                $isBalanced = (round($grandTotalDebit, 2) === round($grandTotalKredit, 2));
                                $selisih = abs($grandTotalDebit - $grandTotalKredit);
                            @endphp
                        <tfoot class="bg-slate-800 text-white font-bold">
                            <tr>
                                <td colspan="3" class="border border-slate-500 p-3 text-right uppercase tracking-wider">
                                    TOTAL NERACA SALDO (MUTASI DETAIL)
                                </td>
                                <td class="border border-slate-500 p-3 text-right text-emerald-300">
                                    Rp {{ number_format($grandTotalDebit, 0, ',', '.') }}
                                </td>
                                <td class="border border-slate-500 p-3 text-right text-emerald-300">
                                    Rp {{ number_format($grandTotalKredit, 0, ',', '.') }}
                                </td>
                                <td class="border border-slate-500 p-3 text-center">
                                    @if($isBalanced)
                                        <span
                                            class="px-2.5 py-1 bg-emerald-600 text-white rounded text-xs font-extrabold tracking-wide">
                                            SEIMBANG (BALANCE)
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 bg-red-600 text-white rounded text-xs font-extrabold"
                                            title="Selisih: Rp {{ number_format($selisih, 0, ',', '.') }}">
                                            TIDAK SEIMBANG
                                        </span>
                                    @endif
                                </td>
                                <td class="border border-slate-500 p-3"></td>
                            </tr>
                        </tfoot>
                    @endif
            </table>
        </div>

        <!-- MODAL TAMBAH/EDIT AKUN -->
        <div id="accountModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-lg w-full max-w-md overflow-hidden flex flex-col max-h-[90vh]">
                <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                    <h3 id="modalTitle" class="text-lg font-bold text-slate-800">Tambah Akun Baru</h3>
                    <button onclick="closeModal()" class="text-slate-400 hover:text-slate-700 transition-colors">
                        <i data-feather="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form id="accountForm" method="POST" action="{{ route('accounts.store') }}"
                    class="p-5 overflow-y-auto custom-scrollbar">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Kode Akun</label>
                            <input type="text" name="code" id="inputCode" required placeholder="Misal: 115"
                                class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm disabled:bg-slate-100 disabled:text-slate-500">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Akun</label>
                            <input type="text" name="name" id="inputName" required placeholder="Misal: Bank BNI"
                                class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>

                        <div class="flex items-center gap-2 mt-2">
                            <!-- Perlu trik hidden input karena checkbox yg tidak dicentang tidak mengirim value '0' secara otomatis -->
                            <input type="hidden" name="is_header" value="0">
                            <input type="checkbox" name="is_header" id="inputIsHeader" value="1"
                                class="w-4 h-4 text-blue-600 rounded border-gray-300">
                            <label for="inputIsHeader" class="text-sm font-medium text-slate-700 cursor-pointer">
                                Akun Induk (Header)
                            </label>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Pos Saldo</label>
                                <select name="normal_balance" id="inputPos"
                                    class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                    <option value="-">- (Netral)</option>
                                    <option value="D">D (Debit)</option>
                                    <option value="K">K (Kredit)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Level Indentasi</label>
                                <input type="number" name="level" id="inputLevel" min="0" max="10" value="0"
                                    class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" onclick="closeModal()"
                            class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg text-sm font-medium transition-colors">Batal</button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors">Simpan
                            Akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal(mode, acc = null) {
            const modal = document.getElementById('accountModal');
            const form = document.getElementById('accountForm');

            if (mode === 'edit') {
                document.getElementById('modalTitle').innerText = "Edit Akun";
                document.getElementById('formMethod').value = "PUT";
                form.action = `/accounts/${acc.code}`;

                // Isi form
                document.getElementById('inputCode').value = acc.code;
                document.getElementById('inputCode').readOnly = true;

                document.getElementById('inputName').value = acc.name;
                document.getElementById('inputIsHeader').checked = acc.isHeader;
                document.getElementById('inputPos').value = acc.pos;
                document.getElementById('inputLevel').value = acc.level;
            } else {
                document.getElementById('modalTitle').innerText = "Tambah Akun Baru";
                document.getElementById('formMethod').value = "POST";
                form.action = "{{ route('accounts.store') }}";

                form.reset();
                document.getElementById('inputCode').readOnly = false;
            }

            modal.classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('accountModal').classList.add('hidden');
        }
    </script>
@endsection --}}

@extends('layouts.app')

@section('content')
    <div class="space-y-6 pb-10 relative">

        <!-- HEADER -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                    <i data-feather="book" class="w-7 h-7 text-blue-600"></i> Chart of Accounts
                </h1>
                <p class="text-slate-500 mt-1">Kelola master data akun keuangan</p>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto">
                <!-- Dropdown Laporan Internal & Pajak -->
                <div class="relative group z-20">
                    <button class="bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 text-emerald-700 px-4 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-colors flex items-center gap-2">
                        <i data-feather="printer" class="w-4 h-4"></i> Ekspor Laporan CoA
                    </button>
                    <div class="absolute right-0 mt-1 w-56 bg-white border border-slate-200 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all overflow-hidden">
                        <div class="px-3 py-2 bg-slate-50 border-b font-bold text-xs text-slate-400 uppercase">Lap. Internal (Asli)</div>
                        <a href="{{ route('accounts.export', ['type' => 'internal', 'format' => 'csv']) }}" class="block px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700">Unduh Excel (CSV)</a>
                        <a href="{{ route('accounts.export', ['type' => 'internal', 'format' => 'pdf']) }}" target="_blank" class="block px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700 border-b">Cetak PDF / Layar</a>

                        <div class="px-3 py-2 bg-purple-50 border-b font-bold text-xs text-purple-500 uppercase">Lap. Pajak (Tax)</div>
                        <a href="{{ route('accounts.export', ['type' => 'pajak', 'format' => 'csv']) }}" class="block px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700">Unduh Excel (CSV)</a>
                        <a href="{{ route('accounts.export', ['type' => 'pajak', 'format' => 'pdf']) }}" target="_blank" class="block px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700">Cetak PDF / Layar</a>
                    </div>
                </div>

                <!-- CEK HAK AKSES CREATE (C) -->
                @if(\App\Models\CompanySetting::canAction('C', 'Akun CoA'))
                <button onclick="openModal('create')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-bold shadow-sm transition-colors flex items-center gap-2">
                    <i data-feather="plus" class="w-4 h-4"></i> Tambah Akun
                </button>
                @endif
            </div>
        </div>

        <!-- TABEL COA -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-300 overflow-x-auto">
            <div class="mb-6 text-center sm:text-left">
                <h2 class="text-xl font-bold font-sans">PT. BERLIAN MANDIRI PERKASA</h2>
                <p class="text-lg font-semibold mt-1">Chart of Account</p>
            </div>

            <table class="w-full text-sm border-collapse border border-slate-400 min-w-[900px]">
                <thead class="bg-slate-200">
                    <tr>
                        <th rowspan="2" class="border border-slate-400 p-2 text-center w-24">CoA</th>
                        <th rowspan="2" class="border border-slate-400 p-2 text-center">Nama Account</th>
                        <th rowspan="2" class="border border-slate-400 p-2 text-center w-16">Pos</th>
                        <th colspan="2" class="border border-slate-400 p-2 text-center">Saldo</th>
                        <th rowspan="2" class="border border-slate-400 p-2 text-center w-40">SISA SALDO</th>
                        <th rowspan="2" class="border border-slate-400 p-2 text-center w-24">Aksi</th>
                    </tr>
                    <tr>
                        <th class="border border-slate-400 p-2 text-center w-36 bg-slate-100">D</th>
                        <th class="border border-slate-400 p-2 text-center w-36 bg-slate-100">K</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($response as $acc)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="border border-slate-400 p-2 {{ $acc['isHeader'] ? 'font-bold' : '' }}">
                                {{ $acc['code'] }}
                            </td>
                            <td class="border border-slate-400 p-2 {{ $acc['isHeader'] ? 'font-bold' : '' }}"
                                style="padding-left: {{ ($acc['level'] * 1.5) + 0.5 }}rem">
                                {{ $acc['name'] }}
                            </td>
                            <td class="border border-slate-400 p-2 text-center font-medium">{{ $acc['pos'] }}</td>
                            <td class="border border-slate-400 p-2 text-right font-medium text-slate-700">
                                {{ isset($acc['debit']) && $acc['debit'] > 0 ? 'Rp ' . number_format($acc['debit'], 0, ',', '.') : '' }}
                            </td>
                            <td class="border border-slate-400 p-2 text-right font-medium text-slate-700">
                                {{ isset($acc['kredit']) && $acc['kredit'] > 0 ? 'Rp ' . number_format($acc['kredit'], 0, ',', '.') : '' }}
                            </td>
                            <td class="border border-slate-400 p-2 text-right {{ $acc['sisa'] > 0 ? 'font-black text-blue-700' : ($acc['sisa'] < 0 ? 'font-black text-red-600' : 'font-bold') }}">
                                @if($acc['sisa'] > 0)
                                    Rp {{ number_format($acc['sisa'], 0, ',', '.') }}
                                @elseif($acc['sisa'] < 0)
                                    (Rp {{ number_format(abs($acc['sisa']), 0, ',', '.') }})
                                @else
                                    -
                                @endif
                            </td>
                            <td class="border border-slate-400 p-2 text-center">
                                <div class="flex items-center justify-center gap-2">

                                    <!-- CEK HAK AKSES UPDATE (U) -->
                                    @if(\App\Models\CompanySetting::canAction('U', 'Akun CoA'))
                                    <!-- Passing data akun sebagai JSON object ke JS -->
                                    <button onclick="openModal('edit', {{ json_encode($acc) }})"
                                        class="text-blue-600 hover:bg-blue-100 p-2 rounded-lg transition-colors border border-transparent hover:border-blue-200 shadow-sm" title="Edit Akun">
                                        <i data-feather="edit" class="w-4 h-4"></i>
                                    </button>
                                    @endif

                                    <!-- CEK HAK AKSES DELETE (D) DENGAN SWAL -->
                                    @if(\App\Models\CompanySetting::canAction('D', 'Akun CoA'))
                                    <form action="{{ route('accounts.destroy', $acc['code']) }}" method="POST" class="inline"
                                        onsubmit="confirmDelete(event, this, '{{ $acc['code'] }}', '{{ addslashes($acc['name']) }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:bg-red-100 p-2 rounded-lg transition-colors border border-transparent hover:border-red-200 shadow-sm"
                                            title="Hapus Akun">
                                            <i data-feather="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center p-8 text-slate-500 font-medium">Belum ada data akun. Silakan tambah data.
                            </td>
                        </tr>
                    @endforelse

                    <!-- BARIS TOTAL NERACA SALDO -->
                    @if(count($response) > 0)
                            @php
                                $isBalanced = (round($grandTotalDebit, 2) === round($grandTotalKredit, 2));
                                $selisih = abs($grandTotalDebit - $grandTotalKredit);
                            @endphp
                        <tfoot class="bg-slate-800 text-white font-bold border-t-4 border-slate-600">
                            <tr>
                                <td colspan="3" class="border border-slate-600 p-4 text-right uppercase tracking-wider">
                                    TOTAL NERACA SALDO (MUTASI DETAIL)
                                </td>
                                <td class="border border-slate-600 p-4 text-right text-emerald-400 text-lg">
                                    Rp {{ number_format($grandTotalDebit, 0, ',', '.') }}
                                </td>
                                <td class="border border-slate-600 p-4 text-right text-emerald-400 text-lg">
                                    Rp {{ number_format($grandTotalKredit, 0, ',', '.') }}
                                </td>
                                <td class="border border-slate-600 p-4 text-center">
                                    @if($isBalanced)
                                        <span class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-sm font-extrabold tracking-wide shadow-sm">
                                            SEIMBANG (BALANCE)
                                        </span>
                                    @else
                                        <span class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-sm font-extrabold shadow-sm"
                                            title="Selisih: Rp {{ number_format($selisih, 0, ',', '.') }}">
                                            TIDAK SEIMBANG
                                        </span>
                                    @endif
                                </td>
                                <td class="border border-slate-600 p-3"></td>
                            </tr>
                        </tfoot>
                    @endif
            </table>
        </div>

        <!-- MODAL TAMBAH/EDIT AKUN (BERANIMASI & SWAL) -->
        <div id="accountModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 backdrop-blur-sm transition-all">
            <div id="accountModalContent" class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col max-h-[90vh] border-t-4 border-blue-600 transform transition-transform scale-95 origin-center">
                <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                    <h3 id="modalTitle" class="text-lg font-bold text-slate-800">Tambah Akun Baru</h3>
                    <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-red-500 bg-white rounded-full p-1 shadow-sm transition-colors">
                        <i data-feather="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form id="accountForm" method="POST" action="{{ route('accounts.store') }}"
                    class="p-6 overflow-y-auto custom-scrollbar">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">

                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Kode Akun (CoA)</label>
                            <input type="text" name="code" id="inputCode" required placeholder="Misal: 115"
                                class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500 font-mono text-slate-800 bg-slate-50 disabled:bg-slate-200 disabled:text-slate-500 disabled:cursor-not-allowed transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Akun</label>
                            <input type="text" name="name" id="inputName" required placeholder="Misal: Bank BNI"
                                class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500 font-bold text-slate-800 transition-all">
                        </div>

                        <div class="flex items-center gap-2 mt-2 p-3 bg-slate-50 rounded-lg border border-slate-200">
                            <!-- Perlu trik hidden input karena checkbox yg tidak dicentang tidak mengirim value '0' secara otomatis -->
                            <input type="hidden" name="is_header" value="0">
                            <input type="checkbox" name="is_header" id="inputIsHeader" value="1"
                                class="w-5 h-5 text-blue-600 rounded border-gray-300 cursor-pointer focus:ring-blue-500 transition-colors">
                            <label for="inputIsHeader" class="text-sm font-bold text-slate-700 cursor-pointer">
                                Jadikan Sebagai Akun Induk (Header)
                            </label>
                        </div>

                        <div class="grid grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Pos Saldo</label>
                                <select name="normal_balance" id="inputPos"
                                    class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500 font-bold text-slate-700 transition-all">
                                    <option value="-">- (Netral)</option>
                                    <option value="D">D (Debit)</option>
                                    <option value="K">K (Kredit)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Level Indentasi</label>
                                <input type="number" name="level" id="inputLevel" min="0" max="10" value="0"
                                    class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500 font-bold text-center text-slate-700 transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3 border-t border-slate-100 pt-5">
                        <button type="button" onclick="closeModal()"
                            class="px-5 py-2.5 text-slate-600 hover:bg-slate-100 rounded-lg font-bold transition-colors">Batal</button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-bold shadow-md flex items-center gap-2 transition-colors">
                            <i data-feather="save" class="w-4 h-4"></i> Simpan Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- PENGENDALI JAVASCRIPT & SWEETALERT2 -->
    <script>
        // --- SWEETALERT2: PESAN GLOBAL (SESSION) ---
        document.addEventListener("DOMContentLoaded", function() {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    timer: 3500,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    position: 'center'
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Aksi Gagal!',
                    text: '{{ session('error') }}',
                    timer: 5000,
                    timerProgressBar: true,
                    showConfirmButton: true,
                    confirmButtonColor: '#ef4444'
                });
            @endif
        });

        // --- SWEETALERT2: KONFIRMASI HAPUS AKUN COA ---
        function confirmDelete(event, form, accountCode, accountName) {
            event.preventDefault();

            Swal.fire({
                title: 'Hapus Akun CoA?',
                html: `Anda akan menghapus Akun <b>[${accountCode}] ${accountName}</b> secara permanen.<br><br><span class="text-sm text-red-500 font-medium">Perhatian: Pastikan belum ada satupun transaksi Buku Besar yang menggunakan akun ini! Jika ada, penghapusan akan dicekal oleh sistem.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', // Merah
                cancelButtonColor: '#64748b',  // Slate
                confirmButtonText: '<i data-feather="trash-2" class="w-4 h-4 inline mr-1"></i> Ya, Hapus Akun',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                didOpen: () => { if (typeof feather !== 'undefined') feather.replace(); }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Mengecek & Menghapus...',
                        text: 'Sistem sedang memverifikasi relasi transaksi.',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    form.submit();
                }
            });
        }

        // --- SWEETALERT2: ANIMASI LOADING SAAT SUBMIT FORM ---
        document.getElementById('accountForm').addEventListener('submit', function() {
            Swal.fire({
                title: 'Menyimpan Akun...',
                text: 'Mendaftarkan pengaturan Chart of Accounts Anda ke database.',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); }
            });
        });

        // --- LOGIKA MODAL FORM (ANIMASI CSS SCALE) ---
        function openModal(mode, acc = null) {
            const modal = document.getElementById('accountModal');
            const form = document.getElementById('accountForm');
            const content = document.getElementById('accountModalContent');

            if (mode === 'edit') {
                document.getElementById('modalTitle').innerText = "Edit Data Akun";
                document.getElementById('formMethod').value = "PUT";
                form.action = `/accounts/${acc.code}`;

                // Isi form
                document.getElementById('inputCode').value = acc.code;
                document.getElementById('inputCode').readOnly = true;

                document.getElementById('inputName').value = acc.name;
                document.getElementById('inputIsHeader').checked = acc.isHeader;
                document.getElementById('inputPos').value = acc.pos;
                document.getElementById('inputLevel').value = acc.level;
            } else {
                document.getElementById('modalTitle').innerText = "Tambah Akun CoA Baru";
                document.getElementById('formMethod').value = "POST";
                form.action = "{{ route('accounts.store') }}";

                form.reset();
                document.getElementById('inputCode').readOnly = false;
            }

            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }, 10);
        }

        function closeModal() {
            const modal = document.getElementById('accountModal');
            const content = document.getElementById('accountModalContent');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 150);
        }
    </script>
@endsection
