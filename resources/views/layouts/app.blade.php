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
            @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                {{ session('success') }}
            </div>
            @endif
            @if (session('error'))
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
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
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

{{-- <!-- resources/views/layouts/app.blade.php -->
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
                    <a href="{{ route('sales.overview') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white font-bold text-indigo-400"><i data-feather="bar-chart-2" class="w-3 h-3 inline mr-1"></i> Overview Analitik</a>
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
                    <a href="{{ route('purchases.overview') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white font-bold text-indigo-400"><i data-feather="bar-chart-2" class="w-3 h-3 inline mr-1"></i> Overview Analitik</a>
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
            <a href="{{ route('payroll.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="credit-card" class="w-4 h-4"></i> <span class="text-sm">Payroll</span>
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
                            @if ($totalAlertGlobal > 0)
                                <span class="absolute top-0 right-0 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[9px] font-bold text-white border border-white">{{ $totalAlertGlobal }}</span>
                            @endif
                        </button>
                        <!-- Isi Dropdown -->
                        <div class="absolute right-0 mt-2 w-80 bg-white border border-slate-200 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 overflow-hidden">
                            <div class="bg-slate-50 border-b border-slate-200 p-3">
                                <h4 class="font-bold text-slate-800 text-sm">Notifikasi Jatuh Tempo (H-7)</h4>
                            </div>
                            <div class="max-h-80 overflow-y-auto custom-scrollbar p-0">
                                @if ($totalAlertGlobal > 0)
                                    @foreach ($poAlertGlobal as $po)
                                        <a href="{{ route('purchases.bills') }}" class="block p-4 border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                            <p class="text-xs font-bold text-red-600 mb-1">Tagihan Pembelian (Hutang)</p>
                                            <p class="text-sm font-semibold text-slate-800">{{ $po->reference_no ?: 'PO #'.$po->id }} - {{ $po->contact->name }}</p>
                                            <p class="text-xs text-slate-500 font-medium mt-1"><i data-feather="clock" class="w-3 h-3 inline text-red-500"></i> Jatuh Tempo: {{ \Carbon\Carbon::parse($po->due_date)->translatedFormat('d M Y') }}</p>
                                        </a>
                                    @endforeach
                                    @foreach ($soAlertGlobal as $so)
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
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">{{ session('success') }}</div>
            @endif
            @if (session('error'))
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
</html> --}}

<!-- resources/views/layouts/app.blade.php -->
{{-- @php
    // LOGIKA GLOBAL NOTIFIKASI LONCENG H-7 UNTUK SELURUH HALAMAN
    $sevenDays = \Carbon\Carbon::now()->addDays(7)->toDateString();
    $poAlertGlobal = \App\Models\Purchase::with('contact')->whereIn('status', ['Pesanan', 'Pengiriman'])
        ->whereDate('due_date', '<=', $sevenDays)->orderBy('due_date', 'asc')->get();
    $soAlertGlobal = \App\Models\Sale::with('contact')->whereIn('status', ['Pesanan', 'Pengiriman'])
        ->whereDate('due_date', '<=', $sevenDays)->orderBy('due_date', 'asc')->get();
    $totalAlertGlobal = $poAlertGlobal->count() + $soAlertGlobal->count();

    // ==============================================================
    // LOGIKA GLOBAL: PEMBACAAN MATRIKS HAK AKSES (RBAC)
    // ==============================================================
    $userRole = Auth::user()->role ?? 'Staff';
    $companySetting = \App\Models\CompanySetting::first();
    $rolePermissions = $companySetting && $companySetting->role_permissions ? json_decode($companySetting->role_permissions, true) : [];

    // Fungsi penolong (helper) untuk mengecek apakah user boleh melihat menu
    $hasAccess = function($menuName) use ($userRole, $rolePermissions) {
        // Jika database matriks belum diatur (kosong sama sekali), beri akses ke Admin sebagai fallback keamanan
        if (empty($rolePermissions)) {
            return in_array($userRole, ['Admin', 'Direktur']);
        }
        // Cek apakah Role user terdaftar di JSON matriks dan memiliki centang untuk menu tersebut
        return isset($rolePermissions[$userRole]) && in_array($menuName, $rolePermissions[$userRole]);
    };
@endphp --}}

@php
    // LOGIKA GLOBAL NOTIFIKASI LONCENG UNTUK SELURUH HALAMAN
    $todayGlobal = \Carbon\Carbon::today()->toDateString();
    $sevenDaysGlobal = \Carbon\Carbon::today()->addDays(7)->toDateString();

    $poAlertsRawG = \App\Models\Purchase::with('contact')
        ->whereIn('status', ['Pesanan', 'Pengiriman'])
        ->whereDate('due_date', '<=', $sevenDaysGlobal)
        ->orderBy('due_date', 'asc')
        ->get();
    $soAlertsRawG = \App\Models\Sale::with('contact')
        ->whereIn('status', ['Pesanan', 'Pengiriman'])
        ->whereDate('due_date', '<=', $sevenDaysGlobal)
        ->orderBy('due_date', 'asc')
        ->get();

    $poOverdueG = $poAlertsRawG->filter(fn($i) => $i->due_date < $todayGlobal);
    $poTodayG = $poAlertsRawG->filter(fn($i) => $i->due_date == $todayGlobal);
    $poUpcomingG = $poAlertsRawG->filter(fn($i) => $i->due_date > $todayGlobal);

    $soOverdueG = $soAlertsRawG->filter(fn($i) => $i->due_date < $todayGlobal);
    $soTodayG = $soAlertsRawG->filter(fn($i) => $i->due_date == $todayGlobal);
    $soUpcomingG = $soAlertsRawG->filter(fn($i) => $i->due_date > $todayGlobal);

    $totalAlertGlobal = $poAlertsRawG->count() + $soAlertsRawG->count();

    // TAMBAHAN: Ambil data rekening bank untuk modal Quick Pay Global
    $bankAccountsGlobal = \App\Models\Account::where(function ($query) {
        $query->where('code', 'LIKE', '10%')->orWhere('code', 'LIKE', '11%');
    })
        ->where('is_header', false)
        ->get();

    // ==============================================================
    // LOGIKA GLOBAL: PEMBACAAN MATRIKS HAK AKSES (RBAC)
    // ==============================================================
    $userRole = Auth::user()->role ?? 'Staff';
    $companySetting = \App\Models\CompanySetting::first();
    $rolePermissions =
        $companySetting && $companySetting->role_permissions
            ? json_decode($companySetting->role_permissions, true)
            : [];

    // Fungsi penolong (helper) untuk mengecek apakah user boleh melihat menu
    // $hasAccess = function($menuName) use ($userRole, $rolePermissions) {
    //     // Jika database matriks belum diatur (kosong sama sekali), beri akses ke Admin sebagai fallback keamanan
    //     if (empty($rolePermissions)) {
    //         return in_array($userRole, ['Admin', 'Direktur']);
    //     }
    //     // Cek apakah Role user terdaftar di JSON matriks dan memiliki centang untuk menu tersebut
    //     return isset($rolePermissions[$userRole]) && in_array($menuName, $rolePermissions[$userRole]);
    // };

    // Fungsi penolong: Cek apakah user boleh melihat (Read) menu di Sidebar
    $hasAccess = function ($menuName) use ($userRole, $rolePermissions) {
        if (empty($rolePermissions)) {
            return in_array($userRole, ['Admin', 'Direktur']);
        }

        // Jaga-jaga jika masih ada format data lama (Array 1D) di database
        if (isset($rolePermissions[$userRole][0]) && is_string($rolePermissions[$userRole][0])) {
            return in_array($menuName, $rolePermissions[$userRole]);
        }

        // Format Baru: Cek apakah menu tersebut memiliki huruf 'R' (Read)
        return isset($rolePermissions[$userRole][$menuName]) && in_array('R', $rolePermissions[$userRole][$menuName]);
    };

    // Fungsi penolong baru: Cek hak aksi spesifik (Create, Update, Delete) di dalam halaman
    $canAction = function ($actionLetter, $menuName) use ($userRole, $rolePermissions) {
        if (empty($rolePermissions)) {
            return in_array($userRole, ['Admin', 'Direktur']);
        }
        if (isset($rolePermissions[$userRole][0]) && is_string($rolePermissions[$userRole][0])) {
            return true;
        } // Bypass format lama

        return isset($rolePermissions[$userRole][$menuName]) &&
            in_array($actionLetter, $rolePermissions[$userRole][$menuName]);
    };
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
        <div class="h-16 flex items-center px-4 border-b border-slate-800 shrink-0">
            <h1 class="text-sm font-bold text-orange-400 leading-tight">PT Berlianmandiri Perkasa</h1>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 space-y-1 custom-scrollbar pb-4 pt-4">

            @if ($hasAccess('Dashboard'))
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-blue-400 font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-feather="home" class="w-4 h-4"></i> <span class="text-sm">Beranda</span>
                </a>
            @endif

            {{-- @if ($hasAccess('Penjualan'))
            <div class="space-y-1">
                <button onclick="document.getElementById('sub-sales').classList.toggle('hidden')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                    <div class="flex items-center gap-3"><i data-feather="shopping-bag" class="w-4 h-4"></i> <span class="text-sm">Penjualan</span></div>
                    <i data-feather="chevron-down" class="w-3 h-3 text-slate-400"></i>
                </button>
                <div id="sub-sales" class="hidden ml-4 pl-4 border-l border-slate-700 space-y-1 mb-2">
                    <a href="{{ route('sales.overview') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white font-bold text-indigo-400"><i data-feather="bar-chart-2" class="w-3 h-3 inline mr-1"></i> Overview Analitik</a>
                    <a href="{{ route('sales.index') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Pesanan Penjualan</a>
                    <a href="{{ route('sales.deliveries') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Pengiriman Penjualan</a>
                    <a href="{{ route('sales.invoices') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Tagihan Penjualan</a>
                </div>
            </div>
            @endif --}}

            @if ($hasAccess('Penjualan'))
                <div class="space-y-1">
                    <button onclick="document.getElementById('sub-sales').classList.toggle('hidden')"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                        <div class="flex items-center gap-3"><i data-feather="shopping-bag" class="w-4 h-4"></i> <span
                                class="text-sm">Penjualan</span></div>
                        <i data-feather="chevron-down" class="w-3 h-3 text-slate-400"></i>
                    </button>
                    <div id="sub-sales" class="hidden ml-4 pl-4 border-l border-slate-700 space-y-1 mb-2">
                        <a href="{{ route('sales.overview') }}"
                            class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white font-bold text-indigo-400"><i
                                data-feather="bar-chart-2" class="w-3 h-3 inline mr-1"></i> Overview Analitik</a>
                        <a href="{{ route('sales.index') }}"
                            class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Pesanan
                            Penjualan</a>
                        <a href="{{ route('sales.deliveries') }}"
                            class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Pengiriman
                            Penjualan</a>
                        <a href="{{ route('sales.invoices') }}"
                            class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Tagihan
                            Penjualan</a>

                        <!-- MENU BARU: LAPORAN UMUR PIUTANG -->
                        <a href="{{ route('sales.aging') }}"
                            class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white font-bold text-rose-400 mt-1 border-t border-slate-700/50 pt-2"><i
                                data-feather="pie-chart" class="w-3 h-3 inline mr-1"></i> Umur Piutang (AR)</a>
                    </div>
                </div>
            @endif

            @if ($hasAccess('Pembelian'))
                <div class="space-y-1">
                    <button onclick="document.getElementById('sub-purchases').classList.toggle('hidden')"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                        <div class="flex items-center gap-3"><i data-feather="shopping-cart" class="w-4 h-4"></i> <span
                                class="text-sm">Pembelian</span></div>
                        <i data-feather="chevron-down" class="w-3 h-3 text-slate-400"></i>
                    </button>
                    <div id="sub-purchases" class="hidden ml-4 pl-4 border-l border-slate-700 space-y-1 mb-2">
                        <a href="{{ route('purchases.overview') }}"
                            class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white font-bold text-indigo-400"><i
                                data-feather="bar-chart-2" class="w-3 h-3 inline mr-1"></i> Overview Analitik</a>
                        <a href="{{ route('purchases.index') }}"
                            class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Pesanan
                            Pembelian</a>
                        <a href="{{ route('purchases.deliveries') }}"
                            class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Penerimaan
                            Pembelian</a>
                        <a href="{{ route('purchases.bills') }}"
                            class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Tagihan
                            Pembelian</a>
                        <a href="{{ route('purchases.aging') }}"
                            class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white font-bold text-orange-400 mt-1 border-t border-slate-700/50 pt-2"><i
                                data-feather="pie-chart" class="w-3 h-3 inline mr-1"></i> Umur Hutang (AP)</a>
                    </div>
                </div>
            @endif

            @if ($hasAccess('Biaya'))
                <a href="{{ route('expenses.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                    <i data-feather="file-text" class="w-4 h-4"></i> <span class="text-sm">Biaya</span>
                </a>
            @endif

            @if ($hasAccess('Produk'))
                <a href="{{ route('products.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                    <i data-feather="package" class="w-4 h-4"></i> <span class="text-sm">Produk</span>
                </a>
            @endif

            @if ($hasAccess('Inventori'))
                <div class="space-y-1">
                    <button onclick="document.getElementById('sub-inventory').classList.toggle('hidden')"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                        <div class="flex items-center gap-3"><i data-feather="archive" class="w-4 h-4"></i> <span
                                class="text-sm">Inventori</span></div>
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
            @endif

            @if ($hasAccess('Aset Tetap'))
                <a href="{{ route('fixed-assets.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                    <i data-feather="briefcase" class="w-4 h-4"></i> <span class="text-sm">Aset Tetap</span>
                </a>
            @endif

            {{-- @if ($hasAccess('Kas & Bank'))
                <a href="{{ route('journals.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                    <i data-feather="dollar-sign" class="w-4 h-4"></i> <span class="text-sm">Kas & Bank</span>
                </a>
            @endif --}}

            @if($hasAccess('Kas & Bank'))
            <div class="space-y-1">
                <button onclick="document.getElementById('sub-banks').classList.toggle('hidden')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                    <div class="flex items-center gap-3"><i data-feather="dollar-sign" class="w-4 h-4"></i> <span class="text-sm">Kas & Bank</span></div>
                    <i data-feather="chevron-down" class="w-3 h-3 text-slate-400"></i>
                </button>
                <div id="sub-banks" class="hidden ml-4 pl-4 border-l border-slate-700 space-y-1 mb-2">
                    <a href="{{ route('journals.index') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Buku Jurnal Umum</a>
                    <!-- MENU REKONSILIASI -->
                    <a href="{{ route('journals.reconciliation') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white font-bold text-teal-400 mt-1 border-t border-slate-700/50 pt-2"><i data-feather="check-square" class="w-3 h-3 inline mr-1"></i> Rekonsiliasi Bank</a>
                </div>
            </div>
            @endif

            @if ($hasAccess('Akun CoA'))
                <a href="{{ route('accounts.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                    <i data-feather="book" class="w-4 h-4"></i> <span class="text-sm">Akun CoA</span>
                </a>
            @endif

            @if ($hasAccess('Kontak'))
                <a href="{{ route('contacts.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                    <i data-feather="users" class="w-4 h-4"></i> <span class="text-sm">Kontak</span>
                </a>
            @endif

            @if ($hasAccess('Penggajian'))
                <a href="{{ route('payroll.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                    <i data-feather="credit-card" class="w-4 h-4"></i> <span
                        class="text-sm font-semibold">Payroll</span>
                </a>
            @endif

            @if ($hasAccess('Laporan Keuangan'))
                <div class="space-y-1">
                    <button onclick="document.getElementById('sub-reports').classList.toggle('hidden')"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                        <div class="flex items-center gap-3"><i data-feather="trending-up" class="w-4 h-4"></i> <span
                                class="text-sm">Laporan Keuangan</span></div>
                        <i data-feather="chevron-down" class="w-3 h-3 text-slate-400"></i>
                    </button>
                    <div id="sub-reports" class="hidden ml-4 pl-4 border-l border-slate-700 space-y-1 mb-2">
                        <a href="{{ route('reports.index', ['type' => 'profit-loss']) }}"
                            class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Laba
                            Rugi</a>
                        <a href="{{ route('reports.index', ['type' => 'balance-sheet']) }}"
                            class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Neraca</a>
                        <a href="{{ route('reports.index', ['type' => 'cash-flow']) }}"
                            class="block px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white">Arus
                            Kas (Cash Flow)</a>
                    </div>
                </div>
            @endif

            @if ($hasAccess('Pengaturan'))
                <a href="{{ route('settings.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                    <i data-feather="settings" class="w-4 h-4"></i> <span class="text-sm">Pengaturan</span>
                </a>
            @endif

            <!-- Menu Bantuan (Umum untuk semua user, tidak perlu dibatasi) -->
            <a href="{{ route('faqs.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-feather="help-circle" class="w-4 h-4"></i> <span class="text-sm">Bantuan / FAQ</span>
            </a>

        </nav>

        @php
            $displayName = Auth::user()->full_name ?: (Auth::user()->name ?: Auth::user()->username ?? 'Admin');
        @endphp

        <div class="border-t border-slate-800 p-4 shrink-0 flex items-center justify-between bg-slate-900">
            <div class="flex items-center gap-3 overflow-hidden">
                <div
                    class="w-9 h-9 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm shrink-0 uppercase">
                    {{ substr($displayName, 0, 1) }}</div>
                <div class="overflow-hidden">
                    <p class="text-sm font-bold text-white truncate">{{ $displayName }}</p>
                    <p
                        class="text-[10px] text-slate-400 font-medium truncate px-1.5 py-0.5 bg-slate-800 rounded mt-0.5 inline-block">
                        Role: {{ Auth::user()->role ?? 'Admin' }}</p>
                </div>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="shrink-0">
                @csrf
                <button type="button" onclick="confirmLogout()" title="Keluar"
                    class="text-red-400 hover:text-red-500 hover:bg-red-400/10 p-2 rounded-lg transition-colors"><i
                        data-feather="log-out" class="w-4 h-4"></i></button>
            </form>
        </div>
    </aside>

    <!-- KONTEN UTAMA -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- HEADER KONTEN -->
        <header
            class="bg-white h-16 border-b border-slate-200 flex items-center justify-between px-4 sticky top-0 z-10 shrink-0">
            <div></div>
            <div class="flex items-center gap-4">
                <span class="text-xs font-bold text-orange-400 hidden md:block">PT BERLIANMANDIRI PERKASA</span>

                <div class="flex items-center gap-3 text-slate-400 border-l border-slate-200 pl-4">

                    <!-- DROPDOWN LONCENG NOTIFIKASI -->
                    <div class="relative group">
                        <button class="hover:text-blue-600 relative p-1.5 transition-colors">
                            <i data-feather="bell" class="w-5 h-5"></i>
                            @if ($totalAlertGlobal > 0)
                                <span
                                    class="absolute top-0 right-0 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[9px] font-bold text-white border border-white">{{ $totalAlertGlobal }}</span>
                            @endif
                        </button>
                        <!-- Isi Dropdown -->
                        {{-- <div class="absolute right-0 mt-2 w-80 bg-white border border-slate-200 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 overflow-hidden">
                            <div class="bg-slate-50 border-b border-slate-200 p-3">
                                <h4 class="font-bold text-slate-800 text-sm">Notifikasi Jatuh Tempo (H-7)</h4>
                            </div>
                            <div class="max-h-80 overflow-y-auto custom-scrollbar p-0">
                                @if ($totalAlertGlobal > 0)
                                    @foreach ($poAlertGlobal as $po)
                                        <a href="{{ route('purchases.bills') }}" class="block p-4 border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                            <p class="text-xs font-bold text-red-600 mb-1">Tagihan Pembelian (Hutang)</p>
                                            <p class="text-sm font-semibold text-slate-800">{{ $po->reference_no ?: 'PO #'.$po->id }} - {{ $po->contact->name }}</p>
                                            <p class="text-xs text-slate-500 font-medium mt-1"><i data-feather="clock" class="w-3 h-3 inline text-red-500"></i> Jatuh Tempo: {{ \Carbon\Carbon::parse($po->due_date)->translatedFormat('d M Y') }}</p>
                                        </a>
                                    @endforeach
                                    @foreach ($soAlertGlobal as $so)
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
                        </div> --}}
                        <!-- Isi Dropdown -->
                        <div
                            class="absolute right-0 mt-2 w-80 bg-white border border-slate-200 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 overflow-hidden">
                            <div class="bg-slate-800 border-b border-slate-700 p-3">
                                <h4 class="font-bold text-white text-sm">Pusat Notifikasi Keuangan</h4>
                            </div>
                            <div class="max-h-80 overflow-y-auto custom-scrollbar p-0">
                                @if ($totalAlertGlobal > 0)

                                    <!-- OVERDUE LIST -->
                                    @if ($poOverdueG->count() > 0 || $soOverdueG->count() > 0)
                                        <div
                                            class="bg-red-50 text-red-800 text-xs font-black px-4 py-2 uppercase border-b border-red-100">
                                            Lewat Tempo (Menunggak)</div>
                                        @foreach ($poOverdueG as $po)
                                            {{-- <a href="{{ route('purchases.bills') }}" class="block p-4 border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                                <p class="text-xs font-bold text-red-600 mb-1">Tagihan Pembelian (Hutang)</p>
                                                <p class="text-sm font-semibold text-slate-800">{{ $po->reference_no ?: 'PO #'.$po->id }} - {{ $po->contact->name }}</p>
                                                <p class="text-xs text-red-500 font-bold mt-1"><i data-feather="x-octagon" class="w-3 h-3 inline"></i> Lewat Sejak: {{ \Carbon\Carbon::parse($po->due_date)->translatedFormat('d M Y') }}</p>
                                            </a> --}}
                                            <button type="button"
                                                onclick="openQuickPay('po', {{ $po->id }}, '{{ $po->reference_no ?: 'PO #' . $po->id }}', {{ $po->total }}, false)"
                                                class="w-full text-left block p-4 border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                                <p class="text-xs font-bold text-red-600 mb-1">Tagihan Pembelian
                                                    (Hutang)
                                                </p>
                                                <p class="text-sm font-semibold text-slate-800">
                                                    {{ $po->reference_no ?: 'PO #' . $po->id }} -
                                                    {{ $po->contact->name }}</p>
                                                <p class="text-xs text-slate-500 font-bold mt-1"><i
                                                        data-feather="clock" class="w-3 h-3 inline"></i> Tempo:
                                                    {{ \Carbon\Carbon::parse($po->due_date)->translatedFormat('d M Y') }}
                                                </p>
                                            </button>
                                        @endforeach
                                        @foreach ($soOverdueG as $so)
                                            {{-- <a href="{{ route('sales.invoices') }}"
                                                class="block p-4 border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                                <p class="text-xs font-bold text-blue-600 mb-1">Tagihan Penjualan
                                                    (Piutang)</p>
                                                <p class="text-sm font-semibold text-slate-800">
                                                    {{ $so->reference_no ?: 'SO #' . $so->id }} -
                                                    {{ $so->contact->name }}</p>
                                                <p class="text-xs text-red-500 font-bold mt-1"><i
                                                        data-feather="x-octagon" class="w-3 h-3 inline"></i> Lewat
                                                    Sejak:
                                                    {{ \Carbon\Carbon::parse($so->due_date)->translatedFormat('d M Y') }}
                                                </p>
                                            </a> --}}
                                            <button type="button"
                                                onclick="openQuickPay('so', {{ $so->id }}, '{{ $so->reference_no ?: 'SO #' . $so->id }}', {{ $so->total }}, true)"
                                                class="w-full text-left block p-4 border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                                <p class="text-xs font-bold text-blue-600 mb-1">Tagihan Penjualan
                                                    (Piutang)
                                                </p>
                                                <p class="text-sm font-semibold text-slate-800">
                                                    {{ $so->reference_no ?: 'SO #' . $so->id }} -
                                                    {{ $so->contact->name }}</p>
                                                <p class="text-xs text-slate-500 font-bold mt-1"><i
                                                        data-feather="clock" class="w-3 h-3 inline"></i> Tempo:
                                                    {{ \Carbon\Carbon::parse($so->due_date)->translatedFormat('d M Y') }}
                                                </p>
                                            </button>
                                        @endforeach
                                    @endif

                                    <!-- TODAY LIST -->
                                    @if ($poTodayG->count() > 0 || $soTodayG->count() > 0)
                                        <div
                                            class="bg-orange-50 text-orange-800 text-xs font-black px-4 py-2 uppercase border-b border-orange-100 border-t">
                                            Jatuh Tempo Hari Ini</div>
                                        @foreach ($poTodayG as $po)
                                            {{-- <a href="{{ route('purchases.bills') }}"
                                                class="block p-4 border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                                <p class="text-xs font-bold text-red-600 mb-1">Tagihan Pembelian
                                                    (Hutang)
                                                </p>
                                                <p class="text-sm font-semibold text-slate-800">
                                                    {{ $po->reference_no ?: 'PO #' . $po->id }} -
                                                    {{ $po->contact->name }}</p>
                                            </a> --}}
                                            <button type="button"
                                                onclick="openQuickPay('po', {{ $po->id }}, '{{ $po->reference_no ?: 'PO #' . $po->id }}', {{ $po->total }}, false)"
                                                class="w-full text-left block p-4 border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                                <p class="text-xs font-bold text-red-600 mb-1">Tagihan Pembelian
                                                    (Hutang)
                                                </p>
                                                <p class="text-sm font-semibold text-slate-800">
                                                    {{ $po->reference_no ?: 'PO #' . $po->id }} -
                                                    {{ $po->contact->name }}</p>
                                                <p class="text-xs text-slate-500 font-bold mt-1"><i
                                                        data-feather="clock" class="w-3 h-3 inline"></i> Tempo:
                                                    {{ \Carbon\Carbon::parse($po->due_date)->translatedFormat('d M Y') }}
                                                </p>
                                            </button>
                                        @endforeach
                                        @foreach ($soTodayG as $so)
                                            {{-- <a href="{{ route('sales.invoices') }}"
                                                class="block p-4 border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                                <p class="text-xs font-bold text-blue-600 mb-1">Tagihan Penjualan
                                                    (Piutang)
                                                </p>
                                                <p class="text-sm font-semibold text-slate-800">
                                                    {{ $so->reference_no ?: 'SO #' . $so->id }} -
                                                    {{ $so->contact->name }}</p>
                                            </a> --}}
                                            <button type="button"
                                                onclick="openQuickPay('so', {{ $so->id }}, '{{ $so->reference_no ?: 'SO #' . $so->id }}', {{ $so->total }}, true)"
                                                class="w-full text-left block p-4 border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                                <p class="text-xs font-bold text-blue-600 mb-1">Tagihan Penjualan
                                                    (Piutang)
                                                </p>
                                                <p class="text-sm font-semibold text-slate-800">
                                                    {{ $so->reference_no ?: 'SO #' . $so->id }} -
                                                    {{ $so->contact->name }}</p>
                                                <p class="text-xs text-slate-500 font-bold mt-1"><i
                                                        data-feather="clock" class="w-3 h-3 inline"></i> Tempo:
                                                    {{ \Carbon\Carbon::parse($so->due_date)->translatedFormat('d M Y') }}
                                                </p>
                                            </button>
                                        @endforeach
                                    @endif

                                    <!-- UPCOMING LIST -->
                                    @if ($poUpcomingG->count() > 0 || $soUpcomingG->count() > 0)
                                        <div
                                            class="bg-yellow-50 text-yellow-800 text-xs font-black px-4 py-2 uppercase border-b border-yellow-100 border-t">
                                            Mendekati H-7</div>
                                        @foreach ($poUpcomingG as $po)
                                            {{-- <a href="{{ route('purchases.bills') }}"
                                                class="block p-4 border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                                <p class="text-xs font-bold text-red-600 mb-1">Tagihan Pembelian
                                                    (Hutang)
                                                </p>
                                                <p class="text-sm font-semibold text-slate-800">
                                                    {{ $po->reference_no ?: 'PO #' . $po->id }} -
                                                    {{ $po->contact->name }}</p>
                                                <p class="text-xs text-slate-500 font-medium mt-1"><i
                                                        data-feather="clock"
                                                        class="w-3 h-3 inline text-yellow-500"></i> Jatuh Tempo:
                                                    {{ \Carbon\Carbon::parse($po->due_date)->translatedFormat('d M Y') }}
                                                </p>
                                            </a> --}}
                                            <button type="button"
                                                onclick="openQuickPay('po', {{ $po->id }}, '{{ $po->reference_no ?: 'PO #' . $po->id }}', {{ $po->total }}, false)"
                                                class="w-full text-left block p-4 border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                                <p class="text-xs font-bold text-red-600 mb-1">Tagihan Pembelian
                                                    (Hutang)
                                                </p>
                                                <p class="text-sm font-semibold text-slate-800">
                                                    {{ $po->reference_no ?: 'PO #' . $po->id }} -
                                                    {{ $po->contact->name }}</p>
                                                <p class="text-xs text-slate-500 font-bold mt-1"><i
                                                        data-feather="clock" class="w-3 h-3 inline"></i> Tempo:
                                                    {{ \Carbon\Carbon::parse($po->due_date)->translatedFormat('d M Y') }}
                                                </p>
                                            </button>
                                        @endforeach
                                        @foreach ($soUpcomingG as $so)
                                            {{-- <a href="{{ route('sales.invoices') }}"
                                                class="block p-4 border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                                <p class="text-xs font-bold text-blue-600 mb-1">Tagihan Penjualan
                                                    (Piutang)
                                                </p>
                                                <p class="text-sm font-semibold text-slate-800">
                                                    {{ $so->reference_no ?: 'SO #' . $so->id }} -
                                                    {{ $so->contact->name }}</p>
                                                <p class="text-xs text-slate-500 font-medium mt-1"><i
                                                        data-feather="clock"
                                                        class="w-3 h-3 inline text-yellow-500"></i> Jatuh Tempo:
                                                    {{ \Carbon\Carbon::parse($so->due_date)->translatedFormat('d M Y') }}
                                                </p>
                                            </a> --}}
                                            <button type="button"
                                                onclick="openQuickPay('so', {{ $so->id }}, '{{ $so->reference_no ?: 'SO #' . $so->id }}', {{ $so->total }}, true)"
                                                class="w-full text-left block p-4 border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                                <p class="text-xs font-bold text-blue-600 mb-1">Tagihan Penjualan
                                                    (Piutang)</p>
                                                <p class="text-sm font-semibold text-slate-800">
                                                    {{ $so->reference_no ?: 'SO #' . $so->id }} -
                                                    {{ $so->contact->name }}</p>
                                                <p class="text-xs text-slate-500 font-bold mt-1"><i
                                                        data-feather="clock" class="w-3 h-3 inline"></i> Tempo:
                                                    {{ \Carbon\Carbon::parse($so->due_date)->translatedFormat('d M Y') }}
                                                </p>
                                            </button>
                                        @endforeach
                                    @endif
                                @else
                                    <div
                                        class="p-8 text-center text-slate-500 text-sm font-medium flex flex-col items-center">
                                        <i data-feather="check-circle" class="w-8 h-8 text-emerald-400 mb-2"></i>
                                        Selamat! Tidak ada tagihan mendesak.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- PROFIL -->
                    <a href="{{ route('profile.index') }}" title="Profil Saya"
                        class="bg-slate-100 p-1.5 rounded-full text-slate-500 hover:bg-slate-200 transition-colors block">
                        <i data-feather="user" class="w-5 h-5"></i>
                    </a>
                </div>
            </div>
        </header>

        <main class="flex-1 p-6 overflow-y-auto">
            @if (session('success'))
                <div
                    class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative font-medium shadow-sm flex items-center gap-2">
                    <i data-feather="check-circle" class="w-5 h-5"></i> {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div
                    class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative font-medium shadow-sm flex items-center gap-2">
                    <i data-feather="alert-circle" class="w-5 h-5"></i> {{ session('error') }}
                </div>
            @endif

            @yield('content')

        </main>
    </div>

    <!-- ============================================== -->
    <!-- MODAL GLOBAL: QUICK PAY (PELUNASAN CEPAT)      -->
    <!-- ============================================== -->
    <div id="quickPayModalGlobal" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-[100] p-4 backdrop-blur-sm transition-all">
        <div class="bg-white rounded-xl w-full max-w-md p-6 shadow-2xl overflow-hidden flex flex-col border-t-4 border-blue-600">
            <div class="flex justify-between items-center mb-5">
                <h3 id="qpTitleGlobal" class="text-lg font-bold text-slate-800">Proses Pelunasan Cepat</h3>
                <button type="button" onclick="document.getElementById('quickPayModalGlobal').classList.add('hidden')" class="text-slate-400 hover:text-red-500 bg-slate-100 hover:bg-red-50 rounded-full p-1 transition-colors">
                    <i data-feather="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="#" method="POST" id="quickPayFormGlobal">
                @csrf
                <div class="bg-slate-50 border border-slate-200 p-4 rounded-lg mb-5 text-center">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Total Tagihan</p>
                    <p id="qpAmountGlobal" class="text-2xl font-black text-slate-800"></p>
                    <p class="text-sm font-medium text-slate-600 mt-1">Ref: <b id="qpRefGlobal" class="text-blue-700"></b></p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Pilih Rekening Transaksi</label>
                        <select name="payment_account" required class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500 font-semibold text-slate-700 shadow-sm">
                            <option value="" disabled selected>-- Pilih Kas atau Bank --</option>
                            @foreach($bankAccountsGlobal as $bank)
                                <option value="{{ $bank->code }}">[{{ $bank->code }}] {{ $bank->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Tanggal Pelunasan</label>
                        <input type="date" name="due_date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500 font-medium shadow-sm">
                    </div>
                </div>

                <div class="mt-5 p-3 bg-blue-50 border border-blue-100 rounded-lg text-xs text-blue-800 font-medium text-center">
                    Transaksi ini akan memotong saldo tagihan dan langsung dicatat ke Jurnal Buku Besar Bank terpilih.
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('quickPayModalGlobal').classList.add('hidden')" class="px-5 py-2.5 text-slate-600 font-bold hover:bg-slate-100 rounded-lg transition-colors">Batal</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-bold transition-colors shadow-md">Lunasi Sekarang</button>
                </div>
            </form>
        </div>
    </div>

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

        // FUNGSI QUICK PAY GLOBAL DARI LONCENG NOTIFIKASI
        function openQuickPay(type, id, ref, amount, isSales) {
            let title = isSales ? 'Terima Pembayaran Piutang' : 'Bayar Hutang Pemasok';
            let actionUrl = isSales ? `/sales/${id}/invoice` : `/purchases/${id}/bill`;

            document.getElementById('qpTitleGlobal').innerText = title;
            document.getElementById('qpRefGlobal').innerText = ref;
            document.getElementById('qpAmountGlobal').innerText = 'Rp ' + parseInt(amount, 10).toLocaleString('id-ID');

            document.getElementById('quickPayFormGlobal').action = actionUrl;
            document.getElementById('quickPayModalGlobal').classList.remove('hidden');
        }
    </script>
</body>

</html>
