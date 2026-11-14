{{-- <!-- resources/views/inventory/warehouses.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10 relative">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="database" class="w-7 h-7 text-indigo-600"></i> Master Data Gudang
            </h1>
            <p class="text-slate-500 mt-1">Kelola lokasi gudang dan cabang penyimpanan barang</p>
        </div>

        <!-- Cek Hak Akses Create (C) untuk modul Inventori -->
        @if(\App\Models\CompanySetting::canAction('C', 'Inventori'))
        <button onclick="openModal('create')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
            <i data-feather="plus" class="w-4 h-4"></i> Tambah Gudang
        </button>
        @endif
    </div>

    <!-- TABEL GUDANG -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600 w-16 text-center">No</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 w-32">Kode Gudang</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Nama Gudang / Cabang</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 w-64">Lokasi / Alamat Lengkap</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($warehouses as $index => $wh)
                    <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                        <td class="p-4 text-sm text-center font-medium text-slate-500">{{ $index + 1 }}</td>
                        <td class="p-4 text-sm font-bold text-indigo-600">{{ $wh->code }}</td>
                        <td class="p-4 text-sm font-bold text-slate-800">{{ $wh->name }}
                            @if($wh->id == 1)
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-800">
                                    BAWAAN
                                </span>
                            @endif
                        </td>
                        <td class="p-4 text-sm text-slate-600 truncate max-w-xs" title="{{ $wh->location }}">{{ $wh->location ?: '-' }}</td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">

                                <!-- Cek Hak Akses Update (U) untuk modul Inventori -->
                                @if(\App\Models\CompanySetting::canAction('U', 'Inventori'))
                                <button onclick="openModal('edit', {{ json_encode($wh) }})" class="text-blue-500 hover:bg-blue-100 p-1.5 rounded transition-colors" title="Edit Data Gudang">
                                    <i data-feather="edit" class="w-4 h-4"></i>
                                </button>
                                @endif

                                <!-- Cek Hak Akses Delete (D) untuk modul Inventori -->
                                @if(\App\Models\CompanySetting::canAction('D', 'Inventori'))
                                    @if($wh->id != 1)
                                    <form action="{{ route('warehouses.destroy', $wh->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus Gudang ini? Pastikan tidak ada sisa stok di dalamnya.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:bg-red-100 p-1.5 rounded transition-colors" title="Hapus Gudang">
                                            <i data-feather="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                    @else
                                    <!-- Disabled trash button for default warehouse -->
                                    <button type="button" class="text-slate-300 p-1.5 cursor-not-allowed" title="Gudang Bawaan tidak dapat dihapus">
                                        <i data-feather="trash-2" class="w-4 h-4"></i>
                                    </button>
                                    @endif
                                @endif

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="p-8 text-center text-slate-500">Belum ada data gudang tercatat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL FORM TAMBAH/EDIT GUDANG -->
    <div id="warehouseModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md overflow-hidden flex flex-col">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 id="modalTitle" class="text-lg font-bold text-slate-800">Tambah Gudang Baru</h3>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-700 transition-colors"><i data-feather="x" class="w-5 h-5"></i></button>
            </div>

            <form id="warehouseForm" method="POST" action="{{ route('warehouses.store') }}" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Kode Gudang</label>
                    <input type="text" name="code" id="inputCode" required placeholder="Misal: WH-002" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Gudang / Cabang</label>
                    <input type="text" name="name" id="inputName" required placeholder="Misal: Gudang Transit Jakarta" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Alamat Lokasi (Opsional)</label>
                    <textarea name="location" id="inputLocation" rows="3" placeholder="Alamat lengkap gudang..." class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-indigo-500 text-sm resize-none"></textarea>
                </div>

                <div class="mt-8 flex justify-end gap-3 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg text-sm font-bold transition-colors">Batal</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-bold transition-colors">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal(mode, warehouse = null) {
        const modal = document.getElementById('warehouseModal');
        const form = document.getElementById('warehouseForm');

        if (mode === 'edit') {
            document.getElementById('modalTitle').innerText = "Edit Data Gudang";
            document.getElementById('formMethod').value = "PUT";
            form.action = `/warehouses/${warehouse.id}`;

            document.getElementById('inputCode').value = warehouse.code;
            document.getElementById('inputName').value = warehouse.name;
            document.getElementById('inputLocation').value = warehouse.location || '';
        } else {
            document.getElementById('modalTitle').innerText = "Tambah Gudang Baru";
            document.getElementById('formMethod').value = "POST";
            form.action = "{{ route('warehouses.store') }}";

            form.reset();
        }

        modal.classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('warehouseModal').classList.add('hidden');
    }
</script>
@endsection --}}

@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10 relative">
    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="database" class="w-7 h-7 text-indigo-600"></i> Master Data Gudang
            </h1>
            <p class="text-slate-500 mt-1">Kelola lokasi gudang dan cabang penyimpanan barang</p>
        </div>

        <!-- Cek Hak Akses Create (C) untuk modul Inventori -->
        @if(\App\Models\CompanySetting::canAction('C', 'Inventori'))
        <button onclick="openModal('create')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
            <i data-feather="plus" class="w-4 h-4"></i> Tambah Gudang
        </button>
        @endif
    </div>

    <!-- TABEL GUDANG -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600 w-16 text-center">No</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 w-32">Kode Gudang</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Nama Gudang / Cabang</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 w-64">Lokasi / Alamat Lengkap</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($warehouses as $index => $wh)
                    <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                        <td class="p-4 text-sm text-center font-medium text-slate-500">{{ $index + 1 }}</td>
                        <td class="p-4 text-sm font-bold text-indigo-600">{{ $wh->code }}</td>
                        <td class="p-4 text-sm font-bold text-slate-800">{{ $wh->name }}
                            @if($wh->id == 1)
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-800">
                                    BAWAAN
                                </span>
                            @endif
                        </td>
                        <td class="p-4 text-sm text-slate-600 truncate max-w-xs" title="{{ $wh->location }}">{{ $wh->location ?: '-' }}</td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">

                                <!-- Cek Hak Akses Update (U) -->
                                @if(\App\Models\CompanySetting::canAction('U', 'Inventori'))
                                <button onclick="openModal('edit', {{ json_encode($wh) }})" class="text-blue-500 hover:bg-blue-100 p-2 rounded-lg transition-colors shadow-sm border border-transparent hover:border-blue-200" title="Edit Data Gudang">
                                    <i data-feather="edit" class="w-4 h-4"></i>
                                </button>
                                @endif

                                <!-- Cek Hak Akses Delete (D) dengan Swal -->
                                @if(\App\Models\CompanySetting::canAction('D', 'Inventori'))
                                    @if($wh->id != 1)
                                    <form action="{{ route('warehouses.destroy', $wh->id) }}" method="POST" class="inline" onsubmit="confirmDelete(event, this, '{{ $wh->name }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:bg-red-100 p-2 rounded-lg transition-colors shadow-sm border border-transparent hover:border-red-200" title="Hapus Gudang">
                                            <i data-feather="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                    @else
                                    <!-- Disabled trash button for default warehouse -->
                                    <button type="button" class="text-slate-300 p-2 cursor-not-allowed" title="Gudang Bawaan tidak dapat dihapus">
                                        <i data-feather="trash-2" class="w-4 h-4"></i>
                                    </button>
                                    @endif
                                @endif

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="p-8 text-center text-slate-500">Belum ada data gudang tercatat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL FORM TAMBAH/EDIT GUDANG -->
    <div id="warehouseModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 backdrop-blur-sm transition-all">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col border-t-4 border-indigo-600 transform transition-transform scale-95 origin-center" id="warehouseModalContent">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 id="modalTitle" class="text-lg font-bold text-slate-800">Tambah Gudang Baru</h3>
                <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-red-500 bg-white rounded-full p-1 shadow-sm transition-colors">
                    <i data-feather="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form id="warehouseForm" method="POST" action="{{ route('warehouses.store') }}" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Kode Gudang</label>
                    <input type="text" name="code" id="inputCode" required placeholder="Misal: WH-002" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-indigo-500 text-sm font-mono uppercase bg-slate-50">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Gudang / Cabang</label>
                    <input type="text" name="name" id="inputName" required placeholder="Misal: Gudang Transit Jakarta" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-indigo-500 text-sm font-medium">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Alamat Lokasi (Opsional)</label>
                    <textarea name="location" id="inputLocation" rows="3" placeholder="Alamat lengkap gudang..." class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-indigo-500 text-sm resize-none"></textarea>
                </div>

                <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeModal()" class="px-5 py-2.5 text-slate-600 font-bold hover:bg-slate-100 rounded-lg text-sm transition-colors">Batal</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-md transition-colors flex items-center gap-2">
                        <i data-feather="save" class="w-4 h-4"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- PENGENDALI JAVASCRIPT & SWEETALERT2 -->
<script>
    // ==========================================
    // SWEETALERT2 UNTUK PESAN SUKSES / ERROR GLOBAL
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
    // SWEETALERT2 UNTUK KONFIRMASI HAPUS (DELETE)
    // ==========================================
    function confirmDelete(event, form, whName) {
        event.preventDefault();

        Swal.fire({
            title: 'Hapus Gudang?',
            html: `Anda yakin ingin menghapus <b>"${whName}"</b> secara permanen?<br><br><span class="text-sm text-red-500 font-medium">Perhatian: Pastikan tidak ada sisa stok barang di dalam gudang ini sebelum menghapusnya!</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444', // Merah
            cancelButtonColor: '#64748b',  // Abu-abu
            confirmButtonText: '<i data-feather="trash-2" class="w-4 h-4 inline mr-1"></i> Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            didOpen: () => { feather.replace(); }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus Gudang...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                form.submit();
            }
        });
    }

    // ==========================================
    // TAMPILKAN SWAL LOADING SAAT FORM DISUBMIT
    // ==========================================
    document.getElementById('warehouseForm').addEventListener('submit', function() {
        Swal.fire({
            title: 'Menyimpan Data...',
            text: 'Sistem sedang memproses data master gudang.',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });

    // ==========================================
    // LOGIKA MODAL GUDANG DENGAN ANIMASI
    // ==========================================
    function openModal(mode, warehouse = null) {
        const modal = document.getElementById('warehouseModal');
        const form = document.getElementById('warehouseForm');
        const content = document.getElementById('warehouseModalContent');

        if (mode === 'edit') {
            document.getElementById('modalTitle').innerText = "Edit Data Gudang";
            document.getElementById('formMethod').value = "PUT";
            form.action = `/warehouses/${warehouse.id}`;

            document.getElementById('inputCode').value = warehouse.code;
            document.getElementById('inputName').value = warehouse.name;
            document.getElementById('inputLocation').value = warehouse.location || '';
        } else {
            document.getElementById('modalTitle').innerText = "Tambah Gudang Baru";
            document.getElementById('formMethod').value = "POST";
            form.action = "{{ route('warehouses.store') }}";

            form.reset();
        }

        modal.classList.remove('hidden');
        // Transisi Halus (Scale in)
        setTimeout(() => {
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    }

    function closeModal() {
        const modal = document.getElementById('warehouseModal');
        const content = document.getElementById('warehouseModalContent');

        content.classList.remove('scale-100');
        content.classList.add('scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 150);
    }
</script>
@endsection
