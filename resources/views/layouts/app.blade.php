<!DOCTYPE html>
@php 
    $themeMode = \App\Models\Setting::getVal('theme_mode', 'light'); 
    $storeName = \App\Models\Setting::getVal('store_name', 'Aneka Kue Pak Yanto'); 
    $storeLogo = \App\Models\Setting::getVal('store_logo', 'logo.png');
@endphp
<html lang="id" class="h-full scroll-smooth {{ $themeMode == 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Aneka Kue Pak Yanto') - Smart Cashier AI</title>
    
    <!-- Favicon / Tab Icon -->
    <link rel="icon" type="image/png" href="{{ asset($storeLogo) }}">
    
    <!-- Google Fonts (Poppins & Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        // Force light mode (Snow White Theme)
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    </script>

    <style>
        html {
            color-scheme: light !important;
        }
        body {
            font-family: 'Inter', sans-serif;
        }
        /* Auto-darken light slate utilities for ultimate readability on Snow White background */
        .text-slate-400 {
            color: #64748B !important; /* maps slate-400 to slate-500 */
        }
        .text-slate-450, .text-slate-500 {
            color: #475569 !important; /* maps slate-450/500 to slate-600 */
        }
        .text-slate-550 {
            color: #334155 !important; /* maps slate-550 to slate-700 */
        }
        .text-slate-850 {
            color: #1E293B !important; /* maps slate-850 to slate-800 for visibility */
        }
        h1, h2, h3, h4, h5, h6, .font-display {
            font-family: 'Poppins', sans-serif;
        }
        h1, h2, h3, h4, h5, h6 {
            color: #1E293B !important;
        }
        /* Premium toggle switch slider */
        .switch-container {
            position: relative;
            display: inline-block;
            width: 32px;
            height: 18px;
            flex-shrink: 0;
        }
        .switch-input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .switch-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #FFFFFF;
            border: 1.5px solid #CBD5E1;
            transition: .3s;
            border-radius: 999px;
        }
        .switch-slider:before {
            position: absolute;
            content: "";
            height: 12px;
            width: 12px;
            left: 2px;
            bottom: 1.5px;
            background-color: #64748B;
            transition: .3s;
            border-radius: 50%;
        }
        .switch-input:checked + .switch-slider {
            background-color: #FFFFFF;
            border-color: #475569;
        }
        .switch-input:checked + .switch-slider:before {
            transform: translateX(14px);
            background-color: #1E293B;
        }

        /* Custom premium scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.25);
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(156, 163, 175, 0.45);
        }
        
        /* Glassmorphism elements */
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
        .dark .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03);
        }
        .glass-sidebar {
            background: #FFFFFF !important;
            border-right: 1px solid #E2E8F0 !important;
            height: 100vh !important;
            min-height: 100vh !important;
        }
        .dark .glass-sidebar {
            background: #FFFFFF !important;
            border-right: 1px solid #E2E8F0 !important;
            height: 100vh !important;
            min-height: 100vh !important;
        }

        /* Prevent SweetAlert2 from collapsing body/sidebar height */
        html, body {
            background-color: #FAFAFC !important;
            min-height: 100vh !important;
        }
        html.swal2-shown:not(.swal2-toast-shown), 
        body.swal2-shown:not(.swal2-toast-shown) {
            height: 100% !important;
            min-height: 100vh !important;
            overflow: hidden !important;
            background-color: #FAFAFC !important;
        }

        /* Modal Backdrop: Only apply overlay blur for centered modals */
        .swal2-container.swal2-center:not(.swal2-toast-container) {
            background-color: rgba(15, 23, 42, 0.35) !important;
            backdrop-filter: blur(3px);
            -webkit-backdrop-filter: blur(3px);
        }

        /* Toast Notifications: Transparent background, NO blur, click-through */
        .swal2-container.swal2-top-end,
        .swal2-container.swal2-top,
        .swal2-container.swal2-top-start,
        .swal2-container.swal2-bottom-end,
        .swal2-toast-container,
        body.swal2-toast-shown .swal2-container {
            background-color: transparent !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            pointer-events: none !important;
        }

        body.swal2-toast-shown {
            overflow: auto !important;
        }

        .swal2-toast {
            pointer-events: auto !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05) !important;
            border: 1px solid #E2E8F0 !important;
            border-radius: 16px !important;
        }

        .swal2-popup {
            background-color: #FFFFFF !important;
            color: #1E293B !important;
        }

        /* Fix for mobile browsers: address bar causes 100vh to be too tall */
        @media (max-width: 767px) {
            html, body {
                height: 100svh !important;
                min-height: 100svh !important;
            }
        }
    </style>
</head>
<body class="h-full flex overflow-hidden bg-[#FAFAFC] text-slate-800 transition-colors duration-300" x-data="{ sidebarOpen: true, mobileSidebarOpen: false, isDark: false }">

    <!-- Sidebar Backdrop for Mobile -->
    <div x-show="mobileSidebarOpen" 
         @click="mobileSidebarOpen = false" 
         class="fixed inset-0 bg-slate-950/40 backdrop-blur-xs z-40 md:hidden"
         style="display: none;"
         x-transition.opacity></div>

    <!-- Collapsible Sidebar -->
    <aside class="fixed md:relative inset-y-0 left-0 flex-shrink-0 glass-sidebar flex flex-col h-full z-50 md:z-20 transition-all duration-300" 
           :class="[
               sidebarOpen ? 'w-64' : 'w-20',
               mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'
           ]">
        
        <!-- Header Logo -->
        <div class="h-16 flex items-center px-4 border-b border-slate-200/80 justify-between shrink-0">
            <div class="flex items-center gap-3 overflow-hidden">
                <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center transition-all duration-300">
                    <img src="{{ asset($storeLogo) }}" alt="Logo" class="w-full h-full object-contain drop-shadow-sm">
                </div>
                <div class="min-w-0 transition-opacity duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">
                    <span class="block text-xs font-extrabold tracking-wider text-slate-400 dark:text-slate-500 uppercase leading-none mb-1">SMART POS</span>
                    <span class="block text-[13.2px] tracking-tight font-black bg-gradient-to-r from-amber-600 to-amber-500 dark:from-amber-400 dark:to-amber-300 bg-clip-text text-transparent truncate leading-tight">{{ $storeName }}</span>
                </div>
            </div>
        </div>

        <!-- Sidebar Navigation Menu -->
        <div class="flex-1 overflow-y-auto px-3 py-4 space-y-1.5 scrollbar-thin">
            <!-- Nav Item: Dashboard -->
            <a href="{{ route('dashboard') }}" @click="mobileSidebarOpen = false"
               class="flex items-center px-3.5 py-3 text-sm font-bold rounded-xl transition-all duration-200 group {{ Route::is('dashboard') ? 'bg-amber-500 text-white shadow-md shadow-amber-500/25' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5 shrink-0 {{ Route::is('dashboard') ? 'text-white' : 'text-slate-700 group-hover:text-slate-900' }}"></i>
                <span class="ml-3.5 truncate font-bold transition-opacity" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Dashboard</span>
            </a>

            <!-- Nav Item: Kasir AI -->
            <a href="{{ route('pos') }}" @click="mobileSidebarOpen = false"
               class="flex items-center px-3.5 py-3 text-sm font-bold rounded-xl transition-all duration-200 group {{ Route::is('pos') ? 'bg-amber-500 text-white shadow-md shadow-amber-500/25' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                <i data-lucide="scan-line" class="w-5 h-5 shrink-0 {{ Route::is('pos') ? 'text-white' : 'text-slate-700 group-hover:text-slate-900' }}"></i>
                <span class="ml-3.5 truncate font-bold transition-opacity" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Kasir AI</span>
                <span class="ml-auto flex h-2 w-2 relative" x-show="sidebarOpen">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
            </a>

            <!-- Nav Item: AI Monitor -->
            <a href="{{ route('ai') }}" @click="mobileSidebarOpen = false"
               class="flex items-center px-3.5 py-3 text-sm font-bold rounded-xl transition-all duration-200 group {{ Route::is('ai') ? 'bg-amber-500 text-white shadow-md shadow-amber-500/25' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                <i data-lucide="cpu" class="w-5 h-5 shrink-0 {{ Route::is('ai') ? 'text-white' : 'text-slate-700 group-hover:text-slate-900' }}"></i>
                <span class="ml-3.5 truncate font-bold transition-opacity" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Deteksi Produk AI</span>
            </a>

            <div class="h-px bg-slate-200/80 my-3 mx-2"></div>

            <!-- Nav Item: Produk -->
            <a href="{{ route('products') }}" @click="mobileSidebarOpen = false"
               class="flex items-center px-3.5 py-3 text-sm font-bold rounded-xl transition-all duration-200 group {{ Route::is('products') ? 'bg-amber-500 text-white shadow-md shadow-amber-500/25' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                <i data-lucide="package" class="w-5 h-5 shrink-0 {{ Route::is('products') ? 'text-white' : 'text-slate-700 group-hover:text-slate-900' }}"></i>
                <span class="ml-3.5 truncate font-bold transition-opacity" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Produk</span>
            </a>

            <!-- Nav Item: Stok -->
            <a href="{{ route('stok') }}" @click="mobileSidebarOpen = false"
               class="flex items-center px-3.5 py-3 text-sm font-bold rounded-xl transition-all duration-200 group {{ Route::is('stok') ? 'bg-amber-500 text-white shadow-md shadow-amber-500/25' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                <i data-lucide="trending-down" class="w-5 h-5 shrink-0 {{ Route::is('stok') ? 'text-white' : 'text-slate-700 group-hover:text-slate-900' }}"></i>
                <span class="ml-3.5 truncate font-bold transition-opacity" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Stok</span>
            </a>

            <!-- Nav Item: Transaksi -->
            <a href="{{ route('transactions') }}" @click="mobileSidebarOpen = false"
               class="flex items-center px-3.5 py-3 text-sm font-bold rounded-xl transition-all duration-200 group {{ Route::is('transactions') ? 'bg-amber-500 text-white shadow-md shadow-amber-500/25' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                <i data-lucide="receipt" class="w-5 h-5 shrink-0 {{ Route::is('transactions') ? 'text-white' : 'text-slate-700 group-hover:text-slate-900' }}"></i>
                <span class="ml-3.5 truncate font-bold transition-opacity" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Transaksi</span>
            </a>

            <!-- Nav Item: Laporan -->
            <a href="{{ route('reports') }}" @click="mobileSidebarOpen = false"
               class="flex items-center px-3.5 py-3 text-sm font-bold rounded-xl transition-all duration-200 group {{ Route::is('reports') ? 'bg-amber-500 text-white shadow-md shadow-amber-500/25' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                <i data-lucide="bar-chart-3" class="w-5 h-5 shrink-0 {{ Route::is('reports') ? 'text-white' : 'text-slate-700 group-hover:text-slate-900' }}"></i>
                <span class="ml-3.5 truncate font-bold transition-opacity" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Laporan</span>
            </a>

            <!-- Nav Item: Pengguna -->
            <a href="{{ route('users') }}" @click="mobileSidebarOpen = false"
               class="flex items-center px-3.5 py-3 text-sm font-bold rounded-xl transition-all duration-200 group {{ Route::is('users') ? 'bg-amber-500 text-white shadow-md shadow-amber-500/25' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                <i data-lucide="users" class="w-5 h-5 shrink-0 {{ Route::is('users') ? 'text-white' : 'text-slate-700 group-hover:text-slate-900' }}"></i>
                <span class="ml-3.5 truncate font-bold transition-opacity" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Pengguna</span>
            </a>

            <!-- Nav Item: Pengaturan -->
            <a href="{{ route('settings') }}" @click="mobileSidebarOpen = false"
               class="flex items-center px-3.5 py-3 text-sm font-bold rounded-xl transition-all duration-200 group {{ Route::is('settings') ? 'bg-amber-500 text-white shadow-md shadow-amber-500/25' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                <i data-lucide="settings" class="w-5 h-5 shrink-0 {{ Route::is('settings') ? 'text-white' : 'text-slate-700 group-hover:text-slate-900' }}"></i>
                <span class="ml-3.5 truncate font-bold transition-opacity" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Pengaturan</span>
            </a>
        </div>

        <!-- Sidebar Footer -->
        <div class="p-3 border-t border-slate-200/80 bg-white shrink-0">

            <!-- User Widget -->
            <div class="mt-3 flex items-center gap-3 p-1.5 overflow-hidden">
                <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-600 flex items-center justify-center font-bold text-sm shrink-0">
                    {{ strtoupper(substr(Auth::user()->name ?? 'K', 0, 2)) }}
                </div>
                <div class="min-w-0 transition-opacity" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 h-0 overflow-hidden'">
                    <span class="block text-xs font-extrabold text-slate-900 truncate leading-tight">{{ Auth::user()->name }}</span>
                    <span class="block text-[10px] text-slate-600 font-bold truncate mt-0.5">Cashier</span>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Panel -->
    <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden bg-[#FAFAFC] dark:bg-[#FAFAFC] transition-colors">
        
        <!-- Top bar Header -->
        <header class="h-16 border-b border-slate-200/80 px-6 flex items-center justify-between bg-white/70 dark:bg-white/70 backdrop-blur-md z-10 shrink-0">
            <div class="flex items-center gap-4">
                <!-- Hamburger Menu Button for Mobile -->
                <button @click.stop="mobileSidebarOpen = !mobileSidebarOpen" 
                        class="p-2 rounded-xl border border-slate-200 text-slate-600 md:hidden hover:bg-slate-50 transition-all">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <h1 class="text-lg font-bold font-display text-slate-800 dark:text-slate-800 tracking-tight">
                    @yield('page_title', 'Dashboard')
                </h1>
            </div>
            
            <div class="flex items-center gap-4">
                <!-- Clock Widget -->
                <div id="clock-widget" class="hidden md:block text-xs font-semibold tracking-wider text-slate-500 dark:text-slate-500 font-mono bg-slate-100/80 dark:bg-slate-100/80 px-3.5 py-2 border border-slate-200/50 dark:border-slate-200 rounded-xl">
                    19 JUL 2026, 00:00:00
                </div>

                <!-- Log Out -->
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="p-2.5 rounded-xl border border-slate-200/60 dark:border-slate-200 hover:bg-slate-100 dark:hover:bg-slate-100 text-slate-450 hover:text-rose-600 dark:hover:text-rose-600 transition-all" title="Keluar">
                        <i data-lucide="log-out" class="w-4.5 h-4.5"></i>
                    </button>
                </form>
            </div>
        </header>

        <!-- Main Body Wrapper -->
        <main class="flex-1 overflow-y-auto p-6 relative">
            @yield('content')
        </main>

        <!-- Dynamic Page Footer -->
        <footer class="min-h-[40px] py-3 lg:py-0 border-t border-slate-100 dark:border-slate-200 px-4 lg:px-6 bg-white dark:bg-white shrink-0 flex items-center justify-center text-[11px] text-slate-400 dark:text-slate-400 font-medium">
            <div class="text-center leading-relaxed">
                © 2026 <span class="font-bold text-slate-600 dark:text-slate-400">UMKM Aneka Kue Pak Yanto</span>. <span class="block sm:inline mt-0.5 sm:mt-0">Powered by computer vision technology.</span>
            </div>
        </footer>
    </div>

    <!-- Alert / Toast Notifications System -->
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });
                Toast.fire({
                    icon: 'success',
                    title: "{{ session('success') }}"
                });
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4500,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });
                Toast.fire({
                    icon: 'error',
                    title: "{{ session('error') }}"
                });
            });
        </script>
    @endif

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });
                Toast.fire({
                    icon: 'error',
                    title: "{{ $errors->first() }}"
                });
            });
        </script>
    @endif


    <!-- Time Clock Script -->
    <script>
        function updateTimeClock() {
            const el = document.getElementById('clock-widget');
            if (el) {
                const now = new Date();
                const days = ['MIN', 'SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB'];
                const months = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];
                
                const day = days[now.getDay()];
                const date = String(now.getDate()).padStart(2, '0');
                const month = months[now.getMonth()];
                const year = now.getFullYear();
                
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                
                el.innerText = `${day}, ${date} ${month} ${year} | ${hours}:${minutes}:${seconds}`;
            }
        }
        setInterval(updateTimeClock, 1000);
        updateTimeClock();

        // Refresh icons globally using Lucide script
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>

    @yield('scripts')
</body>
</html>
