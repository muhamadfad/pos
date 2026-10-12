<div class="bg-gray-50 rounded-md border border-gray-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
        <thead>
            <tr class="border-b border-gray-200 bg-white">
                <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-12">#
                </th>
                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama
                    Customer</th>
                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Alamat
                </th>
                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No HP
                </th>
                <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenis
                </th>
                <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    Transaksi</th>
                <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-44">
                    Aksi</th>
             </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($customers as $customer)
            <tr class="group hover:bg-white transition-colors">
                <td class="px-4 py-2.5 text-center text-xs text-gray-500 font-medium">
                    {{ $customers->firstItem() + $loop->index }}
                 </td>
                <td class="px-4 py-2.5">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-md bg-gray-50 flex items-center justify-center shrink-0">
                            <span class="iconify text-black" data-icon="mdi:account-outline"></span>
                        </div>
                        <span class="font-semibold text-black">{{ $customer->name }}</span>
                    </div>
                 </td>
                <td class="px-4 py-2.5 text-gray-500 text-xs">{{ Str::limit($customer->address, 40) ?: '-' }}</td>
                <td class="px-4 py-2.5">
                    @if($customer->phone)
                    <a href="tel:{{ $customer->phone }}"
                        class="inline-flex items-center gap-1 text-gray-600 hover:text-black transition text-xs">
                        <span class="iconify text-gray-500" data-icon="mdi:phone-outline"></span>
                        {{ $customer->phone }}
                    </a>
                    @else
                    <span class="text-gray-500 text-xs">-</span>
                    @endif
                 </td>
                <td class="px-4 py-2.5 text-center">
                    @php
                    // Perbaiki tipe data yang sesuai dengan validasi di controller
                    $typeColor = match($customer->type) {
                        'store' => 'bg-white border border-gray-200 text-black',
                        'project' => 'bg-white border border-gray-200 text-black',
                        'retail' => 'bg-white border border-gray-200 text-black',
                        default => 'bg-slate-100 text-gray-600',
                    };
                    
                    $typeLabel = match($customer->type) {
                        'store' => 'Toko',
                        'project' => 'Proyek',
                        'retail' => 'Retail',
                        default => ucfirst($customer->type),
                    };
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium {{ $typeColor }}">
                        {{ $typeLabel }}
                    </span>
                 </td>
                <td class="px-4 py-2.5 text-center">
                    {{-- PERBAIKAN ADA DI SINI --}}
                    @if(($customer->orders_count ?? 0) > 0)
                    <a href="{{ route('admin.customers.show', $customer) }}"
                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-gray-50 text-black hover:bg-gray-800 transition">
                        <span class="iconify" data-icon="mdi:receipt-text-outline"></span>
                        {{ $customer->orders_count }}
                    </a>
                    @else
                    <span
                        class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 text-gray-500">0</span>
                    @endif
                 </td>
                <td class="px-4 py-2.5">
                    <div class="flex items-center justify-center gap-1">
                        <a href="{{ route('admin.customers.show', $customer) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 bg-slate-100 hover:bg-slate-200 rounded-md transition">
                            <span class="iconify" data-icon="mdi:eye-outline"></span>
                            Detail
                        </a>
                        <a href="{{ route('admin.customers.edit', $customer) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-black bg-gray-50 hover:bg-gray-50 rounded-md transition">
                            <span class="iconify" data-icon="mdi:pencil-outline"></span>
                            Edit
                        </a>
                        <button type="button"
                            onclick="confirmDelete('{{ route('admin.customers.destroy', $customer) }}', '{{ addslashes($customer->name) }}')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-md transition">
                            <span class="iconify" data-icon="mdi:trash-can-outline"></span>
                            Hapus
                        </button>
                    </div>
                 </td>
             </tr>
            @empty
             <tr>
                <td colspan="7" class="py-16 text-center">
                    <div class="flex flex-col items-center gap-3 text-gray-500">
                        <span class="iconify text-5xl" data-icon="mdi:account-group-outline"></span>
                        <p class="font-medium">Belum ada data customer</p>
                        <a href="{{ route('admin.customers.create') }}"
                            class="mt-1 inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-black bg-gray-50 hover:bg-gray-50 rounded-md transition">
                            <span class="iconify" data-icon="mdi:plus"></span>
                            Tambah Customer
                        </a>
                    </div>
                 </td>
             </tr>
            @endforelse
        </tbody>
     </table>
    </div>

    @if($customers->hasPages())
    <div class="px-4 py-2 border-t border-gray-200 bg-white">
        {{ $customers->links() }}
    </div>
    @endif
</div>
