<div class="overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead>
            <tr class="border-b border-slate-100 bg-slate-50">
                <th class="px-5 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider w-12">#
                </th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Barcode
                </th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama
                    Produk</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Kategori
                </th>
                <th class="px-5 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Harga
                    Beli</th>
                <th class="px-5 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Harga
                    Jual</th>
                <th class="px-5 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Stok
                </th>
                <th class="px-5 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($products as $product)
            <tr class="group hover:bg-slate-50 transition-colors {{ $product->stock == 0 ? 'bg-red-50/40' : '' }}">
                <td class="px-5 py-3.5 text-center text-xs text-slate-400 font-medium">
                    {{ $products->firstItem() + $loop->index }}
                </td>
                <td class="px-5 py-3.5">
                    <span class="font-mono text-xs text-slate-500 bg-slate-100 px-2 py-1 rounded">{{ $product->barcode
                        }}</span>
                </td>
                <td class="px-5 py-3.5">
                    <p class="font-semibold text-slate-800">{{ $product->name }}</p>
                </td>
                <td class="px-5 py-3.5">
                    <span
                        class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-indigo-50 text-indigo-700">
                        {{ $product->category->name ?? '-' }}
                    </span>
                </td>
                <td class="px-5 py-3.5 text-right text-slate-500 text-xs">
                    Rp {{ number_format($product->purchase_price ?? 0, 0, ',', '.') }}
                </td>
                <td class="px-5 py-3.5 text-right">
                    <span class="font-semibold text-slate-800">Rp {{ number_format($product->selling_price, 0, ',', '.')
                        }}</span>
                </td>
                <td class="px-5 py-3.5 text-center">
                    @if($product->stock == 0)
                    <span
                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-100 text-red-600">
                        <span class="iconify" data-icon="mdi:alert-circle-outline"></span>
                        Habis
                    </span>
                    @elseif($product->stock <= 5) <span
                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-100 text-amber-700">
                        <span class="iconify" data-icon="mdi:alert-outline"></span>
                        {{ $product->stock }}
                        </span>
                        @else
                        <span
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-700">
                            {{ $product->stock }}
                        </span>
                        @endif
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center justify-center gap-1">
                        <a href="{{ route('admin.products.show', $product) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                            <span class="iconify" data-icon="mdi:eye-outline"></span>
                            Detail
                        </a>
                        <a href="{{ route('admin.products.edit', $product) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                            <span class="iconify" data-icon="mdi:pencil-outline"></span>
                            Edit
                        </a>
                        <button type="button"
                            onclick="confirmDelete('{{ route('admin.products.destroy', $product) }}', '{{ addslashes($product->name) }}')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">
                            <span class="iconify" data-icon="mdi:trash-can-outline"></span>
                            Hapus
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-16">
                    <div class="flex flex-col items-center gap-3 text-slate-400">
                        <span class="iconify text-5xl" data-icon="mdi:package-variant-closed-remove"></span>
                        <p class="font-medium">Tidak ada produk ditemukan</p>
                        <p class="text-sm">Coba ubah filter pencarian Anda</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($products->hasPages())
<div class="px-5 py-4 border-t border-slate-100">
    {{ $products->links() }}
</div>
@endif
