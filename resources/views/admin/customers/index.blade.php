@extends('layouts.admin')

@section('title', 'Customer')

@section('content')
<div x-data="customerPage()" class="max-w-6xl mx-auto space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Customer</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kelola semua data customer / pembeli</p>
        </div>
        <a href="{{ route('admin.customers.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition shadow-md shadow-indigo-100">
            <span class="iconify" data-icon="mdi:plus"></span>
            Tambah Customer
        </a>
    </div>

    <!-- Stat + Search -->
    <div class="flex flex-col sm:flex-row gap-4">
        <div
            class="flex items-center gap-4 bg-white rounded-2xl border border-slate-200 shadow-sm px-5 py-4 min-w-[180px]">
            <div class="w-11 h-11 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-500 shrink-0">
                <span class="iconify text-2xl" data-icon="mdi:account-group-outline"></span>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Customer</p>
                <p class="text-2xl font-bold text-slate-800">{{ $total }}</p>
            </div>
        </div>
        <div class="flex-1 bg-white rounded-2xl border border-slate-200 shadow-sm px-4 py-3 flex items-center gap-3">
            <span class="iconify text-slate-400 text-xl shrink-0" data-icon="mdi:magnify"></span>
            <input type="text" x-model="search" @input.debounce.400ms="fetchData()"
                placeholder="Cari nama, alamat, atau no hp..."
                class="flex-1 bg-transparent text-sm text-slate-700 placeholder-slate-400 outline-none">
            <button x-show="search" @click="search=''; fetchData()"
                class="text-slate-400 hover:text-slate-600 transition">
                <span class="iconify" data-icon="mdi:close-circle"></span>
            </button>
        </div>
    </div>

    <!-- Table -->
    <div id="tableContent">
        @include('admin.customers.partials.table')
    </div>

</div>

<script>
    function customerPage() {
    return {
        search: '',
        fetchData() {
            let url = `{{ route('admin.customers.index') }}?search=${encodeURIComponent(this.search)}`;
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
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
