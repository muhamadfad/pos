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

    <div class="pagination flex items-center gap-1">
        @if ($purchases->onFirstPage())
            <span class="p-2 text-slate-200 cursor-not-allowed"><span class="iconify text-xl" data-icon="lucide:chevron-left"></span></span>
        @else
            <a href="{{ $purchases->previousPageUrl() }}" class="p-2 text-slate-900 hover:text-indigo-600 transition-colors"><span class="iconify text-xl" data-icon="lucide:chevron-left"></span></a>
        @endif

        <div class="flex gap-1 mx-2">
            @foreach ($purchases->getUrlRange(max(1, $purchases->currentPage() - 2), min($purchases->lastPage(), $purchases->currentPage() + 2)) as $page => $url)
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
