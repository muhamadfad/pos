<div class="overflow-x-auto">
    <table class="w-full text-sm text-left">
        <thead
            class="bg-white text-gray-600 uppercase text-[11px] font-bold tracking-widest border-b border-gray-200">
            <tr>
                <th class="px-4 py-2">Nomor Invoice / Tgl</th>
                <th class="px-4 py-2">Customer</th>
                <th class="px-4 py-2">Cabang & Kasir</th>
                <th class="px-4 py-2">Barang</th>
                <th class="px-4 py-2 text-right">Total Akhir</th>
                <th class="px-4 py-2 text-center">Status Bayar</th>
                <th class="px-4 py-2 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 border-b border-gray-200">
            @forelse($orders as $row)
            <tr class="hover:bg-white transition-colors align-top">
                <!-- Invoice Info -->
                <td class="px-4 py-2 whitespace-nowrap">
                    <div class="font-bold text-black mb-0.5">
                        {{ $row->invoice_number }}
                    </div>
                    <div class="text-[12px] text-gray-500 flex items-center gap-1.5">
                        <span class="iconify" data-icon="mdi:calendar"></span>
                        {{ \Carbon\Carbon::parse($row->created_at)->format('d M Y, H:i') }}
                    </div>
                </td>

                <!-- Customer -->
                <td class="px-4 py-2">
                    <div class="font-bold text-black mb-1">
                        {{ $row->customer->name ?? 'Walk-in' }}
                    </div>
                    @if($row->customer)
                    <div class="flex items-center gap-1.5">
                        @php
                        $typeCol = match($row->customer->type) {
                        'store' => 'bg-white border border-gray-200 text-black border-gray-200',
                        'project' => 'bg-white border border-gray-200 text-black border-gray-200',
                        'retail' => 'bg-white text-gray-600 border-gray-200',
                        default => 'bg-gray-50 text-gray-600 border-gray-100'
                        };
                        $typeLabel = match($row->customer->type) {
                        'store' => 'Toko',
                        'project' => 'Proyek',
                        'retail' => 'Eceran',
                        default => 'Umum'
                        };
                        @endphp
                        <span
                            class="px-2 py-0.5 rounded-md text-[10px] font-bold border uppercase tracking-wider {{ $typeCol }}">
                            {{ $typeLabel }}
                        </span>
                    </div>
                    @endif
                </td>

                <!-- Branch & Cashier -->
                <td class="px-4 py-2 whitespace-nowrap">
                    <div class="font-bold text-slate-700 mb-0.5 text-[13px]">
                        {{ $row->branch->name ?? 'Pusat' }}
                    </div>
                    <div class="text-[12px] text-gray-500 flex items-center gap-1.5">
                        <span class="iconify" data-icon="mdi:account-outline"></span>
                        @if($row->cashierShift && $row->cashierShift->cashier_name)
                            {{ $row->cashierShift->cashier_name }}
                        @else
                            {{ $row->user->name ?? 'Unknown' }}
                        @endif
                        
                        @if($row->cashierShift)
                            <span class="text-gray-500">|</span>
                            <span class="text-[11px] font-semibold bg-slate-100 px-1.5 py-0.5 rounded text-gray-600">
                                {{ $row->cashierShift->shift_time }}
                            </span>
                        @endif
                    </div>
                </td>

                <!-- Items -->
                <td class="px-4 py-2 max-w-xs">
                    <div class="space-y-1.5 min-w-[180px]">
                        @foreach($row->items->take(2) as $item)
                        <div class="flex items-center justify-between text-[12px]">
                            <span class="text-gray-600 truncate mr-2">{{ $item->custom_name ?: ($item->product->name ?? '-') }}</span>
                            <span class="text-gray-500 shrink-0 font-medium">x{{ $item->quantity }}</span>
                        </div>
                        @endforeach
                        @if($row->items->count() > 2)
                        <div class="text-[11px] text-black font-medium italic">
                            +{{ $row->items->count() - 2 }} item lainnya...
                        </div>
                        @endif
                    </div>
                </td>

                <!-- Money -->
                <td class="px-4 py-2 text-right whitespace-nowrap">
                    <div class="text-black font-bold mb-0.5 text-base">
                        Rp {{ number_format($row->total_amount, 0, ',', '.') }}
                    </div>
                    @if($row->remaining_receivable > 0)
                    <div class="text-[11px] text-red-500 font-medium tracking-tight">
                        Sisa Piutang: Rp {{ number_format($row->remaining_receivable, 0, ',', '.') }}
                    </div>
                    @else
                    <div
                        class="text-[11px] text-black font-medium flex items-center justify-end gap-1 uppercase tracking-tighter">
                        <span class="iconify" data-icon="mdi:check-circle"></span>
                        Lunas
                    </div>
                    @endif
                </td>

                <!-- Status -->
                <td class="px-4 py-2 text-center whitespace-nowrap">
                    @php
                    $statusMap = [
                    'paid' => ['bg' => 'bg-gray-50 border border-gray-200', 'text' => 'text-black', 'border' => 'border-gray-200',
                    'label' => 'Lunas', 'icon' => 'mdi:check-circle'],
                    'partial' => ['bg' => 'bg-gray-50', 'text' => 'text-black', 'border' => 'border-gray-200',
                    'label' => 'Sebagian', 'icon' => 'mdi:adjust'],
                    'unpaid' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-200', 'label' =>
                    'Belum Bayar', 'icon' => 'mdi:alert-circle'],
                    ];
                    $s = $statusMap[$row->payment_status] ?? $statusMap['unpaid'];
                    @endphp
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-[11px] font-bold {{ $s['bg'] }} {{ $s['text'] }} border {{ $s['border'] }} shadow-sm uppercase tracking-wider">
                        <span class="iconify text-[14px]" data-icon="{{ $s['icon'] }}"></span>
                        {{ $s['label'] }}
                    </span>
                </td>

                <!-- Actions -->
                <td class="px-4 py-2 text-center whitespace-nowrap">
                    <a href="{{ route('admin.orders.show', $row->id) }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-black bg-gray-50 hover:bg-gray-50 rounded-md transition-all font-semibold text-[12px]">
                        <span class="iconify text-lg" data-icon="mdi:file-document-outline"></span>
                        Lihat Detail
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="py-16 text-center">
                    <div class="flex flex-col items-center justify-center gap-3">
                        <div class="w-16 h-16 bg-white rounded-md flex items-center justify-center text-slate-300">
                            <span class="iconify text-4xl" data-icon="mdi:cash-off"></span>
                        </div>
                        <div>
                            <p class="text-black font-bold text-lg">Belum Ada Penjualan</p>
                            <p class="text-gray-500 text-sm">Semua transaksi penjualan barang akan muncul di sini</p>
                        </div>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($orders->hasPages())
<div class="px-4 py-2 bg-white">
    {{ $orders->links() }}
</div>
@endif
