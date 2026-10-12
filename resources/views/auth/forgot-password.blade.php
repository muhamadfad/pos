<x-guest-layout>
    <div class="mb-4 text-sm text-gray-500 bg-gray-50 p-4 rounded-md border border-gray-200 flex gap-3">
        <span class="iconify text-black shrink-0" data-icon="mdi:information-outline" data-width="20"></span>
        <p>
            {{ __('Lupa password? Jangan khawatir. Masukkan alamat email Anda dan kami akan mengirimkan tautan untuk
            mengatur ulang password.') }}
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
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
                    type="email" name="email" :value="old('email')" required autofocus placeholder="nama@email.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div class="pt-2">
            <button type="submit"
                class="w-full inline-flex justify-center items-center px-4 py-3 bg-black border border-transparent rounded-md font-bold text-white uppercase tracking-widest hover:bg-gray-800 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-[#1677ff] focus:ring-offset-2 transition-all duration-200 shadow-sm">
                {{ __('Kirim Link Reset') }}
            </button>
        </div>

        <div class="text-center">
            <a href="{{ route('login') }}"
                class="text-sm font-medium text-gray-500 hover:text-black flex items-center justify-center gap-1 transition-colors">
                <span class="iconify" data-icon="mdi:arrow-left"></span>
                Kembali ke Login
            </a>
        </div>
    </form>
</x-guest-layout>
