@extends('layouts.admin')

@section('title', 'Manajemen User')

@section('content')
<div x-data="userPage()" class="space-y-8">

    <!-- Branding & Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-semibold text-black tracking-tight flex items-center gap-3">
                <span class="p-2 bg-black text-white rounded-md shadow-sm">
                    <span class="iconify" data-icon="mdi:account-group"></span>
                </span>
                Manajemen User
            </h1>
            <p class="text-gray-500 font-medium tracking-tight">Kelola akses dan otoritas staff di sistem</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-black text-white rounded-md font-bold text-sm shadow-sm  hover:bg-gray-800 transition-all hover:scale-[1.02] active:scale-95">
                <span class="iconify text-lg" data-icon="mdi:account-plus"></span>
                Tambah User
            </a>
        </div>
    </div>

    <!-- Stats Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Total User Card -->
        <div
            class="relative overflow-hidden bg-white p-5 rounded-lg border border-gray-200 shadow-sm hover:shadow-sm transition-all group">
            <div
                class="absolute -right-4 -top-4 w-24 h-24 bg-gray-50 rounded-md opacity-50 group-hover:scale-110 transition-transform">
            </div>
            <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-[0.2em] mb-4 relative">Total User
                Terdaftar</p>
            <div class="flex items-end justify-between relative">
                <div class="flex items-baseline gap-1">
                    <span class="text-3xl font-semibold text-black tracking-tight">{{ $total }}</span>
                    <span class="text-gray-500 font-bold text-xs uppercase tracking-widest ml-1">Staff</span>
                </div>
                <div class="w-12 h-12 rounded-md bg-gray-50 flex items-center justify-center text-black">
                    <span class="iconify text-2xl" data-icon="mdi:account-badge-outline"></span>
                </div>
            </div>
        </div>

        <!-- Active User Card -->
        <div
            class="relative overflow-hidden bg-white p-5 rounded-lg border border-gray-200 shadow-sm hover:shadow-sm transition-all group">
            <div
                class="absolute -right-4 -top-4 w-24 h-24 bg-white border border-gray-200 rounded-md opacity-50 group-hover:scale-110 transition-transform">
            </div>
            <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-[0.2em] mb-4 relative">User Aktif</p>
            <div class="flex items-end justify-between relative">
                <div class="flex items-baseline gap-1">
                    <span class="text-3xl font-semibold text-black tracking-tight">{{ $users->where('is_active',
                        true)->count() }}</span>
                    <span class="text-gray-500 font-bold text-xs uppercase tracking-widest ml-1">Sesi Aktif</span>
                </div>
                <div class="w-12 h-12 rounded-md bg-gray-50 border border-gray-200 flex items-center justify-center text-black">
                    <span class="iconify text-2xl" data-icon="mdi:account-check-outline"></span>
                </div>
            </div>
        </div>

    </div>

    <!-- Controls & Table Area -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden min-h-[500px]">

        <!-- Toolbar -->
        <div
            class="p-6 border-b border-gray-200 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white">
            <div class="relative flex-1 max-w-md group">
                <span
                    class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 transition-colors group-focus-within:text-black">
                    <span class="iconify text-xl" data-icon="mdi:magnify"></span>
                </span>
                <input type="text" x-model="search" @input.debounce.500ms="fetchData(null)"
                    placeholder="Cari user (nama, email, atau role)..."
                    class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-md text-sm font-medium focus:ring-4 focus:ring-black/5 focus:border-gray-200 transition-all outline-none">
            </div>

            <div class="flex items-center gap-2">
                <button @click="fetchData(null)"
                    class="p-3 bg-gray-50 border border-gray-200 rounded-md hover:bg-white transition-colors text-gray-500 shadow-sm">
                    <span class="iconify" data-icon="mdi:refresh"></span>
                </button>
            </div>
        </div>

        <!-- Dynamic Table Entry -->
        <div id="tableContent" class="relative">
            <div x-show="loading" x-cloak class="absolute inset-x-0 top-0 h-[2px] bg-black animate-pulse"></div>
            @include('admin.users.partials.table')
        </div>

    </div>

</div>

<script>
    function userPage() {
    return {
        search: '',
        loading: false,

        fetchData(pageUrl = null) {
            this.loading = true;

            if (typeof pageUrl === 'object') {
                pageUrl = null;
            }

            let baseUrl ="{{ route('admin.users.index') }}";
            let url = pageUrl
                ? pageUrl
                : baseUrl + '?search=' + encodeURIComponent(this.search);

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => {
                document.getElementById('tableContent').innerHTML = html;
                this.loading = false;

                // Re-bind pagination links
                document.querySelectorAll('#tableContent .pagination a').forEach(link => {
                    link.addEventListener('click', e => {
                        e.preventDefault();
                        this.fetchData(link.href);
                    });
                });
            })
            .catch(() => {
                this.loading = false;
            });
        }
    }
}
</script>
@endsection
