@extends('layouts.admin')

@section('content')
<div x-data="profilePage()" class="max-w-6xl mx-auto space-y-8">

    <!-- Header -->
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">Profil</h1>
        <p class="text-sm text-gray-500 mt-1">
            Kelola informasi akun dan keamanan Anda.
        </p>
    </div>

    <!-- Toast -->
    <div x-show="showToast"
         x-transition
         class="fixed top-6 right-6 bg-green-600 text-white 
                px-5 py-3 rounded-xl shadow-lg text-sm z-50">
        Profil berhasil diperbarui.
    </div>

    <div class="grid md:grid-cols-3 gap-8">

        <!-- LEFT COLUMN (Avatar Card) -->
        <div class="bg-white rounded-2xl shadow-sm border p-8 text-center space-y-6">

            <div class="relative w-28 h-28 mx-auto">
                <img :src="avatarPreview"
                     class="w-28 h-28 rounded-full object-cover border">

                <label class="absolute bottom-0 right-0 
                              bg-blue-600 text-white p-2 rounded-full 
                              cursor-pointer hover:bg-blue-700 transition">
                    <input type="file" class="hidden" @change="previewAvatar">
                    ✎
                </label>
            </div>

            <div>
                <p class="font-medium text-gray-800">
                    {{ $user->name }}
                </p>
                <p class="text-sm text-gray-500">
                    {{ $user->email }}
                </p>
            </div>

        </div>

        <!-- RIGHT COLUMN -->
        <div class="md:col-span-2 space-y-8">

            <!-- Profile Form -->
            <div class="bg-white rounded-2xl shadow-sm border p-8">

                <h2 class="text-lg font-semibold text-gray-800 mb-6">
                    Informasi Akun
                </h2>

                <form action="{{ route('profile.update') }}"
                      method="POST"
                      class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div class="grid md:grid-cols-2 gap-6">

                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama
                            </label>
                            <input type="text" name="name"
                                   value="{{ old('name', $user->name) }}"
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
                            <input type="email" name="email"
                                   value="{{ old('email', $user->email) }}"
                                   required
                                   class="w-full border border-gray-300 rounded-xl px-4 py-2.5
                                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                          transition text-sm">
                        </div>

                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit"
                                @click="showToast = true; setTimeout(()=>showToast=false,2000)"
                                class="px-6 py-2.5 rounded-xl bg-blue-600 
                                       text-white text-sm font-medium
                                       hover:bg-blue-700 transition shadow-sm">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>

            </div>

            <!-- Danger Zone -->
            <div class="bg-white rounded-2xl shadow-sm border border-red-200 p-8">

                <h2 class="text-lg font-semibold text-red-600 mb-2">
                    Hapus Akun
                </h2>

                <p class="text-sm text-gray-500 mb-6">
                    Semua data akan dihapus secara permanen dan tidak dapat dikembalikan.
                </p>

                <button @click="openModal"
                        class="px-6 py-2.5 rounded-xl bg-red-600 
                               text-white text-sm font-medium
                               hover:bg-red-700 transition shadow-sm">
                    Hapus Akun
                </button>

            </div>

        </div>

    </div>

    <!-- DELETE MODAL -->
    <div x-show="showModal"
         x-transition
         class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">

        <div class="bg-white rounded-2xl p-8 w-96 space-y-4 shadow-xl">

            <h3 class="text-lg font-semibold text-gray-800">
                Konfirmasi Hapus Akun
            </h3>

            <p class="text-sm text-gray-500">
                Masukkan password untuk melanjutkan.
            </p>

            <form action="{{ route('profile.destroy') }}" method="POST" class="space-y-4">
                @csrf
                @method('DELETE')

                <input type="password" name="password" required
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5
                              focus:ring-2 focus:ring-red-500 focus:border-red-500
                              transition text-sm">

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button"
                            @click="showModal=false"
                            class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">
                        Batal
                    </button>

                    <button type="submit"
                            class="px-4 py-2 rounded-lg bg-red-600 
                                   text-white hover:bg-red-700 text-sm">
                        Hapus
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>

<script>
function profilePage() {
    return {
        showModal: false,
        showToast: false,
        avatarPreview: 'https://ui-avatars.com/api/?name={{ urlencode($user->name) }}',

        previewAvatar(event) {
            const file = event.target.files[0];
            if (file) {
                this.avatarPreview = URL.createObjectURL(file);
            }
        },

        openModal() {
            this.showModal = true;
        }
    }
}
</script>
@endsection
