@extends('layouts.app')

@section('title', 'Manajemen Produk')
@section('page_title', 'Daftar Produk Aneka Kue')

@section('content')
<div class="space-y-6" x-data="{ 
    addModalOpen: false, 
    editModalOpen: false, 
    currentProduct: {},
    searchQuery: '{{ request('search', '') }}',
    selectedCategory: '{{ request('category_id', '') }}',
    products: @js($products),
    matches(p) {
        const q = this.searchQuery.toLowerCase();
        const matchesSearch = !q || 
            (p.name && p.name.toLowerCase().includes(q)) || 
            (p.coco_class && p.coco_class.toLowerCase().includes(q));
        const matchesCategory = !this.selectedCategory || String(p.category_id) === String(this.selectedCategory);
        return matchesSearch && matchesCategory;
    },
    hasVisibleProducts() {
        return this.products.some(p => this.matches(p));
    },
    visibleCount() {
        return this.products.filter(p => this.matches(p)).length;
    }
}">
    
    <!-- Action Bar Grid (Filters, Search, Buttons) -->
    <div class="glass-card rounded-[22px] p-4 flex flex-col md:flex-row items-center justify-between gap-4 shadow-sm">
        
        <!-- Filters Form -->
        <form action="{{ route('products') }}" method="GET" onsubmit="event.preventDefault();" class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
            <!-- Search bar -->
            <div class="relative w-full sm:w-60">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-450">
                    <i data-lucide="search" class="w-4.5 h-4.5"></i>
                </span>
                <input type="text" name="search" x-model="searchQuery" placeholder="Cari nama atau class..."
                       class="w-full bg-white border border-slate-200 rounded-xl py-2 pl-10 pr-4 text-xs font-semibold outline-none focus:border-amber-550 transition-all text-slate-800">
            </div>

            <!-- Category filter -->
            <div class="w-full sm:w-48">
                <select name="category_id" x-model="selectedCategory" 
                        class="w-full bg-white border border-slate-200 rounded-xl py-2 px-3.5 text-xs font-semibold outline-none focus:border-amber-550 transition-all text-slate-800">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Reset Filter if any exist -->
            <template x-if="searchQuery || selectedCategory">
                <a href="#" @click.prevent="searchQuery = ''; selectedCategory = ''" class="text-xs font-bold text-rose-500 hover:text-rose-600 transition-colors shrink-0">
                    Hapus Filter
                </a>
            </template>
        </form>

        <!-- Dynamic Action buttons -->
        <div class="flex items-center gap-2.5 w-full md:w-auto justify-end">
            <button @click="addModalOpen = true" 
                    class="px-4.5 py-2.5 bg-amber-500 hover:bg-amber-400 text-white rounded-xl text-xs font-bold shadow-md shadow-amber-500/10 hover:shadow-amber-500/25 transition-all flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Produk
            </button>
        </div>
    </div>

    <!-- Product list Table view -->
    <div class="glass-card rounded-[24px] p-6 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Gambar</th>
                        <th class="py-3 px-4">Nama Produk</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Kelas</th>
                        <th class="py-3 px-4">Harga Jual</th>
                        <th class="py-3 px-4 text-center">Stok</th>
                        <th class="py-3 px-4 text-center">Rata-Rata Akurasi AI</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100/60 dark:divide-slate-800/40 text-xs text-slate-700 dark:text-slate-300">
                    @foreach($products as $index => $p)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/30 transition-colors"
                            x-show="matches(products[{{ $index }}])">
                            <td class="py-3.5 px-4 shrink-0">
                                <div class="w-10 h-10 rounded-xl overflow-hidden border border-slate-100 dark:border-slate-800 shadow-inner bg-slate-50">
                                    <img src="{{ asset($p->image_url) }}" alt="Thumb" class="w-full h-full object-cover">
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="block font-bold text-slate-850 dark:text-white leading-tight">{{ $p->name }}</span>
                                <span class="block text-[10px] text-slate-400 mt-0.5">ID: PROD-{{ str_pad($p->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex px-2 py-1 rounded-md text-[10px] font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400">{{ $p->category->name ?? 'Kue' }}</span>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-[11px] font-bold text-slate-550 dark:text-slate-450">{{ $p->coco_class ?? '-' }}</td>
                            <td class="py-3.5 px-4 font-extrabold font-mono text-slate-850 dark:text-white">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-extrabold @if($p->stock < 10) bg-rose-500/10 text-rose-600 border border-rose-500/20 @else bg-amber-500/10 text-amber-700 border border-amber-500/20 @endif">
                                    {{ $p->stock }} pcs
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($p->detection_logs_count > 0)
                                    <span class="font-bold text-emerald-600 dark:text-emerald-450 font-mono">{{ number_format($p->average_confidence * 100, 1) }}%</span>
                                    <span class="block text-[9px] text-slate-400 mt-0.5">({{ $p->detection_logs_count }} deteksi)</span>
                                @else
                                    <span class="text-slate-400 font-medium font-mono text-[10px]">-</span>
                                    @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center justify-center gap-1">
                                    <!-- Edit Trigger -->
                                    <button data-product="{{ json_encode($p) }}"
                                             @click="currentProduct = JSON.parse($el.getAttribute('data-product')); editModalOpen = true"
                                             class="p-2 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-amber-400 hover:text-white hover:border-amber-400 transition-all duration-200 shadow-xs hover:shadow-md hover:shadow-amber-500/15" title="Ubah">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </button>

                                    <!-- Delete Trigger Form -->
                                    <form action="{{ route('products.delete', $p->id) }}" method="POST" onsubmit="confirmDelete(event)" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg bg-white border border-slate-200 hover:bg-rose-600 hover:text-white hover:border-rose-600 text-slate-600 transition-all duration-200 shadow-xs hover:shadow-md hover:shadow-rose-600/15" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    <tr x-show="!hasVisibleProducts()" style="display: none;">
                        <td colspan="8" class="py-12 text-center text-slate-450 dark:text-slate-500 font-medium">
                            <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                            Tidak ada produk terdaftar yang cocok dengan pencarian Anda.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Product Count Summary -->
        <div class="mt-5 border-t border-slate-100 dark:border-slate-800/80 pt-4 flex items-center justify-between">
            <span class="text-[11px] text-slate-450 font-semibold uppercase">
                Menampilkan <span class="font-extrabold" x-text="visibleCount()"></span> dari {{ $products->count() }} Produk
            </span>
        </div>
    </div>

    <!-- =========================================================================
     * ADD PRODUCT MODAL (Alpine.js controlled)
     * ========================================================================= -->
    <div id="add-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-show="addModalOpen" x-transition.opacity style="display: none;">
        <!-- Backdrop backdrop-blur -->
        <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-xs" @click="addModalOpen = false"></div>
        <!-- Modal Card -->
        <div class="relative bg-white border border-slate-200 rounded-[28px] max-w-lg w-full shadow-2xl p-6 md:p-8 overflow-hidden transition-all transform scale-100">
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                <h3 class="text-base font-extrabold text-slate-800">Tambah Produk Kue Baru</h3>
                <button @click="addModalOpen = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <!-- Product name -->
                    <div class="col-span-2 space-y-1">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Nama Produk</label>
                        <input type="text" name="name" required placeholder="Contoh: Risol Mayonese" 
                               class="w-full bg-white border border-slate-200 rounded-xl py-2.5 px-3.5 text-xs font-semibold outline-none focus:border-amber-500 transition-all text-slate-800">
                    </div>
                    <!-- Kategori -->
                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Kategori Kue</label>
                        <select name="category_id" required 
                                class="w-full bg-white border border-slate-200 rounded-xl py-2.5 px-3.5 text-xs font-semibold outline-none focus:border-amber-550 transition-all text-slate-800">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- YOLO Class name -->
                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Kelas</label>
                        <input type="text" name="coco_class" placeholder="Contoh: risol mayonese" 
                               class="w-full bg-white border border-slate-200 rounded-xl py-2.5 px-3.5 text-xs font-semibold outline-none focus:border-amber-550 transition-all text-slate-800 font-mono">
                    </div>
                    <!-- Harga Jual -->
                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Harga Jual (Rp)</label>
                        <input type="number" name="price" required placeholder="0" 
                               class="w-full bg-white border border-slate-200 rounded-xl py-2.5 px-3.5 text-xs font-semibold outline-none focus:border-amber-550 transition-all text-slate-800 font-mono">
                    </div>
                    <!-- Stok -->
                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Stok Awal</label>
                        <input type="number" name="stock" required placeholder="100" 
                               class="w-full bg-white border border-slate-200 rounded-xl py-2.5 px-3.5 text-xs font-semibold outline-none focus:border-amber-550 transition-all text-slate-800 font-mono">
                    </div>
                    <!-- Product Image -->
                    <div class="col-span-2 space-y-1">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Gambar Produk (Opsional)</label>
                        <input type="file" name="image" 
                               class="w-full bg-white border border-slate-200 rounded-xl py-2 px-3 text-xs outline-none focus:border-amber-550 transition-all file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-amber-500/10 file:text-amber-600 hover:file:bg-amber-500/20 text-slate-500">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 mt-6 flex justify-end gap-3">
                    <button type="button" @click="addModalOpen = false" class="px-4.5 py-2.5 rounded-xl border border-slate-200 hover:bg-amber-400 hover:text-white hover:border-amber-400 text-xs font-bold text-slate-600 transition-all">
                        Batal
                    </button>
                    <button type="submit" class="px-4.5 py-2.5 bg-amber-500 hover:bg-amber-400 text-white rounded-xl text-xs font-bold shadow-md shadow-amber-500/10 transition-all">
                        Simpan Produk
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- =========================================================================
     * EDIT PRODUCT MODAL (Alpine.js controlled)
     * ========================================================================= -->
    <div id="edit-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-show="editModalOpen" x-transition.opacity style="display: none;">
        <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-xs" @click="editModalOpen = false"></div>
        <div class="relative bg-white border border-slate-200 rounded-[28px] max-w-lg w-full shadow-2xl p-6 md:p-8 overflow-hidden transition-all transform scale-100">
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                <h3 class="text-base font-extrabold text-slate-800">Ubah Detail Produk</h3>
                <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Edit Form Action dynamically set via script -->
            <form :action="'{{ route('products') }}/' + currentProduct.id" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <!-- Product name -->
                    <div class="col-span-2 space-y-1">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Nama Produk</label>
                        <input type="text" name="name" required :value="currentProduct.name"
                               class="w-full bg-white border border-slate-200 rounded-xl py-2.5 px-3.5 text-xs font-semibold outline-none focus:border-amber-550 transition-all text-slate-800">
                    </div>
                    <!-- Kategori -->
                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Kategori Kue</label>
                        <select name="category_id" required 
                                class="w-full bg-white border border-slate-200 rounded-xl py-2.5 px-3.5 text-xs font-semibold outline-none focus:border-amber-550 transition-all text-slate-800">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" :selected="currentProduct.category_id == {{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- YOLO Class name -->
                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Kelas</label>
                        <input type="text" name="coco_class" :value="currentProduct.coco_class"
                               class="w-full bg-white border border-slate-200 rounded-xl py-2.5 px-3.5 text-xs font-semibold outline-none focus:border-amber-550 transition-all text-slate-800 font-mono">
                    </div>
                    <!-- Harga Jual -->
                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Harga Jual (Rp)</label>
                        <input type="number" name="price" required :value="Math.round(currentProduct.price)"
                               class="w-full bg-white border border-slate-200 rounded-xl py-2.5 px-3.5 text-xs font-semibold outline-none focus:border-amber-550 transition-all text-slate-800 font-mono">
                    </div>
                    <!-- Stok -->
                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Stok Inventaris</label>
                        <input type="number" name="stock" required :value="currentProduct.stock"
                               class="w-full bg-white border border-slate-200 rounded-xl py-2.5 px-3.5 text-xs font-semibold outline-none focus:border-amber-550 transition-all text-slate-800 font-mono">
                    </div>
                    <!-- Product Image -->
                    <div class="col-span-2 space-y-1">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Unggah Gambar Baru (Opsional)</label>
                        <input type="file" name="image" 
                               class="w-full bg-white border border-slate-200 rounded-xl py-2 px-3 text-xs outline-none focus:border-amber-550 transition-all file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-amber-500/10 file:text-amber-600 hover:file:bg-amber-500/20 text-slate-500">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 mt-6 flex justify-end gap-3">
                    <button type="button" @click="editModalOpen = false" class="px-4.5 py-2.5 rounded-xl border border-slate-200 hover:bg-amber-400 hover:text-white hover:border-amber-400 text-xs font-bold text-slate-600 transition-all">
                        Batal
                    </button>
                    <button type="submit" class="px-4.5 py-2.5 bg-amber-500 hover:bg-amber-400 text-white rounded-xl text-xs font-bold shadow-md shadow-amber-500/10 transition-all">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    // SweetAlert delete confirmation dialogues
    function confirmDelete(e) {
        e.preventDefault();
        const form = e.target;
        Swal.fire({
            title: 'Hapus Produk?',
            text: "Data produk yang dihapus tidak dapat dipulihkan kembali!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48', // rose 600
            cancelButtonColor: '#64748b', // slate 500
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-3xl border border-slate-100 bg-white shadow-2xl',
                title: 'font-display font-bold text-slate-800',
                htmlContainer: 'text-xs text-slate-500 font-semibold'
            }
        }).then((result) => {
            if (result && result.isConfirmed) {
                form.submit();
            }
        });
    }
</script>
@endsection
