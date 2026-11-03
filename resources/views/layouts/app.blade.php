{{-- <!-- resources/views/layouts/app.blade.php -->
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #334155;
            border-radius: 20px;
        }
    </style>
</head>

<body class="bg-slate-100 flex min-h-screen font-sans antialiased text-slate-800">

    <!-- SIDEBAR -->
    <aside
        class="w-[260px] bg-slate-900 text-white border-r border-slate-800 h-screen sticky top-0 flex flex-col shrink-0">
        <!-- Logo/Header Sidebar -->
        <div class="h-16 flex items-center px-4 border-b border-slate-800 shrink-0">
            <h1 class="text-sm font-bold text-orange-400 leading-tight">PT Berlianmandiri Perkasa</h1>
        </div>

        <!-- Menu Navigasi -->
        <nav class="flex-1 overflow-y-auto px-3 space-y-1 custom-scrollbar pb-4 pt-4">

            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-blue-400 font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-feather="home" class="w-4 h-4"></i> <span class="text-sm">Beranda</span>
            </a>

            <!-- Menu dengan Sub (Penjualan) -->
            <div class="space-y-1">
                <button onclick="document.getElementById('sub-sales').classList.toggle('hidden')"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                    <div class="flex items-center gap-3">
                        <i data-feather="shopping-bag" class="w-4 h-4"></i> <span class="text-sm">Penjualan</span>
                    </div>
                    <i data-feather="chevron-down" class="w-3 h-3 text-slate-400"></i>
                </button>
                <div id="sub-sales" class="hidden ml-4 pl-4 border-l border-slate-700 space-y-1 mb-2">
                    <a href="{{ route('sales.index') }}"
                        class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Pesanan
                        Penjualan</a>
                    <a href="{{ route('sales.deliveries') }}"
                        class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Pengiriman
                        Penjualan</a>
                    <a href="{{ route('sales.invoices') }}"
                        class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Tagihan
                        Penjualan</a>
                </div>
            </div>

            <!-- Menu dengan Sub (Pembelian) -->
            <div class="space-y-1">
                <button onclick="document.getElementById('sub-purchases').classList.toggle('hidden')"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                    <div class="flex items-center gap-3">
                        <i data-feather="shopping-cart" class="w-4 h-4"></i> <span class="text-sm">Pembelian</span>
                    </div>
                    <i data-feather="chevron-down" class="w-3 h-3 text-slate-400"></i>
                </button>
                <div id="sub-purchases" class="hidden ml-4 pl-4 border-l border-slate-700 space-y-1 mb-2">
                    <a href="{{ route('purchases.index') }}"
                        class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Pesanan
                        Pembelian</a>
                    <a href="{{ route('purchases.deliveries') }}"
                        class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Penerimaan
                        Pembelian</a>
                    <a href="{{ route('purchases.bills') }}"
                        class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Tagihan
                        Pembelian</a>
                </div>
            </div>

            <a href="{{ route('expenses.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="file-text" class="w-4 h-4"></i> <span class="text-sm">Biaya</span>
            </a>
            <a href="{{ route('products.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="package" class="w-4 h-4"></i> <span class="text-sm">Produk</span>
            </a>
            <a href="{{ route('inventory.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="archive" class="w-4 h-4"></i> <span class="text-sm">Inventori</span>
            </a>
            <a href="{{ route('fixed-assets.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="briefcase" class="w-4 h-4"></i> <span class="text-sm">Aset Tetap</span>
            </a>
            <a href="{{ route('journals.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="dollar-sign" class="w-4 h-4"></i> <span class="text-sm">Kas & Bank</span>
            </a>
            <a href="{{ route('accounts.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="book" class="w-4 h-4"></i> <span class="text-sm">Akun CoA</span>
            </a>
            <a href="{{ route('contacts.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="users" class="w-4 h-4"></i> <span class="text-sm">Kontak</span>
            </a>
            <!-- Menu dengan Sub (Laporan Keuangan) -->
            <div class="space-y-1">
                <button onclick="document.getElementById('sub-reports').classList.toggle('hidden')"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                    <div class="flex items-center gap-3">
                        <i data-feather="trending-up" class="w-4 h-4"></i> <span class="text-sm">Laporan Keuangan</span>
                    </div>
                    <i data-feather="chevron-down" class="w-3 h-3 text-slate-400"></i>
                </button>
                <div id="sub-reports" class="hidden ml-4 pl-4 border-l border-slate-700 space-y-1 mb-2">
                    <!-- Lempar query parameter '?type=profit-loss' ke URL -->
                    <a href="{{ route('reports.index', ['type' => 'profit-loss']) }}"
                        class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Laba
                        Rugi</a>
                    <!-- Lempar query parameter '?type=balance-sheet' ke URL -->
                    <a href="{{ route('reports.index', ['type' => 'balance-sheet']) }}"
                        class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Neraca</a>
                </div>
            </div>
            <a href="{{ route('settings.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="settings" class="w-4 h-4"></i> <span class="text-sm">Pengaturan</span>
            </a>
            <a href="{{ route('faqs.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="help-circle" class="w-4 h-4"></i> <span class="text-sm">FAQ</span>
            </a>
        </nav>

        <!-- Profil & Logout -->
        @php
        $displayName = Auth::user()->full_name ?: (Auth::user()->name ?: (Auth::user()->username ?? 'Admin'));
        @endphp
        <div class="border-t border-slate-800 p-4 shrink-0 flex items-center justify-between bg-slate-900">
            <div class="flex items-center gap-3 overflow-hidden">
                <div
                    class="w-9 h-9 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm shrink-0 uppercase">
                    {{ substr($displayName, 0, 1) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-sm font-bold text-white truncate">{{ $displayName }}</p>
                    <p
                        class="text-[10px] text-slate-400 font-medium truncate px-1.5 py-0.5 bg-slate-800 rounded mt-0.5 inline-block">
                        Role: {{ Auth::user()->role ?? 'Staff' }}
                    </p>
                </div>
            </div>
            <!-- Form Logout -->
            <form action="{{ route('logout') }}" method="POST" class="shrink-0">
                @csrf
                <button type="submit" title="Keluar"
                    class="text-red-400 hover:text-red-500 hover:bg-red-400/10 p-2 rounded-lg transition-colors">
                    <i data-feather="log-out" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- KONTEN UTAMA -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- HEADER -->
        <header
            class="bg-white h-16 border-b border-slate-200 flex items-center justify-between px-4 sticky top-0 z-10 shrink-0">
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

</html> --}}

{{-- <!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT Berlianmandiri Perkasa - ERP</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #334155;
            border-radius: 20px;
        }
    </style>
</head>

<body class="bg-slate-100 flex min-h-screen font-sans antialiased text-slate-800">

    <!-- SIDEBAR -->
    <aside
        class="w-[260px] bg-slate-900 text-white border-r border-slate-800 h-screen sticky top-0 flex flex-col shrink-0">
        <!-- Logo/Header Sidebar -->
        <div class="h-16 flex items-center px-4 border-b border-slate-800 shrink-0">
            <h1 class="text-sm font-bold text-orange-400 leading-tight">PT Berlianmandiri Perkasa</h1>
        </div>

        <!-- Menu Navigasi -->
        <nav class="flex-1 overflow-y-auto px-3 space-y-1 custom-scrollbar pb-4 pt-4">

            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-blue-400 font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-feather="home" class="w-4 h-4"></i> <span class="text-sm">Beranda</span>
            </a>

            <!-- Menu dengan Sub (Penjualan) -->
            <div class="space-y-1">
                <button onclick="document.getElementById('sub-sales').classList.toggle('hidden')"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                    <div class="flex items-center gap-3">
                        <i data-feather="shopping-bag" class="w-4 h-4"></i> <span class="text-sm">Penjualan</span>
                    </div>
                    <i data-feather="chevron-down" class="w-3 h-3 text-slate-400"></i>
                </button>
                <div id="sub-sales" class="hidden ml-4 pl-4 border-l border-slate-700 space-y-1 mb-2">
                    <a href="{{ route('sales.index') }}"
                        class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Pesanan
                        Penjualan</a>
                    <a href="{{ route('sales.deliveries') }}"
                        class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Pengiriman
                        Penjualan</a>
                    <a href="{{ route('sales.invoices') }}"
                        class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Tagihan
                        Penjualan</a>
                </div>
            </div>

            <!-- Menu dengan Sub (Pembelian) -->
            <div class="space-y-1">
                <button onclick="document.getElementById('sub-purchases').classList.toggle('hidden')"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                    <div class="flex items-center gap-3">
                        <i data-feather="shopping-cart" class="w-4 h-4"></i> <span class="text-sm">Pembelian</span>
                    </div>
                    <i data-feather="chevron-down" class="w-3 h-3 text-slate-400"></i>
                </button>
                <div id="sub-purchases" class="hidden ml-4 pl-4 border-l border-slate-700 space-y-1 mb-2">
                    <a href="{{ route('purchases.index') }}"
                        class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Pesanan
                        Pembelian</a>
                    <a href="{{ route('purchases.deliveries') }}"
                        class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Penerimaan
                        Pembelian</a>
                    <a href="{{ route('purchases.bills') }}"
                        class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Tagihan
                        Pembelian</a>
                </div>
            </div>

            <a href="{{ route('expenses.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="file-text" class="w-4 h-4"></i> <span class="text-sm">Biaya</span>
            </a>
            <a href="{{ route('products.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="package" class="w-4 h-4"></i> <span class="text-sm">Produk</span>
            </a>
            <!-- Menu dengan Sub (Inventori & Gudang) -->
            <div class="space-y-1">
                <button onclick="document.getElementById('sub-inventory').classList.toggle('hidden')"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                    <div class="flex items-center gap-3">
                        <i data-feather="archive" class="w-4 h-4"></i> <span class="text-sm">Inventori</span>
                    </div>
                    <i data-feather="chevron-down" class="w-3 h-3 text-slate-400"></i>
                </button>
                <div id="sub-inventory" class="hidden ml-4 pl-4 border-l border-slate-700 space-y-1 mb-2">
                    <a href="{{ route('warehouses.index') }}"
                        class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Data
                        Gudang</a>
                    <a href="{{ route('inventory.index') }}"
                        class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Stok
                        & Penyesuaian</a>
                </div>
            </div>
            <a href="{{ route('fixed-assets.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="briefcase" class="w-4 h-4"></i> <span class="text-sm">Aset Tetap</span>
            </a>
            <a href="{{ route('journals.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="dollar-sign" class="w-4 h-4"></i> <span class="text-sm">Kas & Bank</span>
            </a>
            <a href="{{ route('accounts.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="book" class="w-4 h-4"></i> <span class="text-sm">Akun CoA</span>
            </a>
            <a href="{{ route('contacts.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="users" class="w-4 h-4"></i> <span class="text-sm">Kontak</span>
            </a>
            <!-- Menu dengan Sub (Laporan Keuangan) -->
            <div class="space-y-1">
                <button onclick="document.getElementById('sub-reports').classList.toggle('hidden')"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                    <div class="flex items-center gap-3">
                        <i data-feather="trending-up" class="w-4 h-4"></i> <span class="text-sm">Laporan Keuangan</span>
                    </div>
                    <i data-feather="chevron-down" class="w-3 h-3 text-slate-400"></i>
                </button>
                <div id="sub-reports" class="hidden ml-4 pl-4 border-l border-slate-700 space-y-1 mb-2">
                    <a href="{{ route('reports.index', ['type' => 'profit-loss']) }}"
                        class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Laba
                        Rugi</a>
                    <a href="{{ route('reports.index', ['type' => 'balance-sheet']) }}"
                        class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Neraca</a>
                </div>
            </div>
            <a href="{{ route('settings.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="settings" class="w-4 h-4"></i> <span class="text-sm">Pengaturan</span>
            </a>
            <a href="{{ route('faqs.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="help-circle" class="w-4 h-4"></i> <span class="text-sm">FAQ</span>
            </a>
        </nav>

        <!-- Profil & Logout -->
        @php
            $displayName = Auth::user()->full_name ?: (Auth::user()->name ?: (Auth::user()->username ?? 'Admin'));
        @endphp
        <div class="border-t border-slate-800 p-4 shrink-0 flex items-center justify-between bg-slate-900">
            <div class="flex items-center gap-3 overflow-hidden">
                <div
                    class="w-9 h-9 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm shrink-0 uppercase">
                    {{ substr($displayName, 0, 1) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-sm font-bold text-white truncate">{{ $displayName }}</p>
                    <p
                        class="text-[10px] text-slate-400 font-medium truncate px-1.5 py-0.5 bg-slate-800 rounded mt-0.5 inline-block">
                        Role: {{ Auth::user()->role ?? 'Staff' }}
                    </p>
                </div>
            </div>

            <!-- Form Logout dengan SweetAlert2 -->
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="shrink-0">
                @csrf
                <button type="button" onclick="confirmLogout()" title="Keluar"
                    class="text-red-400 hover:text-red-500 hover:bg-red-400/10 p-2 rounded-lg transition-colors">
                    <i data-feather="log-out" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- KONTEN UTAMA -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- HEADER -->
        <header
            class="bg-white h-16 border-b border-slate-200 flex items-center justify-between px-4 sticky top-0 z-10 shrink-0">
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
                    <a href="{{ route('profile.index') }}" title="Profil Saya"
                        class="bg-slate-100 p-1.5 rounded-full text-slate-500 hover:bg-slate-200 transition-colors block">
                        <i data-feather="user" class="w-5 h-5"></i>
                    </a>
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

    <!-- Script Utama (Feather Icon & SweetAlert2 Logout) -->
    <script>
        feather.replace();

        function confirmLogout() {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Sesi kerja Anda saat ini akan diakhiri.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Logout!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }
    </script>
</body>

</html> --}}

<!-- resources/views/layouts/app.blade.php -->
@php
    // LOGIKA GLOBAL NOTIFIKASI LONCENG H-7 UNTUK SELURUH HALAMAN
    $sevenDays = \Carbon\Carbon::now()->addDays(7)->toDateString();
    $poAlertGlobal = \App\Models\Purchase::with('contact')->whereIn('status', ['Pesanan', 'Pengiriman'])
        ->whereDate('due_date', '<=', $sevenDays)->orderBy('due_date', 'asc')->get();
    $soAlertGlobal = \App\Models\Sale::with('contact')->whereIn('status', ['Pesanan', 'Pengiriman'])
        ->whereDate('due_date', '<=', $sevenDays)->orderBy('due_date', 'asc')->get();
    $totalAlertGlobal = $poAlertGlobal->count() + $soAlertGlobal->count();
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT Berlianmandiri Perkasa - ERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #334155; border-radius: 20px; }
    </style>
</head>

<body class="bg-slate-100 flex min-h-screen font-sans antialiased text-slate-800">
    <!-- SIDEBAR -->
    <aside class="w-[260px] bg-slate-900 text-white border-r border-slate-800 h-screen sticky top-0 flex flex-col shrink-0">
        <div class="h-16 flex items-center px-4 border-b border-slate-800 shrink-0">
            <h1 class="text-sm font-bold text-orange-400 leading-tight">PT Berlianmandiri Perkasa</h1>
        </div>
        <nav class="flex-1 overflow-y-auto px-3 space-y-1 custom-scrollbar pb-4 pt-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-blue-400 font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-feather="home" class="w-4 h-4"></i> <span class="text-sm">Beranda</span>
            </a>

            <div class="space-y-1">
                <button onclick="document.getElementById('sub-sales').classList.toggle('hidden')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                    <div class="flex items-center gap-3"><i data-feather="shopping-bag" class="w-4 h-4"></i> <span class="text-sm">Penjualan</span></div>
                    <i data-feather="chevron-down" class="w-3 h-3 text-slate-400"></i>
                </button>
                <div id="sub-sales" class="hidden ml-4 pl-4 border-l border-slate-700 space-y-1 mb-2">
                    <a href="{{ route('sales.index') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Pesanan Penjualan</a>
                    <a href="{{ route('sales.deliveries') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Pengiriman Penjualan</a>
                    <a href="{{ route('sales.invoices') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Tagihan Penjualan</a>
                </div>
            </div>

            <div class="space-y-1">
                <button onclick="document.getElementById('sub-purchases').classList.toggle('hidden')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                    <div class="flex items-center gap-3"><i data-feather="shopping-cart" class="w-4 h-4"></i> <span class="text-sm">Pembelian</span></div>
                    <i data-feather="chevron-down" class="w-3 h-3 text-slate-400"></i>
                </button>
                <div id="sub-purchases" class="hidden ml-4 pl-4 border-l border-slate-700 space-y-1 mb-2">
                    <a href="{{ route('purchases.index') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Pesanan Pembelian</a>
                    <a href="{{ route('purchases.deliveries') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Penerimaan Pembelian</a>
                    <a href="{{ route('purchases.bills') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Tagihan Pembelian</a>
                </div>
            </div>

            <a href="{{ route('expenses.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="file-text" class="w-4 h-4"></i> <span class="text-sm">Biaya</span>
            </a>
            <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="package" class="w-4 h-4"></i> <span class="text-sm">Produk</span>
            </a>
            <div class="space-y-1">
                <button onclick="document.getElementById('sub-inventory').classList.toggle('hidden')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                    <div class="flex items-center gap-3"><i data-feather="archive" class="w-4 h-4"></i> <span class="text-sm">Inventori</span></div>
                    <i data-feather="chevron-down" class="w-3 h-3 text-slate-400"></i>
                </button>
                <div id="sub-inventory" class="hidden ml-4 pl-4 border-l border-slate-700 space-y-1 mb-2">
                    <a href="{{ route('warehouses.index') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Data Gudang</a>
                    <a href="{{ route('inventory.index') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Stok & Penyesuaian</a>
                </div>
            </div>
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
            <div class="space-y-1">
                <button onclick="document.getElementById('sub-reports').classList.toggle('hidden')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                    <div class="flex items-center gap-3"><i data-feather="trending-up" class="w-4 h-4"></i> <span class="text-sm">Laporan Keuangan</span></div>
                    <i data-feather="chevron-down" class="w-3 h-3 text-slate-400"></i>
                </button>
                <div id="sub-reports" class="hidden ml-4 pl-4 border-l border-slate-700 space-y-1 mb-2">
                    <a href="{{ route('reports.index', ['type' => 'profit-loss']) }}" class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Laba Rugi</a>
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

        @php $displayName = Auth::user()->full_name ?: (Auth::user()->name ?: (Auth::user()->username ?? 'Admin')); @endphp
        <div class="border-t border-slate-800 p-4 shrink-0 flex items-center justify-between bg-slate-900">
            <div class="flex items-center gap-3 overflow-hidden">
                <div class="w-9 h-9 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm shrink-0 uppercase">{{ substr($displayName, 0, 1) }}</div>
                <div class="overflow-hidden">
                    <p class="text-sm font-bold text-white truncate">{{ $displayName }}</p>
                    <p class="text-[10px] text-slate-400 font-medium truncate px-1.5 py-0.5 bg-slate-800 rounded mt-0.5 inline-block">Role: {{ Auth::user()->role ?? 'Staff' }}</p>
                </div>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="shrink-0">
                @csrf
                <button type="button" onclick="confirmLogout()" title="Keluar" class="text-red-400 hover:text-red-500 hover:bg-red-400/10 p-2 rounded-lg transition-colors"><i data-feather="log-out" class="w-4 h-4"></i></button>
            </form>
        </div>
    </aside>

    <!-- KONTEN UTAMA -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- HEADER -->
        <header class="bg-white h-16 border-b border-slate-200 flex items-center justify-between px-4 sticky top-0 z-10 shrink-0">
            <div></div>
            <div class="flex items-center gap-4">
                <span class="text-xs font-bold text-orange-400 hidden md:block">PT BERLIANMANDIRI PERKASA</span>

                <div class="flex items-center gap-3 text-slate-400 border-l border-slate-200 pl-4">

                    <!-- DROPDOWN LONCENG NOTIFIKASI -->
                    <div class="relative group">
                        <button class="hover:text-blue-600 relative p-1.5">
                            <i data-feather="bell" class="w-5 h-5"></i>
                            @if($totalAlertGlobal > 0)
                                <span class="absolute top-0 right-0 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[9px] font-bold text-white border border-white">{{ $totalAlertGlobal }}</span>
                            @endif
                        </button>
                        <!-- Isi Dropdown -->
                        <div class="absolute right-0 mt-2 w-80 bg-white border border-slate-200 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 overflow-hidden">
                            <div class="bg-slate-50 border-b border-slate-200 p-3">
                                <h4 class="font-bold text-slate-800 text-sm">Notifikasi Jatuh Tempo (H-7)</h4>
                            </div>
                            <div class="max-h-80 overflow-y-auto custom-scrollbar p-0">
                                @if($totalAlertGlobal > 0)
                                    @foreach($poAlertGlobal as $po)
                                        <a href="{{ route('purchases.bills') }}" class="block p-4 border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                            <p class="text-xs font-bold text-red-600 mb-1">Tagihan Pembelian (Hutang)</p>
                                            <p class="text-sm font-semibold text-slate-800">{{ $po->reference_no ?: 'PO #'.$po->id }} - {{ $po->contact->name }}</p>
                                            <p class="text-xs text-slate-500 font-medium mt-1"><i data-feather="clock" class="w-3 h-3 inline text-red-500"></i> Jatuh Tempo: {{ \Carbon\Carbon::parse($po->due_date)->translatedFormat('d M Y') }}</p>
                                        </a>
                                    @endforeach
                                    @foreach($soAlertGlobal as $so)
                                        <a href="{{ route('sales.invoices') }}" class="block p-4 border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                            <p class="text-xs font-bold text-blue-600 mb-1">Tagihan Penjualan (Piutang)</p>
                                            <p class="text-sm font-semibold text-slate-800">{{ $so->reference_no ?: 'SO #'.$so->id }} - {{ $so->contact->name }}</p>
                                            <p class="text-xs text-slate-500 font-medium mt-1"><i data-feather="clock" class="w-3 h-3 inline text-red-500"></i> Jatuh Tempo: {{ \Carbon\Carbon::parse($so->due_date)->translatedFormat('d M Y') }}</p>
                                        </a>
                                    @endforeach
                                @else
                                    <div class="p-6 text-center text-slate-500 text-sm font-medium">
                                        Selamat! Tidak ada tagihan mendesak.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('profile.index') }}" title="Profil Saya" class="bg-slate-100 p-1.5 rounded-full text-slate-500 hover:bg-slate-200 transition-colors block">
                        <i data-feather="user" class="w-5 h-5"></i>
                    </a>
                </div>
            </div>
        </header>

        <main class="flex-1 p-6 overflow-y-auto">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">{{ session('error') }}</div>
            @endif
            @yield('content')
        </main>
    </div>

    <script>
        feather.replace();
        function confirmLogout() {
            Swal.fire({
                title: 'Apakah Anda yakin?', text: "Sesi kerja Anda saat ini akan diakhiri.",
                icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#64748b', confirmButtonText: 'Ya, Logout!', cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) { document.getElementById('logout-form').submit(); }
            });
        }
    </script>
</body>
</html>
