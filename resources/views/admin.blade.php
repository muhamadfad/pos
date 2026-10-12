{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    @vite(['resources/js/app.js'])
</head>

<body class="bg-gray-100 flex">

    {{-- Sidebar --}}
    <aside class="w-64 bg-gray-50 shadow min-h-screen p-4">
        <h1 class="font-bold text-xl mb-6">ADMIN</h1>
        <nav class="space-y-2">
            <a href="{{ route('admin.dashboard') }}" class="block p-2 rounded hover:bg-gray-100">Dashboard</a>
            <a href="/admin/products" class="block p-2 rounded hover:bg-gray-100">Barang</a>
            <a href="/admin/categories" class="block p-2 rounded hover:bg-gray-100">Kategori</a>
            <a href="/admin/units" class="block p-2 rounded hover:bg-gray-100">Satuan</a>
            <a href="/admin/users" class="block p-2 rounded hover:bg-gray-100">User</a>
        </nav>
    </aside>

    {{-- Content --}}
    <main class="flex-1 p-6">
        @yield('content')
    </main>

</body>

</html>
