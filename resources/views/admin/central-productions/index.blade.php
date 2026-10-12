@extends('layouts.admin')

@section('title', 'Produksi (Olah Bahan)')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header Section -->
    <div class="relative overflow-hidden rounded-md bg-gradient-to-br from-indigo-600 via-indigo-700 to-violet-800 p-8 sm:p-10 text-white shadow-sm">
        <!-- Decorative elements -->
        <div class="absolute top-0 right-0 -translate-y-12 translate-x-1/3 opacity-20 pointer-events-none">
            <svg width="404" height="384" fill="none" viewBox="0 0 404 384"><defs><pattern id="d3eb07ae-5182-43e6-857d-35c643af9034" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><rect x="0" y="0" width="4" height="4" fill="currentColor"></rect></pattern></defs><rect width="404" height="384" fill="url(#d3eb07ae-5182-43e6-857d-35c643af9034)"></rect></svg>
        </div>
        <div class="absolute bottom-0 left-0 translate-y-1/3 -translate-x-1/3 opacity-20 pointer-events-none">
            <svg width="404" height="384" fill="none" viewBox="0 0 404 384"><defs><pattern id="d3eb07ae-5182-43e6-857d-35c643af9034" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><rect x="0" y="0" width="4" height="4" fill="currentColor"></rect></pattern></defs><rect width="404" height="384" fill="url(#d3eb07ae-5182-43e6-857d-35c643af9034)"></rect></svg>
        </div>

        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-gray-50/10 border border-white/20 text-indigo-50 text-xs font-semibold tracking-wide uppercase mb-3 backdrop-blur-md">
                    <span class="iconify" data-icon="lucide:chef-hat"></span>
                    Dapur Pusat
                </div>
                <h1 class="text-3xl sm:text-4xl font-semibold tracking-tight mb-2">Olah Bahan Produksi</h1>
                <p class="text-indigo-100 max-w-xl text-sm sm:text-base leading-relaxed text-balance">
                    Konversi bahan mentah menjadi bahan jadi (Yield). Sistem otomatis merekap sisa stok dan mengkalkulasi ulang HPP sesuai modal awal.
                </p>
            </div>
            <div class="shrink-0 flex items-center gap-3">
                <a href="{{ route('admin.central-productions.download-combined-pdf', request()->all()) }}" target="_blank"
                   class="inline-flex items-center justify-center gap-2 px-4 py-3.5 bg-white border border-gray-200 text-black font-bold text-sm rounded-md hover:bg-gray-50 border border-gray-200 hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 ring-1 ring-emerald-200" title="PDF Lengkap (Produksi, Distribusi, Return)">
                    <span class="iconify text-xl" data-icon="mdi:file-pdf-box-multiple"></span>
                    PDF Lengkap
                </a>
                <a href="{{ route('admin.central-productions.download-pdf', request()->all()) }}" target="_blank"
                   class="inline-flex items-center justify-center gap-2 px-4 py-3.5 bg-rose-50 text-rose-600 font-bold text-sm rounded-md hover:bg-rose-100 hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 ring-1 ring-rose-200">
                    <span class="iconify text-xl" data-icon="mdi:file-pdf-box"></span>
                    Download PDF
                </a>
                <a href="{{ route('admin.central-productions.create') }}" 
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-50 text-black font-bold text-sm rounded-md hover:bg-gray-50 hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 shadow-sm shadow-black/10 ring-1 ring-white/50 focus:outline-none focus:ring-4 focus:ring-black/5">
                    <span class="iconify text-xl" data-icon="lucide:plus"></span>
                    Tambah Produksi
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="bg-gray-50 rounded-md shadow-sm border border-gray-200 overflow-hidden relative z-20">
        
        <!-- Filter Toolbar -->
        <div class="border-b border-gray-200 bg-white p-4 sm:p-6">
            <form action="{{ route('admin.central-productions.index') }}" method="GET" class="flex flex-col sm:flex-row items-end gap-4">
                <div class="w-full sm:w-48">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Mulai Tanggal</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <span class="iconify text-gray-500" data-icon="lucide:calendar"></span>
                        </div>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" 
                               class="w-full pl-10 bg-gray-50 border border-gray-200 text-slate-700 text-sm rounded-md focus:ring-2 focus:ring-[#1677ff]/20 focus:border-gray-200 py-2.5 transition-all outline-none">
                    </div>
                </div>
                <div class="w-full sm:w-48">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Sampai Tanggal</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <span class="iconify text-gray-500" data-icon="lucide:calendar"></span>
                        </div>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" 
                               class="w-full pl-10 bg-gray-50 border border-gray-200 text-slate-700 text-sm rounded-md focus:ring-2 focus:ring-[#1677ff]/20 focus:border-gray-200 py-2.5 transition-all outline-none">
                    </div>
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto mt-4 sm:mt-0">
                    <button type="submit" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-[#001529] text-white font-semibold text-sm rounded-md hover:bg-slate-700 hover:shadow-sm transition-all active:scale-95">
                        <span class="iconify" data-icon="lucide:filter"></span>
                        Terapkan
                    </button>
                    @if(request()->hasAny(['start_date', 'end_date']))
                        <a href="{{ route('admin.central-productions.index') }}" class="inline-flex items-center justify-center p-2.5 text-gray-500 hover:text-rose-500 bg-gray-50 border border-gray-200 hover:border-rose-200 rounded-md transition-all" title="Reset Filter">
                            <span class="iconify text-lg" data-icon="lucide:x"></span>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Data -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">Ref No.</th>
                        <th class="px-6 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">Hasil Olahan</th>
                        <th class="px-6 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Yield Aktual</th>
                        <th class="px-6 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Total Biaya (Modal)</th>
                        <th class="px-6 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($productions as $item)
                        <tr class="hover:bg-gray-50/40 transition-colors group">
                            <td class="px-4 py-2">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono font-medium bg-slate-100 text-gray-600 border border-gray-200 group-hover:bg-gray-50 transition-colors">
                                    {{ $item->reference_number ?: '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex items-center gap-2 text-gray-600">
                                    <span class="iconify text-gray-500" data-icon="lucide:calendar-clock"></span>
                                    {{ \Carbon\Carbon::parse($item->date)->translatedFormat('d M Y') }}
                                </div>
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-md bg-gray-50 flex items-center justify-center text-black shrink-0">
                                        <span class="iconify" data-icon="lucide:beef"></span>
                                    </div>
                                    <span class="font-bold text-black">{{ $item->resultIngredient?->name ?? 'Bahan Dihapus' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2 text-right">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-white border border-gray-200 text-black font-bold border border-gray-200">
                                    {{ number_format($item->result_quantity, 1, ',', '.') }} <span class="text-xs font-normal opacity-70">{{ $item->resultIngredient?->unit?->name ?? 'kg' }}</span>
                                </span>
                            </td>
                            <td class="px-4 py-2 text-right">
                                <span class="font-bold text-black">Rp {{ number_format($item->total_cost, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.central-productions.show', $item->id) }}" 
                                       class="w-9 h-9 rounded-md inline-flex items-center justify-center text-gray-500 hover:text-black hover:bg-gray-50 transition-all border border-transparent hover:border-gray-200"
                                       title="Lihat Detail">
                                        <span class="iconify text-lg" data-icon="lucide:arrow-right"></span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center">
                                <div class="inline-flex items-center justify-center w-24 h-24 rounded-md bg-white mb-6 border-8 border-white shadow-sm">
                                    <span class="iconify text-slate-300 text-5xl" data-icon="lucide:package-open"></span>
                                </div>
                                <h3 class="text-lg font-semibold text-black mb-2">Belum ada aktivitas produksi</h3>
                                <p class="text-gray-500 max-w-sm mx-auto">Catat pengolahan bahan mentah Anda untuk melacak penyusutan dan menghitung ulang HPP secara akurat.</p>
                                <a href="{{ route('admin.central-productions.create') }}" class="inline-flex items-center gap-2 mt-6 px-5 py-2.5 bg-gray-50 text-black font-semibold rounded-md hover:bg-gray-50 transition-colors">
                                    <span class="iconify" data-icon="lucide:plus"></span>
                                    Mulai Produksi
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($productions->hasPages())
            <div class="px-4 py-2 border-t border-gray-200 bg-gray-50">
                {{ $productions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
