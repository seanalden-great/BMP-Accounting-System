<!-- resources/views/sales/invoice.blade.php -->
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
@endsection