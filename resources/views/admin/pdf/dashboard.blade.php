<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Dashboard</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 24px; color: #4338ca; }
        .header p { margin: 5px 0 0; color: #666; font-size: 14px; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 5px; }
        .kpi-container { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .kpi-container td { width: 50%; padding: 10px; vertical-align: top; }
        .kpi-box { border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; background-color: #f8fafc; }
        .kpi-box h3 { margin: 0 0 10px; font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: 1px; }
        .kpi-box .value { font-size: 24px; font-weight: bold; color: #0f172a; margin: 0; }
        .kpi-box .sub-value { font-size: 12px; color: #10b981; margin-top: 5px; }
        .kpi-box .sub-value.negative { color: #ef4444; }
        .section-title { font-size: 16px; color: #333; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-bottom: 15px; margin-top: 20px; }
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table.data-table th, table.data-table td { border: 1px solid #ddd; padding: 8px 12px; text-align: left; }
        table.data-table th { background-color: #f1f5f9; color: #475569; font-size: 12px; text-transform: uppercase; }
        table.data-table tr:nth-child(even) { background-color: #f8fafc; }
    </style>
</head>
<body>
    <div class="header">
        <h1>COTO MAKASSAR SELERA RAKYAT</h1>
        <p>Laporan Ringkasan Dashboard</p>
    </div>

    <table class="info-table">
        <tr>
            <td><strong>Cabang:</strong> {{ $currentBranchName }}</td>
            <td style="text-align: right;"><strong>Tanggal Cetak:</strong> {{ date('d/m/Y H:i') }}</td>
        </tr>
    </table>

    <table class="kpi-container">
        <tr>
            <td>
                <div class="kpi-box" style="border-left: 4px solid #4f46e5;">
                    <h3>Pendapatan Hari Ini</h3>
                    <p class="value">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
                    @if($revenueChange >= 0)
                        <p class="sub-value">+{{ number_format($revenueChange, 1) }}% dari kemarin</p>
                    @else
                        <p class="sub-value negative">{{ number_format($revenueChange, 1) }}% dari kemarin</p>
                    @endif
                </div>
            </td>
            <td>
                <div class="kpi-box" style="border-left: 4px solid #0ea5e9;">
                    <h3>Total Transaksi</h3>
                    <p class="value">{{ number_format($todayTransactions, 0, ',', '.') }}</p>
                    <p class="sub-value" style="color: #64748b;">Transaksi Hari Ini</p>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="kpi-box" style="border-left: 4px solid #f59e0b;">
                    <h3>Total Piutang</h3>
                    <p class="value">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</p>
                    <p class="sub-value" style="color: #64748b;">Belum dibayar oleh pelanggan</p>
                </div>
            </td>
            <td>
                <div class="kpi-box" style="border-left: 4px solid #ef4444;">
                    <h3>Total Hutang</h3>
                    <p class="value">Rp {{ number_format($totalHutang, 0, ',', '.') }}</p>
                    <p class="sub-value" style="color: #64748b;">Belum dibayar ke supplier</p>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">Produk / Bahan Baku Paling Banyak Digunakan (Top 10)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 10%; text-align: center;">No</th>
                <th style="width: 70%;">Nama Bahan Baku / Produk</th>
                <th style="width: 20%; text-align: right;">Jumlah Pemakaian</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stockNames as $index => $name)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $name }}</td>
                <td style="text-align: right;">{{ number_format($stockSeries[$index], 0, ',', '.') }}</td>
            </tr>
            @endforeach
            @if(empty($stockNames))
            <tr>
                <td colspan="3" style="text-align: center; font-style: italic;">Belum ada data pemakaian</td>
            </tr>
            @endif
        </tbody>
    </table>

    @if($isSuperAdmin && $showAllBranches)
    <div class="section-title">Ringkasan Per Cabang</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Nama Cabang</th>
                <th style="text-align: right;">Total Pesanan</th>
                <th style="text-align: right;">Pendapatan Hari Ini</th>
                <th style="text-align: right;">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($branchStats as $stat)
            <tr>
                <td>{{ $stat['name'] }}</td>
                <td style="text-align: right;">{{ number_format($stat['total_orders'], 0, ',', '.') }}</td>
                <td style="text-align: right;">Rp {{ number_format($stat['today_revenue'], 0, ',', '.') }}</td>
                <td style="text-align: right;">Rp {{ number_format($stat['total_revenue'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>
