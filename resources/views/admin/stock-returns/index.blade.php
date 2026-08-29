@extends('layouts.admin')

@section('title', 'Return Cabang')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Return Cabang</h1>
        <p class="text-slate-500 text-sm mt-1">Kelola pengiriman stok dari cabang ke Gudang Pusat.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.stock-returns.download-pdf', request()->all()) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-600 text-sm font-semibold rounded-xl transition-all">
            <span class="iconify text-lg" data-icon="mdi:file-pdf-box"></span>
            Download PDF
        </a>
        <a href="{{ route('admin.stock-returns.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all active:scale-[0.98]">
        <span class="iconify text-lg" data-icon="mdi:truck-delivery-outline"></span>
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
                    <th class="px-6 py-4 font-medium border-b border-slate-200">Asal Cabang</th>
                    <th class="px-6 py-4 font-medium border-b border-slate-200">Total Olahan per Item</th>
                    <th class="px-6 py-4 font-medium border-b border-slate-200">Catatan</th>
                    <th class="px-6 py-4 font-medium border-b border-slate-200">Status</th>
                    <th class="px-6 py-4 font-medium border-b border-slate-200 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-slate-100">
                @forelse($returns as $return)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-medium text-slate-800">{{ $return->reference_number ?: '-' }}</div>
                        <div class="text-slate-500 text-xs mt-0.5">{{ \Carbon\Carbon::parse($return->transfer_date)->format('d M Y') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="iconify text-slate-400" data-icon="mdi:store-outline"></span>
                            <span class="font-medium text-slate-700">{{ $return->fromBranch->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-600 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Selesai
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('admin.stock-returns.show', $return->id) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors inline-block" title="Detail">
                            <span class="iconify text-lg" data-icon="mdi:eye-outline"></span>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                        Belum ada riwayat Return Dari Cabang.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($returns->hasPages())
    <div class="p-6 border-t border-slate-100">
        {{ $returns->links() }}
    </div>
    @endif
</div>
@endsection
