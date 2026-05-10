<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'RM. Coto Makassar') }} - Point of Sale</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>

<body class="antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-slate-50 via-white to-indigo-50/50 relative overflow-hidden">
        
        <!-- Decorative blobs -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute -top-[30%] -left-[20%] w-[60%] h-[60%] bg-indigo-200/30 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-[30%] -right-[20%] w-[60%] h-[60%] bg-purple-200/30 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[80%] h-[80%] bg-indigo-100/20 rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-10">
            <a href="/" class="flex flex-col items-center gap-3 group transition-all duration-300 hover:scale-105">
                <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-2xl shadow-lg flex items-center justify-center">
                    <span class="iconify text-white text-3xl" data-icon="mdi:storefront"></span>
                </div>
                <div class="text-center">
                    <h1 class="text-xl font-black text-slate-800 tracking-tight">RM. Coto Makassar</h1>
                    <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider mt-0.5">Selera Rakyat</p>
                </div>
            </a>
        </div>

        <div class="relative z-10 w-full sm:max-w-md mt-8 px-8 py-8 bg-white/90 backdrop-blur-xl border border-white/60 shadow-xl sm:rounded-2xl">
            {{ $slot }}
        </div>

        <p class="relative z-10 mt-8 text-xs text-slate-400 font-medium">
            &copy; {{ date('Y') }} {{ config('app.name', 'RM. Coto Makassar') }}
        </p>
    </div>
    
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
</body>

</html>