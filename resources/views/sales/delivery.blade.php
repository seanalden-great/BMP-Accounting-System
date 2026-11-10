{{-- <!-- resources/views/sales/delivery.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">
    <div class="flex justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="truck" class="text-purple-600 w-7 h-7"></i> Pengiriman Penjualan
            </h1>
        </div>
        <button onclick="document.getElementById('delModal').classList.remove('hidden')" class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2">
            <i data-feather="send" class="w-4 h-4"></i> Proses Pengiriman
        </button>
    </div>

    <!-- Tabel Riwayat (Sama seperti SO List, hanya beda data $deliveries) -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b">
                <tr><th class="p-4">Tanggal SO</th><th class="p-4">Pelanggan</th><th class="p-4">Status</th></tr>
            </thead>
            <tbody>
                @foreach ($deliveries as $del)
                <tr class="border-b">
                    <td class="p-4">{{ \Carbon\Carbon::parse($del->date)->format('d/m/Y') }}</td>
                    <td class="p-4 font-bold">{{ $del->contact->name }}</td>
                    <td class="p-4"><span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-bold">Terkirim</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Form -->
    <div id="delModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl w-full max-w-lg p-6">
            <h3 class="text-xl font-bold text-slate-800 mb-4">Kirim Barang ke Pelanggan</h3>
            <form action="#" method="POST" id="receiveForm">
                @csrf
                <select name="so_id" required onchange="document.getElementById('receiveForm').action = '/sales/' + this.value + '/deliver';" class="w-full border border-purple-200 rounded-lg p-3 outline-none mb-4">
                    <option value="" disabled selected>-- Pilih SO yang Akan Dikirim --</option>
                    @foreach ($pendingOrders as $so) <option value="{{ $so->id }}">[SO #{{ $so->id }}] {{ $so->contact->name }}</option> @endforeach
                </select>
                <div class="mt-4 p-4 bg-orange-50 border border-orange-200 rounded-lg text-sm text-orange-800">
                    <b>Info Sistem:</b> Proses ini akan otomatis mengurangi Stok Gudang Anda.
                </div>
                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" onclick="document.getElementById('delModal').classList.add('hidden')" class="px-4 py-2 font-bold text-slate-600">Batal</button>
                    <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg font-bold">Kirim & Kurangi Stok</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection --}}

{{-- <!-- resources/views/sales/delivery.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">
    <div class="flex justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="truck" class="text-purple-600 w-7 h-7"></i> Pengiriman Penjualan
            </h1>
            <p class="text-slate-500 mt-1">Sistem menggunakan alokasi FIFO (First-In, First-Out) lintas gudang.</p>
        </div>
        <button onclick="document.getElementById('delModal').classList.remove('hidden')" class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2">
            <i data-feather="send" class="w-4 h-4"></i> Proses Pengiriman
        </button>
    </div>

    <!-- Tabel Riwayat (Sama seperti SO List, hanya beda data $deliveries) -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b">
                <tr>
                    <th class="p-4 w-32">Tanggal SO</th>
                    <th class="p-4 w-48">Pelanggan</th>
                    <th class="p-4">Catatan Rincian Pengambilan Gudang (FIFO)</th>
                    <th class="p-4 w-32 text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($deliveries as $del)
                <tr class="border-b hover:bg-slate-50">
                    <td class="p-4 text-sm">{{ \Carbon\Carbon::parse($del->date)->format('d/m/Y') }}</td>
                    <td class="p-4 font-bold text-sm">{{ $del->contact->name }}</td>

                    <!-- Menampilkan hasil laporan cerdas FIFO dari kolom notes -->
                    <td class="p-4 text-xs font-medium text-slate-600 leading-relaxed whitespace-pre-wrap">{{ $del->notes ?: 'Tidak ada catatan' }}</td>

                    <td class="p-4 text-center"><span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-bold">Terkirim</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Form -->
    <div id="delModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl w-full max-w-lg p-6">
            <h3 class="text-xl font-bold text-slate-800 mb-4">Kirim Barang ke Pelanggan</h3>
            <form action="#" method="POST" id="receiveForm">
                @csrf
                <select name="so_id" required onchange="document.getElementById('receiveForm').action = '/sales/' + this.value + '/deliver';" class="w-full border border-purple-200 rounded-lg p-3 outline-none mb-4 font-semibold text-slate-700">
                    <option value="" disabled selected>-- Pilih SO yang Akan Dikirim --</option>
                    @foreach ($pendingOrders as $so) <option value="{{ $so->id }}">[SO #{{ $so->id }}] {{ $so->contact->name }}</option> @endforeach
                </select>
                <div class="mt-4 p-4 bg-orange-50 border border-orange-200 rounded-lg text-sm text-orange-900 leading-relaxed">
                    <b>Info Sistem Stok:</b><br/>
                    Sistem akan memotong stok secara <b>Otomatis & FIFO</b> (Barang paling lama masuk, akan dikeluarkan lebih dulu). Laporan gudang mana saja yang terpotong akan muncul di kolom catatan.
                </div>
                <div class="flex justify-end gap-3 mt-5">
                    <button type="button" onclick="document.getElementById('delModal').classList.add('hidden')" class="px-4 py-2 font-bold text-slate-600">Batal</button>
                    <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg font-bold shadow hover:bg-purple-700">Kirim & Kurangi Stok FIFO</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection --}}

<!-- resources/views/sales/delivery.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="space-y-6 pb-10">
        <div class="flex justify-between items-center gap-4">
            {{-- <div>
                <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                    <i data-feather="truck" class="text-purple-600 w-7 h-7"></i> Pengiriman Penjualan
                </h1>
                <p class="text-slate-500 mt-1">Sistem menggunakan alokasi FIFO (First-In, First-Out) lintas gudang.</p>
            </div>
            <button onclick="document.getElementById('delModal').classList.remove('hidden')"
                class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2">
                <i data-feather="send" class="w-4 h-4"></i> Proses Pengiriman
            </button> --}}

            <div>
                <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                    <i data-feather="truck" class="text-purple-600 w-7 h-7"></i> Pengiriman Penjualan
                </h1>
                <p class="text-slate-500 mt-1">Sistem menggunakan alokasi FIFO (First-In, First-Out) lintas gudang.</p>
            </div>

            <!-- CEK HAK AKSES UPDATE (U) -->
            @if(\App\Models\CompanySetting::canAction('U', 'Penjualan'))
            <button onclick="document.getElementById('delModal').classList.remove('hidden')"
                class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2">
                <i data-feather="send" class="w-4 h-4"></i> Proses Pengiriman
            </button>
            @endif
        </div>

        <!-- Tabel Riwayat (Surat Jalan) -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 border-b">
                    <tr>
                        <th class="p-4 w-32">Tanggal SO</th>
                        <th class="p-4 w-48">Pelanggan</th>
                        <th class="p-4">Catatan Rincian Pengambilan Gudang (FIFO)</th>
                        <th class="p-4 w-32 text-center">Status</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($deliveries as $del)
                        <tr class="border-b hover:bg-slate-50">
                            <td class="p-4 text-sm">{{ \Carbon\Carbon::parse($del->date)->format('d/m/Y') }}</td>
                            <td class="p-4 font-bold text-sm">{{ $del->contact->name }}</td>
                            <td class="p-4 text-xs font-medium text-slate-600 leading-relaxed whitespace-pre-wrap">
                                {{ $del->notes ?: 'Tidak ada catatan' }}</td>
                            <td class="p-4 text-center"><span
                                    class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-bold">Terkirim</span>
                            </td>
                            {{-- <td class="p-4 text-center">
                                <button
                                    onclick="document.getElementById('detailModal_{{ $del->id }}').classList.remove('hidden')"
                                    class="text-purple-600 hover:bg-purple-50 p-2 rounded-lg transition-colors"
                                    title="Lihat Detail Barang">
                                    <i data-feather="eye" class="w-4 h-4"></i>
                                </button>
                            </td> --}}
                            {{-- <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        onclick="document.getElementById('detailModal_{{ $del->id }}').classList.remove('hidden')"
                                        class="text-purple-600 hover:bg-purple-50 p-2 rounded-lg transition-colors"
                                        title="Lihat Detail Barang">
                                        <i data-feather="eye" class="w-4 h-4"></i>
                                    </button>

                                    <!-- TOMBOL CETAK SURAT JALAN (BARU) -->
                                    <a href="{{ route('sales.deliveryNote', $del->id) }}" target="_blank"
                                        class="text-slate-500 hover:text-slate-800 hover:bg-slate-100 p-2 rounded-lg transition-colors border border-slate-200 shadow-sm"
                                        title="Cetak Surat Jalan / Delivery Note">
                                        <i data-feather="printer" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td> --}}

                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="document.getElementById('detailModal_{{ $del->id }}').classList.remove('hidden')" class="text-purple-600 hover:bg-purple-50 p-2 rounded-lg transition-colors" title="Lihat Detail Barang">
                                        <i data-feather="eye" class="w-4 h-4"></i>
                                    </button>

                                    <!-- TOMBOL CETAK SURAT JALAN -->
                                    <a href="{{ route('sales.deliveryNote', $del->id) }}" target="_blank" class="text-slate-500 hover:text-slate-800 hover:bg-slate-100 p-2 rounded-lg transition-colors border border-slate-200 shadow-sm" title="Cetak Surat Jalan / Delivery Note">
                                        <i data-feather="printer" class="w-4 h-4"></i>
                                    </a>

                                    <!-- CEK HAK AKSES UPDATE (U) UNTUK TOMBOL RETUR (BARU) -->
                                    @if(\App\Models\CompanySetting::canAction('U', 'Penjualan'))
                                    <button onclick="openReturnModal({{ $del->id }}, '{{ addslashes($del->contact->name) }}')" class="text-rose-500 hover:text-rose-700 hover:bg-rose-50 p-2 rounded-lg transition-colors border border-transparent hover:border-rose-200 shadow-sm" title="Retur Barang (Kembalikan ke Gudang & Buat Jurnal Pembalik)">
                                        <i data-feather="corner-down-left" class="w-4 h-4"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- PERULANGAN MODAL DETAIL SURAT JALAN -->
        @foreach ($deliveries as $del)
            <div id="detailModal_{{ $del->id }}"
                class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-[70] p-4">
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh]">
                    <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                        <h3 class="text-xl font-bold text-slate-800">Rincian Pengiriman:
                            {{ $del->reference_no ?: '#' . $del->id }}</h3>
                        <button onclick="document.getElementById('detailModal_{{ $del->id }}').classList.add('hidden')"
                            class="text-slate-400 hover:text-slate-700"><i data-feather="x"></i></button>
                    </div>
                    <div class="p-6 overflow-y-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse border border-slate-300">
                            <thead class="bg-slate-100">
                                <tr>
                                    <th class="p-3 border text-sm font-semibold">Produk</th>
                                    <th class="p-3 border text-sm font-semibold text-center">Qty Keluar</th>
                                    <th class="p-3 border text-sm font-semibold text-right">Harga Jual</th>
                                    <th class="p-3 border text-sm font-semibold text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($del->items as $item)
                                    <tr class="hover:bg-slate-50">
                                        <td class="p-3 border text-sm font-medium">
                                            {{ $item->product->name ?? $item->product_sku }}</td>
                                        <td class="p-3 border text-sm text-center font-bold text-slate-700">
                                            {{ number_format($item->quantity, 0, ',', '.') }}</td>
                                        <td class="p-3 border text-sm text-right text-slate-600">Rp
                                            {{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="p-3 border text-sm text-right font-bold text-slate-800">Rp
                                            {{ number_format($item->sub_total, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="p-3 border text-right font-bold text-slate-600 uppercase">
                                        Grand Total Keseluruhan</th>
                                    <th class="p-3 border text-right font-bold text-purple-700 text-lg">Rp
                                        {{ number_format($del->total, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Modal Form -->
        <div id="delModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl w-full max-w-lg p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-4">Kirim Barang ke Pelanggan</h3>
                <form action="#" method="POST" id="receiveForm">
                    @csrf
                    <select name="so_id" required
                        onchange="document.getElementById('receiveForm').action = '/sales/' + this.value + '/deliver';"
                        class="w-full border border-purple-200 rounded-lg p-3 outline-none mb-4 font-semibold text-slate-700">
                        <option value="" disabled selected>-- Pilih SO yang Akan Dikirim --</option>
                        @foreach ($pendingOrders as $so)
                            {{-- <option value="{{ $so->id }}">[SO #{{ $so->id }}] {{ $so->contact->name }}
                            </option> --}}
                            <option value="{{ $so->id }}">[{{ $so->reference_no ?: 'SO #'.$so->id }}] {{ $so->contact->name }}</option>
                        @endforeach
                    </select>
                    <div
                        class="mt-4 p-4 bg-orange-50 border border-orange-200 rounded-lg text-sm text-orange-900 leading-relaxed">
                        <b>Info Sistem Stok:</b><br />
                        Sistem akan memotong stok secara <b>Otomatis & FIFO</b> (Barang paling lama masuk, akan dikeluarkan
                        lebih dulu). Laporan gudang mana saja yang terpotong akan muncul di kolom catatan.
                    </div>
                    <div class="flex justify-end gap-3 mt-5">
                        <button type="button" onclick="document.getElementById('delModal').classList.add('hidden')"
                            class="px-4 py-2 font-bold text-slate-600">Batal</button>
                        <button type="submit"
                            class="bg-purple-600 text-white px-4 py-2 rounded-lg font-bold shadow hover:bg-purple-700">Kirim
                            & Kurangi Stok FIFO</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- MODAL FORM RETUR PENJUALAN -->
    <div id="returnModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-[80] p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-rose-50">
                <h3 class="text-lg font-bold text-rose-800 flex items-center gap-2">
                    <i data-feather="corner-down-left" class="w-5 h-5"></i> Retur Penjualan (Credit Note)
                </h3>
                <button onclick="document.getElementById('returnModal').classList.add('hidden')" class="text-slate-400 hover:text-rose-700 transition-colors"><i data-feather="x"></i></button>
            </div>

            <form id="returnForm" method="POST" class="p-6 space-y-4">
                @csrf
                <p class="text-sm font-medium text-slate-600 mb-2">Proses retur pengiriman dari pelanggan: <b id="returnCustomerName" class="text-slate-800"></b></p>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Kembalikan Stok ke Gudang</label>
                    <select name="warehouse_id" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-rose-500">
                        <option value="" disabled selected>-- Pilih Gudang Penyimpanan Retur --</option>
                        <!-- Panggil relasi Gudang langsung dari Blade agar rapi -->
                        @foreach (\App\Models\Warehouse::all() as $wh)
                            <option value="{{ $wh->id }}">[{{ $wh->code }}] {{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Alasan Retur / Cacat (Cetak Dokumen)</label>
                    <textarea name="reason" required rows="2" placeholder="Cth: Barang rusak saat pengiriman, tidak sesuai pesanan..." class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-rose-500 resize-none"></textarea>
                </div>

                <div class="p-3 bg-rose-50 border border-rose-100 rounded text-xs text-rose-800 font-medium leading-relaxed">
                    <b>Info Akuntansi Otomatis:</b><br/>Menekan tombol simpan akan menarik stok fisik kembali ke gudang pilihan Anda, dan secara otomatis menerbitkan dokumen <i>Credit Note</i> yang membalik (mengoreksi) angka Piutang, Pendapatan, dan HPP di Buku Besar tanpa menghapus histori aslinya.
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('returnModal').classList.add('hidden')" class="px-4 py-2 text-slate-600 font-bold transition-colors hover:bg-slate-100 rounded-lg">Batal</button>
                    <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white px-5 py-2 rounded-lg font-bold transition-colors shadow-sm">Proses Retur Barang</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Skrip Pemanggil Modal Retur -->
    <script>
        function openReturnModal(saleId, customerName) {
            document.getElementById('returnCustomerName').innerText = customerName;
            document.getElementById('returnForm').action = '/sales/' + saleId + '/return';
            document.getElementById('returnModal').classList.remove('hidden');
        }
    </script>
@endsection
