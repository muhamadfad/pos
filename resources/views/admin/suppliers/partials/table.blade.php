<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-white border-b border-gray-200">
                <th class="px-4 py-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest w-12 text-center">#</th>
                <th class="px-4 py-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Supplier</th>
                <th class="px-4 py-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Kontak & Alamat</th>
                <th class="px-4 py-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-center">Kategori</th>
                <th class="px-4 py-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-center">Transaksi</th>
                <th class="px-4 py-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse ($suppliers as $supplier)
            <tr class="group hover:bg-white transition-colors">
                <td class="px-4 py-2 text-center text-xs text-gray-500 font-mono">
                    {{ $suppliers->firstItem() + $loop->index }}
                </td>
                
                <td class="px-4 py-2">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-md bg-slate-100 flex items-center justify-center shrink-0 group-hover:bg-gray-50 transition-colors border border-gray-200/50 group-hover:border-gray-200">
                            <span class="iconify text-gray-500 group-hover:text-black text-lg transition-colors" data-icon="lucide:truck"></span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-black group-hover:text-black transition-colors leading-none">
                                {{ $supplier->name }}
                            </span>
                            <span class="text-[11px] text-gray-500 mt-1 font-medium italic">ID: SUP-{{ str_pad($supplier->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>
                </td>

                <td class="px-4 py-2">
                    <div class="flex flex-col gap-1">
                        @if($supplier->phone)
                        <a href="tel:{{ $supplier->phone }}" class="flex items-center gap-1.5 text-xs font-semibold text-gray-600 hover:text-black transition-colors">
                            <span class="iconify text-gray-500" data-icon="lucide:phone"></span>
                            {{ $supplier->phone }}
                        </a>
                        @endif
                        <span class="flex items-start gap-1.5 text-[11px] text-gray-500 max-w-[200px] leading-relaxed">
                            <span class="iconify mt-0.5 shrink-0" data-icon="lucide:map-pin"></span>
                            {{ $supplier->address ?: 'Alamat belum diatur' }}
                        </span>
                    </div>
                </td>

                <td class="px-4 py-2 text-center">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold border {{ $supplier->type ? 'bg-gray-50 text-black border-gray-200' : 'bg-white text-gray-500 border-gray-200' }}">
                        {{ $supplier->type ? strtoupper($supplier->type) : 'UMUM' }}
                    </span>
                </td>

                <td class="px-4 py-2 text-center">
                    {{-- PERBAIKAN ADA DI SINI --}}
                    @if(($supplier->purchases_count ?? 0) > 0)
                    <div class="inline-flex flex-col items-center">
                        <span class="text-sm font-bold text-slate-700">{{ $supplier->purchases_count }}</span>
                        <span class="text-[9px] font-bold text-gray-500 uppercase tracking-tighter">Transaksi</span>
                    </div>
                    @else
                    <span class="text-xs font-medium text-slate-300 italic">No Data</span>
                    @endif
                </td>

                <td class="px-4 py-2 text-right">
                    <div class="flex items-center justify-end gap-1">
                        <a href="{{ route('admin.suppliers.show', $supplier) }}"
                            class="p-2 text-gray-500 hover:text-gray-600 hover:bg-gray-50 rounded-md transition-all"
                            title="Detail">
                            <span class="iconify text-lg" data-icon="lucide:eye"></span>
                        </a>
                        <a href="{{ route('admin.suppliers.edit', $supplier) }}"
                            class="p-2 text-gray-500 hover:text-black hover:bg-gray-50 rounded-md transition-all"
                            title="Edit">
                            <span class="iconify text-lg" data-icon="lucide:edit-3"></span>
                        </a>
                        <button type="button"
                            onclick="confirmDelete('{{ route('admin.suppliers.destroy', $supplier) }}', '{{ addslashes($supplier->name) }}')"
                            class="p-2 text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded-md transition-all"
                            title="Hapus">
                            <span class="iconify text-lg" data-icon="lucide:trash-2"></span>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="py-24 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-20 h-20 bg-white rounded-md flex items-center justify-center mb-4 border border-gray-200">
                            <span class="iconify text-4xl text-slate-200" data-icon="lucide:truck"></span>
                        </div>
                        <p class="text-sm font-bold text-gray-500">Belum ada mitra supplier</p>
                        <p class="text-xs text-gray-500 mt-1 mb-6">Database supplier Anda masih kosong.</p>
                        <a href="{{ route('admin.suppliers.create') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-black bg-gray-50 hover:bg-gray-50 rounded-md transition-all shadow-sm">
                            <span class="iconify text-lg" data-icon="lucide:plus-circle"></span>
                            Daftarkan Supplier
                        </a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($suppliers->hasPages())
<div class="px-6 py-5 border-t border-gray-200 bg-gray-50">
    <div class="flex items-center justify-between">
        <p class="text-[11px] text-gray-500 font-medium">
            Showing <span class="text-black font-bold">{{ $suppliers->firstItem() }}</span> to <span class="text-black font-bold">{{ $suppliers->lastItem() }}</span> of {{ $suppliers->total() }} results
        </p>
        
        <div class="flex items-center gap-1">
            @if (!$suppliers->onFirstPage())
                <button @click="fetchData('{{ $suppliers->previousPageUrl() }}')" class="p-2 border border-gray-200 rounded-md hover:bg-white transition-all active:scale-90">
                    <span class="iconify" data-icon="lucide:chevron-left"></span>
                </button>
            @endif

            <div class="flex items-center gap-1 mx-2">
                @foreach ($suppliers->getUrlRange(max(1, $suppliers->currentPage() - 1), min($suppliers->lastPage(), $suppliers->currentPage() + 1)) as $page => $url)
                    <button @click="fetchData('{{ $url }}')" 
                        class="w-8 h-8 text-xs font-bold rounded-md transition-all active:scale-90 {{ $page == $suppliers->currentPage() ? 'bg-[#001529] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">
                        {{ $page }}
                    </button>
                @endforeach
            </div>

            @if ($suppliers->hasMorePages())
                <button @click="fetchData('{{ $suppliers->nextPageUrl() }}')" class="p-2 border border-gray-200 rounded-md hover:bg-white transition-all active:scale-90">
                    <span class="iconify" data-icon="lucide:chevron-right"></span>
                </button>
            @endif
        </div>
    </div>
</div>
@endif
