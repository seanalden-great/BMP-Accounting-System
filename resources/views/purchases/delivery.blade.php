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
                @foreach($deliveries as $del)
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
                    @foreach($pendingOrders as $po) <option value="{{ $po->id }}">[PO #{{ $po->id }}] {{ $po->contact->name }}</option> @endforeach
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

<!-- resources/views/purchases/delivery.blade.php -->
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
                @foreach($deliveries as $del)
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
                        @foreach($pendingOrders as $po) 
                            <option value="{{ $po->id }}">[PO #{{ $po->id }}] {{ $po->contact->name }}</option> 
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Bongkar Muat di Gudang</label>
                    <select name="warehouse_id" required class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-emerald-500 font-semibold text-indigo-700">
                        <option value="" disabled selected>-- Pilih Lokasi Gudang --</option>
                        @foreach($warehouses as $wh)
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
@endsection