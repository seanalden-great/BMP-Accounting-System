{{-- <!-- resources/views/expenses/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="space-y-6 pb-10">
        <div class="flex justify-between items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                    <i data-feather="file-text" class="text-orange-600 w-7 h-7"></i> Pengeluaran Biaya
                </h1>
                <p class="text-slate-500 mt-1">Catat biaya operasional dan otomatis bayar dari Kas/Bank</p>
            </div>
            <button onclick="document.getElementById('expModal').classList.remove('hidden')"
                class="bg-orange-600 hover:bg-orange-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
                <i data-feather="plus" class="w-4 h-4"></i> Catat Biaya
            </button>
        </div>

        <!-- Tabel Daftar Biaya -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-white border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600">Tanggal</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Akun Biaya</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Bayar Dari</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Deskripsi</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Nominal (Rp)</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $exp)
                        <tr class="border-b border-slate-50 hover:bg-slate-50">
                            <td class="p-4 text-sm">{{ \Carbon\Carbon::parse($exp->date)->format('d/m/Y') }}</td>
                            <td class="p-4 text-sm font-bold text-slate-700">[{{ $exp->account->code }}]
                                {{ $exp->account->name }}</td>
                            <td class="p-4 text-sm text-slate-600">{{ $exp->paymentAccount->name }}</td>
                            <td class="p-4 text-sm text-slate-800">{{ $exp->description }} <br /><span
                                    class="text-xs text-slate-400">{{ $exp->reference_no }}</span></td>
                            <td class="p-4 text-sm font-bold text-red-600 text-right">Rp
                                {{ number_format($exp->amount, 0, ',', '.') }}</td>
                            <td class="p-4 text-center">
                                <form action="{{ route('expenses.destroy', $exp->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus catatan biaya ini? (Jurnal akuntansi tidak akan terhapus otomatis)');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-500"><i data-feather="trash-2"
                                            class="w-4 h-4"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500">Belum ada catatan biaya.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Modal Form Catat Biaya -->
        <div id="expModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col">
                <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-xl font-bold text-slate-800">Catat Pengeluaran Baru</h3>
                    <button onclick="document.getElementById('expModal').classList.add('hidden')"
                        class="text-slate-400 hover:text-slate-700">
                        <i data-feather="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form action="{{ route('expenses.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal</label>
                            <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                                class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nomor Referensi
                                (Opsional)</label>
                            <input type="text" name="reference_no" placeholder="Cth: INV-PLN-12"
                                class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 bg-orange-50 p-4 rounded-lg border border-orange-100">
                        <div>
                            <label class="block text-sm font-bold text-orange-900 mb-1">Akun Biaya (Debit)</label>
                            <select name="account_code" required
                                class="w-full border border-orange-200 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="" disabled selected>-- Pilih Beban --</option>
                                @foreach($expenseAccounts as $a)
                                <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option> @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Bayar Dari Akun (Kredit)</label>
                            <select name="payment_code" required
                                class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="" disabled selected>-- Pilih Kas/Bank --</option>
                                @foreach($paymentAccounts as $a)
                                <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option> @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Total Nominal Pengeluaran</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-slate-500">Rp</span>
                            <input type="number" name="amount" required min="1"
                                class="w-full pl-10 pr-4 py-3 border-2 border-slate-300 rounded-lg font-bold text-lg outline-none focus:border-orange-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Deskripsi / Keterangan
                            Singkat</label>
                        <textarea name="description" required rows="2"
                            placeholder="Cth: Pembayaran token listrik bulan Agustus"
                            class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500 resize-none"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" onclick="document.getElementById('expModal').classList.add('hidden')"
                            class="px-5 py-2 text-slate-600 hover:bg-slate-100 rounded-lg font-bold">Batal</button>
                        <button type="submit"
                            class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2.5 rounded-lg font-bold flex items-center gap-2">
                            Simpan & Buat Jurnal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection --}}

{{-- <!-- resources/views/expenses/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="space-y-6 pb-10">
        <div class="flex justify-between items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                    <i data-feather="file-text" class="text-orange-600 w-7 h-7"></i> Pengeluaran Biaya
                </h1>
                <p class="text-slate-500 mt-1">Catat biaya operasional dan otomatis bayar dari Kas/Bank</p>
            </div>
            <button onclick="document.getElementById('expModal').classList.remove('hidden')"
                class="bg-orange-600 hover:bg-orange-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
                <i data-feather="plus" class="w-4 h-4"></i> Catat Biaya
            </button>
        </div>

        <!-- Tabel Daftar Biaya -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-white border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600">Tanggal</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Akun Biaya</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Bayar Dari</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Deskripsi</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Nominal (Rp)</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $exp)
                        <tr class="border-b border-slate-50 hover:bg-slate-50">
                            <td class="p-4 text-sm">{{ \Carbon\Carbon::parse($exp->date)->format('d/m/Y') }}</td>
                            <td class="p-4 text-sm font-bold text-slate-700">[{{ $exp->account->code }}]
                                {{ $exp->account->name }}</td>
                            <td class="p-4 text-sm text-slate-600">{{ $exp->paymentAccount->name }}</td>
                            <td class="p-4 text-sm text-slate-800">{{ $exp->description }} <br /><span
                                    class="text-xs text-slate-400">{{ $exp->reference_no }}</span></td>
                            <td class="p-4 text-sm font-bold text-red-600 text-right">Rp
                                {{ number_format($exp->amount, 0, ',', '.') }}</td>
                            <td class="p-4 text-center">
                                <form action="{{ route('expenses.destroy', $exp->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus catatan biaya ini? (Jurnal akuntansi tidak akan terhapus otomatis)');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-500"><i data-feather="trash-2"
                                            class="w-4 h-4"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500">Belum ada catatan biaya.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Modal Form Catat Biaya -->
        <div id="expModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col">
                <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-xl font-bold text-slate-800">Catat Pengeluaran Baru</h3>
                    <button type="button" onclick="document.getElementById('expModal').classList.add('hidden')"
                        class="text-slate-400 hover:text-slate-700">
                        <i data-feather="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form action="{{ route('expenses.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal</label>
                            <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                                class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nomor Referensi (Opsional)</label>
                            <input type="text" name="reference_no" placeholder="Cth: INV-PLN-12"
                                class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 bg-orange-50 p-4 rounded-lg border border-orange-100">
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-sm font-bold text-orange-900">Akun Biaya (Debit)</label>
                                <!-- Tombol Panggil Modal Akun -->
                                <button type="button" onclick="openAccountModal()" class="text-xs font-bold text-orange-600 hover:text-orange-800 hover:underline flex items-center gap-1">
                                    <i data-feather="plus" class="w-3 h-3"></i> Tambah Baru
                                </button>
                            </div>
                            <select name="account_code" id="expenseAccountDropdown" required
                                class="w-full border border-orange-200 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="" disabled selected>-- Pilih Beban --</option>
                                @foreach($expenseAccounts as $a)
                                    <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Bayar Dari Akun (Kredit)</label>
                            <select name="payment_code" required
                                class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="" disabled selected>-- Pilih Kas/Bank --</option>
                                @foreach($paymentAccounts as $a)
                                    <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Total Nominal Pengeluaran</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-slate-500">Rp</span>
                            <input type="number" name="amount" required min="1"
                                class="w-full pl-10 pr-4 py-3 border-2 border-slate-300 rounded-lg font-bold text-lg outline-none focus:border-orange-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Deskripsi / Keterangan Singkat</label>
                        <textarea name="description" required rows="2"
                            placeholder="Cth: Pembayaran token listrik bulan Agustus"
                            class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500 resize-none"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" onclick="document.getElementById('expModal').classList.add('hidden')"
                            class="px-5 py-2 text-slate-600 hover:bg-slate-100 rounded-lg font-bold">Batal</button>
                        <button type="submit"
                            class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2.5 rounded-lg font-bold flex items-center gap-2">
                            Simpan & Buat Jurnal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Mini: Tambah Akun CoA Cepat -->
    <div id="quickAccountModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-[60] p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col">
            <div class="flex justify-between items-center p-4 border-b border-slate-200 bg-slate-50">
                <h3 class="font-bold text-slate-800">Tambah Akun CoA (Biaya)</h3>
                <button type="button" onclick="closeAccountModal()" class="text-slate-400 hover:text-slate-700"><i data-feather="x" class="w-4 h-4"></i></button>
            </div>
            <form id="quickAccountForm" onsubmit="submitQuickAccount(event)" class="p-5 space-y-4">

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Kode Akun</label>
                    <input type="text" name="code" required placeholder="Misal: 706" class="w-full border border-slate-300 rounded-lg p-2 text-sm outline-none focus:ring-2 focus:ring-orange-500">
                    <p class="text-xs text-slate-500 mt-1">*Awali dengan angka 7 untuk kategori Biaya Operasional</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Akun Biaya</label>
                    <input type="text" name="name" required placeholder="Misal: Biaya Langganan Software" class="w-full border border-slate-300 rounded-lg p-2 text-sm outline-none focus:ring-2 focus:ring-orange-500">
                </div>

                <div class="flex items-center gap-2 mt-2 hidden">
                    <!-- Default disembunyikan dan di-set false karena ini untuk input beban langsung, bukan header -->
                    <input type="hidden" name="is_header" value="0">
                    <input type="checkbox" name="is_header" value="1" class="w-4 h-4 text-orange-600 rounded border-gray-300">
                </div>

                <div class="grid grid-cols-2 gap-4 hidden">
                    <!-- Default disembunyikan dan di-set Debit/Level 1 karena ini spesifik akun biaya -->
                    <div>
                        <select name="normal_balance" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none text-sm">
                            <option value="D" selected>D (Debit)</option>
                        </select>
                    </div>
                    <div>
                        <input type="number" name="level" min="0" max="10" value="1" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none text-sm">
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" onclick="closeAccountModal()" class="px-4 py-2 text-slate-600 font-bold text-sm bg-slate-100 rounded-lg hover:bg-slate-200">Batal</button>
                    <button type="submit" id="btnSaveAccount" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg font-bold text-sm">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // FUNGSI UNTUK MODAL AKUN CEPAT (AJAX)
        function openAccountModal() {
            document.getElementById('quickAccountModal').classList.remove('hidden');
        }

        function closeAccountModal() {
            document.getElementById('quickAccountModal').classList.add('hidden');
            document.getElementById('quickAccountForm').reset();
        }

        async function submitQuickAccount(e) {
            e.preventDefault();

            const form = document.getElementById('quickAccountForm');
            const btn = document.getElementById('btnSaveAccount');
            const formData = new FormData(form);

            btn.disabled = true;
            btn.innerText = 'Menyimpan...';

            try {
                // Memanggil route bawaan AccountController@store, ubah path ke /accounts jika Mixed Content terjadi
                const response = await fetch("/accounts", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    // Buat elemen <option> baru
                    const newOption = document.createElement("option");
                    newOption.value = result.account.code;
                    newOption.text = `[${result.account.code}] ${result.account.name}`;

                    // Tambahkan ke dropdown beban dan otomatis pilih
                    const dropdown = document.getElementById('expenseAccountDropdown');
                    dropdown.add(newOption);
                    dropdown.value = result.account.code;

                    closeAccountModal();
                    alert('Akun Biaya berhasil ditambahkan!');
                } else {
                    alert(result.message || 'Gagal menambahkan akun.');
                }
            } catch (error) {
                alert('Terjadi kesalahan jaringan.');
            } finally {
                btn.disabled = false;
                btn.innerText = 'Simpan Akun';
            }
        }
    </script>
@endsection --}}

{{-- <!-- resources/views/expenses/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="space-y-6 pb-10">
        <div class="flex justify-between items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                    <i data-feather="file-text" class="text-orange-600 w-7 h-7"></i> Pengeluaran Biaya
                </h1>
                <p class="text-slate-500 mt-1">Catat biaya operasional dan otomatis bayar dari Kas/Bank</p>
            </div>
            <button onclick="document.getElementById('expModal').classList.remove('hidden')"
                class="bg-orange-600 hover:bg-orange-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
                <i data-feather="plus" class="w-4 h-4"></i> Catat Biaya
            </button>
        </div>

        <!-- Tabel Daftar Biaya -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-white border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600">Tanggal</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Akun Biaya</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Bayar Dari</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Deskripsi</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Nominal (Rp)</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $exp)
                        <tr class="border-b border-slate-50 hover:bg-slate-50">
                            <td class="p-4 text-sm">{{ \Carbon\Carbon::parse($exp->date)->format('d/m/Y') }}</td>
                            <td class="p-4 text-sm font-bold text-slate-700">[{{ $exp->account->code }}]
                                {{ $exp->account->name }}</td>
                            <td class="p-4 text-sm text-slate-600">{{ $exp->paymentAccount->name }}</td>
                            <td class="p-4 text-sm text-slate-800">{{ $exp->description }} <br /><span
                                    class="text-xs text-slate-400">{{ $exp->reference_no }}</span></td>
                            <td class="p-4 text-sm font-bold text-red-600 text-right">Rp
                                {{ number_format($exp->amount, 0, ',', '.') }}</td>
                            <td class="p-4 text-center">
                                <form action="{{ route('expenses.destroy', $exp->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus catatan biaya ini? (Jurnal akuntansi tidak akan terhapus otomatis)');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-500"><i data-feather="trash-2"
                                            class="w-4 h-4"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500">Belum ada catatan biaya.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Modal Form Catat Biaya -->
        <div id="expModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col">
                <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-xl font-bold text-slate-800">Catat Pengeluaran Baru</h3>
                    <button type="button" onclick="document.getElementById('expModal').classList.add('hidden')"
                        class="text-slate-400 hover:text-slate-700">
                        <i data-feather="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form action="{{ route('expenses.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal</label>
                            <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                                class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nomor Referensi (Opsional)</label>
                            <input type="text" name="reference_no" placeholder="Cth: INV-PLN-12"
                                class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>

                    <div class="mt-4 border-t border-slate-200 pt-4">
                        <!-- Posisi tombol dipindah ke posisi general di atas kotak Jurnal -->
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-bold text-slate-700">Jurnal Akuntansi</label>
                            <button type="button" onclick="openAccountModal()" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1 bg-blue-50 px-2 py-1 rounded">
                                <i data-feather="plus" class="w-3 h-3"></i> Tambah Akun CoA Baru
                            </button>
                        </div>

                        <div class="grid grid-cols-2 gap-4 bg-orange-50 p-4 rounded-lg border border-orange-100">
                            <div>
                                <label class="block text-sm font-bold text-orange-900 mb-1">Akun Biaya (Debit)</label>
                                <select name="account_code" id="expenseAccountDropdown" required
                                    class="w-full border border-orange-200 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="" disabled selected>-- Pilih Beban --</option>
                                    @foreach($expenseAccounts as $a)
                                        <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Bayar Dari Akun (Kredit)</label>
                                <select name="payment_code" id="paymentAccountDropdown" required
                                    class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="" disabled selected>-- Pilih Kas/Bank --</option>
                                    @foreach($paymentAccounts as $a)
                                        <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Total Nominal Pengeluaran</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-slate-500">Rp</span>
                            <input type="number" name="amount" required min="1"
                                class="w-full pl-10 pr-4 py-3 border-2 border-slate-300 rounded-lg font-bold text-lg outline-none focus:border-orange-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Deskripsi / Keterangan Singkat</label>
                        <textarea name="description" required rows="2"
                            placeholder="Cth: Pembayaran token listrik bulan Agustus"
                            class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500 resize-none"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" onclick="document.getElementById('expModal').classList.add('hidden')"
                            class="px-5 py-2 text-slate-600 hover:bg-slate-100 rounded-lg font-bold">Batal</button>
                        <button type="submit"
                            class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2.5 rounded-lg font-bold flex items-center gap-2">
                            Simpan & Buat Jurnal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Mini: Tambah Akun CoA Cepat (Sama Persis dengan Modal Utama CoA) -->
    <div id="quickAccountModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-[60] p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 class="font-bold text-slate-800">Tambah Akun CoA Baru</h3>
                <button type="button" onclick="closeAccountModal()" class="text-slate-400 hover:text-slate-700 transition-colors"><i data-feather="x" class="w-5 h-5"></i></button>
            </div>

            <form id="quickAccountForm" onsubmit="submitQuickAccount(event)" class="p-5 space-y-4">

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Kode Akun</label>
                    <input type="text" name="code" required placeholder="Misal: 706 atau 115" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Akun</label>
                    <input type="text" name="name" required placeholder="Misal: Biaya Langganan / Bank Jago" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <div class="flex items-center gap-2 mt-2">
                    <input type="hidden" name="is_header" value="0">
                    <input type="checkbox" name="is_header" value="1" class="w-4 h-4 text-blue-600 rounded border-gray-300">
                    <label class="text-sm font-medium text-slate-700 cursor-pointer">
                        Akun Induk (Header)
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Pos Saldo</label>
                        <select name="normal_balance" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="-">- (Netral)</option>
                            <option value="D" selected>D (Debit)</option>
                            <option value="K">K (Kredit)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Level Indentasi</label>
                        <input type="number" name="level" min="0" max="10" value="1" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeAccountModal()" class="px-4 py-2 text-slate-600 font-bold text-sm bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">Batal</button>
                    <button type="submit" id="btnSaveAccount" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold text-sm transition-colors">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div> --}}

{{-- <!-- resources/views/expenses/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="space-y-6 pb-10">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                    <i data-feather="file-text" class="text-orange-600 w-7 h-7"></i> Pengeluaran Biaya
                </h1>
                <p class="text-slate-500 mt-1">Catat biaya operasional dan cetak Anggaran Dana (KBD)</p>
            </div>

            <div class="flex items-center gap-3">
                <!-- Dropdown Cetak KBD -->
                <div class="relative group">
                    <button class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
                        <i data-feather="printer" class="w-4 h-4"></i> Cetak KBD terpilih
                    </button>
                    <!-- Menu Dropdown -->
                    <div class="absolute right-0 mt-1 w-48 bg-white border border-slate-200 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                        <button onclick="submitExport('print')" class="w-full text-left px-4 py-2.5 text-sm hover:bg-slate-50 font-semibold text-slate-700 border-b flex items-center gap-2">
                            <i data-feather="file" class="w-4 h-4 text-rose-500"></i> Cetak PDF / Layar
                        </button>
                        <button onclick="submitExport('csv')" class="w-full text-left px-4 py-2.5 text-sm hover:bg-slate-50 font-semibold text-slate-700 flex items-center gap-2">
                            <i data-feather="grid" class="w-4 h-4 text-emerald-500"></i> Ekspor ke Excel (CSV)
                        </button>
                    </div>
                </div>

                <button onclick="document.getElementById('expModal').classList.remove('hidden')"
                    class="bg-orange-600 hover:bg-orange-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
                    <i data-feather="plus" class="w-4 h-4"></i> Catat Biaya
                </button>
            </div>
        </div>

        <!-- Tabel Daftar Biaya -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="p-4 w-12 text-center">
                            <input type="checkbox" id="checkAll" onclick="toggleAll(this)" class="w-4 h-4 rounded border-slate-300 text-orange-600 focus:ring-orange-500 cursor-pointer">
                        </th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Tanggal</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">No. KBD</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Deskripsi</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Nominal (Rp)</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $exp)
                        <tr class="border-b border-slate-50 hover:bg-slate-50">
                            <td class="p-4 text-center">
                                <input type="checkbox" class="kbd-checkbox w-4 h-4 rounded border-slate-300 text-orange-600 focus:ring-orange-500 cursor-pointer" value="{{ $exp->id }}">
                            </td>
                            <td class="p-4 text-sm">{{ \Carbon\Carbon::parse($exp->date)->format('d/m/Y') }}</td>
                            <td class="p-4 text-sm font-bold text-orange-700">{{ $exp->kbd_no ?: '-' }}</td>
                            <td class="p-4 text-sm text-slate-800 font-medium">
                                {{ $exp->description }}
                                @if($exp->reference_no) <span class="text-xs text-slate-400 block">{{ $exp->reference_no }}</span> @endif
                            </td>
                            <td class="p-4 text-sm font-bold text-slate-800 text-right">Rp {{ number_format($exp->amount, 0, ',', '.') }}</td>
                            <td class="p-4 text-center">
                                <form action="{{ route('expenses.destroy', $exp->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus catatan biaya ini? (Jurnal akuntansi tidak akan terhapus otomatis)');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-500"><i data-feather="trash-2" class="w-4 h-4"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500">Belum ada catatan biaya.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Form Tersembunyi untuk Ekspor -->
        <form id="exportForm" action="{{ route('expenses.exportKbd') }}" method="GET" target="_blank" class="hidden">
            <input type="hidden" name="ids" id="exportIds">
            <input type="hidden" name="format" id="exportFormat">
        </form>

        <!-- Modal Form Catat Biaya -->
        <div id="expModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col">
                <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-xl font-bold text-slate-800">Catat Pengeluaran Baru</h3>
                    <button type="button" onclick="document.getElementById('expModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700"><i data-feather="x" class="w-5 h-5"></i></button>
                </div>

                <form action="{{ route('expenses.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal</label>
                            <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nomor Referensi Bon (Opsional)</label>
                            <input type="text" name="reference_no" placeholder="Cth: INV-PLN-12" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>

                    <div class="mt-4 border-t border-slate-200 pt-4">
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-bold text-slate-700">Jurnal Akuntansi</label>
                            <button type="button" onclick="openAccountModal()" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1 bg-blue-50 px-2 py-1 rounded">
                                <i data-feather="plus" class="w-3 h-3"></i> Tambah Akun CoA Baru
                            </button>
                        </div>

                        <div class="grid grid-cols-2 gap-4 bg-orange-50 p-4 rounded-lg border border-orange-100">
                            <div>
                                <label class="block text-sm font-bold text-orange-900 mb-1">Akun Biaya (Debit)</label>
                                <select name="account_code" id="expenseAccountDropdown" required class="w-full border border-orange-200 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="" disabled selected>-- Pilih Beban --</option>
                                    @foreach($expenseAccounts as $a) <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Bayar Dari Akun (Kredit)</label>
                                <select name="payment_code" id="paymentAccountDropdown" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="" disabled selected>-- Pilih Kas/Bank --</option>
                                    @foreach($paymentAccounts as $a) <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option> @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Total Nominal Pengeluaran</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-slate-500">Rp</span>
                            <input type="number" name="amount" required min="1" class="w-full pl-10 pr-4 py-3 border-2 border-slate-300 rounded-lg font-bold text-lg outline-none focus:border-orange-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Deskripsi / Keterangan KBD</label>
                        <textarea name="description" required rows="2" placeholder="Cth: XL (0817770299) atau BPJS Kesehatan..." class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500 resize-none"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" onclick="document.getElementById('expModal').classList.add('hidden')" class="px-5 py-2 text-slate-600 hover:bg-slate-100 rounded-lg font-bold">Batal</button>
                        <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2.5 rounded-lg font-bold flex items-center gap-2">Simpan & Buat Jurnal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JS Untuk Export -->
    <script>
        function toggleAll(source) {
            const checkboxes = document.querySelectorAll('.kbd-checkbox');
            checkboxes.forEach(cb => cb.checked = source.checked);
        }

        function submitExport(format) {
            const checkboxes = document.querySelectorAll('.kbd-checkbox:checked');
            if (checkboxes.length === 0) {
                alert('Silakan centang minimal 1 biaya terlebih dahulu sebelum mencetak KBD.');
                return;
            }

            let ids = [];
            checkboxes.forEach(cb => ids.push(cb.value));

            document.getElementById('exportIds').value = ids.join(',');
            document.getElementById('exportFormat').value = format;
            document.getElementById('exportForm').submit();
        }
    </script>

    <script>
        function openAccountModal() {
            document.getElementById('quickAccountModal').classList.remove('hidden');
        }

        function closeAccountModal() {
            document.getElementById('quickAccountModal').classList.add('hidden');
            document.getElementById('quickAccountForm').reset();
        }

        async function submitQuickAccount(e) {
            e.preventDefault();

            const form = document.getElementById('quickAccountForm');
            const btn = document.getElementById('btnSaveAccount');
            const formData = new FormData(form);

            btn.disabled = true;
            btn.innerText = 'Menyimpan...';

            try {
                const response = await fetch("/accounts", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    const newOptionExp = document.createElement("option");
                    newOptionExp.value = result.account.code;
                    newOptionExp.text = `[${result.account.code}] ${result.account.name}`;

                    const newOptionPay = document.createElement("option");
                    newOptionPay.value = result.account.code;
                    newOptionPay.text = `[${result.account.code}] ${result.account.name}`;

                    // Cek kode akun: jika berawalan 6, 7, 8, atau 9 (Kelompok Beban), masukkan ke dropdown Debit
                    if (['6', '7', '8', '9'].includes(result.account.code.charAt(0))) {
                        const dropdownExp = document.getElementById('expenseAccountDropdown');
                        dropdownExp.add(newOptionExp);
                        dropdownExp.value = result.account.code;
                    }
                    // Jika selain itu (misal 1 untuk Kas/Bank), masukkan ke dropdown Kredit
                    else {
                        const dropdownPay = document.getElementById('paymentAccountDropdown');
                        dropdownPay.add(newOptionPay);
                        dropdownPay.value = result.account.code;
                    }

                    closeAccountModal();
                    alert('Akun CoA berhasil ditambahkan!');
                } else {
                    alert(result.message || 'Gagal menambahkan akun.');
                }
            } catch (error) {
                alert('Terjadi kesalahan jaringan.');
            } finally {
                btn.disabled = false;
                btn.innerText = 'Simpan Akun';
            }
        }
    </script>
@endsection --}}

<!-- resources/views/expenses/index.blade.php -->
{{-- @extends('layouts.app')

@section('content')
    <div class="space-y-6 pb-10">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                    <i data-feather="file-text" class="text-orange-600 w-7 h-7"></i> Pengeluaran Biaya
                </h1>
                <p class="text-slate-500 mt-1">Catat biaya operasional dan cetak Anggaran Dana (KBD)</p>
            </div>

            <div class="flex items-center gap-3">
                <!-- Dropdown Cetak KBD -->
                <div class="relative group">
                    <button class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
                        <i data-feather="printer" class="w-4 h-4"></i> Cetak KBD terpilih
                    </button>
                    <!-- Menu Dropdown -->
                    <div class="absolute right-0 mt-1 w-48 bg-white border border-slate-200 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                        <button onclick="submitExport('print')" class="w-full text-left px-4 py-2.5 text-sm hover:bg-slate-50 font-semibold text-slate-700 border-b flex items-center gap-2">
                            <i data-feather="file" class="w-4 h-4 text-rose-500"></i> Cetak PDF / Layar
                        </button>
                        <button onclick="submitExport('csv')" class="w-full text-left px-4 py-2.5 text-sm hover:bg-slate-50 font-semibold text-slate-700 flex items-center gap-2">
                            <i data-feather="grid" class="w-4 h-4 text-emerald-500"></i> Ekspor ke Excel (CSV)
                        </button>
                    </div>
                </div>

                <button onclick="document.getElementById('expModal').classList.remove('hidden')"
                    class="bg-orange-600 hover:bg-orange-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
                    <i data-feather="plus" class="w-4 h-4"></i> Catat Biaya
                </button>
            </div>
        </div>

        <!-- Tabel Daftar Biaya -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="p-4 w-12 text-center">
                            <input type="checkbox" id="checkAll" onclick="toggleAll(this)" class="w-4 h-4 rounded border-slate-300 text-orange-600 focus:ring-orange-500 cursor-pointer">
                        </th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Tanggal</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">No. KBD</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Deskripsi</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Nominal (Rp)</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $exp)
                        <tr class="border-b border-slate-50 hover:bg-slate-50">
                            <td class="p-4 text-center">
                                <input type="checkbox" class="kbd-checkbox w-4 h-4 rounded border-slate-300 text-orange-600 focus:ring-orange-500 cursor-pointer" value="{{ $exp->id }}">
                            </td>
                            <td class="p-4 text-sm">{{ \Carbon\Carbon::parse($exp->date)->format('d/m/Y') }}</td>
                            <td class="p-4 text-sm font-bold text-orange-700">{{ $exp->kbd_no ?: '-' }}</td>
                            <td class="p-4 text-sm text-slate-800 font-medium">
                                {{ $exp->description }}
                                @if($exp->reference_no) <span class="text-xs text-slate-400 block">{{ $exp->reference_no }}</span> @endif
                            </td>
                            <td class="p-4 text-sm font-bold text-slate-800 text-right">Rp {{ number_format($exp->amount, 0, ',', '.') }}</td>
                            <td class="p-4 text-center">
                                <form action="{{ route('expenses.destroy', $exp->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus catatan biaya ini? (Jurnal akuntansi tidak akan terhapus otomatis)');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-500"><i data-feather="trash-2" class="w-4 h-4"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500">Belum ada catatan biaya.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Form Tersembunyi untuk Ekspor -->
        <form id="exportForm" action="{{ route('expenses.exportKbd') }}" method="GET" target="_blank" class="hidden">
            <input type="hidden" name="ids" id="exportIds">
            <input type="hidden" name="format" id="exportFormat">
        </form>

        <!-- Modal Form Catat Biaya -->
        <div id="expModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col">
                <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-xl font-bold text-slate-800">Catat Pengeluaran Baru</h3>
                    <button type="button" onclick="document.getElementById('expModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700"><i data-feather="x" class="w-5 h-5"></i></button>
                </div>

                <!-- PERHATIAN: Tambahkan event onsubmit="unformatRibuan(this)" di sini -->
                <form action="{{ route('expenses.store') }}" method="POST" class="p-6 space-y-4" onsubmit="unformatRibuan(this)">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal</label>
                            <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nomor Referensi Bon (Opsional)</label>
                            <input type="text" name="reference_no" placeholder="Cth: INV-PLN-12" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>

                    <div class="mt-4 border-t border-slate-200 pt-4">
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-bold text-slate-700">Jurnal Akuntansi</label>
                            <button type="button" onclick="openAccountModal()" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1 bg-blue-50 px-2 py-1 rounded">
                                <i data-feather="plus" class="w-3 h-3"></i> Tambah Akun CoA Baru
                            </button>
                        </div>

                        <div class="grid grid-cols-2 gap-4 bg-orange-50 p-4 rounded-lg border border-orange-100">
                            <div>
                                <label class="block text-sm font-bold text-orange-900 mb-1">Akun Biaya (Debit)</label>
                                <select name="account_code" id="expenseAccountDropdown" required class="w-full border border-orange-200 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="" disabled selected>-- Pilih Beban --</option>
                                    @foreach($expenseAccounts as $a) <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Bayar Dari Akun (Kredit)</label>
                                <select name="payment_code" id="paymentAccountDropdown" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="" disabled selected>-- Pilih Kas/Bank --</option>
                                    @foreach($paymentAccounts as $a) <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option> @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Total Nominal Pengeluaran</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-slate-500">Rp</span>
                            <!-- PERHATIAN: Diubah menjadi type="text", ditambah class="format-ribuan", dan event oninput -->
                            <input type="text" name="amount" required oninput="formatRibuan(this)" placeholder="0" class="format-ribuan w-full pl-10 pr-4 py-3 border-2 border-slate-300 rounded-lg font-bold text-lg outline-none focus:border-orange-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Deskripsi / Keterangan KBD</label>
                        <textarea name="description" required rows="2" placeholder="Cth: XL (0817770299) atau BPJS Kesehatan..." class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500 resize-none"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" onclick="document.getElementById('expModal').classList.add('hidden')" class="px-5 py-2 text-slate-600 hover:bg-slate-100 rounded-lg font-bold">Batal</button>
                        <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2.5 rounded-lg font-bold flex items-center gap-2">Simpan & Buat Jurnal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Mini: Tambah Akun CoA Cepat (Telah dikembalikan) -->
    <div id="quickAccountModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-[60] p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 class="font-bold text-slate-800">Tambah Akun CoA Baru</h3>
                <button type="button" onclick="closeAccountModal()" class="text-slate-400 hover:text-slate-700 transition-colors"><i data-feather="x" class="w-5 h-5"></i></button>
            </div>

            <form id="quickAccountForm" onsubmit="submitQuickAccount(event)" class="p-5 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Kode Akun</label>
                    <input type="text" name="code" required placeholder="Misal: 706 atau 115" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Akun</label>
                    <input type="text" name="name" required placeholder="Misal: Biaya Langganan / Bank Jago" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <div class="flex items-center gap-2 mt-2">
                    <input type="hidden" name="is_header" value="0">
                    <input type="checkbox" name="is_header" value="1" class="w-4 h-4 text-blue-600 rounded border-gray-300">
                    <label class="text-sm font-medium text-slate-700 cursor-pointer">
                        Akun Induk (Header)
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Pos Saldo</label>
                        <select name="normal_balance" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="-">- (Netral)</option>
                            <option value="D" selected>D (Debit)</option>
                            <option value="K">K (Kredit)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Level Indentasi</label>
                        <input type="number" name="level" min="0" max="10" value="1" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeAccountModal()" class="px-4 py-2 text-slate-600 font-bold text-sm bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">Batal</button>
                    <button type="submit" id="btnSaveAccount" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold text-sm transition-colors">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script Kumpulan Fungsi -->
    <script>
        // --- Helper Formatting Angka Ribuan ---
        function formatRibuan(inputElement) {
            let value = inputElement.value.replace(/[^0-9]/g, '');
            if (value !== '') {
                inputElement.value = parseInt(value, 10).toLocaleString('id-ID');
            } else {
                inputElement.value = '';
            }
        }

        function unformatRibuan(formElement) {
            let formattedInputs = formElement.querySelectorAll('.format-ribuan');
            formattedInputs.forEach(input => {
                input.value = input.value.replace(/\./g, '');
            });
        }

        // --- Fitur Export Checkbox ---
        function toggleAll(source) {
            const checkboxes = document.querySelectorAll('.kbd-checkbox');
            checkboxes.forEach(cb => cb.checked = source.checked);
        }

        function submitExport(format) {
            const checkboxes = document.querySelectorAll('.kbd-checkbox:checked');
            if (checkboxes.length === 0) {
                alert('Silakan centang minimal 1 biaya terlebih dahulu sebelum mencetak KBD.');
                return;
            }

            let ids = [];
            checkboxes.forEach(cb => ids.push(cb.value));

            document.getElementById('exportIds').value = ids.join(',');
            document.getElementById('exportFormat').value = format;
            document.getElementById('exportForm').submit();
        }

        // --- Fitur Quick Account CoA (AJAX) ---
        function openAccountModal() {
            document.getElementById('quickAccountModal').classList.remove('hidden');
        }

        function closeAccountModal() {
            document.getElementById('quickAccountModal').classList.add('hidden');
            document.getElementById('quickAccountForm').reset();
        }

        async function submitQuickAccount(e) {
            e.preventDefault();

            const form = document.getElementById('quickAccountForm');
            const btn = document.getElementById('btnSaveAccount');
            const formData = new FormData(form);

            btn.disabled = true;
            btn.innerText = 'Menyimpan...';

            try {
                const response = await fetch("/accounts", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    const newOptionExp = document.createElement("option");
                    newOptionExp.value = result.account.code;
                    newOptionExp.text = `[${result.account.code}] ${result.account.name}`;

                    const newOptionPay = document.createElement("option");
                    newOptionPay.value = result.account.code;
                    newOptionPay.text = `[${result.account.code}] ${result.account.name}`;

                    // Cek kode akun: jika berawalan 6, 7, 8, atau 9 (Kelompok Beban), masukkan ke dropdown Debit
                    if (['6', '7', '8', '9'].includes(result.account.code.charAt(0))) {
                        const dropdownExp = document.getElementById('expenseAccountDropdown');
                        dropdownExp.add(newOptionExp);
                        dropdownExp.value = result.account.code;
                    }
                    // Jika selain itu (misal 1 untuk Kas/Bank), masukkan ke dropdown Kredit
                    else {
                        const dropdownPay = document.getElementById('paymentAccountDropdown');
                        dropdownPay.add(newOptionPay);
                        dropdownPay.value = result.account.code;
                    }

                    closeAccountModal();
                    alert('Akun CoA berhasil ditambahkan!');
                } else {
                    alert(result.message || 'Gagal menambahkan akun.');
                }
            } catch (error) {
                alert('Terjadi kesalahan jaringan.');
            } finally {
                btn.disabled = false;
                btn.innerText = 'Simpan Akun';
            }
        }
    </script>
@endsection --}}

{{-- <!-- resources/views/expenses/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="space-y-6 pb-10">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                    <i data-feather="file-text" class="text-orange-600 w-7 h-7"></i> Pengeluaran Biaya
                </h1>
                <p class="text-slate-500 mt-1">Catat biaya operasional dan cetak Anggaran Dana (KBD)</p>
            </div>

            <div class="flex items-center gap-3">
                <!-- Dropdown Cetak KBD -->
                <div class="relative group">
                    <button class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
                        <i data-feather="printer" class="w-4 h-4"></i> Cetak KBD terpilih
                    </button>
                    <!-- Menu Dropdown -->
                    <div class="absolute right-0 mt-1 w-48 bg-white border border-slate-200 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                        <button onclick="submitExport('print')" class="w-full text-left px-4 py-2.5 text-sm hover:bg-slate-50 font-semibold text-slate-700 border-b flex items-center gap-2">
                            <i data-feather="file" class="w-4 h-4 text-rose-500"></i> Cetak PDF / Layar
                        </button>
                        <button onclick="submitExport('csv')" class="w-full text-left px-4 py-2.5 text-sm hover:bg-slate-50 font-semibold text-slate-700 flex items-center gap-2">
                            <i data-feather="grid" class="w-4 h-4 text-emerald-500"></i> Ekspor ke Excel (CSV)
                        </button>
                    </div>
                </div>

                <button onclick="document.getElementById('expModal').classList.remove('hidden')"
                    class="bg-orange-600 hover:bg-orange-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
                    <i data-feather="plus" class="w-4 h-4"></i> Catat Biaya
                </button>
            </div>
        </div>

        <!-- Tabel Daftar Biaya -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="p-4 w-12 text-center">
                            <input type="checkbox" id="checkAll" onclick="toggleAll(this)" class="w-4 h-4 rounded border-slate-300 text-orange-600 focus:ring-orange-500 cursor-pointer">
                        </th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Tanggal</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">No. KBD</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Deskripsi</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Nominal (Rp)</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $exp)
                        <tr class="border-b border-slate-50 hover:bg-slate-50">
                            <td class="p-4 text-center">
                                <input type="checkbox" class="kbd-checkbox w-4 h-4 rounded border-slate-300 text-orange-600 focus:ring-orange-500 cursor-pointer" value="{{ $exp->id }}">
                            </td>
                            <td class="p-4 text-sm">{{ \Carbon\Carbon::parse($exp->date)->format('d/m/Y') }}</td>
                            <td class="p-4 text-sm font-bold text-orange-700">{{ $exp->kbd_no ?: '-' }}</td>
                            <td class="p-4 text-sm text-slate-800 font-medium">
                                {{ $exp->description }}
                                @if($exp->reference_no) <span class="text-xs text-slate-400 block">{{ $exp->reference_no }}</span> @endif
                            </td>
                            <td class="p-4 text-sm font-bold text-slate-800 text-right">Rp {{ number_format($exp->amount, 0, ',', '.') }}</td>
                            <td class="p-4 text-center">
                                <form action="{{ route('expenses.destroy', $exp->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus catatan biaya ini? (Jurnal akuntansi tidak akan terhapus otomatis)');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-500"><i data-feather="trash-2" class="w-4 h-4"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500">Belum ada catatan biaya.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Form Tersembunyi untuk Ekspor -->
        <form id="exportForm" action="{{ route('expenses.exportKbd') }}" method="GET" target="_blank" class="hidden">
            <input type="hidden" name="ids" id="exportIds">
            <input type="hidden" name="format" id="exportFormat">
        </form>

        <!-- Modal Form Catat Biaya -->
        <div id="expModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col">
                <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-xl font-bold text-slate-800">Catat Pengeluaran Baru</h3>
                    <button type="button" onclick="document.getElementById('expModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700"><i data-feather="x" class="w-5 h-5"></i></button>
                </div>

                <!-- PERHATIAN: Tambahkan event onsubmit="unformatRibuan(this)" di sini -->
                <form action="{{ route('expenses.store') }}" method="POST" class="p-6 space-y-4" onsubmit="unformatRibuan(this)">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal</label>
                            <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nomor Referensi Bon (Opsional)</label>
                            <input type="text" name="reference_no" placeholder="Cth: INV-PLN-12" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>

                    <div class="mt-4 border-t border-slate-200 pt-4">
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-bold text-slate-700">Jurnal Akuntansi</label>
                            <button type="button" onclick="openAccountModal()" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1 bg-blue-50 px-2 py-1 rounded">
                                <i data-feather="plus" class="w-3 h-3"></i> Tambah Akun CoA Baru
                            </button>
                        </div>

                        <div class="grid grid-cols-2 gap-4 bg-orange-50 p-4 rounded-lg border border-orange-100">
                            <div>
                                <label class="block text-sm font-bold text-orange-900 mb-1">Akun Biaya (Debit)</label>
                                <select name="account_code" id="expenseAccountDropdown" required class="w-full border border-orange-200 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="" disabled selected>-- Pilih Beban --</option>
                                    @foreach($expenseAccounts as $a) <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Bayar Dari Akun (Kredit)</label>
                                <select name="payment_code" id="paymentAccountDropdown" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="" disabled selected>-- Pilih Kas/Bank --</option>
                                    @foreach($paymentAccounts as $a) <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option> @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Total Nominal Pengeluaran</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-slate-500">Rp</span>
                            <!-- PERHATIAN: Diubah menjadi type="text", ditambah class="format-ribuan", dan event oninput -->
                            <input type="text" name="amount" required oninput="formatRibuan(this)" placeholder="0" class="format-ribuan w-full pl-10 pr-4 py-3 border-2 border-slate-300 rounded-lg font-bold text-lg outline-none focus:border-orange-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Deskripsi / Keterangan KBD</label>
                        <textarea name="description" required rows="2" placeholder="Cth: XL (0817770299) atau BPJS Kesehatan..." class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500 resize-none"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" onclick="document.getElementById('expModal').classList.add('hidden')" class="px-5 py-2 text-slate-600 hover:bg-slate-100 rounded-lg font-bold">Batal</button>
                        <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2.5 rounded-lg font-bold flex items-center gap-2">Simpan & Buat Jurnal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Mini: Tambah Akun CoA Cepat (Telah dikembalikan) -->
    <div id="quickAccountModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-[60] p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 class="font-bold text-slate-800">Tambah Akun CoA Baru</h3>
                <button type="button" onclick="closeAccountModal()" class="text-slate-400 hover:text-slate-700 transition-colors"><i data-feather="x" class="w-5 h-5"></i></button>
            </div>

            <form id="quickAccountForm" onsubmit="submitQuickAccount(event)" class="p-5 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Kode Akun</label>
                    <input type="text" name="code" required placeholder="Misal: 706 atau 115" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Akun</label>
                    <input type="text" name="name" required placeholder="Misal: Biaya Langganan / Bank Jago" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <div class="flex items-center gap-2 mt-2">
                    <input type="hidden" name="is_header" value="0">
                    <input type="checkbox" name="is_header" value="1" class="w-4 h-4 text-blue-600 rounded border-gray-300">
                    <label class="text-sm font-medium text-slate-700 cursor-pointer">
                        Akun Induk (Header)
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Pos Saldo</label>
                        <select name="normal_balance" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="-">- (Netral)</option>
                            <option value="D" selected>D (Debit)</option>
                            <option value="K">K (Kredit)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Level Indentasi</label>
                        <input type="number" name="level" min="0" max="10" value="1" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeAccountModal()" class="px-4 py-2 text-slate-600 font-bold text-sm bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">Batal</button>
                    <button type="submit" id="btnSaveAccount" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold text-sm transition-colors">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script Kumpulan Fungsi -->
    <script>
        // --- Helper Formatting Angka Ribuan ---
        function formatRibuan(inputElement) {
            let value = inputElement.value.replace(/[^0-9]/g, '');
            if (value !== '') {
                inputElement.value = parseInt(value, 10).toLocaleString('id-ID');
            } else {
                inputElement.value = '';
            }
        }

        function unformatRibuan(formElement) {
            let formattedInputs = formElement.querySelectorAll('.format-ribuan');
            formattedInputs.forEach(input => {
                input.value = input.value.replace(/\./g, '');
            });
        }

        // --- Fitur Export Checkbox ---
        function toggleAll(source) {
            const checkboxes = document.querySelectorAll('.kbd-checkbox');
            checkboxes.forEach(cb => cb.checked = source.checked);
        }

        function submitExport(format) {
            const checkboxes = document.querySelectorAll('.kbd-checkbox:checked');
            if (checkboxes.length === 0) {
                alert('Silakan centang minimal 1 biaya terlebih dahulu sebelum mencetak KBD.');
                return;
            }

            let ids = [];
            checkboxes.forEach(cb => ids.push(cb.value));

            document.getElementById('exportIds').value = ids.join(',');
            document.getElementById('exportFormat').value = format;
            document.getElementById('exportForm').submit();
        }

        // --- Fitur Quick Account CoA (AJAX) ---
        function openAccountModal() {
            document.getElementById('quickAccountModal').classList.remove('hidden');
        }

        function closeAccountModal() {
            document.getElementById('quickAccountModal').classList.add('hidden');
            document.getElementById('quickAccountForm').reset();
        }

        async function submitQuickAccount(e) {
            e.preventDefault();

            const form = document.getElementById('quickAccountForm');
            const btn = document.getElementById('btnSaveAccount');
            const formData = new FormData(form);

            btn.disabled = true;
            btn.innerText = 'Menyimpan...';

            try {
                const response = await fetch("/accounts", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    const newOptionExp = document.createElement("option");
                    newOptionExp.value = result.account.code;
                    newOptionExp.text = `[${result.account.code}] ${result.account.name}`;

                    const newOptionPay = document.createElement("option");
                    newOptionPay.value = result.account.code;
                    newOptionPay.text = `[${result.account.code}] ${result.account.name}`;

                    // Cek kode akun: jika berawalan 6, 7, 8, atau 9 (Kelompok Beban), masukkan ke dropdown Debit
                    if (['6', '7', '8', '9'].includes(result.account.code.charAt(0))) {
                        const dropdownExp = document.getElementById('expenseAccountDropdown');
                        dropdownExp.add(newOptionExp);
                        dropdownExp.value = result.account.code;
                    }
                    // Jika selain itu (misal 1 untuk Kas/Bank), masukkan ke dropdown Kredit
                    else {
                        const dropdownPay = document.getElementById('paymentAccountDropdown');
                        dropdownPay.add(newOptionPay);
                        dropdownPay.value = result.account.code;
                    }

                    closeAccountModal();
                    alert('Akun CoA berhasil ditambahkan!');
                } else {
                    alert(result.message || 'Gagal menambahkan akun.');
                }
            } catch (error) {
                alert('Terjadi kesalahan jaringan.');
            } finally {
                btn.disabled = false;
                btn.innerText = 'Simpan Akun';
            }
        }
    </script>
@endsection --}}

<!-- resources/views/expenses/index.blade.php -->
{{-- @extends('layouts.app')

@section('content')
    <div class="space-y-6 pb-10">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                    <i data-feather="file-text" class="text-orange-600 w-7 h-7"></i> Pengeluaran Biaya
                </h1>
                <p class="text-slate-500 mt-1">Catat biaya operasional dan cetak Anggaran Dana (KBD)</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="relative group">
                    <button class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
                        <i data-feather="printer" class="w-4 h-4"></i> Cetak KBD terpilih
                    </button>
                    <div class="absolute right-0 mt-1 w-48 bg-white border border-slate-200 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                        <button onclick="submitExport('print')" class="w-full text-left px-4 py-2.5 text-sm hover:bg-slate-50 font-semibold text-slate-700 border-b flex items-center gap-2">
                            <i data-feather="file" class="w-4 h-4 text-rose-500"></i> Cetak PDF / Layar
                        </button>
                        <button onclick="submitExport('csv')" class="w-full text-left px-4 py-2.5 text-sm hover:bg-slate-50 font-semibold text-slate-700 flex items-center gap-2">
                            <i data-feather="grid" class="w-4 h-4 text-emerald-500"></i> Ekspor ke Excel (CSV)
                        </button>
                    </div>
                </div>

                <button onclick="document.getElementById('expModal').classList.remove('hidden')"
                    class="bg-orange-600 hover:bg-orange-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
                    <i data-feather="plus" class="w-4 h-4"></i> Catat Biaya
                </button>
            </div>
        </div>

        <!-- Filter Tanggal -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
            <form action="{{ route('expenses.index') }}" method="GET" class="flex flex-col sm:flex-row flex-wrap items-end sm:items-center gap-4 w-full">
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <label class="font-semibold text-slate-500 text-sm">Periode KBD:</label>
                    <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="border border-slate-300 rounded-lg p-2 outline-none focus:ring-2 focus:ring-orange-500 text-sm font-medium">
                    <span class="text-slate-400 font-bold">-</span>
                    <input type="date" name="end_date" value="{{ $endDate ?? '' }}" class="border border-slate-300 rounded-lg p-2 outline-none focus:ring-2 focus:ring-orange-500 text-sm font-medium">
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="submit" class="bg-orange-100 hover:bg-orange-200 text-orange-700 px-5 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-colors flex items-center gap-2 w-full sm:w-auto justify-center">
                        <i data-feather="filter" class="w-4 h-4"></i> Filter
                    </button>
                    @if(isset($startDate) || isset($endDate))
                    <a href="{{ route('expenses.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-colors" title="Hapus Filter">
                        <i data-feather="x" class="w-4 h-4"></i>
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Daftar Biaya -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="p-4 w-12 text-center">
                            <input type="checkbox" id="checkAll" onclick="toggleAll(this)" class="w-4 h-4 rounded border-slate-300 text-orange-600 focus:ring-orange-500 cursor-pointer">
                        </th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Tanggal</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">No. KBD</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Deskripsi</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Nominal (Rp)</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $exp)
                        <tr class="border-b border-slate-50 hover:bg-slate-50">
                            <td class="p-4 text-center">
                                <input type="checkbox" class="kbd-checkbox w-4 h-4 rounded border-slate-300 text-orange-600 focus:ring-orange-500 cursor-pointer" value="{{ $exp->id }}">
                            </td>
                            <td class="p-4 text-sm">{{ \Carbon\Carbon::parse($exp->date)->format('d/m/Y') }}</td>
                            <td class="p-4 text-sm font-bold text-orange-700">{{ $exp->kbd_no ?: '-' }}</td>
                            <td class="p-4 text-sm text-slate-800 font-medium">
                                {{ $exp->description }}
                                @if($exp->reference_no) <span class="text-xs text-slate-400 block">{{ $exp->reference_no }}</span> @endif
                            </td>
                            <td class="p-4 text-sm font-bold text-slate-800 text-right">Rp {{ number_format($exp->amount, 0, ',', '.') }}</td>
                            <td class="p-4 text-center">
                                <form action="{{ route('expenses.destroy', $exp->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus catatan biaya ini? (Jurnal akuntansi tidak akan terhapus otomatis)');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-500"><i data-feather="trash-2" class="w-4 h-4"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500">Belum ada catatan biaya pada rentang waktu ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Form Tersembunyi untuk Ekspor -->
        <form id="exportForm" action="{{ route('expenses.exportKbd') }}" method="GET" target="_blank" class="hidden">
            <input type="hidden" name="ids" id="exportIds">
            <input type="hidden" name="format" id="exportFormat">
        </form>

        <!-- Modal Form Catat Biaya -->
        <div id="expModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col">
                <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-xl font-bold text-slate-800">Catat Pengeluaran Baru</h3>
                    <button type="button" onclick="document.getElementById('expModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700"><i data-feather="x" class="w-5 h-5"></i></button>
                </div>

                <form action="{{ route('expenses.store') }}" method="POST" class="p-6 space-y-4" onsubmit="unformatRibuan(this)">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal</label>
                            <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nomor Referensi Bon (Opsional)</label>
                            <input type="text" name="reference_no" placeholder="Cth: INV-PLN-12" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>

                    <div class="mt-4 border-t border-slate-200 pt-4">
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-bold text-slate-700">Jurnal Akuntansi</label>
                            <button type="button" onclick="openAccountModal()" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1 bg-blue-50 px-2 py-1 rounded">
                                <i data-feather="plus" class="w-3 h-3"></i> Tambah Akun CoA Baru
                            </button>
                        </div>

                        <div class="grid grid-cols-2 gap-4 bg-orange-50 p-4 rounded-lg border border-orange-100">
                            <div>
                                <label class="block text-sm font-bold text-orange-900 mb-1">Akun Biaya (Debit)</label>
                                <select name="account_code" id="expenseAccountDropdown" required class="w-full border border-orange-200 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="" disabled selected>-- Pilih Beban --</option>
                                    @foreach($expenseAccounts as $a) <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Bayar Dari Akun (Kredit)</label>
                                <select name="payment_code" id="paymentAccountDropdown" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="" disabled selected>-- Pilih Kas/Bank --</option>
                                    @foreach($paymentAccounts as $a) <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option> @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Total Nominal Pengeluaran</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-slate-500">Rp</span>
                            <input type="text" name="amount" required oninput="formatRibuan(this)" placeholder="0" class="format-ribuan w-full pl-10 pr-4 py-3 border-2 border-slate-300 rounded-lg font-bold text-lg outline-none focus:border-orange-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Deskripsi / Keterangan KBD</label>
                        <textarea name="description" required rows="2" placeholder="Cth: XL (0817770299) atau BPJS Kesehatan..." class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500 resize-none"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" onclick="document.getElementById('expModal').classList.add('hidden')" class="px-5 py-2 text-slate-600 hover:bg-slate-100 rounded-lg font-bold">Batal</button>
                        <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2.5 rounded-lg font-bold flex items-center gap-2">Simpan & Buat Jurnal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Mini: Tambah Akun CoA Cepat -->
    <div id="quickAccountModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-[60] p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 class="font-bold text-slate-800">Tambah Akun CoA Baru</h3>
                <button type="button" onclick="closeAccountModal()" class="text-slate-400 hover:text-slate-700 transition-colors"><i data-feather="x" class="w-5 h-5"></i></button>
            </div>

            <form id="quickAccountForm" onsubmit="submitQuickAccount(event)" class="p-5 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Kode Akun</label>
                    <input type="text" name="code" required placeholder="Misal: 706 atau 115" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Akun</label>
                    <input type="text" name="name" required placeholder="Misal: Biaya Langganan / Bank Jago" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <div class="flex items-center gap-2 mt-2">
                    <input type="hidden" name="is_header" value="0">
                    <input type="checkbox" name="is_header" value="1" class="w-4 h-4 text-blue-600 rounded border-gray-300">
                    <label class="text-sm font-medium text-slate-700 cursor-pointer">
                        Akun Induk (Header)
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Pos Saldo</label>
                        <select name="normal_balance" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="-">- (Netral)</option>
                            <option value="D" selected>D (Debit)</option>
                            <option value="K">K (Kredit)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Level Indentasi</label>
                        <input type="number" name="level" min="0" max="10" value="1" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeAccountModal()" class="px-4 py-2 text-slate-600 font-bold text-sm bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">Batal</button>
                    <button type="submit" id="btnSaveAccount" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold text-sm transition-colors">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script Kumpulan Fungsi -->
    <script>
        function formatRibuan(inputElement) {
            let value = inputElement.value.replace(/[^0-9]/g, '');
            if (value !== '') { inputElement.value = parseInt(value, 10).toLocaleString('id-ID'); } else { inputElement.value = ''; }
        }

        function unformatRibuan(formElement) {
            let formattedInputs = formElement.querySelectorAll('.format-ribuan');
            formattedInputs.forEach(input => { input.value = input.value.replace(/\./g, ''); });
        }

        function toggleAll(source) {
            const checkboxes = document.querySelectorAll('.kbd-checkbox');
            checkboxes.forEach(cb => cb.checked = source.checked);
        }

        function submitExport(format) {
            const checkboxes = document.querySelectorAll('.kbd-checkbox:checked');
            if (checkboxes.length === 0) {
                alert('Silakan centang minimal 1 biaya terlebih dahulu sebelum mencetak KBD.');
                return;
            }
            let ids = [];
            checkboxes.forEach(cb => ids.push(cb.value));
            document.getElementById('exportIds').value = ids.join(',');
            document.getElementById('exportFormat').value = format;
            document.getElementById('exportForm').submit();
        }

        function openAccountModal() { document.getElementById('quickAccountModal').classList.remove('hidden'); }
        function closeAccountModal() { document.getElementById('quickAccountModal').classList.add('hidden'); document.getElementById('quickAccountForm').reset(); }

        async function submitQuickAccount(e) {
            e.preventDefault();
            const form = document.getElementById('quickAccountForm');
            const btn = document.getElementById('btnSaveAccount');
            const formData = new FormData(form);
            btn.disabled = true; btn.innerText = 'Menyimpan...';

            try {
                const response = await fetch("/accounts", {
                    method: "POST",
                    headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" },
                    body: formData
                });
                const result = await response.json();
                if (response.ok && result.success) {
                    const newOptionExp = document.createElement("option");
                    newOptionExp.value = result.account.code;
                    newOptionExp.text = `[${result.account.code}] ${result.account.name}`;

                    const newOptionPay = document.createElement("option");
                    newOptionPay.value = result.account.code;
                    newOptionPay.text = `[${result.account.code}] ${result.account.name}`;

                    if (['6', '7', '8', '9'].includes(result.account.code.charAt(0))) {
                        const dropdownExp = document.getElementById('expenseAccountDropdown');
                        dropdownExp.add(newOptionExp);
                        dropdownExp.value = result.account.code;
                    } else {
                        const dropdownPay = document.getElementById('paymentAccountDropdown');
                        dropdownPay.add(newOptionPay);
                        dropdownPay.value = result.account.code;
                    }
                    closeAccountModal();
                    alert('Akun CoA berhasil ditambahkan!');
                } else { alert(result.message || 'Gagal menambahkan akun.'); }
            } catch (error) { alert('Terjadi kesalahan jaringan.'); } finally {
                btn.disabled = false; btn.innerText = 'Simpan Akun';
            }
        }
    </script>
@endsection --}}

<!-- resources/views/expenses/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="space-y-6 pb-10">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                    <i data-feather="file-text" class="text-orange-600 w-7 h-7"></i> Pengeluaran Biaya
                </h1>
                <p class="text-slate-500 mt-1">Catat biaya operasional dan cetak Anggaran Dana (KBD)</p>
            </div>

            <div class="flex items-center gap-3">

                <!-- DROPDOWN EKSPOR GANDA (INTERNAL & PAJAK) -->
                <div class="relative group">
                    <button class="bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 text-emerald-700 px-4 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-colors flex items-center gap-2">
                        <i data-feather="printer" class="w-4 h-4"></i> Ekspor KBD Terpilih
                    </button>
                    <div class="absolute right-0 mt-1 w-56 bg-white border border-slate-200 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 overflow-hidden">

                        <div class="px-3 py-2 bg-slate-50 border-b font-bold text-xs text-slate-400 uppercase">Lap. Internal (Asli)</div>
                        <button onclick="submitExport('csv', 'internal')" class="w-full text-left px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700">Unduh Excel (CSV)</button>
                        <button onclick="submitExport('print', 'internal')" class="w-full text-left px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700 border-b">Cetak PDF / Layar</button>

                        <div class="px-3 py-2 bg-purple-50 border-b font-bold text-xs text-purple-500 uppercase">Lap. Pajak (Tax)</div>
                        <button onclick="submitExport('csv', 'pajak')" class="w-full text-left px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700">Unduh Excel (CSV)</button>
                        <button onclick="submitExport('print', 'pajak')" class="w-full text-left px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700">Cetak PDF / Layar</button>
                    </div>
                </div>

                <button onclick="document.getElementById('expModal').classList.remove('hidden')"
                    class="bg-orange-600 hover:bg-orange-700 text-white px-5 py-2.5 rounded-lg font-bold transition-colors shadow-sm flex items-center gap-2">
                    <i data-feather="plus" class="w-4 h-4"></i> Catat Biaya
                </button>
            </div>
        </div>

        <!-- Filter Tanggal -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
            <form action="{{ route('expenses.index') }}" method="GET" class="flex flex-col sm:flex-row flex-wrap items-end sm:items-center gap-4 w-full">
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <label class="font-semibold text-slate-500 text-sm">Periode KBD:</label>
                    <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="border border-slate-300 rounded-lg p-2 outline-none focus:ring-2 focus:ring-orange-500 text-sm font-medium">
                    <span class="text-slate-400 font-bold">-</span>
                    <input type="date" name="end_date" value="{{ $endDate ?? '' }}" class="border border-slate-300 rounded-lg p-2 outline-none focus:ring-2 focus:ring-orange-500 text-sm font-medium">
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="submit" class="bg-orange-100 hover:bg-orange-200 text-orange-700 px-5 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-colors flex items-center gap-2 w-full sm:w-auto justify-center">
                        <i data-feather="filter" class="w-4 h-4"></i> Filter
                    </button>
                    @if(isset($startDate) || isset($endDate))
                    <a href="{{ route('expenses.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-colors" title="Hapus Filter">
                        <i data-feather="x" class="w-4 h-4"></i>
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Daftar Biaya -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="p-4 w-12 text-center">
                            <input type="checkbox" id="checkAll" onclick="toggleAll(this)" class="w-4 h-4 rounded border-slate-300 text-orange-600 focus:ring-orange-500 cursor-pointer">
                        </th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Tanggal</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">No. KBD</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Deskripsi</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Nominal (Rp)</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $exp)
                        <tr class="border-b border-slate-50 hover:bg-slate-50">
                            <td class="p-4 text-center">
                                <input type="checkbox" class="kbd-checkbox w-4 h-4 rounded border-slate-300 text-orange-600 focus:ring-orange-500 cursor-pointer" value="{{ $exp->id }}">
                            </td>
                            <td class="p-4 text-sm">{{ \Carbon\Carbon::parse($exp->date)->format('d/m/Y') }}</td>
                            <td class="p-4 text-sm font-bold text-orange-700">{{ $exp->kbd_no ?: '-' }}</td>
                            <td class="p-4 text-sm text-slate-800 font-medium">
                                {{ $exp->description }}
                                @if($exp->reference_no) <span class="text-xs text-slate-400 block">{{ $exp->reference_no }}</span> @endif
                            </td>
                            <td class="p-4 text-sm font-bold text-slate-800 text-right">Rp {{ number_format($exp->amount, 0, ',', '.') }}</td>
                            <td class="p-4 text-center">
                                <form action="{{ route('expenses.destroy', $exp->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus catatan biaya ini? (Jurnal akuntansi tidak akan terhapus otomatis)');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-500"><i data-feather="trash-2" class="w-4 h-4"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500">Belum ada catatan biaya pada rentang waktu ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- FORM TERSEMBUNYI UNTUK EKSPOR (Sudah dimodifikasi agar menangkap "Type") -->
        <form id="exportForm" action="{{ route('expenses.exportKbd') }}" method="GET" target="_blank" class="hidden">
            <input type="hidden" name="ids" id="exportIds">
            <input type="hidden" name="format" id="exportFormat">
            <input type="hidden" name="type" id="exportType">
        </form>

        <!-- Modal Form Catat Biaya -->
        <div id="expModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col">
                <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-xl font-bold text-slate-800">Catat Pengeluaran Baru</h3>
                    <button type="button" onclick="document.getElementById('expModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700"><i data-feather="x" class="w-5 h-5"></i></button>
                </div>

                <form action="{{ route('expenses.store') }}" method="POST" class="p-6 space-y-4" onsubmit="unformatRibuan(this)">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal</label>
                            <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nomor Referensi Bon (Opsional)</label>
                            <input type="text" name="reference_no" placeholder="Cth: INV-PLN-12" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>

                    <div class="mt-4 border-t border-slate-200 pt-4">
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-bold text-slate-700">Jurnal Akuntansi</label>
                            <button type="button" onclick="openAccountModal()" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1 bg-blue-50 px-2 py-1 rounded">
                                <i data-feather="plus" class="w-3 h-3"></i> Tambah Akun CoA Baru
                            </button>
                        </div>

                        <div class="grid grid-cols-2 gap-4 bg-orange-50 p-4 rounded-lg border border-orange-100">
                            <div>
                                <label class="block text-sm font-bold text-orange-900 mb-1">Akun Biaya (Debit)</label>
                                <select name="account_code" id="expenseAccountDropdown" required class="w-full border border-orange-200 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="" disabled selected>-- Pilih Beban --</option>
                                    @foreach($expenseAccounts as $a) <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Bayar Dari Akun (Kredit)</label>
                                <select name="payment_code" id="paymentAccountDropdown" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="" disabled selected>-- Pilih Kas/Bank --</option>
                                    @foreach($paymentAccounts as $a) <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option> @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Total Nominal Pengeluaran</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-slate-500">Rp</span>
                            <input type="text" name="amount" required oninput="formatRibuan(this)" placeholder="0" class="format-ribuan w-full pl-10 pr-4 py-3 border-2 border-slate-300 rounded-lg font-bold text-lg outline-none focus:border-orange-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Deskripsi / Keterangan KBD</label>
                        <textarea name="description" required rows="2" placeholder="Cth: XL (0817770299) atau BPJS Kesehatan..." class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500 resize-none"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" onclick="document.getElementById('expModal').classList.add('hidden')" class="px-5 py-2 text-slate-600 hover:bg-slate-100 rounded-lg font-bold">Batal</button>
                        <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2.5 rounded-lg font-bold flex items-center gap-2">Simpan & Buat Jurnal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Mini: Tambah Akun CoA Cepat -->
    <div id="quickAccountModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-[60] p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 class="font-bold text-slate-800">Tambah Akun CoA Baru</h3>
                <button type="button" onclick="closeAccountModal()" class="text-slate-400 hover:text-slate-700 transition-colors"><i data-feather="x" class="w-5 h-5"></i></button>
            </div>

            <form id="quickAccountForm" onsubmit="submitQuickAccount(event)" class="p-5 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Kode Akun</label>
                    <input type="text" name="code" required placeholder="Misal: 706 atau 115" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Akun</label>
                    <input type="text" name="name" required placeholder="Misal: Biaya Langganan / Bank Jago" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <div class="flex items-center gap-2 mt-2">
                    <input type="hidden" name="is_header" value="0">
                    <input type="checkbox" name="is_header" value="1" class="w-4 h-4 text-blue-600 rounded border-gray-300">
                    <label class="text-sm font-medium text-slate-700 cursor-pointer">Akun Induk (Header)</label>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Pos Saldo</label>
                        <select name="normal_balance" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="-">- (Netral)</option>
                            <option value="D" selected>D (Debit)</option>
                            <option value="K">K (Kredit)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Level Indentasi</label>
                        <input type="number" name="level" min="0" max="10" value="1" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeAccountModal()" class="px-4 py-2 text-slate-600 font-bold text-sm bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">Batal</button>
                    <button type="submit" id="btnSaveAccount" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold text-sm transition-colors">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script Kumpulan Fungsi -->
    <script>
        function formatRibuan(inputElement) {
            let value = inputElement.value.replace(/[^0-9]/g, '');
            if (value !== '') { inputElement.value = parseInt(value, 10).toLocaleString('id-ID'); } else { inputElement.value = ''; }
        }

        function unformatRibuan(formElement) {
            let formattedInputs = formElement.querySelectorAll('.format-ribuan');
            formattedInputs.forEach(input => { input.value = input.value.replace(/\./g, ''); });
        }

        function toggleAll(source) {
            const checkboxes = document.querySelectorAll('.kbd-checkbox');
            checkboxes.forEach(cb => cb.checked = source.checked);
        }

        // FUNGSI SUBMIT EXPORT TELAH DIUPDATE
        function submitExport(format, type = 'internal') {
            const checkboxes = document.querySelectorAll('.kbd-checkbox:checked');
            if (checkboxes.length === 0) {
                alert('Silakan centang minimal 1 biaya (checkbox di tabel) terlebih dahulu sebelum mengekspor KBD.');
                return;
            }
            let ids = [];
            checkboxes.forEach(cb => ids.push(cb.value));

            document.getElementById('exportIds').value = ids.join(',');
            document.getElementById('exportFormat').value = format;
            document.getElementById('exportType').value = type;
            document.getElementById('exportForm').submit();
        }

        function openAccountModal() { document.getElementById('quickAccountModal').classList.remove('hidden'); }
        function closeAccountModal() { document.getElementById('quickAccountModal').classList.add('hidden'); document.getElementById('quickAccountForm').reset(); }

        async function submitQuickAccount(e) {
            e.preventDefault();
            const form = document.getElementById('quickAccountForm');
            const btn = document.getElementById('btnSaveAccount');
            const formData = new FormData(form);
            btn.disabled = true; btn.innerText = 'Menyimpan...';

            try {
                const response = await fetch("/accounts", {
                    method: "POST",
                    headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" },
                    body: formData
                });
                const result = await response.json();
                if (response.ok && result.success) {
                    const newOptionExp = document.createElement("option");
                    newOptionExp.value = result.account.code;
                    newOptionExp.text = `[${result.account.code}] ${result.account.name}`;

                    const newOptionPay = document.createElement("option");
                    newOptionPay.value = result.account.code;
                    newOptionPay.text = `[${result.account.code}] ${result.account.name}`;

                    if (['6', '7', '8', '9'].includes(result.account.code.charAt(0))) {
                        const dropdownExp = document.getElementById('expenseAccountDropdown');
                        dropdownExp.add(newOptionExp);
                        dropdownExp.value = result.account.code;
                    } else {
                        const dropdownPay = document.getElementById('paymentAccountDropdown');
                        dropdownPay.add(newOptionPay);
                        dropdownPay.value = result.account.code;
                    }
                    closeAccountModal();
                    alert('Akun CoA berhasil ditambahkan!');
                } else { alert(result.message || 'Gagal menambahkan akun.'); }
            } catch (error) { alert('Terjadi kesalahan jaringan.'); } finally {
                btn.disabled = false; btn.innerText = 'Simpan Akun';
            }
        }
    </script>
@endsection
