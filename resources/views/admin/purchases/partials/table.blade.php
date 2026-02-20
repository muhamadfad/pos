<div class="overflow-x-auto">
    <table class="w-full text-sm text-left">
        <thead
            class="bg-slate-50 text-slate-600 uppercase text-[11px] font-bold tracking-widest border-b border-slate-200">
            <tr>
                <th class="px-6 py-4">Informasi Transaksi</th>
                <th class="px-6 py-4">Detail Barang</th>
                <th class="px-6 py-4 text-right">Keuangan</th>
                <th class="px-6 py-4 text-center">Status</th>
                <th class="px-6 py-4 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 border-b border-slate-100">
            @forelse($purchases as $row)
            <tr class="hover:bg-slate-50/80 transition-colors align-top">
                <!-- Info -->
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="font-bold text-slate-800 mb-0.5">
                        #PUR-{{ str_pad($row->id, 5, '0', STR_PAD_LEFT) }}
                    </div>
                    <div class="text-[12px] text-slate-500 flex items-center gap-1.5 mb-2">
                        <span class="iconify" data-icon="mdi:calendar-range"></span>
                        {{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}
                    </div>
                    <div
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-[12px] font-medium border border-slate-200">
                        <span class="iconify text-slate-400" data-icon="mdi:truck-outline"></span>
                        {{ $row->supplier->name ?? 'N/A' }}
                    </div>
                </td>

                <!-- Items -->
                <td class="px-6 py-4 max-w-xs">
                    <div class="space-y-1.5 min-w-[200px]">
                        @foreach($row->items->take(2) as $item)
                        <div class="flex items-center justify-between text-[12px]">
                            <span class="text-slate-600 truncate mr-2">{{ $item->product->name ?? '-' }}</span>
                            <span class="text-slate-400 shrink-0">x{{ $item->quantity }}</span>
                        </div>
                        @endforeach
                        @if($row->items->count() > 2)
                        <div class="text-[11px] text-indigo-500 font-medium italic">
                            +{{ $row->items->count() - 2 }} item lainnya...
                        </div>
                        @endif
                    </div>
                </td>

                <!-- Money -->
                <td class="px-6 py-4 text-right whitespace-nowrap">
                    <div class="text-slate-900 font-bold mb-0.5">
                        Rp {{ number_format($row->total_amount, 0, ',', '.') }}
                    </div>
                    @if($row->remaining_debt > 0)
                    <div class="text-[11px] text-red-500 font-medium tracking-tight">
                        Sisa: Rp {{ number_format($row->remaining_debt, 0, ',', '.') }}
                    </div>
                    @else
                    <div
                        class="text-[11px] text-emerald-500 font-medium flex items-center justify-end gap-1 uppercase tracking-tighter">
                        <span class="iconify" data-icon="mdi:check-circle"></span>
                        Lunas
                    </div>
                    @endif
                </td>

                <!-- Status -->
                <td class="px-6 py-4 text-center whitespace-nowrap">
                    @php
                    $statusMap = [
                    'paid' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200',
                    'label' => 'Lunas', 'icon' => 'mdi:check-circle'],
                    'partial' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200',
                    'label' => 'Sebagian', 'icon' => 'mdi:adjust'],
                    'unpaid' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-200', 'label' =>
                    'Belum Bayar', 'icon' => 'mdi:alert-circle'],
                    ];
                    $s = $statusMap[$row->payment_status] ?? $statusMap['unpaid'];
                    @endphp
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold {{ $s['bg'] }} {{ $s['text'] }} border {{ $s['border'] }} shadow-sm uppercase tracking-wider">
                        <span class="iconify text-[14px]" data-icon="{{ $s['icon'] }}"></span>
                        {{ $s['label'] }}
                    </span>
                </td>

                <!-- Actions -->
                <td class="px-6 py-4 text-center whitespace-nowrap">
                    <a href="{{ route('admin.purchases.show', $row->id) }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition-all font-semibold text-[12px]">
                        <span class="iconify" data-icon="mdi:eye"></span>
                        Detail
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="py-16 text-center">
                    <div class="flex flex-col items-center justify-center gap-3">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300">
                            <span class="iconify text-4xl" data-icon="mdi:cart-off"></span>
                        </div>
                        <div>
                            <p class="text-slate-800 font-bold text-lg">Belum Ada Pembelian</p>
                            <p class="text-slate-500 text-sm">Semua transaksi barang masuk akan muncul di sini</p>
                        </div>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($purchases->hasPages())
<div class="px-6 py-4 bg-slate-50">
    {{ $purchases->links() }}
</div>
@endif
