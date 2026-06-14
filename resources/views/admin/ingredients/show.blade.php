@extends('layouts.admin')

@section('title', 'Detail Bahan Baku')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<div class="p-2 lg:p-4 max-w-[1200px] mx-auto space-y-6" style="font-family: 'Inter', sans-serif;">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <nav class="flex mb-2 text-xs font-medium text-slate-400 uppercase tracking-wider">
                <span>Inventory</span>
                <span class="mx-2">/</span>
                <span class="text-indigo-600">Bahan Baku</span>
                <span class="mx-2">/</span>
                <span>Detail</span>
            </nav>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Detail Bahan Baku</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $ingredient->name }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.ingredients.edit', $ingredient) }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-all shadow-sm">
                <span class="iconify text-lg" data-icon="lucide:edit-3"></span>
                Edit
            </a>
            <a href="{{ route('admin.ingredients.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition-all shadow-sm">
                <span class="iconify text-lg" data-icon="lucide:arrow-left"></span>
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/80">
                    <h2 class="text-sm font-semibold text-slate-800 uppercase tracking-wider">Informasi Utama</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide">Nama Bahan</p>
                            <p class="text-lg font-semibold text-slate-900 mt-1">{{ $ingredient->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide">Satuan</p>
                            <p class="text-sm text-slate-700 mt-1">{{ $ingredient->unit ? $ingredient->unit->name : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide">Cabang</p>
                            <p class="text-sm text-slate-700 mt-1">{{ $ingredient->branch ? $ingredient->branch->name : 'Pusat' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide">Barcode</p>
                            <p class="text-sm text-slate-700 mt-1">{{ $ingredient->barcode ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide">Stok Saat Ini</p>
                            <p class="text-2xl font-bold {{ $ingredient->current_stock <= $ingredient->min_stock ? 'text-rose-600' : 'text-slate-900' }} mt-1">{{ number_format($ingredient->current_stock, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide">Stok Minimal</p>
                            <p class="text-sm text-slate-700 mt-1">{{ number_format($ingredient->min_stock, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide">Harga Beli</p>
                            <p class="text-sm text-slate-700 mt-1">Rp {{ number_format($ingredient->unit_cost ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide">Status</p>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $ingredient->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }} mt-1">
                                {{ $ingredient->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/80">
                    <h2 class="text-sm font-semibold text-slate-800 uppercase tracking-wider">Detail Nilai Stok</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="rounded-3xl bg-slate-50 p-4">
                            <p class="text-xs text-slate-500 uppercase tracking-wider">Total Nilai Stok</p>
                            <p class="text-2xl font-bold text-slate-900 mt-2">Rp {{ number_format($ingredient->current_stock * ($ingredient->unit_cost ?? 0), 0, ',', '.') }}</p>
                        </div>
                        <div class="rounded-3xl bg-slate-50 p-4">
                            <p class="text-xs text-slate-500 uppercase tracking-wider">Perhitungan</p>
                            <p class="text-sm text-slate-700 mt-2">{{ number_format($ingredient->current_stock, 2) }} × Rp {{ number_format($ingredient->unit_cost ?? 0, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="rounded-3xl p-4 border border-slate-200 bg-slate-50">
                        <p class="text-sm font-semibold text-slate-900">Kondisi Stok</p>
                        <p class="text-sm text-slate-700 mt-2">
                            @if($ingredient->current_stock <= 0)
                                Stok habis. Segera restock agar tidak mengganggu produksi.
                            @elseif($ingredient->current_stock <= $ingredient->min_stock)
                                Stok menipis. Periksa kembali kebutuhan cabang.
                            @else
                                Stok dalam kondisi aman.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden sticky top-6">
                <div class="px-6 py-5 border-b border-slate-200 bg-gradient-to-r from-indigo-500 to-violet-600">
                    <h2 class="text-sm font-semibold text-white uppercase tracking-wider">Ringkasan Cepat</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs text-slate-400 uppercase tracking-wide">Stok Saat Ini</p>
                            <p class="text-xl font-bold text-slate-900 mt-1">{{ number_format($ingredient->current_stock, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 uppercase tracking-wide">Stok Minimal</p>
                            <p class="text-xl font-bold text-slate-900 mt-1">{{ number_format($ingredient->min_stock, 2) }}</p>
                        </div>
                    </div>

                    <div class="rounded-3xl bg-slate-50 p-4 border border-slate-200">
                        <p class="text-xs text-slate-500 uppercase tracking-wider">Nilai Stok</p>
                        <p class="text-2xl font-bold text-emerald-600 mt-2">Rp {{ number_format($ingredient->current_stock * ($ingredient->unit_cost ?? 0), 0, ',', '.') }}</p>
                    </div>

                    <div class="rounded-3xl bg-white p-4 border border-slate-200">
                        <p class="text-xs text-slate-500 uppercase tracking-wider">Saran Tindakan</p>
                        <div class="mt-3 text-sm text-slate-700">
                            @if($ingredient->current_stock <= 0)
                                <p class="font-semibold text-rose-600">Segera pesan ulang bahan baku ini.</p>
                            @elseif($ingredient->current_stock <= $ingredient->min_stock)
                                <p class="font-semibold text-amber-600">Stok menipis, pertimbangkan reorder.</p>
                            @else
                                <p class="font-semibold text-emerald-600">Stok cukup untuk kebutuhan saat ini.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/80">
                    <h2 class="text-sm font-semibold text-slate-800 uppercase tracking-wider">Riwayat Pergerakan</h2>
                </div>
                <div class="p-6">
                    @if($movements->isEmpty())
                        <div class="rounded-3xl bg-slate-50 border border-dashed border-slate-200 p-6 text-slate-600 text-sm">
                            Belum ada data pergerakan stok untuk bahan baku ini.
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($movements as $movement)
                                <div class="rounded-3xl bg-slate-50 p-4 border border-slate-200">
                                    <div class="flex justify-between items-start gap-2">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">{{ $movement->type }}</p>
                                            <p class="text-xs text-slate-500 mt-1">{{ $movement->created_at->format('d M Y H:i') }}</p>
                                        </div>
                                        <p class="text-sm font-semibold {{ $movement->quantity > 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $movement->quantity > 0 ? '+' : '' }}{{ number_format($movement->quantity, 2) }}</p>
                                    </div>
                                    <p class="text-sm text-slate-600 mt-2">{{ $movement->notes ?? 'Tidak ada catatan tambahan' }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection