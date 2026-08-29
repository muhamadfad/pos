@extends('layouts.admin')

@section('title', 'Distribusi Cabang')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Distribusi Cabang</h1>
        <p class="text-slate-500 text-sm mt-1">Kelola pengiriman stok dari Gudang Pusat ke Cabang.</p>
    </div>
    <div class="flex items-center gap-3">
        <!-- Filter Period -->
        <form action="{{ route('admin.stock-transfers.index') }}" method="GET" class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <span class="iconify" data-icon="mdi:calendar-month-outline"></span>
            </div>
            <select name="period" onchange="this.form.submit()" class="pl-9 pr-8 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all appearance-none cursor-pointer">
                <option value="all" {{ ($period ?? 'all') == 'all' ? 'selected' : '' }}>Semua Waktu</option>
                <option value="today" {{ ($period ?? '') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                <option value="this_week" {{ ($period ?? '') == 'this_week' ? 'selected' : '' }}>Minggu Ini</option>
                <option value="this_month" {{ ($period ?? '') == 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
            </select>
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                <span class="iconify" data-icon="mdi:chevron-down"></span>
            </div>
        </form>

        <a href="{{ route('admin.stock-transfers.download-pdf', request()->all()) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-600 text-sm font-semibold rounded-xl transition-all">
            <span class="iconify text-lg" data-icon="mdi:file-pdf-box"></span>
            Download PDF
        </a>
        <a href="{{ route('admin.stock-transfers.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all active:scale-[0.98]">
            <span class="iconify text-lg" data-icon="mdi:truck-delivery-outline"></span>
            Distribusi Baru
        </a>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-medium border-b border-slate-200">No. Ref & Tanggal</th>
                    <th class="px-6 py-4 font-medium border-b border-slate-200">Tujuan Cabang</th>
                    <th class="px-6 py-4 font-medium border-b border-slate-200">Status</th>
                    <th class="px-6 py-4 font-medium border-b border-slate-200 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-slate-100">
                @forelse($transfers as $transfer)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-medium text-slate-800">{{ $transfer->reference_number ?: '-' }}</div>
                        <div class="text-slate-500 text-xs mt-0.5">{{ \Carbon\Carbon::parse($transfer->transfer_date)->format('d M Y') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="iconify text-slate-400" data-icon="mdi:store-outline"></span>
                            <span class="font-medium text-slate-700">{{ $transfer->toBranch->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-600 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Selesai
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('admin.stock-transfers.show', $transfer->id) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors inline-block" title="Detail">
                            <span class="iconify text-lg" data-icon="mdi:eye-outline"></span>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                        Belum ada riwayat distribusi ke cabang.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($transfers->hasPages())
    <div class="p-6 border-t border-slate-100">
        {{ $transfers->links() }}
    </div>
    @endif
</div>
@endsection
