<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- PWA Settings -->
    <meta name="theme-color" content="#10b981">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="/pwa-192x192.png">
    <link rel="manifest" href="/manifest.json">
    
    <title>Daftar — Dompet Harian</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS Assets -->
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
</head>
<body class="h-full flex items-center justify-center p-4 antialiased selection:bg-emerald-500 selection:text-white" style="font-family: 'Inter', sans-serif;">

    <!-- Background decorative blobs -->
    <div class="absolute inset-0 overflow-hidden -z-10 pointer-events-none">
        <div class="absolute -top-[40%] -right-[20%] w-[80%] h-[80%] rounded-full bg-emerald-500/10 blur-[120px]"></div>
        <div class="absolute -bottom-[30%] -left-[20%] w-[70%] h-[70%] rounded-full bg-teal-500/10 blur-[100px]"></div>
    </div>

    <!-- Register Card Container -->
    <div class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-3xl p-8 shadow-2xl relative overflow-hidden my-8">
        <div class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-500"></div>

        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-12 h-12 bg-emerald-500 text-white rounded-2xl flex items-center justify-center text-2xl mx-auto mb-4 shadow-xl shadow-emerald-500/20">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Buat Akun Baru</h1>
            <p class="text-slate-400 text-xs mt-1">Mulai kendalikan keuangan Anda hari ini secara bijak.</p>
        </div>

        @if ($errors->any())
            <div class="mb-5 p-3.5 bg-rose-950/40 border border-rose-900/40 text-rose-400 text-xs rounded-xl space-y-1">
                @foreach ($errors->all() as $error)
                    <div class="flex items-start gap-2">
                        <i class="fa-solid fa-circle-exclamation mt-0.5 shrink-0"></i>
                        <span>{{ $error }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Nama Lengkap</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                        <i class="fa-regular fa-user"></i>
                    </span>
                    <input type="text" name="name" id="name" required value="{{ old('name') }}" placeholder="Budi Santoso" class="w-full bg-slate-950/50 border border-slate-800 text-slate-200 placeholder-slate-600 rounded-xl py-3 pl-10 pr-4 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition duration-150">
                </div>
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Alamat Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                        <i class="fa-regular fa-envelope"></i>
                    </span>
                    <input type="email" name="email" id="email" required value="{{ old('email') }}" placeholder="namamu@email.com" class="w-full bg-slate-950/50 border border-slate-800 text-slate-200 placeholder-slate-600 rounded-xl py-3 pl-10 pr-4 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition duration-150">
                </div>
            </div>

            <div>
                <label for="payday_day" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Tanggal Gajian Bulanan</label>
                <p class="text-[10px] text-slate-500 mb-2">Digunakan untuk menghitung "Batas Aman Pengeluaran" harian Anda.</p>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                        <i class="fa-regular fa-calendar"></i>
                    </span>
                    <select name="payday_day" id="payday_day" required class="w-full bg-slate-950/50 border border-slate-800 text-slate-300 rounded-xl py-3 pl-10 pr-4 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition duration-150 appearance-none">
                        @for ($i = 1; $i <= 31; $i++)
                            <option value="{{ $i }}" {{ old('payday_day', 25) == $i ? 'selected' : '' }}>Tanggal {{ $i }}</option>
                        @endfor
                    </select>
                    <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 pointer-events-none">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="password" required placeholder="••••••••" class="w-full bg-slate-950/50 border border-slate-800 text-slate-200 placeholder-slate-600 rounded-xl py-3 pl-10 pr-4 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition duration-150">
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Konfirmasi Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="••••••••" class="w-full bg-slate-950/50 border border-slate-800 text-slate-200 placeholder-slate-600 rounded-xl py-3 pl-10 pr-4 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition duration-150">
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 active:scale-[0.98] text-white font-semibold text-sm rounded-xl py-3 shadow-lg shadow-emerald-500/20 transition duration-150 mt-2">
                Daftar Akun
            </button>
        </form>

        <!-- Footer -->
        <div class="mt-8 text-center border-t border-slate-800/80 pt-6">
            <p class="text-slate-500 text-xs">Sudah punya akun? <a href="{{ route('login') }}" class="text-emerald-400 hover:text-emerald-300 font-semibold transition">Masuk di sini</a></p>
        </div>
    </div>

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
