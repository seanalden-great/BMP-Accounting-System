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

<!-- resources/views/purchases/bill.blade.php -->
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
@endsection