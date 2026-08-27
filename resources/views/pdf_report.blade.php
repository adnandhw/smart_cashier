<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - {{ $storeName }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #0f172a;
            margin: 0;
            padding: 30px;
            background: #ffffff;
            font-size: 12px;
        }
        .header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 15px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            margin: 4px 0 0 0;
            color: #64748b;
            font-size: 11px;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        .meta-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 15px;
        }
        .meta-card .label {
            font-size: 9px;
            text-transform: uppercase;
            font-weight: bold;
            color: #64748b;
            letter-spacing: 0.5px;
        }
        .meta-card .val {
            font-size: 16px;
            font-weight: 800;
            color: #020617;
            margin-top: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background: #f1f5f9;
            color: #0f172a;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 12px;
            border-bottom: 2px solid #cbd5e1;
            text-align: left;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
            color: #020617;
            font-size: 11px;
        }
        tr:nth-child(even) {
            background-color: #fafafa;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: monospace; font-weight: bold; }
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="background: #e0f2fe; border: 1px solid #bae6fd; padding: 12px; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-weight: bold; color: #0369a1;">📄 Dokumen Laporan Siap Dicetak / Disimpan sebagai PDF</span>
        <button onclick="window.print()" style="background: #0284c7; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-weight: bold; cursor: pointer;">Cetak / Simpan PDF</button>
    </div>

    <div class="header">
        <div>
            <h1>{{ $storeName }}</h1>
            <p>Laporan Penjualan & Ringkasan Transaksi</p>
        </div>
        <div style="text-align: right;">
            <p style="font-weight: bold; color: #020617;">Periode Laporan:</p>
            <p>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>
    </div>

    <div class="meta-grid">
        <div class="meta-card">
            <div class="label">Total Pendapatan</div>
            <div class="val">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        </div>
        <div class="meta-card">
            <div class="label">Estimasi Keuntungan</div>
            <div class="val" style="color: #059669;">Rp {{ number_format($totalProfit, 0, ',', '.') }}</div>
        </div>
        <div class="meta-card">
            <div class="label">Transaksi Selesai</div>
            <div class="val">{{ $transactionsCount }} Nota</div>
        </div>
        <div class="meta-card">
            <div class="label">Volume Kue Terjual</div>
            <div class="val">{{ $totalItemsSold }} Pcs</div>
        </div>
    </div>

    <h3 style="margin-bottom: 10px; font-size: 14px; color: #0f172a;">Rincian Riwayat Transaksi</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 40px;" class="text-center">No</th>
                <th>Kode Transaksi</th>
                <th>Tanggal & Waktu</th>
                <th class="text-center">Jumlah Item</th>
                <th class="text-right">Subtotal</th>
                <th class="text-right">Diskon</th>
                <th class="text-right">Total Tagihan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $t)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-mono">{{ $t->transaction_code }}</td>
                    <td>{{ $t->created_at->format('d M Y H:i:s') }}</td>
                    <td class="text-center">{{ $t->total_items }} Pcs</td>
                    <td class="text-right font-mono">Rp {{ number_format($t->subtotal, 0, ',', '.') }}</td>
                    <td class="text-right font-mono" style="color: #dc2626;">Rp {{ number_format($t->discount, 0, ',', '.') }}</td>
                    <td class="text-right font-mono" style="font-weight: bold; color: #059669;">Rp {{ number_format($t->total_price, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 30px; color: #94a3b8;">
                        Tidak ada data transaksi pada rentang tanggal ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Smart Cashier System AI ({{ $storeName }}) pada {{ date('d M Y H:i:s') }}
    </div>

    <script>
        window.onload = function() {
            // Auto open print dialog if requested
            if (window.location.search.includes('print=1')) {
                window.print();
            }
        };
    </script>
</body>
</html>
