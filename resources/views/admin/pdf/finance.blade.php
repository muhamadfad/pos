<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
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
        
        .net-cashflow { background-color: {{ $finance['netCashflow'] >= 0 ? '#10b981' : '#ef4444' }}; color: white; padding: 20px; border-radius: 8px; text-align: center; margin-bottom: 30px; }
        .net-cashflow h3 { margin: 0 0 10px; font-size: 14px; text-transform: uppercase; letter-spacing: 2px; }
        .net-cashflow .value { font-size: 32px; font-weight: bold; margin: 0; }
        
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
        <p>{{ $title }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td>
                <strong>Cabang:</strong> {{ $currentBranchName }}<br>
                <strong>Periode:</strong> 
                @if($startDate && $endDate)
                    {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                @elseif($startDate)
                    {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}
                @else
                    Semua Waktu
                @endif
            </td>
            <td style="text-align: right; vertical-align: top;"><strong>Tanggal Cetak:</strong> {{ date('d/m/Y H:i') }}</td>
        </tr>
    </table>

    <div class="net-cashflow">
        <h3>Saldo Bersih (Net Cashflow)</h3>
        <p class="value">Rp {{ number_format($finance['netCashflow'], 0, ',', '.') }}</p>
        <p style="margin-top: 10px; font-size: 11px;">Pendapatan - Pembayaran Supplier - Operasional</p>
    </div>

    <table class="kpi-container">
        <tr>
            <td>
                <div class="kpi-box" style="border-left: 4px solid #10b981;">
                    <h3>Total Pendapatan</h3>
                    <p class="value">Rp {{ number_format($finance['income'], 0, ',', '.') }}</p>
                </div>
            </td>
            <td>
                <div class="kpi-box" style="border-left: 4px solid #f59e0b;">
                    <h3>HPP (Cost of Goods Sold)</h3>
                    <p class="value">Rp {{ number_format($finance['cost'], 0, ',', '.') }}</p>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="kpi-box" style="border-left: 4px solid #ef4444;">
                    <h3>Biaya Operasional</h3>
                    <p class="value">Rp {{ number_format($finance['expenses'], 0, ',', '.') }}</p>
                </div>
            </td>
            <td>
                <div class="kpi-box" style="border-left: 4px solid #8b5cf6;">
                    <h3>Pembayaran Supplier</h3>
                    <p class="value">Rp {{ number_format($finance['supplierPaid'], 0, ',', '.') }}</p>
                </div>
            </td>
        </tr>
    </table>
    
    <div style="text-align: right; margin-top: 50px;">
        <p>Penanggung Jawab,</p>
        <br><br><br><br>
        <p>_______________________</p>
    </div>
</body>
</html>
