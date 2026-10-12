@extends('layouts.admin')

@section('title', 'Pemisahan Bahan (Pusat)')

@section('content')
@if(session('success'))
<div class="mb-6 bg-white border border-gray-200 border border-gray-200 rounded-md p-4 flex items-start gap-3">
    <span class="iconify text-black text-xl shrink-0" data-icon="mdi:check-circle"></span>
    <p class="text-sm text-black">{{ session('success') }}</p>
</div>
@endif

@if(session('error'))
<div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4 flex items-start gap-3">
    <span class="iconify text-red-500 text-xl shrink-0" data-icon="mdi:alert-circle"></span>
    <p class="text-sm text-red-700">{{ session('error') }}</p>
</div>
@endif

<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-black">Pemisahan Bahan (Pusat)</h1>
        <p class="text-gray-500 text-sm mt-1">Riwayat pemisahan bahan baku di Gudang Pusat.</p>
    </div>
    <div class="flex items-center gap-3">
        
        <a href="{{ route('admin.central-splits.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-black hover:bg-gray-800 text-white text-sm font-semibold rounded-md shadow-sm transition-all active:scale-[0.98]">
        <span class="iconify text-lg" data-icon="mdi:call-split"></span>
        Return Baru
        </a>
    </div>
</div>

<div class="bg-gray-50 rounded-md shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider">
                    <th class="px-4 py-2 font-medium border-b border-gray-200">No. Ref & Tanggal</th>
                    <th class="px-4 py-2 font-medium border-b border-gray-200">Bahan Baku Sumber</th>
                    <th class="px-4 py-2 font-medium border-b border-gray-200">Total Olahan per Item</th>
                    <th class="px-4 py-2 font-medium border-b border-gray-200">Catatan</th>
                    <th class="px-4 py-2 font-medium border-b border-gray-200">Status</th>
                    <th class="px-4 py-2 font-medium border-b border-gray-200 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-200">
                @forelse($splits as $split)
                <tr class="hover:bg-white transition-colors">
                    <td class="px-4 py-2">
                        <div class="font-medium text-black">{{ $split->reference_number ?: '-' }}</div>
                        <div class="text-gray-500 text-xs mt-0.5">{{ \Carbon\Carbon::parse($split->transfer_date)->format('d M Y') }}</div>
                    </td>
                    <td class="px-4 py-2">
                        <div class="flex items-center gap-2">
                            <span class="iconify text-gray-500" data-icon="mdi:package-variant-closed"></span>
                            <span class="font-medium text-slate-700">{{ $split->sourceIngredient->name ?? '-' }}</span>
                        </div>
                    </td>
                                        <td class="px-4 py-2">
                        <ul class="list-disc pl-4 text-xs text-gray-600">
                            @foreach($split->items as $item)
                                <li>{{ $item->ingredient->name ?? '-' }} ({{ floatval($item->quantity) }} {{ $item->ingredient->unit->name ?? '' }})</li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="px-4 py-2">
                        <span class="text-xs text-gray-500">{{ $split->notes ?: '-' }}</span>
                    </td>
                    <td class="px-4 py-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-white border border-gray-200 text-black border border-gray-200">
                            <span class="w-1.5 h-1.5 rounded-md bg-white border border-gray-2000"></span> Selesai
                        </span>
                    </td>
                    <td class="px-4 py-2 text-center">
                        <a href="{{ route('admin.central-splits.show', $split->id) }}" class="p-2 text-gray-500 hover:text-black hover:bg-gray-50 rounded-md transition-colors inline-block" title="Detail">
                            <span class="iconify text-lg" data-icon="mdi:eye-outline"></span>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        Belum ada data pemisahan stok.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($splits->hasPages())
    <div class="p-6 border-t border-gray-200">
        {{ $splits->links() }}
    </div>
    @endif
</div>
@endsection
