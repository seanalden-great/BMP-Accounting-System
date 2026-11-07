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
            <div>
                <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                    <i data-feather="truck" class="text-purple-600 w-7 h-7"></i> Pengiriman Penjualan
                </h1>
                <p class="text-slate-500 mt-1">Sistem menggunakan alokasi FIFO (First-In, First-Out) lintas gudang.</p>
            </div>
            <button onclick="document.getElementById('delModal').classList.remove('hidden')"
                class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2">
                <i data-feather="send" class="w-4 h-4"></i> Proses Pengiriman
            </button>
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
                            <td class="p-4 text-center">
                                <button
                                    onclick="document.getElementById('detailModal_{{ $del->id }}').classList.remove('hidden')"
                                    class="text-purple-600 hover:bg-purple-50 p-2 rounded-lg transition-colors"
                                    title="Lihat Detail Barang">
                                    <i data-feather="eye" class="w-4 h-4"></i>
                                </button>
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
@endsection
