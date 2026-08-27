@extends('layouts.app')

@section('title', 'Pemantauan Stok')
@section('page_title', 'Stok Barang & Inventaris')

@section('content')
<div class="space-y-6" x-data="{ searchQuery: '' }">
    
    <!-- Header Summary Widget -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="glass-card rounded-[22px] p-5 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold text-slate-800 uppercase tracking-wider">Total Jenis Kue</span>
                <span class="block text-2xl font-black text-slate-950 mt-1 leading-none">{{ $products->count() }} Jenis</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-700 flex items-center justify-center">
                <i data-lucide="package" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="glass-card rounded-[22px] p-5 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold text-slate-800 uppercase tracking-wider">Total Stok Tersedia</span>
                <span class="block text-2xl font-black text-slate-950 mt-1 leading-none">{{ $products->sum('stock') }} pcs</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-700 flex items-center justify-center">
                <i data-lucide="layers" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="glass-card rounded-[22px] p-5 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold text-slate-800 uppercase tracking-wider">Stok Kritis (&lt; 10)</span>
                <span class="block text-2xl font-black text-rose-700 mt-1 leading-none">
                    {{ $products->where('stock', '<', 10)->count() }} Jenis
                </span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-500/15 text-rose-700 flex items-center justify-center">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="glass-card rounded-[22px] p-4 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
        <div class="relative w-full sm:w-80">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-450 dark:text-slate-500">
                <i data-lucide="search" class="w-4.5 h-4.5"></i>
            </span>
            <input type="text" x-model="searchQuery" placeholder="Cari nama kue di inventaris..."
                   class="w-full bg-white border border-slate-200 rounded-xl py-2.5 pl-10 pr-4 text-xs font-semibold outline-none focus:border-amber-550 transition-all text-slate-800">
        </div>
        
        <div class="text-xs font-bold text-slate-400 dark:text-slate-500">
            * Menampilkan tingkat ketersediaan produk saat ini
        </div>
    </div>

    <!-- Inventory Stock Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($products as $p)
            @php
                $isLowStock = $p->stock < 10;
                // Calculate percentage based on ideal stock of 100
                $stockPercent = min(100, ($p->stock / 100) * 100);
            @endphp
            
            <div class="glass-card rounded-[24px] p-5 shadow-xs transition-all duration-300 hover:shadow-md border flex flex-col justify-between h-48
                        {{ $isLowStock ? 'border-rose-500/35 bg-rose-500/5 dark:border-rose-900/40 dark:bg-rose-950/10' : 'border-slate-200/50 dark:border-slate-800' }}"
                 x-show="searchQuery === '' || '{{ strtolower($p->name) }}'.includes(searchQuery.toLowerCase())">
                
                <!-- Card Header -->
                <div class="flex gap-4">
                    <div class="w-12 h-12 rounded-2xl overflow-hidden border border-slate-200 shrink-0 shadow-sm bg-slate-50">
                        <img src="{{ asset($p->image_url) }}" alt="Thumb" class="w-full h-full object-cover">
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-extrabold text-sm text-slate-950 truncate leading-tight">{{ $p->name }}</h4>
                        <span class="text-[10px] text-amber-800 font-extrabold uppercase tracking-wider block mt-1">{{ $p->category->name ?? 'Aneka Kue' }}</span>
                    </div>
                </div>

                <!-- Stock Slider / Progress Bar -->
                <div class="space-y-2 mt-4">
                    <div class="flex items-center justify-between text-xs font-bold">
                        <span class="text-slate-800 font-extrabold">Kapasitas Stok</span>
                        <span class="{{ $isLowStock ? 'text-rose-700 font-black animate-pulse' : 'text-slate-950 font-black' }} text-sm font-mono">
                            {{ $p->stock }} / 100 Pcs
                        </span>
                    </div>
                    <div class="w-full h-2.5 bg-slate-200 rounded-full overflow-hidden border border-slate-300">
                        <div class="h-full rounded-full transition-all duration-500 
                                    {{ $isLowStock ? 'bg-gradient-to-r from-rose-500 to-red-600' : 'bg-gradient-to-r from-emerald-500 to-emerald-600' }}" 
                             style="width: {{ $stockPercent }}%"></div>
                    </div>
                </div>

                <!-- Footer Quick Add Actions -->
                <div class="mt-4 pt-3 border-t border-slate-200 flex items-center justify-between text-xs">
                    <span class="text-[10px] text-slate-800 font-extrabold">Kelas: <span class="font-mono font-black text-slate-950">{{ $p->coco_class ?? '-' }}</span></span>
                    
                    <!-- Quick Update Button -->
                    <a href="{{ route('products') }}?search={{ urlencode($p->name) }}" 
                       class="px-3 py-1 bg-white border border-slate-200 text-[10px] font-bold rounded-lg text-slate-600 hover:bg-amber-400 hover:text-white hover:border-amber-400 transition-all duration-200 shadow-xs hover:shadow-md hover:shadow-amber-500/15 flex items-center gap-1">
                        <i data-lucide="edit-2" class="w-3.5 h-3.5"></i> Update
                    </a>
                </div>

            </div>
        @endforeach
    </div>
</div>
@endsection
