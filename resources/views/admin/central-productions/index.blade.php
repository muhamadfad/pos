@extends('layouts.admin')

@section('title', 'Produksi (Olah Bahan)')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header Section -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-indigo-700 to-violet-800 p-8 sm:p-10 text-white shadow-xl shadow-indigo-200/50">
        <!-- Decorative elements -->
        <div class="absolute top-0 right-0 -translate-y-12 translate-x-1/3 opacity-20 pointer-events-none">
            <svg width="404" height="384" fill="none" viewBox="0 0 404 384"><defs><pattern id="d3eb07ae-5182-43e6-857d-35c643af9034" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><rect x="0" y="0" width="4" height="4" fill="currentColor"></rect></pattern></defs><rect width="404" height="384" fill="url(#d3eb07ae-5182-43e6-857d-35c643af9034)"></rect></svg>
        </div>
        <div class="absolute bottom-0 left-0 translate-y-1/3 -translate-x-1/3 opacity-20 pointer-events-none">
            <svg width="404" height="384" fill="none" viewBox="0 0 404 384"><defs><pattern id="d3eb07ae-5182-43e6-857d-35c643af9034" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><rect x="0" y="0" width="4" height="4" fill="currentColor"></rect></pattern></defs><rect width="404" height="384" fill="url(#d3eb07ae-5182-43e6-857d-35c643af9034)"></rect></svg>
        </div>

        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 border border-white/20 text-indigo-50 text-xs font-semibold tracking-wide uppercase mb-3 backdrop-blur-md">
                    <span class="iconify" data-icon="lucide:chef-hat"></span>
                    Dapur Pusat
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mb-2">Olah Bahan Produksi</h1>
                <p class="text-indigo-100 max-w-xl text-sm sm:text-base leading-relaxed text-balance">
                    Konversi bahan mentah menjadi bahan jadi (Yield). Sistem otomatis merekap sisa stok dan mengkalkulasi ulang HPP sesuai modal awal.
                </p>
            </div>
            <div class="shrink-0 flex items-center gap-3">
                <a href="{{ route('admin.central-productions.download-pdf', request()->all()) }}" target="_blank"
                   class="inline-flex items-center justify-center gap-2 px-4 py-3.5 bg-rose-50 text-rose-600 font-bold text-sm rounded-2xl hover:bg-rose-100 hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 ring-1 ring-rose-200">
                    <span class="iconify text-xl" data-icon="mdi:file-pdf-box"></span>
                    Download PDF
                </a>
                <a href="{{ route('admin.central-productions.create') }}" 
                   class="inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-white text-indigo-600 font-bold text-sm rounded-2xl hover:bg-indigo-50 hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 shadow-lg shadow-black/10 ring-1 ring-white/50 focus:outline-none focus:ring-4 focus:ring-indigo-300">
                    <span class="iconify text-xl" data-icon="lucide:plus"></span>
                    Tambah Produksi
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden relative z-20">
        
        <!-- Filter Toolbar -->
        <div class="border-b border-slate-100 bg-slate-50/50 p-4 sm:p-6">
            <form action="{{ route('admin.central-productions.index') }}" method="GET" class="flex flex-col sm:flex-row items-end gap-4">
                <div class="w-full sm:w-48">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Mulai Tanggal</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <span class="iconify text-slate-400" data-icon="lucide:calendar"></span>
                        </div>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" 
                               class="w-full pl-10 bg-white border border-slate-200 text-slate-700 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 py-2.5 transition-all outline-none">
                    </div>
                </div>
                <div class="w-full sm:w-48">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Sampai Tanggal</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <span class="iconify text-slate-400" data-icon="lucide:calendar"></span>
                        </div>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" 
                               class="w-full pl-10 bg-white border border-slate-200 text-slate-700 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 py-2.5 transition-all outline-none">
                    </div>
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto mt-4 sm:mt-0">
                    <button type="submit" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-slate-800 text-white font-semibold text-sm rounded-xl hover:bg-slate-700 hover:shadow-md transition-all active:scale-95">
                        <span class="iconify" data-icon="lucide:filter"></span>
                        Terapkan
                    </button>
                    @if(request()->hasAny(['start_date', 'end_date']))
                        <a href="{{ route('admin.central-productions.index') }}" class="inline-flex items-center justify-center p-2.5 text-slate-400 hover:text-rose-500 bg-white border border-slate-200 hover:border-rose-200 rounded-xl transition-all" title="Reset Filter">
                            <span class="iconify text-lg" data-icon="lucide:x"></span>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Data -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-white border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Ref No.</th>
                        <th class="px-6 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Hasil Olahan</th>
                        <th class="px-6 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Yield Aktual</th>
                        <th class="px-6 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Total Biaya (Modal)</th>
                        <th class="px-6 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($productions as $item)
                        <tr class="hover:bg-indigo-50/40 transition-colors group">
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono font-medium bg-slate-100 text-slate-600 border border-slate-200 group-hover:bg-white transition-colors">
                                    {{ $item->reference_number ?: '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 text-slate-600">
                                    <span class="iconify text-slate-400" data-icon="lucide:calendar-clock"></span>
                                    {{ \Carbon\Carbon::parse($item->date)->translatedFormat('d M Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 shrink-0">
                                        <span class="iconify" data-icon="lucide:beef"></span>
                                    </div>
                                    <span class="font-bold text-slate-800">{{ $item->resultIngredient?->name ?? 'Bahan Dihapus' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 font-bold border border-emerald-100">
                                    {{ number_format($item->result_quantity, 1, ',', '.') }} <span class="text-xs font-normal opacity-70">{{ $item->resultIngredient?->unit?->name ?? 'kg' }}</span>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-bold text-slate-800">Rp {{ number_format($item->total_cost, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.central-productions.show', $item->id) }}" 
                                       class="w-9 h-9 rounded-xl inline-flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all border border-transparent hover:border-indigo-100"
                                       title="Lihat Detail">
                                        <span class="iconify text-lg" data-icon="lucide:arrow-right"></span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center">
                                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-slate-50 mb-6 border-8 border-white shadow-sm">
                                    <span class="iconify text-slate-300 text-5xl" data-icon="lucide:package-open"></span>
                                </div>
                                <h3 class="text-xl font-bold text-slate-800 mb-2">Belum ada aktivitas produksi</h3>
                                <p class="text-slate-500 max-w-sm mx-auto">Catat pengolahan bahan mentah Anda untuk melacak penyusutan dan menghitung ulang HPP secara akurat.</p>
                                <a href="{{ route('admin.central-productions.create') }}" class="inline-flex items-center gap-2 mt-6 px-5 py-2.5 bg-indigo-50 text-indigo-600 font-semibold rounded-xl hover:bg-indigo-100 transition-colors">
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
            <div class="px-6 py-4 border-t border-slate-100 bg-white">
                {{ $productions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
