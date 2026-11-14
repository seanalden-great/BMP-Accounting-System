{{-- <!-- resources/views/purchases/bill.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">
    <div class="flex justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="file-text" class="text-blue-600 w-7 h-7"></i> Tagihan & Pembayaran (Bill)
            </h1>
            <p class="text-slate-500 mt-1">Kelola tagihan dari Pemasok dan potong saldo bank otomatis</p>
        </div>
        <button onclick="document.getElementById('billModal').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2">
            <i data-feather="plus-circle" class="w-4 h-4"></i> Proses Pembayaran
        </button>
    </div>

    <!-- Tabel Riwayat Tagihan Lunas -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b">
                <tr>
                    <th class="p-4 text-sm">No. Referensi</th>
                    <th class="p-4 text-sm">Supplier</th>
                    <th class="p-4 text-sm">Tgl Lunas</th>
                    <th class="p-4 text-sm">Bank Pembayar</th>
                    <th class="p-4 text-sm text-right">Total Dibayar</th>
                    <th class="p-4 text-sm text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bills as $bill)
                <tr class="border-b">
                    <td class="p-4 font-bold">{{ $bill->reference_no ?: 'PO #'.$bill->id }}</td>
                    <td class="p-4 font-bold text-blue-700">{{ $bill->contact->name }}</td>
                    <td class="p-4">{{ \Carbon\Carbon::parse($bill->due_date)->format('d/m/Y') }}</td>
                    <td class="p-4 font-bold text-slate-700">{{ $bill->paymentAccount->name ?? 'Tidak Tercatat' }}</td>
                    <td class="p-4 font-bold text-right">Rp {{ number_format($bill->total, 0, ',', '.') }}</td>
                    <td class="p-4 text-center"><span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Lunas</span></td>
                </tr>
                @empty
                <tr><td colspan="6" class="p-8 text-center text-slate-500">Belum ada riwayat tagihan terbayar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Form Bayar Tagihan -->
    <div id="billModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl w-full max-w-2xl p-6">
            <div class="flex justify-between mb-4">
                <h3 class="text-xl font-bold text-slate-800">Proses Pembayaran Tagihan</h3>
                <button onclick="document.getElementById('billModal').classList.add('hidden')" class="text-slate-400 hover:text-red-500"><i data-feather="x"></i></button>
            </div>

            <form action="#" method="POST" id="billForm">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Penerimaan Barang (Belum Lunas)</label>
                        <select name="po_id" required onchange="document.getElementById('billForm').action = '/purchases/' + this.value + '/bill';" class="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" disabled selected>-- Pilih Dokumen Pengiriman Barang --</option>
                            @foreach($readyDeliveries as $po)
                                <option value="{{ $po->id }}">[PO #{{ $po->id }}] {{ $po->contact->name }} - Rp {{ number_format($po->total, 0, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Bayar Dari Rekening</label>
                            <select name="payment_account" required class="w-full border rounded-lg p-3 outline-none">
                                <option value="" disabled selected>-- Pilih Sumber Dana --</option>
                                @foreach($banks as $bank) <option value="{{ $bank->code }}">[{{ $bank->code }}] {{ $bank->name }}</option> @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal Lunas</label>
                            <input type="date" name="due_date" required value="{{ date('Y-m-d') }}" class="w-full border rounded-lg p-3 outline-none">
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-800">
                    <b>Info Sistem:</b> Proses ini akan otomatis membuat Jurnal (Debit: Persediaan, Kredit: Kas/Bank).
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('billModal').classList.add('hidden')" class="px-4 py-2 font-bold text-slate-600">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg font-bold">Bayar Tagihan Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection --}}

{{-- <!-- resources/views/purchases/bill.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">
    <div class="flex justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="file-text" class="text-blue-600 w-7 h-7"></i> Tagihan & Pembayaran (Bill)
            </h1>
            <p class="text-slate-500 mt-1">Kelola tagihan dari Pemasok dan potong saldo bank otomatis</p>
        </div>
        <button onclick="document.getElementById('billModal').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2">
            <i data-feather="plus-circle" class="w-4 h-4"></i> Proses Pembayaran
        </button>
    </div>

    <!-- Tabel Riwayat Tagihan Lunas -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b">
                <tr>
                    <th class="p-4 text-sm">No. Referensi</th>
                    <th class="p-4 text-sm">Supplier</th>
                    <th class="p-4 text-sm">Tgl Lunas</th>
                    <th class="p-4 text-sm">Bank Pembayar</th>
                    <th class="p-4 text-sm text-right">Total Dibayar</th>
                    <th class="p-4 text-sm text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bills as $bill)
                <tr class="border-b hover:bg-slate-50">
                    <td class="p-4 font-bold">{{ $bill->reference_no ?: 'PO #'.$bill->id }}</td>
                    <td class="p-4 font-bold text-blue-700">{{ $bill->contact->name }}</td>
                    <td class="p-4">{{ \Carbon\Carbon::parse($bill->due_date)->format('d/m/Y') }}</td>
                    <td class="p-4 font-bold text-slate-700">{{ $bill->paymentAccount->name ?? 'Tidak Tercatat' }}</td>
                    <td class="p-4 font-bold text-right">Rp {{ number_format($bill->total, 0, ',', '.') }}</td>
                    <td class="p-4 text-center"><span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Lunas</span></td>
                </tr>
                @empty
                <tr><td colspan="6" class="p-8 text-center text-slate-500">Belum ada riwayat tagihan terbayar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Form Bayar Tagihan -->
    <div id="billModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl w-full max-w-2xl p-6">
            <div class="flex justify-between mb-4">
                <h3 class="text-xl font-bold text-slate-800">Proses Pembayaran Tagihan</h3>
                <button onclick="document.getElementById('billModal').classList.add('hidden')" class="text-slate-400 hover:text-red-500"><i data-feather="x"></i></button>
            </div>

            <form action="#" method="POST" id="billForm">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Penerimaan Barang (Belum Lunas)</label>
                        <select name="po_id" required onchange="document.getElementById('billForm').action = '/purchases/' + this.value + '/bill';" class="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" disabled selected>-- Pilih Dokumen Pengiriman Barang --</option>
                            @foreach($readyDeliveries as $po)
                                <option value="{{ $po->id }}">[PO #{{ $po->id }}] {{ $po->contact->name }} - Rp {{ number_format($po->total, 0, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Bayar Dari Rekening</label>
                            <select name="payment_account" required class="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="" disabled selected>-- Pilih Sumber Dana --</option>
                                @foreach($banks as $bank) <option value="{{ $bank->code }}">[{{ $bank->code }}] {{ $bank->name }}</option> @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal Lunas</label>
                            <input type="date" name="due_date" required value="{{ date('Y-m-d') }}" class="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-800">
                    <b>Info Sistem:</b> Proses ini akan otomatis membuat Jurnal (Debit: Hutang Usaha, Kredit: Kas/Bank).
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('billModal').classList.add('hidden')" class="px-4 py-2 font-bold text-slate-600">Batal</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold">Bayar Tagihan Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection --}}

{{-- <!-- resources/views/purchases/bill.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">
    <div class="flex justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="file-text" class="text-blue-600 w-7 h-7"></i> Tagihan & Pembayaran (Bill)
            </h1>
            <p class="text-slate-500 mt-1">Kelola tagihan dari Pemasok dan potong saldo bank otomatis</p>
        </div>

        <!-- CEK HAK AKSES UPDATE (U) -->
        @if(\App\Models\CompanySetting::canAction('U', 'Pembelian'))
        <button onclick="document.getElementById('billModal').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2">
            <i data-feather="plus-circle" class="w-4 h-4"></i> Proses Pembayaran
        </button>
        @endif
    </div>

    <!-- Tabel Riwayat Tagihan Lunas -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b">
                <tr>
                    <th class="p-4 text-sm">No. Referensi</th>
                    <th class="p-4 text-sm">Supplier</th>
                    <th class="p-4 text-sm">Tgl Lunas</th>
                    <th class="p-4 text-sm">Bank Pembayar</th>
                    <th class="p-4 text-sm text-right">Total Dibayar</th>
                    <th class="p-4 text-sm text-center">Status</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bills as $bill)
                <tr class="border-b hover:bg-slate-50">
                    <td class="p-4 font-bold">{{ $bill->reference_no ?: 'PO #'.$bill->id }}</td>
                    <td class="p-4 font-bold text-blue-700">{{ $bill->contact->name }}</td>
                    <td class="p-4">{{ \Carbon\Carbon::parse($bill->due_date)->format('d/m/Y') }}</td>
                    <td class="p-4 font-bold text-slate-700">{{ $bill->paymentAccount->name ?? 'Tidak Tercatat' }}</td>
                    <td class="p-4 font-bold text-right">Rp {{ number_format($bill->total, 0, ',', '.') }}</td>
                    <td class="p-4 text-center"><span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Lunas</span></td>
                    <td class="p-4 text-center">
                        <button onclick="document.getElementById('detailModal_{{ $bill->id }}').classList.remove('hidden')" class="text-blue-600 hover:bg-blue-50 p-2 rounded-lg transition-colors" title="Lihat Detail Barang">
                            <i data-feather="eye" class="w-4 h-4"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="p-8 text-center text-slate-500">Belum ada riwayat tagihan terbayar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PERULANGAN MODAL DETAIL -->
    @foreach($bills as $bill)
    <div id="detailModal_{{ $bill->id }}" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-[70] p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 class="text-xl font-bold text-slate-800">Rincian Invoice: {{ $bill->reference_no ?: '#'.$bill->id }}</h3>
                <button onclick="document.getElementById('detailModal_{{ $bill->id }}').classList.add('hidden')" class="text-slate-400 hover:text-slate-700"><i data-feather="x"></i></button>
            </div>
            <div class="p-6 overflow-y-auto custom-scrollbar">
                <table class="w-full text-left border-collapse border border-slate-300">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="p-3 border text-sm font-semibold">Produk</th>
                            <th class="p-3 border text-sm font-semibold text-center">Qty</th>
                            <th class="p-3 border text-sm font-semibold text-right">Harga Beli</th>
                            <th class="p-3 border text-sm font-semibold text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bill->items as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="p-3 border text-sm font-medium">{{ $item->product->name ?? $item->product_sku }}</td>
                            <td class="p-3 border text-sm text-center font-bold text-slate-700">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                            <td class="p-3 border text-sm text-right text-slate-600">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="p-3 border text-sm text-right font-bold text-slate-800">Rp {{ number_format($item->sub_total, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="p-3 border text-right font-bold text-slate-600 uppercase">Grand Total Keseluruhan</th>
                            <th class="p-3 border text-right font-bold text-green-700 text-lg">Rp {{ number_format($bill->total, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Modal Form Bayar Tagihan -->
    <div id="billModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl w-full max-w-2xl p-6">
            <div class="flex justify-between mb-4">
                <h3 class="text-xl font-bold text-slate-800">Proses Pembayaran Tagihan</h3>
                <button onclick="document.getElementById('billModal').classList.add('hidden')" class="text-slate-400 hover:text-red-500"><i data-feather="x"></i></button>
            </div>

            <form action="#" method="POST" id="billForm">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Penerimaan Barang (Belum Lunas)</label>
                        <select name="po_id" required onchange="document.getElementById('billForm').action = '/purchases/' + this.value + '/bill';" class="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" disabled selected>-- Pilih Dokumen Pengiriman Barang --</option>
                            @foreach($readyDeliveries as $po)
                                <option value="{{ $po->id }}">[{{ $po->reference_no ?: 'PO #'.$po->id }}] {{ $po->contact->name }} - Rp {{ number_format($po->total, 0, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Bayar Dari Rekening</label>
                            <select name="payment_account" required class="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="" disabled selected>-- Pilih Sumber Dana --</option>
                                @foreach($banks as $bank) <option value="{{ $bank->code }}">[{{ $bank->code }}] {{ $bank->name }}</option> @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal Lunas</label>
                            <input type="date" name="due_date" required value="{{ date('Y-m-d') }}" class="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-800">
                    <b>Info Sistem:</b> Proses ini akan otomatis membuat Jurnal (Debit: Hutang Usaha, Kredit: Kas/Bank).
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('billModal').classList.add('hidden')" class="px-4 py-2 font-bold text-slate-600">Batal</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold">Bayar Tagihan Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection --}}

@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10 relative">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="file-text" class="text-blue-600 w-7 h-7"></i> Tagihan & Pembayaran (Bill)
            </h1>
            <p class="text-slate-500 mt-1">Kelola tagihan dari Pemasok dan potong saldo bank otomatis</p>
        </div>

        <!-- CEK HAK AKSES UPDATE (U) -->
        @if(\App\Models\CompanySetting::canAction('U', 'Pembelian'))
        <button onclick="openBillModal()" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-bold flex items-center justify-center gap-2 shadow-sm transition-colors">
            <i data-feather="plus-circle" class="w-4 h-4"></i> Proses Pembayaran
        </button>
        @endif
    </div>

    <!-- Tabel Riwayat Tagihan Lunas -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead class="bg-slate-50 border-b">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600">No. Referensi</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Supplier</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Tgl Lunas</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Bank Pembayar</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Total Dibayar</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Status</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bills as $bill)
                    <tr class="border-b hover:bg-slate-50 transition-colors">
                        <td class="p-4 font-bold text-sm text-slate-800">{{ $bill->reference_no ?: 'PO #'.$bill->id }}</td>
                        <td class="p-4 font-bold text-sm text-blue-700">{{ $bill->contact->name }}</td>
                        <td class="p-4 text-sm font-medium text-slate-700">{{ \Carbon\Carbon::parse($bill->due_date)->format('d/m/Y') }}</td>
                        <td class="p-4 font-bold text-sm text-slate-700">{{ $bill->paymentAccount->name ?? 'Tidak Tercatat' }}</td>
                        <td class="p-4 font-black text-sm text-slate-800 text-right">Rp {{ number_format($bill->total, 0, ',', '.') }}</td>
                        <td class="p-4 text-center">
                            <span class="px-3 py-1 bg-green-100 text-green-700 border border-green-200 rounded-full text-xs font-bold">Lunas</span>
                        </td>
                        <td class="p-4 text-center">
                            <button onclick="openDetailModal({{ $bill->id }})" class="text-blue-600 hover:bg-blue-50 p-2 rounded-lg transition-colors border border-transparent hover:border-blue-200 shadow-sm" title="Lihat Detail Barang">
                                <i data-feather="eye" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="p-10 text-center text-slate-500">Belum ada riwayat tagihan terbayar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- PERULANGAN MODAL DETAIL (BERANIMASI) -->
    @foreach($bills as $bill)
    <div id="detailModal_{{ $bill->id }}" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-[70] p-4 backdrop-blur-sm transition-all">
        <div id="detailModalContent_{{ $bill->id }}" class="bg-white rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh] border-t-4 border-blue-600 transform transition-transform scale-95 origin-center">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 class="text-xl font-bold text-slate-800">Rincian Invoice: {{ $bill->reference_no ?: '#'.$bill->id }}</h3>
                <button onclick="closeDetailModal({{ $bill->id }})" class="text-slate-400 hover:text-red-500 bg-white rounded-full p-1 shadow-sm transition-colors"><i data-feather="x"></i></button>
            </div>
            <div class="p-6 overflow-y-auto custom-scrollbar">
                <div class="overflow-x-auto border border-slate-300 rounded-lg">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-100">
                            <tr>
                                <th class="p-3 border-b text-sm font-semibold">Produk</th>
                                <th class="p-3 border-b text-sm font-semibold text-center">Qty</th>
                                <th class="p-3 border-b text-sm font-semibold text-right">Harga Beli</th>
                                <th class="p-3 border-b text-sm font-semibold text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bill->items as $item)
                            <tr class="hover:bg-slate-50">
                                <td class="p-3 border-b border-slate-100 text-sm font-medium">{{ $item->product->name ?? $item->product_sku }}</td>
                                <td class="p-3 border-b border-slate-100 text-sm text-center font-bold text-slate-700">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                                <td class="p-3 border-b border-slate-100 text-sm text-right text-slate-600">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="p-3 border-b border-slate-100 text-sm text-right font-bold text-slate-800">Rp {{ number_format($item->sub_total, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-50">
                                <th colspan="3" class="p-3 text-right font-bold text-slate-600 uppercase">Grand Total Keseluruhan</th>
                                <th class="p-3 text-right font-black text-green-700 text-lg">Rp {{ number_format($bill->total, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Modal Form Bayar Tagihan (Beranimasi & Swal) -->
    <div id="billModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 backdrop-blur-sm transition-all">
        <div id="billModalContent" class="bg-white rounded-xl w-full max-w-2xl overflow-hidden flex flex-col border-t-4 border-blue-600 shadow-2xl transform transition-transform scale-95 origin-center">

            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 class="text-xl font-bold text-slate-800">Proses Pembayaran Tagihan</h3>
                <button type="button" onclick="closeBillModal()" class="text-slate-400 hover:text-red-500 bg-white rounded-full p-1 shadow-sm transition-colors"><i data-feather="x"></i></button>
            </div>

            <form action="#" method="POST" id="billForm" class="p-6 space-y-4" onsubmit="confirmPayment(event, this)">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Penerimaan Barang (Hutang Belum Lunas)</label>
                        <select name="po_id" id="poDropdown" required onchange="updateFormAction(this)" class="w-full border border-blue-200 rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500 font-semibold text-slate-700 shadow-sm">
                            <option value="" disabled selected>-- Pilih Dokumen Pengiriman Barang --</option>
                            @foreach($readyDeliveries as $po)
                                <option value="{{ $po->id }}">[{{ $po->reference_no ?: 'PO #'.$po->id }}] {{ $po->contact->name }} - Rp {{ number_format($po->total, 0, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Bayar Dari Rekening</label>
                            <select name="payment_account" required class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500 font-bold text-slate-800 shadow-sm bg-slate-50">
                                <option value="" disabled selected>-- Pilih Sumber Dana --</option>
                                @foreach($banks as $bank)
                                    <option value="{{ $bank->code }}">[{{ $bank->code }}] {{ $bank->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal Lunas</label>
                            <input type="date" name="due_date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500 font-medium shadow-sm">
                        </div>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-orange-50 border border-orange-200 rounded-lg text-sm text-orange-900 leading-relaxed shadow-sm">
                    <b>Info Akuntansi:</b> Menyelesaikan proses ini akan memotong saldo Bank/Kas secara otomatis dan mencetak Jurnal Pelunasan Hutang Usaha ke Buku Besar.
                </div>

                <div class="flex justify-end gap-3 mt-6 border-t border-slate-100 pt-4">
                    <button type="button" onclick="closeBillModal()" class="px-5 py-2.5 font-bold text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-bold shadow-md hover:bg-blue-700 transition-colors flex items-center gap-2">
                        <i data-feather="check-circle" class="w-4 h-4"></i> Bayar Tagihan Sekarang
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
                title: 'Pembayaran Sukses!',
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
                title: 'Gagal Memproses!',
                text: '{{ session('error') }}',
                timer: 5000,
                timerProgressBar: true,
                showConfirmButton: true,
                confirmButtonColor: '#ef4444'
            });
        @endif
    });

    // --- SWEETALERT2: KONFIRMASI PELUNASAN ---
    function confirmPayment(event, form) {
        event.preventDefault();

        // Ambil nama supplier dari dropdown (opsional untuk ditampilkan)
        const dropdown = document.getElementById('poDropdown');
        const selectedText = dropdown.options[dropdown.selectedIndex].text;

        Swal.fire({
            title: 'Keluarkan Uang Kas?',
            html: `Anda akan mencatat pembayaran untuk tagihan:<br><b class="text-blue-700 mt-2 block">${selectedText}</b><br><span class="text-sm text-slate-500 font-medium mt-3 block">Sistem akan secara otomatis memotong dana dari Buku Besar Bank/Kas yang dipilih. Lanjutkan?</span>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb', // Blue-600
            cancelButtonColor: '#64748b',  // Slate
            confirmButtonText: '<i data-feather="check-circle" class="w-4 h-4 inline mr-1"></i> Ya, Bayar Sekarang',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            didOpen: () => { if (typeof feather !== 'undefined') feather.replace(); }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Mencatat Pengeluaran...',
                    text: 'Mohon tunggu sebentar, sistem sedang membukukan pengeluaran bank.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                form.submit();
            }
        });
    }

    // --- UBAH ACTION FORM BERDASARKAN DROPDOWN ---
    function updateFormAction(selectElement) {
        document.getElementById('billForm').action = '/purchases/' + selectElement.value + '/bill';
    }

    // --- LOGIKA MODAL (ANIMASI CSS SCALE) ---
    function openBillModal() {
        const modal = document.getElementById('billModal');
        const content = document.getElementById('billModalContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    }

    function closeBillModal() {
        const modal = document.getElementById('billModal');
        const content = document.getElementById('billModalContent');
        content.classList.remove('scale-100');
        content.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.getElementById('billForm').reset();
            // Reset action URL jika batal
            document.getElementById('billForm').action = '#';
        }, 150);
    }

    function openDetailModal(id) {
        const modal = document.getElementById(`detailModal_${id}`);
        const content = document.getElementById(`detailModalContent_${id}`);
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    }

    function closeDetailModal(id) {
        const modal = document.getElementById(`detailModal_${id}`);
        const content = document.getElementById(`detailModalContent_${id}`);
        content.classList.remove('scale-100');
        content.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); }, 150);
    }
</script>
@endsection
