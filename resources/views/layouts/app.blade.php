<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    @vite(['resources/js/app.js'])
</head>

<body class="bg-gray-100 font-sans antialiased">
    <div class="flex min-h-screen">

        <!-- Sidebar Left -->
        <aside class="w-64 bg-white border-r shadow-sm hidden md:flex flex-col">
            <div class="h-16 flex items-center px-6 font-semibold text-lg border-b">
                Admin Panel
            </div>

            <nav class="flex-1 py-4 space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center px-6 py-3 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition rounded-r-full">
                    <span class="text-lg">📊</span>
                    <span class="ml-3">Dashboard</span>
                </a>

                <a href="{{ route('admin.products.index') }}"
                    class="flex items-center px-6 py-3 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition rounded-r-full">
                    <span class="text-lg">📦</span>
                    <span class="ml-3">Produk</span>
                </a>

                <a href="{{ route('admin.categories.index') }}"
                    class="flex items-center px-6 py-3 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition rounded-r-full">
                    <span class="text-lg">🗂️</span>
                    <span class="ml-3">Kategori</span>
                </a>

                <a href="{{ route('admin.users.index') }}"
                    class="flex items-center px-6 py-3 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition rounded-r-full">
                    <span class="text-lg">👤</span>
                    <span class="ml-3">User</span>
                </a>

                <a href="{{ route('profile.edit') }}"
                    class="flex items-center px-6 py-3 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition rounded-r-full">
                    <span class="text-lg">⚙️</span>
                    <span class="ml-3">Profil</span>
                </a>
            </nav>

            <div class="border-t p-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition">
                        🚪 <span class="ml-3">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Area -->
        <div class="flex-1 flex flex-col">

            <!-- Topbar -->
            <header class="h-16 bg-white border-b shadow-sm flex items-center justify-between px-6">
                <h1 class="text-lg font-semibold text-gray-800">
                    @yield('title', 'Dashboard')
                </h1>

                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600 hidden sm:block">
                        {{ Auth::user()->name }}
                    </span>
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff"
                        class="w-9 h-9 rounded-full border" alt="User Avatar">
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 p-6 overflow-y-auto">
                @yield('content')
            </main>
        </div>

        <!-- Sidebar Right (Vertical User Info) -->
        <aside class="w-20 bg-white border-l shadow-sm hidden lg:flex flex-col items-center py-6 space-y-6">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=2563EB&color=fff"
                class="w-10 h-10 rounded-full border" alt="User Avatar">

            <div class="writing-mode-vertical-rl transform rotate-180 text-xs text-gray-500 font-medium tracking-wide">
                {{ Auth::user()->email }}
            </div>
        </aside>

    </div>
</body>

</html>
