<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-50 text-black rounded-md mb-4">
            <span class="iconify" data-icon="mdi:email-check-outline" data-width="32"></span>
        </div>
        <h2 class="text-2xl font-bold text-black">Verifikasi Email</h2>
        <p class="text-gray-500 text-sm mt-2 leading-relaxed px-4">
            {{ __('Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda dengan mengeklik
            tautan yang baru saja kami kirimkan ke email Anda.') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
    <div class="mb-6 p-4 rounded-md bg-white border border-gray-200 border border-gray-200 flex gap-3 text-sm text-black">
        <span class="iconify text-black shrink-0" data-icon="mdi:check-circle-outline" data-width="20"></span>
        <p>{{ __('Tautan verifikasi baru telah dikirim ke alamat email yang Anda berikan saat pendaftaran.') }}</p>
    </div>
    @endif

    <div class="space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit"
                class="w-full inline-flex justify-center items-center px-4 py-3 bg-black border border-transparent rounded-md font-bold text-white uppercase tracking-widest hover:bg-gray-800 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-[#1677ff] focus:ring-offset-2 transition-all duration-200 shadow-sm">
                {{ __('Kirim Ulang Email Verifikasi') }}
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center">
            @csrf
            <button type="submit"
                class="text-sm font-medium text-gray-500 hover:text-red-600 transition-colors inline-flex items-center gap-1">
                <span class="iconify" data-icon="mdi:logout-variant"></span>
                {{ __('Keluar') }}
            </button>
        </form>
    </div>
</x-guest-layout>
