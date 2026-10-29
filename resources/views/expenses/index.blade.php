<!-- resources/views/expenses/index.blade.php -->
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
@endsection