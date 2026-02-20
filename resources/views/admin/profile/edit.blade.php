@extends('layouts.admin')

@section('content')
<div class="min-h-screen pb-12">
    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-800">Pengaturan Profil</h1>
        <p class="text-slate-500 mt-2">Kelola informasi akun, keamanan, dan preferensi Anda.</p>
    </div>

    @if (session('status'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="font-medium">
            @if (session('status') === 'profile-updated')
            Profil berhasil diperbarui.
            @elseif (session('status') === 'password-updated')
            Password berhasil diperbarui.
            @else
            {{ session('status') }}
            @endif
        </span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar / Summary Card -->
        <div class="space-y-6">
            <div
                class="bg-white rounded-[2rem] shadow-sm shadow-indigo-100/50 border border-slate-100 p-8 text-center transition-all hover:shadow-md">
                <div x-data="avatarPreview()" class="relative mx-auto w-32 h-32 mb-6 group">
                    <img :src="avatarUrl"
                        class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-xl ring-1 ring-slate-100"
                        alt="Avatar">

                    <label
                        class="absolute bottom-1 right-1 bg-indigo-600 text-white p-2.5 rounded-full cursor-pointer shadow-lg hover:bg-indigo-700 hover:scale-110 transition-all group-hover:block border-2 border-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <input type="file" class="hidden" @change="updatePreview">
                    </label>
                </div>

                <h3 class="text-xl font-bold text-slate-800">{{ $user->name }}</h3>
                <p class="text-slate-500 text-sm mb-6">{{ $user->email }}</p>

                <div
                    class="flex items-center justify-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-700 rounded-full text-xs font-bold uppercase tracking-wider inline-block">
                    <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                    {{ $user->role ?? 'Staff' }}
                </div>

                <div class="mt-8 pt-8 border-t border-slate-100 grid grid-cols-2 gap-4">
                    <div class="text-left">
                        <p class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">Bergabung</p>
                        <p class="text-sm font-semibold text-slate-700">{{ $user->created_at->format('d M Y') }}</p>
                    </div>
                    <div class="text-left">
                        <p class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">Status</p>
                        <p class="text-sm font-semibold text-emerald-600">Aktif</p>
                    </div>
                </div>
            </div>

            <!-- Quick Info -->
            <div class="bg-indigo-600 rounded-[2rem] p-8 text-white relative overflow-hidden group">
                <div
                    class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all">
                </div>
                <h4 class="text-lg font-bold mb-2">Bantuan?</h4>
                <p class="text-indigo-100 text-sm leading-relaxed mb-6">Jika Anda mengalami kendala pada akun Anda,
                    silakan hubungi tim IT Administrator.</p>
                <a href="#"
                    class="inline-flex items-center gap-2 text-sm font-bold bg-white text-indigo-600 px-6 py-3 rounded-2xl hover:bg-indigo-50 transition-colors">
                    Hubungi Support
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Forms Column -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Profile Info Form -->
            <div class="bg-white rounded-[2rem] shadow-sm shadow-indigo-100/50 border border-slate-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-50 flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-800">Informasi Pribadi</h3>
                        <p class="text-sm text-slate-400 font-medium">Perbarui informasi dasar akun Anda.</p>
                    </div>
                </div>

                <form method="post" action="{{ route('profile.update') }}" class="p-8">
                    @csrf
                    @method('patch')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <x-input-label for="name" value="Nama Lengkap" class="text-slate-700 font-bold ml-1" />
                            <x-text-input id="name" name="name" type="text"
                                class="w-full rounded-2xl! border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4"
                                :value="old('name', $user->name)" required autofocus autocomplete="name" />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div class="space-y-2">
                            <x-input-label for="email" value="Email" class="text-slate-700 font-bold ml-1" />
                            <x-text-input id="email" name="email" type="email"
                                class="w-full rounded-2xl! border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4"
                                :value="old('email', $user->email)" required autocomplete="username" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="submit"
                            class="bg-indigo-600 text-white px-8 py-4 rounded-2xl font-bold hover:bg-indigo-700 transition-all flex items-center gap-2 shadow-lg shadow-indigo-200">
                            Simpan Perubahan
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Password Update Form -->
            <div class="bg-white rounded-[2rem] shadow-sm shadow-indigo-100/50 border border-slate-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-50 flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-800">Keamanan</h3>
                        <p class="text-sm text-slate-400 font-medium">Ubah password secara berkala untuk menjaga
                            keamanan.</p>
                    </div>
                </div>

                <form method="post" action="{{ route('password.update') }}" class="p-8">
                    @csrf
                    @method('put')

                    <div class="space-y-6">
                        <div class="space-y-2">
                            <x-input-label for="update_password_current_password" value="Password Saat Ini"
                                class="text-slate-700 font-bold ml-1" />
                            <x-text-input id="update_password_current_password" name="current_password" type="password"
                                class="w-full !rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4"
                                autocomplete="current-password" />
                            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <x-input-label for="update_password_password" value="Password Baru"
                                    class="text-slate-700 font-bold ml-1" />
                                <x-text-input id="update_password_password" name="password" type="password"
                                    class="w-full !rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4"
                                    autocomplete="new-password" />
                                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                            </div>

                            <div class="space-y-2">
                                <x-input-label for="update_password_password_confirmation"
                                    value="Konfirmasi Password Baru" class="text-slate-700 font-bold ml-1" />
                                <x-text-input id="update_password_password_confirmation" name="password_confirmation"
                                    type="password"
                                    class="w-full !rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4"
                                    autocomplete="new-password" />
                                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')"
                                    class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="submit"
                            class="bg-amber-500 text-white px-8 py-4 rounded-2xl font-bold hover:bg-amber-600 transition-all flex items-center gap-2 shadow-lg shadow-amber-100">
                            Perbarui Password
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17.024V19a1 1 0 01-1 1h-1v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-1m4-8V7a4 4 0 118 0v4h.5" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Danger Zone -->
            <div x-data="{ open: false }" class="bg-rose-50 rounded-[2rem] border border-rose-100 overflow-hidden">
                <div class="px-8 py-6 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-rose-600 shadow-sm border border-rose-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-rose-900">Hapus Akun</h3>
                            <p class="text-sm text-rose-500 font-medium">Tindakan ini tidak dapat dibatalkan.</p>
                        </div>
                    </div>
                    <button @click="open = true"
                        class="bg-white text-rose-600 border border-rose-200 px-6 py-3 rounded-2xl font-bold hover:bg-rose-100 transition-all shadow-sm">
                        Hapus Akun
                    </button>
                </div>

                <!-- Delete Account Modal -->
                <template x-if="open">
                    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
                        aria-modal="true">
                        <div
                            class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div x-cloak @click="open = false"
                                class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
                                aria-hidden="true">&#8203;</span>

                            <div x-cloak
                                class="inline-block align-bottom bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full p-8 border border-slate-100">

                                <div class="mb-6">
                                    <div
                                        class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-600 mb-6 mx-auto">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-2xl font-bold text-slate-800 text-center mb-2">Konfirmasi Hapus Akun
                                    </h3>
                                    <p class="text-slate-500 text-center px-4">Setelah akun Anda dihapus, semua sumber
                                        daya dan datanya akan dihapus secara permanen.</p>
                                </div>

                                <form method="post" action="{{ route('profile.destroy') }}">
                                    @csrf
                                    @method('delete')

                                    <div class="space-y-4">
                                        <x-input-label for="password" value="Password Konfirmasi" class="sr-only" />
                                        <x-text-input id="password" name="password" type="password"
                                            class="w-full !rounded-2xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 shadow-sm"
                                            placeholder="Masukkan password Anda untuk konfirmasi" />
                                        <x-input-error :messages="$errors->userDeletion->get('password')"
                                            class="mt-2" />
                                    </div>

                                    <div class="mt-8 flex flex-col sm:flex-row gap-3">
                                        <button type="button" @click="open = false"
                                            class="flex-1 px-8 py-4 bg-slate-100 text-slate-600 rounded-2xl font-bold hover:bg-slate-200 transition-all order-2 sm:order-1">
                                            Batal
                                        </button>
                                        <button type="submit"
                                            class="flex-1 px-8 py-4 bg-rose-600 text-white rounded-2xl font-bold hover:bg-rose-700 transition-all flex items-center justify-center gap-2 shadow-lg shadow-rose-200 order-1 sm:order-2">
                                            Ya, Hapus Akun
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('turbo:load', function() {
        if (!window.avatarPreview) {
            window.avatarPreview = function() {
                return {
                    avatarUrl: 'https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=6366f1&color=fff&bold=true&size=256',
                    updatePreview(event) {
                        const file = event.target.files[0];
                        if (file) {
                            this.avatarUrl = URL.createObjectURL(file);
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection
