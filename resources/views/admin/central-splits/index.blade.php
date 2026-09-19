@extends('layouts.admin')

@section('title', 'Pemisahan Bahan (Pusat)')

@section('content')
@if(session('success'))
<div class="mb-6 bg-green-50 border border-green-200 rounded-2xl p-4 flex items-start gap-3">
    <span class="iconify text-green-500 text-xl shrink-0" data-icon="mdi:check-circle"></span>
    <p class="text-sm text-green-700">{{ session('success') }}</p>
</div>
@endif

@if(session('error'))
<div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4 flex items-start gap-3">
    <span class="iconify text-red-500 text-xl shrink-0" data-icon="mdi:alert-circle"></span>
    <p class="text-sm text-red-700">{{ session('error') }}</p>
</div>
@endif

<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Pemisahan Bahan (Pusat)</h1>
        <p class="text-slate-500 text-sm mt-1">Riwayat pemisahan bahan baku di Gudang Pusat.</p>
    </div>
    <div class="flex items-center gap-3">
        
        <a href="{{ route('admin.central-splits.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all active:scale-[0.98]">
        <span class="iconify text-lg" data-icon="mdi:call-split"></span>
        Return Baru
        </a>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-medium border-b border-slate-200">No. Ref & Tanggal</th>
                    <th class="px-6 py-4 font-medium border-b border-slate-200">Bahan Baku Sumber</th>
                    <th class="px-6 py-4 font-medium border-b border-slate-200">Total Olahan per Item</th>
                    <th class="px-6 py-4 font-medium border-b border-slate-200">Catatan</th>
                    <th class="px-6 py-4 font-medium border-b border-slate-200">Status</th>
                    <th class="px-6 py-4 font-medium border-b border-slate-200 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-slate-100">
                @forelse($splits as $split)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-medium text-slate-800">{{ $split->reference_number ?: '-' }}</div>
                        <div class="text-slate-500 text-xs mt-0.5">{{ \Carbon\Carbon::parse($split->transfer_date)->format('d M Y') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="iconify text-slate-400" data-icon="mdi:package-variant-closed"></span>
                            <span class="font-medium text-slate-700">{{ $split->sourceIngredient->name ?? '-' }}</span>
                        </div>
                    </td>
                                        <td class="px-6 py-4">
                        <ul class="list-disc pl-4 text-xs text-slate-600">
                            @foreach($split->items as $item)
                                <li>{{ $item->ingredient->name ?? '-' }} ({{ floatval($item->quantity) }} {{ $item->ingredient->unit->name ?? '' }})</li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs text-slate-500">{{ $split->notes ?: '-' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-600 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Selesai
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('admin.central-splits.show', $split->id) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors inline-block" title="Detail">
                            <span class="iconify text-lg" data-icon="mdi:eye-outline"></span>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                        Belum ada data pemisahan stok.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($splits->hasPages())
    <div class="p-6 border-t border-slate-100">
        {{ $splits->links() }}
    </div>
    @endif
</div>
@endsection
