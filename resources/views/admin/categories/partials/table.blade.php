<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-slate-50/80 border-b border-slate-200">
                <th class="px-6 py-4 text-[11px] font-semibold text-slate-500 uppercase tracking-wider w-12 text-center">#</th>
                <th class="px-6 py-4 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Nama Kategori</th>
                <th class="px-6 py-4 text-[11px] font-semibold text-slate-500 uppercase tracking-wider text-center">Total Produk</th>
                <th class="px-6 py-4 text-[11px] font-semibold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($categories as $category)
            <tr class="group hover:bg-slate-50/50 transition-colors">
                <td class="px-6 py-4 text-center text-xs text-slate-400 font-medium">
                    {{ $categories->firstItem() + $loop->index }}
                </td>
                
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center shrink-0 group-hover:bg-indigo-50 transition-colors">
                            <span class="iconify text-slate-400 group-hover:text-indigo-500 text-lg transition-colors" data-icon="lucide:layers"></span>
                        </div>
                        <span class="text-sm font-semibold text-slate-900 group-hover:text-indigo-600 transition-colors">
                            {{ $category->name }}
                        </span>
                    </div>
                </td>

                <td class="px-6 py-4 text-center">
                    @if($category->products_count > 0)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-bold border bg-indigo-50 text-indigo-600 border-indigo-100">
                            {{ $category->products_count }} Produk
                        </span>
                    @else
                        <span class="text-[11px] font-medium text-slate-400 italic">Kosong</span>
                    @endif
                </td>

                <td class="px-6 py-4">
                    <div class="flex items-center justify-end gap-1.5">
                        <a href="{{ route('admin.categories.show', $category->id) }}" 
                           class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-md transition-all" title="View">
                            <span class="iconify text-lg" data-icon="lucide:eye"></span>
                        </a>

                        <a href="{{ route('admin.categories.edit', $category->id) }}" 
                           class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-md transition-all" title="Edit">
                            <span class="iconify text-lg" data-icon="lucide:edit-3"></span>
                        </a>

                        <button type="button" 
                                onclick="confirmDelete('{{ route('admin.categories.destroy', $category->id) }}', '{{ addslashes($category->name) }}')"
                                class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition-all" title="Delete">
                            <span class="iconify text-lg" data-icon="lucide:trash-2"></span>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="py-24 text-center">
                    <div class="flex flex-col items-center justify-center text-slate-400">
                        <span class="iconify text-4xl mb-3 opacity-20" data-icon="lucide:folder-search"></span>
                        <p class="text-sm font-medium">No categories found</p>
                        <p class="text-xs opacity-60">Try adjusting your search</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($categories->hasPages())
<div class="px-6 py-5 border-t border-slate-100 bg-white">
    <div class="flex items-center justify-between">
        <p class="text-xs text-slate-500 font-medium">
            Showing <span class="text-slate-900">{{ $categories->firstItem() }}</span> to <span class="text-slate-900">{{ $categories->lastItem() }}</span> of {{ $categories->total() }} results
        </p>
        
        <div class="flex items-center gap-1">
            @if (!$categories->onFirstPage())
                <button @click="fetchData('{{ $categories->previousPageUrl() }}')" class="p-2 border border-slate-200 rounded-md hover:bg-slate-50 transition-colors">
                    <span class="iconify" data-icon="lucide:chevron-left"></span>
                </button>
            @endif

            <div class="flex items-center gap-1 mx-2">
                @foreach ($categories->getUrlRange(max(1, $categories->currentPage() - 1), min($categories->lastPage(), $categories->currentPage() + 1)) as $page => $url)
                    <button @click="fetchData('{{ $url }}')" 
                        class="w-8 h-8 text-xs font-semibold rounded-md transition-all {{ $page == $categories->currentPage() ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                        {{ $page }}
                    </button>
                @endforeach
            </div>

            @if ($categories->hasMorePages())
                <button @click="fetchData('{{ $categories->nextPageUrl() }}')" class="p-2 border border-slate-200 rounded-md hover:bg-slate-50 transition-colors">
                    <span class="iconify" data-icon="lucide:chevron-right"></span>
                </button>
            @endif
        </div>
    </div>
</div>
@endif