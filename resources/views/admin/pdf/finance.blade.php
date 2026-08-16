<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 13px; }
        
        .info-table { width: 100%; margin-bottom: 20px; font-size: 11px; }
        .info-table td { padding: 3px 0; }
        
        table.finance-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table.finance-table th, table.finance-table td { padding: 8px; border: 1px solid #000; }
        table.finance-table th { background-color: #f0f0f0; font-weight: bold; text-align: left; text-transform: uppercase; font-size: 10px; }
        
        .section-header td { font-weight: bold; background-color: #f9f9f9; text-transform: uppercase; }
        .item-row td:first-child { padding-left: 20px; }
        .total-row td { font-weight: bold; border-top: 2px solid #000; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .net-positive { color: #059669; }
        .net-negative { color: #dc2626; }
        
        .signature-area { width: 100%; margin-top: 40px; page-break-inside: avoid; }
        .signature-box { width: 200px; float: right; text-align: center; }
        
        .page-break { page-break-before: always; }
        .sub-title { font-size: 14px; font-weight: bold; margin-bottom: 10px; text-transform: uppercase; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
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

    <div class="sub-title">1. RINGKASAN KEUANGAN</div>
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
                <td colspan="2">A. Pendapatan (Cash-In)</td>
            </tr>
            <tr class="item-row">
                <td>Total Pendapatan Penjualan</td>
                <td class="text-right">{{ number_format($finance['income'], 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td>Total Cash-In</td>
                <td class="text-right">{{ number_format($finance['income'], 0, ',', '.') }}</td>
            </tr>

            <!-- PENGELUARAN -->
            <tr><td colspan="2" style="border:none; height: 10px;"></td></tr>
            <tr class="section-header">
                <td colspan="2">B. Pengeluaran (Cash-Out)</td>
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
                @php $totalPengeluaran = $finance['expenses'] + $finance['supplierPaid']; @endphp
                <td>Total Cash-Out</td>
                <td class="text-right">{{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
            </tr>

            <!-- RINGKASAN -->
            <tr><td colspan="2" style="border:none; height: 10px;"></td></tr>
            <tr class="section-header">
                <td colspan="2">C. Saldo Bersih</td>
            </tr>
            <tr class="total-row" style="font-size: 13px;">
                <td>NET CASHFLOW (Pendapatan - Pengeluaran)</td>
                <td class="text-right {{ $finance['netCashflow'] >= 0 ? 'net-positive' : 'net-negative' }}">
                    {{ number_format($finance['netCashflow'], 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <div class="header">
        <h1>LAMPIRAN RINCIAN TRANSAKSI</h1>
        <p>{{ $title }}</p>
    </div>

    <div class="sub-title">2. REKAPITULASI PENJUALAN PRODUK</div>
    <table class="finance-table" style="margin-bottom: 30px;">
        <thead>
            <tr>
                <th style="width: 10%; text-align: center;">No</th>
                <th style="width: 70%;">Nama Menu / Produk</th>
                <th style="width: 20%; text-align: center;">Total Terjual (Porsi)</th>
            </tr>
        </thead>
        <tbody>
            @php $productNo = 1; @endphp
            @forelse($productSummary as $productName => $qty)
            <tr>
                <td class="text-center">{{ $productNo++ }}</td>
                <td>{{ $productName }}</td>
                <td class="text-center"><strong>{{ $qty }}</strong></td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center" style="font-style: italic;">Tidak ada data penjualan produk.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="sub-title">3. RINCIAN PENDAPATAN (PENJUALAN)</div>
    <table class="finance-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 13%;">Tanggal</th>
                <th style="width: 17%;">No. Pesanan</th>
                <th style="width: 40%;">Detail Pesanan</th>
                <th style="width: 10%;">Metode</th>
                <th style="width: 15%; text-align: right;">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ordersList as $index => $order)
            <tr>
                <td class="text-center" style="vertical-align: top;">{{ $index + 1 }}</td>
                <td style="vertical-align: top;">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td style="vertical-align: top;">
                    <strong>#{{ $order->id }}</strong><br>
                    <span style="font-size: 9px; color: #555;">{{ $order->customer->name ?? 'Umum' }}</span>
                </td>
                <td style="vertical-align: top;">
                    <ul style="margin: 0; padding-left: 15px; font-size: 10px;">
                        @foreach($order->items as $item)
                            <li>{{ $item->product->name ?? 'Produk' }} (x{{ $item->quantity }})</li>
                        @endforeach
                    </ul>
                </td>
                <td style="vertical-align: top;">{{ ucfirst($order->payment_method) }}</td>
                <td class="text-right" style="vertical-align: top;">{{ number_format($order->total_amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="font-style: italic;">Tidak ada data penjualan pada periode ini.</td>
            </tr>
            @endforelse
            @if($ordersList->count() > 0)
            <tr class="total-row">
                <td colspan="5" class="text-right">Total Pendapatan</td>
                <td class="text-right">{{ number_format($ordersList->sum('total_amount'), 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="sub-title" style="margin-top: 30px;">4. RINCIAN BIAYA OPERASIONAL</div>
    <table class="finance-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 25%;">Kategori</th>
                <th style="width: 35%;">Keterangan</th>
                <th style="width: 20%; text-align: right;">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expensesList as $index => $expense)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($expense->date)->format('d/m/Y') }}</td>
                <td>{{ $expense->category }}</td>
                <td>{{ $expense->description }}</td>
                <td class="text-right">{{ number_format($expense->amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center" style="font-style: italic;">Tidak ada data biaya operasional pada periode ini.</td>
            </tr>
            @endforelse
            @if($expensesList->count() > 0)
            <tr class="total-row">
                <td colspan="4" class="text-right">Total Biaya Operasional</td>
                <td class="text-right">{{ number_format($expensesList->sum('amount'), 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="sub-title" style="margin-top: 30px;">5. RINCIAN PEMBAYARAN SUPPLIER</div>
    <table class="finance-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 25%;">Supplier</th>
                <th style="width: 35%;">Keterangan / Status</th>
                <th style="width: 20%; text-align: right;">Jumlah Dibayar (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchasesList as $index => $purchase)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($purchase->date)->format('d/m/Y') }}</td>
                <td>{{ $purchase->supplier->name ?? '-' }}</td>
                <td>Status: {{ ucfirst($purchase->payment_status) }}</td>
                <td class="text-right">{{ number_format($purchase->total_paid, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center" style="font-style: italic;">Tidak ada data pembayaran supplier pada periode ini.</td>
            </tr>
            @endforelse
            @if($purchasesList->count() > 0)
            <tr class="total-row">
                <td colspan="4" class="text-right">Total Pembayaran Supplier</td>
                <td class="text-right">{{ number_format($purchasesList->sum('total_paid'), 0, ',', '.') }}</td>
            </tr>
            @endif
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
