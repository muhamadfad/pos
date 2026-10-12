<x-guest-layout>
    <div class="mb-4">
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Buat Akun</h2>
        <p class="text-sm text-gray-500 mt-1">Daftar sekarang untuk mulai mengelola bangunan Anda.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" class="font-semibold text-gray-700" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <span class="iconify" data-icon="mdi:account-outline" data-width="18"></span>
                </div>
                <x-text-input id="name"
                    class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 border-gray-200 focus:border-gray-200 focus:ring-[#1677ff] transition-all duration-200 rounded-md"
                    type="text" name="name" :value="old('name')" required autofocus autocomplete="name"
                    placeholder="Masukkan nama lengkap" />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="font-semibold text-gray-700" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <span class="iconify" data-icon="mdi:email-outline" data-width="18"></span>
                </div>
                <x-text-input id="email"
                    class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 border-gray-200 focus:border-gray-200 focus:ring-[#1677ff] transition-all duration-200 rounded-md"
                    type="email" name="email" :value="old('email')" required autocomplete="username"
                    placeholder="nama@email.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="font-semibold text-gray-700" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <span class="iconify" data-icon="mdi:lock-outline" data-width="18"></span>
                </div>
                <x-text-input id="password"
                    class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 border-gray-200 focus:border-gray-200 focus:ring-[#1677ff] transition-all duration-200 rounded-md"
                    type="password" name="password" required autocomplete="new-password"
                    placeholder="Minimal 8 karakter" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')"
                class="font-semibold text-gray-700" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <span class="iconify" data-icon="mdi:lock-check-outline" data-width="18"></span>
                </div>
                <x-text-input id="password_confirmation"
                    class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 border-gray-200 focus:border-gray-200 focus:ring-[#1677ff] transition-all duration-200 rounded-md"
                    type="password" name="password_confirmation" required autocomplete="new-password"
                    placeholder="Ulangi password" />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <div class="pt-4 flex flex-col gap-4">
            <button type="submit"
                class="w-full inline-flex justify-center items-center px-4 py-3 bg-black border border-transparent rounded-md font-bold text-white uppercase tracking-widest hover:bg-gray-800 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-[#1677ff] focus:ring-offset-2 transition-all duration-200 shadow-sm">
                <span class="iconify me-2" data-icon="mdi:account-plus" data-width="20"></span>
                {{ __('Daftar Sekarang') }}
            </button>

            <a class="text-sm text-center text-gray-600 hover:text-black font-medium" href="{{ route('login') }}">
                {{ __('Sudah punya akun? Masuk saja') }}
            </a>
        </div>
    </form>
</x-guest-layout>
