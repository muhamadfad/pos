@extends('layouts.admin')

@section('title', 'Data Supplier')

@section('content')
<div x-data="supplierPage()" class="max-w-[1100px] mx-auto space-y-6 pb-10">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Supplier</h1>
            <p class="text-sm text-slate-500 mt-0.5 font-medium">Kelola semua data mitra supplier</p>
        </div>
        <a href="{{ route('admin.suppliers.create') }}"
            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-sm active:scale-95">
            <span class="iconify text-lg" data-icon="lucide:plus-circle"></span>
            Tambah Supplier
        </a>
    </div>

    <div class="flex flex-col md:flex-row gap-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm px-5 py-4 flex items-center gap-4 min-w-[220px]">
            <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-indigo-500 shrink-0 border border-slate-100">
                <span class="iconify text-2xl" data-icon="lucide:truck"></span>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Mitra</p>
                <p class="text-2xl font-bold text-slate-900 tracking-tight">{{ $total }}</p>
            </div>
        </div>

        <div class="flex-1 bg-white rounded-xl border border-slate-200 shadow-sm px-4 flex items-center gap-3 group focus-within:border-indigo-300 focus-within:ring-4 focus-within:ring-indigo-50 transition-all">
            <span class="iconify text-slate-400 text-xl shrink-0 group-focus-within:text-indigo-500 transition-colors" data-icon="lucide:search"></span>
            <input type="text" x-model="search" @input.debounce.400ms="fetchData(null)"
                placeholder="Cari nama, alamat, atau nomor telepon..."
                class="flex-1 py-4 bg-transparent text-sm text-slate-700 placeholder-slate-400 outline-none">
            
            <button x-show="search" @click="search=''; fetchData(null)" x-cloak
                class="w-6 h-6 flex items-center justify-center rounded-full bg-slate-100 text-slate-400 hover:bg-rose-50 hover:text-rose-500 transition-all">
                <span class="iconify text-sm" data-icon="lucide:x"></span>
            </button>
        </div>
    </div>

    <div id="tableContent" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        @include('admin.suppliers.partials.table')
    </div>

</div>

<script>
    function supplierPage() {
        return {
            search: '',

            fetchData(pageUrl = null) {
                if (typeof pageUrl === 'object') {
                    pageUrl = null;
                }

                let baseUrl = "{{ route('admin.suppliers.index') }}";
                let url = pageUrl ? pageUrl : baseUrl + '?search=' + encodeURIComponent(this.search);

                // Tampilkan loading overlay jika diperlukan
                document.getElementById('tableContent').classList.add('opacity-50');

                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.text())
                .then(html => {
                    document.getElementById('tableContent').innerHTML = html;
                    document.getElementById('tableContent').classList.remove('opacity-50');

                    // Re-bind pagination click events if they are part of the returned HTML
                    this.bindPagination();
                });
            },

            bindPagination() {
                document.querySelectorAll('#tableContent .pagination a, #tableContent button[@click*="fetchData"]')
                .forEach(element => {
                    // Jika itu link standar laravel
                    if (element.tagName === 'A') {
                        element.addEventListener('click', e => {
                            e.preventDefault();
                            this.fetchData(element.href);
                        });
                    }
                });
            }
        }
    }
</script>
@endsection