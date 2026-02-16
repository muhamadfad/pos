<div class="bg-white rounded-2xl shadow-sm border overflow-x-auto">

    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
            <tr>
                <th class="px-6 py-4 text-left">Nama</th>
                <th class="px-6 py-4 text-left">Alamat</th>
                <th class="px-6 py-4 text-left">No. HP</th>
                <th class="px-6 py-4 text-left">Jenis</th>
                <th class="px-6 py-4 text-center">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y">
            @forelse($customers as $customer)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 font-medium text-gray-800">
                    {{ $customer->name }}
                </td>
                <td class="px-6 py-4 text-gray-600">
                    {{ Str::limit($customer->address, 40) ?? '-' }}
                </td>
                <td class="px-6 py-4 text-gray-600">
                    {{ $customer->phone ?? '-' }}
                </td>
                <td class="px-6 py-4">
                    <span
                        class="px-3 py-1 rounded-full text-xs font-medium
                        {{ $customer->type === 'toko' ? 'bg-blue-100 text-blue-700' :
                           ($customer->type === 'proyek' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700') }}">
                        {{ strtoupper($customer->type) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('admin.customers.edit', $customer) }}"
                            class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Edit
                        </a>
                        <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST"
                            onsubmit="return confirm('Yakin hapus customer ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                Hapus
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-12 text-gray-400">
                    Belum ada data customer
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="p-6 border-t bg-gray-50">
        {{ $customers->links() }}
    </div>

</div>
