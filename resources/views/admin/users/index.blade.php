@extends('layouts.admin')

@section('title', 'Manajemen User')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm border">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">👤 Manajemen User</h2>
        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            + Tambah
        </a>
    </div>

    <!-- Alert -->
    @if(session('success'))
        <div class="mb-4 bg-green-100 text-green-700 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left border rounded-lg overflow-hidden">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $user->name }}</td>
                    <td class="px-4 py-3">{{ $user->email }}</td>
                    <td class="px-4 py-3 capitalize">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                            {{ $user->role === 'admin' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center space-x-2">
                        <a href="{{ route('admin.users.edit', $user) }}"
                           class="text-blue-600 hover:underline text-sm">
                            Edit
                        </a>

                        <form action="{{ route('admin.users.destroy', $user) }}"
                              method="POST" class="inline"
                              onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-red-600 hover:underline text-sm">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                        Data user belum tersedia.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
