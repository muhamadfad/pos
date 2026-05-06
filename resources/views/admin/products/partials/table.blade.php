<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-slate-50/80 border-b border-slate-200">
                <th class="px-6 py-4 text-[11px] font-semibold text-slate-500 uppercase tracking-wider w-12 text-center">#</th>
                <th class="px-6 py-4 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Produk</th>
                <th class="px-6 py-4 text-[11px] font-semibold text-slate-500 uppercase tracking-wider text-right">Harga Jual</th>
                <th class="px-6 py-4 text-[11px] font-semibold text-slate-500 uppercase tracking-wider text-center">Status Stok</th>
                <th class="px-6 py-4 text-[11px] font-semibold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($products as $product)
            <tr class="group hover:bg-slate-50/50 transition-colors {{ $product->stock == 0 ? 'bg-rose-50/20' : '' }}">
                <td class="px-6 py-4 text-center text-xs text-slate-400 font-medium">
                    {{ $products->firstItem() + $loop->index }}
                </td>
                
                <td class="px-6 py-4">
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold text-slate-900 group-hover:text-indigo-600 transition-colors">
                            {{ $product->name }}
                        </span>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-[11px] text-slate-500 font-mono">{{ $product->barcode }}</span>
                            <span class="text-[11px] text-indigo-500/80 font-medium">• {{ $product->category->name ?? 'Uncategorized' }}</span>
                        </div>
                    </div>
                </td>

                <td class="px-6 py-4 text-right">
                    <span class="text-sm font-medium text-slate-700">
                        IDR {{ number_format($product->selling_price, 0, ',', '.') }}
                    </span>
                </td>

                <td class="px-6 py-4 text-center">
                    @php
                        $statusClass = $product->stock == 0 
                            ? 'bg-rose-50 text-rose-600 border-rose-100' 
                            : ($product->stock <= 5 ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100');
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-bold border {{ $statusClass }}">
                        {{ $product->stock == 0 ? 'Out of Stock' : $product->stock . ' Unit' }}
                    </span>
                </td>

                <td class="px-6 py-4">
                    <div class="flex items-center justify-end gap-1.5">
                        <a href="{{ route('admin.products.show', $product) }}" 
                           class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-md transition-all" title="View">
                            <span class="iconify text-lg" data-icon="lucide:eye"></span>
                        </a>

                        <a href="{{ route('admin.products.edit', $product) }}" 
                           class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-md transition-all" title="Edit">
                            <span class="iconify text-lg" data-icon="lucide:edit-3"></span>
                        </a>

                        <button type="button" 
                                onclick="confirmDelete('{{ route('admin.products.destroy', $product) }}', '{{ addslashes($product->name) }}')"
                                class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition-all" title="Delete">
                            <span class="iconify text-lg" data-icon="lucide:trash-2"></span>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="py-24 text-center">
                    <div class="flex flex-col items-center justify-center text-slate-400">
                        <span class="iconify text-4xl mb-3 opacity-20" data-icon="lucide:package-open"></span>
                        <p class="text-sm font-medium">No products found</p>
                        <p class="text-xs opacity-60">Try adjusting your search or filters</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($products->hasPages())
<div class="px-6 py-5 border-t border-slate-100 bg-white">
    <div class="flex items-center justify-between">
        <p class="text-xs text-slate-500 font-medium">
            Showing <span class="text-slate-900">{{ $products->firstItem() }}</span> to <span class="text-slate-900">{{ $products->lastItem() }}</span> of {{ $products->total() }} results
        </p>
        
        <div class="flex items-center gap-1">
            @if (!$products->onFirstPage())
                <button @click="fetchPage('{{ $products->previousPageUrl() }}')" class="p-2 border border-slate-200 rounded-md hover:bg-slate-50 transition-colors">
                    <span class="iconify" data-icon="lucide:chevron-left"></span>
                </button>
            @endif

            <div class="flex items-center gap-1 mx-2">
                @foreach ($products->getUrlRange(max(1, $products->currentPage() - 1), min($products->lastPage(), $products->currentPage() + 1)) as $page => $url)
                    <button @click="fetchPage('{{ $url }}')" 
                        class="w-8 h-8 text-xs font-semibold rounded-md transition-all {{ $page == $products->currentPage() ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                        {{ $page }}
                    </button>
                @endforeach
            </div>

            @if ($products->hasMorePages())
                <button @click="fetchPage('{{ $products->nextPageUrl() }}')" class="p-2 border border-slate-200 rounded-md hover:bg-slate-50 transition-colors">
                    <span class="iconify" data-icon="lucide:chevron-right"></span>
                </button>
            @endif
        </div>
    </div>
</div>
@endif