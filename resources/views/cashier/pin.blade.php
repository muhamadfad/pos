<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Lock - {{ config('app.name', 'AnoaPOS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#fafafa] flex items-center justify-center min-h-screen">
    
    <div class="bg-white p-8 rounded-lg border border-gray-200 shadow-sm max-w-sm w-full text-center" x-data="pinPad()">
        <div class="w-12 h-12 bg-gray-50 border border-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="iconify text-xl text-black" data-icon="lucide:lock"></span>
        </div>
        <h2 class="text-xl font-semibold text-black mb-1">POS Terkunci</h2>
        <p class="text-sm text-gray-500 mb-6">Masukkan PIN kasir Anda untuk melanjutkan.</p>

        @if($errors->any())
            <div class="mb-4 text-red-600 text-sm font-medium">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('pos.verify-pin') }}" id="pinForm">
            @csrf
            
            <input type="password" name="pin" x-model="pin" class="w-full text-center text-2xl tracking-[0.5em] font-mono p-3 border border-gray-200 rounded-md focus:border-gray-400 focus:ring-4 focus:ring-gray-100 outline-none mb-6" readonly>
            
            <div class="grid grid-cols-3 gap-3 mb-6">
                <template x-for="i in [1,2,3,4,5,6,7,8,9]">
                    <button type="button" @click="add(i)" class="h-14 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-md text-xl font-semibold text-black transition-colors">
                        <span x-text="i"></span>
                    </button>
                </template>
                <button type="button" @click="clear()" class="h-14 bg-red-50 hover:bg-red-100 border border-red-200 rounded-md text-red-600 flex items-center justify-center transition-colors">
                    <span class="iconify text-xl" data-icon="lucide:delete"></span>
                </button>
                <button type="button" @click="add(0)" class="h-14 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-md text-xl font-semibold text-black transition-colors">
                    0
                </button>
                <button type="submit" class="h-14 bg-black hover:bg-gray-800 rounded-md text-white flex items-center justify-center transition-colors shadow-sm">
                    <span class="iconify text-xl" data-icon="lucide:arrow-right"></span>
                </button>
            </div>
            
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 hover:text-black transition-colors">Kembali ke Dashboard</a>
        </form>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        function pinPad() {
            return {
                pin: '',
                add(num) {
                    if (this.pin.length < 6) {
                        this.pin += num;
                    }
                },
                clear() {
                    this.pin = this.pin.slice(0, -1);
                }
            }
        }
    </script>
</body>
</html>
