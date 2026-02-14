<div class="bg-white rounded-2xl shadow-sm border overflow-hidden">

    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
            <tr>
                <th class="px-6 py-4 text-left">#</th>
                <th class="px-6 py-4 text-left">Nama</th>
                <th class="px-6 py-4 text-left">Email</th>
                <th class="px-6 py-4 text-left">Role</th>
                <th class="px-6 py-4 text-center">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y">
            @forelse ($users as $user)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 text-gray-500">
                    {{ $users->firstItem() + $loop->index }}
                </td>

                <td class="px-6 py-4 font-semibold text-gray-800">
                    {{ $user->name }}
                </td>

                <td class="px-6 py-4 text-gray-600">
                    {{ $user->email }}
                </td>

                <td class="px-6 py-4">
                    <span class="px-3 py-1 rounded-full text-xs font-medium
                        {{ $user->role === 'admin'
                            ? 'bg-blue-100 text-blue-700'
                            : 'bg-gray-100 text-gray-700' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>

                <td class="px-6 py-4 text-center">
                    <div class="flex justify-center gap-3">

                        <a href="{{ route('admin.users.edit', $user) }}"
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Edit
                        </a>

                        <button onclick="openDeleteModal({{ $user->id }})"
                                class="text-red-600 hover:text-red-800 text-sm font-medium">
                            Hapus
                        </button>

                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-12 text-gray-400">
                    Data user belum tersedia.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="p-6 border-t bg-gray-50">
        {{ $users->links() }}
    </div>

</div>
