@extends('layouts.admin')

@section('title', isset($user) ? 'Edit User' : 'Tambah User')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm border max-w-3xl mx-auto">

    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800">
            {{ isset($user) ? '✏ Edit User' : '➕ Tambah User' }}
        </h2>
    </div>

    <!-- Error -->
    @if ($errors->any())
        <div class="mb-4 bg-red-100 text-red-700 px-4 py-3 rounded-lg text-sm">
            {{ implode(', ', $errors->all()) }}
        </div>
    @endif

    <form action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}"
          method="POST" class="space-y-5">
        @csrf
        @if(isset($user))
            @method('PUT')
        @endif

        <!-- Nama -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
            <input type="text" name="name"
                   class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500"
                   value="{{ old('name', $user->name ?? '') }}" required>
            @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Email -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email"
                   class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500"
                   value="{{ old('email', $user->email ?? '') }}" required>
            @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Password -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="password"
                   class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500"
                   {{ isset($user) ? '' : 'required' }}>
            @if(isset($user))
                <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password.</p>
            @endif
            @error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Role -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
            <select name="role"
                    class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500"
                    required>
                <option value="">-- Pilih Role --</option>
                <option value="admin" {{ old('role', $user->role ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="user" {{ old('role', $user->role ?? '') == 'user' ? 'selected' : '' }}>User</option>
            </select>
            @error('role') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Button -->
        <div class="flex items-center gap-3 pt-4">
            <button type="submit"
                class="inline-flex items-center px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                {{ isset($user) ? '💾 Update' : '💾 Simpan' }}
            </button>
            <a href="{{ route('admin.users.index') }}"
               class="inline-flex items-center px-5 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                ↩ Kembali
            </a>
        </div>

    </form>
</div>
@endsection
