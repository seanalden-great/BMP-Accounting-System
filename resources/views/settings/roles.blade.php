<!-- resources/views/settings/roles.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10 max-w-5xl mx-auto md:mx-0">

    <div class="flex items-center gap-4">
        <a href="{{ route('settings.index') }}" class="text-slate-400 hover:text-blue-600 transition-colors">
            <i data-feather="arrow-left" class="w-6 h-6"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="shield" class="text-indigo-600 w-7 h-7"></i> Matriks Hak Akses
            </h1>
            <p class="text-slate-500 mt-1">Tentukan menu mana saja yang boleh dilihat oleh masing-masing jabatan.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 rounded-lg font-bold border border-green-200">
            <i data-feather="check-circle" class="w-5 h-5 inline mr-1"></i> {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('settings.updateRoles') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-800 text-white">
                        <tr>
                            <th class="p-4 text-sm font-bold uppercase w-1/4 tracking-wider">Modul / Menu ERP</th>
                            @foreach($roles as $role)
                                <th class="p-4 text-sm font-bold text-center border-l border-slate-700 w-32 tracking-wider">{{ $role }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    {{-- <tbody class="divide-y divide-slate-100">
                        @foreach($menus as $menu)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-4 text-sm font-bold text-slate-700 flex items-center gap-2">
                                <i data-feather="grid" class="w-4 h-4 text-slate-400"></i> {{ $menu }}
                            </td>
                            @foreach($roles as $role)
                                @php
                                    // Cek apakah menu ini tercentang untuk role ini di database
                                    $isChecked = isset($permissions[$role]) && in_array($menu, $permissions[$role]);
                                @endphp
                                <td class="p-4 text-center border-l border-slate-100">
                                    <label class="inline-flex items-center justify-center cursor-pointer p-2 rounded hover:bg-slate-200 transition-colors">
                                        <input type="checkbox" name="permissions[{{ $role }}][]" value="{{ $menu }}"
                                            class="w-5 h-5 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500 cursor-pointer"
                                            {{ $isChecked ? 'checked' : '' }}>
                                    </label>
                                </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody> --}}
                    <tbody class="divide-y divide-slate-100">
                        @foreach($menus as $menu)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-4 text-sm font-bold text-slate-700 flex items-center gap-2">
                                <i data-feather="grid" class="w-4 h-4 text-slate-400"></i> {{ $menu }}
                            </td>
                            @foreach($roles as $role)
                                @php
                                    $rolePerms = $permissions[$role] ?? [];

                                    // Deteksi Format Lama (Jika masih pakai array 1D ['Menu A', 'Menu B']), ubah visualnya ke CRUD penuh
                                    $isOldFormat = !empty($rolePerms) && isset($rolePerms[0]) && is_string($rolePerms[0]);
                                    if ($isOldFormat) {
                                        $newFormat = [];
                                        foreach($rolePerms as $oldMenu) { $newFormat[$oldMenu] = ['C','R','U','D']; }
                                        $rolePerms = $newFormat;
                                    }

                                    $menuPerms = $rolePerms[$menu] ?? [];
                                    $canC = in_array('C', $menuPerms);
                                    $canR = in_array('R', $menuPerms);
                                    $canU = in_array('U', $menuPerms);
                                    $canD = in_array('D', $menuPerms);
                                @endphp
                                <td class="p-3 text-center border-l border-slate-100">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- C: CREATE -->
                                        <label class="cursor-pointer" title="Create (Tambah Data)">
                                            <input type="checkbox" name="permissions[{{ $role }}][{{ $menu }}][]" value="C" {{ $canC ? 'checked' : '' }} class="peer sr-only">
                                            <div class="w-6 h-6 rounded flex items-center justify-center text-xs font-black text-slate-400 bg-slate-100 border border-slate-200 peer-checked:bg-emerald-100 peer-checked:text-emerald-700 peer-checked:border-emerald-300 transition-all">C</div>
                                        </label>
                                        <!-- R: READ -->
                                        <label class="cursor-pointer" title="Read (Lihat/Buka Menu)">
                                            <input type="checkbox" name="permissions[{{ $role }}][{{ $menu }}][]" value="R" {{ $canR ? 'checked' : '' }} class="peer sr-only">
                                            <div class="w-6 h-6 rounded flex items-center justify-center text-xs font-black text-slate-400 bg-slate-100 border border-slate-200 peer-checked:bg-blue-100 peer-checked:text-blue-700 peer-checked:border-blue-300 transition-all">R</div>
                                        </label>
                                        <!-- U: UPDATE -->
                                        <label class="cursor-pointer" title="Update (Ubah Data)">
                                            <input type="checkbox" name="permissions[{{ $role }}][{{ $menu }}][]" value="U" {{ $canU ? 'checked' : '' }} class="peer sr-only">
                                            <div class="w-6 h-6 rounded flex items-center justify-center text-xs font-black text-slate-400 bg-slate-100 border border-slate-200 peer-checked:bg-orange-100 peer-checked:text-orange-700 peer-checked:border-orange-300 transition-all">U</div>
                                        </label>
                                        <!-- D: DELETE -->
                                        <label class="cursor-pointer" title="Delete (Hapus Data)">
                                            <input type="checkbox" name="permissions[{{ $role }}][{{ $menu }}][]" value="D" {{ $canD ? 'checked' : '' }} class="peer sr-only">
                                            <div class="w-6 h-6 rounded flex items-center justify-center text-xs font-black text-slate-400 bg-slate-100 border border-slate-200 peer-checked:bg-red-100 peer-checked:text-red-700 peer-checked:border-red-300 transition-all">D</div>
                                        </label>
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-5 border-t border-slate-200 bg-slate-50 flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-2.5 rounded-lg font-bold flex items-center gap-2 transition-colors shadow-sm">
                    <i data-feather="save" class="w-4 h-4"></i> Terapkan & Simpan Akses
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
