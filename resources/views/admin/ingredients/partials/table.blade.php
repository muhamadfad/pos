<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-white border-b border-gray-200">
                <th class="px-4 py-2 text-[11px] font-semibold text-gray-500 uppercase tracking-wider w-12 text-center">#</th>
                <th class="px-4 py-2 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Bahan Baku</th>
                <th class="px-4 py-2 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Satuan</th>
                <th class="px-4 py-2 text-[11px] font-semibold text-gray-500 uppercase tracking-wider text-right">Stok Saat Ini</th>
                <th class="px-4 py-2 text-[11px] font-semibold text-gray-500 uppercase tracking-wider text-right">Stok Minimal</th>
                <th class="px-4 py-2 text-[11px] font-semibold text-gray-500 uppercase tracking-wider text-right">Harga Beli</th>
                <th class="px-4 py-2 text-[11px] font-semibold text-gray-500 uppercase tracking-wider text-center">Status Stok</th>
                <th class="px-4 py-2 text-[11px] font-semibold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse ($ingredients as $ingredient)
            <tr class="group hover:bg-white transition-colors {{ $ingredient->current_stock == 0 ? 'bg-rose-50/20' : ($ingredient->current_stock <= $ingredient->min_stock ? 'bg-gray-50/20' : '') }}">
                <td class="px-4 py-2 text-center text-xs text-gray-500 font-medium">
                    {{ $ingredients->firstItem() + $loop->index }}
                </td>
                
                <td class="px-4 py-2">
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold text-black group-hover:text-black transition-colors">
                            {{ $ingredient->name }}
                        </span>
                        @if($ingredient->barcode)
                            <span class="text-[11px] text-gray-500 font-mono mt-0.5">{{ $ingredient->barcode }}</span>
                        @endif
                    </div>
                </td>

                <td class="px-4 py-2">
                    <span class="text-sm text-gray-600">{{ $ingredient->unit ? $ingredient->unit->name : '-' }}</span>
                </td>

                <td class="px-4 py-2 text-right">
                    <span class="text-sm font-medium {{ $ingredient->current_stock <= $ingredient->min_stock ? 'text-black' : 'text-slate-700' }}">
                        {{ number_format($ingredient->current_stock, 2) }}
                    </span>
                </td>

                <td class="px-4 py-2 text-right">
                    <span class="text-sm text-gray-500">{{ number_format($ingredient->min_stock, 2) }}</span>
                </td>

                <td class="px-4 py-2 text-right">
                    <span class="text-sm text-slate-700">Rp {{ number_format($ingredient->unit_cost ?? 0, 0, ',', '.') }}</span>
                </td>

                <td class="px-4 py-2 text-center">
                    @php
                        $statusClass = $ingredient->current_stock == 0 
                            ? 'bg-rose-50 text-rose-600 border-rose-100' 
                            : ($ingredient->current_stock <= $ingredient->min_stock ? 'bg-gray-50 text-black border-gray-200' : 'bg-white border border-gray-200 text-black border-gray-200');
                        $statusText = $ingredient->current_stock == 0 
                            ? 'Out of Stock' 
                            : ($ingredient->current_stock <= $ingredient->min_stock ? 'Low Stock' : number_format($ingredient->current_stock, 2) . ' Unit');
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-bold border {{ $statusClass }}">
                        {{ $statusText }}
                    </span>
                </td>

                <td class="px-4 py-2">
                    <div class="flex items-center justify-end gap-1.5">
                        <a href="{{ route('admin.ingredients.show', $ingredient->id) }}" 
                           class="p-2 text-gray-500 hover:text-gray-600 hover:bg-gray-50 rounded-md transition-all" title="View">
                            <span class="iconify text-lg" data-icon="lucide:eye"></span>
                        </a>

                                                <button type="button" 
                                @click="$dispatch('open-stock-modal', { id: {{ $ingredient->id }}, name: '{{ addslashes($ingredient->name) }}', current: {{ $ingredient->current_stock }}, unit: '{{ $ingredient->unit->name ?? 'pcs' }}' })"
                                class="p-2 text-gray-500 hover:text-black hover:bg-gray-50 rounded-md transition-all" title="Ubah Stok Cepat (Opname)">
                            <span class="iconify text-lg" data-icon="lucide:calculator"></span>
                        </button>

                        <a href="{{ route('admin.ingredients.edit', $ingredient->id) }}" 
                           class="p-2 text-gray-500 hover:text-black hover:bg-gray-50 rounded-md transition-all" title="Edit">
                            <span class="iconify text-lg" data-icon="lucide:edit-3"></span>
                        </a>

                        <button type="button" 
                                onclick="confirmDelete('{{ route('admin.ingredients.destroy', $ingredient->id) }}', '{{ addslashes($ingredient->name) }}')"
                                class="p-2 text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded-md transition-all" title="Delete">
                            <span class="iconify text-lg" data-icon="lucide:trash-2"></span>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="py-24 text-center">
                    <div class="flex flex-col items-center justify-center text-gray-500">
                        <span class="iconify text-4xl mb-3 opacity-20" data-icon="lucide:package-open"></span>
                        <p class="text-sm font-medium">No ingredients found</p>
                        <p class="text-xs opacity-60">Try adjusting your search or filters</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($ingredients->hasPages())
<div class="px-6 py-5 border-t border-gray-200 bg-gray-50">
    <div class="flex items-center justify-between">
        <p class="text-xs text-gray-500 font-medium">
            Showing <span class="text-black">{{ $ingredients->firstItem() }}</span> to <span class="text-black">{{ $ingredients->lastItem() }}</span> of {{ $ingredients->total() }} results
        </p>
        
        <div class="flex items-center gap-1">
            @if (!$ingredients->onFirstPage())
                <button @click="fetchPage('{{ $ingredients->previousPageUrl() }}')" class="p-2 border border-gray-200 rounded-md hover:bg-white transition-colors">
                    <span class="iconify" data-icon="lucide:chevron-left"></span>
                </button>
            @endif

            <div class="flex items-center gap-1 mx-2">
                @foreach ($ingredients->getUrlRange(max(1, $ingredients->currentPage() - 1), min($ingredients->lastPage(), $ingredients->currentPage() + 1)) as $page => $url)
                    <button @click="fetchPage('{{ $url }}')" 
                        class="w-8 h-8 text-xs font-semibold rounded-md transition-all {{ $page == $ingredients->currentPage() ? 'bg-[#001529] text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                        {{ $page }}
                    </button>
                @endforeach
            </div>

            @if ($ingredients->hasMorePages())
                <button @click="fetchPage('{{ $ingredients->nextPageUrl() }}')" class="p-2 border border-gray-200 rounded-md hover:bg-white transition-colors">
                    <span class="iconify" data-icon="lucide:chevron-right"></span>
                </button>
            @endif
        </div>
    </div>
</div>
@endif
