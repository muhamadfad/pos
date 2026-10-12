<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-white border-b border-gray-200">
                <th class="px-4 py-2 text-[11px] font-semibold text-gray-500 uppercase tracking-wider w-12 text-center">#</th>
                <th class="px-4 py-2 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Menu</th>
                <th class="px-4 py-2 text-[11px] font-semibold text-gray-500 uppercase tracking-wider text-right">Harga Jual</th>
                {{-- HAPUS KOLOM STATUS STOK --}}
                <th class="px-4 py-2 text-[11px] font-semibold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
             </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse ($products as $product)
            <tr class="group hover:bg-white transition-colors">
                <td class="px-4 py-2 text-center text-xs text-gray-500 font-medium">
                    {{ $products->firstItem() + $loop->index }}
                 </td>
                
                <td class="px-4 py-2">
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold text-black group-hover:text-black transition-colors">
                            {{ $product->name }}
                        </span>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-[11px] text-gray-500 font-mono">{{ $product->barcode ?? '-' }}</span>
                            <span class="text-[11px] text-black/80 font-medium">• {{ $product->category->name ?? 'Uncategorized' }}</span>
                        </div>
                    </div>
                 </td>

                <td class="px-4 py-2 text-right">
                    <span class="text-sm font-medium text-slate-700">
                        IDR {{ number_format($product->selling_price, 0, ',', '.') }}
                    </span>
                 </td>

                {{-- HAPUS CELL STATUS STOK --}}

                <td class="px-4 py-2">
                    <div class="flex items-center justify-end gap-1.5">
                        <a href="{{ route('admin.products.show', $product) }}" 
                           class="p-2 text-gray-500 hover:text-gray-600 hover:bg-gray-50 rounded-md transition-all" title="View">
                            <span class="iconify text-lg" data-icon="lucide:eye"></span>
                        </a>

                        <a href="{{ route('admin.products.edit', $product) }}" 
                           class="p-2 text-gray-500 hover:text-black hover:bg-gray-50 rounded-md transition-all" title="Edit">
                            <span class="iconify text-lg" data-icon="lucide:edit-3"></span>
                        </a>

                        <button type="button" 
                                onclick="confirmDelete('{{ route('admin.products.destroy', $product) }}', '{{ addslashes($product->name) }}')"
                                class="p-2 text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded-md transition-all" title="Delete">
                            <span class="iconify text-lg" data-icon="lucide:trash-2"></span>
                        </button>
                    </div>
                 </td>
             </tr>
            @empty
             <tr>
                <td colspan="4" class="py-24 text-center">
                    <div class="flex flex-col items-center justify-center text-gray-500">
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
<div class="px-6 py-5 border-t border-gray-200 bg-gray-50">
    <div class="flex items-center justify-between">
        <p class="text-xs text-gray-500 font-medium">
            Showing <span class="text-black">{{ $products->firstItem() }}</span> to <span class="text-black">{{ $products->lastItem() }}</span> of {{ $products->total() }} results
        </p>
        
        <div class="flex items-center gap-1">
            @if (!$products->onFirstPage())
                <button @click="fetchPage('{{ $products->previousPageUrl() }}')" class="p-2 border border-gray-200 rounded-md hover:bg-white transition-colors">
                    <span class="iconify" data-icon="lucide:chevron-left"></span>
                </button>
            @endif

            <div class="flex items-center gap-1 mx-2">
                @foreach ($products->getUrlRange(max(1, $products->currentPage() - 1), min($products->lastPage(), $products->currentPage() + 1)) as $page => $url)
                    <button @click="fetchPage('{{ $url }}')" 
                        class="w-8 h-8 text-xs font-semibold rounded-md transition-all {{ $page == $products->currentPage() ? 'bg-[#001529] text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                        {{ $page }}
                    </button>
                @endforeach
            </div>

            @if ($products->hasMorePages())
                <button @click="fetchPage('{{ $products->nextPageUrl() }}')" class="p-2 border border-gray-200 rounded-md hover:bg-white transition-colors">
                    <span class="iconify" data-icon="lucide:chevron-right"></span>
                </button>
            @endif
        </div>
    </div>
</div>
@endif
