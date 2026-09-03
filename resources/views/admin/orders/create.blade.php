@extends('layouts.admin')

@section('title', 'Tambah Penjualan')

@section('content')
<div x-data="orderForm()" class="max-w-6xl mx-auto">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Tambah Penjualan</h1>
            <div class="flex items-center gap-2 mt-1">
                <span
                    class="px-2 py-0.5 bg-emerald-50 text-emerald-600 rounded text-[12px] font-bold border border-emerald-100">CUSTOMER</span>
                <p class="text-sm font-bold text-slate-600">{{ $customer->name }}</p>
                <span class="text-slate-300 mx-1">/</span>
                <span class="text-[11px] font-bold text-slate-400 uppercase">{{ $customer->type ?? 'retail' }}</span>
            </div>
        </div>
        <a href="{{ route('admin.orders.per-customer', $customer->id) }}"
            class="inline-flex items-center gap-2 px-4 py-2 text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-all text-sm font-semibold shadow-sm">
            <span class="iconify" data-icon="mdi:arrow-left"></span>
            Kembali
        </a>
    </div>

    <!-- Error Alerts -->
    @if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4 flex items-start gap-3">
        <span class="iconify text-red-500 text-xl shrink-0" data-icon="mdi:alert-circle"></span>
        <ul class="text-sm text-red-700 space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.orders.store', $customer->id) }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left Column: Items -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="iconify text-slate-400" data-icon="mdi:cart-outline"></span>
                            <h3 class="font-bold text-slate-700 tracking-tight">Daftar Belanja</h3>
                        </div>
                        <button type="button" @click="addRow()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white rounded-xl text-[12px] font-bold hover:bg-indigo-700 transition-all shadow-md shadow-indigo-100">
                            <span class="iconify" data-icon="mdi:plus"></span>
                            Tambah Item
                        </button>
                    </div>

                    <div class="overflow-x-auto font-sans">
                        <table class="w-full text-sm">
                            <thead
                                class="bg-slate-100/50 text-slate-500 uppercase text-[10px] font-bold tracking-widest border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-3 text-left">Produk</th>
                                    <th class="px-6 py-3 text-center w-24">Stok</th>
                                    <th class="px-6 py-3 text-center w-24">Jumlah</th>
                                    <th class="px-6 py-3 text-right w-40">Harga Jual</th>
                                    <th class="px-6 py-3 text-right w-40">Subtotal</th>
                                    <th class="px-6 py-3 text-center w-12 text-transparent">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <!-- Product Combobox -->
                                        <td class="px-4 py-3 min-w-[240px]">
                                            @include('components.product-combobox', ['priceKey' => 'selling_price',
                                            'showStock' => true])
                                        </td>
                                        <!-- Stock Info -->
                                        <td class="px-4 py-3 text-center">
                                            <span :class="item.stock_available <= 0 ? 'text-red-500' : 'text-slate-400'"
                                                class="text-xs font-bold" x-text="item.stock_available"></span>
                                            <span class="text-[10px] text-slate-400" x-text="item.unit_name"></span>
                                        </td>
                                        <!-- Quantity -->
                                        <td class="px-4 py-3">
                                            <input type="number" :name="'items[' + index + '][quantity]'" min="1" step="any"
                                                class="w-full bg-white border border-slate-200 rounded-xl px-2 py-2 text-sm text-center focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold"
                                                :class="item.quantity > item.stock_available ? 'text-red-600 border-red-200' : ''"
                                                x-model="item.quantity" required>
                                        </td>
                                        <!-- Selling Price -->
                                        <td class="px-4 py-3">
                                            <div class="relative">
                                                <div
                                                    class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none text-slate-400 text-xs">
                                                    Rp</div>
                                                <input type="text"
                                                    class="w-full pl-7 pr-2 py-2 bg-white border border-slate-200 rounded-xl text-sm text-right focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold text-emerald-600"
                                                    :value="item.formatted_price"
                                                    @input="formatInputPrice(item, $event)"
                                                    @focus="if(item.price === 0) item.formatted_price = ''"
                                                    @blur="if(!item.formatted_price) { item.price = 0; item.formatted_price = '0'; }" required>
                                                <input type="hidden" :name="'items[' + index + '][selling_price]'" x-model="item.price">
                                            </div>
                                        </td>
                                        <!-- Subtotal -->
                                        <td class="px-4 py-3 text-right whitespace-nowrap">
                                            <span class="font-bold text-slate-800"
                                                x-text="formatCurrency(item.quantity * item.price)"></span>
                                        </td>
                                        <!-- Actions -->
                                        <td class="px-4 py-3 text-center">
                                            <button type="button" @click="removeRow(index)"
                                                class="text-slate-300 hover:text-red-500 transition-colors p-1">
                                                <span class="iconify text-xl" data-icon="mdi:trash-can-outline"></span>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Stock Warning -->
                <template x-if="hasStockOverload()">
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-center gap-3">
                        <span class="iconify text-amber-500 text-2xl" data-icon="mdi:alert-outline"></span>
                        <p class="text-sm text-amber-800 font-medium">Beberapa item melebihi stok yang tersedia. Tetap
                            lanjutkan?</p>
                    </div>
                </template>
            </div>

            <!-- Right Column: Finalization -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
                    <div>
                        <h3 class="font-bold text-slate-700 mb-4 flex items-center gap-2">
                            <span class="iconify text-slate-400" data-icon="mdi:calculator-variant"></span>
                            Ringkasan Pembayaran
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tanggal
                                    Penjualan</label>
                                <input type="date" name="date"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-semibold"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div>
                                <label
                                    class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Uang
                                    Diterima (DP)</label>
                                <div class="relative">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                        Rp</div>
                                    <input type="text"
                                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold text-emerald-600"
                                        :value="formatted_paid"
                                        @input="formatInputPaid($event)"
                                        @focus="if(paid_amount === 0) formatted_paid = ''"
                                        @blur="if(!formatted_paid) { paid_amount = 0; formatted_paid = '0'; }">
                                    <input type="hidden" name="paid" x-model="paid_amount">
                                </div>
                                <p class="text-[11px] text-slate-400 mt-2 italic px-1 text-center">Biarkan 0 jika belum
                                    ada pembayaran</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100 flex flex-col gap-2">
                        <div class="flex justify-between items-center px-1">
                            <span class="text-slate-500 font-medium font-sans">Total Tagihan</span>
                            <span class="text-3xl font-black text-slate-900"
                                x-text="formatCurrency(calculateGrandTotal())"></span>
                        </div>
                        <div class="flex justify-between items-center px-1"
                            x-show="calculateGrandTotal() - paid_amount > 0">
                            <span class="text-orange-500 text-xs font-bold uppercase tracking-wider">Sisa Piutang</span>
                            <span class="text-orange-600 font-bold"
                                x-text="formatCurrency(calculateGrandTotal() - paid_amount)"></span>
                        </div>
                        <template x-if="paid_amount >= calculateGrandTotal() && calculateGrandTotal() > 0">
                            <div
                                class="flex items-center gap-2 px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-lg text-[11px] font-bold uppercase tracking-widest border border-emerald-100 mt-2">
                                <span class="iconify text-base" data-icon="mdi:check-decagram"></span>
                                Pembayaran Lunas
                            </div>
                        </template>
                    </div>

                    <div class="pt-4 flex flex-col gap-3">
                        <button type="submit"
                            class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-bold hover:bg-indigo-700 active:scale-[0.98] transition-all shadow-lg shadow-indigo-100 flex items-center justify-center gap-2"
                            :disabled="items.length === 0 || !items[0].product_id">
                            <span class="iconify text-xl" data-icon="mdi:check-all"></span>
                            Proses Penjualan
                        </button>
                        <a href="{{ route('admin.orders.per-customer', $customer->id) }}"
                            class="text-center text-slate-400 text-sm font-semibold hover:text-slate-600 py-2 transition-colors">
                            Batalkan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    window.__orderProducts = {!! json_encode($products->map(fn($p) => [
        'id'            => $p->id,
        'name'          => $p->name,
        'selling_price' => (float) $p->selling_price,
        'stock'         => (int)   $p->stock,
        'unit_name'     => $p->unit->name ?? 'pcs',
    ])->values()) !!};

    function orderForm() {
    return {
        _products: window.__orderProducts ?? [],
        items: [
            { product_id: '', quantity: 1, price: 0, stock_available: 0, unit_name: '', _search: '', _open: false, _dropPos: null }
        ],
        paid_amount: 0, formatted_paid: '0',

        addRow() {
            this.items.push({ product_id: '', quantity: 1, price: 0, stock_available: 0, unit_name: '', _search: '', _open: false, _dropPos: null });
        },

        removeRow(index) {
            this.items.splice(index, 1);
        },

        hasStockOverload() {
            return this.items.some(item => item.product_id && item.quantity > item.stock_available);
        },

        calculateGrandTotal() {
            return this.items.reduce((total, item) => total + (item.quantity * item.price), 0);
        },

        formatCurrency(val) {
            return 'Rp ' + parseInt(val).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
    }
}
</script>
@endsection
