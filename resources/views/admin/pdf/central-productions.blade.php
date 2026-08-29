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
        .text-emerald { color: #059669; }
        .text-rose { color: #e11d48; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="company">Resto Coto Makasar</div>
            <div class="report-title">Laporan Proses Produksi Pusat</div>
            <div style="font-size: 10px; margin-top: 4px;">Dicetak pada: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No. Ref & Tanggal</th>
                    <th>Hasil Produksi (Bersih)</th>
                    <th>Bahan Mentah (Kotor)</th>
                    <th class="text-right">Biaya Tambahan</th>
                    <th class="text-right">HPP / Unit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productions as $prod)
                <tr>
                    <td>
                        <div class="font-bold">{{ $prod->reference_number ?: '-' }}</div>
                        <div style="font-size:10px; color:#718096;">{{ \Carbon\Carbon::parse($prod->date)->format('d M Y') }}</div>
                    </td>
                    <td>
                        <div class="font-bold text-emerald">{{ $prod->resultIngredient->name ?? 'Dihapus' }}</div>
                        <div style="font-size:10px;">{{ $prod->result_quantity }} {{ $prod->resultIngredient->unit->name ?? 'kg' }}</div>
                    </td>
                    <td>
                        <ul style="margin:0; padding-left:15px; font-size:10px;">
                            @foreach($prod->items as $item)
                                <li>{{ $item->ingredient->name ?? 'Dihapus' }} ({{ $item->quantity }} {{ $item->ingredient->unit->name ?? 'kg' }})</li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="text-right">Rp {{ number_format($prod->additional_costs, 0, ',', '.') }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($prod->unit_cost, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>