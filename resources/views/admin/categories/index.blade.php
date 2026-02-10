@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-gray-800">📦 Daftar Kategori</h2>
        <a href="{{ route('admin.categories.create') }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg
                  hover:bg-blue-700 transition">
            ➕ Tambah Kategori
        </a>
    </div>

    <!-- Alert -->
    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">

        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left">#</th>
                    <th class="px-6 py-3 text-left">Nama Kategori</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($categories as $category)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 font-medium text-gray-800">
                            {{ $category->name }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('admin.categories.edit', $category->id) }}"
                                   class="inline-flex items-center px-3 py-1.5 rounded-md
                                          bg-yellow-500 text-white hover:bg-yellow-600 transition text-xs">
                                    ✏️ Edit
                                </a>

                                <form action="{{ route('admin.categories.destroy', $category->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center px-3 py-1.5 rounded-md
                                               bg-red-600 text-white hover:bg-red-700 transition text-xs">
                                        🗑️ Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-8 text-gray-500">
                            Belum ada kategori.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>
@endsection
