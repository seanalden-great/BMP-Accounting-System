{{-- <!-- resources/views/profile/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">
    <div>
        <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
            <i data-feather="user" class="w-7 h-7 text-blue-600"></i> Pengaturan Profil
        </h1>
        <p class="text-slate-500 mt-1">Kelola data personal, akses, dan kata sandi akun Anda</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden max-w-4xl">
        <div class="flex items-center gap-4 p-6 border-b border-slate-200 bg-slate-50">
            <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-2xl uppercase shadow-md">
                {{ substr($user->full_name ?: ($user->name ?: $user->username), 0, 1) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-800">{{ $user->full_name ?: ($user->name ?: $user->username) }}</h2>
                <p class="text-sm font-semibold text-slate-500">{{ $user->role ?? 'Staff' }} / {{ $user->username }}</p>
            </div>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="p-6 md:p-8">
            @csrf
            @method('PUT')

            <!-- Bagian Data Personal -->
            <h3 class="text-lg font-bold text-slate-800 border-b border-slate-200 pb-2 mb-4">Informasi Personal</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Panggilan (Display)</label>
                    <input type="text" name="name" required value="{{ old('name', $user->name) }}" class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nomor Telepon / WA</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="08..." class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" placeholder="email@contoh.com" class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                </div>
            </div>

            <!-- Bagian Data Kredensial -->
            <h3 class="text-lg font-bold text-slate-800 border-b border-slate-200 pb-2 mb-4">Kredensial & Keamanan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Username (Untuk Login)</label>
                    <input type="text" name="username" required value="{{ old('username', $user->username) }}" class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-slate-50 font-bold">
                </div>
            </div>

            <!-- Bagian Preferensi Sistem & Notifikasi -->
            <h3 class="text-lg font-bold text-slate-800 border-b border-slate-200 pb-2 mb-4 mt-8">Preferensi Sistem</h3>

            <div class="bg-white border border-slate-200 p-5 rounded-lg mb-8 shadow-sm flex items-center justify-between hover:border-blue-300 transition-colors">
                <div>
                    <h4 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                        <i data-feather="mail" class="w-4 h-4 text-blue-600"></i> Terima Notifikasi Email
                    </h4>
                    <p class="text-xs text-slate-500 mt-1 max-w-lg">Izinkan sistem ERP mengirimkan pengingat (seperti tagihan jatuh tempo, butuh persetujuan, atau peringatan keamanan) langsung ke kotak masuk email Anda.</p>
                </div>

                <!-- Tombol Sakelar (Toggle Switch) -->
                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                    <input type="checkbox" name="email_notifications" value="1" class="sr-only peer" {{ (isset($user->email_notifications) ? $user->email_notifications : 1) ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg mb-6">
                <p class="text-sm text-yellow-800 font-semibold mb-3 flex items-center gap-2">
                    <i data-feather="alert-circle" class="w-4 h-4"></i> Ganti Kata Sandi (Kosongkan jika tidak ingin mengubah sandi)
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <input type="password" name="password" placeholder="Kata Sandi Baru (Min. 6 Karakter)" class="w-full border border-yellow-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-yellow-500 transition-all text-sm">
                    </div>
                    <div>
                        <input type="password" name="password_confirmation" placeholder="Ulangi Kata Sandi Baru" class="w-full border border-yellow-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-yellow-500 transition-all text-sm">
                    </div>
                </div>
            </div>

            <!-- Validasi Error Form Lanjutan -->
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <ul class="list-disc list-inside text-sm font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- ============================================== -->
            <!-- FITUR BARU: RIWAYAT AKTIVITAS LOGIN TERAKHIR   -->
            <!-- ============================================== -->
            <h3 class="text-lg font-bold text-slate-800 border-b border-slate-200 pb-2 mb-4 mt-10 flex items-center gap-2">
                <i data-feather="activity" class="w-5 h-5 text-slate-500"></i> Aktivitas Login Anda Terakhir
            </h3>

            <div class="bg-white border border-slate-200 rounded-lg overflow-hidden mb-4 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Waktu & Tanggal</th>
                                <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Alamat IP (Jaringan)</th>
                                <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Perangkat & Browser</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($loginLogs as $log)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="p-4 text-sm font-semibold text-slate-700 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('d M Y, H:i') }} WIB
                                </td>
                                <td class="p-4 text-sm font-mono text-slate-600 bg-slate-50/50">
                                    {{ $log->ip_address }}
                                </td>
                                <td class="p-4 text-xs text-slate-500 max-w-xs" title="{{ $log->user_agent }}">
                                    <div class="flex items-center gap-2">
                                        <!-- Cek apakah login dari HP atau PC -->
                                        @if(str_contains(strtolower($log->user_agent), 'mobile') || str_contains(strtolower($log->user_agent), 'android') || str_contains(strtolower($log->user_agent), 'iphone'))
                                            <i data-feather="smartphone" class="w-4 h-4 text-blue-500 shrink-0"></i>
                                        @else
                                            <i data-feather="monitor" class="w-4 h-4 text-slate-400 shrink-0"></i>
                                        @endif
                                        <span class="truncate">{{ $log->user_agent ?: 'Perangkat Tidak Dikenal' }}</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="p-6 text-center text-sm text-slate-500 font-medium bg-slate-50/50">
                                    Mulai sesi berikutnya, aktivitas masuk Anda akan terekam di sini demi keamanan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <p class="text-xs text-slate-400 italic mb-8 flex gap-1.5 items-start">
                <i data-feather="info" class="w-3.5 h-3.5 mt-0.5 shrink-0 text-blue-500"></i>
                <span>Sistem secara otomatis mendeteksi jaringan dan perangkat. Jika Anda melihat Alamat IP atau Perangkat asing yang mencurigakan di tabel ini, segera ganti kata sandi Anda pada kolom di atas!</span>
            </p>

            <div class="flex justify-end border-t border-slate-100 pt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-bold shadow-lg shadow-blue-600/30 transition-all flex items-center gap-2">
                    <i data-feather="save" class="w-5 h-5"></i> Simpan Perubahan Profil
                </button>
            </div>
        </form>
    </div>
</div>
@endsection --}}

@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">
    <div>
        <h1 class="text-3xl font-bold text-slate-800 dark:text-white flex items-center gap-3 transition-colors">
            <i data-feather="user" class="w-7 h-7 text-blue-600 dark:text-blue-400"></i> Pengaturan Profil
        </h1>
        <p class="text-slate-500 dark:text-slate-400 mt-1 transition-colors">Kelola data personal, akses, dan kata sandi akun Anda</p>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden max-w-4xl transition-colors">
        <div class="flex items-center gap-4 p-6 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 transition-colors">
            <div class="w-16 h-16 bg-blue-600 dark:bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-2xl uppercase shadow-md transition-colors">
                {{ substr($user->full_name ?: ($user->name ?: $user->username), 0, 1) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white transition-colors">{{ $user->full_name ?: ($user->name ?: $user->username) }}</h2>
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 transition-colors">{{ $user->role ?? 'Staff' }} / {{ $user->username }}</p>
            </div>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="p-6 md:p-8">
            @csrf
            @method('PUT')

            <!-- Bagian Data Personal -->
            <h3 class="text-lg font-bold text-slate-800 dark:text-white border-b border-slate-200 dark:border-slate-700 pb-2 mb-4 transition-colors">Informasi Personal</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 transition-colors">Nama Lengkap</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 transition-colors">Nama Panggilan (Display)</label>
                    <input type="text" name="name" required value="{{ old('name', $user->name) }}" class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 transition-colors">Nomor Telepon / WA</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="08..." class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 transition-colors">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" placeholder="email@contoh.com" class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                </div>
            </div>

            <!-- Bagian Data Kredensial -->
            <h3 class="text-lg font-bold text-slate-800 dark:text-white border-b border-slate-200 dark:border-slate-700 pb-2 mb-4 transition-colors">Kredensial & Keamanan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 transition-colors">Username (Untuk Login)</label>
                    <input type="text" name="username" required value="{{ old('username', $user->username) }}" class="w-full border border-slate-300 dark:border-slate-600 rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-white font-bold">
                </div>
            </div>

            <!-- Bagian Preferensi Sistem & Notifikasi -->
            <h3 class="text-lg font-bold text-slate-800 dark:text-white border-b border-slate-200 dark:border-slate-700 pb-2 mb-4 mt-8 transition-colors">Preferensi Sistem</h3>

            <div class="bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 p-5 rounded-lg mb-8 shadow-sm flex items-center justify-between hover:border-blue-300 dark:hover:border-blue-500 transition-colors">
                <div>
                    <h4 class="font-bold text-slate-800 dark:text-white text-sm flex items-center gap-2 transition-colors">
                        <i data-feather="mail" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i> Terima Notifikasi Email
                    </h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-lg transition-colors">Izinkan sistem ERP mengirimkan pengingat (seperti tagihan jatuh tempo, butuh persetujuan, atau peringatan keamanan) langsung ke kotak masuk email Anda.</p>
                </div>

                <!-- Tombol Sakelar (Toggle Switch) -->
                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                    <input type="checkbox" name="email_notifications" value="1" class="sr-only peer" {{ (isset($user->email_notifications) ? $user->email_notifications : 1) ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-slate-200 dark:bg-slate-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 dark:peer-checked:bg-blue-500"></div>
                </label>
            </div>

            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-900/30 p-4 rounded-lg mb-6 transition-colors">
                <p class="text-sm text-yellow-800 dark:text-yellow-400 font-semibold mb-3 flex items-center gap-2 transition-colors">
                    <i data-feather="alert-circle" class="w-4 h-4"></i> Ganti Kata Sandi (Kosongkan jika tidak ingin mengubah sandi)
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <input type="password" name="password" placeholder="Kata Sandi Baru (Min. 6 Karakter)" class="w-full border border-yellow-300 dark:border-yellow-700/50 bg-white dark:bg-slate-800/50 text-slate-800 dark:text-white rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-yellow-500 transition-all text-sm">
                    </div>
                    <div>
                        <input type="password" name="password_confirmation" placeholder="Ulangi Kata Sandi Baru" class="w-full border border-yellow-300 dark:border-yellow-700/50 bg-white dark:bg-slate-800/50 text-slate-800 dark:text-white rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-yellow-500 transition-all text-sm">
                    </div>
                </div>
            </div>

            <!-- Validasi Error Form Lanjutan -->
            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-900/30 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg mb-6 transition-colors">
                    <ul class="list-disc list-inside text-sm font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- ============================================== -->
            <!-- FITUR BARU: RIWAYAT AKTIVITAS LOGIN TERAKHIR   -->
            <!-- ============================================== -->
            <h3 class="text-lg font-bold text-slate-800 dark:text-white border-b border-slate-200 dark:border-slate-700 pb-2 mb-4 mt-10 flex items-center gap-2 transition-colors">
                <i data-feather="activity" class="w-5 h-5 text-slate-500 dark:text-slate-400"></i> Aktivitas Login Anda Terakhir
            </h3>

            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden mb-4 shadow-sm transition-colors">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-700 transition-colors">
                            <tr>
                                <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Waktu & Tanggal</th>
                                <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Alamat IP (Jaringan)</th>
                                <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Perangkat & Browser</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 transition-colors">
                            @forelse($loginLogs as $log)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <td class="p-4 text-sm font-semibold text-slate-700 dark:text-slate-200 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('d M Y, H:i') }} WIB
                                </td>
                                <td class="p-4 text-sm font-mono text-slate-600 dark:text-slate-300 bg-slate-50/50 dark:bg-slate-700/30 transition-colors">
                                    {{ $log->ip_address }}
                                </td>
                                <td class="p-4 text-xs text-slate-500 dark:text-slate-400 max-w-xs" title="{{ $log->user_agent }}">
                                    <div class="flex items-center gap-2">
                                        <!-- Cek apakah login dari HP atau PC -->
                                        @if(str_contains(strtolower($log->user_agent), 'mobile') || str_contains(strtolower($log->user_agent), 'android') || str_contains(strtolower($log->user_agent), 'iphone'))
                                            <i data-feather="smartphone" class="w-4 h-4 text-blue-500 dark:text-blue-400 shrink-0"></i>
                                        @else
                                            <i data-feather="monitor" class="w-4 h-4 text-slate-400 dark:text-slate-500 shrink-0"></i>
                                        @endif
                                        <span class="truncate">{{ $log->user_agent ?: 'Perangkat Tidak Dikenal' }}</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="p-6 text-center text-sm text-slate-500 dark:text-slate-400 font-medium bg-slate-50/50 dark:bg-slate-800/50 transition-colors">
                                    Mulai sesi berikutnya, aktivitas masuk Anda akan terekam di sini demi keamanan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <p class="text-xs text-slate-400 dark:text-slate-500 italic mb-8 flex gap-1.5 items-start transition-colors">
                <i data-feather="info" class="w-3.5 h-3.5 mt-0.5 shrink-0 text-blue-500 dark:text-blue-400"></i>
                <span>Sistem secara otomatis mendeteksi jaringan dan perangkat. Jika Anda melihat Alamat IP atau Perangkat asing yang mencurigakan di tabel ini, segera ganti kata sandi Anda pada kolom di atas!</span>
            </p>

            <div class="flex justify-end border-t border-slate-100 dark:border-slate-700 pt-6 transition-colors">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white px-8 py-3 rounded-lg font-bold shadow-lg shadow-blue-600/30 dark:shadow-none transition-all flex items-center gap-2">
                    <i data-feather="save" class="w-5 h-5"></i> Simpan Perubahan Profil
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
