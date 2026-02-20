<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <table class="min-w-full text-sm">
        <thead>
            <tr class="border-b border-slate-100 bg-slate-50">
                <th class="px-5 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider w-12">#
                </th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama
                    Customer</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Alamat
                </th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">No HP
                </th>
                <th class="px-5 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Jenis
                </th>
                <th class="px-5 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    Transaksi</th>
                <th class="px-5 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider w-44">
                    Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($customers as $customer)
            <tr class="group hover:bg-slate-50 transition-colors">
                <td class="px-5 py-3.5 text-center text-xs text-slate-400 font-medium">
                    {{ $customers->firstItem() + $loop->index }}
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
                            <span class="iconify text-indigo-400" data-icon="mdi:account-outline"></span>
                        </div>
                        <span class="font-semibold text-slate-800">{{ $customer->name }}</span>
                    </div>
                </td>
                <td class="px-5 py-3.5 text-slate-500 text-xs">{{ Str::limit($customer->address, 40) ?: '-' }}</td>
                <td class="px-5 py-3.5">
                    @if($customer->phone)
                    <a href="tel:{{ $customer->phone }}"
                        class="inline-flex items-center gap-1 text-slate-600 hover:text-indigo-600 transition text-xs">
                        <span class="iconify text-slate-400" data-icon="mdi:phone-outline"></span>
                        {{ $customer->phone }}
                    </a>
                    @else
                    <span class="text-slate-400 text-xs">-</span>
                    @endif
                </td>
                <td class="px-5 py-3.5 text-center">
                    @php
                    $typeColor = match($customer->type) {
                    'toko' => 'bg-blue-50 text-blue-700',
                    'proyek' => 'bg-emerald-50 text-emerald-700',
                    default => 'bg-slate-100 text-slate-600',
                    };
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium {{ $typeColor }}">
                        {{ ucfirst($customer->type) }}
                    </span>
                </td>
                <td class="px-5 py-3.5 text-center">
                    @if(($customer->stock_outs_count ?? 0) > 0)
                    <a href="{{ route('admin.customers.show', $customer) }}"
                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition">
                        <span class="iconify" data-icon="mdi:receipt-text-outline"></span>
                        {{ $customer->stock_outs_count }}
                    </a>
                    @else
                    <span
                        class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-400">0</span>
                    @endif
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center justify-center gap-1">
                        <a href="{{ route('admin.customers.show', $customer) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                            <span class="iconify" data-icon="mdi:eye-outline"></span>
                            Detail
                        </a>
                        <a href="{{ route('admin.customers.edit', $customer) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                            <span class="iconify" data-icon="mdi:pencil-outline"></span>
                            Edit
                        </a>
                        <button type="button"
                            onclick="confirmDelete('{{ route('admin.customers.destroy', $customer) }}', '{{ addslashes($customer->name) }}')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">
                            <span class="iconify" data-icon="mdi:trash-can-outline"></span>
                            Hapus
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-16 text-center">
                    <div class="flex flex-col items-center gap-3 text-slate-400">
                        <span class="iconify text-5xl" data-icon="mdi:account-group-outline"></span>
                        <p class="font-medium">Belum ada data customer</p>
                        <a href="{{ route('admin.customers.create') }}"
                            class="mt-1 inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                            <span class="iconify" data-icon="mdi:plus"></span>
                            Tambah Customer
                        </a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($customers->hasPages())
    <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
        {{ $customers->links() }}
    </div>
    @endif
</div>
