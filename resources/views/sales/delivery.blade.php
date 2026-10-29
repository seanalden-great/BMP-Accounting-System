<!-- resources/views/sales/delivery.blade.php -->
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
                @foreach($deliveries as $del)
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
                    @foreach($pendingOrders as $so) <option value="{{ $so->id }}">[SO #{{ $so->id }}] {{ $so->contact->name }}</option> @endforeach
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
@endsection