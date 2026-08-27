@extends('layouts.app')

@section('title', 'Laporan Keuangan')
@section('page_title', 'Laporan Keuangan & Analisis')

@section('content')
<div class="space-y-6">
    
    <!-- Range Selection Card -->
    <div class="glass-card rounded-[22px] p-4 flex flex-col md:flex-row items-center justify-between gap-4 shadow-sm">
        <form action="{{ route('reports') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Dari:</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                       class="bg-white border border-slate-200 rounded-xl py-2 px-3 text-xs font-semibold outline-none focus:border-amber-500 transition-all text-slate-800">
            </div>
            
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Sampai:</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                       class="bg-white border border-slate-200 rounded-xl py-2 px-3 text-xs font-semibold outline-none focus:border-amber-500 transition-all text-slate-800">
            </div>

            <button type="submit" class="px-4.5 py-2 bg-amber-500 hover:bg-amber-400 text-white rounded-xl text-xs font-bold shadow-md shadow-amber-500/10 transition-all shrink-0 w-full sm:w-auto">
                Filter Laporan
            </button>
        </form>

        <!-- Export tools -->
        <div class="flex items-center gap-2.5 w-full md:w-auto justify-end">
            <!-- PDF Download -->
            <a href="{{ route('reports.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank"
               class="px-4 py-2.5 rounded-xl border border-amber-400 bg-white hover:bg-amber-500 hover:text-white text-xs font-bold text-amber-700 flex items-center gap-1.5 transition-all shadow-xs">
                <i data-lucide="file-text" class="w-4 h-4"></i> PDF
            </a>

            <!-- Excel / CSV Download -->
            <a href="{{ route('reports.excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
               class="px-4 py-2.5 rounded-xl border border-emerald-500 bg-white hover:bg-emerald-600 hover:text-white text-xs font-bold text-emerald-700 flex items-center gap-1.5 transition-all shadow-xs">
                <i data-lucide="sheet" class="w-4 h-4"></i> Excel
            </a>

            <button onclick="window.print()" 
                    class="px-4.5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all shadow-md">
                <i data-lucide="printer" class="w-4 h-4"></i> Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Analytics summary grids -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Revenue Card -->
        <div class="glass-card rounded-[22px] p-5 shadow-xs">
            <span class="text-[10px] font-extrabold text-slate-800 uppercase tracking-wider block">Total Pendapatan</span>
            <span class="block text-2xl font-black text-slate-950 mt-2 leading-none">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
            <span class="block text-[10px] text-slate-600 font-bold mt-2.5">Selama periode laporan</span>
        </div>

        <!-- Profit Card -->
        <div class="glass-card rounded-[22px] p-5 shadow-xs">
            <span class="text-[10px] font-extrabold text-slate-800 uppercase tracking-wider block">Estimasi Keuntungan Netto</span>
            <span class="block text-2xl font-black text-emerald-600 mt-2 leading-none">Rp {{ number_format($totalProfit, 0, ',', '.') }}</span>
            <span class="block text-[10px] text-emerald-700 font-extrabold mt-2.5 flex items-center gap-1">
                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i> Margin bersih ~35%
            </span>
        </div>

        <!-- Total tx card -->
        <div class="glass-card rounded-[22px] p-5 shadow-xs">
            <span class="text-[10px] font-extrabold text-slate-800 uppercase tracking-wider block">Transaksi Selesai</span>
            <span class="block text-2xl font-black text-slate-950 mt-2 leading-none">{{ $transactionsCount }} Nota</span>
            <span class="block text-[10px] text-slate-600 font-bold mt-2.5">Rata-rata: {{ $transactionsCount > 0 ? number_format($totalRevenue / $transactionsCount, 0, ',', '.') : 0 }} / basket</span>
        </div>

        <!-- Products Sold card -->
        <div class="glass-card rounded-[22px] p-5 shadow-xs">
            <span class="text-[10px] font-extrabold text-slate-800 uppercase tracking-wider block">Volume Kue Terjual</span>
            <span class="block text-2xl font-black text-slate-950 mt-2 leading-none">{{ $totalItemsSold }} Pcs</span>
            <span class="block text-[10px] text-slate-600 font-bold mt-2.5">Jenis aneka kue kering & basah</span>
        </div>
    </div>

    <!-- Chart row -->
    <div class="glass-card rounded-[24px] p-6 shadow-xs h-96 flex flex-col">
        <div class="mb-4 shrink-0">
            <h3 class="text-sm font-extrabold text-slate-950">Tren Pendapatan Harian</h3>
            <p class="text-xs text-slate-600 font-bold">Berdasarkan data omzet transaksi penjualan</p>
        </div>
        <div class="flex-1 min-h-0 relative">
            <canvas id="rangeSalesChart" class="w-full h-full"></canvas>
        </div>
    </div>

    <!-- Top selling products table details -->
    <div class="glass-card rounded-[24px] p-6 shadow-xs">
        <div class="mb-5">
            <h3 class="text-sm font-extrabold text-slate-950">Tabel Produk Terlaris</h3>
            <p class="text-xs text-slate-600 font-bold">Berdasarkan frekuensi pembelian tertinggi di periode ini</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 text-xs font-black text-slate-800 uppercase tracking-wider">
                        <th class="py-3 px-4">Peringkat</th>
                        <th class="py-3 px-4">Nama Produk</th>
                        <th class="py-3 px-4">Kelas</th>
                        <th class="py-3 px-4 text-center">Volume Terjual</th>
                        <th class="py-3 px-4">Total Omzet</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-xs text-slate-950">
                    @forelse($topProducts as $index => $tp)
                        <tr class="hover:bg-slate-100/80 transition-colors">
                            <td class="py-3.5 px-4 font-bold">
                                <span class="inline-flex w-6 h-6 items-center justify-center rounded-lg text-xs font-extrabold
                                             {{ $index == 0 ? 'bg-amber-500/20 text-amber-800 border border-amber-500/30' : ($index == 1 ? 'bg-slate-200 text-slate-900 border border-slate-300' : ($index == 2 ? 'bg-orange-500/20 text-orange-800 border border-orange-500/30' : 'bg-slate-100 text-slate-700')) }}">
                                    #{{ $index + 1 }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-black text-slate-950 text-sm">
                                {{ $tp->product->name ?? 'Produk Kue' }}
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-800 font-bold text-xs">{{ $tp->product->coco_class ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-center font-black text-slate-950">{{ $tp->total_qty }} Pcs</td>
                            <td class="py-3.5 px-4 font-black font-mono text-emerald-600">Rp {{ number_format($tp->total_revenue, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-450 dark:text-slate-500 font-medium">
                                <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                Tidak ada data produk terlaris di rentang tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Init Lucide
        lucide.createIcons();

        // Line Chart: Range Sales Trend
        const ctx = document.getElementById('rangeSalesChart').getContext('2d');
        const labels = @json($chartLabels);
        const dataValues = @json($chartData);

        const greenGradient = ctx.createLinearGradient(0, 0, 0, 300);
        greenGradient.addColorStop(0, 'rgba(16, 185, 129, 0.22)');
        greenGradient.addColorStop(1, 'rgba(16, 185, 129, 0.00)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Omzet Harian',
                    data: dataValues,
                    borderColor: '#10B981', // emerald accent
                    borderWidth: 2.5,
                    backgroundColor: greenGradient,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#10B981',
                    pointBorderColor: '#FFFFFF',
                    pointBorderWidth: 1.5,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(15, 23, 42, 0.95)',
                        cornerRadius: 12,
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return ' Omzet: Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 9, family: 'Inter' }, color: '#94a3b8' }
                    },
                    y: {
                        grid: { color: 'rgba(226, 232, 240, 0.4)' },
                        border: { dash: [4, 4] },
                        ticks: {
                            font: { size: 9, family: 'Inter' },
                            color: '#94a3b8',
                            callback: function(val) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(val);
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
