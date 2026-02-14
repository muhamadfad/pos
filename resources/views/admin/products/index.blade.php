@extends('layouts.admin')

@section('title', 'Daftar Barang')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm border">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">📦 Daftar Barang</h2>
        <a href="{{ route('admin.products.create') }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            + Tambah
        </a>
    </div>

    


    <!-- Table -->
    <div class="overflow-x-auto">
        <div x-data="productTable()" class="bg-white p-6 rounded-xl shadow-sm border">

    <!-- FILTER BAR -->
    <div class="flex flex-wrap gap-3 mb-6">

        <!-- Search -->
        <input type="text"
               x-model="search"
               @input.debounce.500ms="fetchData"
               placeholder="Cari produk..."
               class="px-4 py-2 border rounded-lg text-sm w-64">

        <!-- Kategori -->
        <!-- Kategori Custom -->
<div x-data="{ open:false }" class="relative w-64">

    <!-- Trigger -->
    <button type="button"
        @click="open=!open"
        @click.away="open=false"
        class="w-full flex items-center justify-between px-4 py-2.5 
               bg-white border border-gray-300 rounded-xl 
               shadow-sm hover:border-blue-500 hover:shadow-md 
               transition-all duration-200">

        <span class="text-sm font-medium text-gray-700"
              x-text="categoryName()"></span>

        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
             :class="{'rotate-180':open}"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <!-- Dropdown -->
    <div x-show="open"
         x-transition
         class="absolute mt-2 w-full bg-white border border-gray-200 
                rounded-xl shadow-xl overflow-hidden z-50">

        <div class="max-h-60 overflow-y-auto custom-scroll">

            <!-- Semua -->
            <div @click="category=''; open=false; fetchData()"
                 class="px-4 py-2.5 text-sm cursor-pointer 
                        hover:bg-gray-100 transition">
                Semua Kategori
            </div>

            @foreach($categories as $cat)
            <div @click="category='{{ $cat->id }}'; open=false; fetchData()"
                 class="px-4 py-2.5 text-sm cursor-pointer flex justify-between items-center
                        hover:bg-blue-50 transition"
                 :class="category=='{{ $cat->id }}' ? 'bg-blue-50 text-blue-600 font-medium' : ''">
                {{ $cat->name }}

                <svg x-show="category=='{{ $cat->id }}'"
                     class="w-4 h-4"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            @endforeach

        </div>
    </div>
</div>


        <!-- Sorting -->
        <select x-model="sort" @change="fetchData"
                class="px-4 py-2 border rounded-lg text-sm">
            <option value="">Urutkan</option>
            <option value="price">Harga</option>
            <option value="stock">Stok</option>
        </select>

        <!-- Per Page -->
        <select x-model="per_page" @change="fetchData"
                class="px-4 py-2 border rounded-lg text-sm">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </select>

    </div>

    <!-- TABLE CONTENT -->
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

            const categories = @json($categories);
            const found = categories.find(c => c.id == this.category);
            return found ? found.name : 'Semua Kategori';
        },

        fetchData() {
            let params = new URLSearchParams({
                search: this.search,
                category: this.category,
                sort: this.sort,
                per_page: this.per_page
            });

            fetch(`?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.text())
            .then(html => {
                document.getElementById('tableContent').innerHTML = html;
            });
        }
    }
}

</script>


</div>
@endsection
