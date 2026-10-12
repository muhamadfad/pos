<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-white text-black rounded-md mb-4">
            <span class="iconify" data-icon="mdi:lock-alert-outline" data-width="32"></span>
        </div>
        <h2 class="text-2xl font-bold text-black">Konfirmasi Password</h2>
        <p class="text-gray-500 text-sm mt-1 leading-relaxed px-4">
            {{ __('Ini adalah area aplikasi yang aman. Silakan konfirmasi password Anda sebelum melanjutkan.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="font-semibold text-gray-700" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <span class="iconify" data-icon="mdi:lock-outline" data-width="18"></span>
                </div>
                <x-text-input id="password"
                    class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 border-gray-200 focus:border-gray-200 focus:ring-[#1677ff] transition-all duration-200 rounded-md"
                    type="password" name="password" required placeholder="••••••••" autocomplete="current-password" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div class="pt-2">
            <button type="submit"
                class="w-full inline-flex justify-center items-center px-4 py-3 bg-black border border-transparent rounded-md font-bold text-white uppercase tracking-widest hover:bg-gray-800 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-[#1677ff] focus:ring-offset-2 transition-all duration-200 shadow-sm">
                {{ __('Konfirmasi') }}
            </button>
        </div>
    </form>
</x-guest-layout>
