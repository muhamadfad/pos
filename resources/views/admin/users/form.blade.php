@extends('layouts.admin')

@section('title', isset($user) ? 'Edit User' : 'Tambah User')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header -->
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">
            {{ isset($user) ? 'Edit User' : 'Tambah User' }}
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            {{ isset($user) ? 'Perbarui informasi pengguna.' : 'Tambahkan pengguna baru ke sistem.' }}
        </p>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-sm border p-8">

        <!-- Error -->
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}"
              method="POST"
              class="space-y-6">
            @csrf
            @if(isset($user))
                @method('PUT')
            @endif

            <div class="grid md:grid-cols-2 gap-6">

                <!-- Nama -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama
                    </label>
                    <input type="text"
                           name="name"
                           value="{{ old('name', $user->name ?? '') }}"
                           required
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5
                                  focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                  transition text-sm">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Email
                    </label>
                    <input type="email"
                           name="email"
                           value="{{ old('email', $user->email ?? '') }}"
                           required
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5
                                  focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                  transition text-sm">
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <input type="password"
                           name="password"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5
                                  focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                  transition text-sm"
                           {{ isset($user) ? '' : 'required' }}>

                    @if(isset($user))
                        <p class="text-xs text-gray-500 mt-2">
                            Kosongkan jika tidak ingin mengubah password.
                        </p>
                    @endif
                </div>

                <!-- Role -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Role
                    </label>
                    <select name="role"
                            required
                            class="w-full border border-gray-300 rounded-xl px-4 py-2.5
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                   transition text-sm">
                        <option value="">Pilih Role</option>
                        <option value="admin"
                            {{ old('role', $user->role ?? '') == 'admin' ? 'selected' : '' }}>
                            Admin
                        </option>
                        <option value="user"
                            {{ old('role', $user->role ?? '') == 'user' ? 'selected' : '' }}>
                            User
                        </option>
                    </select>
                </div>

            </div>

            <!-- Footer -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t">

                <a href="{{ route('admin.users.index') }}"
                   class="px-5 py-2.5 rounded-xl bg-gray-100 text-gray-700 text-sm font-medium
                          hover:bg-gray-200 transition">
                    Kembali
                </a>

                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-medium
                               hover:bg-blue-700 transition shadow-sm">
                    {{ isset($user) ? 'Update User' : 'Simpan User' }}
                </button>

            </div>

        </form>

    </div>

</div>
@endsection
