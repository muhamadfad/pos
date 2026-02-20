@extends('layouts.admin')

@section('title', 'Daftar Produk')

@section('content')
<div x-data="productTable()">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Daftar Produk</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kelola semua produk yang tersedia di toko</p>
        </div>
        <a href="{{ route('admin.products.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 active:bg-indigo-800 transition-all shadow-md shadow-indigo-100">
            <span class="iconify" data-icon="mdi:plus"></span>
            Tambah Produk
        </a>
    </div>

    <!-- Flash Message -->
    @if(session('success'))
    <div
        class="mb-4 flex items-center gap-3 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">
        <span class="iconify shrink-0" data-icon="mdi:check-circle-outline"></span>
        {{ session('success') }}
    </div>
    @endif

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-4">
        <div class="flex flex-wrap gap-3 items-center">

            <!-- Search -->
            <div class="relative flex-1 min-w-[200px]">
                <span class="iconify absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"
                    data-icon="mdi:magnify"></span>
                <input type="text" x-model="search" @input.debounce.400ms="fetchData"
                    placeholder="Cari nama atau barcode..."
                    class="w-full pl-9 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
            </div>

            <!-- Kategori Dropdown -->
            <div x-data="{ open: false }" class="relative min-w-[180px]">
                <button type="button" @click="open = !open"
                    class="w-full flex items-center justify-between gap-2 px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl hover:border-indigo-400 transition">
                    <div class="flex items-center gap-2 text-slate-600">
                        <span class="iconify text-slate-400" data-icon="mdi:shape-outline"></span>
                        <span x-text="categoryName()"></span>
                    </div>
                    <span class="iconify text-slate-400 transition-transform" :class="{'rotate-180': open}"
                        data-icon="mdi:chevron-down"></span>
                </button>
                <div x-show="open" @click.away="open = false" x-transition
                    class="absolute top-full mt-1 left-0 w-full bg-white border border-slate-200 rounded-xl shadow-lg z-50 overflow-hidden">
                    <div class="max-h-52 overflow-y-auto">
                        <div @click="category = ''; open = false; fetchData()"
                            class="px-4 py-2.5 text-sm cursor-pointer hover:bg-slate-50 transition"
                            :class="category === '' ? 'text-indigo-600 font-semibold bg-indigo-50' : 'text-slate-700'">
                            Semua Kategori
                        </div>
                        @foreach($categories as $cat)
                        <div @click="category = '{{ $cat->id }}'; open = false; fetchData()"
                            class="px-4 py-2.5 text-sm cursor-pointer hover:bg-slate-50 transition flex items-center justify-between"
                            :class="category == '{{ $cat->id }}' ? 'text-indigo-600 font-semibold bg-indigo-50' : 'text-slate-700'">
                            {{ $cat->name }}
                            <span x-show="category == '{{ $cat->id }}'" class="iconify text-indigo-500"
                                data-icon="mdi:check"></span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sort -->
            <div class="relative">
                <span class="iconify absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
                    data-icon="mdi:sort"></span>
                <select x-model="sort" @change="fetchData"
                    class="pl-9 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition appearance-none">
                    <option value="">Urutkan</option>
                    <option value="price">Harga</option>
                    <option value="stock">Stok</option>
                </select>
            </div>

            <!-- Per Page -->
            <select x-model="per_page" @change="fetchData"
                class="px-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                <option value="10">10 / halaman</option>
                <option value="25">25 / halaman</option>
                <option value="50">50 / halaman</option>
            </select>

        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div id="tableContent">
            @include('admin.products.partials.table')
        </div>
    </div>

</div>

<script>
    function productTable() {
    return {
        search: '',
        category: '',
        sort: '',
        per_page: 10,

        categoryName() {
            if (!this.category) return 'Semua Kategori';
            const categories = {!! json_encode($categories->values()->toArray()) !!};
            const found = categories.find(c => c.id == this.category);
            return found ? found.name : 'Semua Kategori';
        },

        fetchData() {
            const params = new URLSearchParams({
                search: this.search,
                category: this.category,
                sort: this.sort,
                per_page: this.per_page
            });
            fetch(`?${params.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => {
                document.getElementById('tableContent').innerHTML = html;
                if (window.Iconify) window.Iconify.scan();
            });
        }
    }
}
</script>
@endsection
