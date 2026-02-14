@extends('layouts.admin')

@section('title', 'Transaksi Barang Masuk')

@section('content')
<div x-data="transactionPage()" class="max-w-7xl mx-auto space-y-6">

    <!-- Header Stat -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 
                text-white p-6 rounded-2xl shadow-lg">

        <h2 class="text-lg font-medium">
            Transaksi Barang Masuk
        </h2>

        <p class="text-2xl font-bold mt-1">
            {{ $mitra->nama_mitra }}
        </p>

        <p class="text-sm mt-2 opacity-80">
            Total transaksi: {{ $total }}
        </p>
    </div>

    <!-- ACTION BUTTONS (KANAN ATAS) -->
<div class="flex justify-end gap-2">

    <!-- Tambah Transaksi -->
    <a href="{{ route('admin.transactions.in.create', $mitra->id) }}"
       class="flex items-center gap-2 px-4 py-2 bg-blue-600 
              text-white text-sm rounded-lg shadow
              hover:bg-blue-700 transition whitespace-nowrap">

        <svg xmlns="http://www.w3.org/2000/svg"
             class="w-4 h-4"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor"
             stroke-width="2">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M12 4v16m8-8H4"/>
        </svg>

        Tambah Transaksi
    </a>

    <!-- Download PDF -->
    <a :href="`{{ route('admin.transactions.in.download', $mitra->id) }}?search=` + search + 
            `&filter=` + filter + 
            `&start_date=` + start_date + 
            `&end_date=` + end_date"
       target="_blank"
       class="flex items-center gap-2 px-4 py-2 bg-red-600 
              text-white text-sm rounded-lg shadow
              hover:bg-red-700 transition whitespace-nowrap">

        <svg xmlns="http://www.w3.org/2000/svg"
             class="w-4 h-4"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor"
             stroke-width="2">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M12 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V10l-8-6z"/>
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M12 4v6h6"/>
        </svg>

        Download PDF
    </a>

</div>


<!-- FILTER BAR (FULL WIDTH DI BAWAH) -->
<div class="mt-4 bg-white p-4 rounded-2xl shadow-sm border">

    <div class="flex flex-wrap items-center gap-3">

        <!-- Search -->
        <input type="text"
               x-model="search"
               @input.debounce.500ms="fetchData()"
               placeholder="Cari nama barang..."
               class="px-4 py-2 text-sm border rounded-lg w-64">

        <!-- Quick Filter -->
        <select x-model="filter"
                @change="start_date=''; end_date=''; fetchData()"
                class="px-4 py-2 text-sm border rounded-lg w-44">
            <option value="">Semua Periode</option>
            <option value="today">Hari Ini</option>
            <option value="month">Bulan Ini</option>
        </select>

        <!-- Date Range -->
        <input type="date"
               x-model="start_date"
               @change="filter=''; fetchData()"
               class="px-3 py-2 text-sm border rounded-lg">

        <span class="text-gray-400 text-sm">s/d</span>

        <input type="date"
               x-model="end_date"
               @change="filter=''; fetchData()"
               class="px-3 py-2 text-sm border rounded-lg">

    </div>

</div>



    <!-- Table -->
    <div id="tableContent">
        
        @include('admin.transactions.in.partials.table')
    </div>

</div>

<script>
function transactionPage() {
    return {
        search: '',
        filter: '',
        start_date: '',
        end_date: '',

        fetchData(pageUrl = null) {

            let baseUrl = "{{ route('admin.transactions.in.perMitra', $mitra->id) }}";

            let url = pageUrl ? pageUrl : baseUrl +
                '?search=' + this.search +
                '&filter=' + this.filter +
                '&start_date=' + this.start_date +
                '&end_date=' + this.end_date;

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
</script>
@endsection
