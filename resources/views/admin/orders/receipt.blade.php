<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: monospace; font-size: 12px; width: 220px; }
        .center { text-align: center; }
        .line { border-bottom: 1px dashed #000; margin: 5px 0; }
    </style>
</head>
<body>
    <div class="center">
        <strong>My Store</strong><br>
        Jl. Contoh No.1<br>
        Telp: 08123456789
    </div>
    <div class="line"></div>

    <p>Order ID: {{ $order->id }}<br>
       Tanggal: {{ $order->date }}</p>

    <table width="100%">
        @foreach($order->items as $item)
        <tr>
            <td>{{ $item->product->name }} x{{ $item->quantity }}</td>
            <td style="text-align:right;">{{ number_format($item->subtotal) }}</td>
        </tr>
        @endforeach
    </table>

    <div class="line"></div>
    <p>
        Subtotal: {{ number_format($order->subtotal) }}<br>
        Diskon: {{ number_format($order->discount) }}<br>
        Total: {{ number_format($order->total_amount) }}<br>
        Dibayar: {{ number_format($order->total_paid) }}<br>
        Sisa: {{ number_format($order->remaining_receivable) }}
    </p>
    <div class="center">Terima kasih!</div>
</body>
</html>