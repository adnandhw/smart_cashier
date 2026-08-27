@extends('layouts.app')

@section('title', 'Pengaturan POS')
@section('page_title', 'Pengaturan Aplikasi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Settings Form Container -->
    <div class="glass-card rounded-[24px] p-6 md:p-8 shadow-xs">
        <div class="pb-5 border-b border-slate-100 dark:border-slate-800/80 mb-6">
            <h3 class="text-base font-extrabold text-slate-800 dark:text-white">Konfigurasi Toko & Sistem</h3>
            <p class="text-xs text-slate-450 dark:text-slate-500 mt-1">Sesuaikan identitas UMKM, printer kasir, dan ambang batas deteksi AI.</p>
        </div>

        <form action="{{ route('settings.save') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                <!-- Left: Logo Uploader (4 Columns) -->
                <div class="md:col-span-4 flex flex-col items-center text-center space-y-4">
                    <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-550 uppercase tracking-wider">Logo Toko</span>
                    
                    <div class="w-28 h-28 rounded-[24px] border border-slate-200/80 dark:border-slate-800 shadow-inner overflow-hidden bg-slate-50 relative flex items-center justify-center group">
                        <img src="{{ asset(\App\Models\Setting::getVal('store_logo', 'logo.png')) }}" id="logo-preview" alt="Store Logo" class="w-full h-full object-contain p-2">
                    </div>

                    <div class="w-full">
                        <label class="px-4 py-2.5 bg-white border border-slate-200 text-slate-600 hover:bg-amber-400 hover:text-white hover:border-amber-400 font-bold rounded-xl cursor-pointer text-xs block transition-all duration-200 shadow-xs hover:shadow-md hover:shadow-amber-500/15">
                            Pilih Logo Baru
                            <input type="file" name="store_logo" onchange="previewLogo(event)" class="hidden">
                        </label>
                        <span class="block text-[9px] text-slate-400 mt-1.5">Format: PNG, JPG (Max. 1MB)</span>
                    </div>
                </div>

                <!-- Right: Store details & printer (8 Columns) -->
                <div class="md:col-span-8 space-y-4">
                    <!-- Store name -->
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama UMKM Toko</label>
                        <input type="text" name="store_name" required value="{{ $settings['store_name'] ?? 'UMKM Aneka Kue Pak Yanto' }}"
                               class="w-full bg-white border border-slate-200 rounded-xl py-2.5 px-3.5 text-xs font-semibold outline-none focus:border-amber-500 focus:bg-white transition-all text-slate-800">
                    </div>

                    <!-- Store address -->
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Alamat Toko</label>
                        <textarea name="store_address" required rows="2"
                                  class="w-full bg-white border border-slate-200 rounded-xl py-2.5 px-3.5 text-xs font-semibold outline-none focus:border-amber-500 focus:bg-white transition-all text-slate-800">{{ $settings['store_address'] ?? 'Jl. Raya Kaliurang KM 10, Sleman, Yogyakarta' }}</textarea>
                    </div>

                    <!-- Thermal printer configuration -->
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Printer Thermal POS</label>
                        <input type="text" name="printer_name" value="{{ $settings['printer_name'] ?? 'POS-80 Thermal Printer' }}"
                               class="w-full bg-white border border-slate-200 rounded-xl py-2.5 px-3.5 text-xs font-semibold outline-none focus:border-amber-500 focus:bg-white transition-all text-slate-800">
                    </div>
                </div>
            </div>

            <!-- AI Engine settings (Threshold Slider & Model name) -->
            <div class="pt-6 border-t border-slate-100 space-y-4" x-data="{ sliderVal: {{ $settings['confidence_threshold'] ?? 0.90 }} }">
                <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="cpu" class="w-4 h-4 text-amber-500"></i> AI Detection Settings
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Confidence Threshold slider -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-[11px] font-bold">
                            <span class="text-slate-400 uppercase tracking-wider">Threshold Akurasi (Auto-Add)</span>
                            <span class="text-amber-600 font-mono text-xs" x-text="Math.round(sliderVal * 100) + '%'"></span>
                        </div>
                        <input type="range" name="confidence_threshold" min="0.50" max="0.95" step="0.05" x-model="sliderVal"
                               class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-amber-500">
                        <span class="block text-[10px] text-slate-450 font-semibold leading-normal">Kue terdeteksi dengan akurasi di atas ambang batas ini akan dimasukkan langsung ke keranjang belanja POS otomatis.</span>
                    </div>

                    <!-- AI Model details -->
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Aktif YOLOv8 Model File</label>
                        <div class="w-full bg-slate-50 border border-slate-200/50 py-2.5 px-3.5 rounded-xl font-mono text-xs text-slate-600 font-bold flex items-center justify-between">
                            <span>best.onnx (12 MB)</span>
                            <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 border border-emerald-500/15 uppercase tracking-wide">Loaded</span>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Save Form Buttons -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 mt-6 flex justify-end gap-3 shrink-0">
                <button type="button" onclick="window.history.back()" class="px-5 py-2.5 rounded-xl border border-slate-200 hover:bg-amber-400 hover:text-white hover:border-amber-400 text-xs font-bold text-slate-600 transition-all">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-white rounded-xl text-xs font-bold shadow-md shadow-amber-500/10 hover:shadow-amber-500/25 transition-all">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Init icons
        lucide.createIcons();
    });

    // Preview uploaded logo image dynamically
    function previewLogo(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('logo-preview').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }


</script>
@endsection
