@extends('layouts.app')

@section('title', 'Riwayat Transaksi')
@section('page_title', 'Riwayat Transaksi POS')

@section('content')
<div class="space-y-6" x-data="{ detailModalOpen: false, activeTx: { details: [] } }">
    
    <!-- Transaction History Table Card -->
    <div class="glass-card rounded-[24px] p-6 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 text-xs font-black text-slate-800 uppercase tracking-wider">
                        <th class="py-3 px-4">Kode Transaksi</th>
                        <th class="py-3 px-4">Tanggal & Waktu</th>
                        <th class="py-3 px-4 text-center">Jumlah Item</th>
                        <th class="py-3 px-4">Subtotal</th>
                        <th class="py-3 px-4">Diskon</th>
                        <th class="py-3 px-4">Total Akhir</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-xs text-slate-950">
                    @forelse($transactions as $t)
                        <tr class="hover:bg-slate-100/80 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-black text-slate-950">{{ $t->transaction_code }}</td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">{{ $t->created_at->format('d M Y H:i:s') }}</td>
                            <td class="py-3.5 px-4 text-center font-extrabold text-slate-900">{{ $t->total_items }} pcs</td>
                            <td class="py-3.5 px-4 font-extrabold font-mono text-slate-950">Rp {{ number_format($t->subtotal, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 font-bold font-mono text-rose-600">Rp {{ number_format($t->discount, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 font-black font-mono text-emerald-600 text-sm">Rp {{ number_format($t->total_price, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">Sukses</span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                     <!-- Invoice Detail Trigger -->
                                     <button data-tx="{{ json_encode($t) }}"
                                             @click="activeTx = JSON.parse($el.getAttribute('data-tx')); detailModalOpen = true"
                                             class="px-3 py-1.5 bg-white rounded-lg border border-slate-300 hover:bg-amber-400 hover:text-white hover:border-amber-400 text-slate-800 font-extrabold transition-all text-xs flex items-center gap-1.5 shadow-xs">
                                         <i data-lucide="eye" class="w-3.5 h-3.5"></i> Detail
                                     </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-450 dark:text-slate-500 font-medium">
                                <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                Belum ada transaksi penjualan yang tercatat dalam sistem.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-5 border-t border-slate-100 dark:border-slate-800/80 pt-4 flex items-center justify-between">
            <span class="text-[11px] text-slate-450 font-semibold uppercase">Halaman {{ $transactions->currentPage() }} dari {{ $transactions->lastPage() }}</span>
            <div class="inline-flex gap-2">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>

    <!-- =========================================================================
     * INVOICE DETAIL MODAL (Alpine.js controlled)
     * ========================================================================= -->
    <div id="detail-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-show="detailModalOpen" x-transition.opacity style="display: none;">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-xs" @click="detailModalOpen = false"></div>
        <!-- Modal Card -->
        <div class="relative bg-white border border-slate-200 rounded-[28px] max-w-md w-full shadow-2xl p-6 overflow-hidden transition-all transform scale-100">
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <div>
                    <h3 class="text-base font-extrabold text-slate-950">Detail Transaksi</h3>
                    <p class="text-xs text-slate-600 font-mono font-bold mt-0.5" x-text="activeTx.transaction_code"></p>
                </div>
                <button @click="detailModalOpen = false" class="text-slate-400 hover:text-slate-700 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Receipt Info Body -->
            <div class="space-y-4 text-xs">
                <!-- Meta data -->
                <div class="grid grid-cols-2 gap-3 text-slate-600">
                    <div>
                        <span class="block text-[10px] font-extrabold uppercase text-slate-500 tracking-wider">Tanggal Transaksi</span>
                        <span class="font-bold text-slate-950" x-text="new Date(activeTx.created_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' })"></span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-extrabold uppercase text-slate-500 tracking-wider">Kasir Pelaksana</span>
                        <span class="font-bold text-slate-950">Adnan Kasir</span>
                    </div>
                </div>

                <!-- Products table details -->
                <div class="border-t border-b border-dashed border-slate-200 py-3 space-y-2">
                    <span class="block text-[10px] font-extrabold uppercase text-slate-600 tracking-wider mb-2">Item Belanja</span>
                    
                    <!-- Table loops in Alpine.js -->
                    <template x-for="item in activeTx.details" :key="item.id">
                        <div class="flex justify-between items-center text-xs">
                            <div class="min-w-0 flex-1 pr-2">
                                <span class="font-extrabold text-slate-950 block truncate" x-text="item.product ? item.product.name : 'Produk Kue'"></span>
                                <span class="text-xs text-slate-600 font-bold" x-text="item.quantity + ' x Rp ' + new Intl.NumberFormat('id-ID').format(item.price)"></span>
                            </div>
                            <span class="font-extrabold text-slate-950 font-mono" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(item.subtotal)"></span>
                        </div>
                    </template>
                </div>

                <!-- Receipt calculations -->
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-2 text-xs">
                    <div class="flex justify-between text-slate-700 font-bold">
                        <span>Subtotal:</span>
                        <span class="font-extrabold text-slate-950 font-mono" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(activeTx.subtotal)"></span>
                    </div>
                    <div class="flex justify-between text-slate-700 font-bold">
                        <span>Diskon Belanja:</span>
                        <span class="font-extrabold text-rose-600 font-mono" x-text="'-Rp ' + new Intl.NumberFormat('id-ID').format(activeTx.discount)"></span>
                    </div>
                    <div class="flex justify-between text-sm font-black border-t border-slate-200 pt-2 text-slate-950">
                        <span>Total Tagihan:</span>
                        <span class="text-emerald-600 font-black text-base font-mono" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(activeTx.total_price)"></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs text-slate-600">
                    <div>
                        <span class="block text-[10px] font-extrabold uppercase text-slate-500 tracking-wider">Uang Diterima</span>
                        <span class="font-black text-slate-950 font-mono text-sm" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(activeTx.paid_amount)">-</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-extrabold uppercase text-slate-500 tracking-wider">Kembalian</span>
                        <span class="font-black text-emerald-600 font-mono text-sm" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(activeTx.change_amount)">-</span>
                    </div>
                </div>
            </div>

            <!-- Print Trigger Button -->
            <div class="mt-6 pt-4 border-t border-slate-100 flex gap-3">
                <button @click="detailModalOpen = false" class="flex-1 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold transition-all text-xs text-center border border-slate-200">
                    Tutup
                </button>
                <button onclick="window.print()" class="flex-1 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold transition-all text-xs flex items-center justify-center gap-1.5 shadow-md shadow-emerald-500/20">
                    <i data-lucide="printer" class="w-4 h-4"></i> Cetak Struk
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Init Lucide icons
        lucide.createIcons();
    });
</script>
@endsection
