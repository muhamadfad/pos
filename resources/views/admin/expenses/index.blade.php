@extends('layouts.admin')

@section('title', 'Pengeluaran')

@section('content')
<div class="space-y-8">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                <span class="p-2 bg-rose-600 text-white rounded-2xl shadow-lg shadow-rose-100">
                    <span class="iconify" data-icon="mdi:cash-minus"></span>
                </span>
                Pengeluaran
            </h1>
            <p class="text-slate-500 font-medium tracking-tight">Catat dan kelola pengeluaran operasional</p>
        </div>
        <a href="{{ route('admin.expenses.create') }}"
            class="inline-flex items-center gap-2 px-6 py-3 bg-rose-600 text-white rounded-2xl font-bold text-sm shadow-lg shadow-rose-100 hover:bg-rose-700 transition-all hover:scale-[1.02] active:scale-95">
            <span class="iconify text-lg" data-icon="mdi:plus"></span>
            Catat Pengeluaran
        </a>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center gap-3">
        <span class="iconify text-xl" data-icon="mdi:check-circle-outline"></span>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Hari Ini -->
        <div
            class="relative overflow-hidden bg-white p-7 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md transition-all group">
            <div
                class="absolute -right-4 -top-4 w-24 h-24 bg-rose-50 rounded-full opacity-50 group-hover:scale-110 transition-transform">
            </div>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 relative">Hari Ini</p>
            <div class="flex items-end justify-between relative">
                <div>
                    <p class="text-2xl font-black text-slate-900 tracking-tight">
                        Rp {{ number_format($totalToday, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-rose-100 flex items-center justify-center text-rose-600">
                    <span class="iconify text-2xl" data-icon="mdi:calendar-today"></span>
                </div>
            </div>
        </div>

        <!-- Bulan Ini -->
        <div
            class="relative overflow-hidden bg-white p-7 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md transition-all group">
            <div
                class="absolute -right-4 -top-4 w-24 h-24 bg-amber-50 rounded-full opacity-50 group-hover:scale-110 transition-transform">
            </div>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 relative">Bulan Ini</p>
            <div class="flex items-end justify-between relative">
                <div>
                    <p class="text-2xl font-black text-slate-900 tracking-tight">
                        Rp {{ number_format($totalMonth, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center text-amber-600">
                    <span class="iconify text-2xl" data-icon="mdi:calendar-month-outline"></span>
                </div>
            </div>
        </div>

        <!-- Semua Waktu -->
        <div
            class="relative overflow-hidden bg-white p-7 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md transition-all group">
            <div
                class="absolute -right-4 -top-4 w-24 h-24 bg-slate-100 rounded-full opacity-50 group-hover:scale-110 transition-transform">
            </div>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 relative">Total Keseluruhan
            </p>
            <div class="flex items-end justify-between relative">
                <div>
                    <p class="text-2xl font-black text-slate-900 tracking-tight">
                        Rp {{ number_format($totalAll, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-600">
                    <span class="iconify text-2xl" data-icon="mdi:sigma"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Table -->
    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">

        <!-- Filter Bar -->
        <div class="p-6 border-b border-slate-100">
            <form method="GET" action="{{ route('admin.expenses.index') }}" class="flex flex-wrap gap-3 items-end">

                <div class="flex-1 min-w-[200px]">
                    <label
                        class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Cari</label>
                    <div class="relative">
                        <span class="iconify absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"
                            data-icon="mdi:magnify"></span>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Deskripsi, kategori, referensi..."
                            class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-4 focus:ring-rose-50 focus:border-rose-400 transition-all">
                    </div>
                </div>

                <div>
                    <label
                        class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Kategori</label>
                    <select name="category"
                        class="px-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-4 focus:ring-rose-50 focus:border-rose-400 transition-all">
                        <option value="">Semua</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category')===$cat ? 'selected' : '' }}>
                            {{ ucfirst($cat) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Dari
                        Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="px-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-4 focus:ring-rose-50 focus:border-rose-400 transition-all">
                </div>

                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Sampai
                        Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="px-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-4 focus:ring-rose-50 focus:border-rose-400 transition-all">
                </div>

                <button type="submit"
                    class="px-6 py-2.5 bg-rose-600 text-white rounded-xl font-bold text-sm hover:bg-rose-700 transition-all shadow-sm">
                    Filter
                </button>

                @if(request()->hasAny(['search','category','date_from','date_to']))
                <a href="{{ route('admin.expenses.index') }}"
                    class="px-6 py-2.5 bg-slate-100 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-200 transition-all">
                    Reset
                </a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="text-left px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-widest">
                            Tanggal</th>
                        <th class="text-left px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-widest">
                            Deskripsi</th>
                        <th class="text-left px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-widest">
                            Kategori</th>
                        <th class="text-left px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-widest">
                            Referensi</th>
                        <th class="text-left px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-widest">
                            Dicatat oleh</th>
                        <th
                            class="text-right px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-widest">
                            Jumlah</th>
                        <th
                            class="text-center px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-widest">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($expenses as $expense)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-4 font-semibold text-slate-600 whitespace-nowrap">
                            {{ $expense->date->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-slate-800">{{ $expense->description }}</p>
                            @if($expense->notes)
                            <p class="text-xs text-slate-400 mt-0.5 truncate max-w-[200px]">{{ $expense->notes }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                            $catColor = match($expense->category) {
                            'operational' => 'bg-blue-50 text-blue-700',
                            'salary' => 'bg-indigo-50 text-indigo-700',
                            'utility' => 'bg-amber-50 text-amber-700',
                            'transport' => 'bg-emerald-50 text-emerald-700',
                            default => 'bg-slate-100 text-slate-600',
                            };
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $catColor }}">
                                {{ ucfirst($expense->category ?? 'other') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-500 font-medium">
                            {{ $expense->reference ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-slate-500 font-medium">
                            {{ $expense->user->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-right font-black text-rose-600 whitespace-nowrap">
                            Rp {{ number_format($expense->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            <div
                                class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.expenses.edit', $expense) }}"
                                    class="p-2 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-100 transition-colors">
                                    <span class="iconify text-base" data-icon="mdi:pencil-outline"></span>
                                </a>
                                <button
                                    onclick="confirmDelete('{{ route('admin.expenses.destroy', $expense) }}', '{{ addslashes($expense->description) }}')"
                                    class="p-2 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-100 transition-colors">
                                    <span class="iconify text-base" data-icon="mdi:trash-can-outline"></span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <span class="iconify text-5xl text-slate-200" data-icon="mdi:cash-off"></span>
                                <p class="text-slate-400 font-semibold">Belum ada data pengeluaran</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expenses->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $expenses->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
