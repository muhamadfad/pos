<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <table class="min-w-full text-sm">
        <thead>
            <tr class="border-b border-slate-100 bg-slate-50">
                <th class="px-5 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider w-14">#
                </th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama
                    Kategori</th>
                <th class="px-5 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider w-32">
                    Total Produk</th>
                <th class="px-5 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider w-44">
                    Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($categories as $category)
            <tr class="group hover:bg-slate-50 transition-colors">
                <td class="px-5 py-3.5 text-center text-xs text-slate-400 font-medium">
                    {{ $categories->firstItem() + $loop->index }}
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
                            <span class="iconify text-indigo-400" data-icon="mdi:shape-outline"></span>
                        </div>
                        <span class="font-semibold text-slate-800">{{ $category->name }}</span>
                    </div>
                </td>
                <td class="px-5 py-3.5 text-center">
                    @if($category->products_count > 0)
                    <a href="{{ route('admin.categories.show', $category->id) }}"
                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition">
                        <span class="iconify" data-icon="mdi:package-variant-closed"></span>
                        {{ $category->products_count }}
                    </a>
                    @else
                    <span
                        class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-400">
                        0
                    </span>
                    @endif
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center justify-center gap-1">
                        <a href="{{ route('admin.categories.show', $category->id) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                            <span class="iconify" data-icon="mdi:eye-outline"></span>
                            Detail
                        </a>
                        <a href="{{ route('admin.categories.edit', $category->id) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                            <span class="iconify" data-icon="mdi:pencil-outline"></span>
                            Edit
                        </a>
                        <button type="button"
                            onclick="confirmDelete('{{ route('admin.categories.destroy', $category->id) }}', '{{ addslashes($category->name) }}')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">
                            <span class="iconify" data-icon="mdi:trash-can-outline"></span>
                            Hapus
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="py-16 text-center">
                    <div class="flex flex-col items-center gap-3 text-slate-400">
                        <span class="iconify text-5xl" data-icon="mdi:shape-outline"></span>
                        <p class="font-medium">Belum ada kategori</p>
                        <a href="{{ route('admin.categories.create') }}"
                            class="mt-1 inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                            <span class="iconify" data-icon="mdi:plus"></span>
                            Tambah Kategori
                        </a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($categories->hasPages())
    <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
        {{ $categories->links() }}
    </div>
    @endif
</div>
