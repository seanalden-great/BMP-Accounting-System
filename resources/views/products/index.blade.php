{{-- <!-- resources/views/products/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10 relative">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="package" class="w-7 h-7 text-blue-600"></i> Daftar Produk
            </h1>
            <p class="text-slate-500 mt-1">Kelola data barang, jasa, dan inventori Anda</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('categories.index') }}" class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
                <i data-feather="tag" class="w-4 h-4 text-purple-600"></i> Kelola Kategori
            </a>
            <button onclick="openProductModal('create')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
                <i data-feather="plus" class="w-4 h-4"></i> Tambah Produk
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1000px]">
                <thead class="bg-white border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600 w-24">SKU</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Nama Produk</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Kategori</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Satuan</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Harga Beli</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">HPP</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Harga Jual</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Stok</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                        <td class="p-4 text-sm font-mono font-medium text-blue-600">{{ $product->sku }}</td>
                        <td class="p-4 text-sm font-medium text-slate-800">{{ $product->name }}</td>
                        <td class="p-4 text-sm"><span class="px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">{{ $product->category }}</span></td>
                        <td class="p-4 text-sm text-center text-slate-600">{{ $product->unit ?: '-' }}</td>
                        <td class="p-4 text-sm text-slate-600 text-right">{{ $product->buy_price > 0 ? 'Rp ' . number_format($product->buy_price, 0, ',', '.') : '-' }}</td>
                        <td class="p-4 text-sm font-medium text-orange-600 text-right">
                            {!! $product->hpp > 0 ? 'Rp ' . number_format($product->hpp, 0, ',', '.') : '<span class="text-slate-400 italic text-xs">Otomatis</span>' !!}
                        </td>
                        <td class="p-4 text-sm font-semibold text-slate-800 text-right">{{ $product->sell_price > 0 ? 'Rp ' . number_format($product->sell_price, 0, ',', '.') : '-' }}</td>
                        <td class="p-4 text-sm text-center">
                            <span class="font-semibold {{ $product->stock < 10 ? 'text-red-500' : 'text-slate-700' }}">{{ $product->stock }}</span>
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openProductModal('edit', {{ $product }})" class="text-blue-500 hover:bg-blue-100 p-1.5 rounded transition-colors"><i data-feather="edit" class="w-4 h-4"></i></button>
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:bg-red-100 p-1.5 rounded transition-colors"><i data-feather="trash-2" class="w-4 h-4"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="p-8 text-center text-slate-500">Produk tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL FORM PRODUK -->
    <div id="productModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="flex justify-between items-center p-5 border-b border-slate-200">
                <h3 id="prodModalTitle" class="text-lg font-bold text-slate-800">Tambah Produk Baru</h3>
                <button onclick="closeProductModal()" class="text-slate-400 hover:text-slate-700 transition-colors"><i data-feather="x" class="w-5 h-5"></i></button>
            </div>

            <form id="productForm" method="POST" action="{{ route('products.store') }}" class="p-5 overflow-y-auto custom-scrollbar">
                @csrf
                <input type="hidden" name="_method" id="prodFormMethod" value="POST">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Produk</label>
                        <input type="text" name="name" id="inpName" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">SKU</label>
                        <input type="text" name="sku" id="inpSku" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Kategori</label>
                        <select name="category" id="inpCategory" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="" disabled selected>Pilih Kategori...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Satuan (Unit)</label>
                        <input type="text" name="unit" id="inpUnit" required value="Pcs" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Stok Awal</label>
                        <input type="number" name="stock" id="inpStock" min="0" required value="0" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Harga Beli Dasar</label>
                        <input type="number" name="buy_price" id="inpBuy" min="0" value="0" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Harga Jual</label>
                        <input type="number" name="sell_price" id="inpSell" min="0" required value="0" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-bold text-blue-700">
                    </div>
                </div>
                <div class="mt-8 flex justify-end gap-3">
                    <button type="button" onclick="closeProductModal()" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg text-sm font-medium transition-colors">Batal</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openProductModal(mode, product = null) {
        const modal = document.getElementById('productModal');
        const form = document.getElementById('productForm');

        if (mode === 'edit') {
            document.getElementById('prodModalTitle').innerText = "Edit Produk";
            document.getElementById('prodFormMethod').value = "PUT";
            form.action = `/products/${product.id}`;

            document.getElementById('inpSku').value = product.sku;
            // document.getElementById('inpSku').readOnly = true; // SKU biasanya tidak boleh diedit
            document.getElementById('inpName').value = product.name;
            document.getElementById('inpCategory').value = product.category;
            document.getElementById('inpUnit').value = product.unit;
            document.getElementById('inpStock').value = product.stock;
            document.getElementById('inpBuy').value = product.buy_price;
            document.getElementById('inpSell').value = product.sell_price;
        } else {
            document.getElementById('prodModalTitle').innerText = "Tambah Produk Baru";
            document.getElementById('prodFormMethod').value = "POST";
            form.action = "{{ route('products.store') }}";

            form.reset();
            document.getElementById('inpSku').readOnly = false;
        }

        modal.classList.remove('hidden');
    }

    function closeProductModal() {
        document.getElementById('productModal').classList.add('hidden');
    }
</script>
@endsection --}}

{{-- <!-- resources/views/products/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10 relative">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="package" class="w-7 h-7 text-blue-600"></i> Daftar Produk
            </h1>
            <p class="text-slate-500 mt-1">Kelola data master barang dan jasa Anda</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('categories.index') }}" class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
                <i data-feather="tag" class="w-4 h-4 text-purple-600"></i> Kelola Kategori
            </a>
            <button onclick="openProductModal('create')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
                <i data-feather="plus" class="w-4 h-4"></i> Tambah Master Produk
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1000px]">
                <thead class="bg-white border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600 w-24">SKU</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Nama Produk</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Kategori</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Satuan</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Harga Beli Akhir</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">HPP Berjalan</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Stok (All Gudang)</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                        <td class="p-4 text-sm font-mono font-medium text-blue-600">{{ $product->sku }}</td>
                        <td class="p-4 text-sm font-bold text-slate-800">{{ $product->name }}</td>
                        <td class="p-4 text-sm"><span class="px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">{{ $product->category }}</span></td>
                        <td class="p-4 text-sm text-center text-slate-600">{{ $product->unit ?: '-' }}</td>

                        <!-- Harga Beli Terakhir (Dari Transaksi Pembelian) -->
                        <td class="p-4 text-sm text-slate-600 text-right">{{ $product->buy_price > 0 ? 'Rp ' . number_format($product->buy_price, 0, ',', '.') : 'Rp 0' }}</td>

                        <!-- HPP Berjalan (Moving Average) -->
                        <td class="p-4 text-sm font-bold text-orange-600 text-right">
                            {{ $product->hpp > 0 ? 'Rp ' . number_format($product->hpp, 0, ',', '.') : 'Rp 0' }}
                        </td>

                        <!-- Total Stok Dinamis dari Gudang -->
                        @php $totalStock = $product->total_stock ?? 0; @endphp
                        <td class="p-4 text-sm text-center">
                            <span class="font-bold text-lg {{ $totalStock <= 0 ? 'text-red-500' : 'text-emerald-600' }}">
                                {{ $totalStock }}
                            </span>
                        </td>

                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openProductModal('edit', {{ json_encode($product) }})" class="text-blue-500 hover:bg-blue-100 p-1.5 rounded transition-colors" title="Edit Master Produk"><i data-feather="edit" class="w-4 h-4"></i></button>
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:bg-red-100 p-1.5 rounded transition-colors" title="Hapus Produk"><i data-feather="trash-2" class="w-4 h-4"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="p-8 text-center text-slate-500">Master Produk tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL FORM PRODUK (Sudah Disederhanakan) -->
    <div id="productModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md overflow-hidden flex flex-col">
            <div class="flex justify-between items-center p-5 border-b border-slate-200">
                <h3 id="prodModalTitle" class="text-lg font-bold text-slate-800">Tambah Produk Baru</h3>
                <button onclick="closeProductModal()" class="text-slate-400 hover:text-slate-700 transition-colors"><i data-feather="x" class="w-5 h-5"></i></button>
            </div>

            <form id="productForm" method="POST" action="{{ route('products.store') }}" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="prodFormMethod" value="POST">

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">SKU (Kode Barang)</label>
                    <input type="text" name="sku" id="inpSku" required placeholder="Misal: BRG-001" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-mono uppercase">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Produk</label>
                    <input type="text" name="name" id="inpName" required placeholder="Nama lengkap barang" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Kategori</label>
                        <select name="category" id="inpCategory" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="" disabled selected>Pilih Kategori...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Satuan (Unit)</label>
                        <input type="text" name="unit" id="inpUnit" required value="Pcs" placeholder="Pcs, Kg, Liter" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                </div>

                <div class="bg-blue-50 text-blue-800 text-xs p-3 rounded-lg border border-blue-100 flex items-start gap-2 mt-4">
                    <i data-feather="info" class="w-4 h-4 shrink-0 mt-0.5"></i>
                    <p><strong>Info ERP:</strong> Stok Awal, HPP, dan Harga Jual diatur secara otomatis melalui modul Pembelian, Penjualan, dan Penyesuaian Stok (Inventori).</p>
                </div>

                <div class="mt-8 flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeProductModal()" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg text-sm font-bold transition-colors">Batal</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-bold transition-colors">Simpan Master</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openProductModal(mode, product = null) {
        const modal = document.getElementById('productModal');
        const form = document.getElementById('productForm');

        if (mode === 'edit') {
            document.getElementById('prodModalTitle').innerText = "Edit Master Produk";
            document.getElementById('prodFormMethod').value = "PUT";
            form.action = `/products/${product.id}`;

            document.getElementById('inpSku').value = product.sku;
            document.getElementById('inpName').value = product.name;
            document.getElementById('inpCategory').value = product.category;
            document.getElementById('inpUnit').value = product.unit;
        } else {
            document.getElementById('prodModalTitle').innerText = "Tambah Produk Baru";
            document.getElementById('prodFormMethod').value = "POST";
            form.action = "{{ route('products.store') }}";

            form.reset();
            document.getElementById('inpUnit').value = 'Pcs'; // Default value
        }

        modal.classList.remove('hidden');
    }

    function closeProductModal() {
        document.getElementById('productModal').classList.add('hidden');
    }
</script>
@endsection --}}

{{-- <!-- resources/views/products/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10 relative">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="package" class="w-7 h-7 text-blue-600"></i> Daftar Produk
            </h1>
            <p class="text-slate-500 mt-1">Kelola data master barang dan jasa Anda</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('categories.index') }}" class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
                <i data-feather="tag" class="w-4 h-4 text-purple-600"></i> Kelola Kategori
            </a>
            <button onclick="openProductModal('create')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
                <i data-feather="plus" class="w-4 h-4"></i> Tambah Master Produk
            </button>
        </div>
    </div>

    <!-- FITUR PENCARIAN (SEARCH BAR) -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <form action="{{ route('products.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <i data-feather="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama produk, SKU, atau kategori..." class="w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500 font-medium text-slate-700">
            </div>
            <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-6 py-2.5 rounded-lg font-bold transition-colors w-full sm:w-auto flex items-center justify-center gap-2 shadow-sm">
                Cari Produk
            </button>
            @if(!empty($search))
            <a href="{{ route('products.index') }}" title="Hapus Pencarian" class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2.5 rounded-lg font-bold transition-colors w-full sm:w-auto text-center border border-red-200 flex items-center justify-center">
                <i data-feather="x" class="w-4 h-4"></i>
            </a>
            @endif
        </form>
    </div>

    <!-- Tabel Daftar Produk -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1000px]">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600 w-24">SKU</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Nama Produk</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Kategori</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Satuan</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Harga Beli Akhir</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">HPP Berjalan</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Stok (All Gudang)</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                        <td class="p-4 text-sm font-mono font-medium text-blue-600">{{ $product->sku }}</td>
                        <td class="p-4 text-sm font-bold text-slate-800">{{ $product->name }}</td>
                        <td class="p-4 text-sm"><span class="px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">{{ $product->category }}</span></td>
                        <td class="p-4 text-sm text-center text-slate-600 font-semibold">{{ $product->unit ?: '-' }}</td>

                        <!-- Harga Beli Terakhir (Dari Transaksi Pembelian) -->
                        <td class="p-4 text-sm text-slate-600 text-right font-medium">{{ $product->buy_price > 0 ? 'Rp ' . number_format($product->buy_price, 0, ',', '.') : 'Rp 0' }}</td>

                        <!-- HPP Berjalan (Moving Average) -->
                        <td class="p-4 text-sm font-bold text-orange-600 text-right">
                            {{ $product->hpp > 0 ? 'Rp ' . number_format($product->hpp, 0, ',', '.') : 'Rp 0' }}
                        </td>

                        <!-- Total Stok Dinamis dari Gudang -->
                        @php $totalStock = $product->total_stock ?? 0; @endphp
                        <td class="p-4 text-sm text-center">
                            <span class="font-bold text-lg {{ $totalStock <= 0 ? 'text-red-500' : 'text-emerald-600' }}">
                                {{ $totalStock }}
                            </span>
                        </td>

                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openProductModal('edit', {{ json_encode($product) }})" class="text-blue-500 hover:bg-blue-100 p-2 rounded-lg transition-colors" title="Edit Master Produk"><i data-feather="edit" class="w-4 h-4"></i></button>
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:bg-red-100 p-2 rounded-lg transition-colors" title="Hapus Produk"><i data-feather="trash-2" class="w-4 h-4"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-10 text-center">
                            <i data-feather="search" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
                            <p class="text-slate-500 font-medium">@if(!empty($search)) Produk dengan kata kunci "<b>{{ $search }}</b>" tidak ditemukan. @else Belum ada data Master Produk. @endif</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL FORM PRODUK -->
    <div id="productModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md overflow-hidden flex flex-col">
            <div class="flex justify-between items-center p-5 border-b border-slate-200">
                <h3 id="prodModalTitle" class="text-lg font-bold text-slate-800">Tambah Produk Baru</h3>
                <button onclick="closeProductModal()" class="text-slate-400 hover:text-slate-700 transition-colors"><i data-feather="x" class="w-5 h-5"></i></button>
            </div>

            <form id="productForm" method="POST" action="{{ route('products.store') }}" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="prodFormMethod" value="POST">

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">SKU (Kode Barang)</label>
                    <input type="text" name="sku" id="inpSku" required placeholder="Misal: BRG-001" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-mono uppercase">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Produk</label>
                    <input type="text" name="name" id="inpName" required placeholder="Nama lengkap barang" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Kategori</label>
                        <select name="category" id="inpCategory" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium text-slate-700">
                            <option value="" disabled selected>Pilih Kategori...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Satuan (Unit)</label>
                        <input type="text" name="unit" id="inpUnit" required value="Pcs" placeholder="Pcs, Kg, Liter" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium text-slate-700">
                    </div>
                </div>

                <div class="bg-blue-50 text-blue-800 text-xs p-3 rounded-lg border border-blue-100 flex items-start gap-2 mt-4">
                    <i data-feather="info" class="w-4 h-4 shrink-0 mt-0.5"></i>
                    <p><strong>Info ERP:</strong> Stok Awal, HPP, dan Harga Jual diatur secara otomatis melalui modul Pembelian, Penjualan, dan Penyesuaian Stok (Inventori).</p>
                </div>

                <div class="mt-8 flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeProductModal()" class="px-5 py-2.5 text-slate-600 hover:bg-slate-100 rounded-lg text-sm font-bold transition-colors">Batal</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-bold transition-colors">Simpan Master</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openProductModal(mode, product = null) {
        const modal = document.getElementById('productModal');
        const form = document.getElementById('productForm');

        if (mode === 'edit') {
            document.getElementById('prodModalTitle').innerText = "Edit Master Produk";
            document.getElementById('prodFormMethod').value = "PUT";
            form.action = `/products/${product.id}`;

            document.getElementById('inpSku').value = product.sku;
            document.getElementById('inpName').value = product.name;
            document.getElementById('inpCategory').value = product.category;
            document.getElementById('inpUnit').value = product.unit;
        } else {
            document.getElementById('prodModalTitle').innerText = "Tambah Produk Baru";
            document.getElementById('prodFormMethod').value = "POST";
            form.action = "{{ route('products.store') }}";

            form.reset();
            document.getElementById('inpUnit').value = 'Pcs'; // Default value
        }

        modal.classList.remove('hidden');
    }

    function closeProductModal() {
        document.getElementById('productModal').classList.add('hidden');
    }
</script>
@endsection --}}

<!-- resources/views/products/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10 relative">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="package" class="w-7 h-7 text-blue-600"></i> Daftar Produk
            </h1>
            <p class="text-slate-500 mt-1">Kelola data master barang dan jasa Anda</p>
        </div>
        {{-- <div class="flex items-center gap-3">
            <a href="{{ route('categories.index') }}" class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
                <i data-feather="tag" class="w-4 h-4 text-purple-600"></i> Kelola Kategori
            </a>
            <button onclick="openProductModal('create')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
                <i data-feather="plus" class="w-4 h-4"></i> Tambah Master Produk
            </button>
        </div> --}}
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto mt-4 sm:mt-0">
            <!-- Dropdown Ekspor -->
            <div class="relative group">
                <button class="w-full sm:w-auto bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 text-emerald-700 px-4 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-colors flex items-center justify-center gap-2">
                    <i data-feather="printer" class="w-4 h-4"></i> Ekspor Data
                </button>
                <div class="absolute right-0 mt-1 w-56 bg-white border border-slate-200 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 overflow-hidden">
                    <div class="px-3 py-2 bg-slate-50 border-b font-bold text-xs text-slate-400 uppercase">Lap. Internal (Asli)</div>
                    <a href="{{ route('products.export', ['search' => request('search'), 'type' => 'internal', 'format' => 'csv']) }}" class="block px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700">Unduh Excel (CSV)</a>
                    <a href="{{ route('products.export', ['search' => request('search'), 'type' => 'internal', 'format' => 'pdf']) }}" target="_blank" class="block px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700 border-b">Cetak PDF / Layar</a>

                    <div class="px-3 py-2 bg-purple-50 border-b font-bold text-xs text-purple-500 uppercase">Lap. Pajak (Tax)</div>
                    <a href="{{ route('products.export', ['search' => request('search'), 'type' => 'pajak', 'format' => 'csv']) }}" class="block px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700">Unduh Excel (CSV)</a>
                    <a href="{{ route('products.export', ['search' => request('search'), 'type' => 'pajak', 'format' => 'pdf']) }}" target="_blank" class="block px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700">Cetak PDF / Layar</a>
                </div>
            </div>

            <a href="{{ route('categories.index') }}" class="w-full sm:w-auto bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center justify-center gap-2">
                <i data-feather="tag" class="w-4 h-4 text-purple-600"></i> Kelola Kategori
            </a>
            <button onclick="openProductModal('create')" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center justify-center gap-2">
                <i data-feather="plus" class="w-4 h-4"></i> Tambah Master Produk
            </button>
        </div>
    </div>

    <!-- FITUR PENCARIAN (LIVE SEARCH MENGGUNAKAN AJAX) -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <!-- Cegah form disubmit dengan tombol Enter secara native -->
        <form id="searchForm" onsubmit="event.preventDefault();" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <i data-feather="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5"></i>

                <!-- Input ini akan didengarkan oleh JavaScript setiap kali ada ketikan -->
                <input type="text" id="searchInput" autocomplete="off" value="{{ $search ?? '' }}" placeholder="Ketik nama produk, kode SKU, atau kategori barang di sini..." class="w-full pl-10 pr-10 py-2.5 border border-slate-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500 font-medium text-slate-700 transition-all">

                <!-- Animasi Loading (Bawaan disembunyikan) -->
                <div id="searchLoading" class="hidden absolute right-4 top-1/2 -translate-y-1/2">
                    <div class="w-4 h-4 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                </div>
            </div>

            <!-- Tombol Reset Manual (Hilang jika input kosong) -->
            <button type="button" id="btnResetSearch" onclick="resetSearch()" class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2.5 rounded-lg font-bold transition-colors w-full sm:w-auto flex items-center justify-center border border-red-200 {{ empty($search) ? 'hidden' : '' }}" title="Bersihkan Pencarian">
                <i data-feather="x" class="w-5 h-5"></i>
            </button>
        </form>
    </div>

    <!-- Tabel Daftar Produk -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1000px]">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600 w-24">SKU</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Nama Produk</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Kategori</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Satuan</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">Harga Beli Akhir</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-right">HPP Berjalan</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Stok (All Gudang)</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center w-24">Aksi</th>
                    </tr>
                </thead>

                <!-- Beri ID "tableBody" agar JavaScript mudah mencari area yang harus diganti datanya -->
                <tbody id="tableBody" class="transition-opacity duration-200 ease-in-out">
                    @forelse($products as $product)
                    <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                        <td class="p-4 text-sm font-mono font-medium text-blue-600">{{ $product->sku }}</td>
                        <td class="p-4 text-sm font-bold text-slate-800">{{ $product->name }}</td>
                        <td class="p-4 text-sm"><span class="px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">{{ $product->category }}</span></td>
                        <td class="p-4 text-sm text-center text-slate-600 font-semibold">{{ $product->unit ?: '-' }}</td>

                        <td class="p-4 text-sm text-slate-600 text-right font-medium">{{ $product->buy_price > 0 ? 'Rp ' . number_format($product->buy_price, 0, ',', '.') : 'Rp 0' }}</td>

                        <td class="p-4 text-sm font-bold text-orange-600 text-right">
                            {{ $product->hpp > 0 ? 'Rp ' . number_format($product->hpp, 0, ',', '.') : 'Rp 0' }}
                        </td>

                        @php $totalStock = $product->total_stock ?? 0; @endphp
                        <td class="p-4 text-sm text-center">
                            <span class="font-bold text-lg {{ $totalStock <= 0 ? 'text-red-500' : 'text-emerald-600' }}">
                                {{ $totalStock }}
                            </span>
                        </td>

                        {{-- <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openProductModal('edit', {{ json_encode($product) }})" class="text-blue-500 hover:bg-blue-100 p-2 rounded-lg transition-colors" title="Edit Master Produk"><i data-feather="edit" class="w-4 h-4"></i></button>
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:bg-red-100 p-2 rounded-lg transition-colors" title="Hapus Produk"><i data-feather="trash-2" class="w-4 h-4"></i></button>
                                </form>
                            </div>
                        </td> --}}
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <!-- TOMBOL DETAIL (BARU) -->
                                <a href="{{ route('products.show', $product->id) }}" class="text-indigo-600 hover:bg-indigo-50 p-2 rounded-lg transition-colors shadow-sm border border-transparent hover:border-indigo-200" title="Lihat Riwayat Kartu Stok & Detail">
                                    <i data-feather="eye" class="w-4 h-4"></i>
                                </a>

                                <button onclick="openProductModal('edit', {{ json_encode($product) }})" class="text-blue-500 hover:bg-blue-100 p-2 rounded-lg transition-colors" title="Edit Master Produk"><i data-feather="edit" class="w-4 h-4"></i></button>
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:bg-red-100 p-2 rounded-lg transition-colors" title="Hapus Produk"><i data-feather="trash-2" class="w-4 h-4"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-10 text-center">
                            <i data-feather="search" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
                            <p class="text-slate-500 font-medium">@if(!empty($search)) Produk dengan kata kunci "<b>{{ $search }}</b>" tidak ditemukan. @else Belum ada data Master Produk. @endif</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL FORM PRODUK -->
    <div id="productModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md overflow-hidden flex flex-col">
            <div class="flex justify-between items-center p-5 border-b border-slate-200">
                <h3 id="prodModalTitle" class="text-lg font-bold text-slate-800">Tambah Produk Baru</h3>
                <button onclick="closeProductModal()" class="text-slate-400 hover:text-slate-700 transition-colors"><i data-feather="x" class="w-5 h-5"></i></button>
            </div>

            <form id="productForm" method="POST" action="{{ route('products.store') }}" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="prodFormMethod" value="POST">

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">SKU (Kode Barang)</label>
                    <input type="text" name="sku" id="inpSku" required placeholder="Misal: BRG-001" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-mono uppercase">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Produk</label>
                    <input type="text" name="name" id="inpName" required placeholder="Nama lengkap barang" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Kategori</label>
                        <select name="category" id="inpCategory" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium text-slate-700">
                            <option value="" disabled selected>Pilih Kategori...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Satuan (Unit)</label>
                        <input type="text" name="unit" id="inpUnit" required value="Pcs" placeholder="Pcs, Kg, Liter" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium text-slate-700">
                    </div>
                </div>

                <div class="bg-blue-50 text-blue-800 text-xs p-3 rounded-lg border border-blue-100 flex items-start gap-2 mt-4">
                    <i data-feather="info" class="w-4 h-4 shrink-0 mt-0.5"></i>
                    <p><strong>Info ERP:</strong> Stok Awal, HPP, Harga Jual, dan Harga Beli diatur secara otomatis melalui modul Pembelian, Penjualan, dan Penyesuaian Stok (Inventori).</p>
                </div>

                <div class="mt-8 flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeProductModal()" class="px-5 py-2.5 text-slate-600 hover:bg-slate-100 rounded-lg text-sm font-bold transition-colors">Batal</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-bold transition-colors">Simpan Master</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // ==========================================
    // LOGIKA LIVE SEARCH (PENCARIAN TANPA ENTER)
    // ==========================================
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('tableBody');
    const searchLoading = document.getElementById('searchLoading');
    const btnResetSearch = document.getElementById('btnResetSearch');

    searchInput.addEventListener('input', function() {
        // Hapus antrean pencarian sebelumnya agar tidak dobel/spam membebani server
        clearTimeout(searchTimeout);

        const query = this.value;

        // Tampilkan atau sembunyikan tombol silang merah muda
        if (query.length > 0) {
            btnResetSearch.classList.remove('hidden');
        } else {
            btnResetSearch.classList.add('hidden');
        }

        // Tampilkan animasi roda berputar dan beri efek buram pada tabel
        searchLoading.classList.remove('hidden');
        tableBody.style.opacity = '0.4';

        // Beri jeda 350 milidetik sebelum menembak ke server agar efisien
        searchTimeout = setTimeout(() => {
            // Kita pinjam rute Index biasa, dan potong tabel isinya
            const url = `{{ route('products.index') }}?search=${encodeURIComponent(query)}`;

            fetch(url)
                .then(response => response.text())
                .then(html => {
                    // Ekstrak bagian 'tableBody' saja dari respons HTML utuh
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTbody = doc.getElementById('tableBody').innerHTML;

                    // Timpa tabel yang lama dengan yang baru didapat
                    tableBody.innerHTML = newTbody;

                    // Kembalikan UI ke wujud normal
                    tableBody.style.opacity = '1';
                    searchLoading.classList.add('hidden');

                    // Supaya ikon-ikon aksi (pensil & kotak sampah) muncul kembali
                    if (typeof feather !== 'undefined') {
                        feather.replace();
                    }
                })
                .catch(error => {
                    console.error('Error fetching live search results:', error);
                    tableBody.style.opacity = '1';
                    searchLoading.classList.add('hidden');
                });
        }, 350);
    });

    // Fungsi klik tombol X (Reset Search)
    function resetSearch() {
        searchInput.value = '';
        searchInput.dispatchEvent(new Event('input')); // Picu event seolah kita menghapus teks manual
        searchInput.focus();
    }


    // ==========================================
    // LOGIKA MODAL PRODUK
    // ==========================================
    function openProductModal(mode, product = null) {
        const modal = document.getElementById('productModal');
        const form = document.getElementById('productForm');

        if (mode === 'edit') {
            document.getElementById('prodModalTitle').innerText = "Edit Master Produk";
            document.getElementById('prodFormMethod').value = "PUT";
            form.action = `/products/${product.id}`;

            document.getElementById('inpSku').value = product.sku;
            document.getElementById('inpName').value = product.name;
            document.getElementById('inpCategory').value = product.category;
            document.getElementById('inpUnit').value = product.unit;
        } else {
            document.getElementById('prodModalTitle').innerText = "Tambah Produk Baru";
            document.getElementById('prodFormMethod').value = "POST";
            form.action = "{{ route('products.store') }}";

            form.reset();
            document.getElementById('inpUnit').value = 'Pcs'; // Default value
        }

        modal.classList.remove('hidden');
    }

    function closeProductModal() {
        document.getElementById('productModal').classList.add('hidden');
    }
</script>
@endsection
