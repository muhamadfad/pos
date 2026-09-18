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
        .section-title { font-size: 14px; font-weight: bold; margin-top: 30px; margin-bottom: 10px; color: #2d3748; background-color: #e2e8f0; padding: 5px 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 8px 10px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background-color: #f7fafc; font-weight: bold; font-size: 10px; color: #4a5568; text-transform: uppercase; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-emerald { color: #059669; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="company">Resto Coto Makasar</div>
            <div class="report-title">Laporan Lengkap Logistik Pusat (Produksi, Distribusi, Return)</div>
            @if($request->has('start_date') && $request->has('end_date'))
                <div style="font-size: 10px; margin-top: 4px;">Periode: {{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}</div>
            @endif
            <div style="font-size: 10px; margin-top: 4px;">Dicetak pada: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</div>
        </div>

        <div class="section-title">1. Proses Produksi (Olahan)</div>
        <table>
            <thead>
                <tr>
                    <th>No. Ref & Tanggal</th>
                    <th>Hasil Produksi (Bersih)</th>
                    <th>Bahan Mentah (Kotor)</th>
                    <th>Catatan</th>
                    <th class="text-right">Biaya Tambahan</th>
                    <th class="text-right">HPP / Unit</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productions as $prod)
                <tr>
                    <td>
                        <div class="font-bold">{{ $prod->reference_number ?: '-' }}</div>
                        <div style="font-size:10px; color:#718096;">{{ \Carbon\Carbon::parse($prod->date)->format('d M Y') }}</div>
                    </td>
                    <td>
                        <div class="font-bold text-emerald">{{ $prod->resultIngredient->name ?? 'Dihapus' }}</div>
                        <div style="font-size:10px;">{{ floatval($prod->result_quantity) }} {{ $prod->resultIngredient->unit->name ?? 'kg' }}</div>
                    </td>
                    <td>
                        <ul style="margin:0; padding-left:15px; font-size:10px;">
                            @foreach($prod->items as $item)
                                <li>{{ $item->ingredient->name ?? 'Dihapus' }} ({{ floatval($item->quantity) }} {{ $item->ingredient->unit->name ?? 'kg' }})</li>
                            @endforeach
                        </ul>
                    </td>
                    <td style="font-size: 10px; max-width: 150px; word-wrap: break-word;">{{ $prod->notes ?: '-' }}</td>
                    <td class="text-right">Rp {{ number_format($prod->additional_costs, 0, ',', '.') }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($prod->unit_cost, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">Tidak ada data produksi.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div style="page-break-after: always;"></div>
        
        <div class="section-title">2. Distribusi Cabang</div>
        <table>
            <thead>
                <tr>
                    <th>No. Ref & Tanggal</th>
                    <th>Tujuan Cabang</th>
                    <th>Daftar Item</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transfers as $transfer)
                <tr>
                    <td>
                        <div class="font-bold">{{ $transfer->reference_number ?: '-' }}</div>
                        <div style="font-size:10px; color:#718096;">{{ \Carbon\Carbon::parse($transfer->transfer_date)->format('d M Y') }}</div>
                    </td>
                    <td>{{ $transfer->toBranch->name ?? '-' }}</td>
                    <td>
                        <ul style="margin:0; padding-left:15px; font-size:10px;">
                            @forelse($transfer->items as $item)
                                <li>{{ $item->ingredient->name ?? 'Dihapus' }} ({{ floatval($item->quantity) }} {{ $item->ingredient->unit->name ?? '' }})</li>
                            @empty
                                <li>-</li>
                            @endforelse
                        </ul>
                    </td>
                    <td>{{ $transfer->notes ?: '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center">Tidak ada data distribusi.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div style="page-break-after: always;"></div>

        <div class="section-title">3. Return Cabang</div>
        <table>
            <thead>
                <tr>
                    <th>No. Ref & Tanggal</th>
                    <th>Asal Cabang</th>
                    <th>Total Olahan per Item</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($returns as $return)
                <tr>
                    <td>
                        <div class="font-bold">{{ $return->reference_number ?: '-' }}</div>
                        <div style="font-size:10px; color:#718096;">{{ \Carbon\Carbon::parse($return->transfer_date)->format('d M Y') }}</div>
                    </td>
                    <td>{{ $return->fromBranch->name ?? '-' }}</td>
                    <td>
                        <ul style="margin:0; padding-left:15px; font-size:10px;">
                            @forelse($return->items as $item)
                                <li>{{ $item->ingredient->name ?? 'Dihapus' }} ({{ floatval($item->quantity) }} {{ $item->ingredient->unit->name ?? '' }})</li>
                            @empty
                                <li>-</li>
                            @endforelse
                        </ul>
                    </td>
                    <td>{{ $return->notes ?: '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center">Tidak ada data return.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>