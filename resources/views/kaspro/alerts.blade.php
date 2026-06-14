@extends('layouts.admin')

@section('title', 'Peringatan KasPro')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Peringatan</h1>
        <p class="text-gray-600 mt-1">Daftar peringatan dari sistem KasPro</p>
    </div>

    <!-- Alert Statistics Cards -->
    @php
        // Cukup ambil data semua alert user ini untuk statistik ringan
        $allAlerts = \App\Models\KasproAlert::where('user_id', auth()->id())->get();
        $countEmergency = $allAlerts->where('severity', 'emergency')->count();
        $countCritical = $allAlerts->where('severity', 'critical')->count();
        $countWarning = $allAlerts->where('severity', 'warning')->count();
        $countInfo = $allAlerts->where('severity', 'info')->count();
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Darurat</p>
                    <p class="text-2xl font-bold text-red-600">{{ $countEmergency }}</p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <span class="text-xl">🔴</span>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Kritis</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $countCritical }}</p>
                </div>
                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                    <span class="text-xl">🟠</span>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Peringatan</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $countWarning }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                    <span class="text-xl">🟡</span>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Info</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $countInfo }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="text-xl">🔵</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs (Visual Only for now) -->
    <div class="flex flex-wrap gap-2 mb-6 border-b border-gray-200 pb-2">
        <a href="{{ route('kaspro.alerts') }}" class="px-4 py-2 text-sm font-medium {{ !request('severity') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900' }}">Semua ({{ $allAlerts->count() }})</a>
        <a href="{{ route('kaspro.alerts', ['severity' => 'emergency']) }}" class="px-4 py-2 text-sm font-medium {{ request('severity') == 'emergency' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900' }}">🔴 Darurat ({{ $countEmergency }})</a>
        <a href="{{ route('kaspro.alerts', ['severity' => 'critical']) }}" class="px-4 py-2 text-sm font-medium {{ request('severity') == 'critical' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900' }}">🟠 Kritis ({{ $countCritical }})</a>
        <a href="{{ route('kaspro.alerts', ['severity' => 'warning']) }}" class="px-4 py-2 text-sm font-medium {{ request('severity') == 'warning' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900' }}">🟡 Peringatan ({{ $countWarning }})</a>
        <a href="{{ route('kaspro.alerts', ['severity' => 'info']) }}" class="px-4 py-2 text-sm font-medium {{ request('severity') == 'info' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900' }}">🔵 Info ({{ $countInfo }})</a>
    </div>

    <!-- Alerts List -->
    <div class="space-y-3">
        @forelse($alerts as $alert)
        @php
            $colors = [
                'emergency' => ['border' => 'border-red-500', 'bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => '🔴 Darurat'],
                'critical' => ['border' => 'border-orange-500', 'bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'icon' => '🟠 Kritis'],
                'warning' => ['border' => 'border-yellow-500', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'icon' => '🟡 Peringatan'],
                'info' => ['border' => 'border-blue-500', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => '🔵 Info'],
            ];
            $style = $colors[$alert->severity] ?? $colors['info'];
            
            $containerClass = $alert->is_read 
                ? 'bg-gray-50 rounded-xl shadow-sm border-l-4 border-gray-300 p-5 hover:shadow-md transition' 
                : 'bg-white rounded-xl shadow-sm border-l-4 ' . $style['border'] . ' p-5 hover:shadow-md transition';
        @endphp
        
        <div class="{{ $containerClass }}">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $style['bg'] }} {{ $style['text'] }}">
                            {{ $style['icon'] }}
                        </span>
                        @if(!$alert->is_read)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                            Baru
                        </span>
                        @endif
                        <span class="text-sm font-semibold text-gray-900">{{ $alert->title }}</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3">{{ $alert->message }}</p>
                    <div class="flex items-center gap-4 text-xs text-gray-400">
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ $alert->created_at->format('d F Y, H:i') }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $alert->created_at->diffForHumans() }}
                        </span>
                        @if($alert->is_read)
                        <span class="flex items-center gap-1 text-green-600">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Sudah dibaca
                        </span>
                        @endif
                    </div>
                </div>
                @if(!$alert->is_read)
                <form action="{{ route('kaspro.alerts.read', $alert->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="ml-4 px-3 py-1.5 text-sm text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-50 transition">
                        Tandai Dibaca
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="p-12 text-center text-slate-400 italic text-sm border border-dashed border-gray-300 rounded-xl">
            Tidak ada peringatan sistem saat ini.
        </div>
        @endforelse

        <!-- Pagination -->
        <div class="mt-6">
            {{ $alerts->links() }}
        </div>
    </div>

    <!-- Tips Section -->
    <div class="mt-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-5 border border-blue-100">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                <span class="text-xl">💡</span>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 mb-2">Tips Menghadapi Peringatan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div class="flex items-start gap-2">
                        <span class="text-red-500">🔴</span>
                        <span class="text-gray-600"><strong class="text-gray-800">Darurat</strong>: Segera evaluasi arus kas dan cari tambahan dana</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-orange-500">🟠</span>
                        <span class="text-gray-600"><strong class="text-gray-800">Kritis</strong>: Kurangi pengeluaran tidak perlu, percepat penagihan</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-yellow-500">🟡</span>
                        <span class="text-gray-600"><strong class="text-gray-800">Peringatan</strong>: Periksa detail transaksi, rencanakan anggaran</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-blue-500">🔵</span>
                        <span class="text-gray-600"><strong class="text-gray-800">Info</strong>: Gunakan sebagai insight untuk perencanaan bisnis</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection