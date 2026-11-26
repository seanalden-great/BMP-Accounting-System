{{-- <!-- resources/views/contacts/index.blade.php -->
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

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="users" class="w-7 h-7 text-blue-600"></i> Buku Kontak
            </h1>
            <p class="text-slate-500 mt-1">Kelola data Pelanggan (Customer) dan Pemasok (Supplier)</p>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <!-- Dropdown Ekspor -->
            <div class="relative group">
                <button class="bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 text-emerald-700 px-4 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-colors flex items-center gap-2">
                    <i data-feather="printer" class="w-4 h-4"></i> Ekspor Data
                </button>
                <div class="absolute right-0 mt-1 w-56 bg-white border border-slate-200 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 overflow-hidden">
                    <div class="px-3 py-2 bg-slate-50 border-b font-bold text-xs text-slate-400 uppercase">Lap. Internal (Asli)</div>
                    <a href="{{ route('contacts.export', ['contact_type' => request('type'), 'type' => 'internal', 'format' => 'csv']) }}" class="block px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700">Unduh Excel (CSV)</a>
                    <a href="{{ route('contacts.export', ['contact_type' => request('type'), 'type' => 'internal', 'format' => 'pdf']) }}" target="_blank" class="block px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700 border-b">Cetak PDF / Layar</a>

                    <div class="px-3 py-2 bg-purple-50 border-b font-bold text-xs text-purple-500 uppercase">Lap. Pajak (Tax)</div>
                    <a href="{{ route('contacts.export', ['contact_type' => request('type'), 'type' => 'pajak', 'format' => 'csv']) }}" class="block px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700">Unduh Excel (CSV)</a>
                    <a href="{{ route('contacts.export', ['contact_type' => request('type'), 'type' => 'pajak', 'format' => 'pdf']) }}" target="_blank" class="block px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700">Cetak PDF / Layar</a>
                </div>
            </div>

            <button onclick="openModal('create')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2 w-full sm:w-auto justify-center">
                <i data-feather="plus" class="w-4 h-4"></i> Tambah Kontak
            </button>

            <!-- CEK HAK AKSES CREATE (C) -->
            @if(\App\Models\CompanySetting::canAction('C', 'Kontak'))
            <button onclick="openModal('create')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2 w-full sm:w-auto justify-center">
                <i data-feather="plus" class="w-4 h-4"></i> Tambah Kontak
            </button>
            @endif
        </div>
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

                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">

                                <!-- CEK HAK AKSES UPDATE (U) -->
                                @if(\App\Models\CompanySetting::canAction('U', 'Kontak'))
                                <button onclick="openModal('edit', {{ $contact }})" class="text-blue-500 hover:bg-blue-100 p-1.5 rounded transition-colors" title="Edit">
                                    <i data-feather="edit" class="w-4 h-4"></i>
                                </button>
                                @endif

                                <!-- CEK HAK AKSES DELETE (D) -->
                                @if(\App\Models\CompanySetting::canAction('D', 'Kontak'))
                                <form action="{{ route('contacts.destroy', $contact->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus kontak ini? Transaksi lampau tidak akan ikut terhapus.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:bg-red-100 p-1.5 rounded transition-colors" title="Hapus">
                                        <i data-feather="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                @endif

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
@endsection --}}

{{-- @extends('layouts.app')

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

        <div class="flex items-center gap-3 w-full md:w-auto">
            <!-- Dropdown Ekspor -->
            <div class="relative group z-20">
                <button class="bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 text-emerald-700 px-4 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-colors flex items-center gap-2">
                    <i data-feather="printer" class="w-4 h-4"></i> Ekspor Data
                </button>
                <div class="absolute right-0 mt-1 w-56 bg-white border border-slate-200 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all overflow-hidden">
                    <div class="px-3 py-2 bg-slate-50 border-b font-bold text-xs text-slate-400 uppercase">Lap. Internal (Asli)</div>
                    <a href="{{ route('contacts.export', ['contact_type' => request('type'), 'type' => 'internal', 'format' => 'csv']) }}" class="block px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700">Unduh Excel (CSV)</a>
                    <a href="{{ route('contacts.export', ['contact_type' => request('type'), 'type' => 'internal', 'format' => 'pdf']) }}" target="_blank" class="block px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700 border-b">Cetak PDF / Layar</a>

                    <div class="px-3 py-2 bg-purple-50 border-b font-bold text-xs text-purple-500 uppercase">Lap. Pajak (Tax)</div>
                    <a href="{{ route('contacts.export', ['contact_type' => request('type'), 'type' => 'pajak', 'format' => 'csv']) }}" class="block px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700">Unduh Excel (CSV)</a>
                    <a href="{{ route('contacts.export', ['contact_type' => request('type'), 'type' => 'pajak', 'format' => 'pdf']) }}" target="_blank" class="block px-4 py-2 text-sm hover:bg-slate-50 font-semibold text-slate-700">Cetak PDF / Layar</a>
                </div>
            </div>

            <!-- CEK HAK AKSES CREATE (C) -->
            @if(\App\Models\CompanySetting::canAction('C', 'Kontak'))
            <button onclick="openModal('create')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2 w-full sm:w-auto justify-center">
                <i data-feather="plus" class="w-4 h-4"></i> Tambah Kontak
            </button>
            @endif
        </div>
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
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $contact->type == 'Customer' ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'bg-orange-100 text-orange-700 border border-orange-200' }}">
                                {{ $contact->type }}
                            </span>
                        </td>
                        <td class="p-4 text-sm text-slate-600 font-medium">{{ $contact->phone ?: '-' }}</td>
                        <td class="p-4 text-sm text-slate-600">{{ $contact->email ?: '-' }}</td>
                        <td class="p-4 text-sm text-slate-600 truncate max-w-xs" title="{{ $contact->address }}">{{ $contact->address ?: '-' }}</td>

                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <!-- CEK HAK AKSES UPDATE (U) -->
                                @if(\App\Models\CompanySetting::canAction('U', 'Kontak'))
                                <button onclick="openModal('edit', {{ $contact }})" class="text-blue-500 hover:bg-blue-100 p-2 rounded-lg transition-colors shadow-sm border border-transparent hover:border-blue-200" title="Edit">
                                    <i data-feather="edit" class="w-4 h-4"></i>
                                </button>
                                @endif

                                <!-- CEK HAK AKSES DELETE (D) -->
                                @if(\App\Models\CompanySetting::canAction('D', 'Kontak'))
                                <form action="{{ route('contacts.destroy', $contact->id) }}" method="POST" class="inline" onsubmit="confirmDelete(event, this, '{{ $contact->name }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:bg-red-100 p-2 rounded-lg transition-colors shadow-sm border border-transparent hover:border-red-200" title="Hapus">
                                        <i data-feather="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="p-10 text-center text-slate-500">Kontak tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL FORM KONTAK BERANIMASI -->
    <div id="contactModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 backdrop-blur-sm transition-all">
        <div id="contactModalContent" class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh] border-t-4 border-blue-600 transform transition-transform scale-95 origin-center">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 id="modalTitle" class="text-lg font-bold text-slate-800">Tambah Kontak Baru</h3>
                <button onclick="closeModal()" class="text-slate-400 hover:text-red-500 bg-white rounded-full p-1 shadow-sm transition-colors"><i data-feather="x" class="w-5 h-5"></i></button>
            </div>

            <form id="contactForm" method="POST" action="{{ route('contacts.store') }}" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="p-6 overflow-y-auto custom-scrollbar space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Kontak</label>
                            <input type="text" name="name" id="inputName" required placeholder="Nama Perusahaan / Perorangan" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-bold">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Tipe Kontak</label>
                            <select name="type" id="inputType" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium">
                                <option value="Customer">Customer (Pelanggan)</option>
                                <option value="Supplier">Supplier (Pemasok)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">No. Telepon / WA (Opsional)</label>
                            <input type="text" name="phone" id="inputPhone" placeholder="08..." class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Email (Opsional)</label>
                            <input type="email" name="email" id="inputEmail" placeholder="email@contoh.com" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Alamat Lengkap</label>
                            <textarea name="address" id="inputAddress" rows="3" placeholder="Alamat jalan, kota, provinsi..." class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm resize-none"></textarea>
                        </div>
                    </div>
                </div>

                <div class="p-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-5 py-2.5 text-slate-600 hover:bg-slate-200 rounded-lg text-sm font-bold transition-colors">Batal</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-md transition-colors flex items-center gap-2">
                        <i data-feather="save" class="w-4 h-4"></i> Simpan Kontak
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
    // SWEETALERT2: KONFIRMASI HAPUS KONTAK
    // ==========================================
    function confirmDelete(event, form, contactName) {
        event.preventDefault();

        Swal.fire({
            title: 'Hapus Kontak?',
            html: `Yakin ingin menghapus <b>"${contactName}"</b> dari Buku Kontak?<br><br><span class="text-sm text-slate-500 font-medium">Jangan khawatir, transaksi lampau (SO/PO) atas nama kontak ini tidak akan ikut terhapus.</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i data-feather="trash-2" class="w-4 h-4 inline mr-1"></i> Ya, Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            didOpen: () => { if (typeof feather !== 'undefined') feather.replace(); }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus Data...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                form.submit();
            }
        });
    }

    // ==========================================
    // FUNGSI SWAL LOADING UNTUK FORM KONTAK
    // ==========================================
    document.getElementById('contactForm').addEventListener('submit', function() {
        Swal.fire({
            title: 'Menyimpan Data...',
            text: 'Sistem sedang menyimpan data kontak.',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });
    });

    // ==========================================
    // LOGIKA MODAL FORM KONTAK DENGAN ANIMASI
    // ==========================================
    function openModal(mode, contact = null) {
        const modal = document.getElementById('contactModal');
        const form = document.getElementById('contactForm');
        const content = document.getElementById('contactModalContent');

        if (mode === 'edit') {
            document.getElementById('modalTitle').innerText = "Edit Kontak";
            document.getElementById('formMethod').value = "PUT";
            form.action = `/contacts/${contact.id}`;

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
        // Transisi Halus (Scale in)
        setTimeout(() => {
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    }

    function closeModal() {
        const modal = document.getElementById('contactModal');
        const content = document.getElementById('contactModalContent');

        content.classList.remove('scale-100');
        content.classList.add('scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 150);
    }
</script>
@endsection --}}

{{-- @extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10 relative">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 dark:text-white flex items-center gap-3 transition-colors">
                <i data-feather="users" class="w-7 h-7 text-blue-600 dark:text-blue-400"></i> Buku Kontak
            </h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 transition-colors">Kelola data Pelanggan (Customer) dan Pemasok (Supplier)</p>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <!-- Dropdown Ekspor -->
            <div class="relative group z-20">
                <button class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-900/30 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 px-4 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-colors flex items-center gap-2">
                    <i data-feather="printer" class="w-4 h-4"></i> Ekspor Data
                </button>
                <div class="absolute right-0 mt-1 w-56 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all overflow-hidden">
                    <div class="px-3 py-2 bg-slate-50 dark:bg-slate-700/50 border-b dark:border-slate-700 font-bold text-xs text-slate-400 dark:text-slate-300 uppercase">Lap. Internal (Asli)</div>
                    <a href="{{ route('contacts.export', ['contact_type' => request('type'), 'type' => 'internal', 'format' => 'csv']) }}" class="block px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 font-semibold text-slate-700 dark:text-slate-200 transition-colors">Unduh Excel (CSV)</a>
                    <a href="{{ route('contacts.export', ['contact_type' => request('type'), 'type' => 'internal', 'format' => 'pdf']) }}" target="_blank" class="block px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 font-semibold text-slate-700 dark:text-slate-200 border-b dark:border-slate-700 transition-colors">Cetak PDF / Layar</a>

                    <div class="px-3 py-2 bg-purple-50 dark:bg-purple-900/20 border-b dark:border-slate-700 font-bold text-xs text-purple-500 dark:text-purple-400 uppercase">Lap. Pajak (Tax)</div>
                    <a href="{{ route('contacts.export', ['contact_type' => request('type'), 'type' => 'pajak', 'format' => 'csv']) }}" class="block px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 font-semibold text-slate-700 dark:text-slate-200 transition-colors">Unduh Excel (CSV)</a>
                    <a href="{{ route('contacts.export', ['contact_type' => request('type'), 'type' => 'pajak', 'format' => 'pdf']) }}" target="_blank" class="block px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 font-semibold text-slate-700 dark:text-slate-200 transition-colors">Cetak PDF / Layar</a>
                </div>
            </div>

            <!-- CEK HAK AKSES CREATE (C) -->
            @if(\App\Models\CompanySetting::canAction('C', 'Kontak'))
            <button onclick="openModal('create')" class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2 w-full sm:w-auto justify-center">
                <i data-feather="plus" class="w-4 h-4"></i> Tambah Kontak
            </button>
            @endif
        </div>
    </div>

    <!-- TABEL -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden transition-colors">
        <div class="p-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 flex flex-col sm:flex-row justify-between gap-4 transition-colors">
            <form method="GET" action="{{ route('contacts.index') }}" class="flex items-center gap-2 w-full">
                <!-- Kita serahkan filter/search ke Backend Controller agar data sinkron -->
                <div class="flex items-center gap-2">
                    <i data-feather="filter" class="w-4 h-4 text-slate-400"></i>
                    <select name="type" onchange="this.form.submit()" class="border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 dark:text-slate-200 font-medium transition-colors">
                        <option value="">Semua Tipe</option>
                        <option value="Customer" {{ request('type') == 'Customer' ? 'selected' : '' }}>Customer</option>
                        <option value="Supplier" {{ request('type') == 'Supplier' ? 'selected' : '' }}>Supplier</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead class="bg-white dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700 transition-colors">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300 w-12 text-center">No</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300">Nama Kontak</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300 w-32">Tipe</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300">No. Telepon</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300">Email</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300 w-64">Alamat</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contacts as $index => $contact)
                    <tr class="border-b border-slate-50 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="p-4 text-sm text-center font-medium text-slate-500 dark:text-slate-400">{{ $index + 1 }}</td>
                        <td class="p-4 text-sm font-bold text-slate-800 dark:text-white">{{ $contact->name }}</td>
                        <td class="p-4 text-sm">
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $contact->type == 'Customer' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800/30' : 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 border border-orange-200 dark:border-orange-800/30' }}">
                                {{ $contact->type }}
                            </span>
                        </td>
                        <td class="p-4 text-sm text-slate-600 dark:text-slate-400 font-medium">{{ $contact->phone ?: '-' }}</td>
                        <td class="p-4 text-sm text-slate-600 dark:text-slate-400">{{ $contact->email ?: '-' }}</td>
                        <td class="p-4 text-sm text-slate-600 dark:text-slate-400 truncate max-w-xs" title="{{ $contact->address }}">{{ $contact->address ?: '-' }}</td>

                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <!-- CEK HAK AKSES UPDATE (U) -->
                                @if(\App\Models\CompanySetting::canAction('U', 'Kontak'))
                                <button onclick="openModal('edit', {{ $contact }})" class="text-blue-500 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/30 p-2 rounded-lg transition-colors shadow-sm border border-transparent hover:border-blue-200 dark:hover:border-blue-800" title="Edit">
                                    <i data-feather="edit" class="w-4 h-4"></i>
                                </button>
                                @endif

                                <!-- CEK HAK AKSES DELETE (D) -->
                                @if(\App\Models\CompanySetting::canAction('D', 'Kontak'))
                                <form action="{{ route('contacts.destroy', $contact->id) }}" method="POST" class="inline" onsubmit="confirmDelete(event, this, '{{ $contact->name }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 p-2 rounded-lg transition-colors shadow-sm border border-transparent hover:border-red-200 dark:hover:border-red-800" title="Hapus">
                                        <i data-feather="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="p-10 text-center text-slate-500 dark:text-slate-400">Kontak tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL FORM KONTAK BERANIMASI -->
    <div id="contactModal" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4 backdrop-blur-sm transition-all">
        <div id="contactModalContent" class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh] border-t-4 border-blue-600 dark:border-blue-500 transform transition-transform scale-95 origin-center transition-colors">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                <h3 id="modalTitle" class="text-lg font-bold text-slate-800 dark:text-white">Tambah Kontak Baru</h3>
                <button onclick="closeModal()" class="text-slate-400 dark:text-slate-500 hover:text-red-500 dark:hover:text-red-400 bg-white dark:bg-slate-700 rounded-full p-1 shadow-sm transition-colors"><i data-feather="x" class="w-5 h-5"></i></button>
            </div>

            <form id="contactForm" method="POST" action="{{ route('contacts.store') }}" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="p-6 overflow-y-auto custom-scrollbar space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Kontak</label>
                            <input type="text" name="name" id="inputName" required placeholder="Nama Perusahaan / Perorangan" class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-bold transition-colors">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Tipe Kontak</label>
                            <select name="type" id="inputType" required class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium transition-colors">
                                <option value="Customer">Customer (Pelanggan)</option>
                                <option value="Supplier">Supplier (Pemasok)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">No. Telepon / WA (Opsional)</label>
                            <input type="text" name="phone" id="inputPhone" placeholder="08..." class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Email (Opsional)</label>
                            <input type="email" name="email" id="inputEmail" placeholder="email@contoh.com" class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium transition-colors">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Alamat Lengkap</label>
                            <textarea name="address" id="inputAddress" rows="3" placeholder="Alamat jalan, kota, provinsi..." class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm resize-none transition-colors"></textarea>
                        </div>
                    </div>
                </div>

                <div class="p-5 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3 transition-colors">
                    <button type="button" onclick="closeModal()" class="px-5 py-2.5 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg text-sm font-bold transition-colors">Batal</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-md transition-colors flex items-center gap-2">
                        <i data-feather="save" class="w-4 h-4"></i> Simpan Kontak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- PENGENDALI JAVASCRIPT & SWEETALERT2 -->
<script>
    // ==========================================
    // SWEETALERT2: PESAN GLOBAL (SESSION)
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
    // SWEETALERT2: KONFIRMASI HAPUS KONTAK
    // ==========================================
    function confirmDelete(event, form, contactName) {
        event.preventDefault();

        Swal.fire({
            title: 'Hapus Kontak?',
            html: `Yakin ingin menghapus <b>"${contactName}"</b> dari Buku Kontak?<br><br><span class="text-sm text-slate-500 font-medium">Jangan khawatir, transaksi lampau (SO/PO) atas nama kontak ini tidak akan ikut terhapus.</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i data-feather="trash-2" class="w-4 h-4 inline mr-1"></i> Ya, Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            didOpen: () => { if (typeof feather !== 'undefined') feather.replace(); }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus Data...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                form.submit();
            }
        });
    }

    // ==========================================
    // FUNGSI SWAL LOADING UNTUK FORM KONTAK
    // ==========================================
    document.getElementById('contactForm').addEventListener('submit', function() {
        Swal.fire({
            title: 'Menyimpan Data...',
            text: 'Sistem sedang menyimpan data kontak.',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });
    });

    // ==========================================
    // LOGIKA MODAL FORM KONTAK DENGAN ANIMASI
    // ==========================================
    function openModal(mode, contact = null) {
        const modal = document.getElementById('contactModal');
        const form = document.getElementById('contactForm');
        const content = document.getElementById('contactModalContent');

        if (mode === 'edit') {
            document.getElementById('modalTitle').innerText = "Edit Kontak";
            document.getElementById('formMethod').value = "PUT";
            form.action = `/contacts/${contact.id}`;

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
        // Transisi Halus (Scale in)
        setTimeout(() => {
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    }

    function closeModal() {
        const modal = document.getElementById('contactModal');
        const content = document.getElementById('contactModalContent');

        content.classList.remove('scale-100');
        content.classList.add('scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 150);
    }
</script>
@endsection --}}

{{-- @extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10 relative">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 dark:text-white flex items-center gap-3 transition-colors">
                <i data-feather="users" class="w-7 h-7 text-blue-600 dark:text-blue-400"></i> {{ __('Buku Kontak') }}
            </h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 transition-colors">{{ __('Kelola data Pelanggan (Customer) dan Pemasok (Supplier)') }}</p>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <!-- Dropdown Ekspor -->
            <div class="relative group z-20">
                <button class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-900/30 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 px-4 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-colors flex items-center gap-2">
                    <i data-feather="printer" class="w-4 h-4"></i> {{ __('Ekspor Data') }}
                </button>
                <div class="absolute right-0 mt-1 w-56 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all overflow-hidden">
                    <div class="px-3 py-2 bg-slate-50 dark:bg-slate-700/50 border-b dark:border-slate-700 font-bold text-xs text-slate-400 dark:text-slate-300 uppercase">{{ __('Lap. Internal (Asli)') }}</div>
                    <a href="{{ route('contacts.export', ['contact_type' => request('type'), 'type' => 'internal', 'format' => 'csv']) }}" class="block px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 font-semibold text-slate-700 dark:text-slate-200 transition-colors">{{ __('Unduh Excel (CSV)') }}</a>
                    <a href="{{ route('contacts.export', ['contact_type' => request('type'), 'type' => 'internal', 'format' => 'pdf']) }}" target="_blank" class="block px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 font-semibold text-slate-700 dark:text-slate-200 border-b dark:border-slate-700 transition-colors">{{ __('Cetak PDF / Layar') }}</a>

                    <div class="px-3 py-2 bg-purple-50 dark:bg-purple-900/20 border-b dark:border-slate-700 font-bold text-xs text-purple-500 dark:text-purple-400 uppercase">{{ __('Lap. Pajak (Tax)') }}</div>
                    <a href="{{ route('contacts.export', ['contact_type' => request('type'), 'type' => 'pajak', 'format' => 'csv']) }}" class="block px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 font-semibold text-slate-700 dark:text-slate-200 transition-colors">{{ __('Unduh Excel (CSV)') }}</a>
                    <a href="{{ route('contacts.export', ['contact_type' => request('type'), 'type' => 'pajak', 'format' => 'pdf']) }}" target="_blank" class="block px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 font-semibold text-slate-700 dark:text-slate-200 transition-colors">{{ __('Cetak PDF / Layar') }}</a>
                </div>
            </div>

            <!-- CEK HAK AKSES CREATE (C) -->
            @if(\App\Models\CompanySetting::canAction('C', 'Kontak'))
            <button onclick="openModal('create')" class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2 w-full sm:w-auto justify-center">
                <i data-feather="plus" class="w-4 h-4"></i> {{ __('Tambah Kontak') }}
            </button>
            @endif
        </div>
    </div>

    <!-- TABEL -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden transition-colors">
        <div class="p-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 flex flex-col sm:flex-row justify-between gap-4 transition-colors">
            <form method="GET" action="{{ route('contacts.index') }}" class="flex items-center gap-2 w-full">
                <div class="flex items-center gap-2">
                    <i data-feather="filter" class="w-4 h-4 text-slate-400"></i>
                    <select name="type" onchange="this.form.submit()" class="border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 dark:text-slate-200 font-medium transition-colors">
                        <option value="">{{ __('Semua Tipe') }}</option>
                        <option value="Customer" {{ request('type') == 'Customer' ? 'selected' : '' }}>Customer</option>
                        <option value="Supplier" {{ request('type') == 'Supplier' ? 'selected' : '' }}>Supplier</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead class="bg-white dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700 transition-colors">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300 w-12 text-center">{{ __('No') }}</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300">{{ __('Nama Kontak') }}</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300 w-32">{{ __('Tipe') }}</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300">{{ __('No. Telepon') }}</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300">Email</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300 w-64">{{ __('Alamat') }}</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300 text-center w-24">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contacts as $index => $contact)
                    <tr class="border-b border-slate-50 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="p-4 text-sm text-center font-medium text-slate-500 dark:text-slate-400">{{ $index + 1 }}</td>
                        <td class="p-4 text-sm font-bold text-slate-800 dark:text-white">{{ $contact->name }}</td>
                        <td class="p-4 text-sm">
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $contact->type == 'Customer' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800/30' : 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 border border-orange-200 dark:border-orange-800/30' }}">
                                {{ $contact->type }}
                            </span>
                        </td>
                        <td class="p-4 text-sm text-slate-600 dark:text-slate-400 font-medium">{{ $contact->phone ?: '-' }}</td>
                        <td class="p-4 text-sm text-slate-600 dark:text-slate-400">{{ $contact->email ?: '-' }}</td>
                        <td class="p-4 text-sm text-slate-600 dark:text-slate-400 truncate max-w-xs" title="{{ $contact->address }}">{{ $contact->address ?: '-' }}</td>

                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <!-- CEK HAK AKSES UPDATE (U) -->
                                @if(\App\Models\CompanySetting::canAction('U', 'Kontak'))
                                <button onclick="openModal('edit', {{ $contact }})" class="text-blue-500 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/30 p-2 rounded-lg transition-colors shadow-sm border border-transparent hover:border-blue-200 dark:hover:border-blue-800" title="{{ __('Edit') }}">
                                    <i data-feather="edit" class="w-4 h-4"></i>
                                </button>
                                @endif

                                <!-- CEK HAK AKSES DELETE (D) -->
                                @if(\App\Models\CompanySetting::canAction('D', 'Kontak'))
                                <form action="{{ route('contacts.destroy', $contact->id) }}" method="POST" class="inline" onsubmit="confirmDelete(event, this, '{{ addslashes($contact->name) }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 p-2 rounded-lg transition-colors shadow-sm border border-transparent hover:border-red-200 dark:hover:border-red-800" title="{{ __('Hapus') }}">
                                        <i data-feather="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="p-10 text-center text-slate-500 dark:text-slate-400">{{ __('Kontak tidak ditemukan.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL FORM KONTAK BERANIMASI -->
    <div id="contactModal" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4 backdrop-blur-sm transition-all">
        <div id="contactModalContent" class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh] border-t-4 border-blue-600 dark:border-blue-500 transform transition-transform scale-95 origin-center transition-colors">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                <h3 id="modalTitle" class="text-lg font-bold text-slate-800 dark:text-white">{{ __('Tambah Kontak Baru') }}</h3>
                <button onclick="closeModal()" class="text-slate-400 dark:text-slate-500 hover:text-red-500 dark:hover:text-red-400 bg-white dark:bg-slate-700 rounded-full p-1 shadow-sm transition-colors"><i data-feather="x" class="w-5 h-5"></i></button>
            </div>

            <form id="contactForm" method="POST" action="{{ route('contacts.store') }}" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="p-6 overflow-y-auto custom-scrollbar space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">{{ __('Nama Kontak') }}</label>
                            <input type="text" name="name" id="inputName" required placeholder="{{ __('Nama Perusahaan / Perorangan') }}" class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-bold transition-colors">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">{{ __('Tipe Kontak') }}</label>
                            <select name="type" id="inputType" required class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium transition-colors">
                                <option value="Customer">{{ __('Customer (Pelanggan)') }}</option>
                                <option value="Supplier">{{ __('Supplier (Pemasok)') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">{{ __('No. Telepon / WA (Opsional)') }}</label>
                            <input type="text" name="phone" id="inputPhone" placeholder="08..." class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">{{ __('Email (Opsional)') }}</label>
                            <input type="email" name="email" id="inputEmail" placeholder="{{ __('email@contoh.com') }}" class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium transition-colors">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">{{ __('Alamat Lengkap') }}</label>
                            <textarea name="address" id="inputAddress" rows="3" placeholder="{{ __('Alamat jalan, kota, provinsi...') }}" class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm resize-none transition-colors"></textarea>
                        </div>
                    </div>
                </div>

                <div class="p-5 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3 transition-colors">
                    <button type="button" onclick="closeModal()" class="px-5 py-2.5 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg text-sm font-bold transition-colors">{{ __('Batal') }}</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-md transition-colors flex items-center gap-2">
                        <i data-feather="save" class="w-4 h-4"></i> {{ __('Simpan Kontak') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- PENGENDALI JAVASCRIPT & SWEETALERT2 -->
<script>
    // ==========================================
    // SWEETALERT2: PESAN GLOBAL (SESSION)
    // ==========================================
    document.addEventListener("DOMContentLoaded", function() {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: {!! json_encode(__('Berhasil!')) !!},
                text: {!! json_encode(session('success')) !!},
                timer: 3500,
                timerProgressBar: true,
                showConfirmButton: false,
                position: 'center'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: {!! json_encode(__('Aksi Gagal!')) !!},
                text: {!! json_encode(session('error')) !!},
                timer: 5000,
                timerProgressBar: true,
                showConfirmButton: true,
                confirmButtonColor: '#ef4444'
            });
        @endif
    });

    // ==========================================
    // SWEETALERT2: KONFIRMASI HAPUS KONTAK
    // ==========================================
    function confirmDelete(event, form, contactName) {
        event.preventDefault();

        const confirmText = {!! json_encode(__('Yakin ingin menghapus :name dari Buku Kontak?')) !!}.replace(':name', `<b>"${contactName}"</b>`);
        const infoText = {!! json_encode(__('Jangan khawatir, transaksi lampau (SO/PO) atas nama kontak ini tidak akan ikut terhapus.')) !!};

        Swal.fire({
            title: {!! json_encode(__('Hapus Kontak?')) !!},
            html: `${confirmText}<br><br><span class="text-sm text-slate-500 font-medium">${infoText}</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i data-feather="trash-2" class="w-4 h-4 inline mr-1"></i> ' + {!! json_encode(__('Ya, Hapus')) !!},
            cancelButtonText: {!! json_encode(__('Batal')) !!},
            reverseButtons: true,
            didOpen: () => { if (typeof feather !== 'undefined') feather.replace(); }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: {!! json_encode(__('Menghapus Data...')) !!},
                    text: {!! json_encode(__('Mohon tunggu sebentar.')) !!},
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                form.submit();
            }
        });
    }

    // ==========================================
    // FUNGSI SWAL LOADING UNTUK FORM KONTAK
    // ==========================================
    document.getElementById('contactForm').addEventListener('submit', function() {
        Swal.fire({
            title: {!! json_encode(__('Menyimpan Data...')) !!},
            text: {!! json_encode(__('Sistem sedang menyimpan data kontak.')) !!},
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });
    });

    // ==========================================
    // LOGIKA MODAL FORM KONTAK DENGAN ANIMASI
    // ==========================================
    function openModal(mode, contact = null) {
        const modal = document.getElementById('contactModal');
        const form = document.getElementById('contactForm');
        const content = document.getElementById('contactModalContent');

        if (mode === 'edit') {
            document.getElementById('modalTitle').innerText = {!! json_encode(__('Edit Kontak')) !!};
            document.getElementById('formMethod').value = "PUT";
            form.action = `/contacts/${contact.id}`;

            document.getElementById('inputName').value = contact.name;
            document.getElementById('inputType').value = contact.type;
            document.getElementById('inputPhone').value = contact.phone || '';
            document.getElementById('inputEmail').value = contact.email || '';
            document.getElementById('inputAddress').value = contact.address || '';
        } else {
            document.getElementById('modalTitle').innerText = {!! json_encode(__('Tambah Kontak Baru')) !!};
            document.getElementById('formMethod').value = "POST";
            form.action = "{{ route('contacts.store') }}";

            form.reset();
        }

        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    }

    function closeModal() {
        const modal = document.getElementById('contactModal');
        const content = document.getElementById('contactModalContent');

        content.classList.remove('scale-100');
        content.classList.add('scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 150);
    }
</script>
@endsection --}}

@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10 relative">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 dark:text-white flex items-center gap-3 transition-colors">
                <i data-feather="users" class="w-7 h-7 text-blue-600 dark:text-blue-400"></i> {{ __('Buku Kontak') }}
            </h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 transition-colors">{{ __('Kelola data Pelanggan (Customer) dan Pemasok (Supplier)') }}</p>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <!-- Dropdown Ekspor -->
            <div class="relative group z-20">
                <button class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-900/30 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 px-4 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-colors flex items-center gap-2">
                    <i data-feather="printer" class="w-4 h-4"></i> {{ __('Ekspor Data') }}
                </button>
                <div class="absolute right-0 mt-1 w-56 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all overflow-hidden">
                    <div class="px-3 py-2 bg-slate-50 dark:bg-slate-700/50 border-b dark:border-slate-700 font-bold text-xs text-slate-400 dark:text-slate-300 uppercase">{{ __('Lap. Internal (Asli)') }}</div>
                    <a href="{{ route('contacts.export', ['contact_type' => request('type'), 'search' => request('search'), 'type' => 'internal', 'format' => 'csv']) }}" class="block px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 font-semibold text-slate-700 dark:text-slate-200 transition-colors">{{ __('Unduh Excel (CSV)') }}</a>
                    <a href="{{ route('contacts.export', ['contact_type' => request('type'), 'search' => request('search'), 'type' => 'internal', 'format' => 'pdf']) }}" target="_blank" class="block px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 font-semibold text-slate-700 dark:text-slate-200 border-b dark:border-slate-700 transition-colors">{{ __('Cetak PDF / Layar') }}</a>

                    <div class="px-3 py-2 bg-purple-50 dark:bg-purple-900/20 border-b dark:border-slate-700 font-bold text-xs text-purple-500 dark:text-purple-400 uppercase">{{ __('Lap. Pajak (Tax)') }}</div>
                    <a href="{{ route('contacts.export', ['contact_type' => request('type'), 'search' => request('search'), 'type' => 'pajak', 'format' => 'csv']) }}" class="block px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 font-semibold text-slate-700 dark:text-slate-200 transition-colors">{{ __('Unduh Excel (CSV)') }}</a>
                    <a href="{{ route('contacts.export', ['contact_type' => request('type'), 'search' => request('search'), 'type' => 'pajak', 'format' => 'pdf']) }}" target="_blank" class="block px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 font-semibold text-slate-700 dark:text-slate-200 transition-colors">{{ __('Cetak PDF / Layar') }}</a>
                </div>
            </div>

            <!-- CEK HAK AKSES CREATE (C) -->
            @if(\App\Models\CompanySetting::canAction('C', 'Kontak'))
            <button onclick="openModal('create')" class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2 w-full sm:w-auto justify-center">
                <i data-feather="plus" class="w-4 h-4"></i> {{ __('Tambah Kontak') }}
            </button>
            @endif
        </div>
    </div>

    <!-- TABEL -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden transition-colors">
        <div class="p-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 transition-colors">
            <form method="GET" action="{{ route('contacts.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 w-full">
                <!-- Filter Tipe -->
                <div class="flex items-center gap-2">
                    <i data-feather="filter" class="w-4 h-4 text-slate-400"></i>
                    <select name="type" onchange="this.form.submit()" class="border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 dark:text-slate-200 font-medium transition-colors">
                        <option value="">{{ __('Semua Tipe') }}</option>
                        <option value="Customer" {{ request('type') == 'Customer' ? 'selected' : '' }}>Customer</option>
                        <option value="Supplier" {{ request('type') == 'Supplier' ? 'selected' : '' }}>Supplier</option>
                    </select>
                </div>

                <!-- Kolom Pencarian -->
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <div class="relative flex-1 sm:w-72">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-feather="search" class="w-4 h-4"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Cari nama, telp, email, alamat...') }}" class="w-full pl-9 pr-3 py-2 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm">
                        {{ __('Cari') }}
                    </button>
                    @if(request('search') || request('type'))
                    <a href="{{ route('contacts.index') }}" class="px-3 py-2 text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg text-sm font-semibold transition-colors" title="{{ __('Reset Filter') }}">
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
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300 w-12 text-center">{{ __('No') }}</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300">{{ __('Nama Kontak') }}</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300 w-32">{{ __('Tipe') }}</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300">{{ __('No. Telepon') }}</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300">Email</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300 w-64">{{ __('Alamat') }}</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-300 text-center w-24">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contacts as $index => $contact)
                    <tr class="border-b border-slate-50 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="p-4 text-sm text-center font-medium text-slate-500 dark:text-slate-400">{{ $contacts->firstItem() + $index }}</td>
                        <td class="p-4 text-sm font-bold text-slate-800 dark:text-white">{{ $contact->name }}</td>
                        <td class="p-4 text-sm">
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $contact->type == 'Customer' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800/30' : 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 border border-orange-200 dark:border-orange-800/30' }}">
                                {{ $contact->type }}
                            </span>
                        </td>
                        <td class="p-4 text-sm text-slate-600 dark:text-slate-400 font-medium">
                            @if($contact->phone)
                                @php
                                    $waNumber = preg_replace('/[^0-9]/', '', $contact->phone);
                                    if (str_starts_with($waNumber, '0')) {
                                        $waNumber = '62' . substr($waNumber, 1);
                                    }
                                @endphp
                                <a href="https://wa.me/{{ $waNumber }}" target="_blank" class="hover:text-emerald-600 dark:hover:text-emerald-400 hover:underline transition-colors" title="Hubungi via WhatsApp">
                                    {{ $contact->phone }}
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td class="p-4 text-sm text-slate-600 dark:text-slate-400">{{ $contact->email ?: '-' }}</td>
                        <td class="p-4 text-sm text-slate-600 dark:text-slate-400 truncate max-w-xs" title="{{ $contact->address }}">{{ $contact->address ?: '-' }}</td>

                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <!-- CEK HAK AKSES UPDATE (U) -->
                                @if(\App\Models\CompanySetting::canAction('U', 'Kontak'))
                                <button type="button" data-contact="{{ json_encode($contact) }}" onclick="openModal('edit', this)" class="text-blue-500 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/30 p-2 rounded-lg transition-colors shadow-sm border border-transparent hover:border-blue-200 dark:hover:border-blue-800" title="{{ __('Edit') }}">
                                    <i data-feather="edit" class="w-4 h-4"></i>
                                </button>
                                @endif

                                <!-- CEK HAK AKSES DELETE (D) -->
                                @if(\App\Models\CompanySetting::canAction('D', 'Kontak'))
                                <form action="{{ route('contacts.destroy', $contact->id) }}" method="POST" class="inline" onsubmit="confirmDelete(event, this, {{ json_encode($contact->name) }});">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 p-2 rounded-lg transition-colors shadow-sm border border-transparent hover:border-red-200 dark:hover:border-red-800" title="{{ __('Hapus') }}">
                                        <i data-feather="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="p-10 text-center text-slate-500 dark:text-slate-400">{{ __('Kontak tidak ditemukan.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        @if($contacts->hasPages())
        <div class="p-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 transition-colors">
            {{ $contacts->links() }}
        </div>
        @endif
    </div>

    <!-- MODAL FORM KONTAK BERANIMASI -->
    <div id="contactModal" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4 backdrop-blur-sm transition-all">
        <div id="contactModalContent" class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh] border-t-4 border-blue-600 dark:border-blue-500 transform transition-transform scale-95 origin-center transition-colors">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                <h3 id="modalTitle" class="text-lg font-bold text-slate-800 dark:text-white">{{ __('Tambah Kontak Baru') }}</h3>
                <button onclick="closeModal()" class="text-slate-400 dark:text-slate-500 hover:text-red-500 dark:hover:text-red-400 bg-white dark:bg-slate-700 rounded-full p-1 shadow-sm transition-colors"><i data-feather="x" class="w-5 h-5"></i></button>
            </div>

            <form id="contactForm" method="POST" action="{{ route('contacts.store') }}" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="p-6 overflow-y-auto custom-scrollbar space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">{{ __('Nama Kontak') }}</label>
                            <input type="text" name="name" id="inputName" required placeholder="{{ __('Nama Perusahaan / Perorangan') }}" class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-bold transition-colors">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">{{ __('Tipe Kontak') }}</label>
                            <select name="type" id="inputType" required class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium transition-colors">
                                <option value="Customer">{{ __('Customer (Pelanggan)') }}</option>
                                <option value="Supplier">{{ __('Supplier (Pemasok)') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">{{ __('No. Telepon / WA (Opsional)') }}</label>
                            <input type="text" name="phone" id="inputPhone" placeholder="08..." class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">{{ __('Email (Opsional)') }}</label>
                            <input type="email" name="email" id="inputEmail" placeholder="{{ __('email@contoh.com') }}" class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium transition-colors">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">{{ __('Alamat Lengkap') }}</label>
                            <textarea name="address" id="inputAddress" rows="3" placeholder="{{ __('Alamat jalan, kota, provinsi...') }}" class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-sm resize-none transition-colors"></textarea>
                        </div>
                    </div>
                </div>

                <div class="p-5 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3 transition-colors">
                    <button type="button" onclick="closeModal()" class="px-5 py-2.5 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg text-sm font-bold transition-colors">{{ __('Batal') }}</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-md transition-colors flex items-center gap-2">
                        <i data-feather="save" class="w-4 h-4"></i> {{ __('Simpan Kontak') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- PENGENDALI JAVASCRIPT & SWEETALERT2 -->
<script>
    // ==========================================
    // SWEETALERT2: PESAN GLOBAL (SESSION & VALIDASI)
    // ==========================================
    document.addEventListener("DOMContentLoaded", function() {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: {!! json_encode(__('Berhasil!')) !!},
                text: {!! json_encode(session('success')) !!},
                timer: 3500,
                timerProgressBar: true,
                showConfirmButton: false,
                position: 'center'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: {!! json_encode(__('Aksi Gagal!')) !!},
                text: {!! json_encode(session('error')) !!},
                timer: 5000,
                timerProgressBar: true,
                showConfirmButton: true,
                confirmButtonColor: '#ef4444'
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'warning',
                title: {!! json_encode(__('Validasi Gagal!')) !!},
                text: {!! json_encode($errors->first()) !!},
                showConfirmButton: true,
                confirmButtonColor: '#f59e0b'
            });
        @endif
    });

    // ==========================================
    // SWEETALERT2: KONFIRMASI HAPUS KONTAK
    // ==========================================
    function confirmDelete(event, form, contactName) {
        event.preventDefault();

        const confirmText = {!! json_encode(__('Yakin ingin menghapus :name dari Buku Kontak?')) !!}.replace(':name', `<b>"${contactName}"</b>`);
        const infoText = {!! json_encode(__('Jangan khawatir, transaksi lampau (SO/PO) atas nama kontak ini tidak akan ikut terhapus.')) !!};

        Swal.fire({
            title: {!! json_encode(__('Hapus Kontak?')) !!},
            html: `${confirmText}<br><br><span class="text-sm text-slate-500 font-medium">${infoText}</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i data-feather="trash-2" class="w-4 h-4 inline mr-1"></i> ' + {!! json_encode(__('Ya, Hapus')) !!},
            cancelButtonText: {!! json_encode(__('Batal')) !!},
            reverseButtons: true,
            didOpen: () => { if (typeof feather !== 'undefined') feather.replace(); }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: {!! json_encode(__('Menghapus Data...')) !!},
                    text: {!! json_encode(__('Mohon tunggu sebentar.')) !!},
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                form.submit();
            }
        });
    }

    // ==========================================
    // FUNGSI SWAL LOADING UNTUK FORM KONTAK
    // ==========================================
    document.getElementById('contactForm').addEventListener('submit', function() {
        Swal.fire({
            title: {!! json_encode(__('Menyimpan Data...')) !!},
            text: {!! json_encode(__('Sistem sedang menyimpan data kontak.')) !!},
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });
    });

    // ==========================================
    // LOGIKA MODAL FORM KONTAK DENGAN ANIMASI
    // ==========================================
    function openModal(mode, buttonEl = null) {
        const modal = document.getElementById('contactModal');
        const form = document.getElementById('contactForm');
        const content = document.getElementById('contactModalContent');

        if (mode === 'edit' && buttonEl) {
            const contact = JSON.parse(buttonEl.getAttribute('data-contact'));
            document.getElementById('modalTitle').innerText = {!! json_encode(__('Edit Kontak')) !!};
            document.getElementById('formMethod').value = "PUT";
            form.action = `/contacts/${contact.id}`;

            document.getElementById('inputName').value = contact.name;
            document.getElementById('inputType').value = contact.type;
            document.getElementById('inputPhone').value = contact.phone || '';
            document.getElementById('inputEmail').value = contact.email || '';
            document.getElementById('inputAddress').value = contact.address || '';
        } else {
            document.getElementById('modalTitle').innerText = {!! json_encode(__('Tambah Kontak Baru')) !!};
            document.getElementById('formMethod').value = "POST";
            form.action = "{{ route('contacts.store') }}";

            form.reset();
        }

        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    }

    function closeModal() {
        const modal = document.getElementById('contactModal');
        const content = document.getElementById('contactModalContent');

        content.classList.remove('scale-100');
        content.classList.add('scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 150);
    }
</script>
@endsection
