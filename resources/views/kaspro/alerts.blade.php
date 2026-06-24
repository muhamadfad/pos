@extends('layouts.admin')

@section('title', 'Peringatan KasPro')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-gray-200 pb-5">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 tracking-tight">Peringatan Sistem</h1>
            <p class="text-sm text-gray-500 mt-1">Daftar notifikasi aktivitas dan pengingat dari KasPro.</p>
        </div>
    </div>

    <!-- Alert Statistics Cards -->
    @php
        $allAlerts = \App\Models\KasproAlert::where('user_id', auth()->id())->get();
        $countEmergency = $allAlerts->where('severity', 'emergency')->count();
        $countCritical = $allAlerts->where('severity', 'critical')->count();
        $countWarning = $allAlerts->where('severity', 'warning')->count();
        $countInfo = $allAlerts->where('severity', 'info')->count();
    @endphp
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <!-- Darurat -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="w-8 h-8 rounded-md bg-red-50 text-red-600 flex items-center justify-center ring-1 ring-red-100">
                    <span class="iconify text-lg" data-icon="mdi:alert-octagon"></span>
                </div>
                <span class="text-xs font-medium text-gray-500 uppercase">Darurat</span>
            </div>
            <div>
                <p class="text-3xl font-semibold text-gray-900">{{ $countEmergency }}</p>
            </div>
        </div>

        <!-- Kritis -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="w-8 h-8 rounded-md bg-orange-50 text-orange-600 flex items-center justify-center ring-1 ring-orange-100">
                    <span class="iconify text-lg" data-icon="mdi:alert"></span>
                </div>
                <span class="text-xs font-medium text-gray-500 uppercase">Kritis</span>
            </div>
            <div>
                <p class="text-3xl font-semibold text-gray-900">{{ $countCritical }}</p>
            </div>
        </div>

        <!-- Peringatan -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="w-8 h-8 rounded-md bg-amber-50 text-amber-600 flex items-center justify-center ring-1 ring-amber-100">
                    <span class="iconify text-lg" data-icon="mdi:alert-circle-outline"></span>
                </div>
                <span class="text-xs font-medium text-gray-500 uppercase">Peringatan</span>
            </div>
            <div>
                <p class="text-3xl font-semibold text-gray-900">{{ $countWarning }}</p>
            </div>
        </div>

        <!-- Info -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="w-8 h-8 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center ring-1 ring-blue-100">
                    <span class="iconify text-lg" data-icon="mdi:information-variant"></span>
                </div>
                <span class="text-xs font-medium text-gray-500 uppercase">Info</span>
            </div>
            <div>
                <p class="text-3xl font-semibold text-gray-900">{{ $countInfo }}</p>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('kaspro.alerts') }}" 
           class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ !request('severity') ? 'bg-gray-900 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
           Semua ({{ $allAlerts->count() }})
        </a>
        <a href="{{ route('kaspro.alerts', ['severity' => 'emergency']) }}" 
           class="px-4 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-1.5 {{ request('severity') == 'emergency' ? 'bg-red-50 text-red-700 ring-1 ring-red-200' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
           <span class="iconify" data-icon="mdi:alert-octagon"></span> Darurat
        </a>
        <a href="{{ route('kaspro.alerts', ['severity' => 'critical']) }}" 
           class="px-4 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-1.5 {{ request('severity') == 'critical' ? 'bg-orange-50 text-orange-700 ring-1 ring-orange-200' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
           <span class="iconify" data-icon="mdi:alert"></span> Kritis
        </a>
        <a href="{{ route('kaspro.alerts', ['severity' => 'warning']) }}" 
           class="px-4 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-1.5 {{ request('severity') == 'warning' ? 'bg-amber-50 text-amber-700 ring-1 ring-amber-200' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
           <span class="iconify" data-icon="mdi:alert-circle-outline"></span> Peringatan
        </a>
        <a href="{{ route('kaspro.alerts', ['severity' => 'info']) }}" 
           class="px-4 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-1.5 {{ request('severity') == 'info' ? 'bg-blue-50 text-blue-700 ring-1 ring-blue-200' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
           <span class="iconify" data-icon="mdi:information-variant"></span> Info
        </a>
    </div>

    <!-- Alerts List -->
    <div class="space-y-3">
        @forelse($alerts as $alert)
        @php
            $colors = [
                'emergency' => ['border' => 'border-red-600', 'bg' => 'bg-red-50', 'text' => 'text-red-700', 'icon' => 'mdi:alert-octagon'],
                'critical' => ['border' => 'border-orange-500', 'bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'icon' => 'mdi:alert'],
                'warning' => ['border' => 'border-amber-400', 'bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'icon' => 'mdi:alert-circle-outline'],
                'info' => ['border' => 'border-blue-500', 'bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'icon' => 'mdi:information-variant'],
            ];
            $style = $colors[$alert->severity] ?? $colors['info'];
            
            $containerClass = $alert->is_read 
                ? 'bg-gray-50/50 rounded-xl border border-gray-200 p-5' 
                : 'bg-white rounded-xl border border-gray-200 shadow-sm border-l-4 ' . $style['border'] . ' p-5';
        @endphp
        
        <div class="{{ $containerClass }}">
            <div class="flex flex-col sm:flex-row items-start justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="mt-1 w-10 h-10 rounded-full {{ $style['bg'] }} flex items-center justify-center flex-shrink-0">
                        <span class="iconify {{ $style['text'] }} text-xl" data-icon="{{ $style['icon'] }}"></span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="text-base font-semibold {{ $alert->is_read ? 'text-gray-600' : 'text-gray-900' }}">{{ $alert->title }}</h3>
                            @if(!$alert->is_read)
                            <span class="px-2 py-0.5 bg-blue-50 text-blue-600 text-[11px] font-semibold rounded-md border border-blue-100">
                                Baru
                            </span>
                            @endif
                        </div>
                        <p class="{{ $alert->is_read ? 'text-gray-500' : 'text-gray-600' }} text-sm mb-3">{{ $alert->message }}</p>
                        
                        <div class="flex items-center gap-4 text-xs font-medium text-gray-500">
                            <span class="flex items-center gap-1.5">
                                <span class="iconify" data-icon="mdi:calendar-outline"></span>
                                {{ $alert->created_at->format('d M Y, H:i') }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="iconify" data-icon="mdi:clock-outline"></span>
                                {{ $alert->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                </div>
                
                @if(!$alert->is_read)
                <form action="{{ route('kaspro.alerts.read', $alert->id) }}" method="POST" class="w-full sm:w-auto">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        Tandai Dibaca
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="py-12 bg-gray-50 rounded-xl border border-dashed border-gray-300 text-center">
            <span class="iconify text-4xl text-gray-400 mx-auto mb-3" data-icon="mdi:check-circle-outline"></span>
            <h3 class="text-sm font-medium text-gray-900">Tidak ada peringatan</h3>
            <p class="text-sm text-gray-500 mt-1">Sistem berjalan normal dan tidak ada notifikasi yang perlu diperhatikan.</p>
        </div>
        @endforelse

        <!-- Pagination -->
        @if($alerts->hasPages())
        <div class="mt-6">
            {{ $alerts->links() }}
        </div>
        @endif
    </div>

    <!-- Tips Section -->
    <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 mt-8">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 bg-white border border-gray-200 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                <span class="iconify text-gray-600 text-xl" data-icon="mdi:lightbulb-outline"></span>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Panduan Tindak Lanjut Peringatan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex gap-3">
                        <span class="iconify text-red-500 text-lg flex-shrink-0 mt-0.5" data-icon="mdi:alert-octagon"></span>
                        <div>
                            <h4 class="text-xs font-semibold text-gray-900 uppercase">Darurat</h4>
                            <p class="text-xs text-gray-600 mt-0.5">Evaluasi arus kas segera. Tunda pengeluaran non-esensial dan pastikan dana darurat mencukupi.</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <span class="iconify text-orange-500 text-lg flex-shrink-0 mt-0.5" data-icon="mdi:alert"></span>
                        <div>
                            <h4 class="text-xs font-semibold text-gray-900 uppercase">Kritis</h4>
                            <p class="text-xs text-gray-600 mt-0.5">Pantau ketat pengeluaran mingguan dan maksimalkan upaya penagihan piutang jika ada.</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <span class="iconify text-amber-500 text-lg flex-shrink-0 mt-0.5" data-icon="mdi:alert-circle-outline"></span>
                        <div>
                            <h4 class="text-xs font-semibold text-gray-900 uppercase">Peringatan</h4>
                            <p class="text-xs text-gray-600 mt-0.5">Tinjau ulang efisiensi operasional. Identifikasi lonjakan biaya yang tidak biasa.</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <span class="iconify text-blue-500 text-lg flex-shrink-0 mt-0.5" data-icon="mdi:information-variant"></span>
                        <div>
                            <h4 class="text-xs font-semibold text-gray-900 uppercase">Info</h4>
                            <p class="text-xs text-gray-600 mt-0.5">Analisis ringkasan performa untuk memandu keputusan strategis bisnis Anda bulan ini.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection