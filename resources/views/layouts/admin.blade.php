<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Admin Panel') }}</title>

    @vite(['resources/js/app.js', 'resources/css/app.css'])

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <script src="https://code.iconify.design/3/3.1.1/iconify.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background-color: #334155; border-radius: 20px; }
        .sidebar-transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>
</head>

<body class="font-sans antialiased bg-slate-50 text-slate-800">

    <div x-data="{
        sidebarOpen: false,
        sidebarCollapsed: false
    }" class="h-screen flex overflow-hidden">

        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 lg:hidden"></div>

        <aside x-cloak :class="[
            sidebarOpen ? 'translate-x-0' : '-translate-x-full',
            sidebarCollapsed ? 'w-64 lg:w-20' : 'w-64'
        ]" class="fixed inset-y-0 left-0 z-50 bg-[#0f172a] text-slate-400 border-r border-slate-800 shadow-2xl sidebar-transition flex flex-col lg:translate-x-0">

            <div class="h-20 flex items-center px-6 border-b border-slate-800/50 shrink-0">
                <div class="flex items-center gap-3">
                    <span class="iconify text-2xl text-indigo-500" data-icon="mdi:storefront"></span>
                    <div x-show="!sidebarCollapsed" class="flex flex-col">
                        <span class="font-bold text-white text-base tracking-tight leading-none">AnoaPOS</span>
                        <span class="text-[10px] text-slate-500 mt-1">Admin Panel</span>
                    </div>
                </div>
            </div>

            <nav class="flex-1 py-4 px-3 space-y-1 overflow-y-auto sidebar-scroll text-[13px]">
                
                @php
                    $navBase = "flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 group ";
                    $active = "bg-indigo-600/10 text-white font-medium border-l-4 border-indigo-500 rounded-l-none";
                    $inactive = "hover:bg-slate-800 hover:text-white text-slate-400";
                @endphp

                <a href="{{ route('admin.dashboard') }}" class="{{ $navBase }} {{ request()->routeIs('admin.dashboard') ? $active : $inactive }}">
                    <span class="iconify text-xl" data-icon="mdi:view-dashboard-outline"></span>
                    <span x-show="!sidebarCollapsed">Dashboard</span>
                </a>

                <a href="{{ route('pos.shift.index') }}" data-turbo="false" class="{{ $navBase }} {{ request()->routeIs('pos.*') ? $active : $inactive }}">
                    <span class="iconify text-xl" data-icon="mdi:cash-register"></span>
                    <span x-show="!sidebarCollapsed">POS Kasir</span>
                </a>

                <!-- INVENTORY MANAGEMENT GROUP -->
                @php
                    $isSuperAdmin = Auth::user()->role === 'super_admin';
                @endphp

                <div x-data="{ open: {{ request()->routeIs('admin.products.*', 'admin.categories.*', 'admin.ingredients.*', 'admin.recipes.*', 'admin.branch-stock.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full {{ $navBase }} {{ $inactive }} justify-between">
                        <div class="flex items-center gap-3">
                            <span class="iconify text-xl" data-icon="mdi:package-variant"></span>
                            <span x-show="!sidebarCollapsed">Inventory</span>
                        </div>
                        <span x-show="!sidebarCollapsed" class="iconify transition-transform" :class="open ? 'rotate-180' : ''" data-icon="mdi:chevron-down"></span>
                    </button>
                    <div x-show="open && !sidebarCollapsed" x-collapse class="mt-1 space-y-1 pl-10 border-l border-slate-800 ml-6">
                        
                        @if($isSuperAdmin)
                            <!-- Menu khusus Super Admin -->
                            <a href="{{ route('admin.products.index') }}" class="block py-2 hover:text-white transition">Menu</a>
                            <a href="{{ route('admin.categories.index') }}" class="block py-2 hover:text-white transition">Kategori</a>
                            <a href="{{ route('admin.recipes.index') }}" class="block py-2 hover:text-white transition">Resep Menu</a>
                        @endif
                        
                        <!-- Menu untuk semua user (termasuk Super Admin) -->
                        <a href="{{ route('admin.ingredients.index') }}" class="block py-2 hover:text-white transition">Bahan Baku</a>
                        
                    </div>
                </div>

                <a href="/admin/hpp-report" class="{{ $navBase }} {{ request()->routeIs('admin.recipes.*') ? $active : $inactive }}">
                    <span class="iconify text-xl" data-icon="mdi:chart-line"></span>
                    <span x-show="!sidebarCollapsed">HPP & Margin</span>
                </a>

                <a href="{{ route('admin.suppliers.index') }}" class="{{ $navBase }} {{ request()->routeIs('admin.suppliers.*') ? $active : $inactive }}">
                    <span class="iconify text-xl" data-icon="mdi:truck-outline"></span>
                    <span x-show="!sidebarCollapsed">Supplier</span>
                </a>

                <a href="{{ route('admin.customers.index') }}" class="{{ $navBase }} {{ request()->routeIs('admin.customers.*') ? $active : $inactive }}">
                    <span class="iconify text-xl" data-icon="mdi:account-group-outline"></span>
                    <span x-show="!sidebarCollapsed">Customer</span>
                </a>

                <div x-data="{ open: {{ request()->routeIs('admin.purchases.*', 'admin.orders.*', 'admin.purchase-payments.*', 'admin.order-payments.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full {{ $navBase }} {{ $inactive }} justify-between">
                        <div class="flex items-center gap-3">
                            <span class="iconify text-xl" data-icon="mdi:swap-horizontal"></span>
                            <span x-show="!sidebarCollapsed">Transaksi</span>
                        </div>
                        <span x-show="!sidebarCollapsed" class="iconify transition-transform" :class="open ? 'rotate-180' : ''" data-icon="mdi:chevron-down"></span>
                    </button>
                    <div x-show="open && !sidebarCollapsed" x-collapse class="mt-1 space-y-1 pl-10 border-l border-slate-800 ml-6">
                        <a href="{{ route('admin.purchases.index') }}" class="block py-2 hover:text-white transition">Pembelian</a>
                        <a href="{{ route('admin.orders.index') }}" class="block py-2 hover:text-white transition">Penjualan</a>
                        <a href="{{ route('admin.purchase-payments.index') }}" class="block py-2 hover:text-white transition">Hutang</a>
                        <a href="{{ route('admin.order-payments.index') }}" class="block py-2 hover:text-white transition">Piutang</a>
                    </div>
                </div>

                @if($isSuperAdmin)
                <div x-data="{ open: {{ request()->routeIs('admin.central-purchases.*', 'admin.central-stocks.*', 'admin.stock-transfers.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full {{ $navBase }} {{ $inactive }} justify-between text-emerald-400 hover:bg-emerald-900/30">
                        <div class="flex items-center gap-3">
                            <span class="iconify text-xl" data-icon="mdi:warehouse"></span>
                            <span x-show="!sidebarCollapsed">Logistik Pusat</span>
                        </div>
                        <span x-show="!sidebarCollapsed" class="iconify transition-transform" :class="open ? 'rotate-180' : ''" data-icon="mdi:chevron-down"></span>
                    </button>
                    <div x-show="open && !sidebarCollapsed" x-collapse class="mt-1 space-y-1 pl-10 border-l border-emerald-800/50 ml-6">
                        <a href="{{ route('admin.central-purchases.index') }}" class="block py-2 hover:text-white transition">Pembelian Pusat</a>
                        <a href="{{ route('admin.central-stocks.index') }}" class="block py-2 hover:text-white transition">Stok Gudang Pusat</a>
                        <a href="{{ route('admin.stock-transfers.index') }}" class="block py-2 hover:text-white transition">Distribusi Cabang</a>
                    </div>
                </div>
                @endif

                <div x-data="{ open: {{ request()->routeIs('admin.finance.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full {{ $navBase }} {{ $inactive }} justify-between">
                        <div class="flex items-center gap-3">
                            <span class="iconify text-xl" data-icon="mdi:finance"></span>
                            <span x-show="!sidebarCollapsed">Finance</span>
                        </div>
                        <span x-show="!sidebarCollapsed" class="iconify transition-transform" :class="open ? 'rotate-180' : ''" data-icon="mdi:chevron-down"></span>
                    </button>
                    <div x-show="open && !sidebarCollapsed" x-collapse class="mt-1 space-y-1 pl-10 border-l border-slate-800 ml-6">
                        <a href="{{ route('admin.finance.index') }}" class="block py-2 hover:text-white transition">Laporan</a>
                        <a href="{{ route('admin.finance.daily') }}" class="block py-2 hover:text-white transition">Harian</a>
                        <a href="{{ route('admin.finance.weekly') }}" class="block py-2 hover:text-white transition">Mingguan</a>
                        <a href="{{ route('admin.finance.monthly') }}" class="block py-2 hover:text-white transition">Bulanan</a>
                    </div>
                </div>

                <a href="{{ route('admin.expenses.index') }}" class="{{ $navBase }} {{ request()->routeIs('admin.expenses.*') ? $active : $inactive }}">
                    <span class="iconify text-xl" data-icon="mdi:cash-minus"></span>
                    <span x-show="!sidebarCollapsed">Pengeluaran</span>
                </a>

                <div x-data="{ open: {{ request()->routeIs('kaspro.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full {{ $navBase }} {{ $inactive }} justify-between">
                        <div class="flex items-center gap-3">
                            <span class="iconify text-xl" data-icon="mdi:chart-line"></span>
                            <span x-show="!sidebarCollapsed">KasPro</span>
                        </div>
                        <span x-show="!sidebarCollapsed" class="iconify transition-transform" :class="open ? 'rotate-180' : ''" data-icon="mdi:chevron-down"></span>
                    </button>
                    <div x-show="open && !sidebarCollapsed" x-collapse class="mt-1 space-y-1 pl-10 border-l border-slate-800 ml-6">
                        <a href="{{ route('kaspro.dashboard') }}" class="block py-2 hover:text-white transition text-[12px]">Dashboard KasPro</a>
                        <a href="{{ route('kaspro.prediction') }}" class="block py-2 hover:text-white transition text-[12px]">Prediksi Arus Kas</a>
                        <a href="{{ route('kaspro.alerts') }}" class="flex items-center justify-between py-2 hover:text-white transition text-[12px]">
                            <span>Peringatan</span>
                            @php
                                $unread = \App\Models\KasproAlert::where('user_id', Auth::id())->where('is_read', false)->count();
                            @endphp
                            @if($unread > 0)
                                <span class="bg-red-500 text-white text-[10px] px-1.5 rounded-full mr-4">{{ $unread }}</span>
                            @endif
                        </a>
                        <a href="{{ route('kaspro.margin-analysis') }}" class="block py-2 hover:text-white transition text-[12px]">Analisis Margin</a>
                    </div>
                </div>

                @if($isSuperAdmin)
                <a href="{{ route('super.branches.index') }}" class="{{ $navBase }} {{ request()->routeIs('super.branches.*') ? $active : $inactive }}">
                    <span class="iconify text-xl" data-icon="mdi:store-cog-outline"></span>
                    <span x-show="!sidebarCollapsed">Manajemen Cabang</span>
                </a>
                @endif

                <a href="{{ route('admin.users.index') }}" class="{{ $navBase }} {{ request()->routeIs('admin.users.index') ? $active : $inactive }}">
                    <span class="iconify text-xl" data-icon="mdi:account-multiple-outline"></span>
                    <span x-show="!sidebarCollapsed">User</span>
                </a>

                <a href="{{ route('profile.edit') }}" class="{{ $navBase }} {{ request()->routeIs('profile.edit') ? $active : $inactive }}">
                    <span class="iconify text-xl" data-icon="mdi:account-circle-outline"></span>
                    <span x-show="!sidebarCollapsed">Profil</span>
                </a>

            </nav>

            <div class="p-4 border-t border-slate-800 shrink-0">
                <div x-show="!sidebarCollapsed" class="text-[11px] text-slate-500">
                    <p>Built by <span class="text-white">Anoadev</span></p>
                    <p>CV. Anoa Nusantara Solusi</p>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0 h-screen sidebar-transition"
             :class="sidebarCollapsed ? 'lg:ml-20' : 'lg:ml-64'">

            <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 z-30 flex items-center justify-between px-6 shrink-0">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg hover:bg-slate-100">
                        <span class="iconify text-2xl" data-icon="mdi:menu"></span>
                    </button>
                    <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden lg:block p-2 rounded-lg hover:bg-slate-100">
                        <span class="iconify text-2xl" :data-icon="sidebarCollapsed ? 'mdi:menu' : 'mdi:menu-open'"></span>
                    </button>
                    <h2 class="text-xl font-semibold tracking-tight text-slate-800">@yield('title', 'Dashboard')</h2>
                </div>

                <div class="flex items-center gap-4 sm:gap-6">
                    <div class="hidden md:relative md:block">
                        <span class="iconify absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" data-icon="mdi:magnify"></span>
                        <input type="text" placeholder="Cari..." class="w-64 pl-10 pr-4 py-2 bg-slate-100 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20">
                    </div>

                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.away="open = false" class="flex items-center gap-3 p-1 rounded-xl hover:bg-slate-100 transition">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=6366f1&color=fff" class="w-10 h-10 rounded-xl shadow-sm" alt="Avatar">
                            <div class="hidden sm:block text-left">
                                <p class="text-sm font-bold leading-none mb-1">{{ Auth::user()->name }}</p>
                                <p class="text-[11px] text-slate-500 leading-none">Administrator</p>
                            </div>
                            <span class="iconify text-slate-400" data-icon="mdi:chevron-down"></span>
                        </button>
                        <div x-show="open" x-transition class="absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-2xl shadow-xl py-2 z-50">
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2 hover:bg-slate-50 text-sm">
                                <span class="iconify text-lg" data-icon="mdi:account-outline"></span> Profil
                            </a>
                            <div class="border-t my-2 border-slate-100"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-red-500 hover:bg-red-50 text-sm font-medium">
                                    <span class="iconify text-lg" data-icon="mdi:logout"></span> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6 bg-slate-50">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 z-[999] flex items-center justify-center p-4" style="display:none">
        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-sm p-6 text-center space-y-4">
            <div class="w-16 h-16 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto">
                <span class="iconify text-3xl" data-icon="mdi:trash-can-outline"></span>
            </div>
            <h3 class="text-lg font-bold text-slate-800">Hapus Data?</h3>
            <p class="text-sm text-slate-500">Yakin ingin menghapus <span id="deleteModalName" class="font-bold text-slate-800"></span>?</p>
            <div class="flex gap-3">
                <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 text-sm font-bold text-slate-500 bg-slate-100 rounded-xl hover:bg-slate-200 transition">Batal</button>
                <form id="deleteModalForm" method="POST" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2.5 text-sm font-bold text-white bg-red-600 rounded-xl hover:bg-red-700 shadow-lg shadow-red-200 transition">Hapus</button>
                </form>
            </div>
        </div>
    </div>

    @stack('scripts')
    <script>
        function confirmDelete(url, name) {
            document.getElementById('deleteModalName').textContent = name ?? 'item ini';
            document.getElementById('deleteModalForm').action = url;
            document.getElementById('deleteModal').style.display = 'flex';
        }
        function closeDeleteModal() { document.getElementById('deleteModal').style.display = 'none'; }
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeDeleteModal(); });
    </script>
</body>
</html>