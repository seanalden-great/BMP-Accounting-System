{{-- <!-- resources/views/sales/index.blade.php -->
@extends('layouts.app')

@section('content')
@php
    $customers = \App\Models\Contact::where('type', 'Customer')->get();
    $products = \App\Models\Product::all();
@endphp

<div class="space-y-6 pb-10">
    <div class="flex justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="shopping-bag" class="text-indigo-600 w-7 h-7"></i> Pesanan Penjualan (SO)
            </h1>
            <p class="text-slate-500 mt-1">Buat dokumen pesanan barang dari Pelanggan (Customer)</p>
        </div>
        <button onclick="document.getElementById('soModal').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium shadow-sm flex items-center gap-2">
            <i data-feather="plus" class="w-4 h-4"></i> Buat SO Baru
        </button>
    </div>

    <!-- Tabel SO -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="p-4 text-sm font-semibold text-slate-600">Tanggal</th>
                    <th class="p-4 text-sm font-semibold text-slate-600">No. Referensi</th>
                    <th class="p-4 text-sm font-semibold text-slate-600">Pelanggan</th>
                    <th class="p-4 text-sm font-semibold text-slate-600 text-right">Total Penjualan</th>
                    <th class="p-4 text-sm font-semibold text-slate-600 text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $so)
                <tr class="border-b border-slate-50 hover:bg-slate-50">
                    <td class="p-4 text-sm">{{ \Carbon\Carbon::parse($so->date)->format('d/m/Y') }}</td>
                    <td class="p-4 text-sm font-bold text-slate-800">{{ $so->reference_no ?: '-' }}</td>
                    <td class="p-4 text-sm font-medium text-indigo-700">{{ $so->contact->name }}</td>
                    <td class="p-4 text-sm font-bold text-slate-800 text-right">Rp {{ number_format($so->total, 0, ',', '.') }}</td>
                    <td class="p-4 text-center">
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold">{{ $so->status }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-8 text-center text-slate-500">Belum ada pesanan penjualan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Form SO -->
    <div id="soModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl overflow-hidden flex flex-col max-h-[95vh]">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 class="text-xl font-bold text-slate-800">Buat Pesanan Penjualan Baru</h3>
                <button onclick="document.getElementById('soModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700"><i data-feather="x"></i></button>
            </div>
            
            <form action="{{ route('sales.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <div class="p-6 overflow-y-auto custom-scrollbar flex-1 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <div class="lg:col-span-2">
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-sm font-semibold text-slate-700">Pilih Pelanggan</label>
                                <!-- Tombol Panggil Modal Kontak -->
                                <button type="button" onclick="openContactModal()" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline flex items-center gap-1">
                                    <i data-feather="plus" class="w-3 h-3"></i> Tambah Baru
                                </button>
                            </div>
                            <select name="contact_id" id="contactDropdown" required class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="" disabled selected>-- Pilih Customer --</option>
                                @foreach($customers as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal</label>
                            <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">No. PO Pelanggan (Opsional)</label>
                            <input type="text" name="reference_no" placeholder="SO-2026-001" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none">
                        </div>
                    </div>

                    <!-- Tabel Dinamis Barang -->
                    <div>
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="font-bold text-slate-800">Detail Pesanan Barang</h4>
                            <button type="button" onclick="addRow()" class="bg-indigo-50 text-indigo-600 hover:bg-indigo-100 px-3 py-1.5 rounded-lg text-sm font-semibold flex items-center gap-1">
                                + Tambah Baris
                            </button>
                        </div>
                        <table class="w-full text-left border-collapse border border-slate-300" id="itemTable">
                            <thead class="bg-slate-100">
                                <tr>
                                    <th class="p-2 border text-sm font-semibold">Produk</th>
                                    <th class="p-2 border text-sm font-semibold w-24">Qty</th>
                                    <th class="p-2 border text-sm font-semibold w-32">Harga Jual</th>
                                    <th class="p-2 border text-sm font-semibold w-32 text-right">Subtotal</th>
                                    <th class="p-2 border w-12 text-center"></th>
                                </tr>
                            </thead>
                            <tbody id="itemBody"></tbody>
                        </table>
                        <div class="mt-4 text-right text-lg font-bold text-indigo-700">
                            GRAND TOTAL: <span id="grandTotalText">Rp 0</span>
                        </div>
                    </div>
                </div>

                <div class="p-5 border-t border-slate-200 bg-slate-50 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('soModal').classList.add('hidden')" class="px-5 py-2.5 text-slate-600 font-bold">Batal</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-bold">Simpan Pesanan (SO)</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Mini: Tambah Kontak Cepat -->

<div id="quickContactModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-[60] p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col">
        <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-bold text-slate-800">Tambah Customer Baru</h3>
            <button onclick="closeContactModal()" class="text-slate-400 hover:text-slate-700 transition-colors"><i data-feather="x" class="w-5 h-5"></i></button>
        </div>
        <form id="quickContactForm" onsubmit="submitQuickContact(event)" class="p-5 space-y-4">
            <input type="hidden" name="type" value="Customer"> <!-- Otomatis Customer -->
            
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Customer</label>
                    <input type="text" name="name" required class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">No. Telepon / WA (Opsional)</label>
                    <input type="text" name="phone" placeholder="08..." class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Email (Opsional)</label>
                    <input type="email" name="email" placeholder="email@contoh.com" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Alamat Lengkap</label>
                    <textarea name="address" rows="3" placeholder="Alamat jalan, kota, provinsi..." class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-500 resize-none"></textarea>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeContactModal()" class="px-4 py-2 text-slate-600 font-bold text-sm bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">Batal</button>
                <button type="submit" id="btnSaveContact" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg font-bold text-sm transition-colors">Simpan Kontak</button>
            </div>
        </form>
    </div>
</div>

<script>
    const productsData = @json($products);
    let rowIdx = 0;

    function addRow() {
        let tbody = document.getElementById('itemBody');
        let tr = document.createElement('tr');
        
        let selectOptions = `<option value="" disabled selected>Pilih Produk...</option>`;
        // PERHATIAN: Di Sales, harga yang diambil adalah sell_price
        productsData.forEach(p => { selectOptions += `<option value="${p.sku}" data-price="${p.sell_price}">[${p.sku}] ${p.name}</option>`; });

        tr.innerHTML = `
            <td class="p-1 border">
                <select name="items[${rowIdx}][product_sku]" onchange="updatePrice(this, ${rowIdx})" required class="w-full p-1.5 text-sm outline-none bg-transparent">
                    ${selectOptions}
                </select>
            </td>
            <td class="p-1 border">
                <input type="number" name="items[${rowIdx}][quantity]" id="qty_${rowIdx}" min="1" value="1" required onkeyup="calcSub(${rowIdx})" onchange="calcSub(${rowIdx})" class="w-full p-1.5 text-sm text-center outline-none bg-transparent">
            </td>
            <td class="p-1 border">
                <input type="number" name="items[${rowIdx}][price]" id="price_${rowIdx}" min="0" required onkeyup="calcSub(${rowIdx})" onchange="calcSub(${rowIdx})" class="w-full p-1.5 text-sm text-right outline-none bg-transparent">
            </td>
            <td class="p-2 border text-right text-sm font-semibold bg-slate-50 subtotal-text" id="subtext_${rowIdx}">Rp 0</td>
            <td class="p-1 border text-center">
                <button type="button" onclick="this.closest('tr').remove(); calcGrand();" class="text-red-500 hover:text-red-700">X</button>
            </td>
        `;
        tbody.appendChild(tr);
        rowIdx++;
    }

    function updatePrice(sel, idx) {
        let price = sel.options[sel.selectedIndex].getAttribute('data-price');
        document.getElementById(`price_${idx}`).value = price;
        calcSub(idx);
    }

    function calcSub(idx) {
        let q = document.getElementById(`qty_${idx}`).value || 0;
        let p = document.getElementById(`price_${idx}`).value || 0;
        let sub = parseInt(q) * parseInt(p);
        document.getElementById(`subtext_${idx}`).innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(sub);
        calcGrand();
    }

    function calcGrand() {
        let total = 0;
        document.querySelectorAll('[id^="qty_"]').forEach(el => {
            let idx = el.id.split('_')[1];
            let q = document.getElementById(`qty_${idx}`).value || 0;
            let p = document.getElementById(`price_${idx}`).value || 0;
            total += parseInt(q) * parseInt(p);
        });
        document.getElementById('grandTotalText').innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(total);
    }

    document.addEventListener("DOMContentLoaded", function() { addRow(); });

    // FUNGSI UNTUK MODAL KONTAK CEPAT (AJAX)
    function openContactModal() {
        document.getElementById('quickContactModal').classList.remove('hidden');
    }
    
    function closeContactModal() {
        document.getElementById('quickContactModal').classList.add('hidden');
        document.getElementById('quickContactForm').reset();
    }

    async function submitQuickContact(e) {
        e.preventDefault();
        
        const form = document.getElementById('quickContactForm');
        const btn = document.getElementById('btnSaveContact');
        const formData = new FormData(form);
        
        btn.disabled = true;
        btn.innerText = 'Menyimpan...';

        try {
            const response = await fetch("{{ route('contacts.store') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: formData
            });

            const result = await response.json();

            if (response.ok && result.success) {
                const newOption = document.createElement("option");
                newOption.value = result.contact.id;
                newOption.text = result.contact.name;
                
                const dropdown = document.getElementById('contactDropdown');
                dropdown.add(newOption);
                dropdown.value = result.contact.id;

                closeContactModal();
                alert('Customer berhasil ditambahkan!');
            } else {
                alert(result.message || 'Gagal menambahkan kontak.');
            }
        } catch (error) {
            alert('Terjadi kesalahan jaringan.');
        } finally {
            btn.disabled = false;
            btn.innerText = 'Simpan';
        }
    }
</script>
@endsection --}}

<!-- resources/views/sales/index.blade.php -->
@extends('layouts.app')

@section('content')
@php
    $customers = \App\Models\Contact::where('type', 'Customer')->get();
    $products = \App\Models\Product::all();
@endphp

<div class="space-y-6 pb-10">
    <div class="flex justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="shopping-bag" class="text-indigo-600 w-7 h-7"></i> Pesanan Penjualan (SO)
            </h1>
            <p class="text-slate-500 mt-1">Buat dokumen pesanan barang dari Pelanggan (Customer)</p>
        </div>
        <button onclick="document.getElementById('soModal').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium shadow-sm flex items-center gap-2">
            <i data-feather="plus" class="w-4 h-4"></i> Buat SO Baru
        </button>
    </div>

    <!-- Tabel SO -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="p-4 text-sm font-semibold text-slate-600">Tanggal</th>
                    <th class="p-4 text-sm font-semibold text-slate-600">No. Referensi</th>
                    <th class="p-4 text-sm font-semibold text-slate-600">Pelanggan</th>
                    <th class="p-4 text-sm font-semibold text-slate-600">Jatuh Tempo</th>
                    <th class="p-4 text-sm font-semibold text-slate-600 text-right">Total Penjualan</th>
                    <th class="p-4 text-sm font-semibold text-slate-600 text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $so)
                <tr class="border-b border-slate-50 hover:bg-slate-50">
                    <td class="p-4 text-sm">{{ \Carbon\Carbon::parse($so->date)->format('d/m/Y') }}</td>
                    <td class="p-4 text-sm font-bold text-slate-800">{{ $so->reference_no ?: '-' }}</td>
                    <td class="p-4 text-sm font-medium text-indigo-700">{{ $so->contact->name }}</td>
                    
                    <!-- Menampilkan Tanggal Jatuh Tempo di Tabel -->
                    <td class="p-4 text-sm">
                        @if($so->due_date)
                            <span class="px-2 py-1 rounded bg-slate-100 text-slate-700 font-medium border border-slate-200">
                                {{ \Carbon\Carbon::parse($so->due_date)->format('d/m/Y') }}
                            </span>
                        @else
                            -
                        @endif
                    </td>
                    
                    <td class="p-4 text-sm font-bold text-slate-800 text-right">Rp {{ number_format($so->total, 0, ',', '.') }}</td>
                    <td class="p-4 text-center">
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold">{{ $so->status }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="p-8 text-center text-slate-500">Belum ada pesanan penjualan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Form SO -->
    <div id="soModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl overflow-hidden flex flex-col max-h-[95vh]">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 class="text-xl font-bold text-slate-800">Buat Pesanan Penjualan Baru</h3>
                <button onclick="document.getElementById('soModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700"><i data-feather="x"></i></button>
            </div>
            
            <form action="{{ route('sales.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <div class="p-6 overflow-y-auto custom-scrollbar flex-1 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <div class="lg:col-span-2">
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-sm font-semibold text-slate-700">Pilih Pelanggan</label>
                                <button type="button" onclick="openContactModal()" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline flex items-center gap-1">
                                    <i data-feather="plus" class="w-3 h-3"></i> Tambah Baru
                                </button>
                            </div>
                            <select name="contact_id" id="contactDropdown" required class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="" disabled selected>-- Pilih Customer --</option>
                                @foreach($customers as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal SO</label>
                            <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-rose-700 mb-1">Jatuh Tempo</label>
                            <!-- Input Jatuh Tempo (due_date) ditambahkan di sini -->
                            <input type="date" name="due_date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">No. Referensi / PO</label>
                            <input type="text" name="reference_no" placeholder="PO-CUST-001" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <!-- Tabel Dinamis Barang -->
                    <div>
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="font-bold text-slate-800">Detail Pesanan Barang</h4>
                            <button type="button" onclick="addRow()" class="bg-indigo-50 text-indigo-600 hover:bg-indigo-100 px-3 py-1.5 rounded-lg text-sm font-semibold flex items-center gap-1">
                                + Tambah Baris
                            </button>
                        </div>
                        <table class="w-full text-left border-collapse border border-slate-300" id="itemTable">
                            <thead class="bg-slate-100">
                                <tr>
                                    <th class="p-2 border text-sm font-semibold">Produk</th>
                                    <th class="p-2 border text-sm font-semibold w-24">Qty</th>
                                    <th class="p-2 border text-sm font-semibold w-32">Harga Jual</th>
                                    <th class="p-2 border text-sm font-semibold w-32 text-right">Subtotal</th>
                                    <th class="p-2 border w-12 text-center"></th>
                                </tr>
                            </thead>
                            <tbody id="itemBody"></tbody>
                        </table>
                        <div class="mt-4 text-right text-lg font-bold text-indigo-700">
                            GRAND TOTAL: <span id="grandTotalText">Rp 0</span>
                        </div>
                    </div>
                </div>

                <div class="p-5 border-t border-slate-200 bg-slate-50 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('soModal').classList.add('hidden')" class="px-5 py-2.5 text-slate-600 font-bold">Batal</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-bold">Simpan Pesanan (SO)</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="quickContactModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-[60] p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col">
        <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-bold text-slate-800">Tambah Customer Baru</h3>
            <button onclick="closeContactModal()" class="text-slate-400 hover:text-slate-700 transition-colors"><i data-feather="x" class="w-5 h-5"></i></button>
        </div>
        <form id="quickContactForm" onsubmit="submitQuickContact(event)" class="p-5 space-y-4">
            <input type="hidden" name="type" value="Customer">
            
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Customer</label>
                    <input type="text" name="name" required class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">No. Telepon / WA (Opsional)</label>
                    <input type="text" name="phone" placeholder="08..." class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Email (Opsional)</label>
                    <input type="email" name="email" placeholder="email@contoh.com" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Alamat Lengkap</label>
                    <textarea name="address" rows="3" placeholder="Alamat jalan, kota, provinsi..." class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-500 resize-none"></textarea>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeContactModal()" class="px-4 py-2 text-slate-600 font-bold text-sm bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">Batal</button>
                <button type="submit" id="btnSaveContact" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg font-bold text-sm transition-colors">Simpan Kontak</button>
            </div>
        </form>
    </div>
</div>

<script>
    const productsData = @json($products);
    let rowIdx = 0;

    function addRow() {
        let tbody = document.getElementById('itemBody');
        let tr = document.createElement('tr');
        
        let selectOptions = `<option value="" disabled selected>Pilih Produk...</option>`;
        productsData.forEach(p => { selectOptions += `<option value="${p.sku}" data-price="${p.sell_price}">[${p.sku}] ${p.name}</option>`; });

        tr.innerHTML = `
            <td class="p-1 border">
                <select name="items[${rowIdx}][product_sku]" onchange="updatePrice(this, ${rowIdx})" required class="w-full p-1.5 text-sm outline-none bg-transparent">
                    ${selectOptions}
                </select>
            </td>
            <td class="p-1 border">
                <input type="number" name="items[${rowIdx}][quantity]" id="qty_${rowIdx}" min="1" value="1" required onkeyup="calcSub(${rowIdx})" onchange="calcSub(${rowIdx})" class="w-full p-1.5 text-sm text-center outline-none bg-transparent">
            </td>
            <td class="p-1 border">
                <input type="number" name="items[${rowIdx}][price]" id="price_${rowIdx}" min="0" required onkeyup="calcSub(${rowIdx})" onchange="calcSub(${rowIdx})" class="w-full p-1.5 text-sm text-right outline-none bg-transparent">
            </td>
            <td class="p-2 border text-right text-sm font-semibold bg-slate-50 subtotal-text" id="subtext_${rowIdx}">Rp 0</td>
            <td class="p-1 border text-center">
                <button type="button" onclick="this.closest('tr').remove(); calcGrand();" class="text-red-500 hover:text-red-700">X</button>
            </td>
        `;
        tbody.appendChild(tr);
        rowIdx++;
    }

    function updatePrice(sel, idx) {
        let price = sel.options[sel.selectedIndex].getAttribute('data-price');
        document.getElementById(`price_${idx}`).value = price;
        calcSub(idx);
    }

    function calcSub(idx) {
        let q = document.getElementById(`qty_${idx}`).value || 0;
        let p = document.getElementById(`price_${idx}`).value || 0;
        let sub = parseInt(q) * parseInt(p);
        document.getElementById(`subtext_${idx}`).innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(sub);
        calcGrand();
    }

    function calcGrand() {
        let total = 0;
        document.querySelectorAll('[id^="qty_"]').forEach(el => {
            let idx = el.id.split('_')[1];
            let q = document.getElementById(`qty_${idx}`).value || 0;
            let p = document.getElementById(`price_${idx}`).value || 0;
            total += parseInt(q) * parseInt(p);
        });
        document.getElementById('grandTotalText').innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(total);
    }

    document.addEventListener("DOMContentLoaded", function() { addRow(); });

    function openContactModal() {
        document.getElementById('quickContactModal').classList.remove('hidden');
    }
    
    function closeContactModal() {
        document.getElementById('quickContactModal').classList.add('hidden');
        document.getElementById('quickContactForm').reset();
    }

    async function submitQuickContact(e) {
        e.preventDefault();
        
        const form = document.getElementById('quickContactForm');
        const btn = document.getElementById('btnSaveContact');
        const formData = new FormData(form);
        
        btn.disabled = true;
        btn.innerText = 'Menyimpan...';

        try {
            const response = await fetch("{{ route('contacts.store') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: formData
            });

            const result = await response.json();

            if (response.ok && result.success) {
                const newOption = document.createElement("option");
                newOption.value = result.contact.id;
                newOption.text = result.contact.name;
                
                const dropdown = document.getElementById('contactDropdown');
                dropdown.add(newOption);
                dropdown.value = result.contact.id;

                closeContactModal();
                alert('Customer berhasil ditambahkan!');
            } else {
                alert(result.message || 'Gagal menambahkan kontak.');
            }
        } catch (error) {
            alert('Terjadi kesalahan jaringan.');
        } finally {
            btn.disabled = false;
            btn.innerText = 'Simpan';
        }
    }
</script>
@endsection