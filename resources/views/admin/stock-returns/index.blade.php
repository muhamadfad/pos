@extends('layouts.admin')

@section('title', 'Return Cabang')

@section('content')
<div x-data="{ showSummaryModal: false }">
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Return Cabang</h1>
        <p class="text-slate-500 text-sm mt-1">Kelola pengiriman stok dari cabang ke Gudang Pusat.</p>
    </div>
    <div class="flex items-center gap-3">
        <button type="button" @click="showSummaryModal = true" class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-600 text-sm font-semibold rounded-xl transition-all">
            <span class="iconify text-lg" data-icon="mdi:notebook-edit-outline"></span>
            Catat Laporan Harian
        </button>
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
                        @if($return->sourceIngredient)
                            <div class="font-bold text-slate-700">{{ $return->sourceIngredient->name }} <span class="text-xs font-normal text-slate-500">({{ floatval($return->source_quantity) }} {{ $return->sourceIngredient->unit->name ?? '' }})</span></div>
                            <div class="text-[10px] text-slate-400 mt-1 uppercase tracking-wider">Hasil Pemisahan:</div>
                        @endif
                        <ul class="list-disc pl-4 text-xs text-slate-600 mt-0.5">
                            @foreach($return->items as $item)
                                <li>{{ $item->ingredient->name ?? '-' }} ({{ floatval($item->quantity) }} {{ $item->ingredient->unit->name ?? '' }})</li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs text-slate-500">{{ $return->notes ?: '-' }}</span>
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
                    <td colspan="6" class="px-6 py-12 text-center text-slate-500">
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
    <!-- Modal Rekap Pemisahan Gabungan -->
    <div x-show="showSummaryModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0" style="display: none;">
        <div x-show="showSummaryModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="showSummaryModal = false"></div>
        <div x-show="showSummaryModal" 
             x-transition.enter="transition ease-out duration-300"
             x-transition.enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition.enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition.leave="transition ease-in duration-200"
             x-transition.leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition.leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100">
            
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                        <span class="iconify text-xl" data-icon="mdi:notebook-edit"></span>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg">Catat Laporan Return Harian</h3>
                        <p class="text-xs text-slate-500 font-medium">Khusus untuk cetak di Laporan Logistik CEO</p>
                    </div>
                </div>
                <button @click="showSummaryModal = false" class="text-slate-400 hover:text-rose-500 transition-colors p-2 bg-white rounded-xl shadow-sm border border-slate-100">
                    <span class="iconify text-xl" data-icon="mdi:close"></span>
                </button>
            </div>

            <form action="{{ route('admin.stock-returns.store-daily-summary') }}" method="POST">
                @csrf
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Tanggal Return</label>
                        <input type="date" name="summary_date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all font-semibold text-slate-700">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Rincian / Catatan Gabungan</label>
                        <p class="text-[11px] text-slate-500 mb-3 ml-1">Catatan ini TIDAK mengubah stok, murni hanya untuk direkap di PDF Laporan Logistik Lengkap (CEO).</p>
                        <textarea name="summary_notes" rows="5" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all placeholder:text-slate-300" placeholder="Contoh: Total pemisahan return dari semua cabang hari ini:&#10;- Hati: 2 Kg&#10;- Paru: 1 Kg&#10;- Daging: 5 Kg"></textarea>
                    </div>
                </div>

                <div class="px-6 py-5 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                    <button type="button" @click="showSummaryModal = false" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold hover:bg-slate-50 transition-all text-sm shadow-sm">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-amber-600 text-white rounded-xl font-bold hover:bg-amber-700 active:scale-95 transition-all text-sm shadow-md shadow-amber-100 flex items-center gap-2">
                        <span class="iconify text-lg" data-icon="mdi:check"></span>
                        Simpan Catatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
@endsection