<!-- resources/views/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ERP PT Berlian Mandiri Perkasa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen p-4 font-sans">

    <div class="bg-white max-w-md w-full rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-slate-900 p-8 text-center">
            <h1 class="text-2xl font-bold text-orange-400">PT Berlian Mandiri Perkasa</h1>
            <p class="text-slate-400 mt-2 text-sm">Sistem ERP Internal Management</p>
        </div>
        
        <div class="p-8">
            <h2 class="text-2xl font-bold text-slate-800 mb-6 text-center">Masuk ke Akun Anda</h2>
            
            <!-- Tampilkan Pesan Sukses (Contoh: Setelah Register) -->
            @if(session('success'))
                <div class="bg-green-50 text-green-700 p-3 rounded-lg text-sm mb-5 text-center font-medium border border-green-200">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Tampilkan Pesan Error Global -->
            @if($errors->any())
                <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm mb-5 text-center font-medium border border-red-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf <!-- Token CSRF Wajib di Laravel -->
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-feather="user" class="w-4 h-4"></i>
                        </div>
                        <input 
                            type="text" 
                            name="username" 
                            required
                            value="{{ old('username') }}"
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
                            placeholder="Ketik username Anda"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-feather="lock" class="w-4 h-4"></i>
                        </div>
                        <input 
                            type="password" 
                            name="password" 
                            required
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
                            placeholder="Ketik password Anda"
                        >
                    </div>
                </div>

                <div class="flex justify-between items-center text-sm">
                    <label class="flex items-center text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="mr-2 rounded text-blue-600 focus:ring-blue-500">
                        Ingat saya
                    </label>
                    <a href="#" class="text-blue-600 font-semibold hover:text-blue-700">Lupa Password?</a>
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl flex items-center justify-center gap-2 transition-colors mt-2 shadow-md"
                >
                    Login ke Sistem
                </button>
            </form>

            <div class="mt-8 text-center text-sm text-slate-600">
                Belum punya akun staf? 
                <a href="{{ route('register') }}" class="text-blue-600 font-bold hover:underline hover:text-blue-700 transition-colors">
                    Daftar di sini
                </a>
            </div>

        </div>
    </div>

    <script>feather.replace()</script>
</body>
</html>