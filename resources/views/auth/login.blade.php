<x-guest-layout>
    <div class="mb-4">
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Selamat Datang</h2>
        <p class="text-sm text-gray-500 mt-1">Silakan masuk ke akun Anda untuk melanjutkan.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5" data-turbo="false">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="font-semibold text-gray-700" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <span class="iconify" data-icon="mdi:email-outline" data-width="18"></span>
                </div>
                <x-text-input id="email"
                    class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 border-gray-200 focus:border-gray-200 focus:ring-[#1677ff] transition-all duration-200 rounded-md"
                    type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@email.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Password')" class="font-semibold text-gray-700" />
                @if (Route::has('password.request'))
                <a class="text-xs font-medium text-black hover:text-black"
                    href="{{ route('password.request') }}">
                    {{ __('Lupa password?') }}
                </a>
                @endif
            </div>

            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <span class="iconify" data-icon="mdi:lock-outline" data-width="18"></span>
                </div>
                <x-text-input id="password"
                    class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 border-gray-200 focus:border-gray-200 focus:ring-[#1677ff] transition-all duration-200 rounded-md"
                    type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded-md border-gray-300 text-black shadow-sm focus:ring-[#1677ff] transition-all cursor-pointer"
                    name="remember">
                <span class="ms-2 text-sm text-gray-600 select-none cursor-pointer">{{ __('Ingat saya') }}</span>
            </label>
        </div>

        <div class="pt-2">
            <button type="submit"
                class="w-full inline-flex justify-center items-center px-4 py-3 bg-black border border-transparent rounded-md font-bold text-white uppercase tracking-widest hover:bg-gray-800 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-[#1677ff] focus:ring-offset-2 transition-all duration-200 shadow-sm">
                <span class="iconify me-2" data-icon="mdi:login" data-width="20"></span>
                {{ __('Masuk Sekarang') }}
            </button>
        </div>

        @if (Route::has('register'))
        <p class="text-center text-sm text-gray-500 pt-2">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-bold text-black hover:text-black">
                Daftar di sini
            </a>
        </p>
        @endif
    </form>

    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
</x-guest-layout>
