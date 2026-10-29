<!-- resources/views/products/index.blade.php -->
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
@endsection