<!-- resources/views/categories/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="tag" class="text-purple-600 w-7 h-7"></i> Kategori Produk
            </h1>
            <p class="text-slate-500 mt-1">Kelola pengelompokan barang dan jasa</p>
        </div>
        <button 
            onclick="openCategoryModal('create')"
            class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2"
        >
            <i data-feather="plus" class="w-4 h-4"></i> Tambah Kategori
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600 w-16 text-center">No</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Nama Kategori</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Keterangan</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $index => $cat)
                    <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                        <td class="p-4 text-sm text-center font-medium text-slate-500">{{ $index + 1 }}</td>
                        <td class="p-4 text-sm font-bold text-slate-800">{{ $cat->name }}</td>
                        <td class="p-4 text-sm text-slate-600">{{ $cat->description ?: '-' }}</td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openCategoryModal('edit', {{ json_encode($cat) }})" class="text-blue-500 hover:bg-blue-100 p-1.5 rounded transition-colors" title="Edit">
                                    <i data-feather="edit" class="w-4 h-4"></i>
                                </button>
                                <form action="{{ route('categories.destroy', $cat->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus kategori {{ $cat->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:bg-red-100 p-1.5 rounded transition-colors" title="Hapus">
                                        <i data-feather="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="p-8 text-center text-slate-500">Belum ada kategori.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL FORM KATEGORI -->
    <div id="categoryModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md overflow-hidden flex flex-col">
            <div class="flex justify-between items-center p-5 border-b border-slate-200">
                <h3 id="catModalTitle" class="text-lg font-bold text-slate-800">Tambah Kategori</h3>
                <button onclick="closeCategoryModal()" class="text-slate-400 hover:text-slate-700 transition-colors">
                    <i data-feather="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <form id="categoryForm" method="POST" action="{{ route('categories.store') }}" class="p-5">
                @csrf
                <input type="hidden" name="_method" id="catFormMethod" value="POST">

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Kategori</label>
                        <input type="text" name="name" id="catName" required placeholder="Misal: Barang Jadi" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-purple-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Keterangan (Opsional)</label>
                        <textarea name="description" id="catDesc" rows="3" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-purple-500 text-sm resize-none"></textarea>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <button type="button" onclick="closeCategoryModal()" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg text-sm font-medium">Batal</button>
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openCategoryModal(mode, cat = null) {
        const modal = document.getElementById('categoryModal');
        const form = document.getElementById('categoryForm');
        
        if (mode === 'edit') {
            document.getElementById('catModalTitle').innerText = "Edit Kategori";
            document.getElementById('catFormMethod').value = "PUT";
            form.action = `/categories/${cat.id}`;
            
            document.getElementById('catName').value = cat.name;
            document.getElementById('catDesc').value = cat.description || '';
        } else {
            document.getElementById('catModalTitle').innerText = "Tambah Kategori";
            document.getElementById('catFormMethod').value = "POST";
            form.action = "{{ route('categories.store') }}";
            
            form.reset();
        }
        
        modal.classList.remove('hidden');
    }

    function closeCategoryModal() {
        document.getElementById('categoryModal').classList.add('hidden');
    }
</script>
@endsection