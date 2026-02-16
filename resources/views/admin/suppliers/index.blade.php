@extends('layouts.admin')

@section('title', 'Data Supplier')

@section('content')
<div x-data="supplierPage()" class="max-w-7xl mx-auto space-y-6">

    <!-- Stat Card -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600
                text-white p-6 rounded-2xl shadow-lg flex justify-between items-center">

        <div>
            <h2 class="text-lg font-medium">Total Supplier</h2>
            <p class="text-3xl font-bold mt-1">{{ $total }}</p>
        </div>
    </div>

    <!-- Header & Search -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <input type="text" x-model="search" @input.debounce.500ms="fetchData(null)"
            placeholder="Cari nama, alamat, atau no hp..." class="w-full md:w-80 px-4 py-2.5 border rounded-xl shadow-sm
                      focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">

        <a href="{{ route('admin.suppliers.create') }}" class="inline-flex items-center px-4 py-2.5
                  bg-indigo-600 text-white rounded-xl shadow-sm
                  hover:bg-indigo-700 hover:shadow-md transition-all">
            Tambah Supplier
        </a>
    </div>

    <!-- Table -->
    <div id="tableContent">
        @include('admin.suppliers.partials.table')
    </div>

    <!-- Delete Modal -->
    <div x-show="showModal" x-transition class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">

        <div class="bg-white p-6 rounded-2xl shadow-xl w-96 space-y-4">

            <h3 class="text-lg font-semibold text-gray-800">
                Hapus Supplier?
            </h3>

            <p class="text-sm text-gray-500">
                Data yang dihapus tidak dapat dikembalikan.
            </p>

            <div class="flex justify-end gap-2">
                <button @click="showModal=false" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">
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
    function supplierPage() {
    return {
        search: '',
        showModal: false,
        deleteUrl: '',

        fetchData(pageUrl = null) {

            if (typeof pageUrl === 'object') {
                pageUrl = null;
            }

            let baseUrl = "{{ route('admin.suppliers.index') }}";

            let url = pageUrl
                ? pageUrl
                : baseUrl + '?search=' + encodeURIComponent(this.search);

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => {
                document.getElementById('tableContent').innerHTML = html;

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
    alpine.deleteUrl = `/admin/suppliers/${id}`;
    alpine.showModal = true;
}
</script>
@endsection
