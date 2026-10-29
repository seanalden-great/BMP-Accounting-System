<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT Berlianmandiri Perkasa - ERP</title>
    <!-- Tailwind CSS (Gunakan CDN untuk tes cepat, ganti ke Vite saat produksi) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #334155; border-radius: 20px; }
    </style>
</head>
<body class="bg-slate-100 flex min-h-screen font-sans antialiased text-slate-800">

    <!-- SIDEBAR -->
    <aside class="w-[260px] bg-slate-900 text-white border-r border-slate-800 h-screen sticky top-0 flex flex-col shrink-0">
        <!-- Logo/Header Sidebar -->
        <div class="h-16 flex items-center px-4 border-b border-slate-800 shrink-0">
            <h1 class="text-sm font-bold text-orange-400 leading-tight">PT Berlianmandiri Perkasa</h1>
        </div>
        
        <!-- Menu Navigasi -->
        <nav class="flex-1 overflow-y-auto px-3 space-y-1 custom-scrollbar pb-4 pt-4">
            
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-blue-400 font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-feather="home" class="w-4 h-4"></i> <span class="text-sm">Beranda</span>
            </a>

            <!-- Menu dengan Sub (Contoh Penjualan) -->
            {{-- <div class="space-y-1">
                <button onclick="document.getElementById('sub-sales').classList.toggle('hidden')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                    <div class="flex items-center gap-3">
                        <i data-feather="shopping-bag" class="w-4 h-4"></i> <span class="text-sm">Penjualan</span>
                    </div>
                    <i data-feather="chevron-down" class="w-3 h-3 text-slate-400"></i>
                </button>
                <div id="sub-sales" class="hidden ml-4 pl-4 border-l border-slate-700 space-y-1 mb-2">
                    <a href="{{ route('sales.index') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Semua Penjualan</a>
                </div>
            </div> --}}

            <!-- Menu dengan Sub (Contoh Pembelian) -->
            {{-- <div class="space-y-1">
                <button onclick="document.getElementById('sub-purchases').classList.toggle('hidden')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                    <div class="flex items-center gap-3">
                        <i data-feather="shopping-cart" class="w-4 h-4"></i> <span class="text-sm">Pembelian</span>
                    </div>
                    <i data-feather="chevron-down" class="w-3 h-3 text-slate-400"></i>
                </button>
                <div id="sub-purchases" class="hidden ml-4 pl-4 border-l border-slate-700 space-y-1 mb-2">
                    <a href="{{ route('purchases.index') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Semua Pembelian</a>
                </div>
            </div> --}}

            <!-- Menu dengan Sub (Penjualan) -->
            <div class="space-y-1">
                <button onclick="document.getElementById('sub-sales').classList.toggle('hidden')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                    <div class="flex items-center gap-3">
                        <i data-feather="shopping-bag" class="w-4 h-4"></i> <span class="text-sm">Penjualan</span>
                    </div>
                    <i data-feather="chevron-down" class="w-3 h-3 text-slate-400"></i>
                </button>
                <div id="sub-sales" class="hidden ml-4 pl-4 border-l border-slate-700 space-y-1 mb-2">
                    <a href="{{ route('sales.index') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Pesanan Penjualan</a>
                    <a href="{{ route('sales.deliveries') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Pengiriman Penjualan</a>
                    <a href="{{ route('sales.invoices') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Tagihan Penjualan</a>
                </div>
            </div>

            <!-- Menu dengan Sub (Pembelian) -->
            <div class="space-y-1">
                <button onclick="document.getElementById('sub-purchases').classList.toggle('hidden')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                    <div class="flex items-center gap-3">
                        <i data-feather="shopping-cart" class="w-4 h-4"></i> <span class="text-sm">Pembelian</span>
                    </div>
                    <i data-feather="chevron-down" class="w-3 h-3 text-slate-400"></i>
                </button>
                <div id="sub-purchases" class="hidden ml-4 pl-4 border-l border-slate-700 space-y-1 mb-2">
                    <a href="{{ route('purchases.index') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Pesanan Pembelian</a>
                    <a href="{{ route('purchases.deliveries') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Penerimaan Pembelian</a>
                    <a href="{{ route('purchases.bills') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Tagihan Pembelian</a>
                </div>
            </div>

            <a href="{{ route('expenses.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="receipt" class="w-4 h-4"></i> <span class="text-sm">Biaya</span>
            </a>
            <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="package" class="w-4 h-4"></i> <span class="text-sm">Produk</span>
            </a>
            <a href="{{ route('inventory.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="archive" class="w-4 h-4"></i> <span class="text-sm">Inventori</span>
            </a>
            <a href="{{ route('fixed-assets.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="briefcase" class="w-4 h-4"></i> <span class="text-sm">Aset Tetap</span>
            </a>
            <a href="{{ route('journals.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="dollar-sign" class="w-4 h-4"></i> <span class="text-sm">Kas & Bank</span>
            </a>
            <a href="{{ route('accounts.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="book" class="w-4 h-4"></i> <span class="text-sm">Akun CoA</span>
            </a>
            <a href="{{ route('contacts.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="users" class="w-4 h-4"></i> <span class="text-sm">Kontak</span>
            </a>
            {{-- <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="trending-up" class="w-4 h-4"></i> <span class="text-sm">Laporan Keuangan</span>
            </a> --}}
            <!-- Menu dengan Sub (Laporan Keuangan) -->
            <div class="space-y-1">
                <button onclick="document.getElementById('sub-reports').classList.toggle('hidden')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                    <div class="flex items-center gap-3">
                        <i data-feather="trending-up" class="w-4 h-4"></i> <span class="text-sm">Laporan Keuangan</span>
                    </div>
                    <i data-feather="chevron-down" class="w-3 h-3 text-slate-400"></i>
                </button>
                <div id="sub-reports" class="hidden ml-4 pl-4 border-l border-slate-700 space-y-1 mb-2">
                    <!-- Lempar query parameter '?type=profit-loss' ke URL -->
                    <a href="{{ route('reports.index', ['type' => 'profit-loss']) }}" class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Laba Rugi</a>
                    <!-- Lempar query parameter '?type=balance-sheet' ke URL -->
                    <a href="{{ route('reports.index', ['type' => 'balance-sheet']) }}" class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Neraca</a>
                </div>
            </div>
            <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="settings" class="w-4 h-4"></i> <span class="text-sm">Pengaturan</span>
            </a>
            <a href="{{ route('faqs.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="help-circle" class="w-4 h-4"></i> <span class="text-sm">FAQ</span>
            </a>
        </nav>

        <!-- Profil & Logout -->
        <div class="border-t border-slate-800 p-4 shrink-0 flex items-center justify-between bg-slate-900">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                    {{ substr(Auth::user()->full_name ?? 'U', 0, 1) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-sm font-bold text-white truncate">{{ Auth::user()->full_name ?? 'Admin' }}</p>
                    <p class="text-[10px] text-slate-400 font-medium truncate px-1.5 py-0.5 bg-slate-800 rounded mt-0.5 inline-block">
                        Role: {{ Auth::user()->role ?? 'Staff' }}
                    </p>
                </div>
            </div>
            <!-- Form Logout -->
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" title="Keluar" class="text-red-400 hover:text-red-500 hover:bg-red-400/10 p-2 rounded-lg transition-colors">
                    <i data-feather="log-out" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- KONTEN UTAMA -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- HEADER -->
        <header class="bg-white h-16 border-b border-slate-200 flex items-center justify-between px-4 sticky top-0 z-10 shrink-0">
            <div></div>
            <div class="flex items-center gap-4">
                <span class="text-xs font-bold text-orange-400 hidden md:block">
                    PT BERLIANMANDIRI PERKASA
                </span>
                <div class="flex items-center gap-3 text-slate-400 border-l border-slate-200 pl-4">
                    <button class="hover:text-blue-600 relative">
                        <i data-feather="bell" class="w-5 h-5"></i>
                        <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
                    </button>
                    <button class="bg-slate-100 p-1.5 rounded-full text-slate-500 hover:bg-slate-200">
                        <i data-feather="user" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
        </header>

        <!-- AREA RENDER KONTEN (Outlet) -->
        <main class="flex-1 p-6 overflow-y-auto">
            <!-- Menampilkan Flash Message Global -->
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Konten Halaman Spesifik Akan Masuk ke Sini -->
            @yield('content')
        </main>
    </div>

    <!-- Inisialisasi Ikon -->
    <script>
      feather.replace()
    </script>
</body>
</html>