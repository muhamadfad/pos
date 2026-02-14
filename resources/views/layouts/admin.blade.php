<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Admin Panel') }}</title>

    @vite(['resources/css/app.css','resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <script src="https://code.iconify.design/3/3.1.1/iconify.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>[x-cloak]{display:none!important;}</style>
    <style>
/* ===== Custom Sidebar Scrollbar ===== */

.sidebar-scroll::-webkit-scrollbar {
    width: 6px;
}

.sidebar-scroll::-webkit-scrollbar-track {
    background: transparent;
}

.sidebar-scroll::-webkit-scrollbar-thumb {
    background-color: #334155; /* slate-700 */
    border-radius: 20px;
}

.sidebar-scroll::-webkit-scrollbar-thumb:hover {
    background-color: #475569; /* slate-600 */
}

/* Firefox */
.sidebar-scroll {
    scrollbar-width: thin;
    scrollbar-color: #334155 transparent;
}
</style>

</head>


<body class="font-sans antialiased bg-slate-100 text-slate-800">

<div 
    x-data="{ 
        sidebarOpen: false, 
        sidebarCollapsed: window.innerWidth < 768 
    }" 
    class="h-screen overflow-hidden"
>

    <!-- Overlay -->
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/40 z-30 md:hidden"></div>

    <!-- SIDEBAR -->
    <aside x-cloak
        :class="[
            sidebarOpen ? 'translate-x-0' : '-translate-x-full',
            sidebarCollapsed ? 'w-20' : 'w-64'
        ]"
        class="fixed inset-y-0 left-0 z-40
               bg-slate-900 text-slate-400
               border-r border-slate-800
               shadow-xl
               transform transition-all duration-300
               flex flex-col md:translate-x-0">

        <!-- Logo -->
        <div class="px-5 py-5 border-b border-slate-800 space-y-1">

    <div class="flex items-center gap-3">
        <span class="iconify text-2xl text-indigo-500" data-icon="mdi:storefront"></span>
        <span x-show="!sidebarCollapsed" class="font-semibold text-white text-base tracking-tight">
            Depo Bangunan
        </span>
    </div>

    <div x-show="!sidebarCollapsed" class="pl-9">
        <p class="text-xs text-slate-400">Sumber Jaya</p>
        <p class="text-[11px] text-slate-500">Kolaka</p>
    </div>

</div>


        <!-- NAV -->
        <nav class="flex-1 py-4 px-3 space-y-1 overflow-y-auto sidebar-scroll text-sm">

            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200
               {{ request()->routeIs('admin.dashboard') 
                    ? 'bg-slate-800 text-white' 
                    : 'hover:bg-slate-800 hover:text-white' }}">
                <span class="iconify text-xl text-slate-400" data-icon="mdi:view-dashboard-outline"></span>
                <span x-show="!sidebarCollapsed">Dashboard</span>
            </a>

            <!-- Produk -->
            <a href="{{ route('admin.products.index') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200
               {{ request()->routeIs('admin.products.*') 
                    ? 'bg-slate-800 text-white' 
                    : 'hover:bg-slate-800 hover:text-white' }}">
                <span class="iconify text-xl text-slate-400" data-icon="mdi:package-variant-closed"></span>
                <span x-show="!sidebarCollapsed">Produk</span>
            </a>

            <!-- Kategori -->
            <a href="{{ route('admin.categories.index') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200
               {{ request()->routeIs('admin.categories.*') 
                    ? 'bg-slate-800 text-white' 
                    : 'hover:bg-slate-800 hover:text-white' }}">
                <span class="iconify text-xl text-slate-400" data-icon="mdi:shape-outline"></span>
                <span x-show="!sidebarCollapsed">Kategori</span>
            </a>

            <!-- Mitra -->
            <a href="{{ route('admin.mitra.index') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200
               {{ request()->routeIs('admin.mitra.*') 
                    ? 'bg-slate-800 text-white' 
                    : 'hover:bg-slate-800 hover:text-white' }}">
                <span class="iconify text-xl text-slate-400" data-icon="mdi:account-group-outline"></span>
                <span x-show="!sidebarCollapsed">Mitra</span>
            </a>

            <!-- TRANSAKSI DROPDOWN -->
            <div x-data="{ open: {{ request()->routeIs('admin.transactions.*') ? 'true' : 'false' }} }">

                <button @click="open = !open"
                    class="w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl
                           hover:bg-slate-800 hover:text-white transition">

                    <div class="flex items-center gap-3">
                        <span class="iconify text-xl text-slate-400" data-icon="mdi:swap-horizontal"></span>
                        <span x-show="!sidebarCollapsed">Transaksi</span>
                    </div>

                    <span x-show="!sidebarCollapsed"
                          class="iconify text-base transition-transform"
                          :class="{'rotate-180': open}"
                          data-icon="mdi:chevron-down"></span>
                </button>

                <div x-show="open" x-collapse class="mt-1 space-y-1 pl-6">

                    <a href="{{ route('admin.transactions.in.index') }}"
                       class="block px-4 py-2 rounded-xl transition
                       {{ request()->routeIs('admin.transactions.in.*')
                            ? 'bg-slate-800 text-white'
                            : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        Barang Masuk
                    </a>

                    <a href="{{ route('admin.transactions.out.index') }}"
                       class="block px-4 py-2 rounded-xl transition
                       {{ request()->routeIs('admin.transactions.out.*')
                            ? 'bg-slate-800 text-white'
                            : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        Barang Keluar
                    </a>

                </div>
            </div>

            <!-- FINANCE DROPDOWN -->
            <div x-data="{ openFinance: {{ request()->routeIs('admin.finance.*') ? 'true' : 'false' }} }">

                <button @click="openFinance = !openFinance"
                    class="w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl
                           hover:bg-slate-800 hover:text-white transition">

                    <div class="flex items-center gap-3">
                        <span class="iconify text-xl text-slate-400" data-icon="mdi:finance"></span>
                        <span x-show="!sidebarCollapsed">Finance</span>
                    </div>

                    <span x-show="!sidebarCollapsed"
                          class="iconify text-base transition-transform"
                          :class="{'rotate-180': openFinance}"
                          data-icon="mdi:chevron-down"></span>
                </button>

                <div x-show="openFinance" x-collapse class="mt-1 space-y-1 pl-6">

                    <a href="{{ route('admin.finance.index') }}"
                       class="block px-4 py-2 rounded-xl hover:bg-slate-800 hover:text-white transition">
                        Laporan
                    </a>
                    <a href="{{ route('admin.finance.daily') }}"
                       class="block px-4 py-2 rounded-xl hover:bg-slate-800 hover:text-white transition">
                        Harian
                    </a>
                    <a href="{{ route('admin.finance.weekly') }}"
                       class="block px-4 py-2 rounded-xl hover:bg-slate-800 hover:text-white transition">
                        Mingguan
                    </a>
                    <a href="{{ route('admin.finance.monthly') }}"
                       class="block px-4 py-2 rounded-xl hover:bg-slate-800 hover:text-white transition">
                        Bulanan
                    </a>

                </div>
            </div>

            <!-- User -->
            <a href="{{ route('admin.users.index') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition hover:bg-slate-800 hover:text-white">
                <span class="iconify text-xl text-slate-400" data-icon="mdi:account-multiple-outline"></span>
                <span x-show="!sidebarCollapsed">User</span>
            </a>

            <!-- Profil -->
            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition hover:bg-slate-800 hover:text-white">
                <span class="iconify text-xl text-slate-400" data-icon="mdi:account-circle-outline"></span>
                <span x-show="!sidebarCollapsed">Profil</span>
            </a>

        </nav>

        <div class="border-t border-slate-800 p-4 space-y-4">

  

    <!-- Credit -->
    <div x-show="!sidebarCollapsed" class="text-[11px] text-slate-500 leading-relaxed">
        <p>Built by</p>
        <p class="text-slate-400 font-medium">
            Anoadev
        </p>
        <p class="text-slate-600">
            CV. Anoa Nusantara Solusi
        </p>
    </div>

</div>


    </aside>

    <!-- MAIN -->
    <div
        class="h-screen overflow-y-auto transition-all duration-300"
        :class="sidebarCollapsed ? 'md:ml-20' : 'md:ml-64'"
    >

        <!-- TOPBAR -->
        <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-20">
    <div class="flex items-center justify-between px-8 py-4">

        <!-- LEFT -->
        <div class="flex items-center gap-4">

            <button @click="sidebarOpen = true" class="md:hidden p-2 rounded hover:bg-slate-100">
                <span class="iconify text-2xl" data-icon="mdi:menu"></span>
            </button>

            <button @click="sidebarCollapsed = !sidebarCollapsed"
                    class="hidden md:inline-flex p-2 rounded hover:bg-slate-100">
                <span class="iconify text-xl" data-icon="mdi:menu-open"></span>
            </button>

            <h2 class="text-xl font-semibold tracking-tight">
                @yield('title', 'Dashboard')
            </h2>

        </div>


        <!-- CENTER SEARCH -->
        <div class="hidden md:flex flex-1 justify-center px-8">
            <div class="relative w-full max-w-md">
                <span class="iconify absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"
                      data-icon="mdi:magnify"></span>
                <input type="text"
                       placeholder="Cari produk, transaksi..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl
                              bg-slate-100 border border-slate-200
                              focus:outline-none focus:ring-2 focus:ring-indigo-500
                              text-sm transition">
            </div>
        </div>


        <!-- RIGHT -->
        <div class="flex items-center gap-6">

            <!-- Location -->
            <div class="hidden lg:flex items-center gap-2 text-sm text-slate-600">
                <span class="iconify text-lg text-indigo-500" data-icon="mdi:map-marker"></span>
                <span>Kolaka</span>
            </div>

            <!-- Notification -->
            <div class="relative">
                <button class="p-2 rounded-xl hover:bg-slate-100 transition relative">
                    <span class="iconify text-xl text-slate-600" data-icon="mdi:bell-outline"></span>

                    <!-- Badge -->
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px]
                                 w-5 h-5 flex items-center justify-center rounded-full">
                        3
                    </span>
                </button>
            </div>


            <!-- PROFILE DROPDOWN -->
            <div x-data="{ openProfile:false }" class="relative">

                <button @click="openProfile = !openProfile"
                        class="flex items-center gap-3 p-1.5 rounded-xl hover:bg-slate-100 transition">

                    <img
                        src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}"
                        class="w-9 h-9 rounded-xl"
                        alt="User Avatar"
                    >

                    <div class="hidden sm:block text-left">
                        <p class="text-sm font-medium leading-tight">
                            {{ Auth::user()->name }}
                        </p>
                        <p class="text-xs text-slate-500">
                            Administrator
                        </p>
                    </div>

                    <span class="iconify text-base text-slate-500"
                          data-icon="mdi:chevron-down"></span>
                </button>


                <!-- DROPDOWN -->
                <div x-show="openProfile"
                     @click.away="openProfile=false"
                     x-transition
                     class="absolute right-0 mt-3 w-56 bg-white border border-slate-200
                            rounded-2xl shadow-xl py-2 text-sm z-50">

                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-100 transition">
                        <span class="iconify text-lg text-slate-500"
                              data-icon="mdi:account-circle-outline"></span>
                        Profil
                    </a>

                    <div class="border-t my-2"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center gap-3 px-4 py-2.5
                                       text-red-500 hover:bg-red-50 transition">
                            <span class="iconify text-lg"
                                  data-icon="mdi:logout-variant"></span>
                            Logout
                        </button>
                    </form>

                </div>

            </div>

        </div>

    </div>
</header>


        <main class="p-8 bg-slate-100 min-h-full">
            @yield('content')
        </main>

    </div>

</div>

</body>
</html>
