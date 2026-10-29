<!-- resources/views/contacts/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10 relative">
    
    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="users" class="w-7 h-7 text-blue-600"></i> Buku Kontak
            </h1>
            <p class="text-slate-500 mt-1">Kelola data Pelanggan (Customer) dan Pemasok (Supplier)</p>
        </div>
        <button onclick="openModal('create')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
            <i data-feather="plus" class="w-4 h-4"></i> Tambah Kontak
        </button>
    </div>

    <!-- TABEL -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        
        <div class="p-4 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row justify-between gap-4">
            <form method="GET" action="{{ route('contacts.index') }}" class="flex items-center gap-2 w-full">
                <!-- Kita serahkan filter/search ke Backend Controller agar data sinkron -->
                <div class="flex items-center gap-2">
                    <i data-feather="filter" class="w-4 h-4 text-slate-400"></i>
                    <select name="type" onchange="this.form.submit()" class="border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 font-medium">
                        <option value="">Semua Tipe</option>
                        <option value="Customer" {{ request('type') == 'Customer' ? 'selected' : '' }}>Customer</option>
                        <option value="Supplier" {{ request('type') == 'Supplier' ? 'selected' : '' }}>Supplier</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead class="bg-white border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600 w-12 text-center">No</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Nama Kontak</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 w-32">Tipe</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">No. Telepon</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Email</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 w-64">Alamat</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contacts as $index => $contact)
                    <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                        <td class="p-4 text-sm text-center font-medium text-slate-500">{{ $index + 1 }}</td>
                        <td class="p-4 text-sm font-bold text-slate-800">{{ $contact->name }}</td>
                        <td class="p-4 text-sm">
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $contact->type == 'Customer' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700' }}">
                                {{ $contact->type }}
                            </span>
                        </td>
                        <td class="p-4 text-sm text-slate-600 font-medium">{{ $contact->phone ?: '-' }}</td>
                        <td class="p-4 text-sm text-slate-600">{{ $contact->email ?: '-' }}</td>
                        <td class="p-4 text-sm text-slate-600 truncate max-w-xs" title="{{ $contact->address }}">{{ $contact->address ?: '-' }}</td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openModal('edit', {{ $contact }})" class="text-blue-500 hover:bg-blue-100 p-1.5 rounded transition-colors" title="Edit"><i data-feather="edit" class="w-4 h-4"></i></button>
                                <form action="{{ route('contacts.destroy', $contact->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus kontak ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:bg-red-100 p-1.5 rounded transition-colors" title="Hapus"><i data-feather="trash-2" class="w-4 h-4"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="p-8 text-center text-slate-500">Kontak tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL FORM KONTAK -->
    <div id="contactModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]">
            <div class="flex justify-between items-center p-5 border-b border-slate-200">
                <h3 id="modalTitle" class="text-lg font-bold text-slate-800">Tambah Kontak Baru</h3>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-700 transition-colors"><i data-feather="x" class="w-5 h-5"></i></button>
            </div>
            
            <form id="contactForm" method="POST" action="{{ route('contacts.store') }}" class="p-5 overflow-y-auto custom-scrollbar">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Kontak</label>
                            <input type="text" name="name" id="inputName" required placeholder="Nama Perusahaan / Perorangan" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Tipe Kontak</label>
                            <select name="type" id="inputType" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium">
                                <option value="Customer">Customer (Pelanggan)</option>
                                <option value="Supplier">Supplier (Pemasok)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">No. Telepon / WA</label>
                            <input type="text" name="phone" id="inputPhone" placeholder="08..." class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                            <input type="email" name="email" id="inputEmail" placeholder="email@contoh.com" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Alamat Lengkap</label>
                            <textarea name="address" id="inputAddress" rows="3" placeholder="Alamat jalan, kota, provinsi..." class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm resize-none"></textarea>
                        </div>
                    </div>
                </div>
                <div class="mt-8 flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg text-sm font-medium transition-colors">Batal</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors">Simpan Kontak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal(mode, contact = null) {
        const modal = document.getElementById('contactModal');
        const form = document.getElementById('contactForm');
        
        if (mode === 'edit') {
            document.getElementById('modalTitle').innerText = "Edit Kontak";
            document.getElementById('formMethod').value = "PUT";
            form.action = `/contacts/${contact.id}`; // Arahkan ke route UPDATE
            
            document.getElementById('inputName').value = contact.name;
            document.getElementById('inputType').value = contact.type;
            document.getElementById('inputPhone').value = contact.phone || '';
            document.getElementById('inputEmail').value = contact.email || '';
            document.getElementById('inputAddress').value = contact.address || '';
        } else {
            document.getElementById('modalTitle').innerText = "Tambah Kontak Baru";
            document.getElementById('formMethod').value = "POST";
            form.action = "{{ route('contacts.store') }}";
            
            form.reset();
        }
        
        modal.classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('contactModal').classList.add('hidden');
    }
</script>
@endsection