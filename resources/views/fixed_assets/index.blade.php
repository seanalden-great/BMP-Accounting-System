{{-- <!-- resources/views/fixed_assets/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">
    <div class="flex justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="briefcase" class="text-slate-700 w-7 h-7"></i> Aset Tetap (Fixed Assets)
            </h1>
            <p class="text-slate-500 mt-1">Kelola pembelian inventaris perusahaan dan jurnal penyusutan otomatis</p>
        </div>
        <button onclick="document.getElementById('assetModal').classList.remove('hidden')" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
            <i data-feather="plus" class="w-4 h-4"></i> Beli Aset Baru
        </button>
    </div>

    <!-- Tabel Daftar Aset -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead class="bg-white border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600">Nama Aset</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Tgl Beli</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Harga Beli</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Akumulasi Susut</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Nilai Buku</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $ast)
                    @php $bookValue = $ast->purchase_price - $ast->accum_depreciation; @endphp
                    <tr class="border-b border-slate-50 hover:bg-slate-50">
                        <td class="p-4 text-sm font-bold text-slate-800">
                            {{ $ast->name }} <br/>
                            <span class="text-xs font-normal text-slate-400">Umur: {{ $ast->useful_life }} Bln</span>
                        </td>
                        <td class="p-4 text-sm text-slate-600">{{ \Carbon\Carbon::parse($ast->purchase_date)->format('d/m/Y') }}</td>
                        <td class="p-4 text-sm text-right text-slate-800 font-medium">Rp {{ number_format($ast->purchase_price, 0, ',', '.') }}</td>
                        <td class="p-4 text-sm text-right text-red-600 font-medium">Rp {{ number_format($ast->accum_depreciation, 0, ',', '.') }}</td>
                        <td class="p-4 text-sm text-right text-emerald-700 font-bold">Rp {{ number_format($bookValue, 0, ',', '.') }}</td>
                        <td class="p-4 text-center">
                            @if($ast->status === 'Aktif')
                                <form action="{{ route('fixed-assets.depreciate', $ast->id) }}" method="POST" onsubmit="return confirm('Proses penyusutan 1 bulan untuk aset ini? Jurnal akan otomatis dibuat.');">
                                    @csrf
                                    <button type="submit" class="bg-orange-100 hover:bg-orange-200 text-orange-700 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center justify-center gap-1 w-full transition-colors">
                                        <i data-feather="trending-down" class="w-3 h-3"></i> Susutkan
                                    </button>
                                </form>
                            @else
                                <span class="bg-slate-100 text-slate-500 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center justify-center gap-1 w-full">
                                    <i data-feather="check-circle" class="w-3 h-3"></i> Lunas
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="p-8 text-center text-slate-500">Belum ada aset tetap yang didaftarkan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Beli Aset -->
    <div id="assetModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 class="text-xl font-bold text-slate-800">Daftarkan Aset Tetap Baru</h3>
                <button onclick="document.getElementById('assetModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">
                    <i data-feather="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('fixed-assets.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Aset / Inventaris</label>
                    <input type="text" name="name" required placeholder="Cth: MacBook Pro M3, Mobil Operasional..." class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-slate-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal Pembelian</label>
                        <input type="date" name="purchase_date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Umur Ekonomis (Bulan)</label>
                        <div class="relative">
                            <input type="number" name="useful_life" required min="1" placeholder="48" class="w-full pr-14 pl-4 py-2.5 border border-slate-300 rounded-lg outline-none focus:ring-2 focus:ring-slate-500">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 font-medium text-slate-400">Bln</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Harga Beli Aset</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-slate-500">Rp</span>
                        <input type="number" name="purchase_price" required min="1" class="w-full pl-10 pr-4 py-3 border-2 border-slate-300 rounded-lg font-bold text-lg outline-none focus:border-slate-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-lg border border-slate-200">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Akun Aset (Debit)</label>
                        <select name="asset_account" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-slate-500">
                            <option value="" disabled selected>-- Pilih Akun 121 --</option>
                            @foreach($assetAccounts as $a) <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option> @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Bayar Dari (Kredit)</label>
                        <select name="payment_account" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-slate-500">
                            <option value="" disabled selected>-- Pilih Kas/Bank --</option>
                            @foreach($banks as $a) <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option> @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="document.getElementById('assetModal').classList.add('hidden')" class="px-5 py-2 text-slate-600 hover:bg-slate-100 rounded-lg font-bold">Batal</button>
                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-6 py-2.5 rounded-lg font-bold flex items-center gap-2">
                        Simpan & Buat Jurnal Pembelian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection --}}

{{-- <!-- resources/views/fixed_assets/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="briefcase" class="text-slate-700 w-7 h-7"></i> Aset Tetap (Fixed Assets)
            </h1>
            <p class="text-slate-500 mt-1">Kelola pembelian inventaris perusahaan dan jurnal penyusutan otomatis</p>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <!-- Dropdown Ekspor -->
            <div class="relative group">
                <button class="bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 text-emerald-700 px-4 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-colors flex items-center gap-2">
                    <i data-feather="printer" class="w-4 h-4"></i> Ekspor Data
                </button>
                <div class="absolute right-0 mt-1 w-56 bg-white border border-slate-200 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 overflow-hidden">
                    <div class="px-3 py-2 bg-slate-50 border-b font-bold text-xs text-slate-400 uppercase">Lap. Internal (Asli)</div>
                    <a href="{{ route('fixed-assets.export', ['type' => 'internal', 'format' => 'csv']) }}" class="block px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700">Unduh Excel (CSV)</a>
                    <a href="{{ route('fixed-assets.export', ['type' => 'internal', 'format' => 'pdf']) }}" target="_blank" class="block px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700 border-b">Cetak PDF / Layar</a>

                    <div class="px-3 py-2 bg-purple-50 border-b font-bold text-xs text-purple-500 uppercase">Lap. Pajak (Tax)</div>
                    <a href="{{ route('fixed-assets.export', ['type' => 'pajak', 'format' => 'csv']) }}" class="block px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700">Unduh Excel (CSV)</a>
                    <a href="{{ route('fixed-assets.export', ['type' => 'pajak', 'format' => 'pdf']) }}" target="_blank" class="block px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700">Cetak PDF / Layar</a>
                </div>
            </div>

            <!-- CEK HAK AKSES CREATE (C) -->
            @if(\App\Models\CompanySetting::canAction('C', 'Aset Tetap'))
            <button onclick="document.getElementById('assetModal').classList.remove('hidden')" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2 w-full sm:w-auto justify-center">
                <i data-feather="plus" class="w-4 h-4"></i> Beli Aset Baru
            </button>
            @endif
        </div>
    </div>

    <!-- Tabel Daftar Aset -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead class="bg-white border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600">Nama Aset</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Tgl Beli</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Harga Beli</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Akumulasi Susut</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Nilai Buku</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $ast)
                    @php $bookValue = $ast->purchase_price - $ast->accum_depreciation; @endphp
                    <tr class="border-b border-slate-50 hover:bg-slate-50">
                        <td class="p-4 text-sm font-bold text-slate-800">
                            {{ $ast->name }} <br/>
                            <span class="text-xs font-normal text-slate-400">Umur: {{ $ast->useful_life }} Bln</span>
                        </td>
                        <td class="p-4 text-sm text-slate-600">{{ \Carbon\Carbon::parse($ast->purchase_date)->format('d/m/Y') }}</td>
                        <td class="p-4 text-sm text-right text-slate-800 font-medium">Rp {{ number_format($ast->purchase_price, 0, ',', '.') }}</td>
                        <td class="p-4 text-sm text-right text-red-600 font-medium">Rp {{ number_format($ast->accum_depreciation, 0, ',', '.') }}</td>
                        <td class="p-4 text-sm text-right text-emerald-700 font-bold">Rp {{ number_format($bookValue, 0, ',', '.') }}</td>

                        <td class="p-4 text-center">
                            @if($ast->status === 'Aktif')

                                <!-- CEK HAK AKSES UPDATE (U) -->
                                @if(\App\Models\CompanySetting::canAction('U', 'Aset Tetap'))
                                <form action="{{ route('fixed-assets.depreciate', $ast->id) }}" method="POST" onsubmit="return confirm('Proses penyusutan 1 bulan untuk aset ini? Jurnal akan otomatis dibuat.');">
                                    @csrf
                                    <button type="submit" class="bg-orange-100 hover:bg-orange-200 text-orange-700 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center justify-center gap-1 w-full transition-colors">
                                        <i data-feather="trending-down" class="w-3 h-3"></i> Susutkan
                                    </button>
                                </form>
                                @else
                                <!-- Jika tidak punya hak UPDATE, tampilkan tombol terkunci -->
                                <span class="bg-slate-100 text-slate-400 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center justify-center gap-1 w-full cursor-not-allowed" title="Anda tidak memiliki hak akses (Update) untuk menyusutkan aset">
                                    <i data-feather="lock" class="w-3 h-3"></i> Terkunci
                                </span>
                                @endif

                            @else
                                <span class="bg-slate-100 text-slate-500 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center justify-center gap-1 w-full">
                                    <i data-feather="check-circle" class="w-3 h-3"></i> Lunas
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="p-8 text-center text-slate-500">Belum ada aset tetap yang didaftarkan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Beli Aset -->
    <div id="assetModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 class="text-xl font-bold text-slate-800">Daftarkan Aset Tetap Baru</h3>
                <button onclick="document.getElementById('assetModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">
                    <i data-feather="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- PERHATIAN: Tambahkan event onsubmit="unformatRibuan(this)" di sini -->
            <form action="{{ route('fixed-assets.store') }}" method="POST" class="p-6 space-y-4" onsubmit="unformatRibuan(this)">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Aset / Inventaris</label>
                    <input type="text" name="name" required placeholder="Cth: MacBook Pro M3, Mobil Operasional..." class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-slate-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal Pembelian</label>
                        <input type="date" name="purchase_date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Umur Ekonomis (Bulan)</label>
                        <div class="relative">
                            <input type="number" name="useful_life" required min="1" placeholder="48" class="w-full pr-14 pl-4 py-2.5 border border-slate-300 rounded-lg outline-none focus:ring-2 focus:ring-slate-500">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 font-medium text-slate-400">Bln</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Harga Beli Aset</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-slate-500">Rp</span>
                        <!-- PERHATIAN: Type diubah ke text, class format-ribuan dan oninput ditambahkan -->
                        <input type="text" name="purchase_price" required oninput="formatRibuan(this)" placeholder="0" class="format-ribuan w-full pl-10 pr-4 py-3 border-2 border-slate-300 rounded-lg font-bold text-lg outline-none focus:border-slate-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-lg border border-slate-200">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Akun Aset (Debit)</label>
                        <select name="asset_account" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-slate-500">
                            <option value="" disabled selected>-- Pilih Akun 121 --</option>
                            @foreach($assetAccounts as $a) <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option> @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Bayar Dari (Kredit)</label>
                        <select name="payment_account" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-slate-500">
                            <option value="" disabled selected>-- Pilih Kas/Bank --</option>
                            @foreach($banks as $a) <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option> @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="document.getElementById('assetModal').classList.add('hidden')" class="px-5 py-2 text-slate-600 hover:bg-slate-100 rounded-lg font-bold">Batal</button>
                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-6 py-2.5 rounded-lg font-bold flex items-center gap-2">
                        Simpan & Buat Jurnal Pembelian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // ==========================================
    // HELPER JAVASCRIPT GLOBAL UNTUK INPUT ANGKA
    // ==========================================
    function formatRibuan(inputElement) {
        // Hapus semua karakter selain angka (0-9)
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
            // Hilangkan semua titik sebelum data dikirim ke Controller/Database
            input.value = input.value.replace(/\./g, '');
        });
    }
</script>
@endsection --}}

{{-- @extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10 relative">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="briefcase" class="text-slate-700 w-7 h-7"></i> Aset Tetap (Fixed Assets)
            </h1>
            <p class="text-slate-500 mt-1">Kelola pembelian inventaris perusahaan dan jurnal penyusutan otomatis</p>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <!-- Dropdown Ekspor -->
            <div class="relative group z-20">
                <button class="bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 text-emerald-700 px-4 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-colors flex items-center gap-2">
                    <i data-feather="printer" class="w-4 h-4"></i> Ekspor Data
                </button>
                <div class="absolute right-0 mt-1 w-56 bg-white border border-slate-200 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all overflow-hidden">
                    <div class="px-3 py-2 bg-slate-50 border-b font-bold text-xs text-slate-400 uppercase">Lap. Internal (Asli)</div>
                    <a href="{{ route('fixed-assets.export', ['type' => 'internal', 'format' => 'csv']) }}" class="block px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700">Unduh Excel (CSV)</a>
                    <a href="{{ route('fixed-assets.export', ['type' => 'internal', 'format' => 'pdf']) }}" target="_blank" class="block px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700 border-b">Cetak PDF / Layar</a>

                    <div class="px-3 py-2 bg-purple-50 border-b font-bold text-xs text-purple-500 uppercase">Lap. Pajak (Tax)</div>
                    <a href="{{ route('fixed-assets.export', ['type' => 'pajak', 'format' => 'csv']) }}" class="block px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700">Unduh Excel (CSV)</a>
                    <a href="{{ route('fixed-assets.export', ['type' => 'pajak', 'format' => 'pdf']) }}" target="_blank" class="block px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700">Cetak PDF / Layar</a>
                </div>
            </div>

            <!-- CEK HAK AKSES CREATE (C) -->
            @if(\App\Models\CompanySetting::canAction('C', 'Aset Tetap'))
            <button onclick="openAssetModal()" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2 w-full sm:w-auto justify-center">
                <i data-feather="plus" class="w-4 h-4"></i> Beli Aset Baru
            </button>
            @endif
        </div>
    </div>

    <!-- Tabel Daftar Aset -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead class="bg-white border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600">Nama Aset</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Tgl Beli</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Harga Beli</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Akumulasi Susut</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Nilai Buku</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $ast)
                    @php $bookValue = $ast->purchase_price - $ast->accum_depreciation; @endphp
                    <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                        <td class="p-4 text-sm font-bold text-slate-800">
                            {{ $ast->name }} <br/>
                            <span class="text-xs font-normal text-slate-400">Umur: {{ $ast->useful_life }} Bln</span>
                        </td>
                        <td class="p-4 text-sm text-slate-600">{{ \Carbon\Carbon::parse($ast->purchase_date)->format('d/m/Y') }}</td>
                        <td class="p-4 text-sm text-right text-slate-800 font-medium">Rp {{ number_format($ast->purchase_price, 0, ',', '.') }}</td>
                        <td class="p-4 text-sm text-right text-red-600 font-medium">Rp {{ number_format($ast->accum_depreciation, 0, ',', '.') }}</td>
                        <td class="p-4 text-sm text-right text-emerald-700 font-bold">Rp {{ number_format($bookValue, 0, ',', '.') }}</td>
                        <td class="p-4 text-center">
                            @if($ast->status === 'Aktif')

                                <!-- CEK HAK AKSES UPDATE (U) -->
                                @if(\App\Models\CompanySetting::canAction('U', 'Aset Tetap'))
                                <form action="{{ route('fixed-assets.depreciate', $ast->id) }}" method="POST" onsubmit="confirmDepreciate(event, this, '{{ $ast->name }}')">
                                    @csrf
                                    <button type="submit" class="bg-orange-100 hover:bg-orange-200 text-orange-700 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center justify-center gap-1 w-full transition-colors shadow-sm">
                                        <i data-feather="trending-down" class="w-3 h-3"></i> Susutkan
                                    </button>
                                </form>
                                @else
                                <!-- Jika tidak punya hak UPDATE, tampilkan tombol terkunci -->
                                <span class="bg-slate-100 text-slate-400 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center justify-center gap-1 w-full cursor-not-allowed" title="Anda tidak memiliki hak akses (Update) untuk menyusutkan aset">
                                    <i data-feather="lock" class="w-3 h-3"></i> Terkunci
                                </span>
                                @endif

                            @else
                                <span class="bg-slate-100 text-slate-500 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center justify-center gap-1 w-full border border-slate-200">
                                    <i data-feather="check-circle" class="w-3 h-3 text-emerald-500"></i> Lunas Disusutkan
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="p-10 text-center text-slate-500">Belum ada aset tetap yang didaftarkan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Beli Aset Beranimasi -->
    <div id="assetModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 backdrop-blur-sm transition-all">
        <div id="assetModalContent" class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col border-t-4 border-slate-800 transform transition-transform scale-95 origin-center">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 class="text-xl font-bold text-slate-800">Daftarkan Aset Tetap Baru</h3>
                <button type="button" onclick="closeAssetModal()" class="text-slate-400 hover:text-red-500 bg-white rounded-full p-1 shadow-sm transition-colors">
                    <i data-feather="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form id="assetForm" action="{{ route('fixed-assets.store') }}" method="POST" class="p-6 space-y-4" onsubmit="unformatRibuan(this)">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Aset / Inventaris</label>
                    <input type="text" name="name" required placeholder="Cth: MacBook Pro M3, Mobil Operasional..." class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-slate-500 font-medium">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal Pembelian</label>
                        <input type="date" name="purchase_date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-slate-500 font-medium">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Umur Ekonomis (Bulan)</label>
                        <div class="relative">
                            <input type="number" name="useful_life" required min="1" placeholder="48" class="w-full pr-14 pl-4 py-2.5 border border-slate-300 rounded-lg outline-none focus:ring-2 focus:ring-slate-500 font-bold text-center">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 font-bold text-slate-400">Bln</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Harga Beli Aset (DPP)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-slate-500">Rp</span>
                        <input type="text" name="purchase_price" required oninput="formatRibuan(this)" placeholder="0" class="format-ribuan w-full pl-10 pr-4 py-3 border-2 border-slate-300 rounded-lg font-black text-lg outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-200">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-lg border border-slate-200">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Akun Aset (Debit)</label>
                        <select name="asset_account" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-slate-500 font-semibold text-slate-700">
                            <option value="" disabled selected>-- Pilih Akun 121 --</option>
                            @foreach($assetAccounts as $a) <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option> @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Bayar Dari (Kredit)</label>
                        <select name="payment_account" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-slate-500 font-semibold text-slate-700">
                            <option value="" disabled selected>-- Pilih Kas/Bank --</option>
                            @foreach($banks as $a) <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option> @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeAssetModal()" class="px-5 py-2.5 text-slate-600 font-bold hover:bg-slate-100 rounded-lg text-sm transition-colors">Batal</button>
                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-6 py-2.5 rounded-lg text-sm font-bold flex items-center gap-2 shadow-md transition-colors">
                        <i data-feather="save" class="w-4 h-4"></i> Simpan & Buat Jurnal Pembelian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- PENGENDALI JAVASCRIPT & SWEETALERT2 -->
<script>
    // ==========================================
    // SWEETALERT2: SESSION SUCCESS & ERROR
    // ==========================================
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

    // ==========================================
    // SWEETALERT2: KONFIRMASI PENYUSUTAN ASET
    // ==========================================
    function confirmDepreciate(event, form, assetName) {
        event.preventDefault();

        Swal.fire({
            title: 'Susutkan Aset?',
            html: `Proses penyusutan 1 bulan untuk <b>"${assetName}"</b> akan dijalankan.<br><br><span class="text-sm text-slate-500 font-medium">Sistem akan secara otomatis menghitung nilai depresiasi (Garis Lurus) dan mencetak Jurnal Biaya Penyusutan ke Buku Besar.</span>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ea580c', // Orange-600
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i data-feather="trending-down" class="w-4 h-4 inline mr-1"></i> Ya, Susutkan Sekarang',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            didOpen: () => { if (typeof feather !== 'undefined') feather.replace(); }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses Jurnal...',
                    text: 'Menghitung penyusutan dan mencatat ke Buku Besar.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                form.submit();
            }
        });
    }

    // ==========================================
    // FUNGSI SWAL LOADING UNTUK FORM BELI ASET
    // ==========================================
    document.getElementById('assetForm').addEventListener('submit', function() {
        Swal.fire({
            title: 'Mencatat Pembelian Aset...',
            text: 'Sistem sedang mendaftarkan aset dan membuat Jurnal Pengeluaran Kas.',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });
    });

    // ==========================================
    // LOGIKA MODAL FORM ASET DENGAN ANIMASI PEGAS
    // ==========================================
    function openAssetModal() {
        const modal = document.getElementById('assetModal');
        const content = document.getElementById('assetModalContent');
        modal.classList.remove('hidden');

        // Transisi Halus (Scale in)
        setTimeout(() => {
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    }

    function closeAssetModal() {
        const modal = document.getElementById('assetModal');
        const content = document.getElementById('assetModalContent');

        content.classList.remove('scale-100');
        content.classList.add('scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 150);
    }

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
</script>
@endsection --}}

{{-- @extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10 relative">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 dark:text-white flex items-center gap-3 transition-colors">
                <i data-feather="briefcase" class="text-slate-700 dark:text-slate-400 w-7 h-7"></i> Aset Tetap (Fixed Assets)
            </h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 transition-colors">Kelola pembelian inventaris perusahaan dan jurnal penyusutan otomatis</p>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <!-- Dropdown Ekspor -->
            <div class="relative group z-20">
                <button class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-900/30 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 px-4 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-colors flex items-center gap-2">
                    <i data-feather="printer" class="w-4 h-4"></i> Ekspor Data
                </button>
                <div class="absolute right-0 mt-1 w-56 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all overflow-hidden">
                    <div class="px-3 py-2 bg-slate-50 dark:bg-slate-700/50 border-b dark:border-slate-700 font-bold text-xs text-slate-400 dark:text-slate-300 uppercase">Lap. Internal (Asli)</div>
                    <a href="{{ route('fixed-assets.export', ['type' => 'internal', 'format' => 'csv']) }}" class="block px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 font-semibold text-slate-700 dark:text-slate-200 transition-colors">Unduh Excel (CSV)</a>
                    <a href="{{ route('fixed-assets.export', ['type' => 'internal', 'format' => 'pdf']) }}" target="_blank" class="block px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 font-semibold text-slate-700 dark:text-slate-200 border-b dark:border-slate-700 transition-colors">Cetak PDF / Layar</a>

                    <div class="px-3 py-2 bg-purple-50 dark:bg-purple-900/20 border-b dark:border-slate-700 font-bold text-xs text-purple-500 dark:text-purple-400 uppercase">Lap. Pajak (Tax)</div>
                    <a href="{{ route('fixed-assets.export', ['type' => 'pajak', 'format' => 'csv']) }}" class="block px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 font-semibold text-slate-700 dark:text-slate-200 transition-colors">Unduh Excel (CSV)</a>
                    <a href="{{ route('fixed-assets.export', ['type' => 'pajak', 'format' => 'pdf']) }}" target="_blank" class="block px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 font-semibold text-slate-700 dark:text-slate-200 transition-colors">Cetak PDF / Layar</a>
                </div>
            </div>

            <!-- CEK HAK AKSES CREATE (C) -->
            @if(\App\Models\CompanySetting::canAction('C', 'Aset Tetap'))
            <button onclick="openAssetModal()" class="bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 dark:hover:bg-slate-600 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2 w-full sm:w-auto justify-center">
                <i data-feather="plus" class="w-4 h-4"></i> Beli Aset Baru
            </button>
            @endif
        </div>
    </div>

    <!-- Tabel Daftar Aset -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden transition-colors">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead class="bg-white dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700 transition-colors">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300">Nama Aset</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300">Tgl Beli</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300 text-right">Harga Beli</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300 text-right">Akumulasi Susut</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300 text-right">Nilai Buku</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $ast)
                    @php $bookValue = $ast->purchase_price - $ast->accum_depreciation; @endphp
                    <tr class="border-b border-slate-50 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="p-4 text-sm font-bold text-slate-800 dark:text-white">
                            {{ $ast->name }} <br/>
                            <span class="text-xs font-normal text-slate-400 dark:text-slate-500">Umur: {{ $ast->useful_life }} Bln</span>
                        </td>
                        <td class="p-4 text-sm text-slate-600 dark:text-slate-400">{{ \Carbon\Carbon::parse($ast->purchase_date)->format('d/m/Y') }}</td>
                        <td class="p-4 text-sm text-right text-slate-800 dark:text-slate-200 font-medium">Rp {{ number_format($ast->purchase_price, 0, ',', '.') }}</td>
                        <td class="p-4 text-sm text-right text-red-600 dark:text-red-400 font-medium">Rp {{ number_format($ast->accum_depreciation, 0, ',', '.') }}</td>
                        <td class="p-4 text-sm text-right text-emerald-700 dark:text-emerald-400 font-bold">Rp {{ number_format($bookValue, 0, ',', '.') }}</td>
                        <td class="p-4 text-center">
                            @if($ast->status === 'Aktif')

                                <!-- CEK HAK AKSES UPDATE (U) -->
                                @if(\App\Models\CompanySetting::canAction('U', 'Aset Tetap'))
                                <form action="{{ route('fixed-assets.depreciate', $ast->id) }}" method="POST" onsubmit="confirmDepreciate(event, this, '{{ $ast->name }}')">
                                    @csrf
                                    <button type="submit" class="bg-orange-100 dark:bg-orange-900/30 hover:bg-orange-200 dark:hover:bg-orange-900/50 text-orange-700 dark:text-orange-400 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center justify-center gap-1 w-full transition-colors shadow-sm">
                                        <i data-feather="trending-down" class="w-3 h-3"></i> Susutkan
                                    </button>
                                </form>
                                @else
                                <!-- Jika tidak punya hak UPDATE, tampilkan tombol terkunci -->
                                <span class="bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center justify-center gap-1 w-full cursor-not-allowed" title="Anda tidak memiliki hak akses (Update) untuk menyusutkan aset">
                                    <i data-feather="lock" class="w-3 h-3"></i> Terkunci
                                </span>
                                @endif

                            @else
                                <span class="bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center justify-center gap-1 w-full border border-slate-200 dark:border-slate-600 transition-colors">
                                    <i data-feather="check-circle" class="w-3 h-3 text-emerald-500 dark:text-emerald-400"></i> Lunas Disusutkan
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="p-10 text-center text-slate-500 dark:text-slate-400">Belum ada aset tetap yang didaftarkan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Beli Aset Beranimasi -->
    <div id="assetModal" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4 backdrop-blur-sm transition-all">
        <div id="assetModalContent" class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col border-t-4 border-slate-800 dark:border-slate-500 transform transition-transform scale-95 origin-center transition-colors">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">Daftarkan Aset Tetap Baru</h3>
                <button type="button" onclick="closeAssetModal()" class="text-slate-400 dark:text-slate-500 hover:text-red-500 dark:hover:text-red-400 bg-white dark:bg-slate-700 rounded-full p-1 shadow-sm transition-colors">
                    <i data-feather="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form id="assetForm" action="{{ route('fixed-assets.store') }}" method="POST" class="p-6 space-y-4" onsubmit="unformatRibuan(this)">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Aset / Inventaris</label>
                    <input type="text" name="name" required placeholder="Cth: MacBook Pro M3, Mobil Operasional..." class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-slate-500 font-medium transition-colors">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Pembelian</label>
                        <input type="date" name="purchase_date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-slate-500 font-medium transition-colors dark:color-scheme-dark">
                        <style>.dark\:color-scheme-dark::-webkit-calendar-picker-indicator { filter: invert(1); }</style>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Umur Ekonomis (Bulan)</label>
                        <div class="relative">
                            <input type="number" name="useful_life" required min="1" placeholder="48" class="w-full pr-14 pl-4 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg outline-none focus:ring-2 focus:ring-slate-500 font-bold text-center transition-colors">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 font-bold text-slate-400 dark:text-slate-500">Bln</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Harga Beli Aset (DPP)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-slate-500 dark:text-slate-400">Rp</span>
                        <input type="text" name="purchase_price" required oninput="formatRibuan(this)" placeholder="0" class="format-ribuan w-full pl-10 pr-4 py-3 border-2 border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg font-black text-lg outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-400 transition-colors">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 bg-slate-50 dark:bg-slate-900/50 p-4 rounded-lg border border-slate-200 dark:border-slate-700 transition-colors">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Akun Aset (Debit)</label>
                        <select name="asset_account" required class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-slate-500 font-semibold transition-colors">
                            <option value="" disabled selected>-- Pilih Akun 121 --</option>
                            @foreach($assetAccounts as $a) <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option> @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Bayar Dari (Kredit)</label>
                        <select name="payment_account" required class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-slate-500 font-semibold transition-colors">
                            <option value="" disabled selected>-- Pilih Kas/Bank --</option>
                            @foreach($banks as $a) <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option> @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-700">
                    <button type="button" onclick="closeAssetModal()" class="px-5 py-2.5 text-slate-600 dark:text-slate-400 font-bold hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg text-sm transition-colors">Batal</button>
                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white px-6 py-2.5 rounded-lg text-sm font-bold flex items-center gap-2 shadow-md transition-colors">
                        <i data-feather="save" class="w-4 h-4"></i> Simpan & Buat Jurnal Pembelian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- PENGENDALI JAVASCRIPT & SWEETALERT2 -->
<script>
    // ==========================================
    // SWEETALERT2: SESSION SUCCESS & ERROR
    // ==========================================
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

    // ==========================================
    // SWEETALERT2: KONFIRMASI PENYUSUTAN ASET
    // ==========================================
    function confirmDepreciate(event, form, assetName) {
        event.preventDefault();

        Swal.fire({
            title: 'Susutkan Aset?',
            html: `Proses penyusutan 1 bulan untuk <b>"${assetName}"</b> akan dijalankan.<br><br><span class="text-sm text-slate-500 font-medium">Sistem akan secara otomatis menghitung nilai depresiasi (Garis Lurus) dan mencetak Jurnal Biaya Penyusutan ke Buku Besar.</span>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ea580c', // Orange-600
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i data-feather="trending-down" class="w-4 h-4 inline mr-1"></i> Ya, Susutkan Sekarang',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            didOpen: () => { if (typeof feather !== 'undefined') feather.replace(); }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses Jurnal...',
                    text: 'Menghitung penyusutan dan mencatat ke Buku Besar.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                form.submit();
            }
        });
    }

    // ==========================================
    // FUNGSI SWAL LOADING UNTUK FORM BELI ASET
    // ==========================================
    document.getElementById('assetForm').addEventListener('submit', function() {
        Swal.fire({
            title: 'Mencatat Pembelian Aset...',
            text: 'Sistem sedang mendaftarkan aset dan membuat Jurnal Pengeluaran Kas.',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });
    });

    // ==========================================
    // LOGIKA MODAL FORM ASET DENGAN ANIMASI PEGAS
    // ==========================================
    function openAssetModal() {
        const modal = document.getElementById('assetModal');
        const content = document.getElementById('assetModalContent');
        modal.classList.remove('hidden');

        // Transisi Halus (Scale in)
        setTimeout(() => {
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    }

    function closeAssetModal() {
        const modal = document.getElementById('assetModal');
        const content = document.getElementById('assetModalContent');

        content.classList.remove('scale-100');
        content.classList.add('scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 150);
    }

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
</script>
@endsection --}}

{{-- @extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10 relative">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 dark:text-white flex items-center gap-3 transition-colors">
                <i data-feather="briefcase" class="text-slate-700 dark:text-slate-400 w-7 h-7"></i> {{ __('Aset Tetap (Fixed Assets)') }}
            </h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 transition-colors">{{ __('Kelola pembelian inventaris perusahaan dan jurnal penyusutan otomatis') }}</p>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <!-- Dropdown Ekspor -->
            <div class="relative group z-20">
                <button class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-900/30 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 px-4 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-colors flex items-center gap-2">
                    <i data-feather="printer" class="w-4 h-4"></i> {{ __('Ekspor Data') }}
                </button>
                <div class="absolute right-0 mt-1 w-56 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all overflow-hidden">
                    <div class="px-3 py-2 bg-slate-50 dark:bg-slate-700/50 border-b dark:border-slate-700 font-bold text-xs text-slate-400 dark:text-slate-300 uppercase">{{ __('Lap. Internal (Asli)') }}</div>
                    <a href="{{ route('fixed-assets.export', ['type' => 'internal', 'format' => 'csv']) }}" class="block px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 font-semibold text-slate-700 dark:text-slate-200 transition-colors">{{ __('Unduh Excel (CSV)') }}</a>
                    <a href="{{ route('fixed-assets.export', ['type' => 'internal', 'format' => 'pdf']) }}" target="_blank" class="block px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 font-semibold text-slate-700 dark:text-slate-200 border-b dark:border-slate-700 transition-colors">{{ __('Cetak PDF / Layar') }}</a>

                    <div class="px-3 py-2 bg-purple-50 dark:bg-purple-900/20 border-b dark:border-slate-700 font-bold text-xs text-purple-500 dark:text-purple-400 uppercase">{{ __('Lap. Pajak (Tax)') }}</div>
                    <a href="{{ route('fixed-assets.export', ['type' => 'pajak', 'format' => 'csv']) }}" class="block px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 font-semibold text-slate-700 dark:text-slate-200 transition-colors">{{ __('Unduh Excel (CSV)') }}</a>
                    <a href="{{ route('fixed-assets.export', ['type' => 'pajak', 'format' => 'pdf']) }}" target="_blank" class="block px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 font-semibold text-slate-700 dark:text-slate-200 transition-colors">{{ __('Cetak PDF / Layar') }}</a>
                </div>
            </div>

            <!-- CEK HAK AKSES CREATE (C) -->
            @if(\App\Models\CompanySetting::canAction('C', 'Aset Tetap'))
            <button onclick="openAssetModal()" class="bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 dark:hover:bg-slate-600 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2 w-full sm:w-auto justify-center">
                <i data-feather="plus" class="w-4 h-4"></i> {{ __('Beli Aset Baru') }}
            </button>
            @endif
        </div>
    </div>

    <!-- Tabel Daftar Aset -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden transition-colors">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead class="bg-white dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700 transition-colors">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300">{{ __('Nama Aset') }}</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300">{{ __('Tgl Beli') }}</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300 text-right">{{ __('Harga Beli') }}</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300 text-right">{{ __('Akumulasi Susut') }}</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300 text-right">{{ __('Nilai Buku') }}</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300 text-center">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $ast)
                    @php $bookValue = $ast->purchase_price - $ast->accum_depreciation; @endphp
                    <tr class="border-b border-slate-50 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="p-4 text-sm font-bold text-slate-800 dark:text-white">
                            {{ $ast->name }} <br/>
                            <span class="text-xs font-normal text-slate-400 dark:text-slate-500">{{ __('Umur:') }} {{ $ast->useful_life }} {{ __('Bln') }}</span>
                        </td>
                        <td class="p-4 text-sm text-slate-600 dark:text-slate-400">{{ \Carbon\Carbon::parse($ast->purchase_date)->format('d/m/Y') }}</td>
                        <td class="p-4 text-sm text-right text-slate-800 dark:text-slate-200 font-medium">Rp {{ number_format($ast->purchase_price, 0, ',', '.') }}</td>
                        <td class="p-4 text-sm text-right text-red-600 dark:text-red-400 font-medium">Rp {{ number_format($ast->accum_depreciation, 0, ',', '.') }}</td>
                        <td class="p-4 text-sm text-right text-emerald-700 dark:text-emerald-400 font-bold">Rp {{ number_format($bookValue, 0, ',', '.') }}</td>
                        <td class="p-4 text-center">
                            @if($ast->status === 'Aktif')

                                <!-- CEK HAK AKSES UPDATE (U) -->
                                @if(\App\Models\CompanySetting::canAction('U', 'Aset Tetap'))
                                <form action="{{ route('fixed-assets.depreciate', $ast->id) }}" method="POST" onsubmit="confirmDepreciate(event, this, '{{ $ast->name }}')">
                                    @csrf
                                    <button type="submit" class="bg-orange-100 dark:bg-orange-900/30 hover:bg-orange-200 dark:hover:bg-orange-900/50 text-orange-700 dark:text-orange-400 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center justify-center gap-1 w-full transition-colors shadow-sm">
                                        <i data-feather="trending-down" class="w-3 h-3"></i> {{ __('Susutkan') }}
                                    </button>
                                </form>
                                @else
                                <!-- Jika tidak punya hak UPDATE, tampilkan tombol terkunci -->
                                <span class="bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center justify-center gap-1 w-full cursor-not-allowed" title="{{ __('Anda tidak memiliki hak akses (Update) untuk menyusutkan aset') }}">
                                    <i data-feather="lock" class="w-3 h-3"></i> {{ __('Terkunci') }}
                                </span>
                                @endif

                            @else
                                <span class="bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center justify-center gap-1 w-full border border-slate-200 dark:border-slate-600 transition-colors">
                                    <i data-feather="check-circle" class="w-3 h-3 text-emerald-500 dark:text-emerald-400"></i> {{ __('Lunas Disusutkan') }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="p-10 text-center text-slate-500 dark:text-slate-400">{{ __('Belum ada aset tetap yang didaftarkan.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Beli Aset Beranimasi -->
    <div id="assetModal" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4 backdrop-blur-sm transition-all">
        <div id="assetModalContent" class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col border-t-4 border-slate-800 dark:border-slate-500 transform transition-transform scale-95 origin-center transition-colors">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">{{ __('Daftarkan Aset Tetap Baru') }}</h3>
                <button type="button" onclick="closeAssetModal()" class="text-slate-400 dark:text-slate-500 hover:text-red-500 dark:hover:text-red-400 bg-white dark:bg-slate-700 rounded-full p-1 shadow-sm transition-colors">
                    <i data-feather="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form id="assetForm" action="{{ route('fixed-assets.store') }}" method="POST" class="p-6 space-y-4" onsubmit="unformatRibuan(this)">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">{{ __('Nama Aset / Inventaris') }}</label>
                    <input type="text" name="name" required placeholder="{{ __('Cth: MacBook Pro M3, Mobil Operasional...') }}" class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-slate-500 font-medium transition-colors">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">{{ __('Tanggal Pembelian') }}</label>
                        <input type="date" name="purchase_date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-slate-500 font-medium transition-colors dark:color-scheme-dark">
                        <style>.dark\:color-scheme-dark::-webkit-calendar-picker-indicator { filter: invert(1); }</style>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">{{ __('Umur Ekonomis (Bulan)') }}</label>
                        <div class="relative">
                            <input type="number" name="useful_life" required min="1" placeholder="48" class="w-full pr-14 pl-4 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg outline-none focus:ring-2 focus:ring-slate-500 font-bold text-center transition-colors">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 font-bold text-slate-400 dark:text-slate-500">{{ __('Bln') }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">{{ __('Harga Beli Aset (DPP)') }}</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-slate-500 dark:text-slate-400">Rp</span>
                        <input type="text" name="purchase_price" required oninput="formatRibuan(this)" placeholder="0" class="format-ribuan w-full pl-10 pr-4 py-3 border-2 border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg font-black text-lg outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-400 transition-colors">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 bg-slate-50 dark:bg-slate-900/50 p-4 rounded-lg border border-slate-200 dark:border-slate-700 transition-colors">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">{{ __('Akun Aset (Debit)') }}</label>
                        <select name="asset_account" required class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-slate-500 font-semibold transition-colors">
                            <option value="" disabled selected>{{ __('-- Pilih Akun 121 --') }}</option>
                            @foreach($assetAccounts as $a) <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option> @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">{{ __('Bayar Dari (Kredit)') }}</label>
                        <select name="payment_account" required class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-slate-500 font-semibold transition-colors">
                            <option value="" disabled selected>{{ __('-- Pilih Kas/Bank --') }}</option>
                            @foreach($banks as $a) <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option> @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-700">
                    <button type="button" onclick="closeAssetModal()" class="px-5 py-2.5 text-slate-600 dark:text-slate-400 font-bold hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg text-sm transition-colors">{{ __('Batal') }}</button>
                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white px-6 py-2.5 rounded-lg text-sm font-bold flex items-center gap-2 shadow-md transition-colors">
                        <i data-feather="save" class="w-4 h-4"></i> {{ __('Simpan & Buat Jurnal Pembelian') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- PENGENDALI JAVASCRIPT & SWEETALERT2 -->
<script>
    // ==========================================
    // SWEETALERT2: SESSION SUCCESS & ERROR
    // ==========================================
    document.addEventListener("DOMContentLoaded", function() {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: @json(__('Berhasil!')),
                text: @json(session('success')),
                timer: 3500,
                timerProgressBar: true,
                showConfirmButton: false,
                position: 'center'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: @json(__('Aksi Gagal!')),
                text: @json(session('error')),
                timer: 5000,
                timerProgressBar: true,
                showConfirmButton: true,
                confirmButtonColor: '#ef4444'
            });
        @endif
    });

    // ==========================================
    // SWEETALERT2: KONFIRMASI PENYUSUTAN ASET
    // ==========================================
    function confirmDepreciate(event, form, assetName) {
        event.preventDefault();

        const descText = @json(__('Proses penyusutan 1 bulan untuk :name akan dijalankan.')).replace(':name', `<b>"${assetName}"</b>`);

        Swal.fire({
            title: @json(__('Susutkan Aset?')),
            html: `${descText}<br><br><span class="text-sm text-slate-500 font-medium">${@json(__('Sistem akan secara otomatis menghitung nilai depresiasi (Garis Lurus) dan mencetak Jurnal Biaya Penyusutan ke Buku Besar.'))}</span>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ea580c',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i data-feather="trending-down" class="w-4 h-4 inline mr-1"></i> ' + @json(__('Ya, Susutkan Sekarang')),
            cancelButtonText: @json(__('Batal')),
            reverseButtons: true,
            didOpen: () => { if (typeof feather !== 'undefined') feather.replace(); }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: @json(__('Memproses Jurnal...')),
                    text: @json(__('Menghitung penyusutan dan mencatat ke Buku Besar.')),
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                form.submit();
            }
        });
    }

    // ==========================================
    // FUNGSI SWAL LOADING UNTUK FORM BELI ASET
    // ==========================================
    document.getElementById('assetForm').addEventListener('submit', function() {
        Swal.fire({
            title: @json(__('Mencatat Pembelian Aset...')),
            text: @json(__('Sistem sedang mendaftarkan aset dan membuat Jurnal Pengeluaran Kas.')),
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });
    });

    // ==========================================
    // LOGIKA MODAL FORM ASET DENGAN ANIMASI PEGAS
    // ==========================================
    function openAssetModal() {
        const modal = document.getElementById('assetModal');
        const content = document.getElementById('assetModalContent');
        modal.classList.remove('hidden');

        setTimeout(() => {
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    }

    function closeAssetModal() {
        const modal = document.getElementById('assetModal');
        const content = document.getElementById('assetModalContent');

        content.classList.remove('scale-100');
        content.classList.add('scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 150);
    }

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
</script>
@endsection --}}

@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10 relative">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 dark:text-white flex items-center gap-3 transition-colors">
                <i data-feather="briefcase" class="text-slate-700 dark:text-slate-400 w-7 h-7"></i> {{ __('Aset Tetap (Fixed Assets)') }}
            </h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 transition-colors">{{ __('Kelola pembelian inventaris perusahaan dan jurnal penyusutan otomatis') }}</p>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <!-- Dropdown Ekspor -->
            <div class="relative group z-20">
                <button class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-900/30 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 px-4 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-colors flex items-center gap-2">
                    <i data-feather="printer" class="w-4 h-4"></i> {{ __('Ekspor Data') }}
                </button>
                <div class="absolute right-0 mt-1 w-56 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all overflow-hidden">
                    <div class="px-3 py-2 bg-slate-50 dark:bg-slate-700/50 border-b dark:border-slate-700 font-bold text-xs text-slate-400 dark:text-slate-300 uppercase">{{ __('Lap. Internal (Asli)') }}</div>
                    <a href="{{ route('fixed-assets.export', ['status' => request('status'), 'search' => request('search'), 'type' => 'internal', 'format' => 'csv']) }}" class="block px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 font-semibold text-slate-700 dark:text-slate-200 transition-colors">{{ __('Unduh Excel (CSV)') }}</a>
                    <a href="{{ route('fixed-assets.export', ['status' => request('status'), 'search' => request('search'), 'type' => 'internal', 'format' => 'pdf']) }}" target="_blank" class="block px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 font-semibold text-slate-700 dark:text-slate-200 border-b dark:border-slate-700 transition-colors">{{ __('Cetak PDF / Layar') }}</a>

                    <div class="px-3 py-2 bg-purple-50 dark:bg-purple-900/20 border-b dark:border-slate-700 font-bold text-xs text-purple-500 dark:text-purple-400 uppercase">{{ __('Lap. Pajak (Tax)') }}</div>
                    <a href="{{ route('fixed-assets.export', ['status' => request('status'), 'search' => request('search'), 'type' => 'pajak', 'format' => 'csv']) }}" class="block px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 font-semibold text-slate-700 dark:text-slate-200 transition-colors">{{ __('Unduh Excel (CSV)') }}</a>
                    <a href="{{ route('fixed-assets.export', ['status' => request('status'), 'search' => request('search'), 'type' => 'pajak', 'format' => 'pdf']) }}" target="_blank" class="block px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 font-semibold text-slate-700 dark:text-slate-200 transition-colors">{{ __('Cetak PDF / Layar') }}</a>
                </div>
            </div>

            <!-- CEK HAK AKSES CREATE (C) -->
            @if(\App\Models\CompanySetting::canAction('C', 'Aset Tetap'))
            <button onclick="openAssetModal()" class="bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 dark:hover:bg-slate-600 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2 w-full sm:w-auto justify-center">
                <i data-feather="plus" class="w-4 h-4"></i> {{ __('Beli Aset Baru') }}
            </button>
            @endif
        </div>
    </div>

    <!-- Tabel Daftar Aset -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden transition-colors">
        <!-- BAR FILTER & PENCARIAN -->
        <div class="p-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 transition-colors">
            <form method="GET" action="{{ route('fixed-assets.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 w-full">
                <div class="flex items-center gap-2">
                    <i data-feather="filter" class="w-4 h-4 text-slate-400"></i>
                    <select name="status" onchange="this.form.submit()" class="border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-slate-500 text-slate-700 dark:text-slate-200 font-medium transition-colors">
                        <option value="">{{ __('Semua Status') }}</option>
                        <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>{{ __('Aktif') }}</option>
                        <option value="Lunas Disusutkan" {{ request('status') == 'Lunas Disusutkan' ? 'selected' : '' }}>{{ __('Lunas Disusutkan') }}</option>
                    </select>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <div class="relative flex-1 sm:w-72">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-feather="search" class="w-4 h-4"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Cari nama aset...') }}" class="w-full pl-9 pr-3 py-2 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-slate-500 transition-colors">
                    </div>
                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm">
                        {{ __('Cari') }}
                    </button>
                    @if(request('search') || request('status'))
                    <a href="{{ route('fixed-assets.index') }}" class="px-3 py-2 text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg text-sm font-semibold transition-colors" title="{{ __('Reset Filter') }}">
                        <i data-feather="x" class="w-4 h-4"></i>
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead class="bg-white dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700 transition-colors">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300">{{ __('Nama Aset') }}</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300">{{ __('Tgl Beli') }}</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300 text-right">{{ __('Harga Beli') }}</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300 text-right">{{ __('Akumulasi Susut') }}</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300 text-right">{{ __('Nilai Buku') }}</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300 text-center">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $ast)
                    @php $bookValue = $ast->purchase_price - $ast->accum_depreciation; @endphp
                    <tr class="border-b border-slate-50 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="p-4 text-sm font-bold text-slate-800 dark:text-white">
                            {{ $ast->name }} <br/>
                            <span class="text-xs font-normal text-slate-400 dark:text-slate-500">{{ __('Umur:') }} {{ $ast->useful_life }} {{ __('Bln') }}</span>
                        </td>
                        <td class="p-4 text-sm text-slate-600 dark:text-slate-400">{{ \Carbon\Carbon::parse($ast->purchase_date)->format('d/m/Y') }}</td>
                        <td class="p-4 text-sm text-right text-slate-800 dark:text-slate-200 font-medium">Rp {{ number_format($ast->purchase_price, 0, ',', '.') }}</td>
                        <td class="p-4 text-sm text-right text-red-600 dark:text-red-400 font-medium">Rp {{ number_format($ast->accum_depreciation, 0, ',', '.') }}</td>
                        <td class="p-4 text-sm text-right text-emerald-700 dark:text-emerald-400 font-bold">Rp {{ number_format($bookValue, 0, ',', '.') }}</td>
                        <td class="p-4 text-center">
                            @if($ast->status === 'Aktif')

                                <!-- CEK HAK AKSES UPDATE (U) -->
                                @if(\App\Models\CompanySetting::canAction('U', 'Aset Tetap'))
                                <form action="{{ route('fixed-assets.depreciate', $ast->id) }}" method="POST" onsubmit="confirmDepreciate(event, this, {{ json_encode($ast->name) }})">
                                    @csrf
                                    <button type="submit" class="bg-orange-100 dark:bg-orange-900/30 hover:bg-orange-200 dark:hover:bg-orange-900/50 text-orange-700 dark:text-orange-400 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center justify-center gap-1 w-full transition-colors shadow-sm">
                                        <i data-feather="trending-down" class="w-3 h-3"></i> {{ __('Susutkan') }}
                                    </button>
                                </form>
                                @else
                                <!-- Jika tidak punya hak UPDATE, tampilkan tombol terkunci -->
                                <span class="bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center justify-center gap-1 w-full cursor-not-allowed" title="{{ __('Anda tidak memiliki hak akses (Update) untuk menyusutkan aset') }}">
                                    <i data-feather="lock" class="w-3 h-3"></i> {{ __('Terkunci') }}
                                </span>
                                @endif

                            @else
                                <span class="bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center justify-center gap-1 w-full border border-slate-200 dark:border-slate-600 transition-colors">
                                    <i data-feather="check-circle" class="w-3 h-3 text-emerald-500 dark:text-emerald-400"></i> {{ __('Lunas Disusutkan') }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="p-10 text-center text-slate-500 dark:text-slate-400">{{ __('Belum ada aset tetap yang didaftarkan.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        @if($assets->hasPages())
        <div class="p-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 transition-colors">
            {{ $assets->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Form Beli Aset Beranimasi -->
    <div id="assetModal" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4 backdrop-blur-sm transition-all">
        <div id="assetModalContent" class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col border-t-4 border-slate-800 dark:border-slate-500 transform transition-transform scale-95 origin-center transition-colors">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">{{ __('Daftarkan Aset Tetap Baru') }}</h3>
                <button type="button" onclick="closeAssetModal()" class="text-slate-400 dark:text-slate-500 hover:text-red-500 dark:hover:text-red-400 bg-white dark:bg-slate-700 rounded-full p-1 shadow-sm transition-colors">
                    <i data-feather="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form id="assetForm" action="{{ route('fixed-assets.store') }}" method="POST" class="p-6 space-y-4" onsubmit="unformatRibuan(this)">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">{{ __('Nama Aset / Inventaris') }}</label>
                    <input type="text" name="name" required placeholder="{{ __('Cth: MacBook Pro M3, Mobil Operasional...') }}" class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-slate-500 font-medium transition-colors">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">{{ __('Tanggal Pembelian') }}</label>
                        <input type="date" name="purchase_date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-slate-500 font-medium transition-colors dark:color-scheme-dark">
                        <style>.dark\:color-scheme-dark::-webkit-calendar-picker-indicator { filter: invert(1); }</style>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">{{ __('Umur Ekonomis (Bulan)') }}</label>
                        <div class="relative">
                            <input type="number" name="useful_life" required min="1" placeholder="48" class="w-full pr-14 pl-4 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg outline-none focus:ring-2 focus:ring-slate-500 font-bold text-center transition-colors">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 font-bold text-slate-400 dark:text-slate-500">{{ __('Bln') }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">{{ __('Harga Beli Aset (DPP)') }}</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-slate-500 dark:text-slate-400">Rp</span>
                        <input type="text" name="purchase_price" required oninput="formatRibuan(this)" placeholder="0" class="format-ribuan w-full pl-10 pr-4 py-3 border-2 border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg font-black text-lg outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-400 transition-colors">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 bg-slate-50 dark:bg-slate-900/50 p-4 rounded-lg border border-slate-200 dark:border-slate-700 transition-colors">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">{{ __('Akun Aset (Debit)') }}</label>
                        <select name="asset_account" required class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-slate-500 font-semibold transition-colors">
                            <option value="" disabled selected>{{ __('-- Pilih Akun Aset (20x) --') }}</option>
                            @foreach($assetAccounts as $a) <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option> @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">{{ __('Bayar Dari (Kredit)') }}</label>
                        <select name="payment_account" required class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-slate-500 font-semibold transition-colors">
                            <option value="" disabled selected>{{ __('-- Pilih Kas/Bank --') }}</option>
                            @foreach($banks as $a) <option value="{{ $a->code }}">[{{ $a->code }}] {{ $a->name }}</option> @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-700">
                    <button type="button" onclick="closeAssetModal()" class="px-5 py-2.5 text-slate-600 dark:text-slate-400 font-bold hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg text-sm transition-colors">{{ __('Batal') }}</button>
                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white px-6 py-2.5 rounded-lg text-sm font-bold flex items-center gap-2 shadow-md transition-colors">
                        <i data-feather="save" class="w-4 h-4"></i> {{ __('Simpan & Buat Jurnal Pembelian') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- PENGENDALI JAVASCRIPT & SWEETALERT2 -->
<script>
    // ==========================================
    // SWEETALERT2: SESSION SUCCESS, ERROR & VALIDASI
    // ==========================================
    document.addEventListener("DOMContentLoaded", function() {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: @json(__('Berhasil!')),
                text: @json(session('success')),
                timer: 3500,
                timerProgressBar: true,
                showConfirmButton: false,
                position: 'center'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: @json(__('Aksi Gagal!')),
                text: @json(session('error')),
                timer: 5000,
                timerProgressBar: true,
                showConfirmButton: true,
                confirmButtonColor: '#ef4444'
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'warning',
                title: @json(__('Validasi Gagal!')),
                text: @json($errors->first()),
                showConfirmButton: true,
                confirmButtonColor: '#f59e0b'
            });
        @endif
    });

    // ==========================================
    // SWEETALERT2: KONFIRMASI PENYUSUTAN ASET
    // ==========================================
    function confirmDepreciate(event, form, assetName) {
        event.preventDefault();

        const descText = @json(__('Proses penyusutan 1 bulan untuk :name akan dijalankan.')).replace(':name', `<b>"${assetName}"</b>`);

        Swal.fire({
            title: @json(__('Susutkan Aset?')),
            html: `${descText}<br><br><span class="text-sm text-slate-500 font-medium">${@json(__('Sistem akan secara otomatis menghitung nilai depresiasi (Garis Lurus) dan mencetak Jurnal Biaya Penyusutan ke Buku Besar.'))}</span>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ea580c',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i data-feather="trending-down" class="w-4 h-4 inline mr-1"></i> ' + @json(__('Ya, Susutkan Sekarang')),
            cancelButtonText: @json(__('Batal')),
            reverseButtons: true,
            didOpen: () => { if (typeof feather !== 'undefined') feather.replace(); }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: @json(__('Memproses Jurnal...')),
                    text: @json(__('Menghitung penyusutan dan mencatat ke Buku Besar.')),
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                form.submit();
            }
        });
    }

    // ==========================================
    // FUNGSI SWAL LOADING UNTUK FORM BELI ASET
    // ==========================================
    document.getElementById('assetForm').addEventListener('submit', function() {
        Swal.fire({
            title: @json(__('Mencatat Pembelian Aset...')),
            text: @json(__('Sistem sedang mendaftarkan aset dan membuat Jurnal Pengeluaran Kas.')),
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });
    });

    // ==========================================
    // LOGIKA MODAL FORM ASET DENGAN ANIMASI PEGAS
    // ==========================================
    function openAssetModal() {
        const modal = document.getElementById('assetModal');
        const content = document.getElementById('assetModalContent');
        modal.classList.remove('hidden');

        setTimeout(() => {
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    }

    function closeAssetModal() {
        const modal = document.getElementById('assetModal');
        const content = document.getElementById('assetModalContent');

        content.classList.remove('scale-100');
        content.classList.add('scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 150);
    }

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
</script>
@endsection
