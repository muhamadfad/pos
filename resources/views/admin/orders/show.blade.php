@extends('layouts.admin')

@section('title', 'Detail Penjualan')

@section('content')
<div x-data="{ 
    showPaymentModal: false, 
    amount: {{ $order->remaining_debt ?? 0 }}, 
    formatted_amount: parseInt({{ $order->remaining_debt ?? 0 }}).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'),
    formatAmount(event) {
        let val = event.target.value.replace(/\D/g, '');
        if (val === '') {
            this.amount = 0;
            this.formatted_amount = '';
        } else {
            this.amount = parseInt(val);
            this.formatted_amount = parseInt(val).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
    }
}" class="w-full mx-auto">

    <!-- Actions Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.orders.per-customer', $order->customer_id) }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-gray-600 bg-gray-50 border border-gray-200 rounded-md hover:bg-white transition-all text-sm font-semibold shadow-sm">
                <span class="iconify" data-icon="mdi:arrow-left"></span>
                Kembali
            </a>
            <h1 class="text-lg font-semibold text-black">Detail Penjualan</h1>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 text-slate-700 border border-gray-200 rounded-md hover:bg-white transition-all text-sm font-semibold shadow-sm">
                <span class="iconify text-lg" data-icon="mdi:printer-eye"></span>
                Cetak Invoice
            </button>
            @if($order->payment_status !== 'paid')
            <button @click="showPaymentModal = true"
                class="inline-flex items-center gap-2 px-4 py-2 bg-black text-white text-white rounded-md hover:bg-gray-800 transition-all text-sm font-bold shadow-sm">
                <span class="iconify text-xl" data-icon="mdi:cash-plus"></span>
                Terima Pembayaran
            </button>
            @endif
        </div>
    </div>

    <!-- Invoice Card -->
    <div class="bg-gray-50 rounded-md border border-gray-200 shadow-sm overflow-hidden mb-8">
        <!-- Banner Branding -->
        <div class="bg-[#001529] px-8 py-3 flex justify-between items-center">
            <span class="text-[10px] font-semibold tracking-[0.2em] text-gray-500 uppercase">Official Invoice</span>
            <span class="text-[10px] font-semibold tracking-[0.2em] text-gray-500 uppercase">Kopi Selera Rakyat</span>
        </div>

        <div class="p-8 border-b border-gray-200 flex flex-col md:flex-row justify-between gap-8">
            <div class="space-y-4">
                <div>
                    <h2 class="text-3xl font-semibold text-black tracking-tight">{{ $order->invoice_number }}</h2>
                    <p class="text-gray-500 font-medium">{{ \Carbon\Carbon::parse($order->date)->format('d F Y') }}</p>
                </div>
                <div class="flex items-center gap-2">
                    @php
                    $statusMap = [
                    'paid' => ['bg' => 'bg-gray-50 border border-gray-200', 'text' => 'text-black', 'border' => 'border-gray-200',
                    'label' => 'Lunas', 'icon' => 'mdi:check-decagram'],
                    'partial' => ['bg' => 'bg-gray-50 border border-gray-200', 'text' => 'text-black', 'border' => 'border-gray-200',
                    'label' => 'Sebagian', 'icon' => 'mdi:clock-outline'],
                    'unpaid' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'border' => 'border-orange-200',
                    'label' => 'Belum Lunas', 'icon' => 'mdi:alert-circle-outline'],
                    ];
                    $s = $statusMap[$order->payment_status] ?? $statusMap['unpaid'];
                    @endphp
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-[11px] font-bold {{ $s['bg'] }} {{ $s['text'] }} border {{ $s['border'] }} uppercase tracking-wider">
                        <span class="iconify" data-icon="{{ $s['icon'] }}"></span>
                        {{ $s['label'] }}
                    </span>
                </div>
            </div>

            <div class="text-left md:text-right space-y-4">
                <p class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">Kepada Pelanggan</p>
                <div>
                    <h3 class="text-lg font-semibold text-black">{{ $order->customer->name ?? 'Pelanggan Umum' }}</h3>
                    <p class="text-gray-500 text-sm max-w-xs md:ml-auto">
                        {{ $order->customer->address ?? '-' }}<br>
                        {{ $order->customer->phone ?? '-' }}
                    </p>
                    @if($order->customer)
                    <span
                        class="inline-block mt-2 px-2 py-0.5 bg-slate-100 text-gray-500 rounded text-[10px] font-bold uppercase tracking-widest">{{
                        $order->customer->type }}</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="p-0 sm:p-8">
            <div class="overflow-x-auto font-sans">
                <table class="w-full text-sm">
                    <thead>
                        <tr
                            class="text-gray-500 font-bold text-[11px] uppercase tracking-widest border-b border-gray-200">
                            <th class="px-4 py-2 text-left">Produk / Barang</th>
                            <th class="px-4 py-2 text-center">Jumlah</th>
                            <th class="px-4 py-2 text-right">Harga Jual</th>
                            <th class="px-4 py-4 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($order->items as $item)
                        <tr>
                            <td class="px-6 py-5">
                                <div class="font-bold text-black">{{ $item->custom_name ?: ($item->product->name ?? 'Produk Custom') }}
                                </div>
                                <div class="text-[11px] text-gray-500 uppercase tracking-tight">{{
                                    $item->product->category->name ?? 'Umum' }}</div>
                            </td>
                            <td class="px-6 py-5 text-center font-semibold text-gray-600">
                                {{ floatval($item->quantity) }} <span class="text-[10px] font-medium text-gray-500">{{
                                    $item->product->unit->name ?? 'pcs' }}</span>
                            </td>
                            <td class="px-6 py-5 text-right font-medium text-gray-600 font-mono">
                                Rp {{ number_format($item->selling_price, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-5 text-right font-bold text-black font-mono text-base">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Totals Section -->
        <div class="bg-white p-8 border-t border-gray-200 flex flex-col md:flex-row justify-between gap-8">
            <div class="max-w-xs space-y-4">
                <div>
                    <p class="text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Note Transaksi</p>
                    <p class="text-xs text-gray-500 leading-relaxed italic">Terima kasih atas kepercayaan Anda
                        berbelanja di Kopi Selera Rakyat. Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan tanpa
                        nota ini.</p>
                </div>
                <!-- Signature Area (Hidden on screen, maybe for print) -->
                <div class="pt-4 border-t border-gray-200 hidden print:block">
                    <p class="text-[10px] text-gray-500 mb-8 uppercase font-bold">Diterima Oleh,</p>
                    <p class="text-sm font-bold text-black underline">________________</p>
                </div>
            </div>

            <div class="w-full md:w-80 space-y-3">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500 font-medium">Total Belanja</span>
                    <span class="text-black font-bold">Rp {{ number_format($order->total_amount, 0, ',', '.')
                        }}</span>
                </div>
                <div class="flex justify-between items-center text-sm pt-2">
                    <span class="text-black font-bold">Bayar / DP</span>
                    <span class="text-black font-semibold">Rp {{ number_format($order->total_paid, 0, ',', '.')
                        }}</span>
                </div>
                @if($order->remaining_receivable > 0)
                <div class="flex justify-between items-center text-sm pt-3 border-t-2 border-gray-200">
                    <span class="text-orange-500 font-bold uppercase tracking-widest text-[11px]">Sisa Piutang</span>
                    <span class="text-orange-600 font-semibold text-2xl">Rp {{ number_format($order->remaining_receivable,
                        0, ',', '.') }}</span>
                </div>
                @else
                <div class="flex items-center justify-end gap-2 text-black py-3">
                    <span class="iconify text-xl" data-icon="mdi:check-circle"></span>
                    <span class="text-sm font-semibold uppercase tracking-[0.1em]">Paid in Full</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- History Card -->
    <div class="bg-gray-50 rounded-md border border-gray-200 shadow-sm overflow-hidden mb-6">
        <div class="px-8 py-4 border-b border-gray-200">
            <h3 class="font-bold text-slate-700 text-[11px] uppercase tracking-widest flex items-center gap-2">
                <span class="iconify" data-icon="mdi:history"></span>
                Log Penerimaan Kas
            </h3>
        </div>
        <div class="p-6">
            @if($order->payments->isNotEmpty())
            <div class="space-y-4">
                @foreach($order->payments as $payment)
                <div class="flex items-center justify-between p-4 bg-white rounded-md border border-gray-200">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-md bg-gray-50 flex items-center justify-center text-black border border-gray-200">
                            <span class="iconify" data-icon="mdi:check"></span>
                        </div>
                        <div>
                            <p class="font-bold text-black text-sm">Rp {{ number_format($payment->amount, 0, ',',
                                '.') }}</p>
                            <p class="text-[10px] text-gray-500 font-medium uppercase">{{
                                \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }} • {{
                                $payment->payment_method }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-center text-gray-500 text-sm italic py-2">Belum ada cicilan pembayaran.</p>
            @endif
        </div>
    </div>

    <!-- Receiver Modal (Alpine) -->
    <template x-if="showPaymentModal">
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showPaymentModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-10 bg-[#001529]/60 backdrop-blur-sm transition-opacity" aria-hidden="true"
                    @click="showPaymentModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showPaymentModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative z-20 inline-block align-bottom bg-gray-50 rounded-md text-left overflow-hidden shadow-sm transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form action="{{ route('admin.orders.receive', $order->id) }}" method="POST">
                        @csrf
                        <div class="p-8">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-semibold text-black tracking-tight" id="modal-title">💰 Terima
                                    Piutang</h3>
                                <button type="button" @click="showPaymentModal = false"
                                    class="text-gray-500 hover:text-gray-600 transition-colors">
                                    <span class="iconify text-2xl" data-icon="mdi:close"></span>
                                </button>
                            </div>

                            <div class="space-y-5">
                                <div
                                    class="p-4 bg-orange-50 border border-orange-100 rounded-md flex justify-between items-center">
                                    <span class="text-orange-600 font-bold text-sm uppercase">Sisa Tagihan</span>
                                    <span class="text-orange-700 font-semibold text-lg">Rp {{
                                        number_format($order->remaining_receivable, 0, ',', '.') }}</span>
                                </div>

                                <div>
                                    <label
                                        class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-1.5 ml-1">Tgl
                                        Penerimaan</label>
                                    <input type="date" name="payment_date"
                                        class="w-full bg-white border border-gray-200 rounded-md px-4 py-3 text-sm focus:outline-none focus:ring-4 focus:ring-[#1677ff]/10 focus:border-gray-200 transition-all font-semibold"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>

                                <div>
                                    <label
                                        class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-1.5 ml-1">Jumlah
                                        Terima</label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                                            Rp</div>
                                        <input type="text"
                                            class="w-full pl-10 pr-4 py-3.5 bg-white border border-gray-200 rounded-md text-lg font-semibold text-black focus:outline-none focus:ring-4 focus:ring-[#1677ff]/10 focus:border-gray-200 transition-all"
                                            :value="formatted_amount" @input="formatAmount($event)" @focus="if(amount === 0) formatted_amount = ''" @blur="if(!formatted_amount) { amount = 0; formatted_amount = '0'; }" required>
                                        <input type="hidden" name="amount" :value="amount">
                                    </div>
                                </div>

                                <div>
                                    <label
                                        class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-1.5 ml-1">Metode
                                        Bayar</label>
                                    <select name="payment_method"
                                        class="w-full bg-white border border-gray-200 rounded-md px-4 py-3 text-sm focus:outline-none focus:ring-4 focus:ring-[#1677ff]/10 focus:border-gray-200 transition-all font-semibold uppercase">
                                        <option value="cash">🛍️ Tunai / Cash</option>
                                        <option value="transfer">📱 Transfer Bank</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="p-8 bg-white border-t border-gray-200 flex gap-3">
                            <button type="button" @click="showPaymentModal = false"
                                class="flex-1 bg-gray-50 border border-gray-200 text-gray-600 py-3 rounded-md font-bold hover:bg-white transition-all">
                                Batal
                            </button>
                            <button type="submit"
                                class="flex-1 bg-black text-white py-3 rounded-md font-bold hover:bg-gray-800 shadow-sm  active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                                <span class="iconify text-lg" data-icon="mdi:check-circle-outline"></span>
                                Konfirmasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

</div>
@endsection
