@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10 max-w-5xl mx-auto md:mx-0">

    <div class="flex items-center gap-4">
        <a href="{{ route('settings.index') }}" class="text-slate-400 hover:text-blue-600 transition-colors">
            <i data-feather="arrow-left" class="w-6 h-6"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="users" class="text-blue-600 w-7 h-7"></i> Daftar Pengguna
            </h1>
            <p class="text-slate-500 mt-1">Kelola akun staf yang terdaftar dan tentukan otoritas jabatannya.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-700 p-4 rounded-lg font-bold border border-emerald-200 flex items-center gap-2">
            <i data-feather="check-circle" class="w-5 h-5"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 text-rose-700 p-4 rounded-lg font-bold border border-rose-200 flex items-center gap-2">
            <i data-feather="alert-circle" class="w-5 h-5"></i> {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600">Nama Pengguna</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">Alamat Email (Login)</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">Jabatan / Role Aktif</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                        <td class="p-4 text-sm font-bold text-slate-800 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            {{ $user->name }}
                            @if(auth()->id() === $user->id)
                                <span class="ml-2 px-2 py-0.5 bg-slate-800 text-white text-[10px] rounded-full font-bold">ANDA</span>
                            @endif
                        </td>
                        <td class="p-4 text-sm text-slate-600 font-medium">{{ $user->email }}</td>
                        <td class="p-4 text-center">
                            <span class="px-3 py-1 rounded-full text-xs font-bold
                                {{ $user->role === 'Admin' || $user->role === 'Direktur' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-700' }}">
                                <i data-feather="shield" class="w-3 h-3 inline mr-1"></i> {{ $user->role ?? 'Staff' }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openRoleModal({{ json_encode($user) }})" class="text-blue-500 hover:bg-blue-100 p-2 rounded-lg transition-colors" title="Ubah Jabatan (Role)">
                                    <i data-feather="edit" class="w-4 h-4"></i>
                                </button>

                                @if(auth()->id() !== $user->id)
                                <form action="{{ route('settings.deleteUser', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('PERINGATAN: Yakin ingin menghapus permanen akun {{ $user->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:bg-red-100 p-2 rounded-lg transition-colors" title="Hapus Akun">
                                        <i data-feather="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                @else
                                <button type="button" class="text-slate-300 p-2 cursor-not-allowed" title="Anda tidak dapat menghapus diri sendiri">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Ubah Role Pengguna -->
<div id="roleModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden flex flex-col">
        <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-bold text-slate-800">Ubah Jabatan (Role)</h3>
            <button onclick="document.getElementById('roleModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700"><i data-feather="x"></i></button>
        </div>
        <form id="roleForm" method="POST" class="p-5 space-y-4">
            @csrf
            @method('PUT')

            <p class="text-sm font-medium text-slate-600 mb-2">Pilih jabatan baru untuk <b id="modalUserName" class="text-slate-800"></b>:</p>

            <div>
                <select name="role" id="modalUserRole" required class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500 font-bold text-slate-700">
                    @foreach($roles as $r)
                        <option value="{{ $r }}">{{ $r }}</option>
                    @endforeach
                </select>
            </div>

            <div class="pt-2 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('roleModal').classList.add('hidden')" class="px-4 py-2 text-slate-600 font-bold bg-slate-100 rounded-lg">Batal</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold shadow-sm">Terapkan Jabatan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRoleModal(user) {
        document.getElementById('modalUserName').innerText = user.name;
        document.getElementById('modalUserRole').value = user.role || 'Staff';
        document.getElementById('roleForm').action = `/settings/users/${user.id}`;
        document.getElementById('roleModal').classList.remove('hidden');
    }
</script>
@endsection
