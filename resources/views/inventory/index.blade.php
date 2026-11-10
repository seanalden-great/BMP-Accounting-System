{{-- <!-- resources/views/inventory/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">
    <div class="flex justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="archive" class="text-teal-600 w-7 h-7"></i> Stok & Penyesuaian
            </h1>
            <p class="text-slate-500 mt-1">Sesuaikan jumlah stok fisik per Gudang dan otomatis catat selisih persediaan</p>
        </div>
        <button onclick="document.getElementById('invModal').classList.remove('hidden')" class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
            <i data-feather="plus" class="w-4 h-4"></i> Stock Opname
        </button>
    </div>

    <!-- Tabel Riwayat Penyesuaian Stok -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1000px]">
                <thead class="bg-white border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600 w-28">Tanggal</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Gudang</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Produk</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Tipe Mutasi</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center w-28">Selisih Qty</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right w-36">Nilai Barang</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Alasan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($adjustments as $adj)
                    <tr class="border-b border-slate-50 hover:bg-slate-50">
                        <td class="p-4 text-sm">{{ \Carbon\Carbon::parse($adj->date)->format('d/m/Y') }}</td>
                        <!-- Menampilkan nama gudang dari relasi -->
                        <td class="p-4 text-sm font-bold text-indigo-700">{{ $adj->warehouse->name ?? '-' }}</td>
                        <td class="p-4 text-sm font-bold text-slate-700">[{{ $adj->product_sku }}] {{ $adj->product->name }}</td>
                        <td class="p-4 text-center">
                            @if ($adj->type === 'Penambahan')
                                <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">
                                    <i data-feather="arrow-up-right" class="w-3 h-3"></i> Tambah
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">
                                    <i data-feather="arrow-down-right" class="w-3 h-3"></i> Kurang
                                </span>
                            @endif
                        </td>
                        <td class="p-4 text-sm font-bold text-center">{{ $adj->quantity }} {{ $adj->product->unit }}</td>
                        <td class="p-4 text-sm font-medium text-slate-600 text-right">Rp {{ number_format($adj->total_value, 0, ',', '.') }}</td>
                        <td class="p-4 text-sm text-slate-500 italic">{{ $adj->reason }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="p-8 text-center text-slate-500">Belum ada riwayat penyesuaian stok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Penyesuaian Stok (Stock Opname) -->
    <div id="invModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 class="text-xl font-bold text-slate-800">Form Penyesuaian Stok Gudang</h3>
                <button onclick="document.getElementById('invModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">
                    <i data-feather="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('inventory.adjust') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                    <!-- Dropdown Pilihan Gudang -->
                    <div>
                        <label class="block text-sm font-bold text-indigo-900 mb-1">Lokasi Gudang</label>
                        <select name="warehouse_id" required class="w-full border border-indigo-200 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="" disabled selected>-- Pilih Gudang --</option>
                            @foreach ($warehouses as $wh)
                                <option value="{{ $wh->id }}">[{{ $wh->code }}] {{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-teal-900 mb-1">Pilih Produk</label>
                    <select name="product_sku" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-teal-500">
                        <option value="" disabled selected>-- Pilih Produk --</option>
                        @foreach ($products as $p)
                            <!-- Menghilangkan info stok lama, karena sekarang stok dihitung per gudang -->
                            <option value="{{ $p->sku }}">[{{ $p->sku }}] {{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4 bg-teal-50 p-4 rounded-lg border border-teal-100">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Tipe Penyesuaian</label>
                        <select name="type" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-teal-500 font-bold">
                            <option value="Penambahan">➕ Penambahan Stok Masuk</option>
                            <option value="Pengurangan">➖ Pengurangan Stok Keluar</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Kuantitas Selisih</label>
                        <input type="number" name="quantity" required min="1" placeholder="Berapa qty?" class="w-full border border-slate-300 rounded-lg p-2.5 text-center font-bold outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Alasan Penyesuaian</label>
                    <textarea name="reason" required rows="2" placeholder="Cth: Barang rusak, salah hitung di gudang, daluwarsa..." class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-teal-500 resize-none"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="document.getElementById('invModal').classList.add('hidden')" class="px-5 py-2 text-slate-600 hover:bg-slate-100 rounded-lg font-bold">Batal</button>
                    <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2.5 rounded-lg font-bold flex items-center gap-2">
                        Sesuaikan Stok & Jurnal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection --}}

<!-- resources/views/inventory/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="space-y-6 pb-10">
        <div class="flex justify-between items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                    <i data-feather="archive" class="text-teal-600 w-7 h-7"></i> Stok & Penyesuaian
                </h1>
                <p class="text-slate-500 mt-1">Sesuaikan jumlah stok fisik per Gudang dan otomatis catat selisih persediaan
                </p>
            </div>
            {{-- <button onclick="document.getElementById('invModal').classList.remove('hidden')" class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
            <i data-feather="plus" class="w-4 h-4"></i> Stock Opname
        </button> --}}
            <!-- Cek Hak Akses CREATE (C) -->
            @if (\App\Models\CompanySetting::canAction('C', 'Inventori'))
                <button onclick="document.getElementById('invModal').classList.remove('hidden')"
                    class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
                    <i data-feather="plus" class="w-4 h-4"></i> Stock Opname
                </button>
            @endif
        </div>

        <!-- Tabel Riwayat Penyesuaian Stok -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[1000px]">
                    <thead class="bg-white border-b border-slate-200">
                        <tr>
                            <th class="p-4 text-sm font-semibold text-slate-600 w-28">Tanggal</th>
                            <th class="p-4 text-sm font-semibold text-slate-600">Gudang</th>
                            <th class="p-4 text-sm font-semibold text-slate-600">Produk</th>
                            <th class="p-4 text-sm font-semibold text-slate-600 text-center">Tipe Mutasi</th>
                            <th class="p-4 text-sm font-semibold text-slate-600 text-center w-28">Selisih Qty</th>
                            <th class="p-4 text-sm font-semibold text-slate-600 text-right w-36">Nilai Barang</th>
                            <th class="p-4 text-sm font-semibold text-slate-600">Alasan</th>
                            <th class="p-4 text-sm font-semibold text-slate-600 text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- @forelse($adjustments as $adj)
                    <tr class="border-b border-slate-50 hover:bg-slate-50">
                        <td class="p-4 text-sm">{{ \Carbon\Carbon::parse($adj->date)->format('d/m/Y') }}</td>
                        <!-- Menampilkan nama gudang dari relasi -->
                        <td class="p-4 text-sm font-bold text-indigo-700">{{ $adj->warehouse->name ?? '-' }}</td>
                        <td class="p-4 text-sm font-bold text-slate-700">[{{ $adj->product_sku }}] {{ $adj->product->name }}</td>
                        <td class="p-4 text-center">
                            @if ($adj->type === 'Penambahan')
                                <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">
                                    <i data-feather="arrow-up-right" class="w-3 h-3"></i> Tambah
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">
                                    <i data-feather="arrow-down-right" class="w-3 h-3"></i> Kurang
                                </span>
                            @endif
                        </td>
                        <td class="p-4 text-sm font-bold text-center">{{ number_format($adj->quantity, 0, ',', '.') }} {{ $adj->product->unit }}</td>
                        <td class="p-4 text-sm font-medium text-slate-600 text-right">Rp {{ number_format($adj->total_value, 0, ',', '.') }}</td>
                        <td class="p-4 text-sm text-slate-500 italic">{{ $adj->reason }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="p-8 text-center text-slate-500">Belum ada riwayat penyesuaian stok.</td></tr>
                    @endforelse --}}

                        @forelse($adjustments as $adj)
                            <tr class="border-b border-slate-50 hover:bg-slate-50">
                                <!-- Menampilkan Tanggal beserta JAM (H:i) dari waktu aktual sistem -->
                                <td class="p-4 text-sm font-mono text-slate-500">
                                    {{ \Carbon\Carbon::parse($adj->created_at)->format('d/m/Y H:i') }}</td>
                                <td class="p-4 text-sm font-bold text-indigo-700">{{ $adj->warehouse->name ?? '-' }}</td>
                                <td class="p-4 text-sm font-bold text-slate-700">[{{ $adj->product_sku }}]
                                    {{ $adj->product->name }}</td>
                                <td class="p-4 text-center">
                                    @if ($adj->type === 'Penambahan')
                                        <span
                                            class="inline-flex items-center gap-1 bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold"><i
                                                data-feather="arrow-up-right" class="w-3 h-3"></i> Tambah</span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold"><i
                                                data-feather="arrow-down-right" class="w-3 h-3"></i> Kurang</span>
                                    @endif
                                </td>
                                <td class="p-4 text-sm font-bold text-center">
                                    {{ number_format($adj->quantity, 0, ',', '.') }} {{ $adj->product->unit }}</td>
                                <td class="p-4 text-sm font-medium text-slate-600 text-right">Rp
                                    {{ number_format($adj->total_value, 0, ',', '.') }}</td>
                                <td class="p-4 text-sm text-slate-500 italic">{{ $adj->reason }}</td>

                                <!-- KOLOM AKSI (UPDATE & DELETE) -->
                                <td class="p-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Cek Hak Akses UPDATE (U) -->
                                        @if (\App\Models\CompanySetting::canAction('U', 'Inventori'))
                                            <button onclick="openEditInvModal({{ json_encode($adj) }})"
                                                class="text-blue-500 hover:bg-blue-100 p-1.5 rounded transition-colors"
                                                title="Ubah Keterangan">
                                                <i data-feather="edit" class="w-4 h-4"></i>
                                            </button>
                                        @endif

                                        <!-- Cek Hak Akses DELETE (D) -->
                                        @if (\App\Models\CompanySetting::canAction('D', 'Inventori'))
                                            <form action="{{ route('inventory.destroy', $adj->id) }}" method="POST"
                                                class="inline"
                                                onsubmit="return confirm('PENTING: Yakin ingin membatalkan Stock Opname ini? Stok akan dikembalikan (Rollback) ke posisi semula.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-500 hover:bg-red-100 p-1.5 rounded transition-colors"
                                                    title="Batalkan & Hapus">
                                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-8 text-center text-slate-500">Belum ada riwayat penyesuaian
                                    stok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Form Penyesuaian Stok (Stock Opname) -->
        <div id="invModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col">
                <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-xl font-bold text-slate-800">Form Penyesuaian Stok Gudang</h3>
                    <button onclick="document.getElementById('invModal').classList.add('hidden')"
                        class="text-slate-400 hover:text-slate-700">
                        <i data-feather="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- PERHATIAN: Tambahkan event onsubmit="unformatRibuan(this)" pada form -->
                <form action="{{ route('inventory.adjust') }}" method="POST" class="p-6 space-y-4"
                    onsubmit="unformatRibuan(this)">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal</label>
                            <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                                class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                        {{-- <div>
                        <label class="block text-sm font-bold text-indigo-900 mb-1">Lokasi Gudang</label>
                        <select name="warehouse_id" required class="w-full border border-indigo-200 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="" disabled selected>-- Pilih Gudang --</option>
                            @foreach ($warehouses as $wh)
                                <option value="{{ $wh->id }}">[{{ $wh->code }}] {{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div> --}}

                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-sm font-bold text-indigo-900">Lokasi Gudang</label>
                                <button type="button" onclick="openQuickWarehouseModal()"
                                    class="text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline flex items-center gap-1">
                                    <i data-feather="plus" class="w-3 h-3"></i> Tambah Baru
                                </button>
                            </div>
                            <!-- PERHATIKAN penambahan id="warehouseDropdown" di sini -->
                            <select name="warehouse_id" id="warehouseDropdown" required
                                class="w-full border border-indigo-200 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="" disabled selected>-- Pilih Gudang --</option>
                                @foreach ($warehouses as $wh)
                                    <option value="{{ $wh->id }}">[{{ $wh->code }}] {{ $wh->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-teal-900 mb-1">Pilih Produk</label>
                        <select name="product_sku" required
                            class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="" disabled selected>-- Pilih Produk --</option>
                            @foreach ($products as $p)
                                <option value="{{ $p->sku }}">[{{ $p->sku }}] {{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4 bg-teal-50 p-4 rounded-lg border border-teal-100">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Tipe Penyesuaian</label>
                            <select name="type" required
                                class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-teal-500 font-bold">
                                <option value="Penambahan">➕ Penambahan Stok Masuk</option>
                                <option value="Pengurangan">➖ Pengurangan Stok Keluar</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Kuantitas Selisih</label>
                            <!-- PERHATIAN: Input diubah jadi type="text" dengan class="format-ribuan" dan event oninput -->
                            <input type="text" name="quantity" required placeholder="Berapa qty?"
                                oninput="formatRibuan(this)"
                                class="format-ribuan w-full border border-slate-300 rounded-lg p-2.5 text-center font-bold outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Alasan Penyesuaian</label>
                        <textarea name="reason" required rows="2"
                            placeholder="Cth: Barang rusak, salah hitung di gudang, daluwarsa..."
                            class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-teal-500 resize-none"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" onclick="document.getElementById('invModal').classList.add('hidden')"
                            class="px-5 py-2 text-slate-600 hover:bg-slate-100 rounded-lg font-bold">Batal</button>
                        <button type="submit"
                            class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2.5 rounded-lg font-bold flex items-center gap-2">
                            Sesuaikan Stok & Jurnal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL POP-UP TAMBAH GUDANG CEPAT -->
        <div id="quickWarehouseModal"
            class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-[60] p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col">
                <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-lg font-bold text-slate-800">Tambah Gudang Baru</h3>
                    <button type="button" onclick="closeQuickWarehouseModal()"
                        class="text-slate-400 hover:text-slate-700 transition-colors"><i data-feather="x"
                            class="w-5 h-5"></i></button>
                </div>

                <form id="quickWarehouseForm" onsubmit="submitQuickWarehouse(event)" class="p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Kode Gudang</label>
                        <input type="text" name="code" required placeholder="Misal: WH-002"
                            class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Gudang / Cabang</label>
                        <input type="text" name="name" required placeholder="Misal: Gudang Transit Jakarta"
                            class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Alamat Lokasi (Opsional)</label>
                        <textarea name="location" rows="3" placeholder="Alamat lengkap gudang..."
                            class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-indigo-500 text-sm resize-none"></textarea>
                    </div>

                    <div class="mt-8 flex justify-end gap-3 pt-2 border-t border-slate-100">
                        <button type="button" onclick="closeQuickWarehouseModal()"
                            class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg text-sm font-bold transition-colors">Batal</button>
                        <button type="submit" id="btnSaveWarehouse"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-bold transition-colors">Simpan
                            Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- HELPER JAVASCRIPT GLOBAL UNTUK INPUT ANGKA -->
    <!-- ========================================== -->
    <script>
        /**
         * Helper 1: Format Ribuan (Saat user mengetik)
         * Tambahkan `oninput="formatRibuan(this)"` pada tag <input type="text">
         */
        function formatRibuan(inputElement) {
            // Hapus semua karakter selain angka (0-9)
            let value = inputElement.value.replace(/[^0-9]/g, '');

            // Format kembali angka dengan titik (standard locale Indonesia)
            if (value !== '') {
                inputElement.value = parseInt(value, 10).toLocaleString('id-ID');
            } else {
                inputElement.value = '';
            }
        }

        /**
         * Helper 2: Hapus Titik Sebelum Submit Form
         * Tambahkan `onsubmit="unformatRibuan(this)"` pada tag <form>
         * Tambahkan class `format-ribuan` pada input terkait agar terdeteksi
         */
        function unformatRibuan(formElement) {
            let formattedInputs = formElement.querySelectorAll('.format-ribuan');
            formattedInputs.forEach(input => {
                // Hilangkan semua titik sebelum data dikirim ke Controller/Database
                input.value = input.value.replace(/\./g, '');
            });
        }

        // FUNGSI UNTUK MODAL GUDANG CEPAT (AJAX)
        function openQuickWarehouseModal() {
            document.getElementById('quickWarehouseModal').classList.remove('hidden');
        }

        function closeQuickWarehouseModal() {
            document.getElementById('quickWarehouseModal').classList.add('hidden');
            document.getElementById('quickWarehouseForm').reset();
        }

        async function submitQuickWarehouse(e) {
            e.preventDefault();
            const form = document.getElementById('quickWarehouseForm');
            const btn = document.getElementById('btnSaveWarehouse');
            const formData = new FormData(form);

            btn.disabled = true;
            btn.innerText = 'Menyimpan...';

            try {
                const response = await fetch("{{ route('warehouses.store') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    },
                    body: formData
                });
                const result = await response.json();

                if (response.ok && result.success) {
                    // Buat opsi dropdown baru secara dinamis
                    const newOption = document.createElement("option");
                    newOption.value = result.warehouse.id;
                    newOption.text = `[${result.warehouse.code}] ${result.warehouse.name}`;

                    const dropdown = document.getElementById('warehouseDropdown');
                    dropdown.add(newOption);
                    dropdown.value = result.warehouse.id; // Otomatis pilih gudang yang baru dibuat

                    closeQuickWarehouseModal();
                    alert('Gudang berhasil ditambahkan dan dipilih!');
                } else {
                    alert(result.message || 'Gagal menambahkan gudang.');
                }
            } catch (error) {
                alert('Terjadi kesalahan jaringan.');
            } finally {
                btn.disabled = false;
                btn.innerText = 'Simpan Data';
            }
        }
    </script>
    <!-- Modal Form Edit Keterangan Stock Opname -->
    <div id="editInvModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 class="text-xl font-bold text-slate-800">Ubah Keterangan Opname</h3>
                <button onclick="document.getElementById('editInvModal').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-700"><i data-feather="x" class="w-5 h-5"></i></button>
            </div>
            <form method="POST" id="editInvForm" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Alasan / Keterangan Baru</label>
                    <textarea name="reason" id="editReason" required rows="3"
                        class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-teal-500 resize-none"></textarea>
                    <div class="mt-2 p-3 bg-teal-50 border border-teal-100 rounded text-xs text-teal-800 font-medium">
                        Sistem hanya mengizinkan pengubahan keterangan teks untuk menjaga integritas jurnal akuntansi. Jika
                        terjadi kesalahan pada angka Qty (jumlah), Anda wajib menekan tombol <b>Hapus</b> untuk membatalkan
                        jurnal, dan membuat Stock Opname baru.
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="document.getElementById('editInvModal').classList.add('hidden')"
                        class="px-5 py-2 text-slate-600 hover:bg-slate-100 rounded-lg font-bold transition-colors">Batal</button>
                    <button type="submit"
                        class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded-lg font-bold transition-colors shadow-sm">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tambahkan fungsi JavaScript ini di dalam blok <script>
        Anda yang sudah ada -->
    <script >
        function openEditInvModal(adj) {
            document.getElementById('editInvForm').action = '/inventory/' + adj.id;
            document.getElementById('editReason').value = adj.reason;
            document.getElementById('editInvModal').classList.remove('hidden');
        }
    </script>
@endsection
