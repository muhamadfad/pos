<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-slate-800">Atur Ulang Password</h2>
        <p class="text-slate-500 text-sm mt-1">Silakan masukkan password baru Anda</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="font-semibold text-gray-700" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <span class="iconify" data-icon="mdi:email-outline" data-width="18"></span>
                </div>
                <x-text-input id="email"
                    class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200 rounded-xl pointer-events-none opacity-70"
                    type="email" name="email" :value="old('email', $request->email)" required autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password Baru')" class="font-semibold text-gray-700" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <span class="iconify" data-icon="mdi:lock-outline" data-width="18"></span>
                </div>
                <x-text-input id="password"
                    class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200 rounded-xl"
                    type="password" name="password" required placeholder="••••••••" autocomplete="new-password" />
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
                    class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200 rounded-xl"
                    type="password" name="password_confirmation" placeholder="••••••••" required
                    autocomplete="new-password" />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <div class="pt-2">
            <button type="submit"
                class="w-full inline-flex justify-center items-center px-4 py-3 bg-indigo-600 border border-transparent rounded-xl font-bold text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 shadow-lg shadow-indigo-100">
                {{ __('Reset Password') }}
            </button>
        </div>
    </form>
</x-guest-layout>
