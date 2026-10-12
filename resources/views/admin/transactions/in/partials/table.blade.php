<div class="bg-gray-50 rounded-md shadow-sm border overflow-x-auto">

    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
            <tr>
                <th class="px-4 py-2 text-left">Tanggal</th>
                <th class="px-4 py-2 text-left">Barang</th>
                <th class="px-4 py-2 text-left">Total</th>
                <th class="px-4 py-2 text-left">Dibayar</th>
                <th class="px-4 py-2 text-left">Status</th>
                <th class="px-4 py-2 text-center">Detail</th>
            </tr>
        </thead>

        <tbody class="divide-y">
            @forelse($transactions as $row)
            <tr class="hover:bg-gray-50 transition">

                <td class="px-4 py-2 text-gray-600">
                    {{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}
                </td>

                <td class="px-4 py-2">
                    <div class="flex flex-wrap gap-1">
                        @foreach($row->items as $item)
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-md text-xs">
                            {{ $item->product->name ?? '-' }} ({{ $item->quantity }})
                        </span>
                        @endforeach
                    </div>
                </td>

                <td class="px-4 py-2 font-semibold text-gray-800">
                    Rp {{ number_format($row->total_amount, 0, ',', '.') }}
                </td>

                <td class="px-4 py-2 text-black font-medium">
                    Rp {{ number_format($row->total_paid, 0, ',', '.') }}
                </td>

                <td class="px-4 py-2">
                    <span class="px-3 py-1 rounded-md text-xs font-medium
                        {{ $row->payment_status === 'LUNAS'
                            ? 'bg-gray-50 border border-gray-200 text-black'
                            : 'bg-yellow-100 text-yellow-700' }}">
                        {{ strtoupper($row->payment_status) }}
                    </span>
                </td>

                <td class="px-4 py-2 text-center">
                    <a href="{{ route('admin.transactions.in.show', $row->id) }}"
                        class="text-black hover:text-black text-sm font-medium">
                        Lihat
                    </a>
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-12 text-gray-400">
                    Belum ada transaksi.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="p-6 border-t bg-gray-50">
        {{ $transactions->links() }}
    </div>

</div>
