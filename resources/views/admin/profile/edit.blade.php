@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow">
    <h2 class="text-2xl font-bold mb-6">👤 Profil Saya</h2>

    @if (session('status') === 'profile-updated')
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            Profil berhasil diperbarui.
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" class="space-y-5">
        @csrf
        @method('PATCH')

        <!-- Nama -->
        <div>
            <label class="block font-semibold mb-1">Nama</label>
            <input type="text" name="name"
                   class="w-full border rounded-lg px-4 py-2 focus:ring focus:ring-blue-300"
                   value="{{ old('name', $user->name) }}" required>
            @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Email -->
        <div>
            <label class="block font-semibold mb-1">Email</label>
            <input type="email" name="email"
                   class="w-full border rounded-lg px-4 py-2 focus:ring focus:ring-blue-300"
                   value="{{ old('email', $user->email) }}" required>
            @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-3 pt-4">
            <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                💾 Simpan Perubahan
            </button>
            <a href="{{ route('dashboard') }}"
               class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">
                ↩ Kembali
            </a>
        </div>
    </form>

    <hr class="my-8">

    <!-- Form Hapus Akun -->
    <form action="{{ route('profile.destroy') }}" method="POST">
        @csrf
        @method('DELETE')

        <h3 class="text-lg font-semibold mb-3 text-red-600">⚠ Hapus Akun</h3>

        <p class="text-sm text-gray-600 mb-3">
            Tindakan ini akan menghapus akun Anda secara permanen.
        </p>

        <div class="mb-3">
            <label class="block font-semibold mb-1">Konfirmasi Password</label>
            <input type="password" name="password"
                   class="w-full border rounded-lg px-4 py-2 focus:ring focus:ring-red-300" required>
            @error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit"
            onclick="return confirm('Yakin ingin menghapus akun?')"
            class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
            🗑 Hapus Akun
        </button>
    </form>
</div>
@endsection
