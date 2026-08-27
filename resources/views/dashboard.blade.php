@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Smart Cashier AI Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome banner -->
    <div class="glass-card rounded-[22px] p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-sm hover:shadow-md transition-shadow">
        <div>
            <h2 class="text-xl font-bold font-display text-slate-800 dark:text-white">Smart Cashier AI</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Sistem Kasir Berbasis Computer Vision (UMKM Aneka Kue Pak Yanto)</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('pos') }}" class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-white rounded-xl text-xs font-bold shadow-md shadow-amber-500/10 hover:shadow-amber-500/25 transition-all flex items-center gap-2">
                <i data-lucide="scan-line" class="w-4 h-4"></i> Buka Kasir AI
            </a>
        </div>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Stat Card 1: Today's Revenue -->
        <div class="glass-card rounded-[20px] p-5 shadow-xs hover:scale-[1.01] hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold text-slate-800 uppercase tracking-wider">Pendapatan Hari Ini</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-700 flex items-center justify-center">
                    <i data-lucide="wallet" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="block text-xl font-black text-slate-950 leading-none">
                    Rp {{ number_format($todayRevenue, 0, ',', '.') }}
                </span>
                <span class="block text-[10px] text-emerald-700 font-extrabold mt-1.5 flex items-center gap-1">
                    <i data-lucide="trending-up" class="w-3 h-3"></i> Hari ini
                </span>
            </div>
        </div>

        <!-- Stat Card 2: Today's Transactions -->
        <div class="glass-card rounded-[20px] p-5 shadow-xs hover:scale-[1.01] hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold text-slate-800 uppercase tracking-wider">Total Transaksi</span>
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-700 flex items-center justify-center">
                    <i data-lucide="receipt" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="block text-xl font-black text-slate-950 leading-none">
                    {{ $todayTransactions }}
                </span>
                <span class="block text-[10px] text-slate-700 font-extrabold mt-1.5 flex items-center gap-1">
                    <i data-lucide="activity" class="w-3 h-3"></i> Nota penjualan
                </span>
            </div>
        </div>

        <!-- Stat Card 3: Today's Products Sold -->
        <div class="glass-card rounded-[20px] p-5 shadow-xs hover:scale-[1.01] hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold text-slate-800 uppercase tracking-wider">Produk Terjual</span>
                <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-700 flex items-center justify-center">
                    <i data-lucide="package-check" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="block text-xl font-black text-slate-950 leading-none">
                    {{ $todayProductsSold }}
                </span>
                <span class="block text-[10px] text-slate-700 font-extrabold mt-1.5 flex items-center gap-1">
                    Pcs kue terjual
                </span>
            </div>
        </div>

        <!-- Stat Card 4: Low Stock Alerts -->
        <div class="glass-card rounded-[20px] p-5 shadow-xs hover:scale-[1.01] hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold text-slate-800 uppercase tracking-wider">Stok Menipis</span>
                <div class="w-8 h-8 rounded-lg @if($lowStockProducts > 0) bg-rose-500/15 text-rose-700 @else bg-slate-100 text-slate-600 @endif flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-4 h-4 animate-pulse"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="block text-xl font-black text-slate-950 leading-none">
                    {{ $lowStockProducts }}
                </span>
                <span class="block text-[10px] @if($lowStockProducts > 0) text-rose-700 font-extrabold @else text-slate-700 font-extrabold @endif mt-1.5 flex items-center gap-1">
                    Butuh restock segera
                </span>
            </div>
        </div>

        <!-- Stat Card 5: AI Detections Today -->
        <div class="glass-card rounded-[20px] p-5 col-span-2 lg:col-span-1 shadow-xs hover:scale-[1.01] hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold text-slate-800 uppercase tracking-wider">Deteksi AI</span>
                <div class="w-8 h-8 rounded-lg bg-pink-500/10 text-pink-700 flex items-center justify-center">
                    <i data-lucide="cpu" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="block text-xl font-black text-slate-950 leading-none">
                    {{ $todayAiDetections }}
                </span>
                <span class="block text-[10px] text-pink-700 font-extrabold mt-1.5 flex items-center gap-1">
                    YOLOv8 Edge Runs
                </span>
            </div>
        </div>
    </div>

    <!-- Charts & Analytics Section -->
    <!-- Charts & Analytics Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Sales Trend Line Chart (8 Columns) -->
        <div class="lg:col-span-8 glass-card rounded-[24px] p-6 shadow-xs flex flex-col h-96">
            <div class="flex items-center justify-between mb-4 flex-shrink-0">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-950">Tren Penjualan Harian</h3>
                    <p class="text-xs text-slate-600 font-bold">Statistik pendapatan 7 hari terakhir</p>
                </div>
                <div class="flex items-center gap-2 bg-slate-100 p-1 rounded-xl">
                    <button id="chart-tab-daily" class="px-3 py-1.5 text-[10px] font-bold rounded-lg bg-white shadow-sm text-slate-800 transition-all">Harian</button>
                    <button id="chart-tab-monthly" class="px-3 py-1.5 text-[10px] font-bold rounded-lg text-slate-500 hover:text-slate-800 transition-all">Bulanan</button>
                </div>
            </div>
            <div class="flex-1 min-h-0 relative">
                <canvas id="salesChart" class="w-full h-full"></canvas>
            </div>
        </div>

        <!-- Top Selling Products Doughnut Chart (4 Columns) -->
        <div class="lg:col-span-4 glass-card rounded-[24px] p-6 shadow-xs flex flex-col h-96">
            <div class="mb-4 flex-shrink-0">
                <h3 class="text-sm font-extrabold text-slate-950">Produk Terlaris</h3>
                <p class="text-xs text-slate-600 font-bold">Berdasarkan volume penjualan</p>
            </div>
            <div class="flex-1 min-h-0 relative flex items-center justify-center">
                @if(count($topProductLabels) > 0)
                    <canvas id="topProductsChart" class="max-w-[210px] max-h-[210px]"></canvas>
                @else
                    <div class="text-center text-slate-400 py-10">
                        <i data-lucide="package-x" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                        <span class="text-xs font-semibold">Belum ada transaksi</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Activity & Audit Logs -->
    <div class="glass-card rounded-[24px] p-6 shadow-xs">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-sm font-extrabold text-slate-950">Transaksi Terbaru</h3>
                <p class="text-xs text-slate-600 font-bold">Aktivitas penjualan kasir hari ini</p>
            </div>
            <a href="{{ route('transactions') }}" class="text-xs font-extrabold text-amber-700 hover:text-amber-800 transition-colors flex items-center gap-1">
                Lihat Semua <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 text-xs font-black text-slate-800 uppercase tracking-wider">
                        <th class="py-3 px-4">Kode Transaksi</th>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4 text-center">Jumlah Item</th>
                        <th class="py-3 px-4">Subtotal</th>
                        <th class="py-3 px-4">Total Belanja</th>
                        <th class="py-3 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-xs text-slate-950">
                    @forelse($recentTransactions as $transaction)
                        <tr class="hover:bg-slate-100/80 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-black text-slate-950">{{ $transaction->transaction_code }}</td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">{{ $transaction->created_at->format('d M Y H:i') }}</td>
                            <td class="py-3.5 px-4 text-center font-extrabold text-slate-900">{{ $transaction->total_items }} pcs</td>
                            <td class="py-3.5 px-4 font-extrabold font-mono text-slate-950">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 font-black font-mono text-emerald-600">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">Sukses</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-450 dark:text-slate-500 font-medium">
                                <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-slate-350 dark:text-slate-600 opacity-60"></i>
                                Belum ada transaksi penjualan yang tercatat hari ini.
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
    document.addEventListener('DOMContentLoaded', function () {
        // --- 1. Line Chart: Sales Trend ---
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const dailyLabels = @json($salesLabels);
        const dailyData = @json($salesData);
        
        const monthlyLabels = @json($monthlyLabels);
        const monthlyData = @json($monthlyData);

        let currentLabels = dailyLabels;
        let currentData = dailyData;

        // Custom Amber colors matching Stripe/Linear style
        const amberGradient = salesCtx.createLinearGradient(0, 0, 0, 300);
        amberGradient.addColorStop(0, 'rgba(92, 29, 27, 0.22)');
        amberGradient.addColorStop(1, 'rgba(92, 29, 27, 0.00)');

        const salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: currentLabels,
                datasets: [{
                    label: 'Pendapatan',
                    data: currentData,
                    borderColor: '#5C1D1B',
                    borderWidth: 2.5,
                    backgroundColor: amberGradient,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#5C1D1B',
                    pointBorderColor: '#FFFFFF',
                    pointBorderWidth: 1.5,
                    pointRadius: 4,
                    pointHoverRadius: 6
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
                        titleColor: '#FFF',
                        bodyColor: '#FFF',
                        padding: 12,
                        cornerRadius: 12,
                        callbacks: {
                            label: function (context) {
                                return ' Pendapatan: Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 9, family: 'Inter' },
                            color: '#94a3b8'
                        }
                    },
                    y: {
                        grid: { color: 'rgba(226, 232, 240, 0.4)' },
                        border: { dash: [4, 4] },
                        ticks: {
                            font: { size: 9, family: 'Inter' },
                            color: '#94a3b8',
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(value);
                            }
                        }
                    }
                }
            }
        });

        // Tabs to toggle Daily / Monthly
        const tabDaily = document.getElementById('chart-tab-daily');
        const tabMonthly = document.getElementById('chart-tab-monthly');

        tabDaily.addEventListener('click', () => {
            tabDaily.className = "px-3 py-1.5 text-[10px] font-bold rounded-lg bg-white shadow-sm text-slate-800 transition-all";
            tabMonthly.className = "px-3 py-1.5 text-[10px] font-bold rounded-lg text-slate-500 hover:text-slate-800 transition-all";
            salesChart.data.labels = dailyLabels;
            salesChart.data.datasets[0].data = dailyData;
            salesChart.update();
        });

        tabMonthly.addEventListener('click', () => {
            tabMonthly.className = "px-3 py-1.5 text-[10px] font-bold rounded-lg bg-white shadow-sm text-slate-800 transition-all";
            tabDaily.className = "px-3 py-1.5 text-[10px] font-bold rounded-lg text-slate-500 hover:text-slate-800 transition-all";
            salesChart.data.labels = monthlyLabels;
            salesChart.data.datasets[0].data = monthlyData;
            salesChart.update();
        });

        // --- 2. Doughnut Chart: Top Selling Products ---
        const topCtx = document.getElementById('topProductsChart');
        if (topCtx) {
            const productLabels = @json($topProductLabels);
            const productQtys = @json($topProductQuantities);

            new Chart(topCtx, {
                type: 'doughnut',
                data: {
                    labels: productLabels,
                    datasets: [{
                        data: productQtys,
                        backgroundColor: [
                            '#6C2422', // Maroon Secondary
                            '#10B981', // Emerald Accent
                            '#3B82F6', // Blue
                            '#EC4899', // Pink
                            '#8B5CF6'  // Purple
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                boxWidth: 8,
                                padding: 12,
                                font: { size: 9, family: 'Inter' }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.95)',
                            padding: 10,
                            cornerRadius: 10,
                            callbacks: {
                                label: function(context) {
                                    return ' ' + context.label + ': ' + context.raw + ' pcs';
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
