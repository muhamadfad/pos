@extends('layouts.admin')

@section('title', 'Rincian Saldo Bersih')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="{{ route('admin.finance.index') }}"
                    class="text-slate-400 hover:text-slate-600 transition-colors">
                    <span class="iconify text-xl" data-icon="mdi:arrow-left"></span>
                </a>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">Rincian Saldo Bersih</h1>
            </div>
            <p class="text-slate-500 text-sm ml-9">
                Saldo Bersih = Pendapatan Penjualan ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã¢â‚¬Â¹ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ Bayar Supplier ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã¢â‚¬Â¹ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ Pengeluaran Operasional
            </p>
        </div>
        <div class="flex items-center gap-2 p-1 bg-slate-100 rounded-2xl">
            <a href="{{ route('admin.finance.index') }}"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all text-slate-500 hover:text-slate-700">Ringkasan</a>
            <a href="{{ route('admin.finance.daily') }}"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all text-slate-500 hover:text-slate-700">Harian</a>
            <a href="{{ route('admin.finance.weekly') }}"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all text-slate-500 hover:text-slate-700">Mingguan</a>
            <a href="{{ route('admin.finance.monthly') }}"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all text-slate-500 hover:text-slate-700">Bulanan</a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        {{-- Pendapatan --}}
        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-7">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 flex items-center justify-center">
                    <span class="iconify text-emerald-600 text-xl" data-icon="mdi:trending-up"></span>
                </div>
                <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Pendapatan</p>
            </div>
            <p class="text-2xl font-black text-slate-900">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-400 mt-1">Total dari penjualan</p>
        </div>

        {{-- Bayar Supplier --}}
        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-7">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-2xl bg-orange-50 flex items-center justify-center">
                    <span class="iconify text-orange-500 text-xl" data-icon="mdi:truck-outline"></span>
                </div>
                <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Bayar Supplier</p>
            </div>
            <p class="text-2xl font-black text-slate-900">Rp {{ number_format($totalSupplierPaid, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-400 mt-1">Total sudah dibayarkan</p>
        </div>

        {{-- Operasional --}}
        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-7">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-2xl bg-rose-50 flex items-center justify-center">
                    <span class="iconify text-rose-500 text-xl" data-icon="mdi:cash-remove"></span>
                </div>
                <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Operasional</p>
            </div>
            <p class="text-2xl font-black text-slate-900">Rp {{ number_format($totalOperasional, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-400 mt-1">Biaya listrik, gaji, dll</p>
        </div>

        {{-- Net Cashflow --}}
        @php $isPositive = $netCashflow >= 0; @endphp
        <div class="{{ $isPositive ? 'bg-indigo-600' : 'bg-rose-600' }} rounded-[2rem] shadow-xl p-7">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center">
                    <span class="iconify text-white text-xl"
                        data-icon="{{ $isPositive ? 'mdi:piggy-bank' : 'mdi:alert-circle-outline' }}"></span>
                </div>
                <p class="text-[11px] font-black text-white/60 uppercase tracking-widest">Saldo Bersih</p>
            </div>
            <p class="text-2xl font-black text-white">Rp {{ number_format(abs($netCashflow), 0, ',', '.') }}</p>
            <p class="text-xs text-white/60 mt-1">{{ $isPositive ? 'Surplus' : 'Defisit' }}</p>
        </div>
        {{-- Keuntungan Bersih --}}
            <div class="bg-yellow-500 rounded-[2rem] shadow-xl p-7">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center">
                        <span class="iconify text-white text-xl" data-icon="mdi:cash-multiple"></span>
                    </div>
                    <p class="text-[11px] font-black text-white/60 uppercase tracking-widest">Keuntungan Bersih</p>
                </div>
                <p class="text-2xl font-black text-white">Rp {{ number_format($totalProfit, 0, ',', '.') }}</p>
                <p class="text-xs text-white/60 mt-1">Pendapatan ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã¢â‚¬Â¹ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ HPP</p>
            </div>
    </div>

    {{-- Formula Visual --}}
    <div class="bg-slate-50 border border-slate-200 rounded-[2rem] p-6">
        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Formula Perhitungan</p>
        <div class="flex flex-wrap items-center gap-3 text-sm font-bold">
            <span class="px-4 py-2 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl">
                Rp {{ number_format($totalIncome, 0, ',', '.') }} <span
                    class="font-normal text-emerald-500">(Pendapatan)</span>
            </span>
            <span class="text-slate-400 text-lg">ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã¢â‚¬Â¹ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢</span>
            <span class="px-4 py-2 bg-orange-50 text-orange-700 border border-orange-200 rounded-xl">
                Rp {{ number_format($totalSupplierPaid, 0, ',', '.') }} <span
                    class="font-normal text-orange-500">(Supplier)</span>
            </span>
            <span class="text-slate-400 text-lg">ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã¢â‚¬Â¹ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢</span>
            <span class="px-4 py-2 bg-rose-50 text-rose-700 border border-rose-200 rounded-xl">
                Rp {{ number_format($totalOperasional, 0, ',', '.') }} <span
                    class="font-normal text-rose-500">(Operasional)</span>
            </span>
            <span class="text-slate-400 text-lg">=</span>
            <span
                class="px-4 py-2 {{ $isPositive ? 'bg-indigo-600 text-white' : 'bg-rose-600 text-white' }} rounded-xl">
                Rp {{ number_format($netCashflow, 0, ',', '.') }}
            </span>
        </div>
    </div>

    {{-- Chart per Bulan --}}
    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-8 py-5 border-b border-slate-100">
            <h3 class="font-black text-slate-800 text-sm uppercase tracking-widest">Tren 12 Bulan Terakhir</h3>
        </div>
        <div class="overflow-x-auto p-6">
            <table class="w-full text-sm min-w-[600px]">
                <thead>
                    <tr
                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                        <th class="pb-3 text-left px-2">Bulan</th>
                        <th class="pb-3 text-right px-2">Pendapatan</th>
                        <th class="pb-3 text-right px-2">Bayar Supplier</th>
                        <th class="pb-3 text-right px-2">Operasional</th>
                        <th class="pb-3 text-right px-2">Saldo Bersih</th>
                        <th class="pb-3 text-left px-2 w-40">Bar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @php
                    $maxNet = $months->max(fn($m) => abs($m['net'])) ?: 1;
                    $maxIncome = $months->max(fn($m) => $m['income']) ?: 1;
                    @endphp
                    @foreach($months as $month)
                    @php $pos = $month['net'] >= 0; @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-3 px-2 font-bold text-slate-700">{{ $month['label'] }}</td>
                        <td class="py-3 px-2 text-right text-emerald-600 font-semibold">
                            Rp {{ number_format($month['income'], 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-2 text-right text-orange-500 font-semibold">
                            Rp {{ number_format($month['supplier'], 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-2 text-right text-rose-500 font-semibold">
                            Rp {{ number_format($month['operasional'], 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-2 text-right font-black {{ $pos ? 'text-indigo-600' : 'text-rose-600' }}">
                            Rp {{ number_format($month['net'], 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-2">
                            @php $pct = min(100, abs($month['net']) / $maxNet * 100); @endphp
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="h-2 rounded-full {{ $pos ? 'bg-indigo-500' : 'bg-rose-400' }}"
                                    style="width: {{ $pct }}%"></div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Bottom Grid: Expense per Kategori + Top Supplier --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Expense per Kategori --}}
        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-8 py-5 border-b border-slate-100">
                <h3 class="font-black text-slate-800 text-sm uppercase tracking-widest">Pengeluaran per Kategori</h3>
            </div>
            <div class="p-6">
                @if($expenseByCategory->isEmpty())
                <p class="text-sm text-slate-400 text-center py-6 italic">Belum ada data pengeluaran operasional.</p>
                @else
                @php $totalCat = $expenseByCategory->sum('total') ?: 1; @endphp
                <div class="space-y-3">
                    @foreach($expenseByCategory as $cat)
                    @php $pct = $cat->total / $totalCat * 100; @endphp
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-bold text-slate-700 capitalize">{{ $cat->category ?? 'Lainnya'
                                }}</span>
                            <span class="text-sm font-black text-slate-900">Rp {{ number_format($cat->total, 0, ',',
                                '.') }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2">
                            <div class="h-2 rounded-full bg-rose-400" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- Top Supplier Purchases --}}
        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-black text-slate-800 text-sm uppercase tracking-widest">Pembayaran Terbesar ke Supplier
                </h3>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($topSupplierPurchases as $purchase)
                <a href="{{ route('admin.purchases.show', $purchase->id) }}"
                    class="flex items-center justify-between px-6 py-4 hover:bg-slate-50 transition-colors group">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-orange-50 flex items-center justify-center shrink-0">
                            <span class="iconify text-orange-500" data-icon="mdi:truck-outline"></span>
                        </div>
                        <div>
                            <p class="font-bold text-slate-800 text-sm">{{ $purchase->supplier->name ?? 'ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â' }}</p>
                            <p class="text-[11px] text-slate-400">
                                {{ \Carbon\Carbon::parse($purchase->date)->format('d M Y') }}
                                &nbsp;ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢&nbsp;
                                <span
                                    class="font-bold {{ $purchase->payment_status === 'paid' ? 'text-emerald-500' : 'text-orange-500' }}">
                                    {{ $purchase->payment_status === 'paid' ? 'Lunas' : ($purchase->payment_status ===
                                    'partial' ? 'Sebagian' : 'Belum Bayar') }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-black text-slate-900 text-sm">Rp {{ number_format($purchase->total_paid, 0, ',',
                            '.') }}</p>
                        <p class="text-[10px] text-slate-400">dari Rp {{ number_format($purchase->total_amount, 0, ',',
                            '.') }}</p>
                    </div>
                </a>
                @empty
                <p class="text-sm text-slate-400 text-center py-8 italic">Belum ada data pembelian.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
