<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Smart Cashier AI</title>
    
    <!-- Favicon / Tab Icon -->
    <link rel="icon" type="image/png" href="{{ asset(\App\Models\Setting::getVal('store_logo', 'logo.png')) }}">
    
    <!-- Google Fonts (Poppins & Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #FAFAFC;
        }
        h1, h2, h3, .font-display {
            font-family: 'Poppins', sans-serif;
        }
        /* Premium Card style on Snow White background */
        .glass-login {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.04);
        }
        .dark .glass-login {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.35);
        }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 bg-[#FAFAFC] dark:bg-slate-950 overflow-hidden">

    <div class="w-full max-w-md relative z-10 transition-all duration-500 ease-out transform translate-y-0"
         x-data="{ showPass: false }">
        
        <!-- Sparkles background deco -->
        <div class="absolute -top-12 -left-12 w-48 h-48 bg-amber-500/25 rounded-full filter blur-3xl opacity-60"></div>
        <div class="absolute -bottom-12 -right-12 w-48 h-48 bg-emerald-500/25 rounded-full filter blur-3xl opacity-60"></div>
        
        <!-- Main Form Glass Container -->
        <div class="glass-login rounded-[24px] p-8 md:p-10">
            <!-- Brand Logo -->
            <div class="flex flex-col items-center justify-center text-center mb-6">
                <div class="w-28 h-28 rounded-full overflow-hidden border border-slate-200/80 bg-white shadow-lg shadow-amber-500/10 mb-3 flex items-center justify-center p-1">
                    <img src="{{ asset(\App\Models\Setting::getVal('store_logo', 'logo.png')) }}" alt="Logo" class="w-full h-full object-contain">
                </div>
                <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight leading-tight">Smart Cashier AI</h2>
                <p class="text-xs text-slate-900 dark:text-slate-900 font-bold uppercase mt-1 tracking-wider">Aneka Kue Pak Yanto</p>
            </div>

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="mb-5 p-4 rounded-2xl border border-rose-500/10 bg-rose-500/10 text-rose-600 dark:text-rose-450 text-xs font-semibold space-y-1">
                    @foreach ($errors->all() as $error)
                        <div class="flex items-center gap-2">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
                            <span>{{ $error }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf
                
                <!-- Email Input -->
                <div class="space-y-1.5">
                    <label for="email" class="block text-xs font-bold text-slate-900 dark:text-slate-900 uppercase tracking-wider">Alamat Email</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                            <i data-lucide="mail" class="w-4.5 h-4.5"></i>
                        </span>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                               placeholder="email@toko.com"
                               onclick="if(!this.dataset.cleared) { this.value = ''; this.dataset.cleared = '1'; }"
                               class="w-full bg-white border border-slate-200/80 rounded-2xl py-3.5 pl-12 pr-4 text-sm font-semibold tracking-wide outline-none transition-all text-slate-900 focus:bg-white focus:border-amber-500">
                    </div>
                </div>

                <!-- Password Input -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-xs font-bold text-slate-900 dark:text-slate-900 uppercase tracking-wider">Kata Sandi</label>
                        <a href="#" class="text-xs font-bold text-amber-650 hover:text-amber-500 transition-colors">Lupa Password?</a>
                    </div>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                            <i data-lucide="lock" class="w-4.5 h-4.5"></i>
                        </span>
                        <input type="password" name="password" id="password" required
                               placeholder="••••••••"
                               onclick="if(!this.dataset.cleared) { this.value = ''; this.dataset.cleared = '1'; }"
                               class="w-full bg-white border border-slate-200/80 rounded-2xl py-3.5 pl-12 pr-12 text-sm font-semibold tracking-wide outline-none transition-all text-slate-900 focus:bg-white focus:border-amber-500">
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                            <i id="eye-icon" data-lucide="eye" class="w-4.5 h-4.5"></i>
                        </button>
                    </div>
                </div>


                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full py-4 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-white rounded-2xl font-bold text-sm tracking-wide shadow-lg shadow-amber-500/20 hover:shadow-amber-500/35 transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2">
                    <span>Masuk ke Kasir</span>
                    <i data-lucide="arrow-right" class="w-4.5 h-4.5"></i>
                </button>
            </form>
        </div>

        <!-- Footer Info -->
        <div class="text-center mt-6 text-xs text-slate-550 dark:text-slate-500 font-medium">
            <p>© 2026 UMKM Aneka Kue Pak Yanto. All Rights Reserved.</p>
        </div>
    </div>

    <script>
        // Init Lucide
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });

        function togglePasswordVisibility() {
            const input = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                input.type = 'password';
                eyeIcon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }
    </script>
</body>
</html>
