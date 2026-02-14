<div class="bg-white rounded-2xl shadow-sm border overflow-hidden">

    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
            <tr>
                <th class="px-6 py-4 text-left">#</th>
                <th class="px-6 py-4 text-left">Nama</th>
                <th class="px-6 py-4 text-center">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y">
            @forelse ($categories as $category)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-gray-500">
                        {{ $categories->firstItem() + $loop->index }}
                    </td>

                    <td class="px-6 py-4 font-semibold text-gray-800">
                        {{ $category->name }}
                    </td>

                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-2">

                            <a href="{{ route('admin.categories.edit', $category->id) }}"
                               class="px-3 py-1.5 rounded-lg bg-yellow-500 text-white text-xs hover:bg-yellow-600 transition">
                                Edit
                            </a>

                            <button onclick="openDeleteModal({{ $category->id }})"
                                    class="px-3 py-1.5 rounded-lg bg-red-600 text-white text-xs hover:bg-red-700 transition">
                                Hapus
                            </button>

                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center py-12 text-gray-400">
                        Belum ada kategori.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="p-6 border-t bg-gray-50">
        {{ $categories->links() }}
    </div>

</div>
