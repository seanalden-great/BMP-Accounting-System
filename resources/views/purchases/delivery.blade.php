{{-- <!-- resources/views/purchases/delivery.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">
    <div class="flex justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="truck" class="text-emerald-600 w-7 h-7"></i> Penerimaan Pembelian
            </h1>
        </div>
        <button onclick="document.getElementById('delModal').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2">
            <i data-feather="check-square" class="w-4 h-4"></i> Proses Penerimaan
        </button>
    </div>

    <!-- Tabel Riwayat (Sama seperti PO List, hanya beda data $deliveries) -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b">
                <tr><th class="p-4">Tanggal PO</th><th class="p-4">Supplier</th><th class="p-4">Status</th></tr>
            </thead>
            <tbody>
                @foreach ($deliveries as $del)
                <tr class="border-b">
                    <td class="p-4">{{ \Carbon\Carbon::parse($del->date)->format('d/m/Y') }}</td>
                    <td class="p-4 font-bold">{{ $del->contact->name }}</td>
                    <td class="p-4"><span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold">Diterima</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Form -->
    <div id="delModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl w-full max-w-lg p-6">
            <h3 class="text-xl font-bold text-slate-800 mb-4">Terima Barang dari PO</h3>
            <form action="#" method="POST" id="receiveForm">
                @csrf
                <select name="po_id" required onchange="document.getElementById('receiveForm').action = '/purchases/' + this.value + '/receive';" class="w-full border rounded-lg p-3 outline-none mb-4">
                    <option value="" disabled selected>-- Pilih PO --</option>
                    @foreach ($pendingOrders as $po) <option value="{{ $po->id }}">[PO #{{ $po->id }}] {{ $po->contact->name }}</option> @endforeach
                </select>
                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" onclick="document.getElementById('delModal').classList.add('hidden')" class="px-4 py-2 font-bold text-slate-600">Batal</button>
                    <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded-lg font-bold">Simpan ke Gudang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection --}}

{{-- <!-- resources/views/purchases/delivery.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">
    <div class="flex justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="truck" class="text-emerald-600 w-7 h-7"></i> Penerimaan Pembelian
            </h1>
            <p class="text-slate-500 mt-1">Pilih Gudang Penyimpanan untuk PO yang datang dari Pemasok</p>
        </div>
        <button onclick="document.getElementById('delModal').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2">
            <i data-feather="check-square" class="w-4 h-4"></i> Proses Penerimaan
        </button>
    </div>

    <!-- Tabel Riwayat -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b">
                <tr>
                    <th class="p-4 w-32">Tanggal PO</th>
                    <th class="p-4 w-48">Supplier</th>
                    <th class="p-4">Catatan Gudang Penerima</th>
                    <th class="p-4 w-32 text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($deliveries as $del)
                <tr class="border-b hover:bg-slate-50">
                    <td class="p-4 text-sm">{{ \Carbon\Carbon::parse($del->date)->format('d/m/Y') }}</td>
                    <td class="p-4 font-bold text-sm">{{ $del->contact->name }}</td>

                    <!-- Menampilkan info gudang penerima -->
                    <td class="p-4 text-xs font-medium text-slate-600 leading-relaxed whitespace-pre-wrap">{{ $del->notes ?: 'Tidak ada catatan' }}</td>

                    <td class="p-4 text-center"><span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold">Diterima</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Form -->
    <div id="delModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl w-full max-w-lg p-6">
            <h3 class="text-xl font-bold text-slate-800 mb-4">Terima Barang dari PO</h3>
            <form action="#" method="POST" id="receiveForm">
                @csrf

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Dokumen Pesanan (PO)</label>
                    <select name="po_id" required onchange="document.getElementById('receiveForm').action = '/purchases/' + this.value + '/receive';" class="w-full border border-emerald-200 rounded-lg p-3 outline-none mb-4 font-semibold text-slate-700">
                        <option value="" disabled selected>-- Pilih PO yang Diterima --</option>
                        @foreach ($pendingOrders as $po)
                            <option value="{{ $po->id }}">[PO #{{ $po->id }}] {{ $po->contact->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Bongkar Muat di Gudang</label>
                    <select name="warehouse_id" required class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-emerald-500 font-semibold text-indigo-700">
                        <option value="" disabled selected>-- Pilih Lokasi Gudang --</option>
                        @foreach ($warehouses as $wh)
                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-5 p-4 bg-orange-50 border border-orange-200 rounded-lg text-sm text-orange-900 leading-relaxed">
                    <b>Info Sistem Stok:</b><br/>
                    Menyimpan form ini akan otomatis <b>menambahkan stok ke Gudang terpilih</b>, merevisi angka HPP (*Moving Average*), dan membuat Jurnal Hutang.
                </div>

                <div class="flex justify-end gap-3 mt-5">
                    <button type="button" onclick="document.getElementById('delModal').classList.add('hidden')" class="px-4 py-2 font-bold text-slate-600">Batal</button>
                    <button type="submit" class="bg-emerald-600 text-white px-5 py-2 rounded-lg font-bold shadow hover:bg-emerald-700">Simpan ke Gudang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection --}}

{{-- <!-- resources/views/purchases/delivery.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="space-y-6 pb-10">
        <div class="flex justify-between items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                    <i data-feather="truck" class="text-emerald-600 w-7 h-7"></i> Penerimaan Pembelian
                </h1>
                <p class="text-slate-500 mt-1">Pilih Gudang Penyimpanan untuk PO yang datang dari Pemasok</p>
            </div>
            <!-- CEK HAK AKSES UPDATE (U) -->
            @if (\App\Models\CompanySetting::canAction('U', 'Pembelian'))
                <button onclick="document.getElementById('delModal').classList.remove('hidden')"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2">
                    <i data-feather="check-square" class="w-4 h-4"></i> Proses Penerimaan
                </button>
            @endif
        </div>

        <!-- Tabel Riwayat -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 border-b">
                    <tr>
                        <th class="p-4 w-32">Tanggal PO</th>
                        <th class="p-4 w-48">Supplier</th>
                        <th class="p-4">Catatan Gudang Penerima</th>
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
                                    class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold">Diterima</span>
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        onclick="document.getElementById('detailModal_{{ $del->id }}').classList.remove('hidden')"
                                        class="text-emerald-600 hover:bg-emerald-50 p-2 rounded-lg transition-colors"
                                        title="Lihat Detail Barang">
                                        <i data-feather="eye" class="w-4 h-4"></i>
                                    </button>

                                    <!-- CEK HAK AKSES UPDATE (U) UNTUK TOMBOL RETUR (BARU) -->
                                    @if (\App\Models\CompanySetting::canAction('U', 'Pembelian'))
                                        <button
                                            onclick="openReturnModal({{ $del->id }}, '{{ addslashes($del->contact->name) }}')"
                                            class="text-rose-500 hover:text-rose-700 hover:bg-rose-50 p-2 rounded-lg transition-colors border border-transparent hover:border-rose-200 shadow-sm"
                                            title="Retur Barang Cacat (Kembalikan ke Supplier)">
                                            <i data-feather="corner-up-right" class="w-4 h-4"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- PERULANGAN MODAL DETAIL -->
        @foreach ($deliveries as $del)
            <div id="detailModal_{{ $del->id }}"
                class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-[70] p-4">
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh]">
                    <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                        <h3 class="text-xl font-bold text-slate-800">Rincian Dokumen:
                            {{ $del->reference_no ?: '#' . $del->id }}</h3>
                        <button onclick="document.getElementById('detailModal_{{ $del->id }}').classList.add('hidden')"
                            class="text-slate-400 hover:text-slate-700"><i data-feather="x"></i></button>
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
                                    <th class="p-3 border text-right font-bold text-emerald-700 text-lg">Rp
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
                <h3 class="text-xl font-bold text-slate-800 mb-4">Terima Barang dari PO</h3>
                <form action="#" method="POST" id="receiveForm">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Dokumen Pesanan (PO)</label>
                        <select name="po_id" required
                            onchange="document.getElementById('receiveForm').action = '/purchases/' + this.value + '/receive';"
                            class="w-full border border-emerald-200 rounded-lg p-3 outline-none mb-4 font-semibold text-slate-700">
                            <option value="" disabled selected>-- Pilih PO yang Diterima --</option>
                            @foreach ($pendingOrders as $po)
                                <option value="{{ $po->id }}">[{{ $po->reference_no ?: 'PO #' . $po->id }}]
                                    {{ $po->contact->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Bongkar Muat di Gudang</label>
                        <select name="warehouse_id" required
                            class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-emerald-500 font-semibold text-indigo-700">
                            <option value="" disabled selected>-- Pilih Lokasi Gudang --</option>
                            @foreach ($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div
                        class="mt-5 p-4 bg-orange-50 border border-orange-200 rounded-lg text-sm text-orange-900 leading-relaxed">
                        <b>Info Sistem Stok:</b><br />
                        Menyimpan form ini akan otomatis <b>menambahkan stok ke Gudang terpilih</b>, merevisi angka HPP
                        (*Moving Average*), dan membuat Jurnal Hutang.
                    </div>
                    <div class="flex justify-end gap-3 mt-5">
                        <button type="button" onclick="document.getElementById('delModal').classList.add('hidden')"
                            class="px-4 py-2 font-bold text-slate-600">Batal</button>
                        <button type="submit"
                            class="bg-emerald-600 text-white px-5 py-2 rounded-lg font-bold shadow hover:bg-emerald-700">Simpan
                            ke Gudang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- MODAL FORM RETUR PEMBELIAN -->
    <div id="returnModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-[80] p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-rose-50">
                <h3 class="text-lg font-bold text-rose-800 flex items-center gap-2">
                    <i data-feather="corner-up-right" class="w-5 h-5"></i> Retur Pembelian (Debit Note)
                </h3>
                <button onclick="document.getElementById('returnModal').classList.add('hidden')"
                    class="text-slate-400 hover:text-rose-700 transition-colors"><i data-feather="x"></i></button>
            </div>

            <form id="returnForm" method="POST" class="p-6 space-y-4">
                @csrf
                <p class="text-sm font-medium text-slate-600 mb-2">Proses pengembalian barang cacat ke Supplier: <b
                        id="returnCustomerName" class="text-slate-800"></b></p>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Keluarkan dari Gudang Mana?</label>
                    <select name="warehouse_id" required
                        class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-rose-500">
                        <option value="" disabled selected>-- Pilih Gudang Asal Retur --</option>
                        <!-- Panggil relasi Gudang langsung dari Blade agar data selalu up-to-date -->
                        @foreach (\App\Models\Warehouse::all() as $wh)
                            <option value="{{ $wh->id }}">[{{ $wh->code }}] {{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Alasan Retur / Komplain</label>
                    <textarea name="reason" required rows="2"
                        placeholder="Cth: Barang cacat pabrik, kadaluwarsa, tidak sesuai PO..."
                        class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-rose-500 resize-none"></textarea>
                </div>

                <div
                    class="p-3 bg-rose-50 border border-rose-100 rounded text-xs text-rose-800 font-medium leading-relaxed">
                    <b>Info Akuntansi Otomatis:</b><br />Menekan tombol simpan akan mengeluarkan stok fisik dari gudang,
                    membatalkan status penerimaan, dan secara otomatis menerbitkan dokumen <i>Debit Note</i> yang
                    mendebit/mengurangi angka Hutang Usaha Anda di Buku Besar.
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('returnModal').classList.add('hidden')"
                        class="px-4 py-2 text-slate-600 font-bold transition-colors hover:bg-slate-100 rounded-lg">Batal</button>
                    <button type="submit"
                        class="bg-rose-600 hover:bg-rose-700 text-white px-5 py-2 rounded-lg font-bold transition-colors shadow-sm">Keluarkan
                        & Retur Barang</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Skrip Pemanggil Modal Retur -->
    <script>
        function openReturnModal(poId, supplierName) {
            document.getElementById('returnCustomerName').innerText = supplierName;
            document.getElementById('returnForm').action = '/purchases/' + poId + '/return';
            document.getElementById('returnModal').classList.remove('hidden');
        }
    </script>
@endsection --}}

@extends('layouts.app')

@section('content')
    <div class="space-y-6 pb-10 relative">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                    <i data-feather="truck" class="text-emerald-600 w-7 h-7"></i> Penerimaan Pembelian
                </h1>
                <p class="text-slate-500 mt-1">Pilih Gudang Penyimpanan untuk PO yang datang dari Pemasok</p>
            </div>

            <!-- CEK HAK AKSES UPDATE (U) -->
            @if (\App\Models\CompanySetting::canAction('U', 'Pembelian'))
                <button onclick="openDeliveryModal()"
                    class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-lg font-bold flex items-center justify-center gap-2 shadow-sm transition-colors">
                    <i data-feather="check-square" class="w-4 h-4"></i> Proses Penerimaan
                </button>
            @endif
        </div>

        <!-- Tabel Riwayat -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[900px]">
                    <thead class="bg-slate-50 border-b">
                        <tr>
                            <th class="p-4 w-32 font-semibold text-slate-600 text-sm">Tanggal PO</th>
                            <th class="p-4 w-48 font-semibold text-slate-600 text-sm">Supplier</th>
                            <th class="p-4 font-semibold text-slate-600 text-sm">Catatan Gudang Penerima</th>
                            <th class="p-4 w-32 text-center font-semibold text-slate-600 text-sm">Status</th>
                            <th class="p-4 text-center font-semibold text-slate-600 text-sm">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($deliveries as $del)
                            <tr class="border-b hover:bg-slate-50 transition-colors">
                                <td class="p-4 text-sm font-medium text-slate-700">{{ \Carbon\Carbon::parse($del->date)->format('d/m/Y') }}</td>
                                <td class="p-4 font-bold text-sm text-slate-800">{{ $del->contact->name }}</td>
                                <td class="p-4 text-xs font-medium text-slate-600 leading-relaxed whitespace-pre-wrap">{{ $del->notes ?: 'Tidak ada catatan' }}</td>
                                <td class="p-4 text-center">
                                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold border border-emerald-200">Diterima</span>
                                </td>
                                <td class="p-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Tombol Detail -->
                                        <button onclick="openDetailModal({{ $del->id }})" class="text-emerald-600 hover:bg-emerald-50 p-2 rounded-lg transition-colors border border-transparent hover:border-emerald-200 shadow-sm" title="Lihat Detail Barang">
                                            <i data-feather="eye" class="w-4 h-4"></i>
                                        </button>

                                        <!-- CEK HAK AKSES UPDATE (U) UNTUK TOMBOL RETUR -->
                                        @if (\App\Models\CompanySetting::canAction('U', 'Pembelian'))
                                            <button onclick="openReturnModal({{ $del->id }}, '{{ addslashes($del->contact->name) }}')"
                                                class="text-rose-500 hover:text-rose-700 hover:bg-rose-50 p-2 rounded-lg transition-colors border border-transparent hover:border-rose-200 shadow-sm"
                                                title="Retur Barang Cacat (Kembalikan ke Supplier)">
                                                <i data-feather="corner-up-right" class="w-4 h-4"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-10 text-center text-slate-500">Belum ada riwayat penerimaan barang.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PERULANGAN MODAL DETAIL SURAT JALAN (BERANIMASI) -->
        @foreach ($deliveries as $del)
            <div id="detailModal_{{ $del->id }}" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-[70] p-4 backdrop-blur-sm transition-all">
                <div id="detailModalContent_{{ $del->id }}" class="bg-white rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh] border-t-4 border-emerald-600 transform transition-transform scale-95 origin-center">
                    <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                        <h3 class="text-xl font-bold text-slate-800">Rincian Penerimaan: {{ $del->reference_no ?: '#' . $del->id }}</h3>
                        <button onclick="closeDetailModal({{ $del->id }})" class="text-slate-400 hover:text-slate-700 transition-colors"><i data-feather="x"></i></button>
                    </div>
                    <div class="p-6 overflow-y-auto custom-scrollbar">
                        <div class="overflow-x-auto border border-slate-300 rounded-lg">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-slate-100">
                                    <tr>
                                        <th class="p-3 border-b text-sm font-semibold">Produk</th>
                                        <th class="p-3 border-b text-sm font-semibold text-center">Qty Diterima</th>
                                        <th class="p-3 border-b text-sm font-semibold text-right">Harga Beli</th>
                                        <th class="p-3 border-b text-sm font-semibold text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($del->items as $item)
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
                                        <th class="p-3 text-right font-black text-emerald-700 text-lg">Rp {{ number_format($del->total, 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Modal Form Penerimaan (Beranimasi & Swal) -->
        <div id="delModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 backdrop-blur-sm transition-all">
            <div id="delModalContent" class="bg-white rounded-xl w-full max-w-lg overflow-hidden flex flex-col border-t-4 border-emerald-600 shadow-2xl transform transition-transform scale-95 origin-center">
                <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-xl font-bold text-slate-800">Terima Barang dari PO</h3>
                    <button onclick="closeDeliveryModal()" class="text-slate-400 hover:text-red-500 bg-white rounded-full p-1 shadow-sm transition-colors"><i data-feather="x"></i></button>
                </div>

                <form action="#" method="POST" id="receiveForm" class="p-6" onsubmit="confirmAction(event, this, 'Terima Barang?', 'Sistem akan menambahkan stok ke gudang secara otomatis dan mencatat Jurnal Hutang. Lanjutkan?', 'Ya, Simpan ke Gudang', '#10b981')">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Dokumen Pesanan (PO)</label>
                        <select name="po_id" required
                            onchange="document.getElementById('receiveForm').action = '/purchases/' + this.value + '/receive';"
                            class="w-full border border-emerald-200 rounded-lg p-3 outline-none mb-4 font-semibold text-slate-700 shadow-sm focus:ring-2 focus:ring-emerald-500">
                            <option value="" disabled selected>-- Pilih PO yang Diterima --</option>
                            @foreach ($pendingOrders as $po)
                                <option value="{{ $po->id }}">[{{ $po->reference_no ?: 'PO #' . $po->id }}] {{ $po->contact->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Bongkar Muat di Gudang</label>
                        <select name="warehouse_id" required class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-emerald-500 font-bold text-indigo-700 shadow-sm bg-slate-50">
                            <option value="" disabled selected>-- Pilih Lokasi Gudang --</option>
                            @foreach ($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-5 p-4 bg-orange-50 border border-orange-200 rounded-lg text-sm text-orange-900 leading-relaxed shadow-sm">
                        <b>Info Sistem Stok:</b><br />
                        Menyimpan form ini akan otomatis <b>menambahkan stok ke Gudang terpilih</b>, merevisi angka HPP (*Moving Average*), dan membuat Jurnal Hutang Usaha.
                    </div>

                    <div class="flex justify-end gap-3 mt-6 border-t border-slate-100 pt-4">
                        <button type="button" onclick="closeDeliveryModal()" class="px-5 py-2.5 font-bold text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">Batal</button>
                        <button type="submit" class="bg-emerald-600 text-white px-6 py-2.5 rounded-lg font-bold shadow-md hover:bg-emerald-700 transition-colors flex items-center gap-2">
                            <i data-feather="check-square" class="w-4 h-4"></i> Simpan ke Gudang
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL FORM RETUR PEMBELIAN (Beranimasi & Swal) -->
        <div id="returnModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-[80] p-4 backdrop-blur-sm transition-all">
            <div id="returnModalContent" class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col border-t-4 border-rose-600 transform transition-transform scale-95 origin-center">
                <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-rose-50">
                    <h3 class="text-lg font-bold text-rose-800 flex items-center gap-2">
                        <i data-feather="corner-up-right" class="w-5 h-5"></i> Retur Pembelian (Debit Note)
                    </h3>
                    <button onclick="closeReturnModal()" class="text-slate-400 hover:text-rose-700 bg-white rounded-full p-1 shadow-sm transition-colors"><i data-feather="x"></i></button>
                </div>

                <form id="returnForm" method="POST" class="p-6 space-y-4" onsubmit="confirmAction(event, this, 'Proses Retur?', 'Stok fisik akan dikeluarkan dari gudang pilihan Anda dan jurnal pembalik akan otomatis dibuat. Tindakan ini tidak dapat dibatalkan.', 'Ya, Retur Barang', '#e11d48')">
                    @csrf
                    <p class="text-sm font-medium text-slate-600 mb-2 bg-slate-50 p-3 rounded-lg border border-slate-100">Proses pengembalian barang cacat ke Supplier: <br/><b id="returnCustomerName" class="text-slate-800 text-base"></b></p>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Keluarkan dari Gudang Mana?</label>
                        <select name="warehouse_id" required class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-rose-500 font-medium shadow-sm">
                            <option value="" disabled selected>-- Pilih Gudang Asal Retur --</option>
                            @foreach (\App\Models\Warehouse::all() as $wh)
                                <option value="{{ $wh->id }}">[{{ $wh->code }}] {{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Alasan Retur / Komplain</label>
                        <textarea name="reason" required rows="2" placeholder="Cth: Barang cacat pabrik, kadaluwarsa, tidak sesuai PO..." class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-rose-500 resize-none shadow-sm"></textarea>
                    </div>

                    <div class="p-3 bg-rose-50 border border-rose-100 rounded-lg text-xs text-rose-800 font-medium leading-relaxed shadow-sm">
                        <b>Info Akuntansi Otomatis:</b><br />Menekan tombol simpan akan mengeluarkan stok fisik dari gudang, membatalkan status penerimaan, dan secara otomatis menerbitkan dokumen <i>Debit Note</i> yang mendebit/mengurangi angka Hutang Usaha Anda di Buku Besar.
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" onclick="closeReturnModal()" class="px-5 py-2.5 text-slate-600 font-bold transition-colors hover:bg-slate-100 rounded-lg">Batal</button>
                        <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white px-6 py-2.5 rounded-lg font-bold transition-colors shadow-md flex items-center gap-2">
                            <i data-feather="corner-up-right" class="w-4 h-4"></i> Keluarkan & Retur
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

        // --- SWEETALERT2: KONFIRMASI TINDAKAN KRUSIAL ---
        function confirmAction(event, form, title, text, confirmText, confirmColor) {
            event.preventDefault();

            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: confirmColor,
                cancelButtonColor: '#64748b',
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal',
                reverseButtons: true,
                didOpen: () => { if (typeof feather !== 'undefined') feather.replace(); }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mencatat transaksi stok ke dalam sistem dan Buku Besar.',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    form.submit();
                }
            });
        }

        // --- LOGIKA MODAL (ANIMASI CSS SCALE) ---
        function openDeliveryModal() {
            const modal = document.getElementById('delModal');
            const content = document.getElementById('delModalContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }, 10);
        }

        function closeDeliveryModal() {
            const modal = document.getElementById('delModal');
            const content = document.getElementById('delModalContent');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                document.getElementById('receiveForm').reset();
                // Reset action URL jika batal
                document.getElementById('receiveForm').action = '#';
            }, 150);
        }

        function openReturnModal(poId, customerName) {
            document.getElementById('returnCustomerName').innerText = customerName;
            document.getElementById('returnForm').action = '/purchases/' + poId + '/return';

            const modal = document.getElementById('returnModal');
            const content = document.getElementById('returnModalContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }, 10);
        }

        function closeReturnModal() {
            const modal = document.getElementById('returnModal');
            const content = document.getElementById('returnModalContent');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                document.getElementById('returnForm').reset();
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
