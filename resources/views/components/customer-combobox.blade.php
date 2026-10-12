{{--
Customer Combobox — Alpine.js
──────────────────────────────────────
Props:
:customers — array of {id, name, type, phone}
:name — hidden input name (default:"customer_id")
:selected — initial selected customer object (optional)
:required — boolean (optional, default false)
:label — label text (optional, default"Customer")
:placeholder — search placeholder text
:allow-walk-in — allow null / walk-in selection (default true)

Usage:
<x-customer-combobox :customers="$customers" name="customer_id" :selected="$selectedCustomer ?? null" label="Customer"
    placeholder="Cari nama atau no. HP..." />
--}}

@props([
'customers' => [],
'name' => 'customer_id',
'selected' => null,
'required' => false,
'label' => 'Customer',
'placeholder' => 'Cari nama atau nomor HP...',
'allowWalkIn' => true,
])

<div x-data="{
        open: false,
        search: '',
        _dropPos: { top: 0, left: 0, width: 0 },
        _calcPos() {
            const rect = this.$el.getBoundingClientRect();
            this._dropPos = {
                top:   rect.bottom + window.scrollY + 4,
                left:  rect.left   + window.scrollX,
                width: rect.width,
            };
        },
        selected: {{ $selected ? json_encode(['id' => $selected->id, 'name' => $selected->name, 'type' => $selected->type ?? null, 'phone' => $selected->phone ?? null]) : 'null' }},
        customers: {{ json_encode($customers->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'type' => $c->type ?? null, 'phone' => $c->phone ?? null])) }},
        get filtered() {
            const q = this.search.toLowerCase().trim();
            if (!q) return this.customers.slice(0, 10);
            return this.customers.filter(c =>
                c.name.toLowerCase().includes(q) ||
                (c.phone && c.phone.includes(q))
            ).slice(0, 10);
        },
        select(customer) {
            this.selected = customer;
            this.search = '';
            this.open = false;
        },
        clear() {
            this.selected = null;
            this.search = '';
        },
        typeLabel(type) {
            const map = { store: 'Toko', project: 'Proyek', retail: 'Eceran' };
            return map[type] ?? 'Umum';
        },
        typeColor(type) {
            const map = {
                store: 'bg-white border border-gray-200 text-black border-gray-200',
                project: 'bg-white border border-gray-200 text-black border-gray-200',
                retail: 'bg-white text-gray-600 border-gray-200',
            };
            return map[type] ?? 'bg-gray-50 text-gray-500 border-gray-100';
        }
    }" x-on:keydown.escape="open = false" @scroll.window="open = false" @resize.window="open = false" class="relative">
    {{-- Hidden input for form submission --}}
    <input type="hidden" name="{{ $name }}" :value="selected ? selected.id : ''" @if($required)
        x-bind:required="!selected" @endif />

    {{-- Label --}}
    @if($label)
    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">
        {{ $label }}
        @if(!$required && $allowWalkIn)
        <span class="text-gray-500 font-normal normal-case tracking-normal ml-1">(opsional — kosong = Walk-in)</span>
        @endif
    </label>
    @endif

    {{-- Selected Badge --}}
    <div x-show="selected !== null" x-cloak class="mb-1.5">
        <div class="flex items-center justify-between px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-md">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-md bg-black flex items-center justify-center text-white text-xs font-bold shrink-0"
                    x-text="selected ? selected.name.charAt(0).toUpperCase() : ''">
                </div>
                <div>
                    <p class="text-sm font-semibold text-indigo-800 leading-tight" x-text="selected?.name"></p>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded border uppercase tracking-wide"
                            x-bind:class="typeColor(selected?.type)" x-text="typeLabel(selected?.type)">
                        </span>
                        <span class="text-xs text-black" x-text="selected?.phone ?? 'Tidak ada no. HP'"></span>
                    </div>
                </div>
            </div>
            <button type="button" @click="clear()" class="text-indigo-300 hover:text-red-500 transition-colors p-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Search Input --}}
    <div x-show="selected === null" class="relative" x-cloak>
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        <input type="text" x-model="search" @focus="_calcPos(); open = true" @input="open = true"
            @keydown.escape="open = false; search = ''" placeholder="{{ $placeholder }}" autocomplete="off"
            class="w-full pl-9 pr-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-4 focus:ring-[#1677ff]/10 focus:border-gray-200 transition-all" />
    </div>

    {{-- Walk-in placeholder when no search & no selected --}}
    <div x-show="selected === null && !open" class="mt-1" x-cloak>
        <p class="text-xs text-gray-500">Ketik untuk mencari, atau biarkan kosong untuk Walk-in Customer</p>
    </div>

    {{-- Dropdown (fixed → tembus overflow parent) --}}
    <div x-show="open && filtered.length > 0" @click.outside="open = false"
        x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        :style="`position:fixed; z-index:9999; top:${_dropPos.top}px; left:${_dropPos.left}px; width:${_dropPos.width}px; max-height:224px; overflow-y:auto;`"
        class="bg-gray-50 border border-gray-200 rounded-md shadow-sm" x-cloak>
        @if($allowWalkIn)
        <button type="button" @click="clear(); open = false"
            class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-white border-b border-gray-200 transition-colors text-left">
            <div class="w-7 h-7 rounded-md bg-slate-200 flex items-center justify-center text-gray-500 shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-700">Walk-in Customer</p>
                <p class="text-xs text-gray-500">Pelanggan umum tanpa data</p>
            </div>
        </button>
        @endif

        <template x-for="customer in filtered" :key="customer.id">
            <button type="button" @click="select(customer)"
                class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 border-b last:border-0 border-gray-200 transition-colors text-left">
                <div class="w-7 h-7 rounded-md bg-gray-50 flex items-center justify-center text-black text-xs font-bold shrink-0"
                    x-text="customer.name.charAt(0).toUpperCase()">
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-black truncate" x-text="customer.name"></p>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded border uppercase tracking-wide"
                            x-bind:class="typeColor(customer.type)" x-text="typeLabel(customer.type)">
                        </span>
                        <span class="text-xs text-gray-500" x-text="customer.phone ?? '-'"></span>
                    </div>
                </div>
            </button>
        </template>

        <div x-show="filtered.length === 0 && search.length > 0" class="px-4 py-3 text-sm text-gray-500 text-center">
            Tidak ditemukan —"<span x-text="search"></span>"
        </div>
    </div>
</div>
