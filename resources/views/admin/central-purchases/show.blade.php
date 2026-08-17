@extends('layouts.admin')

@section('title', 'Detail Pembelian')

@section('content')
<div x-data="{ showPaymentModal: false }" class="w-full mx-auto">

    <!-- Actions Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.central-purchases.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-all text-sm font-semibold shadow-sm">
                <span class="iconify" data-icon="mdi:arrow-left"></span>
                Kembali
            </a>
            <h1 class="text-xl font-bold text-slate-800">Detail Pembelian Pusat</h1>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white text-slate-700 border border-slate-200 rounded-xl hover:bg-slate-50 transition-all text-sm font-semibold shadow-sm">
                <span class="iconify" data-icon="mdi:printer-outline"></span>
                Cetak
            </button>
            @if($purchase->payment_status !== 'paid')
            <button @click="showPaymentModal = true"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all text-sm font-bold shadow-md shadow-indigo-100">
                <span class="iconify text-lg" data-icon="mdi:cash-multiple"></span>
                Bayar Hutang
            </button>
            @endif
        </div>
    </div>

    <!-- Invoice Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden mb-8">
        <!-- Top Section -->
        <div class="p-8 border-b border-slate-100 flex flex-col md:flex-row justify-between gap-8">
            <div class="space-y-4">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-[11px] font-black uppercase tracking-widest border border-indigo-100">
                    <span class="iconify" data-icon="mdi:file-document-outline"></span>
                    Invoice Pembelian
                </div>
                <div>
                    <h2 class="text-3xl font-black text-slate-900 tracking-tight">#PUR-{{ str_pad($purchase->id, 5, '0',
                        STR_PAD_LEFT) }}</h2>
                    <p class="text-slate-500 font-medium">{{ \Carbon\Carbon::parse($purchase->date)->format('d F Y') }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    @php
                    $statusMap = [
                    'paid' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200',
                    'label' => 'Lunas', 'icon' => 'mdi:check-decagram'],
                    'partial' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200',
                    'label' => 'Sebagian', 'icon' => 'mdi:clock-outline'],
                    'unpaid' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-200', 'label' =>
                    'Belum Bayar', 'icon' => 'mdi:alert-circle-outline'],
                    ];
                    $s = $statusMap[$purchase->payment_status] ?? $statusMap['unpaid'];
                    @endphp
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold {{ $s['bg'] }} {{ $s['text'] }} border {{ $s['border'] }} uppercase tracking-wider">
                        <span class="iconify" data-icon="{{ $s['icon'] }}"></span>
                        {{ $s['label'] }}
                    </span>
                </div>
            </div>

            <div class="text-left md:text-right space-y-4">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Supplier</p>
                <div>
                    <h3 class="text-xl font-bold text-slate-900">{{ $purchase->supplier->name ?? 'Supplier Umum' }}</h3>
                    <p class="text-slate-500 text-sm max-w-xs md:ml-auto">
                        {{ $purchase->supplier->address ?? '-' }}<br>
                        {{ $purchase->supplier->phone ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="p-0 sm:p-8">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr
                            class="text-slate-400 font-bold text-[11px] uppercase tracking-widest border-b border-slate-100">
                            <th class="px-4 py-4 text-left">Item Deskrpsi</th>
                            <th class="px-4 py-4 text-center">Qty</th>
                            <th class="px-4 py-4 text-right">Harga Satuan</th>
                            <th class="px-4 py-4 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($purchase->items as $item)
                        <tr>
                            <td class="px-4 py-5">
                                <div class="font-bold text-slate-800">{{ $item->ingredient->name ?? 'Unknown Product' }}
                                </div>
                                <div class="text-[11px] text-slate-400">SKU: #{{ str_pad($item->ingredient_id, 4, '0',
                                    STR_PAD_LEFT) }}</div>
                            </td>
                            <td class="px-4 py-5 text-center font-bold text-slate-600">
                                {{ floatval($item->quantity) }} <span class="text-[10px] font-medium text-slate-400">{{
                                    $item->ingredient->unit->name ?? 'pcs' }}</span>
                            </td>
                            <td class="px-4 py-5 text-right font-medium text-slate-600">
                                Rp {{ number_format($item->purchase_price, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-5 text-right font-bold text-slate-900">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bottom Totals -->
        <div class="bg-slate-50/50 p-8 border-t border-slate-100">
            <div class="flex flex-col md:flex-row justify-between gap-8">
                <!-- Notes / Info -->
                <div class="max-w-xs">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Informasi Tambahan
                    </p>
                    <p class="text-[12px] text-slate-500 leading-relaxed italic">
                        Transaksi ini mencatat barang masuk dari supplier untuk menambah stok opname.
                        @if($purchase->payment_status !== 'paid')
                        Pastikan pembayaran hutang diselesaikan tepat waktu.
                        @endif
                    </p>
                </div>

                <!-- Totals -->
                <div class="w-full md:w-80 space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500 font-medium">Subtotal</span>
                        <span class="text-slate-800 font-bold">Rp {{ number_format($purchase->total_amount, 0, ',', '.')
                            }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm pt-2">
                        <span class="text-emerald-600 font-bold">Total Terbayar</span>
                        <span class="text-emerald-700 font-black">Rp {{ number_format($purchase->total_paid, 0, ',',
                            '.') }}</span>
                    </div>
                    @if($purchase->remaining_debt > 0)
                    <div class="flex justify-between items-center text-sm pt-2 border-t border-slate-200">
                        <span class="text-red-500 font-bold uppercase tracking-wider text-[11px]">Sisa Hutang</span>
                        <span class="text-red-600 font-black text-xl">Rp {{ number_format($purchase->remaining_debt, 0,
                            ',', '.') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="iconify text-slate-400" data-icon="mdi:history"></span>
                <h3 class="font-bold text-slate-700 tracking-tight text-sm uppercase">Riwayat Pembayaran</h3>
            </div>
        </div>
        <div class="p-8">
            @if($purchase->payments->isNotEmpty())
            <div class="relative space-y-4">
                @foreach($purchase->payments as $payment)
                <div class="flex items-start gap-4">
                    <div
                        class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center shrink-0 border border-slate-100">
                        <span class="iconify text-slate-400" data-icon="mdi:cash-check"></span>
                    </div>
                    <div class="flex-1 border-b border-slate-50 pb-4 flex justify-between items-center">
                        <div>
                            <p class="font-bold text-slate-800">Rp {{ number_format($payment->amount, 0, ',', '.') }}
                            </p>
                            <p class="text-[12px] text-slate-400">
                                {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}
                                @if($payment->payment_method)
                                <span class="mx-1">â€¢</span> {{ strtoupper($payment->payment_method) }}
                                @endif
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="text-[11px] font-bold text-slate-300 uppercase">{{
                                $payment->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-4">
                <p class="text-slate-400 text-sm italic">Belum ada riwayat pembayaran.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Payment Modal (Alpine.js) -->
    <template x-if="showPaymentModal">
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showPaymentModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true"
                    @click="showPaymentModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showPaymentModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form action="{{ route('admin.purchases.pay', $purchase->id) }}" method="POST">
                        @csrf
                        <div class="p-8">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-black text-slate-900 tracking-tight" id="modal-title">ðŸ’° Bayar
                                    Hutang</h3>
                                <button type="button" @click="showPaymentModal = false"
                                    class="text-slate-400 hover:text-slate-600 transition-colors">
                                    <span class="iconify text-2xl" data-icon="mdi:close"></span>
                                </button>
                            </div>

                            <div class="space-y-5">
                                <div
                                    class="p-4 bg-indigo-50 border border-indigo-100 rounded-2xl flex justify-between items-center">
                                    <span class="text-indigo-600 font-bold text-sm uppercase">Sisa Hutang</span>
                                    <span class="text-indigo-700 font-black text-lg">Rp {{
                                        number_format($purchase->remaining_debt, 0, ',', '.') }}</span>
                                </div>

                                <div>
                                    <label
                                        class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Tanggal
                                        Bayar</label>
                                    <input type="date" name="payment_date"
                                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-semibold"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>

                                <div>
                                    <label
                                        class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Jumlah
                                        Bayar</label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                            Rp</div>
                                        <input type="number" name="amount" min="1" max="{{ $purchase->remaining_debt }}"
                                            class="w-full pl-10 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-lg font-black text-indigo-600 focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all"
                                            value="{{ $purchase->remaining_debt }}" required>
                                    </div>
                                </div>

                                <div>
                                    <label
                                        class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Metode</label>
                                    <select name="payment_method"
                                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-semibold uppercase">
                                        <option value="cash"><span class="iconify inline-block" data-icon="mdi:cash"></span> Tunai / Cash</option>
                                        <option value="transfer"><span class="iconify inline-block" data-icon="mdi:bank-outline"></span> Transfer Bank</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="p-8 bg-slate-50 border-t border-slate-100 flex gap-3">
                            <button type="button" @click="showPaymentModal = false"
                                class="flex-1 bg-white border border-slate-200 text-slate-600 py-3 rounded-2xl font-bold hover:bg-slate-50 transition-all">
                                Batal
                            </button>
                            <button type="submit"
                                class="flex-1 bg-indigo-600 text-white py-3 rounded-2xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-100 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                                <span class="iconify text-lg" data-icon="mdi:check-circle-outline"></span>
                                Proses Bayar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

</div>
@endsection
