{{-- <!-- resources/views/purchases/index.blade.php -->
@extends('layouts.app')

@section('content')
@php
$suppliers = \App\Models\Contact::where('type', 'Supplier')->get();
$products = \App\Models\Product::all();
@endphp

<div class="space-y-6 pb-10">
    <div class="flex justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="shopping-cart" class="text-orange-500 w-7 h-7"></i> Pesanan Pembelian (PO)
            </h1>
            <p class="text-slate-500 mt-1">Buat dokumen pemesanan barang ke Pemasok/Supplier</p>
        </div>
        <button onclick="document.getElementById('poModal').classList.remove('hidden')"
            class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
            <i data-feather="plus" class="w-4 h-4"></i> Buat PO Baru
        </button>
    </div>

    <!-- Tabel PO -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="p-4 text-sm font-semibold text-slate-600">Tanggal</th>
                    <th class="p-4 text-sm font-semibold text-slate-600">No. Referensi</th>
                    <th class="p-4 text-sm font-semibold text-slate-600">Supplier</th>
                    <th class="p-4 text-sm font-semibold text-slate-600 text-right">Total Transaksi</th>
                    <th class="p-4 text-sm font-semibold text-slate-600 text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $po)
                <tr class="border-b border-slate-50 hover:bg-slate-50">
                    <td class="p-4 text-sm">{{ \Carbon\Carbon::parse($po->date)->format('d/m/Y') }}</td>
                    <td class="p-4 text-sm font-bold text-slate-800">{{ $po->reference_no ?: '-' }}</td>
                    <td class="p-4 text-sm font-medium text-blue-700">{{ $po->contact->name }}</td>
                    <td class="p-4 text-sm font-bold text-slate-800 text-right">Rp
                        {{ number_format($po->total, 0, ',', '.') }}</td>
                    <td class="p-4 text-center">
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold">{{
                            $po->status }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-slate-500">Belum ada pesanan pembelian.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Form PO -->
    <div id="poModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl overflow-hidden flex flex-col max-h-[95vh]">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 class="text-xl font-bold text-slate-800">Buat Pesanan Pembelian Baru</h3>
                <button onclick="document.getElementById('poModal').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-700"><i data-feather="x"></i></button>
            </div>

            <form action="{{ route('purchases.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <div class="p-6 overflow-y-auto custom-scrollbar flex-1 space-y-6">
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 bg-slate-50 p-4 rounded-lg border border-slate-200">

                        <div class="lg:col-span-2">
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-sm font-semibold text-slate-700">Pilih Supplier</label>
                                <!-- Tombol Panggil Modal Kontak -->
                                <button type="button" onclick="openContactModal()"
                                    class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1">
                                    <i data-feather="plus" class="w-3 h-3"></i> Tambah Baru
                                </button>
                            </div>
                            <select name="contact_id" id="contactDropdown" required
                                class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="" disabled selected>-- Pilih Pemasok --</option>
                                @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option> @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal</label>
                            <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                                class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">No. Referensi / PO</label>
                            <!-- Ubah placeholder agar user tahu ini bisa auto-generate -->
                            <input type="text" name="reference_no" placeholder="Otomatis (Cth: 001/PO/BMP/...)"
                                class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-[10px] text-slate-500 mt-1">Biarkan kosong untuk Auto-Generate.</p>
                        </div>
                    </div>

                    <!-- Tabel Dinamis Barang -->
                    <div>
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="font-bold text-slate-800">Detail Pesanan Barang</h4>
                            <button type="button" onclick="addRow()"
                                class="bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-sm font-semibold flex items-center gap-1">
                                + Tambah Baris
                            </button>
                        </div>
                        <table class="w-full text-left border-collapse border border-slate-300" id="itemTable">
                            <thead class="bg-slate-100">
                                <tr>
                                    <th class="p-2 border text-sm font-semibold">Produk</th>
                                    <th class="p-2 border text-sm font-semibold w-24">Qty</th>
                                    <th class="p-2 border text-sm font-semibold w-32">Harga Satuan</th>
                                    <th class="p-2 border text-sm font-semibold w-32 text-right">Subtotal</th>
                                    <th class="p-2 border w-12 text-center"></th>
                                </tr>
                            </thead>
                            <tbody id="itemBody">
                                <!-- Baris akan ditambahkan via JS -->
                            </tbody>
                        </table>
                        <div class="mt-4 text-right text-lg font-bold text-blue-700">
                            GRAND TOTAL: <span id="grandTotalText">Rp 0</span>
                        </div>
                    </div>
                </div>

                <div class="p-5 border-t border-slate-200 bg-slate-50 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('poModal').classList.add('hidden')"
                        class="px-5 py-2.5 text-slate-600 font-bold">Batal</button>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-bold">Simpan Pesanan
                        (PO)</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Mini: Tambah Kontak Cepat -->

<!-- Modal Mini: Tambah Kontak Cepat (Supplier) -->
<div id="quickContactModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-[60] p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col">
        <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-bold text-slate-800">Tambah Supplier Baru</h3>
            <button onclick="closeContactModal()" class="text-slate-400 hover:text-slate-700 transition-colors"><i
                    data-feather="x" class="w-5 h-5"></i></button>
        </div>
        <form id="quickContactForm" onsubmit="submitQuickContact(event)" class="p-5 space-y-4">
            <input type="hidden" name="type" value="Supplier"> <!-- Otomatis Supplier -->

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Perusahaan / Pemasok</label>
                    <input type="text" name="name" required
                        class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">No. Telepon / WA (Opsional)</label>
                    <input type="text" name="phone" placeholder="08..."
                        class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Email (Opsional)</label>
                    <input type="email" name="email" placeholder="email@contoh.com"
                        class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Alamat Lengkap</label>
                    <textarea name="address" rows="3" placeholder="Alamat jalan, kota, provinsi..."
                        class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeContactModal()"
                    class="px-4 py-2 text-slate-600 font-bold text-sm bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">Batal</button>
                <button type="submit" id="btnSaveContact"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold text-sm transition-colors">Simpan
                    Kontak</button>
            </div>
        </form>
    </div>
</div>

<!-- Data Produk untuk JS -->
<script>
    const productsData = @json($products);
    let rowIdx = 0;

    function addRow() {
        let tbody = document.getElementById('itemBody');
        let tr = document.createElement('tr');

        let selectOptions = `<option value="" disabled selected>Pilih Produk...</option>`;
        productsData.forEach(p => { selectOptions += `<option value="${p.sku}" data-price="${p.buy_price}">[${p.sku}] ${p.name}</option>`; });

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

    // Tambahkan 1 baris kosong saat halaman dimuat
    document.addEventListener("DOMContentLoaded", function () { addRow(); });

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
                // 1. Buat elemen <option> baru
                const newOption = document.createElement("option");
                newOption.value = result.contact.id;
                newOption.text = result.contact.name;

                // 2. Tambahkan ke dropdown dan otomatis pilih
                const dropdown = document.getElementById('contactDropdown');
                dropdown.add(newOption);
                dropdown.value = result.contact.id;

                // 3. Tutup modal
                closeContactModal();
                alert('Supplier berhasil ditambahkan!');
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

{{-- <!-- resources/views/purchases/index.blade.php -->
@extends('layouts.app')

@section('content')
    @php
        $suppliers = \App\Models\Contact::where('type', 'Supplier')->get();
        $products = \App\Models\Product::all();
    @endphp

    <div class="space-y-6 pb-10">
        <div class="flex justify-between items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                    <i data-feather="shopping-cart" class="text-orange-500 w-7 h-7"></i> Pesanan Pembelian (PO)
                </h1>
                <p class="text-slate-500 mt-1">Buat dokumen pemesanan barang ke Pemasok/Supplier</p>
            </div>
            <button onclick="document.getElementById('poModal').classList.remove('hidden')"
                class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
                <i data-feather="plus" class="w-4 h-4"></i> Buat PO Baru
            </button>
        </div>

        <!-- Tabel PO -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600">Tanggal</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">No. Referensi</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Supplier</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Jatuh Tempo</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Total Transaksi</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $po)
                        <tr class="border-b border-slate-50 hover:bg-slate-50">
                            <td class="p-4 text-sm">{{ \Carbon\Carbon::parse($po->date)->format('d/m/Y') }}</td>
                            <td class="p-4 text-sm font-bold text-slate-800">{{ $po->reference_no ?: '-' }}</td>
                            <td class="p-4 text-sm font-medium text-blue-700">{{ $po->contact->name }}</td>

                            <!-- Menampilkan Tanggal Jatuh Tempo di Tabel -->
                            <td class="p-4 text-sm">
                                @if($po->due_date)
                                    <span class="px-2 py-1 rounded bg-slate-100 text-slate-700 font-medium border border-slate-200">
                                        {{ \Carbon\Carbon::parse($po->due_date)->format('d/m/Y') }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>

                            <td class="p-4 text-sm font-bold text-slate-800 text-right">Rp
                                {{ number_format($po->total, 0, ',', '.') }}</td>
                            <td class="p-4 text-center">
                                <span
                                    class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold">{{ $po->status }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500">Belum ada pesanan pembelian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Modal Form PO -->
        <div id="poModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl overflow-hidden flex flex-col max-h-[95vh]">
                <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-xl font-bold text-slate-800">Buat Pesanan Pembelian Baru</h3>
                    <button onclick="document.getElementById('poModal').classList.add('hidden')"
                        class="text-slate-400 hover:text-slate-700"><i data-feather="x"></i></button>
                </div>

                <form action="{{ route('purchases.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden">
                    @csrf
                    <div class="p-6 overflow-y-auto custom-scrollbar flex-1 space-y-6">
                        <!-- Ubah grid menjadi 5 kolom untuk memberi ruang pada input Jatuh Tempo -->
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 bg-slate-50 p-4 rounded-lg border border-slate-200">
                            <div class="lg:col-span-2">
                                <div class="flex justify-between items-center mb-1">
                                    <label class="block text-sm font-semibold text-slate-700">Pilih Supplier</label>
                                    <!-- Tombol Panggil Modal Kontak -->
                                    <button type="button" onclick="openContactModal()"
                                        class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1">
                                        <i data-feather="plus" class="w-3 h-3"></i> Tambah Baru
                                    </button>
                                </div>
                                <select name="contact_id" id="contactDropdown" required
                                    class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="" disabled selected>-- Pilih Pemasok --</option>
                                    @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal</label>
                                <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                                    class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-rose-700 mb-1">Jatuh Tempo</label>
                                <!-- Input Jatuh Tempo ditambahkan -->
                                <input type="date" name="due_date" required value="{{ date('Y-m-d') }}"
                                    class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">No. Referensi / PO</label>
                                <!-- Kolom dibuat Disabled/Readonly dan menampilkan angka PO selanjutnya secara Live -->
                                <input type="text" value="{{ $nextDocNumber }}" disabled
                                    class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none bg-slate-100 text-slate-500 font-bold cursor-not-allowed text-center">
                                <p class="text-[10px] text-slate-500 mt-1 font-medium">Diisi otomatis oleh sistem.</p>
                            </div>
                        </div>

                        <!-- Tabel Dinamis Barang -->
                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-bold text-slate-800">Detail Pesanan Barang</h4>
                                <button type="button" onclick="addRow()"
                                    class="bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-sm font-semibold flex items-center gap-1">
                                    + Tambah Baris
                                </button>
                            </div>
                            <table class="w-full text-left border-collapse border border-slate-300" id="itemTable">
                                <thead class="bg-slate-100">
                                    <tr>
                                        <th class="p-2 border text-sm font-semibold">Produk</th>
                                        <th class="p-2 border text-sm font-semibold w-24">Qty</th>
                                        <th class="p-2 border text-sm font-semibold w-32">Harga Satuan</th>
                                        <th class="p-2 border text-sm font-semibold w-32 text-right">Subtotal</th>
                                        <th class="p-2 border w-12 text-center"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemBody">
                                    <!-- Baris akan ditambahkan via JS -->
                                </tbody>
                            </table>
                            <div class="mt-4 text-right text-lg font-bold text-blue-700">
                                GRAND TOTAL: <span id="grandTotalText">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 border-t border-slate-200 bg-slate-50 flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('poModal').classList.add('hidden')"
                            class="px-5 py-2.5 text-slate-600 font-bold">Batal</button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-bold">Simpan Pesanan
                            (PO)</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Mini: Tambah Kontak Cepat (Supplier) -->
    <div id="quickContactModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-[60] p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 class="text-lg font-bold text-slate-800">Tambah Supplier Baru</h3>
                <button onclick="closeContactModal()" class="text-slate-400 hover:text-slate-700 transition-colors"><i
                        data-feather="x" class="w-5 h-5"></i></button>
            </div>
            <form id="quickContactForm" onsubmit="submitQuickContact(event)" class="p-5 space-y-4">
                <input type="hidden" name="type" value="Supplier"> <!-- Otomatis Supplier -->

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Perusahaan / Pemasok</label>
                        <input type="text" name="name" required
                            class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">No. Telepon / WA (Opsional)</label>
                        <input type="text" name="phone" placeholder="08..."
                            class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Email (Opsional)</label>
                        <input type="email" name="email" placeholder="email@contoh.com"
                            class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Alamat Lengkap</label>
                        <textarea name="address" rows="3" placeholder="Alamat jalan, kota, provinsi..."
                            class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeContactModal()"
                        class="px-4 py-2 text-slate-600 font-bold text-sm bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">Batal</button>
                    <button type="submit" id="btnSaveContact"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold text-sm transition-colors">Simpan
                        Kontak</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Produk untuk JS -->
    <script>
        const productsData = @json($products);
        let rowIdx = 0;

        function addRow() {
            let tbody = document.getElementById('itemBody');
            let tr = document.createElement('tr');

            let selectOptions = `<option value="" disabled selected>Pilih Produk...</option>`;
            productsData.forEach(p => { selectOptions += `<option value="${p.sku}" data-price="${p.buy_price}">[${p.sku}] ${p.name}</option>`; });

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

        // Tambahkan 1 baris kosong saat halaman dimuat
        document.addEventListener("DOMContentLoaded", function () { addRow(); });

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
                    // 1. Buat elemen <option> baru
                    const newOption = document.createElement("option");
                    newOption.value = result.contact.id;
                    newOption.text = result.contact.name;

                    // 2. Tambahkan ke dropdown dan otomatis pilih
                    const dropdown = document.getElementById('contactDropdown');
                    dropdown.add(newOption);
                    dropdown.value = result.contact.id;

                    // 3. Tutup modal
                    closeContactModal();
                    alert('Supplier berhasil ditambahkan!');
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

{{-- <!-- resources/views/purchases/index.blade.php -->
@extends('layouts.app')

@section('content')
    @php
        $suppliers = \App\Models\Contact::where('type', 'Supplier')->get();
        $products = \App\Models\Product::all();
    @endphp

    <div class="space-y-6 pb-10">
        <div class="flex justify-between items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                    <i data-feather="shopping-cart" class="text-orange-500 w-7 h-7"></i> Pesanan Pembelian (PO)
                </h1>
                <p class="text-slate-500 mt-1">Buat dokumen pemesanan barang ke Pemasok/Supplier</p>
            </div>
            <button onclick="document.getElementById('poModal').classList.remove('hidden')"
                class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
                <i data-feather="plus" class="w-4 h-4"></i> Buat PO Baru
            </button>
        </div>

        <!-- Tabel PO -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600">Tanggal</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">No. Referensi</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Supplier</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Jatuh Tempo</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Total Transaksi</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $po)
                        <tr class="border-b border-slate-50 hover:bg-slate-50">
                            <td class="p-4 text-sm">{{ \Carbon\Carbon::parse($po->date)->format('d/m/Y') }}</td>
                            <td class="p-4 text-sm font-bold text-slate-800">{{ $po->reference_no ?: '-' }}</td>
                            <td class="p-4 text-sm font-medium text-blue-700">{{ $po->contact->name }}</td>

                            <!-- Menampilkan Tanggal Jatuh Tempo di Tabel -->
                            <td class="p-4 text-sm">
                                @if($po->due_date)
                                    <span class="px-2 py-1 rounded bg-slate-100 text-slate-700 font-medium border border-slate-200">
                                        {{ \Carbon\Carbon::parse($po->due_date)->format('d/m/Y') }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>

                            <td class="p-4 text-sm font-bold text-slate-800 text-right">Rp
                                {{ number_format($po->total, 0, ',', '.') }}</td>
                            <td class="p-4 text-center">
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold">{{ $po->status }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500">Belum ada pesanan pembelian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Modal Form PO -->
        <div id="poModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl overflow-hidden flex flex-col max-h-[95vh]">
                <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-xl font-bold text-slate-800">Buat Pesanan Pembelian Baru</h3>
                    <button onclick="document.getElementById('poModal').classList.add('hidden')"
                        class="text-slate-400 hover:text-slate-700"><i data-feather="x"></i></button>
                </div>

                <!-- PERHATIAN: Tambahkan event onsubmit="unformatRibuan(this)" di sini -->
                <form action="{{ route('purchases.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden" onsubmit="unformatRibuan(this)">
                    @csrf
                    <div class="p-6 overflow-y-auto custom-scrollbar flex-1 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 bg-slate-50 p-4 rounded-lg border border-slate-200">
                            <div class="lg:col-span-2">
                                <div class="flex justify-between items-center mb-1">
                                    <label class="block text-sm font-semibold text-slate-700">Pilih Supplier</label>
                                    <button type="button" onclick="openContactModal()"
                                        class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1">
                                        <i data-feather="plus" class="w-3 h-3"></i> Tambah Baru
                                    </button>
                                </div>
                                <select name="contact_id" id="contactDropdown" required
                                    class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="" disabled selected>-- Pilih Pemasok --</option>
                                    @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal</label>
                                <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                                    class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Jatuh Tempo</label>
                                <input type="date" name="due_date" required value="{{ date('Y-m-d') }}"
                                    class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">No. Referensi / PO</label>
                                <input type="text" value="{{ $nextDocNumber ?? '' }}" disabled
                                    class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none bg-slate-100 text-slate-500 font-bold cursor-not-allowed text-center">
                                <p class="text-[10px] text-slate-500 mt-1 font-medium">Diisi otomatis oleh sistem.</p>
                            </div>
                        </div>

                        <!-- Tabel Dinamis Barang -->
                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-bold text-slate-800">Detail Pesanan Barang</h4>
                                <button type="button" onclick="addRow()"
                                    class="bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-sm font-semibold flex items-center gap-1">
                                    + Tambah Baris
                                </button>
                            </div>
                            <table class="w-full text-left border-collapse border border-slate-300" id="itemTable">
                                <thead class="bg-slate-100">
                                    <tr>
                                        <th class="p-2 border text-sm font-semibold">Produk</th>
                                        <th class="p-2 border text-sm font-semibold w-24">Qty</th>
                                        <th class="p-2 border text-sm font-semibold w-32">Harga Satuan</th>
                                        <th class="p-2 border text-sm font-semibold w-32 text-right">Subtotal</th>
                                        <th class="p-2 border w-12 text-center"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemBody">
                                    <!-- Baris akan ditambahkan via JS -->
                                </tbody>
                            </table>
                            <div class="mt-4 text-right text-lg font-bold text-blue-700">
                                GRAND TOTAL: <span id="grandTotalText">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 border-t border-slate-200 bg-slate-50 flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('poModal').classList.add('hidden')"
                            class="px-5 py-2.5 text-slate-600 font-bold">Batal</button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-bold">Simpan Pesanan
                            (PO)</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Kontak Cepat -->
    <div id="quickContactModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-[60] p-4">
        <!-- ... (Kode modal kontak Anda biarkan persis sama) ... -->
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 class="text-lg font-bold text-slate-800">Tambah Supplier Baru</h3>
                <button onclick="closeContactModal()" class="text-slate-400 hover:text-slate-700 transition-colors"><i
                        data-feather="x" class="w-5 h-5"></i></button>
            </div>
            <form id="quickContactForm" onsubmit="submitQuickContact(event)" class="p-5 space-y-4">
                <input type="hidden" name="type" value="Supplier">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Perusahaan / Pemasok</label>
                        <input type="text" name="name" required
                            class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">No. Telepon / WA (Opsional)</label>
                        <input type="text" name="phone" placeholder="08..."
                            class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Email (Opsional)</label>
                        <input type="email" name="email" placeholder="email@contoh.com"
                            class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Alamat Lengkap</label>
                        <textarea name="address" rows="3" placeholder="Alamat jalan, kota, provinsi..."
                            class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeContactModal()"
                        class="px-4 py-2 text-slate-600 font-bold text-sm bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">Batal</button>
                    <button type="submit" id="btnSaveContact"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold text-sm transition-colors">Simpan
                        Kontak</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ==========================================
        // HELPER JAVASCRIPT GLOBAL UNTUK INPUT ANGKA
        // ==========================================
        function formatRibuan(inputElement) {
            let value = inputElement.value.replace(/[^0-9]/g, '');
            if (value !== '') {
                inputElement.value = parseInt(value, 10).toLocaleString('id-ID');
            } else {
                inputElement.value = '';
            }
        }

        function unformatRibuan(formElement) {
            let formattedInputs = formElement.querySelectorAll('.format-ribuan');
            formattedInputs.forEach(input => {
                input.value = input.value.replace(/\./g, '');
            });
        }

        // ==========================================
        // LOGIKA TABEL DINAMIS PEMBELIAN
        // ==========================================
        const productsData = @json($products);
        let rowIdx = 0;

        function addRow() {
            let tbody = document.getElementById('itemBody');
            let tr = document.createElement('tr');

            let selectOptions = `<option value="" disabled selected>Pilih Produk...</option>`;
            productsData.forEach(p => { selectOptions += `<option value="${p.sku}" data-price="${p.buy_price}">[${p.sku}] ${p.name}</option>`; });

            // PERHATIAN: Type input diubah menjadi text, class "format-ribuan" ditambahkan, dan oninput mengatur pemformatan sekaligus kalkulasi
            tr.innerHTML = `
                <td class="p-1 border">
                    <select name="items[${rowIdx}][product_sku]" onchange="updatePrice(this, ${rowIdx})" required class="w-full p-1.5 text-sm outline-none bg-transparent">
                        ${selectOptions}
                    </select>
                </td>
                <td class="p-1 border">
                    <input type="text" name="items[${rowIdx}][quantity]" id="qty_${rowIdx}" value="1" required oninput="formatRibuan(this); calcSub(${rowIdx})" class="format-ribuan w-full p-1.5 text-sm text-center outline-none bg-transparent">
                </td>
                <td class="p-1 border">
                    <input type="text" name="items[${rowIdx}][price]" id="price_${rowIdx}" required oninput="formatRibuan(this); calcSub(${rowIdx})" class="format-ribuan w-full p-1.5 text-sm text-right outline-none bg-transparent">
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
            let priceInput = document.getElementById(`price_${idx}`);

            // Masukkan harga ke kolom, lalu panggil fungsi format agar langsung bertitik
            priceInput.value = price;
            formatRibuan(priceInput);

            calcSub(idx);
        }

        function calcSub(idx) {
            // Bersihkan titik sebelum dikali
            let rawQ = document.getElementById(`qty_${idx}`).value.replace(/\./g, '') || 0;
            let rawP = document.getElementById(`price_${idx}`).value.replace(/\./g, '') || 0;

            let sub = parseInt(rawQ, 10) * parseInt(rawP, 10);
            document.getElementById(`subtext_${idx}`).innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(sub);
            calcGrand();
        }

        function calcGrand() {
            let total = 0;
            document.querySelectorAll('[id^="qty_"]').forEach(el => {
                let idx = el.id.split('_')[1];
                let rawQ = document.getElementById(`qty_${idx}`).value.replace(/\./g, '') || 0;
                let rawP = document.getElementById(`price_${idx}`).value.replace(/\./g, '') || 0;

                total += parseInt(rawQ, 10) * parseInt(rawP, 10);
            });
            document.getElementById('grandTotalText').innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(total);
        }

        document.addEventListener("DOMContentLoaded", function () { addRow(); });

        // ==========================================
        // AJAX MODAL KONTAK
        // ==========================================
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
                    alert('Supplier berhasil ditambahkan!');
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

{{-- <!-- resources/views/purchases/index.blade.php -->
@extends('layouts.app')

@section('content')
    @php
        $suppliers = \App\Models\Contact::where('type', 'Supplier')->get();
        $products = \App\Models\Product::all();
    @endphp

    <div class="space-y-6 pb-10">
        <div class="flex justify-between items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                    <i data-feather="shopping-cart" class="text-orange-500 w-7 h-7"></i> Pesanan Pembelian (PO)
                </h1>
                <p class="text-slate-500 mt-1">Buat dokumen pemesanan barang ke Pemasok/Supplier</p>
            </div>
            <button onclick="document.getElementById('poModal').classList.remove('hidden')"
                class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
                <i data-feather="plus" class="w-4 h-4"></i> Buat PO Baru
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600">Tanggal</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">No. Referensi</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Supplier</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Jatuh Tempo</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Total Transaksi</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Status</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $po)
                        <tr class="border-b border-slate-50 hover:bg-slate-50">
                            <td class="p-4 text-sm">{{ \Carbon\Carbon::parse($po->date)->format('d/m/Y') }}</td>
                            <td class="p-4 text-sm font-bold text-slate-800">{{ $po->reference_no ?: '-' }}</td>
                            <td class="p-4 text-sm font-medium text-blue-700">{{ $po->contact->name }}</td>
                            <td class="p-4 text-sm">
                                @if($po->due_date)
                                    <span class="px-2 py-1 rounded bg-slate-100 text-slate-700 font-medium border border-slate-200">
                                        {{ \Carbon\Carbon::parse($po->due_date)->format('d/m/Y') }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="p-4 text-sm font-bold text-slate-800 text-right">Rp {{ number_format($po->total, 0, ',', '.') }}</td>
                            <td class="p-4 text-center">
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold">{{ $po->status }}</span>
                            </td>
                            <td class="p-4 text-center">
                                <!-- Tombol Buka Modal Detail -->
                                <button onclick="document.getElementById('detailModal_{{ $po->id }}').classList.remove('hidden')" class="text-blue-600 hover:bg-blue-50 p-2 rounded-lg transition-colors" title="Lihat Detail Barang">
                                    <i data-feather="eye" class="w-4 h-4"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-8 text-center text-slate-500">Belum ada pesanan pembelian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- MODAL DETAIL UNTUK TIAP PO (DIBUAT OTOMATIS OLEH LOOP) -->
        @foreach($purchases as $po)
        <div id="detailModal_{{ $po->id }}" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-[70] p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh]">
                <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-xl font-bold text-slate-800">Rincian Dokumen: {{ $po->reference_no ?: '#'.$po->id }}</h3>
                    <button onclick="document.getElementById('detailModal_{{ $po->id }}').classList.add('hidden')" class="text-slate-400 hover:text-slate-700"><i data-feather="x"></i></button>
                </div>
                <div class="p-6 overflow-y-auto custom-scrollbar">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div><p class="text-xs text-slate-500 font-semibold mb-1">Supplier / Pemasok</p><p class="font-bold text-slate-800 text-lg">{{ $po->contact->name }}</p></div>
                        <div class="text-right"><p class="text-xs text-slate-500 font-semibold mb-1">Tanggal Terbit</p><p class="font-bold text-slate-800 text-lg">{{ \Carbon\Carbon::parse($po->date)->format('d F Y') }}</p></div>
                    </div>
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
                            @foreach($po->items as $item)
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
                                <th class="p-3 border text-right font-bold text-blue-700 text-lg">Rp {{ number_format($po->total, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        @endforeach

        <!-- Modal Form PO Utama -->
        <div id="poModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl overflow-hidden flex flex-col max-h-[95vh]">
                <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-xl font-bold text-slate-800">Buat Pesanan Pembelian Baru</h3>
                    <button onclick="document.getElementById('poModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700"><i data-feather="x"></i></button>
                </div>

                <form action="{{ route('purchases.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden" onsubmit="unformatRibuan(this)">
                    @csrf
                    <div class="p-6 overflow-y-auto custom-scrollbar flex-1 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 bg-slate-50 p-4 rounded-lg border border-slate-200">
                            <div class="lg:col-span-2">
                                <div class="flex justify-between items-center mb-1">
                                    <label class="block text-sm font-semibold text-slate-700">Pilih Supplier</label>
                                    <button type="button" onclick="openContactModal()" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1">
                                        <i data-feather="plus" class="w-3 h-3"></i> Tambah Baru
                                    </button>
                                </div>
                                <select name="contact_id" id="contactDropdown" required class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="" disabled selected>-- Pilih Pemasok --</option>
                                    @foreach($suppliers as $s) <option value="{{ $s->id }}">{{ $s->name }}</option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal</label>
                                <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Jatuh Tempo</label>
                                <input type="date" name="due_date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">No. Referensi / PO</label>
                                <input type="text" value="{{ $nextDocNumber ?? '' }}" disabled class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none bg-slate-100 text-slate-500 font-bold cursor-not-allowed text-center">
                                <p class="text-[10px] text-slate-500 mt-1 font-medium">Diisi otomatis oleh sistem.</p>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-bold text-slate-800">Detail Pesanan Barang</h4>
                                <button type="button" onclick="addRow()" class="bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-sm font-semibold flex items-center gap-1">
                                    + Tambah Baris
                                </button>
                            </div>
                            <table class="w-full text-left border-collapse border border-slate-300" id="itemTable">
                                <thead class="bg-slate-100">
                                    <tr>
                                        <th class="p-2 border text-sm font-semibold">Produk</th>
                                        <th class="p-2 border text-sm font-semibold w-24">Qty</th>
                                        <th class="p-2 border text-sm font-semibold w-32">Harga Satuan</th>
                                        <th class="p-2 border text-sm font-semibold w-32 text-right">Subtotal</th>
                                        <th class="p-2 border w-12 text-center"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemBody"></tbody>
                            </table>
                            <div class="mt-4 text-right text-lg font-bold text-blue-700">
                                GRAND TOTAL: <span id="grandTotalText">Rp 0</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-5 border-t border-slate-200 bg-slate-50 flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('poModal').classList.add('hidden')" class="px-5 py-2.5 text-slate-600 font-bold">Batal</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-bold">Simpan Pesanan (PO)</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Kontak Cepat -->
    <div id="quickContactModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-[60] p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 class="text-lg font-bold text-slate-800">Tambah Supplier Baru</h3>
                <button onclick="closeContactModal()" class="text-slate-400 hover:text-slate-700 transition-colors"><i data-feather="x" class="w-5 h-5"></i></button>
            </div>
            <form id="quickContactForm" onsubmit="submitQuickContact(event)" class="p-5 space-y-4">
                <input type="hidden" name="type" value="Supplier">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Perusahaan / Pemasok</label>
                        <input type="text" name="name" required class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">No. Telepon / WA (Opsional)</label>
                        <input type="text" name="phone" placeholder="08..." class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Email (Opsional)</label>
                        <input type="email" name="email" placeholder="email@contoh.com" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Alamat Lengkap</label>
                        <textarea name="address" rows="3" placeholder="Alamat jalan, kota, provinsi..." class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeContactModal()" class="px-4 py-2 text-slate-600 font-bold text-sm bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">Batal</button>
                    <button type="submit" id="btnSaveContact" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold text-sm transition-colors">Simpan Kontak</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script Kumpulan Fungsi -->
    <script>
        function formatRibuan(inputElement) {
            let value = inputElement.value.replace(/[^0-9]/g, '');
            if (value !== '') { inputElement.value = parseInt(value, 10).toLocaleString('id-ID'); }
            else { inputElement.value = ''; }
        }
        function unformatRibuan(formElement) {
            let formattedInputs = formElement.querySelectorAll('.format-ribuan');
            formattedInputs.forEach(input => { input.value = input.value.replace(/\./g, ''); });
        }

        const productsData = @json($products);
        let rowIdx = 0;
        function addRow() {
            let tbody = document.getElementById('itemBody');
            let tr = document.createElement('tr');
            let selectOptions = `<option value="" disabled selected>Pilih Produk...</option>`;
            productsData.forEach(p => { selectOptions += `<option value="${p.sku}" data-price="${p.buy_price}">[${p.sku}] ${p.name}</option>`; });

            tr.innerHTML = `
                <td class="p-1 border">
                    <select name="items[${rowIdx}][product_sku]" onchange="updatePrice(this, ${rowIdx})" required class="w-full p-1.5 text-sm outline-none bg-transparent">${selectOptions}</select>
                </td>
                <td class="p-1 border">
                    <input type="text" name="items[${rowIdx}][quantity]" id="qty_${rowIdx}" value="1" required oninput="formatRibuan(this); calcSub(${rowIdx})" class="format-ribuan w-full p-1.5 text-sm text-center outline-none bg-transparent">
                </td>
                <td class="p-1 border">
                    <input type="text" name="items[${rowIdx}][price]" id="price_${rowIdx}" required oninput="formatRibuan(this); calcSub(${rowIdx})" class="format-ribuan w-full p-1.5 text-sm text-right outline-none bg-transparent">
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
            let priceInput = document.getElementById(`price_${idx}`);
            priceInput.value = price;
            formatRibuan(priceInput);
            calcSub(idx);
        }

        function calcSub(idx) {
            let rawQ = document.getElementById(`qty_${idx}`).value.replace(/\./g, '') || 0;
            let rawP = document.getElementById(`price_${idx}`).value.replace(/\./g, '') || 0;
            let sub = parseInt(rawQ, 10) * parseInt(rawP, 10);
            document.getElementById(`subtext_${idx}`).innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(sub);
            calcGrand();
        }

        function calcGrand() {
            let total = 0;
            document.querySelectorAll('[id^="qty_"]').forEach(el => {
                let idx = el.id.split('_')[1];
                let rawQ = document.getElementById(`qty_${idx}`).value.replace(/\./g, '') || 0;
                let rawP = document.getElementById(`price_${idx}`).value.replace(/\./g, '') || 0;
                total += parseInt(rawQ, 10) * parseInt(rawP, 10);
            });
            document.getElementById('grandTotalText').innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(total);
        }

        document.addEventListener("DOMContentLoaded", function () { addRow(); });

        function openContactModal() { document.getElementById('quickContactModal').classList.remove('hidden'); }
        function closeContactModal() { document.getElementById('quickContactModal').classList.add('hidden'); document.getElementById('quickContactForm').reset(); }

        async function submitQuickContact(e) {
            e.preventDefault();
            const form = document.getElementById('quickContactForm');
            const btn = document.getElementById('btnSaveContact');
            const formData = new FormData(form);
            btn.disabled = true; btn.innerText = 'Menyimpan...';

            try {
                const response = await fetch("{{ route('contacts.store') }}", {
                    method: "POST",
                    headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" },
                    body: formData
                });
                const result = await response.json();
                if (response.ok && result.success) {
                    const newOption = document.createElement("option");
                    newOption.value = result.contact.id;
                    newOption.text = result.contact.name;
                    document.getElementById('contactDropdown').add(newOption);
                    document.getElementById('contactDropdown').value = result.contact.id;
                    closeContactModal();
                    alert('Supplier berhasil ditambahkan!');
                } else { alert(result.message || 'Gagal menambahkan kontak.'); }
            } catch (error) { alert('Terjadi kesalahan jaringan.'); } finally {
                btn.disabled = false; btn.innerText = 'Simpan';
            }
        }
    </script>
@endsection --}}

{{-- <!-- resources/views/purchases/index.blade.php -->
@extends('layouts.app')

@section('content')
    @php
        $suppliers = \App\Models\Contact::where('type', 'Supplier')->get();
        // PERHATIAN: Tarik data produk sekaligus dengan jumlah (Sum) stok aktif dari tabel relasi
        $products = \App\Models\Product::withSum('stocks as total_stock', 'quantity')->get();
    @endphp

    <div class="space-y-6 pb-10">
        <div class="flex justify-between items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                    <i data-feather="shopping-cart" class="text-orange-500 w-7 h-7"></i> Pesanan Pembelian (PO)
                </h1>
                <p class="text-slate-500 mt-1">Buat dokumen pemesanan barang ke Pemasok/Supplier</p>
            </div>
            <!-- CEK HAK AKSES CREATE (C) -->
            @if(\App\Models\CompanySetting::canAction('C', 'Pembelian'))
            <button onclick="document.getElementById('poModal').classList.remove('hidden')"
                class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
                <i data-feather="plus" class="w-4 h-4"></i> Buat PO Baru
            </button>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600">Tanggal</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">No. Referensi</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Supplier</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Jatuh Tempo</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Total Transaksi</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Status</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $po)
                        <tr class="border-b border-slate-50 hover:bg-slate-50">
                            <td class="p-4 text-sm">{{ \Carbon\Carbon::parse($po->date)->format('d/m/Y') }}</td>
                            <td class="p-4 text-sm font-bold text-slate-800">{{ $po->reference_no ?: '-' }}</td>
                            <td class="p-4 text-sm font-medium text-blue-700">{{ $po->contact->name }}</td>
                            <td class="p-4 text-sm">
                                @if($po->due_date)
                                    <span class="px-2 py-1 rounded bg-slate-100 text-slate-700 font-medium border border-slate-200">
                                        {{ \Carbon\Carbon::parse($po->due_date)->format('d/m/Y') }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="p-4 text-sm font-bold text-slate-800 text-right">Rp {{ number_format($po->total, 0, ',', '.') }}</td>

                            <!-- Kolom Status Berwarna -->
                            <td class="p-4 text-center">
                                @if($po->status == 'Penawaran')
                                    <span class="px-3 py-1 bg-slate-100 text-slate-600 border border-slate-200 rounded-full text-xs font-bold whitespace-nowrap"><i data-feather="file-text" class="w-3 h-3 inline mr-1"></i>Draf RFQ</span>
                                @elseif($po->status == 'Menunggu Persetujuan')
                                    <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold whitespace-nowrap"><i data-feather="clock" class="w-3 h-3 inline mr-1"></i>Menunggu</span>
                                @elseif($po->status == 'Pesanan')
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold"><i data-feather="check" class="w-3 h-3 inline mr-1"></i>Disetujui</span>
                                @elseif($po->status == 'Ditolak')
                                    <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold line-through">Ditolak</span>
                                @elseif($po->status == 'Diretur')
                                    <span class="px-3 py-1 bg-rose-100 text-rose-800 rounded-full text-xs font-bold"><i data-feather="corner-up-right" class="w-3 h-3 inline mr-1"></i>Diretur (Kembali)</span>
                                @else
                                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold">{{ $po->status }}</span>
                                @endif
                            </td>

                            <!-- Kolom Aksi Dinamis -->
                            <td class="p-4 text-center">
                                <div class="flex justify-center items-center gap-2">
                                    <button onclick="document.getElementById('detailModal_{{ $po->id }}').classList.remove('hidden')" class="text-blue-600 hover:bg-blue-50 p-2 rounded-lg transition-colors shadow-sm border border-transparent hover:border-blue-200" title="Lihat Detail Barang">
                                        <i data-feather="eye" class="w-4 h-4"></i>
                                    </button>

                                    <!-- TOMBOL KHUSUS STATUS PENAWARAN -->
                                    @if($po->status == 'Penawaran')
                                        <a href="{{ route('purchases.printQuotation', $po->id) }}" target="_blank" class="text-slate-500 hover:text-slate-800 hover:bg-slate-100 p-2 rounded-lg transition-colors border border-slate-200 shadow-sm" title="Cetak Request for Quotation (PDF)">
                                            <i data-feather="printer" class="w-4 h-4"></i>
                                        </a>

                                        @if(\App\Models\CompanySetting::canAction('C', 'Pembelian') || \App\Models\CompanySetting::canAction('U', 'Pembelian'))
                                        <form action="{{ route('purchases.convertToPo', $po->id) }}" method="POST" onsubmit="return confirm('Harga telah disepakati oleh Supplier? Dokumen akan diubah permanen menjadi Pesanan Pembelian (PO) resmi. Lanjutkan?');">
                                            @csrf
                                            <button type="submit" class="text-orange-600 hover:bg-orange-50 p-2 rounded-lg transition-colors shadow-sm border border-transparent hover:border-orange-200" title="Ubah ke PO">
                                                <i data-feather="arrow-right-circle" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                        @endif
                                    @endif

                                    <!-- Tombol Setuju/Tolak (Khusus Status Menunggu & Memiliki Hak UPDATE) -->
                                    @if($po->status == 'Menunggu Persetujuan' && \App\Models\CompanySetting::canAction('U', 'Pembelian'))
                                        <form action="{{ route('purchases.approve', $po->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENYETUJUI dokumen ini?');">
                                            @csrf
                                            <button type="submit" class="text-emerald-600 hover:bg-emerald-50 p-2 rounded-lg transition-colors shadow-sm border border-transparent hover:border-emerald-200" title="Setujui PO">
                                                <i data-feather="check-circle" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('purchases.reject', $po->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENOLAK dokumen ini secara permanen?');">
                                            @csrf
                                            <button type="submit" class="text-red-500 hover:bg-red-50 p-2 rounded-lg transition-colors shadow-sm border border-transparent hover:border-red-200" title="Tolak PO">
                                                <i data-feather="x-circle" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-8 text-center text-slate-500">Belum ada pesanan pembelian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @foreach($purchases as $po)
        <div id="detailModal_{{ $po->id }}" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-[70] p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh]">
                <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-xl font-bold text-slate-800">Rincian Dokumen: {{ $po->reference_no ?: '#'.$po->id }}</h3>
                    <button onclick="document.getElementById('detailModal_{{ $po->id }}').classList.add('hidden')" class="text-slate-400 hover:text-slate-700"><i data-feather="x"></i></button>
                </div>
                <div class="p-6 overflow-y-auto custom-scrollbar">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div><p class="text-xs text-slate-500 font-semibold mb-1">Supplier / Pemasok</p><p class="font-bold text-slate-800 text-lg">{{ $po->contact->name }}</p></div>
                        <div class="text-right"><p class="text-xs text-slate-500 font-semibold mb-1">Tanggal Terbit</p><p class="font-bold text-slate-800 text-lg">{{ \Carbon\Carbon::parse($po->date)->format('d F Y') }}</p></div>
                    </div>
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
                            @foreach($po->items as $item)
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
                                <th class="p-3 border text-right font-bold text-blue-700 text-lg">Rp {{ number_format($po->total, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        @endforeach

        <!-- Modal Form PO Utama -->
        <div id="poModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl overflow-hidden flex flex-col max-h-[95vh]">
                <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-xl font-bold text-slate-800">Buat Pesanan Pembelian Baru</h3>
                    <button onclick="document.getElementById('poModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700"><i data-feather="x"></i></button>
                </div>

                <form action="{{ route('purchases.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden" onsubmit="unformatRibuan(this)">
                    @csrf
                    <div class="p-6 overflow-y-auto custom-scrollbar flex-1 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 bg-slate-50 p-4 rounded-lg border border-slate-200">
                            <div class="lg:col-span-2">
                                <div class="flex justify-between items-center mb-1">
                                    <label class="block text-sm font-semibold text-slate-700">Pilih Supplier</label>
                                    <button type="button" onclick="openContactModal()" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1">
                                        <i data-feather="plus" class="w-3 h-3"></i> Tambah Baru
                                    </button>
                                </div>
                                <select name="contact_id" id="contactDropdown" required class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="" disabled selected>-- Pilih Pemasok --</option>
                                    @foreach($suppliers as $s) <option value="{{ $s->id }}">{{ $s->name }}</option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal</label>
                                <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Jatuh Tempo</label>
                                <input type="date" name="due_date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">No. Referensi / PO</label>
                                <input type="text" value="{{ $nextDocNumber ?? '' }}" disabled class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none bg-slate-100 text-slate-500 font-bold cursor-not-allowed text-center">
                                <p class="text-[10px] text-slate-500 mt-1 font-medium">Diisi otomatis oleh sistem.</p>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-bold text-slate-800">Detail Pesanan Barang</h4>
                                <button type="button" onclick="addRow()" class="bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-sm font-semibold flex items-center gap-1">
                                    + Tambah Baris
                                </button>
                            </div>
                            <table class="w-full text-left border-collapse border border-slate-300" id="itemTable">
                                <thead class="bg-slate-100">
                                    <tr>
                                        <th class="p-2 border text-sm font-semibold">Produk (Info Stok)</th>
                                        <th class="p-2 border text-sm font-semibold w-24">Qty</th>
                                        <th class="p-2 border text-sm font-semibold w-32">Harga Satuan</th>
                                        <!-- TAMBAHKAN KOLOM PPN DI SINI -->
                                        <th class="p-2 border text-sm font-semibold w-24 text-center">PPN</th>
                                        <th class="p-2 border text-sm font-semibold w-32 text-right">Subtotal</th>
                                        <th class="p-2 border w-12 text-center"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemBody"></tbody>
                            </table>
                            <div class="mt-4 text-right text-lg font-bold text-blue-700">
                                GRAND TOTAL: <span id="grandTotalText">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 border-t border-slate-200 bg-slate-50 flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('poModal').classList.add('hidden')" class="px-5 py-2.5 text-slate-600 font-bold">Batal</button>

                        <!-- TOMBOL OPSI: QUOTATION ATAU PO LANGSUNG -->
                        <button type="submit" name="action" value="quotation" class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 px-5 py-2.5 rounded-lg font-bold transition-colors shadow-sm">Simpan Draf (RFQ)</button>
                        <button type="submit" name="action" value="po" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2.5 rounded-lg font-bold shadow-sm">Simpan Pesanan (PO)</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Kontak Cepat -->
    <div id="quickContactModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-[60] p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 class="text-lg font-bold text-slate-800">Tambah Supplier Baru</h3>
                <button onclick="closeContactModal()" class="text-slate-400 hover:text-slate-700 transition-colors"><i data-feather="x" class="w-5 h-5"></i></button>
            </div>
            <form id="quickContactForm" onsubmit="submitQuickContact(event)" class="p-5 space-y-4">
                <input type="hidden" name="type" value="Supplier">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Perusahaan / Pemasok</label>
                        <input type="text" name="name" required class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">No. Telepon / WA (Opsional)</label>
                        <input type="text" name="phone" placeholder="08..." class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Email (Opsional)</label>
                        <input type="email" name="email" placeholder="email@contoh.com" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Alamat Lengkap</label>
                        <textarea name="address" rows="3" placeholder="Alamat jalan, kota, provinsi..." class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeContactModal()" class="px-4 py-2 text-slate-600 font-bold text-sm bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">Batal</button>
                    <button type="submit" id="btnSaveContact" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold text-sm transition-colors">Simpan Kontak</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function formatRibuan(inputElement) {
            let value = inputElement.value.replace(/[^0-9]/g, '');
            if (value !== '') { inputElement.value = parseInt(value, 10).toLocaleString('id-ID'); } else { inputElement.value = ''; }
        }
        function unformatRibuan(formElement) {
            let formattedInputs = formElement.querySelectorAll('.format-ribuan');
            formattedInputs.forEach(input => { input.value = input.value.replace(/\./g, ''); });
        }

        const productsData = @json($products);
        let rowIdx = 0;

        // function addRow() {
        //     let tbody = document.getElementById('itemBody');
        //     let tr = document.createElement('tr');
        //     let selectOptions = `<option value="" disabled selected>Pilih Produk...</option>`;

        //     // PERHATIAN: Menyematkan total_stock ke dalam label Dropdown Option
        //     productsData.forEach(p => {
        //         let stokAman = p.total_stock || 0;
        //         selectOptions += `<option value="${p.sku}" data-price="${p.buy_price}">[${p.sku}] ${p.name} (Stok: ${stokAman})</option>`;
        //     });

        //     tr.innerHTML = `
        //         <td class="p-1 border">
        //             <select name="items[${rowIdx}][product_sku]" onchange="updatePrice(this, ${rowIdx})" required class="w-full p-1.5 text-sm outline-none bg-transparent font-medium">${selectOptions}</select>
        //         </td>
        //         <td class="p-1 border">
        //             <input type="text" name="items[${rowIdx}][quantity]" id="qty_${rowIdx}" value="1" required oninput="formatRibuan(this); calcSub(${rowIdx})" class="format-ribuan w-full p-1.5 text-sm text-center outline-none bg-transparent">
        //         </td>
        //         <td class="p-1 border">
        //             <input type="text" name="items[${rowIdx}][price]" id="price_${rowIdx}" required oninput="formatRibuan(this); calcSub(${rowIdx})" class="format-ribuan w-full p-1.5 text-sm text-right outline-none bg-transparent">
        //         </td>
        //         <td class="p-2 border text-right text-sm font-semibold bg-slate-50 subtotal-text" id="subtext_${rowIdx}">Rp 0</td>
        //         <td class="p-1 border text-center">
        //             <button type="button" onclick="this.closest('tr').remove(); calcGrand();" class="text-red-500 hover:text-red-700">X</button>
        //         </td>
        //     `;
        //     tbody.appendChild(tr);
        //     rowIdx++;
        // }

        function addRow() {
            let tbody = document.getElementById('itemBody');
            let tr = document.createElement('tr');
            let selectOptions = `<option value="" disabled selected>Pilih Produk...</option>`;

            productsData.forEach(p => {
                let stokAman = p.total_stock || 0;
                selectOptions += `<option value="${p.sku}" data-price="${p.buy_price}">[${p.sku}] ${p.name} (Stok: ${stokAman})</option>`;
            });

            tr.innerHTML = `
                <td class="p-1 border">
                    <select name="items[${rowIdx}][product_sku]" onchange="updatePrice(this, ${rowIdx})" required class="w-full p-1.5 text-sm outline-none bg-transparent font-medium">${selectOptions}</select>
                </td>
                <td class="p-1 border">
                    <input type="text" name="items[${rowIdx}][quantity]" id="qty_${rowIdx}" value="1" required oninput="formatRibuan(this); calcSub(${rowIdx})" class="format-ribuan w-full p-1.5 text-sm text-center outline-none bg-transparent">
                </td>
                <td class="p-1 border">
                    <input type="text" name="items[${rowIdx}][price]" id="price_${rowIdx}" required oninput="formatRibuan(this); calcSub(${rowIdx})" class="format-ribuan w-full p-1.5 text-sm text-right outline-none bg-transparent">
                </td>
                <td class="p-1 border text-center">
                    <select name="items[${rowIdx}][tax_rate]" id="tax_${rowIdx}" onchange="calcSub(${rowIdx})" class="w-full p-1.5 text-xs font-bold text-orange-600 outline-none bg-transparent">
                        <option value="0">0%</option>
                        <option value="11">11%</option>
                        <option value="12" selected>12%</option>
                    </select>
                </td>
                <td class="p-2 border text-right text-sm font-bold bg-slate-50 subtotal-text text-slate-800" id="subtext_${rowIdx}">Rp 0</td>
                <td class="p-1 border text-center">
                    <button type="button" onclick="this.closest('tr').remove(); calcGrand();" class="text-red-500 hover:text-red-700">X</button>
                </td>
            `;
            tbody.appendChild(tr);
            rowIdx++;
        }

        function updatePrice(sel, idx) {
            let price = sel.options[sel.selectedIndex].getAttribute('data-price');
            let priceInput = document.getElementById(`price_${idx}`);
            priceInput.value = price;
            formatRibuan(priceInput);
            calcSub(idx);
        }

        // function calcSub(idx) {
        //     let rawQ = document.getElementById(`qty_${idx}`).value.replace(/\./g, '') || 0;
        //     let rawP = document.getElementById(`price_${idx}`).value.replace(/\./g, '') || 0;
        //     let sub = parseInt(rawQ, 10) * parseInt(rawP, 10);
        //     document.getElementById(`subtext_${idx}`).innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(sub);
        //     calcGrand();
        // }

        // Fungsi Kalkulasi Subtotal Diperbarui (DPP Pembelian + PPN Masukan)
        function calcSub(idx) {
            let rawQ = document.getElementById(`qty_${idx}`).value.replace(/\./g, '') || 0;
            let rawP = document.getElementById(`price_${idx}`).value.replace(/\./g, '') || 0;
            let taxRate = parseInt(document.getElementById(`tax_${idx}`).value) || 0;

            let dpp = parseInt(rawQ, 10) * parseInt(rawP, 10);
            let taxAmount = dpp * (taxRate / 100);
            let sub = dpp + taxAmount;

            document.getElementById(`subtext_${idx}`).innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(sub);
            calcGrand();
        }

        // function calcGrand() {
        //     let total = 0;
        //     document.querySelectorAll('[id^="qty_"]').forEach(el => {
        //         let idx = el.id.split('_')[1];
        //         let rawQ = document.getElementById(`qty_${idx}`).value.replace(/\./g, '') || 0;
        //         let rawP = document.getElementById(`price_${idx}`).value.replace(/\./g, '') || 0;
        //         total += parseInt(rawQ, 10) * parseInt(rawP, 10);
        //     });
        //     document.getElementById('grandTotalText').innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(total);
        // }

        function calcGrand() {
            let total = 0;
            document.querySelectorAll('[id^="qty_"]').forEach(el => {
                let idx = el.id.split('_')[1];
                let rawQ = document.getElementById(`qty_${idx}`).value.replace(/\./g, '') || 0;
                let rawP = document.getElementById(`price_${idx}`).value.replace(/\./g, '') || 0;
                let taxRate = parseInt(document.getElementById(`tax_${idx}`).value) || 0;

                let dpp = parseInt(rawQ, 10) * parseInt(rawP, 10);
                let taxAmount = dpp * (taxRate / 100);
                total += (dpp + taxAmount);
            });
            document.getElementById('grandTotalText').innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(total);
        }

        document.addEventListener("DOMContentLoaded", function () { addRow(); });

        function openContactModal() { document.getElementById('quickContactModal').classList.remove('hidden'); }
        function closeContactModal() { document.getElementById('quickContactModal').classList.add('hidden'); document.getElementById('quickContactForm').reset(); }

        async function submitQuickContact(e) {
            e.preventDefault();
            const form = document.getElementById('quickContactForm');
            const btn = document.getElementById('btnSaveContact');
            const formData = new FormData(form);
            btn.disabled = true; btn.innerText = 'Menyimpan...';

            try {
                const response = await fetch("{{ route('contacts.store') }}", {
                    method: "POST",
                    headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" },
                    body: formData
                });
                const result = await response.json();
                if (response.ok && result.success) {
                    const newOption = document.createElement("option");
                    newOption.value = result.contact.id;
                    newOption.text = result.contact.name;
                    document.getElementById('contactDropdown').add(newOption);
                    document.getElementById('contactDropdown').value = result.contact.id;
                    closeContactModal();
                    alert('Supplier berhasil ditambahkan!');
                } else { alert(result.message || 'Gagal menambahkan kontak.'); }
            } catch (error) { alert('Terjadi kesalahan jaringan.'); } finally {
                btn.disabled = false; btn.innerText = 'Simpan';
            }
        }
    </script>
@endsection --}}

@extends('layouts.app')

@section('content')
    @php
        $suppliers = \App\Models\Contact::where('type', 'Supplier')->get();
        // PERHATIAN: Tarik data produk sekaligus dengan jumlah (Sum) stok aktif dari tabel relasi
        $products = \App\Models\Product::withSum('stocks as total_stock', 'quantity')->get();
    @endphp

    <div class="space-y-6 pb-10 relative">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                    <i data-feather="shopping-cart" class="text-orange-500 w-7 h-7"></i> Pesanan Pembelian (PO)
                </h1>
                <p class="text-slate-500 mt-1">Buat dokumen pemesanan barang ke Pemasok/Supplier</p>
            </div>

            <!-- CEK HAK AKSES CREATE (C) -->
            @if(\App\Models\CompanySetting::canAction('C', 'Pembelian'))
            <button onclick="openPoModal()"
                class="w-full sm:w-auto bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-colors flex items-center justify-center gap-2">
                <i data-feather="plus" class="w-4 h-4"></i> Buat PO Baru
            </button>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[900px]">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="p-4 text-sm font-semibold text-slate-600">Tanggal</th>
                            <th class="p-4 text-sm font-semibold text-slate-600">No. Referensi</th>
                            <th class="p-4 text-sm font-semibold text-slate-600">Supplier</th>
                            <th class="p-4 text-sm font-semibold text-slate-600">Jatuh Tempo</th>
                            <th class="p-4 text-sm font-semibold text-slate-600 text-right">Total Transaksi</th>
                            <th class="p-4 text-sm font-semibold text-slate-600 text-center">Status</th>
                            <th class="p-4 text-sm font-semibold text-slate-600 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $po)
                            <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                                <td class="p-4 text-sm">{{ \Carbon\Carbon::parse($po->date)->format('d/m/Y') }}</td>
                                <td class="p-4 text-sm font-bold text-slate-800">{{ $po->reference_no ?: '-' }}</td>
                                <td class="p-4 text-sm font-medium text-blue-700">{{ $po->contact->name }}</td>
                                <td class="p-4 text-sm">
                                    @if($po->due_date)
                                        <span class="px-2 py-1 rounded bg-slate-100 text-slate-700 font-medium border border-slate-200">
                                            {{ \Carbon\Carbon::parse($po->due_date)->format('d/m/Y') }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="p-4 text-sm font-bold text-slate-800 text-right">Rp {{ number_format($po->total, 0, ',', '.') }}</td>

                                <!-- Kolom Status Berwarna -->
                                <td class="p-4 text-center">
                                    @if($po->status == 'Penawaran')
                                        <span class="px-3 py-1 bg-slate-100 text-slate-600 border border-slate-200 rounded-full text-xs font-bold whitespace-nowrap"><i data-feather="file-text" class="w-3 h-3 inline mr-1"></i>Draf RFQ</span>
                                    @elseif($po->status == 'Menunggu Persetujuan')
                                        <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold whitespace-nowrap"><i data-feather="clock" class="w-3 h-3 inline mr-1"></i>Menunggu</span>
                                    @elseif($po->status == 'Pesanan')
                                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold whitespace-nowrap"><i data-feather="check" class="w-3 h-3 inline mr-1"></i>Disetujui</span>
                                    @elseif($po->status == 'Ditolak')
                                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold line-through whitespace-nowrap">Ditolak</span>
                                    @elseif($po->status == 'Diretur')
                                        <span class="px-3 py-1 bg-rose-100 text-rose-800 rounded-full text-xs font-bold whitespace-nowrap"><i data-feather="corner-up-right" class="w-3 h-3 inline mr-1"></i>Diretur (Kembali)</span>
                                    @else
                                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold whitespace-nowrap">{{ $po->status }}</span>
                                    @endif
                                </td>

                                <!-- Kolom Aksi Dinamis -->
                                <td class="p-4 text-center">
                                    <div class="flex justify-center items-center gap-2">
                                        <!-- Tombol Detail -->
                                        <button onclick="openDetailModal({{ $po->id }})" class="text-blue-600 hover:bg-blue-50 p-2 rounded-lg transition-colors shadow-sm border border-transparent hover:border-blue-200" title="Lihat Detail Barang">
                                            <i data-feather="eye" class="w-4 h-4"></i>
                                        </button>

                                        <!-- TOMBOL KHUSUS STATUS PENAWARAN (DRAF RFQ) -->
                                        @if($po->status == 'Penawaran')
                                            <a href="{{ route('purchases.printQuotation', $po->id) }}" target="_blank" class="text-slate-500 hover:text-slate-800 hover:bg-slate-100 p-2 rounded-lg transition-colors border border-slate-200 shadow-sm" title="Cetak Request for Quotation (PDF)">
                                                <i data-feather="printer" class="w-4 h-4"></i>
                                            </a>

                                            @if(\App\Models\CompanySetting::canAction('C', 'Pembelian') || \App\Models\CompanySetting::canAction('U', 'Pembelian'))
                                            <form action="{{ route('purchases.convertToPo', $po->id) }}" method="POST" onsubmit="confirmAction(event, this, 'Supplier Deal?', 'Dokumen ini akan dikonversi menjadi Pesanan Pembelian (PO) resmi. Anda yakin?', 'Ya, Konversi ke PO', '#ea580c')">
                                                @csrf
                                                <button type="submit" class="text-orange-600 hover:bg-orange-50 p-2 rounded-lg transition-colors shadow-sm border border-transparent hover:border-orange-200" title="Ubah ke PO">
                                                    <i data-feather="arrow-right-circle" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                            @endif
                                        @endif

                                        <!-- Tombol Setuju/Tolak (Khusus Status Menunggu & Memiliki Hak UPDATE) -->
                                        @if($po->status == 'Menunggu Persetujuan' && \App\Models\CompanySetting::canAction('U', 'Pembelian'))
                                            <form action="{{ route('purchases.approve', $po->id) }}" method="POST" onsubmit="confirmAction(event, this, 'Setujui PO ini?', 'Dokumen ini akan disahkan dan siap untuk proses penerimaan barang di gudang.', 'Ya, Setujui', '#10b981')">
                                                @csrf
                                                <button type="submit" class="text-emerald-600 hover:bg-emerald-50 p-2 rounded-lg transition-colors shadow-sm border border-transparent hover:border-emerald-200" title="Setujui PO">
                                                    <i data-feather="check-circle" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('purchases.reject', $po->id) }}" method="POST" onsubmit="confirmAction(event, this, 'Tolak PO ini?', 'Pesanan ini akan ditolak secara permanen dan tidak dapat diproses lebih lanjut.', 'Ya, Tolak', '#ef4444')">
                                                @csrf
                                                <button type="submit" class="text-red-500 hover:bg-red-50 p-2 rounded-lg transition-colors shadow-sm border border-transparent hover:border-red-200" title="Tolak PO">
                                                    <i data-feather="x-circle" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="p-10 text-center text-slate-500">Belum ada pesanan pembelian.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PERULANGAN MODAL DETAIL UNTUK TIAP PO (BERANIMASI) -->
        @foreach($purchases as $po)
        <div id="detailModal_{{ $po->id }}" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-[70] p-4 backdrop-blur-sm transition-all">
            <div id="detailModalContent_{{ $po->id }}" class="bg-white rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh] border-t-4 border-orange-500 transform transition-transform scale-95 origin-center">
                <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-xl font-bold text-slate-800">Rincian Dokumen: {{ $po->reference_no ?: '#'.$po->id }}</h3>
                    <button onclick="closeDetailModal({{ $po->id }})" class="text-slate-400 hover:text-slate-700 transition-colors"><i data-feather="x"></i></button>
                </div>
                <div class="p-6 overflow-y-auto custom-scrollbar">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div><p class="text-xs text-slate-500 font-semibold mb-1">Supplier / Pemasok</p><p class="font-bold text-slate-800 text-lg">{{ $po->contact->name }}</p></div>
                        <div class="text-right"><p class="text-xs text-slate-500 font-semibold mb-1">Tanggal Terbit</p><p class="font-bold text-slate-800 text-lg">{{ \Carbon\Carbon::parse($po->date)->format('d F Y') }}</p></div>
                    </div>
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
                                @foreach($po->items as $item)
                                <tr class="hover:bg-slate-50 border-b border-slate-100">
                                    <td class="p-3 text-sm font-medium">{{ $item->product->name ?? $item->product_sku }}</td>
                                    <td class="p-3 text-sm text-center font-bold text-slate-700">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                                    <td class="p-3 text-sm text-right text-slate-600">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="p-3 text-sm text-right font-bold text-slate-800">Rp {{ number_format($item->sub_total, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-slate-50">
                                    <th colspan="3" class="p-3 text-right font-bold text-slate-600 uppercase">Grand Total Keseluruhan</th>
                                    <th class="p-3 text-right font-black text-blue-700 text-lg">Rp {{ number_format($po->total, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        <!-- Modal Form PO Utama (Beranimasi) -->
        <div id="poModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 backdrop-blur-sm transition-all">
            <div id="poModalContent" class="bg-white rounded-xl shadow-2xl w-full max-w-5xl overflow-hidden flex flex-col max-h-[95vh] border-t-4 border-orange-500 transform transition-transform scale-95 origin-center">
                <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-xl font-bold text-slate-800">Buat Pesanan Pembelian Baru</h3>
                    <button onclick="closePoModal()" class="text-slate-400 hover:text-red-500 bg-white rounded-full p-1 shadow-sm transition-colors"><i data-feather="x"></i></button>
                </div>

                <form id="poForm" action="{{ route('purchases.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden" onsubmit="unformatRibuan(this)">
                    @csrf
                    <div class="p-6 overflow-y-auto custom-scrollbar flex-1 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 bg-slate-50 p-4 rounded-lg border border-slate-200">
                            <div class="lg:col-span-2">
                                <div class="flex justify-between items-center mb-1">
                                    <label class="block text-sm font-semibold text-slate-700">Pilih Supplier</label>
                                    <button type="button" onclick="openContactModal()" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1">
                                        <i data-feather="plus" class="w-3 h-3"></i> Tambah Baru
                                    </button>
                                </div>
                                <select name="contact_id" id="contactDropdown" required class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="" disabled selected>-- Pilih Pemasok --</option>
                                    @foreach($suppliers as $s) <option value="{{ $s->id }}">{{ $s->name }}</option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal</label>
                                <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Jatuh Tempo</label>
                                <input type="date" name="due_date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">No. Referensi / PO</label>
                                <input type="text" value="{{ $nextDocNumber ?? '' }}" disabled class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none bg-slate-100 text-slate-500 font-bold cursor-not-allowed text-center">
                                <p class="text-[10px] text-slate-500 mt-1 font-medium">Diisi otomatis oleh sistem.</p>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-bold text-slate-800">Detail Pesanan Barang</h4>
                                <button type="button" onclick="addRow()" class="bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-sm font-semibold flex items-center gap-1 transition-colors">
                                    <i data-feather="plus" class="w-3 h-3"></i> Tambah Baris
                                </button>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse border border-slate-300 min-w-[700px]" id="itemTable">
                                    <thead class="bg-slate-100">
                                        <tr>
                                            <th class="p-2 border text-sm font-semibold">Produk (Info Stok)</th>
                                            <th class="p-2 border text-sm font-semibold w-24">Qty</th>
                                            <th class="p-2 border text-sm font-semibold w-32">Harga Satuan</th>
                                            <th class="p-2 border text-sm font-semibold w-24 text-center">PPN</th>
                                            <th class="p-2 border text-sm font-semibold w-32 text-right">Subtotal</th>
                                            <th class="p-2 border w-12 text-center"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemBody"></tbody>
                                </table>
                            </div>

                            <div class="mt-4 text-right text-lg font-bold text-blue-700 p-4 bg-blue-50 rounded-lg border border-blue-100">
                                GRAND TOTAL: <span id="grandTotalText">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 border-t border-slate-200 bg-slate-50 flex justify-end gap-3">
                        <button type="button" onclick="closePoModal()" class="px-5 py-2.5 text-slate-600 font-bold hover:bg-slate-200 rounded-lg transition-colors">Batal</button>

                        <!-- TOMBOL OPSI: QUOTATION ATAU PO LANGSUNG -->
                        <button type="submit" name="action" value="quotation" class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 px-5 py-2.5 rounded-lg font-bold transition-colors shadow-sm">Simpan Draf (RFQ)</button>
                        <button type="submit" name="action" value="po" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2.5 rounded-lg font-bold shadow-sm flex items-center gap-2 transition-colors">
                            <i data-feather="check" class="w-4 h-4"></i> Simpan Pesanan (PO)
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Kontak Cepat (AJAX) -->
        <div id="quickContactModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-[60] p-4 backdrop-blur-sm transition-all">
            <div id="quickContactContent" class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col border-t-4 border-orange-500 transform transition-transform scale-95 origin-center">
                <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-lg font-bold text-slate-800">Tambah Supplier Baru</h3>
                    <button onclick="closeContactModal()" class="text-slate-400 hover:text-red-500 bg-white rounded-full p-1 shadow-sm transition-colors"><i data-feather="x" class="w-5 h-5"></i></button>
                </div>
                <form id="quickContactForm" onsubmit="submitQuickContact(event)" class="p-5 space-y-4">
                    <input type="hidden" name="type" value="Supplier">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Perusahaan / Pemasok</label>
                            <input type="text" name="name" required class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500 font-bold bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">No. Telepon / WA (Opsional)</label>
                            <input type="text" name="phone" placeholder="08..." class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Email (Opsional)</label>
                            <input type="email" name="email" placeholder="email@contoh.com" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Alamat Lengkap</label>
                            <textarea name="address" rows="3" placeholder="Alamat jalan, kota, provinsi..." class="w-full border border-slate-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" onclick="closeContactModal()" class="px-4 py-2 text-slate-600 font-bold text-sm bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">Batal</button>
                        <button type="submit" id="btnSaveContact" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold text-sm shadow-md transition-colors flex items-center gap-2">
                            <i data-feather="save" class="w-4 h-4"></i> Simpan Kontak
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
                    title: 'Berhasil!',
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
                    title: 'Aksi Gagal!',
                    text: '{{ session('error') }}',
                    timer: 5000,
                    timerProgressBar: true,
                    showConfirmButton: true,
                    confirmButtonColor: '#ef4444'
                });
            @endif
        });

        // --- SWEETALERT2: KONFIRMASI AKSI DINAMIS (Ubah PO, Setujui, Tolak) ---
        function confirmAction(event, form, title, text, confirmText, confirmColor) {
            event.preventDefault();

            Swal.fire({
                title: title,
                text: text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: confirmColor,
                cancelButtonColor: '#64748b',
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mohon tunggu sebentar.',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    form.submit();
                }
            });
        }

        // --- SWEETALERT2: ANIMASI LOADING SAAT SUBMIT PO ---
        document.getElementById('poForm').addEventListener('submit', function() {
            Swal.fire({
                title: 'Menyimpan Dokumen...',
                text: 'Sistem sedang memproses pesanan belanja Anda.',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); }
            });
        });

        // --- HELPER FORMAT ANGKA ---
        function formatRibuan(inputElement) {
            let value = inputElement.value.replace(/[^0-9]/g, '');
            if (value !== '') { inputElement.value = parseInt(value, 10).toLocaleString('id-ID'); } else { inputElement.value = ''; }
        }
        function unformatRibuan(formElement) {
            let formattedInputs = formElement.querySelectorAll('.format-ribuan');
            formattedInputs.forEach(input => { input.value = input.value.replace(/\./g, ''); });
        }

        // --- LOGIKA TABEL DINAMIS (ITEM PEMBELIAN) ---
        const productsData = @json($products);
        let rowIdx = 0;

        function addRow() {
            let tbody = document.getElementById('itemBody');
            let tr = document.createElement('tr');
            let selectOptions = `<option value="" disabled selected>Pilih Produk...</option>`;

            productsData.forEach(p => {
                let stokAman = p.total_stock || 0;
                selectOptions += `<option value="${p.sku}" data-price="${p.buy_price}">[${p.sku}] ${p.name} (Stok: ${stokAman})</option>`;
            });

            tr.innerHTML = `
                <td class="p-1 border">
                    <select name="items[${rowIdx}][product_sku]" onchange="updatePrice(this, ${rowIdx})" required class="w-full p-1.5 text-sm outline-none bg-transparent font-medium">${selectOptions}</select>
                </td>
                <td class="p-1 border">
                    <input type="text" name="items[${rowIdx}][quantity]" id="qty_${rowIdx}" value="1" required oninput="formatRibuan(this); calcSub(${rowIdx})" class="format-ribuan w-full p-1.5 text-sm text-center outline-none bg-transparent font-bold">
                </td>
                <td class="p-1 border">
                    <input type="text" name="items[${rowIdx}][price]" id="price_${rowIdx}" required oninput="formatRibuan(this); calcSub(${rowIdx})" class="format-ribuan w-full p-1.5 text-sm text-right outline-none bg-transparent font-medium">
                </td>
                <td class="p-1 border text-center">
                    <select name="items[${rowIdx}][tax_rate]" id="tax_${rowIdx}" onchange="calcSub(${rowIdx})" class="w-full p-1.5 text-xs font-bold text-orange-600 outline-none bg-transparent text-center">
                        <option value="0">0%</option>
                        <option value="11">11%</option>
                        <option value="12" selected>12%</option>
                    </select>
                </td>
                <td class="p-2 border text-right text-sm font-bold bg-slate-50 subtotal-text text-slate-800" id="subtext_${rowIdx}">Rp 0</td>
                <td class="p-1 border text-center bg-red-50">
                    <button type="button" onclick="this.closest('tr').remove(); calcGrand();" class="text-red-500 hover:text-red-700" title="Hapus Baris"><i data-feather="trash-2" class="w-4 h-4 mx-auto"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
            if (typeof feather !== 'undefined') { feather.replace(); }
            rowIdx++;
        }

        function updatePrice(sel, idx) {
            let price = sel.options[sel.selectedIndex].getAttribute('data-price');
            let priceInput = document.getElementById(`price_${idx}`);
            priceInput.value = price;
            formatRibuan(priceInput);
            calcSub(idx);
        }

        function calcSub(idx) {
            let rawQ = document.getElementById(`qty_${idx}`).value.replace(/\./g, '') || 0;
            let rawP = document.getElementById(`price_${idx}`).value.replace(/\./g, '') || 0;
            let taxRate = parseInt(document.getElementById(`tax_${idx}`).value) || 0;

            let dpp = parseInt(rawQ, 10) * parseInt(rawP, 10);
            let taxAmount = dpp * (taxRate / 100);
            let sub = dpp + taxAmount;

            document.getElementById(`subtext_${idx}`).innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(sub);
            calcGrand();
        }

        function calcGrand() {
            let total = 0;
            document.querySelectorAll('[id^="qty_"]').forEach(el => {
                let idx = el.id.split('_')[1];
                let rawQ = document.getElementById(`qty_${idx}`).value.replace(/\./g, '') || 0;
                let rawP = document.getElementById(`price_${idx}`).value.replace(/\./g, '') || 0;
                let taxRate = parseInt(document.getElementById(`tax_${idx}`).value) || 0;

                let dpp = parseInt(rawQ, 10) * parseInt(rawP, 10);
                let taxAmount = dpp * (taxRate / 100);
                total += (dpp + taxAmount);
            });
            document.getElementById('grandTotalText').innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(total);
        }

        document.addEventListener("DOMContentLoaded", function () { addRow(); });

        // --- LOGIKA MODAL (ANIMASI CSS SCALE) ---
        function openPoModal() {
            const modal = document.getElementById('poModal');
            const content = document.getElementById('poModalContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }, 10);
        }

        function closePoModal() {
            const modal = document.getElementById('poModal');
            const content = document.getElementById('poModalContent');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            setTimeout(() => { modal.classList.add('hidden'); }, 150);
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

        // --- LOGIKA MODAL KONTAK CEPAT (AJAX) ---
        function openContactModal() {
            const modal = document.getElementById('quickContactModal');
            const content = document.getElementById('quickContactContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }, 10);
        }

        function closeContactModal() {
            const modal = document.getElementById('quickContactModal');
            const content = document.getElementById('quickContactContent');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                document.getElementById('quickContactForm').reset();
            }, 150);
        }

        async function submitQuickContact(e) {
            e.preventDefault();
            const form = document.getElementById('quickContactForm');
            const btn = document.getElementById('btnSaveContact');
            const formData = new FormData(form);

            btn.disabled = true;
            btn.innerHTML = '<i class="w-4 h-4 animate-spin border-2 border-white border-t-transparent rounded-full inline-block mr-2"></i> Menyimpan...';

            try {
                const response = await fetch("{{ route('contacts.store') }}", {
                    method: "POST",
                    headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" },
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

                    Swal.fire({
                        icon: 'success',
                        title: 'Supplier Tersimpan!',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan!',
                        text: result.message || 'Nama/Email mungkin sudah terdaftar.',
                        confirmButtonColor: '#4f46e5'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Jaringan',
                    text: 'Tidak dapat menghubungi server. Periksa koneksi internet Anda.',
                    confirmButtonColor: '#4f46e5'
                });
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i data-feather="save" class="w-4 h-4"></i> Simpan Kontak';
                feather.replace();
            }
        }
    </script>
@endsection
