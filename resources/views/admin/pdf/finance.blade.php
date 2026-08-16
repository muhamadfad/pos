<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 20px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 14px; }
        
        .info-table { width: 100%; margin-bottom: 30px; font-size: 12px; }
        .info-table td { padding: 3px 0; }
        
        table.finance-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table.finance-table th, table.finance-table td { padding: 10px; border: 1px solid #000; }
        table.finance-table th { background-color: #f0f0f0; font-weight: bold; text-align: left; text-transform: uppercase; font-size: 11px; }
        
        .section-header td { font-weight: bold; background-color: #f9f9f9; text-transform: uppercase; }
        .item-row td:first-child { padding-left: 20px; }
        .total-row td { font-weight: bold; border-top: 2px solid #000; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .net-positive { color: #059669; }
        .net-negative { color: #dc2626; }
        
        .signature-area { width: 100%; margin-top: 50px; }
        .signature-box { width: 250px; float: right; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Coto Makassar Selera Rakyat</h1>
        <p>{{ $title }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td style="width: 15%;"><strong>Cabang</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 43%;">{{ $currentBranchName }}</td>
            
            <td style="width: 15%;"><strong>Tanggal Cetak</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 23%;">{{ date('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td><strong>Periode</strong></td>
            <td>:</td>
            <td>
                @if($startDate && $endDate)
                    {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                @elseif($startDate)
                    {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}
                @else
                    Semua Waktu
                @endif
            </td>
            
            <td><strong>Dicetak Oleh</strong></td>
            <td>:</td>
            <td>{{ auth()->user()->name ?? 'Admin' }}</td>
        </tr>
    </table>

    <table class="finance-table">
        <thead>
            <tr>
                <th style="width: 70%;">Keterangan</th>
                <th style="width: 30%; text-align: right;">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <!-- PENDAPATAN -->
            <tr class="section-header">
                <td colspan="2">A. Pendapatan</td>
            </tr>
            <tr class="item-row">
                <td>Pendapatan Penjualan (Cash-In)</td>
                <td class="text-right">{{ number_format($finance['income'], 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td>Total Pendapatan</td>
                <td class="text-right">{{ number_format($finance['income'], 0, ',', '.') }}</td>
            </tr>

            <!-- PENGELUARAN -->
            <tr><td colspan="2" style="border:none; height: 10px;"></td></tr>
            <tr class="section-header">
                <td colspan="2">B. Pengeluaran (Cash-Out)</td>
            </tr>
            <tr class="item-row">
                <td>Harga Pokok Penjualan (HPP) / Modal Bahan Baku</td>
                <td class="text-right">{{ number_format($finance['cost'], 0, ',', '.') }}</td>
            </tr>
            <tr class="item-row">
                <td>Biaya Operasional (Listrik, Gaji, dll)</td>
                <td class="text-right">{{ number_format($finance['expenses'], 0, ',', '.') }}</td>
            </tr>
            <tr class="item-row">
                <td>Pembayaran ke Supplier (Pembelian Stok)</td>
                <td class="text-right">{{ number_format($finance['supplierPaid'], 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                @php $totalPengeluaran = $finance['cost'] + $finance['expenses'] + $finance['supplierPaid']; @endphp
                <td>Total Pengeluaran</td>
                <td class="text-right">{{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
            </tr>

            <!-- RINGKASAN -->
            <tr><td colspan="2" style="border:none; height: 10px;"></td></tr>
            <tr class="section-header">
                <td colspan="2">C. Ringkasan Keuangan</td>
            </tr>
            <tr class="total-row" style="font-size: 14px;">
                <td>SALDO BERSIH (NET CASHFLOW)</td>
                <td class="text-right {{ $finance['netCashflow'] >= 0 ? 'net-positive' : 'net-negative' }}">
                    {{ number_format($finance['netCashflow'], 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    <div class="signature-area">
        <div class="signature-box">
            <p>Penanggung Jawab,</p>
            <br><br><br><br><br>
            <p style="text-decoration: underline; font-weight: bold;">{{ auth()->user()->name ?? 'Admin' }}</p>
        </div>
    </div>
</body>
</html>
