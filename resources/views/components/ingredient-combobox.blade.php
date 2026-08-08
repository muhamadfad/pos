{{--
ingredient Combobox — Alpine.js (untuk baris di dalam x-for)
──────────────────────────────────────────────────────────
Dirancang untuk digunakan di dalam <template x-for="(item, index) in items">
    State pencarian disimpan di item itu sendiri (item._search, item._open)
    sehingga setiap baris punya state independen tanpa konflik scope.

    Props (Blade — pakai @include, bukan <x-component>):
        $priceKey — field harga: "selling_price" | "purchase_price" (default: selling_price)
        $showStock — boolean, tampilkan badge stok (default: false)

        Usage di dalam x-for:
        @include('components.ingredient-combobox', ['priceKey' => 'selling_price', 'showStock' => true])

        Parent Alpine component WAJIB:
        - Expose `_ingredients` property (JSON string) di x-data root
        - item struktur: { ingredient_id, price, unit_name, [stock_available] }
        --}}
        @php $priceKey = $priceKey ?? 'selling_price'; $showStock = $showStock ?? false; @endphp

        <div class="relative" @click.outside="item._open = false" @keydown.escape.window="item._open = false"
            @scroll.window="item._open = false" @resize.window="item._open = false" x-ref="comboWrap">

            {{-- Hidden input untuk form POST (ingredient_id) --}}
            <input type="hidden" :name="'items[' + index + '][ingredient_id]'" :value="item.ingredient_id" />

            {{-- ── Badge Bahan Baku terpilih ── --}}
            <div x-show="item.ingredient_id"
                class="flex items-center gap-2 px-3 py-2 bg-indigo-50 border border-indigo-200 rounded-xl cursor-pointer hover:bg-indigo-100 transition-colors"
                @click="
            item.ingredient_id = ''; item.price = 0; item.unit_name = '';
            @if($showStock) item.stock_available = 0; @endif
            item._search = '';
            const r = $refs.comboWrap.getBoundingClientRect();
            item._dropPos = { top: r.bottom + window.scrollY + 4, left: r.left + window.scrollX, width: r.width };
            item._open = true;
        " title="Klik untuk ganti Bahan Baku">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-indigo-800 truncate"
                        x-text="_ingredients.find(p => p.id == item.ingredient_id)?.name ?? '—'"></p>
                    <p class="text-xs text-indigo-400" x-text="item.unit_name"></p>
                </div>
                <button type="button"
                    @click.stop="item.ingredient_id = ''; item.price = 0; item.unit_name = ''; @if($showStock) item.stock_available = 0; @endif item._search = ''; item._open = false;"
                    class="text-indigo-300 hover:text-red-500 transition-colors shrink-0" title="Hapus pilihan">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- ── Input pencarian (tampil saat belum ada pilihan) ── --}}
            <div x-show="!item.ingredient_id" class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" x-model="item._search" @focus="
                const r = $refs.comboWrap.getBoundingClientRect();
                item._dropPos = { top: r.bottom + window.scrollY + 4, left: r.left + window.scrollX, width: r.width };
                item._open = true;
            " @input="item._open = true" @keydown.escape.stop="item._open = false; item._search = ''"
                    placeholder="Cari Bahan Baku..." autocomplete="off"
                    class="w-full pl-9 pr-3 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all" />
            </div>

            {{-- ── Dropdown hasil pencarian (fixed → tembus overflow) ── --}}
            <div x-show="item._open && !item.ingredient_id" x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                :style="'position:fixed; z-index:9999; top:' + ((item._dropPos || {}).top || 0) + 'px; left:' + ((item._dropPos || {}).left || 0) + 'px; width:' + ((item._dropPos || {}).width || 280) + 'px; min-width:280px; max-height:220px; overflow-y:auto;'"
                class="bg-white border border-slate-200 rounded-xl shadow-2xl">
                <template x-for="ingredient in _ingredients.filter(p => {
            const q = (item._search ?? '').toLowerCase().trim();
            return !q || p.name.toLowerCase().includes(q);
        }).slice(0, 10)" :key="ingredient.id">
                    <button type="button" @click.prevent="
                    item.ingredient_id = ingredient.id;
                    item.price      = ingredient.{{ $priceKey }};
                    item.unit_name  = ingredient.unit_name;
                    @if($showStock) item.stock_available = ingredient.stock; @endif
                    item._open   = false;
                    item._search = '';
                " class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-indigo-50 border-b border-slate-100 last:border-0 transition-colors text-left">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-800 truncate" x-text="ingredient.name"></p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-[11px] text-slate-400" x-text="ingredient.unit_name"></span>
                                @if($showStock)
                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded"
                                    :class="ingredient.stock > 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-500'"
                                    x-text="'Stok: ' + ingredient.stock"></span>
                                @endif
                            </div>
                        </div>
                        <span class="text-xs font-bold text-slate-500 shrink-0"
                            x-text="'Rp ' + Number(ingredient.{{ $priceKey }}).toLocaleString('id-ID')"></span>
                    </button>
                </template>
                <div x-show="_ingredients.filter(p => {
                const q = (item._search ?? '').toLowerCase().trim();
                return !q || p.name.toLowerCase().includes(q);
            }).length === 0" class="px-4 py-3 text-sm text-slate-400 text-center">
                    Tidak ditemukan — "<span x-text="item._search ?? ''"></span>"
                </div>
            </div>
        </div>
