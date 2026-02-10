<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Admin Panel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
<div class="min-h-screen bg-gray-100 flex">

    <!-- Sidebar Left -->
    <!-- Sidebar Left -->
<aside class="w-64 bg-white shadow-sm border-r hidden md:flex flex-col h-screen sticky top-0">
    <div class="h-16 flex items-center px-6 font-semibold text-lg border-b">
        Admin Panel
    </div>

    <nav class="flex-1 py-4 space-y-1 overflow-y-auto">
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition">
            <span class="ml-1">Dashboard</span>
        </a>

        <a href="{{ route('admin.products.index') }}"
           class="flex items-center px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition">
            <span class="ml-1">Produk</span>
        </a>

        <a href="{{ route('admin.categories.index') }}"
           class="flex items-center px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition">
            <span class="ml-1">Kategori</span>
        </a>

        <!-- Transaksi Barang -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="w-full flex justify-between items-center px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg">
                <span>Transaksi Barang</span>
                <svg :class="{'rotate-180': open}" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" class="mt-1 space-y-1 pl-4">
                <a href="{{ route('admin.transactions.in.index') }}" class="block px-6 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">Barang Masuk</a>
                <a href="{{ route('admin.transactions.out.index') }}" class="block px-6 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">Barang Keluar</a>
            </div>
        </div>

        <!-- Laporan Keuangan / Finance -->
        <div x-data="{ openFinance: false }" class="relative">
            <button @click="openFinance = !openFinance" class="w-full flex justify-between items-center px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg">
                <span>Finance / Laporan</span>
                <svg :class="{'rotate-180': openFinance}" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="openFinance" class="mt-1 space-y-1 pl-4">
                <a href="{{ route('admin.finance.index') }}" class="block px-6 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">Laporan Keuangan</a>
                <!-- Bisa ditambahkan submenu lain jika perlu, contoh: -->
                <a href="{{ route('admin.finance.daily') }}" class="block px-6 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">Harian</a>
                <a href="{{ route('admin.finance.weekly') }}" class="block px-6 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">Mingguan</a>
                <a href="{{ route('admin.finance.monthly') }}" class="block px-6 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">Bulanan</a>
            </div>
        </div>

        <a href="{{ route('admin.users.index') }}"
           class="flex items-center px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition">
            <span class="ml-1">User</span>
        </a>

        <a href="{{ route('profile.edit') }}"
           class="flex items-center px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition">
            <span class="ml-1">Profil</span>
        </a>

    </nav>

    <div class="border-t p-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full text-left px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition">
                Logout
            </button>
        </form>
    </div>
</aside>


    <!-- Main Area -->
    <div class="flex-1 flex flex-col">

        <!-- Topbar -->
        <header class="bg-white shadow sticky top-0 z-10">
            <div class="max-w-7xl mx-auto py-4 px-6 flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    @yield('title', 'Dashboard')
                </h2>

                <div class="flex items-center space-x-3">
                    <span class="text-sm text-gray-600">
                        {{ Auth::user()->name }}
                    </span>
                    <img
                        src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff"
                        class="w-9 h-9 rounded-full border"
                        alt="User Avatar"
                    >
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 py-6 px-6 overflow-auto">
            @yield('content')
        </main>
    </div>

</div>
</body>
</html>
