@extends('layouts.admin')

@section('title', 'Distribusi Stok Baru')

@section('content')
<div x-data="transferForm()" class="max-w-6xl mx-auto">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Distribusi Stok Baru</h1>
            <p class="text-sm text-slate-500 mt-1">Kirim bahan baku dari Gudang Pusat ke Cabang.</p>
        </div>
        <a href="{{ route('admin.stock-transfers.index') }}"
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

    <form action="{{ route('admin.stock-transfers.store') }}" method="POST" @submit="handleFormSubmit" novalidate>
        @csrf

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Cabang Tujuan</label>
                    <select name="to_branch_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-semibold" required>
                        <option value="">-- Pilih Cabang --</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tanggal Distribusi</label>
                    <input type="date" name="transfer_date" value="{{ date('Y-m-d') }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-semibold" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Catatan (Opsional)</label>
                    <input type="text" name="notes" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-semibold" placeholder="Contoh: Pengiriman rutin mingguan">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="iconify text-slate-400" data-icon="mdi:format-list-bulleted"></span>
                    <h3 class="font-bold text-slate-700 tracking-tight">Daftar Bahan Baku</h3>
                </div>
                <button type="button" @click="addRow()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white rounded-xl text-[12px] font-bold hover:bg-indigo-700 transition-all shadow-md shadow-indigo-100">
                    <span class="iconify" data-icon="mdi:plus"></span>
                    Tambah Baris
                </button>
            </div>

            <div class="overflow-x-auto font-sans">
                <table class="w-full text-sm">
                    <thead class="bg-slate-100/50 text-slate-500 uppercase text-[10px] font-bold tracking-widest border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3 text-left w-1/2">Bahan Baku (Pusat)</th>
                            <th class="px-6 py-3 text-center">Sisa Stok Pusat</th>
                            <th class="px-6 py-3 text-center">Jumlah Dikirim</th>
                            <th class="px-6 py-3 text-center w-12">Hapus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3">
                                    @include('components.ingredient-combobox', ['priceKey' => 'dummy', 'showStock' => true])
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="font-medium text-slate-600" x-text="item.stock + ' ' + (item.unit_name || '')"></span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <input type="number" :name="`items[${index}][quantity]`" min="0.1" step="any"
                                            class="w-32 bg-white border border-slate-200 rounded-xl px-2 py-2 text-sm text-center focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold"
                                            x-model="item.quantity" :max="item.stock">
                                        <span class="text-xs text-slate-400" x-text="item.unit_name || ''"></span>
                                    </div>
                                    <p x-show="item.quantity > item.stock" class="text-xs text-red-500 text-center mt-1">Melebihi stok!</p>
                                </td>
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

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.stock-transfers.index') }}"
                class="px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-2xl font-bold hover:bg-slate-50 transition-all">
                Batalkan
            </a>
            <button type="submit"
                class="px-6 py-3 bg-indigo-600 text-white rounded-2xl font-bold hover:bg-indigo-700 active:scale-[0.98] transition-all shadow-lg shadow-indigo-100 flex items-center justify-center gap-2 disabled:bg-slate-400 disabled:cursor-not-allowed"
                :disabled="!isValid()">
                <span class="iconify text-xl" data-icon="mdi:send-check-outline"></span>
                Proses Distribusi
            </button>
        </div>
    </form>
</div>

<script>
    window.__purchaseIngredients = {!! json_encode($ingredients->map(fn($p) => [
        'id'             => $p->id,
        'name'           => $p->name,
        'dummy'          => 0, // not used for transfer but needed for combobox
        'stock'          => (float) $p->current_stock,
        'unit_name'      => $p->unit->name ?? 'pcs',
    ])->values()) !!};

    function transferForm() {
        return {
            _ingredients: window.__purchaseIngredients ?? [],
            items: [
                { ingredient_id: '', quantity: 1, stock: 0, unit_name: '', _search: '', _open: false, _dropPos: null }
            ],

            addRow() {
                this.items.push({ ingredient_id: '', quantity: 1, stock: 0, unit_name: '', _search: '', _open: false, _dropPos: null });
            },

            removeRow(index) {
                this.items.splice(index, 1);
            },

            isValid() {
                const validItems = this.items.filter(item => item.ingredient_id && item.quantity > 0 && item.quantity <= item.stock);
                return validItems.length > 0;
            },

            handleFormSubmit(e) {
                const validItems = this.items.filter(item => item.ingredient_id && item.quantity > 0 && item.quantity <= item.stock);
                
                if (validItems.length === 0) {
                    e.preventDefault();
                    alert('Pilih minimal 1 bahan baku dengan quantity valid dan tidak melebihi stok pusat!');
                    return;
                }

                const form = e.target;
                const inputs = form.querySelectorAll('input[name^="items"]');
                
                inputs.forEach((input, idx) => {
                    const itemMatch = input.name.match(/items\[(\d+)\]/);
                    if (itemMatch) {
                        const itemIndex = parseInt(itemMatch[1]);
                        const item = this.items[itemIndex];
                        
                        if (!item || !item.ingredient_id || item.quantity <= 0 || item.quantity > item.stock) {
                            input.disabled = true;
                        }
                    }
                });
            }
        }
    }
</script>
@endsection
