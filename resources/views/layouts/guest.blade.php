<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>

<body class="font-sans text-gray-900 antialiased selection:bg-indigo-100 selection:text-indigo-900">
    <div
        class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-slate-50 relative overflow-hidden">
        <!-- Background decorative elements -->
        <div class="absolute -top-[10%] -left-[5%] w-[40%] h-[40%] bg-indigo-100/50 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-[10%] -right-[5%] w-[40%] h-[40%] bg-blue-100/50 rounded-full blur-3xl"></div>

        <div class="relative z-10">
            <a href="/"
                class="flex flex-col items-center gap-3 group transition-transform duration-300 hover:scale-105">
                <div
                    class="w-16 h-16 bg-white rounded-2xl shadow-xl flex items-center justify-center border border-slate-100 p-3">
                    <x-application-logo class="w-full h-full fill-current text-indigo-600" />
                </div>
                <h1 class="text-xl font-black text-slate-800 uppercase tracking-tighter">Depo<span
                        class="text-indigo-600">Bangun</span></h1>
            </a>
        </div>

        <div
            class="relative z-10 w-full sm:max-w-md mt-8 px-6 py-10 bg-white/80 backdrop-blur-xl border border-white/50 shadow-[0_20px_50px_rgba(31,38,135,0.1)] overflow-hidden sm:rounded-[2rem]">
            {{ $slot }}
        </div>

        <p class="relative z-10 mt-8 text-sm text-slate-400 font-medium">
            &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
        </p>
    </div>
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
</body>
</body>

</html>
