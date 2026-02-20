@extends('layouts.admin')

@section('title', isset($expense) ? 'Edit Pengeluaran' : 'Catat Pengeluaran')

@section('content')
<div class="space-y-8">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                <span class="p-2 bg-rose-600 text-white rounded-2xl shadow-lg shadow-rose-100">
                    <span class="iconify" data-icon="{{ isset($expense) ? 'mdi:pencil' : 'mdi:cash-minus' }}"></span>
                </span>
                {{ isset($expense) ? 'Edit Pengeluaran' : 'Catat Pengeluaran' }}
            </h1>
            <p class="text-slate-500 font-medium mt-1">
                {{ isset($expense) ? 'Perbarui data pengeluaran yang sudah dicatat.' : 'Tambahkan catatan pengeluaran
                operasional baru.' }}
            </p>
        </div>
        <a href="{{ route('admin.expenses.index') }}"
            class="flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 rounded-2xl font-bold text-sm text-slate-600 hover:bg-slate-50 transition-all shadow-sm">
            <span class="iconify" data-icon="mdi:arrow-left"></span>
            Kembali
        </a>
    </div>

    @if($errors->any())
    <div class="p-4 bg-rose-50 border border-rose-100 text-rose-700 rounded-2xl">
        <ul class="list-disc list-inside space-y-1 text-sm font-medium">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Form -->
    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden p-8 md:p-12">
        <form action="{{ isset($expense) ? route('admin.expenses.update', $expense) : route('admin.expenses.store') }}"
            method="POST" class="space-y-10">
            @csrf
            @if(isset($expense))
            @method('PUT')
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

                <!-- Kolom Kiri -->
                <div class="space-y-6">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        Informasi Pengeluaran
                        <div class="h-px flex-1 bg-slate-100"></div>
                    </h3>

                    <!-- Deskripsi -->
                    <div class="space-y-2">
                        <label
                            class="text-sm font-black text-slate-700 tracking-tight after:content-['*'] after:text-rose-500">Deskripsi</label>
                        <input type="text" name="description"
                            value="{{ old('description', $expense->description ?? '') }}"
                            placeholder="Contoh: Bayar listrik bulan Januari" required
                            class="w-full px-5 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-4 focus:ring-rose-50 focus:border-rose-400 transition-all">
                    </div>

                    <!-- Jumlah -->
                    <div class="space-y-2" x-data="currencyInput({{ old('amount', $expense->amount ?? 0) }})">
                        <label
                            class="text-sm font-black text-slate-700 tracking-tight after:content-['*'] after:text-rose-500">Jumlah
                            (Rp)</label>
                        <!-- Input hidden untuk submit form -->
                        <input type="hidden" name="amount" :value="raw">
                        <!-- Input display untuk user -->
                        <div class="relative">
                            <span
                                class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm select-none">Rp</span>
                            <input type="text" inputmode="numeric" x-model="display" @input="onInput" @blur="onBlur"
                                placeholder="0"
                                class="w-full pl-10 pr-5 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-4 focus:ring-rose-50 focus:border-rose-400 transition-all">
                        </div>
                    </div>

                    <!-- Tanggal -->
                    <div class="space-y-2">
                        <label
                            class="text-sm font-black text-slate-700 tracking-tight after:content-['*'] after:text-rose-500">Tanggal</label>
                        <input type="date" name="date"
                            value="{{ old('date', isset($expense) ? $expense->date->format('Y-m-d') : date('Y-m-d')) }}"
                            required
                            class="w-full px-5 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-4 focus:ring-rose-50 focus:border-rose-400 transition-all">
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="space-y-6">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        Detail Tambahan
                        <div class="h-px flex-1 bg-slate-100"></div>
                    </h3>

                    <!-- Kategori -->
                    <div class="space-y-2">
                        <label class="text-sm font-black text-slate-700 tracking-tight">Kategori</label>
                        <select name="category"
                            class="w-full px-5 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-4 focus:ring-rose-50 focus:border-rose-400 transition-all">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ old('category', $expense->category ?? '') === $cat ?
                                'selected' : '' }}>
                                {{ ucfirst($cat) }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Referensi -->
                    <div class="space-y-2">
                        <label class="text-sm font-black text-slate-700 tracking-tight">No. Referensi / Kwitansi</label>
                        <input type="text" name="reference" value="{{ old('reference', $expense->reference ?? '') }}"
                            placeholder="Opsional"
                            class="w-full px-5 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-4 focus:ring-rose-50 focus:border-rose-400 transition-all">
                    </div>

                    <!-- Catatan -->
                    <div class="space-y-2">
                        <label class="text-sm font-black text-slate-700 tracking-tight">Catatan</label>
                        <textarea name="notes" rows="4" placeholder="Catatan tambahan (opsional)..."
                            class="w-full px-5 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-4 focus:ring-rose-50 focus:border-rose-400 transition-all resize-none">{{ old('notes', $expense->notes ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.expenses.index') }}"
                    class="px-8 py-3.5 bg-slate-100 text-slate-600 rounded-2xl font-bold text-sm hover:bg-slate-200 transition-all">
                    Batal
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-8 py-3.5 bg-rose-600 text-white rounded-2xl font-bold text-sm shadow-lg shadow-rose-100 hover:bg-rose-700 transition-all hover:scale-[1.02] active:scale-95">
                    <span class="iconify text-lg"
                        data-icon="{{ isset($expense) ? 'mdi:content-save' : 'mdi:plus-circle' }}"></span>
                    {{ isset($expense) ? 'Simpan Perubahan' : 'Catat Pengeluaran' }}
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function currencyInput(initialValue) {
    return {
        raw: initialValue ? Number(initialValue) : '',
        display: initialValue ? new Intl.NumberFormat('id-ID').format(Number(initialValue)) : '',

        onInput(e) {
            // Ambil hanya angka
            const digits = e.target.value.replace(/\D/g, '');
            this.raw = digits ? Number(digits) : '';
            // Format ulang tampilan
            this.display = digits ? new Intl.NumberFormat('id-ID').format(Number(digits)) : '';
            // Set cursor ke akhir
            this.$nextTick(() => {
                e.target.setSelectionRange(this.display.length, this.display.length);
            });
        },

        onBlur() {
            if (!this.raw) {
                this.display = '';
            }
        }
    }
}
</script>
@endpush
