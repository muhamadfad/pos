<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-white border-b border-gray-200">
                <th class="px-4 py-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-center w-12">#</th>
                <th class="px-4 py-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Tanggal</th>
                <th class="px-4 py-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-right">Tagihan</th>
                <th class="px-4 py-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-center">Status</th>
                <th class="px-4 py-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-right">Sisa Piutang</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($orders as $order)
            <tr class="hover:bg-white transition-colors">
                <td class="px-4 py-2 text-center text-xs text-gray-500 font-mono">{{ $orders->firstItem() + $loop->index }}</td>
                <td class="px-4 py-2">
                    <span class="text-sm font-bold text-slate-700">{{ \Carbon\Carbon::parse($order->date)->format('d M Y') }}</span>
                    <span class="block text-[10px] text-gray-500 font-mono uppercase">#REF-{{ $order->id }}</span>
                </td>
                <td class="px-4 py-2 text-right">
                    <span class="text-sm font-semibold text-black tracking-tight">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </td>
                <td class="px-4 py-2 text-center">
                    @php
                        $statusClass = match($order->payment_status) {
                            'paid' => 'bg-white border border-gray-200 text-black border-gray-200',
                            'partial' => 'bg-gray-50 text-black border-gray-200',
                            default => 'bg-rose-50 text-rose-600 border-rose-100',
                        };
                    @endphp
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border uppercase {{ $statusClass }}">
                        {{ $order->payment_status }}
                    </span>
                </td>
                <td class="px-4 py-2 text-right font-mono text-xs {{ $order->remaining_receivable > 0 ? 'text-rose-600 font-bold' : 'text-slate-300' }}">
                    Rp {{ number_format($order->remaining_receivable, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-20 text-center text-gray-500 italic text-sm">Belum ada data transaksi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($orders->hasPages())
<div class="px-6 py-5 border-t border-gray-200 bg-white flex items-center justify-between">
    <p class="text-[11px] text-gray-500 font-medium">
        Halaman {{ $orders->currentPage() }} dari {{ $orders->lastPage() }}
    </p>
    <div class="flex items-center gap-1">
        {{-- Previous Button --}}
        @if(!$orders->onFirstPage())
            <button @click.prevent="fetchData('{{ $orders->previousPageUrl() }}')" 
                class="p-2 border border-gray-200 rounded-md bg-gray-50 hover:bg-white transition-all active:scale-90">
                <span class="iconify" data-icon="lucide:chevron-left"></span>
            </button>
        @endif

        {{-- Nomor Halaman --}}
        @foreach ($orders->getUrlRange(max(1, $orders->currentPage() - 1), min($orders->lastPage(), $orders->currentPage() + 1)) as $page => $url)
            <button @click.prevent="fetchData('{{ $url }}')" 
                class="w-9 h-9 text-xs font-bold rounded-md transition-all {{ $page == $orders->currentPage() ? 'bg-black text-white shadow-sm' : 'bg-gray-50 border border-gray-200 text-gray-600' }}">
                {{ $page }}
            </button>
        @endforeach

        {{-- Next Button --}}
        @if($orders->hasMorePages())
            <button @click.prevent="fetchData('{{ $orders->nextPageUrl() }}')" 
                class="p-2 border border-gray-200 rounded-md bg-gray-50 hover:bg-white transition-all active:scale-90">
                <span class="iconify" data-icon="lucide:chevron-right"></span>
            </button>
        @endif
    </div>
</div>
@endif
