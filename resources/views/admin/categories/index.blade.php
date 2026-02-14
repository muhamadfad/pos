@extends('layouts.admin')

@section('content')
<div x-data="categoryPage()" class="max-w-6xl mx-auto space-y-6">

    <!-- Stats -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 
                text-white p-6 rounded-2xl shadow-lg flex justify-between items-center">

        <div>
            <h2 class="text-xl font-semibold">Total Kategori</h2>
            <p class="text-3xl font-bold mt-1">{{ $total }}</p>
        </div>

        <span class="iconify text-5xl opacity-20"
              data-icon="mdi:shape-outline"></span>
    </div>

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <input type="text"
               x-model="search"
               @input.debounce.500ms="fetchData()"
               placeholder="Cari kategori..."
               class="w-full md:w-80 px-4 py-2.5 border rounded-xl shadow-sm 
                      focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">

        <a href="{{ route('admin.categories.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 
                  bg-blue-600 text-white rounded-xl shadow-sm 
                  hover:bg-blue-700 hover:shadow-md transition-all">
            Tambah Kategori
        </a>
    </div>

    <!-- Table -->
    <div id="tableContent">
        @include('admin.categories.partials.table')
    </div>

    <!-- Delete Modal -->
    <div x-show="showModal"
         x-transition
         class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">

        <div class="bg-white p-6 rounded-2xl shadow-xl w-96 space-y-4">

            <h3 class="text-lg font-semibold text-gray-800">
                Hapus Kategori?
            </h3>

            <p class="text-sm text-gray-500">
                Data yang dihapus tidak bisa dikembalikan.
            </p>

            <div class="flex justify-end gap-2">
                <button @click="showModal=false"
                        class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">
                    Batal
                </button>

                <form :action="deleteUrl" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
                        Hapus
                    </button>
                </form>
            </div>

        </div>
    </div>

</div>

<script>
function categoryPage() {
    return {
        search: '',
        showModal: false,
        deleteUrl: '',

        fetchData(pageUrl = null) {

            let url = pageUrl ?? `?search=${this.search}`;

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.text())
            .then(html => {
                document.getElementById('tableContent').innerHTML = html;

                // rebind pagination
                document.querySelectorAll('#tableContent .pagination a')
                .forEach(link => {
                    link.addEventListener('click', e => {
                        e.preventDefault();
                        this.fetchData(link.href);
                    });
                });
            });
        }
    }
}

function openDeleteModal(id) {
    let alpine = document.querySelector('[x-data]').__x.$data;
    alpine.deleteUrl = `/admin/categories/${id}`;
    alpine.showModal = true;
}
</script>
@endsection
