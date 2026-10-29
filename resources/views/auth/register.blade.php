<!-- resources/views/auth/register.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ERP PT Berlian Mandiri Perkasa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen p-4 font-sans py-10">

    <div class="bg-white max-w-md w-full rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-slate-900 p-8 text-center">
            <h1 class="text-2xl font-bold text-orange-400">PT Berlian Mandiri Perkasa</h1>
            <p class="text-slate-400 mt-2 text-sm">Pendaftaran Akun Baru</p>
        </div>
        
        <div class="p-8">
            <h2 class="text-xl font-bold text-slate-800 mb-6 text-center">Lengkapi Data Anda</h2>
            
            @if($errors->any())
                <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm mb-5 text-center font-medium border border-red-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf
                
                <!-- Field Nama Lengkap -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-feather="credit-card" class="w-4 h-4"></i>
                        </div>
                        <input 
                            type="text" 
                            name="full_name" 
                            required
                            value="{{ old('full_name') }}"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
                            placeholder="Contoh: Budi Santoso"
                        >
                    </div>
                </div>

                <!-- Field Username -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Username Login</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-feather="user" class="w-4 h-4"></i>
                        </div>
                        <input 
                            type="text" 
                            name="username" 
                            required
                            value="{{ old('username') }}"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
                            placeholder="Ketik username unik"
                        >
                    </div>
                </div>

                <!-- Field Password -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-feather="lock" class="w-4 h-4"></i>
                        </div>
                        <input 
                            type="password" 
                            name="password" 
                            required 
                            minlength="6"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
                            placeholder="Minimal 6 karakter"
                        >
                    </div>
                </div>

                <!-- Field Hak Akses / Role -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Jabatan / Hak Akses</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-feather="briefcase" class="w-4 h-4"></i>
                        </div>
                        <select 
                            name="role"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all appearance-none"
                        >
                            <option value="Staff" {{ old('role') == 'Staff' ? 'selected' : '' }}>Staff (Akses Standar)</option>
                            <option value="Manager" {{ old('role') == 'Manager' ? 'selected' : '' }}>Manager (Persetujuan Data)</option>
                            <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin (Akses Penuh)</option>
                        </select>
                    </div>
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl flex items-center justify-center gap-2 transition-colors mt-4 shadow-md"
                >
                    Daftarkan Akun
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-slate-600">
                Sudah memiliki akun? 
                <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline hover:text-blue-700 transition-colors">
                    Login di sini
                </a>
            </div>

        </div>
    </div>

    <script>feather.replace()</script>
</body>
</html>