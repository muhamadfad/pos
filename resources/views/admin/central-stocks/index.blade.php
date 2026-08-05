@extends('layouts.admin')

@section('title', 'Stok Gudang Pusat')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Stok Gudang Pusat</h1>
        <p class="text-slate-500 text-sm mt-1">Pantau stok bahan di Gudang Pusat yang siap didistribusikan.</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-6 border-b border-slate-100 bg-slate-50/50">
        <form action="{{ route('central-stocks.index') }}" method="GET" class="flex gap-4">
            <div class="relative flex-1 max-w-md">
                <span class="iconify absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" data-icon="mdi:magnify"></span>
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Cari nama bahan baku..." 
                    class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition-all">
            </div>
            <button type="submit" class="px-6 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-xl transition-all shadow-sm">
                Cari
            </button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-medium border-b border-slate-200">Nama Bahan</th>
                    <th class="px-6 py-4 font-medium border-b border-slate-200 text-center">Stok Tersedia</th>
                    <th class="px-6 py-4 font-medium border-b border-slate-200 text-right">Harga Rata-rata</th>
                    <th class="px-6 py-4 font-medium border-b border-slate-200 text-right">Total Nilai</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-slate-100">
                @forelse($ingredients as $ingredient)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-medium text-slate-800">{{ $ingredient->name }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium {{ $ingredient->current_stock <= $ingredient->min_stock ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-600' }}">
                            {{ number_format($ingredient->current_stock, 1, ',', '.') }} 
                            <span class="text-xs opacity-70">{{ $ingredient->unit->symbol ?? '' }}</span>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        Rp {{ number_format($ingredient->unit_cost ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-right font-medium text-slate-800">
                        Rp {{ number_format($ingredient->current_stock * ($ingredient->unit_cost ?? 0), 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <span class="iconify text-6xl mb-4" data-icon="mdi:package-variant-closed"></span>
                            <p class="text-lg font-medium text-slate-600">Belum ada bahan baku di Gudang Pusat</p>
                            <p class="text-sm">Silakan lakukan Pembelian Pusat untuk menambah stok.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($ingredients->hasPages())
    <div class="p-6 border-t border-slate-100">
        {{ $ingredients->links() }}
    </div>
    @endif
</div>
@endsection
