<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #2d3748;
        }

        .container {
            padding: 10px 20px;
        }

        .header {
            margin-bottom: 25px;
        }

        .company {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .report-title {
            font-size: 14px;
            margin-top: 4px;
            color: #4a5568;
        }

        .meta {
            font-size: 11px;
            margin-top: 6px;
            color: #718096;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            text-align: left;
            padding: 8px 6px;
            font-size: 10px;
            text-transform: uppercase;
            border-bottom: 1px solid #cbd5e0;
            background-color: #f7fafc;
        }

        td {
            padding: 6px;
            vertical-align: top;
            border-bottom: 1px solid #edf2f7;
        }

        .right { text-align: right; }

        .items {
            font-size: 10px;
            color: #4a5568;
        }

        .summary {
            margin-top: 25px;
            width: 100%;
        }

        .summary td {
            padding: 6px;
        }

        .summary .label {
            text-align: right;
            font-weight: bold;
        }

        .summary .value {
            text-align: right;
            width: 150px;
        }

        .footer {
            margin-top: 40px;
            font-size: 10px;
            color: #718096;
        }
    </style>
</head>
<body>

<div class="container">

    <!-- HEADER -->
    <div class="header">
        <div class="company">LAPORAN TRANSAKSI BARANG MASUK</div>
        <div class="report-title">{{ $mitra->nama_mitra }}</div>
        <div class="meta">
            Dicetak pada: {{ now()->format('d M Y H:i') }}
        </div>
    </div>

    <!-- TABLE -->
    <table>
        <thead>
            <tr>
                <th width="12%">Tanggal</th>
                <th width="38%">Detail Barang</th>
                <th width="15%">Total</th>
                <th width="15%">Dibayar</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $trx)
            <tr>
                <td>
                    {{ \Carbon\Carbon::parse($trx->tanggal)->format('d M Y') }}
                </td>

                <td class="items">
                    @foreach($trx->items as $item)
                        • {{ $item->product->name ?? '-' }}
                        ({{ $item->qty }})<br>
                    @endforeach
                </td>

                <td class="right">
                    Rp {{ number_format($trx->total_harga,0,',','.') }}
                </td>

                <td class="right">
                    Rp {{ number_format($trx->total_dibayar,0,',','.') }}
                </td>

                <td>
                    {{ strtoupper($trx->status_pembayaran) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- SUMMARY -->
    <table class="summary">
        <tr>
            <td class="label">Total Transaksi :</td>
            <td class="value">{{ $transactions->count() }}</td>
        </tr>
        <tr>
            <td class="label">Total Nilai :</td>
            <td class="value">
                Rp {{ number_format($transactions->sum('total_harga'),0,',','.') }}
            </td>
        </tr>
        <tr>
            <td class="label">Total Dibayar :</td>
            <td class="value">
                Rp {{ number_format($transactions->sum('total_dibayar'),0,',','.') }}
            </td>
        </tr>
        <tr>
            <td class="label">Total Hutang :</td>
            <td class="value">
                Rp {{ number_format(
                    $transactions->sum('total_harga') - 
                    $transactions->sum('total_dibayar'),
                0,',','.') }}
            </td>
        </tr>
    </table>

    <!-- FOOTER -->
    <div class="footer">
        Dokumen ini dihasilkan secara otomatis oleh sistem.
    </div>

</div>

</body>
</html>
