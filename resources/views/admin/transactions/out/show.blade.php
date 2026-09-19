@extends('layouts.admin')

@section('title', 'Detail Transaksi')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                <span class="iconify text-blue-600" data-icon="mdi:file-document"></span>
                Detail Transaksi Barang Keluar
            </h2>
            <p class="text-sm text-gray-500">
                {{ \Carbon\Carbon::parse($transaction->date)->format('d M Y') }}
                — {{ $transaction->customer->name }}
            </p>
        </div>

        <a href="{{ route('admin.transactions.out.index') }}"
            class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 text-sm">
            ← Kembali
        </a>
    </div>

    <div class="grid grid-cols-3 gap-4 mb-6">

        <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-xs text-gray-500">Total Transaksi</p>
            <p class="text-lg font-semibold text-gray-800">
                Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-xs text-gray-500">Sudah Diterima</p>
            <p class="text-lg font-semibold text-green-600">
                Rp {{ number_format($transaction->total_paid, 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-xs text-gray-500">Sisa Piutang</p>
            <p class="text-lg font-semibold text-orange-600">
                Rp {{ number_format($transaction->remaining_receivable, 0, ',', '.') }}
            </p>
        </div>

    </div>

    <div class="flex items-center justify-between mb-6">
        <span class="px-3 py-1 rounded-full text-sm font-semibold
            {{ $transaction->payment_status === 'LUNAS'
                ? 'bg-green-100 text-green-700'
                : 'bg-yellow-100 text-yellow-700' }}">
            {{ strtoupper($transaction->payment_status) }}
        </span>

        @if($transaction->payment_status !== 'LUNAS')
        <button onclick="openModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
            💰 Terima Pembayaran
        </button>
        @endif
    </div>

    <div class="overflow-x-auto mb-8 border rounded-lg overflow-hidden border-gray-200">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">Barang</th>
                    <th class="px-4 py-3 text-center">Qty</th>
                    <th class="px-4 py-3 text-right">Harga Jual</th>
                    <th class="px-4 py-3 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($transaction->items as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-800">
                        {{ $item->product->name ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        {{ $item->quantity }}
                    </td>
                    <td class="px-4 py-3 text-right text-blue-600 font-medium">
                        Rp {{ number_format($item->selling_price, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-gray-800">
                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="border rounded-xl p-4 bg-gray-50 border-gray-200">
        <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
            <span class="iconify" data-icon="mdi:history"></span>
            Riwayat Pembayaran
        </h3>

        @if($transaction->payments->isNotEmpty())
        <ul class="space-y-2 text-sm">
            @foreach($transaction->payments as $payment)
            <li class="flex justify-between items-center bg-white p-3 rounded-lg shadow-sm border">
                <div>
                    <p class="font-medium text-gray-800">
                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}
                        — {{ strtoupper($payment->payment_method ?? '-') }}
                    </p>
                </div>
                <span class="text-xs text-gray-400">
                    {{ $payment->created_at->diffForHumans() }}
                </span>
            </li>
            @endforeach
        </ul>
        @else
        <p class="text-sm text-gray-500">Belum ada pembayaran.</p>
        @endif
    </div>

</div>

<!-- PAYMENT MODAL -->
<div id="paymentModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 hidden">

    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">

        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <span class="iconify text-blue-600" data-icon="mdi:cash-multiple"></span>
                Terima Pembayaran Piutang
            </h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>

        <form x-data="{ amount: 0, formatted_amount: '' }" action="{{ route('admin.transactions.out.receive', $transaction->id) }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Terima</label>
                <input type="date" name="payment_date"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    value="{{ date('Y-m-d') }}" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Diterima</label>
                <input type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" :value="formatted_amount" @input="let val = $event.target.value.replace(/\D/g, ''); if(val==='') { amount = 0; formatted_amount = ''; } else { amount = parseInt(val); formatted_amount = parseInt(val).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }" @focus="if(amount === 0) formatted_amount = ''" @blur="if(!formatted_amount) { amount = 0; formatted_amount = '0'; }" required placeholder="Masukkan nominal">
                <input type="hidden" name="amount" :value="amount">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                <select name="payment_method"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="cash">Cash</option>
                    <option value="transfer">Transfer</option>
                    <option value="qris">QRIS</option>
                </select>
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModal()"
                    class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 font-medium flex items-center gap-2">
                    <span class="iconify" data-icon="mdi:content-save"></span>
                    Simpan
                </button>
            </div>
        </form>

    </div>
</div>

<script>
    function openModal() {
    document.getElementById('paymentModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}
</script>
@endsection
