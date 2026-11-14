{{-- <!-- resources/views/sales/invoice.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">
    <div class="flex justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="file-text" class="text-rose-600 w-7 h-7"></i> Invoice & Penerimaan Dana
            </h1>
        </div>
        <button onclick="document.getElementById('invModal').classList.remove('hidden')" class="bg-rose-600 hover:bg-rose-700 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2">
            <i data-feather="plus-circle" class="w-4 h-4"></i> Terima Pembayaran
        </button>
    </div>

    <!-- Tabel Riwayat Tagihan Lunas -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b">
                <tr>
                    <th class="p-4 text-sm">No. Referensi</th>
                    <th class="p-4 text-sm">Pelanggan</th>
                    <th class="p-4 text-sm">Bank Penerima</th>
                    <th class="p-4 text-sm text-right">Total Diterima</th>
                    <th class="p-4 text-sm text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                <tr class="border-b">
                    <td class="p-4 font-bold">{{ $inv->reference_no ?: 'SO #'.$inv->id }}</td>
                    <td class="p-4 font-bold text-rose-700">{{ $inv->contact->name }}</td>
                    <td class="p-4 font-bold text-slate-700">{{ $inv->paymentAccount->name ?? 'Tidak Tercatat' }}</td>
                    <td class="p-4 font-bold text-right">Rp {{ number_format($inv->total, 0, ',', '.') }}</td>
                    <td class="p-4 text-center"><span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Lunas</span></td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-8 text-center text-slate-500">Belum ada invoice penjualan terbayar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Form Bayar Tagihan -->
    <div id="invModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl w-full max-w-2xl p-6">
            <div class="flex justify-between mb-4">
                <h3 class="text-xl font-bold text-slate-800">Terima Pembayaran Invoice Penjualan</h3>
                <button onclick="document.getElementById('invModal').classList.add('hidden')" class="text-slate-400 hover:text-red-500"><i data-feather="x"></i></button>
            </div>

            <form action="#" method="POST" id="invForm">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Pengiriman Barang (Belum Lunas)</label>
                        <select name="so_id" required onchange="document.getElementById('invForm').action = '/sales/' + this.value + '/invoice';" class="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-rose-500">
                            <option value="" disabled selected>-- Pilih Dokumen Pengiriman Barang --</option>
                            @foreach($readyDeliveries as $so)
                                <option value="{{ $so->id }}">[SO #{{ $so->id }}] {{ $so->contact->name }} - Rp {{ number_format($so->total, 0, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Simpan Dana ke Rekening (Kas/Bank)</label>
                        <select name="payment_account" required class="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-rose-500">
                            <option value="" disabled selected>-- Pilih Rekening Penerima --</option>
                            @foreach($banks as $bank) <option value="{{ $bank->code }}">[{{ $bank->code }}] {{ $bank->name }}</option> @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4 p-4 bg-rose-50 border border-rose-200 rounded-lg text-sm text-rose-800">
                    <b>Info Akuntansi:</b> Menyelesaikan proses ini akan otomatis membuat 2 Pasang Jurnal (Bank ke Pendapatan & HPP ke Persediaan).
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('invModal').classList.add('hidden')" class="px-4 py-2 font-bold text-slate-600">Batal</button>
                    <button type="submit" class="bg-rose-600 text-white px-5 py-2 rounded-lg font-bold">Terima Pembayaran & Jurnal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection --}}

{{-- <!-- resources/views/sales/invoice.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">
    <div class="flex justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="file-text" class="text-rose-600 w-7 h-7"></i> Invoice & Penerimaan Dana
            </h1>
        </div>

        <!-- CEK HAK AKSES UPDATE (U) -->
        @if(\App\Models\CompanySetting::canAction('U', 'Penjualan'))
        <button onclick="document.getElementById('invModal').classList.remove('hidden')" class="bg-rose-600 hover:bg-rose-700 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2">
            <i data-feather="plus-circle" class="w-4 h-4"></i> Terima Pembayaran
        </button>
        @endif
    </div>

    <!-- Tabel Riwayat Tagihan Lunas -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b">
                <tr>
                    <th class="p-4 text-sm">No. Referensi</th>
                    <th class="p-4 text-sm">Pelanggan</th>
                    <th class="p-4 text-sm">Bank Penerima</th>
                    <th class="p-4 text-sm text-right">Total Diterima</th>
                    <th class="p-4 text-sm text-center">Status</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                <tr class="border-b">
                    <td class="p-4 font-bold">{{ $inv->reference_no ?: 'SO #'.$inv->id }}</td>
                    <td class="p-4 font-bold text-rose-700">{{ $inv->contact->name }}</td>
                    <td class="p-4 font-bold text-slate-700">{{ $inv->paymentAccount->name ?? 'Tidak Tercatat' }}</td>
                    <td class="p-4 font-bold text-right">Rp {{ number_format($inv->total, 0, ',', '.') }}</td>
                    <td class="p-4 text-center"><span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Lunas</span></td>
                    <td class="p-4 text-center">
                        <button onclick="document.getElementById('detailModal_{{ $inv->id }}').classList.remove('hidden')" class="text-rose-600 hover:bg-rose-50 p-2 rounded-lg transition-colors" title="Lihat Detail Barang">
                            <i data-feather="eye" class="w-4 h-4"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="p-8 text-center text-slate-500">Belum ada invoice penjualan terbayar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PERULANGAN MODAL DETAIL INVOICE -->
    @foreach($invoices as $inv)
    <div id="detailModal_{{ $inv->id }}" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-[70] p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 class="text-xl font-bold text-slate-800">Rincian Tagihan GKP: {{ $inv->reference_no ?: '#'.$inv->id }}</h3>
                <button onclick="document.getElementById('detailModal_{{ $inv->id }}').classList.add('hidden')" class="text-slate-400 hover:text-slate-700"><i data-feather="x"></i></button>
            </div>
            <div class="p-6 overflow-y-auto custom-scrollbar">
                <table class="w-full text-left border-collapse border border-slate-300">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="p-3 border text-sm font-semibold">Produk</th>
                            <th class="p-3 border text-sm font-semibold text-center">Qty Tagihan</th>
                            <th class="p-3 border text-sm font-semibold text-right">Harga Jual</th>
                            <th class="p-3 border text-sm font-semibold text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inv->items as $item)
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
                            <th class="p-3 border text-right font-bold text-rose-700 text-lg">Rp {{ number_format($inv->total, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Modal Form Bayar Tagihan -->
    <div id="invModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl w-full max-w-2xl p-6">
            <div class="flex justify-between mb-4">
                <h3 class="text-xl font-bold text-slate-800">Terima Pembayaran Invoice Penjualan</h3>
                <button onclick="document.getElementById('invModal').classList.add('hidden')" class="text-slate-400 hover:text-red-500"><i data-feather="x"></i></button>
            </div>

            <form action="#" method="POST" id="invForm">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Pengiriman Barang (Belum Lunas)</label>
                        <select name="so_id" required onchange="document.getElementById('invForm').action = '/sales/' + this.value + '/invoice';" class="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-rose-500">
                            <option value="" disabled selected>-- Pilih Dokumen Pengiriman Barang --</option>
                            @foreach($readyDeliveries as $so)
                                <option value="{{ $so->id }}">[{{ $so->reference_no ?: 'SO #'.$so->id }}] {{ $so->contact->name }} - Rp {{ number_format($so->total, 0, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Simpan Dana ke Rekening (Kas/Bank)</label>
                        <select name="payment_account" required class="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-rose-500">
                            <option value="" disabled selected>-- Pilih Rekening Penerima --</option>
                            @foreach($banks as $bank) <option value="{{ $bank->code }}">[{{ $bank->code }}] {{ $bank->name }}</option> @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4 p-4 bg-rose-50 border border-rose-200 rounded-lg text-sm text-rose-800">
                    <b>Info Akuntansi:</b> Menyelesaikan proses ini akan otomatis membuat 2 Pasang Jurnal (Bank ke Pendapatan & HPP ke Persediaan).
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('invModal').classList.add('hidden')" class="px-4 py-2 font-bold text-slate-600">Batal</button>
                    <button type="submit" class="bg-rose-600 text-white px-5 py-2 rounded-lg font-bold">Terima Pembayaran & Jurnal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection --}}

@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10 relative">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="file-text" class="text-rose-600 w-7 h-7"></i> Invoice & Penerimaan Dana
            </h1>
            <p class="text-slate-500 mt-1">Kelola pelunasan piutang pelanggan dan pembukuan jurnal bank</p>
        </div>

        <!-- CEK HAK AKSES UPDATE (U) -->
        @if(\App\Models\CompanySetting::canAction('U', 'Penjualan'))
        <button onclick="openInvoiceModal()" class="w-full sm:w-auto bg-rose-600 hover:bg-rose-700 text-white px-5 py-2.5 rounded-lg font-bold flex items-center justify-center gap-2 shadow-sm transition-colors">
            <i data-feather="plus-circle" class="w-4 h-4"></i> Terima Pembayaran
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
                        <th class="p-4 text-sm font-semibold text-slate-600">Pelanggan</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Bank Penerima</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Total Diterima</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Status</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                    <tr class="border-b hover:bg-slate-50 transition-colors">
                        <td class="p-4 font-bold text-sm text-slate-800">{{ $inv->reference_no ?: 'SO #'.$inv->id }}</td>
                        <td class="p-4 font-bold text-sm text-rose-700">{{ $inv->contact->name }}</td>
                        <td class="p-4 font-bold text-sm text-slate-600">{{ $inv->paymentAccount->name ?? 'Tidak Tercatat' }}</td>
                        <td class="p-4 font-black text-sm text-slate-800 text-right">Rp {{ number_format($inv->total, 0, ',', '.') }}</td>
                        <td class="p-4 text-center">
                            <span class="px-3 py-1 bg-green-100 text-green-700 border border-green-200 rounded-full text-xs font-bold">Lunas</span>
                        </td>
                        <td class="p-4 text-center">
                            <button onclick="openDetailModal({{ $inv->id }})" class="text-rose-600 hover:bg-rose-50 p-2 rounded-lg transition-colors border border-transparent hover:border-rose-200 shadow-sm" title="Lihat Detail Barang">
                                <i data-feather="eye" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="p-10 text-center text-slate-500">Belum ada invoice penjualan yang terbayar lunas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- PERULANGAN MODAL DETAIL INVOICE (BERANIMASI) -->
    @foreach($invoices as $inv)
    <div id="detailModal_{{ $inv->id }}" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-[70] p-4 backdrop-blur-sm transition-all">
        <div id="detailModalContent_{{ $inv->id }}" class="bg-white rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh] border-t-4 border-rose-600 transform transition-transform scale-95 origin-center">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 class="text-xl font-bold text-slate-800">Rincian Tagihan GKP: {{ $inv->reference_no ?: '#'.$inv->id }}</h3>
                <button onclick="closeDetailModal({{ $inv->id }})" class="text-slate-400 hover:text-slate-700 transition-colors"><i data-feather="x"></i></button>
            </div>
            <div class="p-6 overflow-y-auto custom-scrollbar">
                <div class="overflow-x-auto border border-slate-300 rounded-lg">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-100">
                            <tr>
                                <th class="p-3 border-b text-sm font-semibold">Produk</th>
                                <th class="p-3 border-b text-sm font-semibold text-center">Qty Tagihan</th>
                                <th class="p-3 border-b text-sm font-semibold text-right">Harga Jual</th>
                                <th class="p-3 border-b text-sm font-semibold text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inv->items as $item)
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
                                <th class="p-3 text-right font-black text-rose-700 text-lg">Rp {{ number_format($inv->total, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Modal Form Bayar Tagihan (Beranimasi & Swal) -->
    <div id="invModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 backdrop-blur-sm transition-all">
        <div id="invModalContent" class="bg-white rounded-xl w-full max-w-2xl overflow-hidden flex flex-col border-t-4 border-rose-600 shadow-2xl transform transition-transform scale-95 origin-center">

            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 class="text-xl font-bold text-slate-800">Terima Pembayaran Invoice Penjualan</h3>
                <button type="button" onclick="closeInvoiceModal()" class="text-slate-400 hover:text-red-500 bg-white rounded-full p-1 shadow-sm transition-colors"><i data-feather="x"></i></button>
            </div>

            <form action="#" method="POST" id="invForm" class="p-6 space-y-4" onsubmit="confirmPayment(event, this)">
                @csrf
                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Pengiriman Barang (Piutang Menunggak)</label>
                        <select name="so_id" id="soDropdown" required onchange="updateFormAction(this)" class="w-full border border-rose-200 rounded-lg p-3 outline-none focus:ring-2 focus:ring-rose-500 font-semibold text-slate-700 shadow-sm">
                            <option value="" disabled selected>-- Pilih Dokumen Pengiriman Barang --</option>
                            @foreach($readyDeliveries as $so)
                                <option value="{{ $so->id }}">[{{ $so->reference_no ?: 'SO #'.$so->id }}] {{ $so->contact->name }} - Rp {{ number_format($so->total, 0, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Simpan Dana ke Rekening (Kas/Bank)</label>
                        <select name="payment_account" required class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-rose-500 font-bold text-slate-800 shadow-sm bg-slate-50">
                            <option value="" disabled selected>-- Pilih Rekening Penerima --</option>
                            @foreach($banks as $bank)
                                <option value="{{ $bank->code }}">[{{ $bank->code }}] {{ $bank->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-rose-50 border border-rose-200 rounded-lg text-sm text-rose-800 leading-relaxed shadow-sm">
                    <b>Info Akuntansi:</b> Menyelesaikan proses ini akan otomatis membuat Jurnal Penerimaan Uang di Buku Besar dan menghapus status tunggakan (*Aging*) pelanggan terkait.
                </div>

                <div class="flex justify-end gap-3 mt-6 border-t border-slate-100 pt-4">
                    <button type="button" onclick="closeInvoiceModal()" class="px-5 py-2.5 font-bold text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">Batal</button>
                    <button type="submit" class="bg-rose-600 text-white px-6 py-2.5 rounded-lg font-bold shadow-md hover:bg-rose-700 transition-colors flex items-center gap-2">
                        <i data-feather="check-circle" class="w-4 h-4"></i> Terima Pembayaran & Jurnal
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
                title: 'Transaksi Sukses!',
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

        // Ambil nama pelanggan dari dropdown (opsional untuk ditampilkan)
        const dropdown = document.getElementById('soDropdown');
        const selectedText = dropdown.options[dropdown.selectedIndex].text;

        Swal.fire({
            title: 'Terima Pembayaran?',
            html: `Anda akan mencatat pelunasan untuk tagihan:<br><b class="text-rose-700 mt-2 block">${selectedText}</b><br><span class="text-sm text-slate-500 font-medium mt-3 block">Sistem akan secara otomatis menyetor dana ke Buku Besar Bank yang dipilih. Lanjutkan?</span>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#e11d48', // Rose-600
            cancelButtonColor: '#64748b',  // Slate
            confirmButtonText: '<i data-feather="check-circle" class="w-4 h-4 inline mr-1"></i> Ya, Proses Pelunasan',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            didOpen: () => { if (typeof feather !== 'undefined') feather.replace(); }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Mencatat Jurnal Bank...',
                    text: 'Mohon tunggu sebentar, sistem sedang membukukan pendapatan.',
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
        document.getElementById('invForm').action = '/sales/' + selectElement.value + '/invoice';
    }

    // --- LOGIKA MODAL (ANIMASI CSS SCALE) ---
    function openInvoiceModal() {
        const modal = document.getElementById('invModal');
        const content = document.getElementById('invModalContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    }

    function closeInvoiceModal() {
        const modal = document.getElementById('invModal');
        const content = document.getElementById('invModalContent');
        content.classList.remove('scale-100');
        content.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.getElementById('invForm').reset();
            // Reset action URL jika batal
            document.getElementById('invForm').action = '#';
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
