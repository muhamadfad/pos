<div class="bg-white rounded-2xl shadow-sm border overflow-hidden">

    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
            <tr>
                <th class="px-6 py-4 text-left">#</th>
                <th class="px-6 py-4 text-left">Nama Mitra</th>
                <th class="px-6 py-4 text-left">Alamat</th>
                <th class="px-6 py-4 text-left">No HP</th>
                <th class="px-6 py-4 text-left">Jenis</th>
                <th class="px-6 py-4 text-center">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y">
            @forelse ($mitras as $mitra)
            <tr class="hover:bg-gray-50 transition">

                <td class="px-6 py-4 text-gray-500">
                    {{ $mitras->firstItem() + $loop->index }}
                </td>

                <td class="px-6 py-4 font-semibold text-gray-800">
                    {{ $mitra->nama_mitra }}
                </td>

                <td class="px-6 py-4 text-gray-600">
                    {{ $mitra->alamat }}
                </td>

                <td class="px-6 py-4 text-gray-600">
                    {{ $mitra->no_hp }}
                </td>

                <td class="px-6 py-4">
                    <span class="px-3 py-1 rounded-full text-xs font-medium
                        {{ $mitra->jenis_mitra === 'supplier'
                            ? 'bg-blue-100 text-blue-700'
                            : 'bg-purple-100 text-purple-700' }}">
                        {{ ucfirst($mitra->jenis_mitra) }}
                    </span>
                </td>

                <td class="px-6 py-4 text-center">
                    <div class="flex justify-center gap-3">

                        <a href="{{ route('admin.mitra.edit', $mitra) }}"
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Edit
                        </a>

                        <button onclick="openDeleteModal({{ $mitra->id }})"
                                class="text-red-600 hover:text-red-800 text-sm font-medium">
                            Hapus
                        </button>

                    </div>
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-12 text-gray-400">
                    Data mitra belum tersedia.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="p-6 border-t bg-gray-50">
        {{ $mitras->links() }}
    </div>

</div>
