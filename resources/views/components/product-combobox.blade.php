{{--
Product Combobox — Alpine.js (untuk baris di dalam x-for)
──────────────────────────────────────────────────────────
Dirancang untuk digunakan di dalam <template x-for="(item, index) in items">
    State pencarian disimpan di item itu sendiri (item._search, item._open)
    sehingga setiap baris punya state independen tanpa konflik scope.

    Props (Blade — pakai @include, bukan <x-component>):
        $priceKey — field harga:"selling_price" |"purchase_price" (default: selling_price)
        $showStock — boolean, tampilkan badge stok (default: false)

        Usage di dalam x-for:
        @include('components.product-combobox', ['priceKey' => 'selling_price', 'showStock' => true])

        Parent Alpine component WAJIB:
        - Expose `_products` property (JSON string) di x-data root
        - item struktur: { product_id, price, unit_name, [stock_available] }
        --}}
        @php $priceKey = $priceKey ?? 'selling_price'; $showStock = $showStock ?? false; @endphp

        <div class="relative" @click.outside="item._open = false" @keydown.escape.window="item._open = false"
            @scroll.window="item._open = false" @resize.window="item._open = false" x-ref="comboWrap">

            {{-- Hidden input untuk form POST (product_id) --}}
            <input type="hidden" :name="'items[' + index + '][product_id]'" :value="item.product_id" />

            {{-- ── Badge produk terpilih ── --}}
            <div x-show="item.product_id"
                class="flex items-center gap-2 px-3 py-2 bg-gray-50 border border-gray-200 rounded-md cursor-pointer hover:bg-gray-50 transition-colors"
                @click="
            item.product_id = ''; item.price = 0; item.unit_name = '';
            @if($showStock) item.stock_available = 0; @endif
            item._search = '';
            const r = $refs.comboWrap.getBoundingClientRect();
            item._dropPos = { top: r.bottom + window.scrollY + 4, left: r.left + window.scrollX, width: r.width };
            item._open = true;" title="Klik untuk ganti produk">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-indigo-800 truncate"
                        x-text="_products.find(p => p.id == item.product_id)?.name ?? '—'"></p>
                    <p class="text-xs text-black" x-text="item.unit_name"></p>
                </div>
                <button type="button"
                    @click.stop="item.product_id = ''; item.price = 0; item.unit_name = ''; @if($showStock) item.stock_available = 0; @endif item._search = ''; item._open = false;"
                    class="text-indigo-300 hover:text-red-500 transition-colors shrink-0" title="Hapus pilihan">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- ── Input pencarian (tampil saat belum ada pilihan) ── --}}
            <div x-show="!item.product_id" class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" x-model="item._search" @focus="
                const r = $refs.comboWrap.getBoundingClientRect();
                item._dropPos = { top: r.bottom + window.scrollY + 4, left: r.left + window.scrollX, width: r.width };
                item._open = true;" @input="item._open = true" @keydown.escape.stop="item._open = false; item._search = ''"
                    placeholder="Cari produk..." autocomplete="off"
                    class="w-full pl-9 pr-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-4 focus:ring-[#1677ff]/10 focus:border-gray-200 transition-all" />
            </div>

            {{-- ── Dropdown hasil pencarian (fixed → tembus overflow) ── --}}
            <div x-show="item._open && !item.product_id" x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                :style="'position:fixed; z-index:9999; top:' + ((item._dropPos || {}).top || 0) + 'px; left:' + ((item._dropPos || {}).left || 0) + 'px; width:' + ((item._dropPos || {}).width || 280) + 'px; min-width:280px; max-height:220px; overflow-y:auto;'"
                class="bg-gray-50 border border-gray-200 rounded-md shadow-sm">
                <template x-for="product in _products.filter(p => {
            const q = (item._search ?? '').toLowerCase().trim();
            return !q || p.name.toLowerCase().includes(q);
        }).slice(0, 10)" :key="product.id">
                    <button type="button" @click.prevent="
                    item.product_id = product.id;
                    item.price      = product.{{ $priceKey }};
                    item.unit_name  = product.unit_name;
                    @if($showStock) item.stock_available = product.stock; @endif
                    item._open   = false;
                    item._search = '';" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 border-b border-gray-200 last:border-0 transition-colors text-left">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-black truncate" x-text="product.name"></p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-[11px] text-gray-500" x-text="product.unit_name"></span>
                                @if($showStock)
                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded"
                                    :class="product.stock > 0 ? 'bg-white border border-gray-200 text-black' : 'bg-red-50 text-red-500'"
                                    x-text="'Stok: ' + product.stock"></span>
                                @endif
                            </div>
                        </div>
                        <span class="text-xs font-bold text-gray-500 shrink-0"
                            x-text="'Rp ' + Number(product.{{ $priceKey }}).toLocaleString('id-ID')"></span>
                    </button>
                </template>
                <div x-show="_products.filter(p => {
                const q = (item._search ?? '').toLowerCase().trim();
                return !q || p.name.toLowerCase().includes(q);
            }).length === 0" class="px-4 py-3 text-sm text-gray-500 text-center">
                    Tidak ditemukan —"<span x-text="item._search ?? ''"></span>"
                </div>
            </div>
        </div>
