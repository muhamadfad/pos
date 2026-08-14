@extends('layouts.admin')

@section('title', 'Produksi (Olah Bahan)')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Produksi (Olah Bahan)</h1>
            <p class="text-slate-500 mt-2 font-medium">Data produksi olah bahan mentah ke bahan jadi di Gudang Pusat</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.central-productions.create') }}" 
               class="inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 text-white font-semibold text-sm rounded-xl hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-500/30 transition-all duration-200">
                <span class="iconify mr-2" data-icon="lucide:plus" data-width="18"></span>
                Tambah Produksi
            </a>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
        
        <!-- Filters -->
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <form action="{{ route('admin.central-productions.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" 
                           class="w-full bg-white border border-slate-200 text-slate-700 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-colors">
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" 
                           class="w-full bg-white border border-slate-200 text-slate-700 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-colors">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-slate-800 text-white font-semibold text-sm rounded-xl hover:bg-slate-700 transition-colors">
                        Filter Data
                    </button>
                    @if(request()->hasAny(['start_date', 'end_date']))
                        <a href="{{ route('admin.central-productions.index') }}" class="ml-3 px-4 py-2.5 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/50 text-xs uppercase font-semibold text-slate-500 tracking-wider">
                    <tr>
                        <th class="px-6 py-4 rounded-tl-2xl">Ref. No</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Barang Hasil</th>
                        <th class="px-6 py-4 text-right">Yield Aktual</th>
                        <th class="px-6 py-4 text-right">Total Biaya Mentah</th>
                        <th class="px-6 py-4 text-center rounded-tr-2xl">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100/80">
                    @forelse ($productions as $item)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <td class="px-6 py-4">
                                <span class="font-semibold text-slate-800">{{ $item->reference_number }}</span>
                            </td>
                            <td class="px-6 py-4">
                                {{ \Carbon\Carbon::parse($item->date)->translatedFormat('d F Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800">{{ $item->resultIngredient->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-right font-medium">
                                {{ number_format($item->result_quantity, 1, ',', '.') }} {{ $item->resultIngredient->unit->name ?? 'kg' }}
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-slate-800">
                                Rp {{ number_format($item->total_cost, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.central-productions.show', $item->id) }}" 
                                       class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                       title="Lihat Detail">
                                        <span class="iconify" data-icon="lucide:eye" data-width="18"></span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                                    <span class="iconify text-slate-400" data-icon="lucide:package-open" data-width="32"></span>
                                </div>
                                <h3 class="text-slate-800 font-semibold mb-1">Belum ada data</h3>
                                <p class="text-slate-500 text-sm">Tidak ada riwayat produksi olah bahan yang ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($productions->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $productions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
