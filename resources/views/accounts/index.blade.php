<!-- resources/views/accounts/index.blade.php -->
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
            <button onclick="openModal('create')"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
                <i data-feather="plus" class="w-4 h-4"></i> Tambah Akun Baru
            </button>
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
                                    <!-- Passing data akun sebagai JSON object ke JS -->
                                    <button onclick="openModal('edit', {{ json_encode($acc) }})"
                                        class="text-slate-400 hover:text-blue-600 transition-colors" title="Edit Akun">
                                        <i data-feather="edit" class="w-4 h-4"></i>
                                    </button>
                                    <form action="{{ route('accounts.destroy', $acc['code']) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Yakin ingin menghapus akun {{ $acc['code'] }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-slate-400 hover:text-red-600 transition-colors"
                                            title="Hapus Akun">
                                            <i data-feather="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
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
@endsection