<div class="overflow-x-auto">
    <table class="min-w-full text-sm text-left border rounded-lg overflow-hidden">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
            <tr>
                <th class="px-4 py-3">Barcode</th>
                <th class="px-4 py-3">Nama</th>
                <th class="px-4 py-3">Kategori</th>
                <th class="px-4 py-3">Harga</th>
                <th class="px-4 py-3">Stok</th>
                <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse ($products as $product)
            <tr class="hover:bg-gray-50 {{ $product->stock == 0 ? 'bg-red-50' : '' }}">
                <td class="px-4 py-3">{{ $product->barcode }}</td>
                <td class="px-4 py-3 font-medium">{{ $product->name }}</td>
                <td class="px-4 py-3">{{ $product->category->name ?? '-' }}</td>
                <td class="px-4 py-3 text-green-600 font-medium">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </td>
                <td class="px-4 py-3">
                    @if($product->stock == 0)
                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-600 font-semibold">
                            Stok Habis
                        </span>
                    @else
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-600 font-semibold">
                            {{ $product->stock }}
                        </span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center">
                    <a href="{{ route('admin.products.edit', $product) }}"
                           class="text-blue-600 hover:underline text-sm">Edit</a>

                        <form action="{{ route('admin.products.destroy', $product) }}"
                              method="POST" class="inline"
                              onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-red-600 hover:underline text-sm">
                                Hapus
                            </button>
                        </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-6 text-gray-500">
                    Tidak ada data
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $products->links() }}
</div>
