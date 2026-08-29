<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #2d3748; }
        .container { padding: 10px 20px; }
        .header { margin-bottom: 25px; text-align: center; border-bottom: 2px solid #cbd5e0; padding-bottom: 10px; }
        .company { font-size: 18px; font-weight: bold; letter-spacing: 1px; }
        .report-title { font-size: 14px; margin-top: 4px; color: #4a5568; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 8px 10px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background-color: #f7fafc; font-weight: bold; font-size: 10px; color: #4a5568; text-transform: uppercase; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="company">Resto Coto Makasar</div>
            <div class="report-title">Laporan Stok Gudang Pusat</div>
            <div style="font-size: 10px; margin-top: 4px;">Dicetak pada: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item/Bahan</th>
                    <th class="text-right">Harga Satuan (HPP)</th>
                    <th class="text-right">Stok Gudang</th>
                    <th class="text-right">Total Nilai Asset</th>
                </tr>
            </thead>
            <tbody>
                @php $totalAsset = 0; @endphp
                @foreach($ingredients as $ing)
                @php $totalAsset += ($ing->current_stock * $ing->unit_cost); @endphp
                <tr>
                    <td>
                        <div class="font-bold">{{ $ing->name }}</div>
                    </td>
                    <td class="text-right">Rp {{ number_format($ing->unit_cost, 0, ',', '.') }}/{{ $ing->unit->name ?? 'kg' }}</td>
                    <td class="text-right">
                        <span class="font-bold">{{ $ing->current_stock }}</span> <span style="font-size:10px; color:#718096;">{{ $ing->unit->name ?? 'kg' }}</span>
                    </td>
                    <td class="text-right font-bold">
                        Rp {{ number_format($ing->current_stock * $ing->unit_cost, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-right font-bold" style="background-color: #f7fafc;">Total Nilai Asset Keseluruhan:</th>
                    <th class="text-right font-bold" style="background-color: #f7fafc;">Rp {{ number_format($totalAsset, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>