@extends('layouts.admin')

@section('title', 'Tambah Pembelian')

@section('content')
<div x-data="purchaseForm()" class="max-w-6xl mx-auto">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-black">Tambah Pembelian Pusat</h1>
            <p class="text-sm text-gray-500 mt-1">Pembelian barang khusus untuk masuk ke Gudang Pusat.</p>
        </div>
        <a href="{{ route('admin.central-purchases.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 text-gray-600 bg-gray-50 border border-gray-200 rounded-md hover:bg-white transition-all text-sm font-semibold shadow-sm">
            <span class="iconify" data-icon="mdi:arrow-left"></span>
            Kembali
        </a>
    </div>

    <form action="{{ route('admin.central-purchases.store') }}" method="POST" @submit="handleFormSubmit" novalidate>
        @csrf

        <div class="bg-gray-50 rounded-md border border-gray-200 shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div x-data="{ selectedBranchId: '{{ $pusat->id }}' }">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5 flex justify-between">
                        <span>Sumber Dana / Kas</span>
                        <span class="text-black normal-case" x-show="window.__branchCash[selectedBranchId] !== undefined">
                            Sisa Kasir: <span x-text="formatCurrency(window.__branchCash[selectedBranchId])"></span>
                        </span>
                    </label>
                    <select name="branch_id" x-model="selectedBranchId" class="w-full bg-white border border-gray-200 rounded-md px-4 py-2.5 text-sm focus:outline-none focus:ring-4 focus:ring-[#1677ff]/10 focus:border-gray-200 transition-all font-semibold" required>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $branch->id == $pusat->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Supplier (Opsional)</label>
                    <select name="supplier_id" class="w-full bg-white border border-gray-200 rounded-md px-4 py-2.5 text-sm focus:outline-none focus:ring-4 focus:ring-[#1677ff]/10 focus:border-gray-200 transition-all font-semibold searchable-select">
                        <option value="">-- Tanpa Supplier --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">No. Referensi (Opsional)</label>
                    <input type="text" name="reference_number" class="w-full bg-white border border-gray-200 rounded-md px-4 py-2.5 text-sm focus:outline-none focus:ring-4 focus:ring-[#1677ff]/10 focus:border-gray-200 transition-all font-semibold" placeholder="INV/2026/...">
                </div>
            </div>
        </div>

    <!-- Error Alerts -->
    @if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4 flex items-start gap-3">
        <span class="iconify text-red-500 text-xl shrink-0" data-icon="mdi:alert-circle"></span>
        <ul class="text-sm text-red-700 space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left Column: Items -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-gray-50 rounded-md border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-4 py-2 bg-white border-b border-gray-200 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="iconify text-gray-500" data-icon="mdi:format-list-bulleted"></span>
                            <h3 class="font-bold text-slate-700 tracking-tight">Daftar Bahan Baku</h3>
                        </div>
                        <button type="button" @click="addRow()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-black text-white rounded-md text-[12px] font-bold hover:bg-gray-800 transition-all shadow-sm">
                            <span class="iconify" data-icon="mdi:plus"></span>
                            Tambah Baris
                        </button>
                    </div>

                    <div class="overflow-x-auto font-sans">
                        <table class="text-sm" style="width:1000px" >
                            <thead 
                                class="bg-slate-100/50 text-gray-500 uppercase text-[10px] font-bold tracking-widest border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-2 text-left w-20">Bahan Baku</th>
                                    <th class="px-4 py-2 text-center w-20">Unit</th>
                                    <th class="px-4 py-2 text-center w-20">Jumlah</th>
                                    <th class="px-4 py-2 text-right w-50">Harga Beli</th>
                                    <th class="px-4 py-2 text-right w-40">Subtotal</th>
                                    <th class="px-4 py-2 text-center w-12 text-transparent">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="hover:bg-white transition-colors">
                                        <!-- Ingredient Combobox -->
                                        <td class="px-4 py-3 min-w-[200px]">
                                            @include('components.ingredient-combobox', ['priceKey' => 'purchase_price',
                                            'showStock' => false])
                                        </td>
                                        <!-- Unit Display -->
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-gray-500 text-xs font-medium"
                                                x-text="item.unit_name || '-'"></span>
                                        </td>
                                        <!-- Quantity -->
                                        <td class="px-4 py-3">
                                            <input type="number" :name="'items[' + index + '][quantity]'" min="1" step="any"
                                                class="w-full bg-gray-50 border border-gray-200 rounded-md px-2 py-2 text-sm text-center focus:outline-none focus:ring-4 focus:ring-[#1677ff]/10 focus:border-gray-200 transition-all font-bold"
                                                x-model="item.quantity">
                                        </td>
                                        <!-- Purchase Price -->
                                        <td class="px-4 py-3">
                                            <div class="relative">
                                                <div
                                                    class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none text-gray-500 text-xs">
                                                    Rp</div>
                                                <input type="text"
                                                    class="w-full pl-7 pr-2 py-2 bg-gray-50 border border-gray-200 rounded-md text-sm text-right focus:outline-none focus:ring-4 focus:ring-[#1677ff]/10 focus:border-gray-200 transition-all font-bold"
                                                    :value="item.formatted_price"
                                                    @input="formatInputPrice(item, $event)"
                                                    @focus="if(item.price === 0) item.formatted_price = ''"
                                                    @blur="if(!item.formatted_price) { item.price = 0; item.formatted_price = '0'; }">
                                                <input type="hidden" :name="'items[' + index + '][purchase_price]'" :value="item.price">
                                            </div>
                                        </td>
                                        <!-- Subtotal -->
                                        <td class="px-4 py-3 text-right whitespace-nowrap">
                                            <span class="font-bold text-black"
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
            </div>

            <!-- Right Column: Finalization -->
            <div class="space-y-6">
                <div class="bg-gray-50 rounded-md border border-gray-200 shadow-sm p-6 space-y-6">
                    <div>
                        <h3 class="font-bold text-slate-700 mb-4 flex items-center gap-2">
                            <span class="iconify text-gray-500" data-icon="mdi:cash-multiple"></span>
                            Pembayaran & Tanggal
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Tanggal
                                    Transaksi</label>
                                <input type="date" name="date"
                                    class="w-full bg-white border border-gray-200 rounded-md px-4 py-2.5 text-sm focus:outline-none focus:ring-4 focus:ring-[#1677ff]/10 focus:border-gray-200 transition-all font-semibold"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div>
                                <label
                                    class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Jumlah
                                    Dibayar (DP)</label>
                                <div class="relative">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                                        Rp</div>
                                    <input type="text"
                                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-4 focus:ring-[#1677ff]/10 focus:border-gray-200 transition-all font-bold text-black"
                                        :value="formatted_paid"
                                        @input="formatInputPaid($event)"
                                        @focus="if(paid_amount === 0) formatted_paid = ''"
                                        @blur="if(!formatted_paid) { paid_amount = 0; formatted_paid = '0'; }">
                                    <input type="number" name="paid" x-model="paid_amount" style="display:none;">
                                </div>
                                <p class="text-[11px] text-gray-500 mt-2 italic px-1">Isi 0 jika transaksi ini
                                    merupakan hutang penuh</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-200 flex flex-col gap-2">
                        <div class="flex justify-between items-center px-1">
                            <span class="text-gray-500 font-medium">Grand Total</span>
                            <span class="text-2xl font-semibold text-black"
                                x-text="formatCurrency(calculateGrandTotal())"></span>
                        </div>
                        <div class="flex justify-between items-center px-1"
                            x-show="calculateGrandTotal() - paid_amount > 0">
                            <span class="text-red-500 text-xs font-bold uppercase tracking-wider">Sisa Hutang</span>
                            <span class="text-red-600 font-bold"
                                x-text="formatCurrency(calculateGrandTotal() - paid_amount)"></span>
                        </div>
                        <template x-if="paid_amount >= calculateGrandTotal() && calculateGrandTotal() > 0">
                            <div
                                class="flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-200 text-black rounded-md text-[11px] font-bold uppercase tracking-widest border border-gray-200 mt-2">
                                <span class="iconify text-base" data-icon="mdi:check-decagram"></span>
                                Transaksi Lunas
                            </div>
                        </template>
                    </div>

                    <div class="pt-4 flex flex-col gap-3">
                        <button type="submit"
                            class="w-full bg-black text-white py-4 rounded-md font-bold hover:bg-gray-800 active:scale-[0.98] transition-all shadow-sm  flex items-center justify-center gap-2 disabled:bg-slate-400 disabled:cursor-not-allowed"
                            :disabled="!items.some(item => item.ingredient_id && item.quantity > 0 && item.price > 0)">
                            <span class="iconify text-xl" data-icon="mdi:content-save-outline"></span>
                            Simpan Transaksi
                        </button>
                        <a href="{{ route('admin.central-purchases.index') }}"
                            class="text-center text-gray-500 text-sm font-semibold hover:text-gray-600 py-2 transition-colors">
                            Batalkan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    window.__branchCash = {!! json_encode($branchCash) !!};
    window.__purchaseIngredients = {!! json_encode($ingredients->map(fn($p) => [
        'id'             => $p->id,
        'name'           => $p->name,
        'purchase_price' => (float) $p->unit_cost,
        'stock'          => (float) $p->current_stock,
        'unit_name'      => $p->unit->name ?? 'pcs',
    ])->values()) !!};

    function purchaseForm() {
    return {
        _ingredients: window.__purchaseIngredients ?? [],
        items: [
            { ingredient_id: '', quantity: 1, price: 0, formatted_price: '0', unit_name: '', _search: '', _open: false, _dropPos: null }
        ],
        paid_amount: 0,
        formatted_paid: '0',
        formatted_paid: '0',

        addRow() {
            this.items.push({ ingredient_id: '', quantity: 1, price: 0, formatted_price: '0', unit_name: '', _search: '', _open: false, _dropPos: null });
        },

        removeRow(index) {
            this.items.splice(index, 1);
        },

        calculateGrandTotal() {
            return this.items.reduce((total, item) => total + (item.quantity * item.price), 0);
        },

        formatCurrency(val) {
            return 'Rp ' + parseInt(val).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        },
        
        formatInputPrice(item, event) {
            let val = event.target.value.replace(/\D/g, '');
            if (val === '') {
                item.price = 0;
                item.formatted_price = '';
            } else {
                item.price = parseInt(val);
                item.formatted_price = parseInt(val).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }
        },

        formatInputPaid(event) {
            let val = event.target.value.replace(/\D/g, '');
            if (val === '') {
                this.paid_amount = 0;
                this.formatted_paid = '';
            } else {
                this.paid_amount = parseInt(val);
                this.formatted_paid = parseInt(val).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }
        },

        handleFormSubmit(e) {
            // Filter hanya item yang memiliki ingredient_id dan quantity > 0
            const validItems = this.items.filter(item => item.ingredient_id && item.quantity > 0 && item.price > 0);
            
            if (validItems.length === 0) {
                e.preventDefault();
                alert('Pilih minimal 1 bahan baku dengan quantity dan harga yang valid!');
                return;
            }

            // Validasi tanggal harus diisi
            const dateInput = e.target.querySelector('input[name="date"]');
            if (!dateInput.value) {
                e.preventDefault();
                alert('Tanggal transaksi harus diisi!');
                dateInput.focus();
                return;
            }

            // Update items array agar hanya yang valid yang dikirim
            // Hapus input fields untuk item yang tidak valid
            const form = e.target;
            const inputs = form.querySelectorAll('input[name^="items"]');
            
            inputs.forEach((input, idx) => {
                const itemMatch = input.name.match(/items\[(\d+)\]/);
                if (itemMatch) {
                    const itemIndex = parseInt(itemMatch[1]);
                    const item = this.items[itemIndex];
                    
                    // Jika item tidak valid, disable input agar tidak terkirim
                    if (!item || !item.ingredient_id || item.quantity <= 0 || item.price <= 0) {
                        input.disabled = true;
                    }
                }
            });

            // Form akan di-submit, input yang disabled tidak akan terkirim
        }
    }
}
</script>
@endsection
