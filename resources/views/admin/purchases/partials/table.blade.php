@extends('layouts.admin')

@section('title', 'Daftar Pembelian')

@section('content')
<div class="max-w-[1400px] mx-auto p-6 bg-[#F9FAFB] min-h-screen font-sans antialiased text-slate-900">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
                <span class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Inventory Ops</span>
            </div>
            <h1 class="text-3xl font-black tracking-tighter text-slate-900 uppercase italic">Purchasing</h1>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative group">
                <span class="iconify absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" data-icon="lucide:search"></span>
                <input type="text" placeholder="Quick Search..." 
                    class="h-10 w-64 pl-10 pr-4 bg-white border border-slate-200 rounded-xl text-xs font-bold focus:ring-4 focus:ring-indigo-50 focus:border-indigo-500 transition-all outline-none shadow-sm">
            </div>
            <a href="{{ route('admin.purchases.select-supplier') }}"
                class="h-10 px-6 bg-slate-900 text-white text-[11px] font-black rounded-xl hover:bg-indigo-600 flex items-center gap-2 transition-all active:scale-95 shadow-lg shadow-slate-200 uppercase tracking-widest">
                <span class="iconify text-lg" data-icon="lucide:plus"></span>
                New Entry
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8 text-slate-900">
        <div class="bg-white p-6 rounded-[1.5rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Volume</p>
            <p class="text-3xl font-black tracking-tighter mt-1">{{ $stats['total'] }} <span class="text-[10px] text-slate-300 italic uppercase">Tx</span></p>
        </div>
        <div class="bg-white p-6 rounded-[1.5rem] border-b-4 border-emerald-500 shadow-sm">
            <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Settled</p>
            <p class="text-3xl font-black tracking-tighter mt-1">{{ $stats['paid'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-[1.5rem] border-b-4 border-orange-500 shadow-sm">
            <p class="text-[10px] font-black text-orange-500 uppercase tracking-widest">Liability</p>
            <p class="text-3xl font-black tracking-tighter mt-1">{{ $stats['debt'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.04)] overflow-hidden">
        
        <div class="overflow-x-auto pb-2 custom-scrollbar">
            <table class="w-full border-separate border-spacing-y-0 min-w-[900px]">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">Info / Ref</th>
                        <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">Supplier & Items</th>
                        <th class="px-8 py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">Valuation</th>
                        <th class="px-8 py-5 text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">Status</th>
                        <th class="px-8 py-5 text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">Control</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($purchases as $row)
                    <tr class="group hover:bg-slate-50/50 transition-colors duration-150">
                        <td class="px-8 py-6">
                            <span class="text-xs font-black text-slate-900 tracking-tighter block">#PUR-{{ str_pad($row->id, 5, '0', STR_PAD_LEFT) }}</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ \Carbon\Carbon::parse($row->date)->format('d.m.Y') }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <div class="text-[11px] font-black text-slate-700 uppercase tracking-tight mb-2 flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                {{ $row->supplier->name ?? 'N/A' }}
                            </div>
                            <div class="flex flex-wrap gap-x-3 gap-y-1">
                                @foreach($row->items->take(2) as $item)
                                    <span class="text-[10px] font-bold text-slate-400 italic">{{ $item->product->name ?? '-' }} <span class="text-slate-900 font-black not-italic">x{{ $item->quantity }}</span></span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="text-sm font-black text-slate-900 tracking-tighter">Rp {{ number_format($row->total_amount, 0, ',', '.') }}</div>
                            @if($row->remaining_debt > 0)
                                <div class="text-[9px] font-black text-red-500 uppercase tracking-tighter italic">Rem: {{ number_format($row->remaining_debt, 0, ',', '.') }}</div>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-center">
                            @php
                                $statusMap = [
                                    'paid' => ['bg' => 'bg-emerald-500', 'text' => 'text-white', 'label' => 'Settle'],
                                    'partial' => ['bg' => 'bg-slate-900', 'text' => 'text-white', 'label' => 'Partial'],
                                    'unpaid' => ['bg' => 'bg-white', 'text' => 'text-slate-400', 'label' => 'Void', 'border' => 'border-slate-200'],
                                ];
                                $s = $statusMap[$row->payment_status] ?? $statusMap['unpaid'];
                            @endphp
                            <span class="inline-block px-3 py-1 rounded text-[9px] font-black tracking-widest {{ $s['bg'] }} {{ $s['text'] }} {{ $s['border'] ?? '' }} border uppercase shadow-sm">
                                {{ $s['label'] }}
                            </span>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <a href="{{ route('admin.purchases.show', $row->id) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-slate-900 hover:border-slate-900 transition-all active:scale-90">
                                <span class="iconify text-lg" data-icon="lucide:arrow-right"></span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-24 text-center text-slate-300 font-black uppercase text-[10px] tracking-[0.5em]">No_Records_Found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchases->hasPages())
        <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                Log <span class="text-slate-900">{{ $purchases->firstItem() }}-{{ $purchases->lastItem() }}</span> // Total <span class="text-slate-900">{{ $purchases->total() }}</span>
            </div>

            <div class="flex items-center gap-1">
                @if ($purchases->onFirstPage())
                    <span class="p-2 text-slate-200 cursor-not-allowed"><span class="iconify text-xl" data-icon="lucide:chevron-left"></span></span>
                @else
                    <a href="{{ $purchases->previousPageUrl() }}" class="p-2 text-slate-900 hover:text-indigo-600 transition-colors"><span class="iconify text-xl" data-icon="lucide:chevron-left"></span></a>
                @endif

                <div class="flex gap-1 mx-2">
                    @foreach ($purchases->getUrlRange(1, $purchases->lastPage()) as $page => $url)
                        @if ($page == $purchases->currentPage())
                            <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-900 text-white text-[10px] font-black shadow-lg shadow-slate-200">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-[10px] font-black text-slate-400 hover:border-slate-900 hover:text-slate-900 transition-all">{{ $page }}</a>
                        @endif
                    @endforeach
                </div>

                @if ($purchases->hasMorePages())
                    <a href="{{ $purchases->nextPageUrl() }}" class="p-2 text-slate-900 hover:text-indigo-600 transition-colors"><span class="iconify text-xl" data-icon="lucide:chevron-right"></span></a>
                @else
                    <span class="p-2 text-slate-200 cursor-not-allowed"><span class="iconify text-xl" data-icon="lucide:chevron-right"></span></span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    /* Styling Scrollbar agar Tipis & Modern */
    .custom-scrollbar::-webkit-scrollbar { height: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }

    /* Fix table border collapse for rounded corners */
    table { border-collapse: separate; text-indent: 0; border-spacing: 0; }
    
    /* Font tajam */
    body { background-color: #F9FAFB !important; }
</style>
@endsection