<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- PWA Settings -->
    <meta name="theme-color" content="#10b981">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="/pwa-192x192.png">
    <link rel="manifest" href="/manifest.json">
    
    <title>@yield('title', 'Dompet Harian') — Kelola Keuangan Pribadi</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS & JS Assets -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        .active-nav {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 600;
            border-left: 4px solid #10b981;
        }
        .active-mobile-nav {
            color: #10b981;
        }
    </style>
</head>
<body class="text-slate-800 antialiased min-h-screen flex flex-col">

    <!-- Wrapper -->
    <div class="flex flex-1 min-h-screen">
        
        <!-- SIDEBAR MOBILE DRAWER -->
        <!-- Backdrop -->
        <div id="mobile-sidebar-backdrop" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[100] transition-opacity duration-300 opacity-0 pointer-events-none lg:hidden"></div>
        
        <!-- Drawer -->
        <aside id="mobile-sidebar-drawer" class="fixed inset-y-0 left-0 w-72 bg-slate-900 text-slate-300 z-[100] flex flex-col transform -translate-x-full transition-transform duration-300 ease-in-out lg:hidden shadow-2xl">
            <div class="p-6 flex items-center justify-between border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center text-white text-xl shadow-lg shadow-emerald-900/30">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <div>
                        <h1 class="font-bold text-white tracking-wide text-lg">Dompet Harian</h1>
                        <span class="text-xs text-slate-500">Pencatat Keuangan</span>
                    </div>
                </div>
                <button id="mobile-sidebar-close" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-white transition duration-150 rounded-xl hover:bg-slate-800">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            
            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition duration-200 hover:bg-slate-800 hover:text-white {{ Route::is('dashboard') ? 'bg-slate-800 text-emerald-400 font-medium' : '' }}">
                    <i class="fa-solid fa-chart-pie w-5 text-center text-lg"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('transactions.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition duration-200 hover:bg-slate-800 hover:text-white {{ Route::is('transactions.index') ? 'bg-slate-800 text-emerald-400 font-medium' : '' }}">
                    <i class="fa-solid fa-money-bill-transfer w-5 text-center text-lg"></i>
                    <span>Riwayat Transaksi</span>
                </a>
                <a href="{{ route('wallets.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition duration-200 hover:bg-slate-800 hover:text-white {{ Route::is('wallets.index') ? 'bg-slate-800 text-emerald-400 font-medium' : '' }}">
                    <i class="fa-solid fa-vault w-5 text-center text-lg"></i>
                    <span>Dompetku</span>
                </a>
                <a href="{{ route('categories.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition duration-200 hover:bg-slate-800 hover:text-white {{ Route::is('categories.index') ? 'bg-slate-800 text-emerald-400 font-medium' : '' }}">
                    <i class="fa-solid fa-tags w-5 text-center text-lg"></i>
                    <span>Kategori</span>
                </a>
                <a href="{{ route('budgets.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition duration-200 hover:bg-slate-800 hover:text-white {{ Route::is('budgets.index') ? 'bg-slate-800 text-emerald-400 font-medium' : '' }}">
                    <i class="fa-solid fa-sliders w-5 text-center text-lg"></i>
                    <span>Budget Bulanan</span>
                </a>
                <a href="{{ route('debts.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition duration-200 hover:bg-slate-800 hover:text-white {{ Route::is('debts.index') ? 'bg-slate-800 text-emerald-400 font-medium' : '' }}">
                    <i class="fa-solid fa-hand-holding-dollar w-5 text-center text-lg"></i>
                    <span>Hutang & Cicilan</span>
                </a>
                <a href="{{ route('savings.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition duration-200 hover:bg-slate-800 hover:text-white {{ Route::is('savings.index') ? 'bg-slate-800 text-emerald-400 font-medium' : '' }}">
                    <i class="fa-solid fa-piggy-bank w-5 text-center text-lg"></i>
                    <span>Target Tabungan</span>
                </a>
                <a href="{{ route('laporan') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition duration-200 hover:bg-slate-800 hover:text-white {{ Route::is('laporan') ? 'bg-slate-800 text-emerald-400 font-medium' : '' }}">
                    <i class="fa-solid fa-file-invoice-dollar w-5 text-center text-lg"></i>
                    <span>Laporan</span>
                </a>
            </nav>
            
            <!-- User Footer in Sidebar (pb-28 prevents collision with standard mobile notches / virtual home bars / browser bars) -->
            <div class="p-4 pb-28 border-t border-slate-800 flex flex-col gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-slate-700 text-white rounded-lg flex items-center justify-center font-bold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                
                <div class="flex gap-2">
                    <a href="{{ route('profile.edit') }}" class="flex-1 py-1.5 px-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-medium rounded-lg text-center transition">
                        <i class="fa-solid fa-user-gear mr-1"></i>Profil
                    </a>
                    
                    <form action="{{ route('logout') }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full py-1.5 px-2 bg-red-950/30 hover:bg-red-900/40 text-red-400 text-xs font-medium rounded-lg transition text-center">
                            <i class="fa-solid fa-right-from-bracket mr-1"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </aside>
        
        <!-- SIDEBAR DESKTOP -->
        <aside class="hidden lg:flex flex-col w-64 bg-slate-900 text-slate-300 border-r border-slate-850 sticky top-0 h-screen shrink-0">
            <div class="p-6 flex items-center gap-3 border-b border-slate-800">
                <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center text-white text-xl shadow-lg shadow-emerald-900/30">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div>
                    <h1 class="font-bold text-white tracking-wide text-lg">Dompet Harian</h1>
                    <span class="text-xs text-slate-500">Pencatat Keuangan</span>
                </div>
            </div>
            
            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition duration-200 hover:bg-slate-800 hover:text-white {{ Route::is('dashboard') ? 'bg-slate-800 text-emerald-400 font-medium' : '' }}">
                    <i class="fa-solid fa-chart-pie w-5 text-center text-lg"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('transactions.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition duration-200 hover:bg-slate-800 hover:text-white {{ Route::is('transactions.index') ? 'bg-slate-800 text-emerald-400 font-medium' : '' }}">
                    <i class="fa-solid fa-money-bill-transfer w-5 text-center text-lg"></i>
                    <span>Riwayat Transaksi</span>
                </a>
                <a href="{{ route('wallets.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition duration-200 hover:bg-slate-800 hover:text-white {{ Route::is('wallets.index') ? 'bg-slate-800 text-emerald-400 font-medium' : '' }}">
                    <i class="fa-solid fa-vault w-5 text-center text-lg"></i>
                    <span>Dompetku</span>
                </a>
                <a href="{{ route('categories.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition duration-200 hover:bg-slate-800 hover:text-white {{ Route::is('categories.index') ? 'bg-slate-800 text-emerald-400 font-medium' : '' }}">
                    <i class="fa-solid fa-tags w-5 text-center text-lg"></i>
                    <span>Kategori</span>
                </a>
                <a href="{{ route('budgets.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition duration-200 hover:bg-slate-800 hover:text-white {{ Route::is('budgets.index') ? 'bg-slate-800 text-emerald-400 font-medium' : '' }}">
                    <i class="fa-solid fa-sliders w-5 text-center text-lg"></i>
                    <span>Budget Bulanan</span>
                </a>
                <a href="{{ route('debts.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition duration-200 hover:bg-slate-800 hover:text-white {{ Route::is('debts.index') ? 'bg-slate-800 text-emerald-400 font-medium' : '' }}">
                    <i class="fa-solid fa-hand-holding-dollar w-5 text-center text-lg"></i>
                    <span>Hutang & Cicilan</span>
                </a>
                <a href="{{ route('savings.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition duration-200 hover:bg-slate-800 hover:text-white {{ Route::is('savings.index') ? 'bg-slate-800 text-emerald-400 font-medium' : '' }}">
                    <i class="fa-solid fa-piggy-bank w-5 text-center text-lg"></i>
                    <span>Target Tabungan</span>
                </a>
                <a href="{{ route('laporan') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition duration-200 hover:bg-slate-800 hover:text-white {{ Route::is('laporan') ? 'bg-slate-800 text-emerald-400 font-medium' : '' }}">
                    <i class="fa-solid fa-file-invoice-dollar w-5 text-center text-lg"></i>
                    <span>Laporan</span>
                </a>
            </nav>
            
            <!-- User Footer in Sidebar -->
            <div class="p-4 border-t border-slate-800 flex flex-col gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-slate-700 text-white rounded-lg flex items-center justify-center font-bold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                
                <div class="flex gap-2">
                    <a href="{{ route('profile.edit') }}" class="flex-1 py-1.5 px-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-medium rounded-lg text-center transition">
                        <i class="fa-solid fa-user-gear mr-1"></i>Profil
                    </a>
                    
                    <form action="{{ route('logout') }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full py-1.5 px-2 bg-red-950/30 hover:bg-red-900/40 text-red-400 text-xs font-medium rounded-lg transition text-center">
                            <i class="fa-solid fa-right-from-bracket mr-1"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </aside>
        
        <!-- MAIN CONTENT CONTAINER -->
        <main class="flex-1 flex flex-col min-w-0 pb-24 lg:pb-0">
            <!-- TOP BAR (Mobile only) -->
            <header class="lg:hidden flex items-center justify-between px-4 py-3 bg-white/95 backdrop-blur-md border-b border-slate-100 sticky top-0 z-40">
                <div class="flex items-center gap-3">
                    <button id="mobile-sidebar-toggle" class="w-10 h-10 flex items-center justify-center text-slate-600 hover:bg-slate-50 rounded-xl transition duration-150">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 truncate max-w-[180px]">@yield('page-title', 'Dashboard')</h2>
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    <a href="{{ route('transactions.create') }}" class="w-9 h-9 bg-emerald-500 text-white rounded-lg flex items-center justify-center shadow-md shadow-emerald-100 transition active:scale-95">
                        <i class="fa-solid fa-plus text-sm"></i>
                    </a>
                </div>
            </header>

            <!-- TOP BAR (Desktop only) -->
            <header class="hidden lg:flex items-center justify-between px-8 py-5 bg-white border-b border-slate-100">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">@yield('page-title', 'Dashboard')</h2>
                    <p class="text-xs text-slate-500">Kelola kondisi keuangan Anda dengan bijak hari ini.</p>
                </div>
                
                <div class="flex items-center gap-4">
                    <a href="{{ route('transactions.create') }}" class="flex items-center gap-2 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-medium shadow-md shadow-emerald-100 transition duration-150">
                        <i class="fa-solid fa-plus-circle"></i>
                        <span>Catat Transaksi</span>
                    </a>
                </div>
            </header>
            
            <!-- Dynamic Main Content Section -->
            <div class="flex-1 p-4 lg:p-8">
                
                <!-- Alert/Notifications -->
                @if (session('success'))
                    <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-xl flex items-center justify-between shadow-sm animate-fade-in">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                            <span class="text-sm font-medium">{{ session('success') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-600">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-r-xl flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-exclamation text-rose-500 text-lg"></i>
                            <span class="text-sm font-medium">{{ session('error') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-600">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="mb-6 p-4 bg-amber-50 border-l-4 border-amber-500 text-amber-800 rounded-r-xl flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-info text-amber-500 text-lg"></i>
                            <span class="text-sm font-medium">{{ session('warning') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-amber-400 hover:text-amber-600">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                @endif
                
                @yield('content')
            </div>
        </main>
        
    </div>

    <!-- BOTTOM NAVIGATION (Mobile-friendly) -->
    <div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-slate-100 px-6 py-2.5 flex items-center justify-between z-50 shadow-2xl">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 text-slate-400 hover:text-slate-700 {{ Route::is('dashboard') ? 'text-emerald-500 active-mobile-nav' : '' }}">
            <i class="fa-solid fa-chart-pie text-lg"></i>
            <span class="text-[10px] font-medium">Dashboard</span>
        </a>
        <a href="{{ route('transactions.index') }}" class="flex flex-col items-center gap-1 text-slate-400 hover:text-slate-700 {{ Route::is('transactions.index') ? 'text-emerald-500 active-mobile-nav' : '' }}">
            <i class="fa-solid fa-receipt text-lg"></i>
            <span class="text-[10px] font-medium">Riwayat</span>
        </a>
        
        <!-- Prominent floating Catat button -->
        <a href="{{ route('transactions.create') }}" class="relative -top-5 w-14 h-14 bg-emerald-500 text-white rounded-full flex items-center justify-center shadow-lg shadow-emerald-500/40 hover:bg-emerald-600 transition active:scale-95 transform">
            <i class="fa-solid fa-plus text-2xl"></i>
        </a>
        
        <a href="{{ route('laporan') }}" class="flex flex-col items-center gap-1 text-slate-400 hover:text-slate-700 {{ Route::is('laporan') ? 'text-emerald-500 active-mobile-nav' : '' }}">
            <i class="fa-solid fa-file-lines text-lg"></i>
            <span class="text-[10px] font-medium">Laporan</span>
        </a>
        <a href="{{ route('profile.edit') }}" class="flex flex-col items-center gap-1 text-slate-400 hover:text-slate-700 {{ Route::is('profile.edit') ? 'text-emerald-500 active-mobile-nav' : '' }}">
            <i class="fa-solid fa-user-gear text-lg"></i>
            <span class="text-[10px] font-medium">Profil</span>
        </a>
    </div>

    <!-- MOBILE SIDEBAR TOGGLE SCRIPT -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('mobile-sidebar-toggle');
            const closeBtn = document.getElementById('mobile-sidebar-close');
            const backdrop = document.getElementById('mobile-sidebar-backdrop');
            const drawer = document.getElementById('mobile-sidebar-drawer');

            function openSidebar() {
                backdrop.classList.remove('opacity-0', 'pointer-events-none');
                backdrop.classList.add('opacity-100', 'pointer-events-auto');
                drawer.classList.remove('-translate-x-full');
                drawer.classList.add('translate-x-0');
                document.body.classList.add('overflow-hidden');
            }

            function closeSidebar() {
                backdrop.classList.add('opacity-0', 'pointer-events-none');
                backdrop.classList.remove('opacity-100', 'pointer-events-auto');
                drawer.classList.add('-translate-x-full');
                drawer.classList.remove('translate-x-0');
                document.body.classList.remove('overflow-hidden');
            }

            if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (backdrop) backdrop.addEventListener('click', closeSidebar);
        });
    </script>

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('Service Worker registered successfully!', reg.scope))
                    .catch(err => console.error('Service Worker registration failed:', err));
            });
        }
    </script>

</body>
</html>
