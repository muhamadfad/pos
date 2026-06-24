@extends('layouts.admin')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="space-y-8">

    <!-- Branding & Range -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                <span class="p-2 bg-indigo-600 text-white rounded-2xl shadow-lg shadow-indigo-100">
                    <span class="iconify" data-icon="mdi:finance"></span>
                </span>
                Overview Keuangan
            </h1>
            <p class="text-slate-500 font-medium">Monitoring pergerakan kas dan profitabilitas toko</p>
        </div>

        <div class="flex items-center gap-3">
            <!-- Branch Filter (Hanya untuk Super Admin) -->
            @if($isSuperAdmin)
            <select id="branchFilter" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-indigo-500">
                <option value="">🌍 Semua Cabang</option>
                @foreach($branches as $branch)
                <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>
                    <span class="iconify inline-block" data-icon="mdi:store-outline"></span> {{ $branch->name }}
                </option>
                @endforeach
            </select>
            @endif
            
            <div class="flex items-center gap-2 p-1 bg-slate-100 rounded-2xl">
                <a href="{{ route('admin.finance.index') }}"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.finance.index') ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Ringkasan</a>
                <a href="{{ route('admin.finance.daily') }}"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.finance.daily') ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Harian</a>
                <a href="{{ route('admin.finance.weekly') }}"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.finance.weekly') ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Mingguan</a>
                <a href="{{ route('admin.finance.monthly') }}"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.finance.monthly') ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Bulanan</a>
            </div>
        </div>
    </div>
    
    <!-- Info Cabang -->
    <div class="bg-indigo-50 rounded-2xl p-3 px-5 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="iconify text-indigo-600" data-icon="mdi:store"></span>
            <span class="text-sm font-semibold text-indigo-800">Cabang: {{ $currentBranchName }}</span>
        </div>
        @if($isSuperAdmin && $branchId)
        <a href="{{ route('admin.finance.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800">Reset Filter</a>
        @endif
    </div>

    <!-- Stats Grid (3 kolom: Pendapatan | Operasional | Supplier, lalu Net span-full) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Income Card -->
        <div
            class="relative overflow-hidden bg-white p-7 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md transition-all group">
            <div
                class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 group-hover:scale-110 transition-transform">
            </div>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 relative">Total Pendapatan
            </p>
            <div class="flex items-baseline gap-1 relative">
                <span class="text-sm font-bold text-emerald-500">Rp</span>
                <span class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($totalIncome, 0, ',',
                    '.') }}</span>
            </div>
            <div
                class="mt-4 flex items-center gap-2 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full w-fit">
                <span class="iconify" data-icon="mdi:trending-up"></span>
                Cash-In
            </div>
        </div>

        <!-- Expense Card -->
        <div
            class="relative overflow-hidden bg-white p-7 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md transition-all group">
            <div
                class="absolute -right-4 -top-4 w-24 h-24 bg-rose-50 rounded-full opacity-50 group-hover:scale-110 transition-transform">
            </div>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 relative">Pengeluaran
                Operasional
            </p>
            <div class="flex items-baseline gap-1 relative">
                <span class="text-sm font-bold text-rose-500">Rp</span>
                <span class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($totalExpense, 0, ',',
                    '.') }}</span>
            </div>
            <div
                class="mt-4 flex items-center gap-2 text-[10px] font-bold text-rose-600 bg-rose-50 px-3 py-1 rounded-full w-fit">
                <span class="iconify" data-icon="mdi:cash-remove"></span>
                Listrik, Gaji, dll
            </div>
        </div>

        <!-- Supplier Paid Card -->
        <div
            class="relative overflow-hidden bg-white p-7 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md transition-all group">
            <div
                class="absolute -right-4 -top-4 w-24 h-24 bg-orange-50 rounded-full opacity-50 group-hover:scale-110 transition-transform">
            </div>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 relative">Bayar Supplier
            </p>
            <div class="flex items-baseline gap-1 relative">
                <span class="text-sm font-bold text-orange-500">Rp</span>
                <span class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($totalSupplierPaid, 0,
                    ',', '.') }}</span>
            </div>
            <div
                class="mt-4 flex items-center gap-2 text-[10px] font-bold text-orange-600 bg-orange-50 px-3 py-1 rounded-full w-fit">
                <span class="iconify" data-icon="mdi:truck-outline"></span>
                Pembelian Stok
            </div>
        </div>

        <!-- Net Balance (clickable → detail) -->
        @php $isPositive = $netCashflow >= 0; @endphp
        <a href="{{ route('admin.finance.cashflow') . ($branchId ? '?branch_id=' . $branchId : '') }}"
            class="relative overflow-hidden {{ $isPositive ? 'bg-indigo-600 shadow-indigo-100' : 'bg-rose-600 shadow-rose-100' }} p-7 rounded-[2rem] shadow-xl hover:scale-[1.02] active:scale-[0.99] transition-all group md:col-span-3 block">
            <div class="absolute right-0 bottom-0 opacity-10">
                <span class="iconify text-9xl text-white"
                    data-icon="{{ $isPositive ? 'mdi:piggy-bank' : 'mdi:alert-circle' }}"></span>
            </div>
            <div
                class="absolute top-5 right-6 flex items-center gap-1 text-white/50 text-[10px] font-bold uppercase tracking-widest">
                <span>Lihat Rincian</span>
                <span class="iconify group-hover:translate-x-1 transition-transform" data-icon="mdi:arrow-right"></span>
            </div>
            <p class="text-[11px] font-black text-white/60 uppercase tracking-[0.2em] mb-3">Saldo Bersih (Net Cashflow)
            </p>
            <div class="flex items-baseline gap-1">
                <span class="text-sm font-bold text-white/70">Rp</span>
                <span class="text-4xl font-black text-white tracking-tighter">{{ number_format($netCashflow, 0, ',',
                    '.') }}</span>
            </div>
            <p class="text-white/50 text-[10px] mt-4 font-medium">
                Pendapatan − Bayar Supplier − Operasional &nbsp;•&nbsp; {{ $isPositive ? 'Surplus' : 'Defisit' }}
            </p>
        </a>

    </div>

    <!-- Quick Actions / Reports -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200 shadow-sm p-8">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-lg font-black text-slate-800 tracking-tight uppercase text-sm">Informasi Keuangan</h3>
                <span class="iconify text-slate-300 text-xl" data-icon="mdi:information-outline"></span>
            </div>
            <div class="space-y-6">
                <div class="flex items-start gap-5">
                    <div
                        class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center shrink-0">
                        <span class="iconify text-indigo-500 text-2xl" data-icon="mdi:cash-fast"></span>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800">Manajemen Arus Kas</h4>
                        <p class="text-sm text-slate-500 leading-relaxed">System secara otomatis merekam setiap
                            transaksi penjualan (Cash-In) dan pembelian serta operasional (Cash-Out) untuk akurasi
                            laporan keuangan Anda.</p>
                    </div>
                </div>
                <div class="flex items-start gap-5">
                    <div
                        class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center shrink-0">
                        <span class="iconify text-emerald-500 text-2xl" data-icon="mdi:file-chart-outline"></span>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800">Laporan Periodik</h4>
                        <p class="text-sm text-slate-500 leading-relaxed">Gunakan filter harian, mingguan, atau bulanan
                            di pojok kanan atas untuk melihat performa toko dalam rentang waktu tertentu secara lebih
                            mendetail.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-indigo-50/50 rounded-3xl border border-indigo-100 p-8">
            <h3 class="text-sm font-black text-indigo-700 tracking-widest uppercase mb-6">Navigasi Cepat</h3>
            <div class="grid grid-cols-1 gap-3">
                <a href="{{ route('admin.purchase-payments.index') . ($branchId ? '?branch_id=' . $branchId : '') }}"
                    class="flex items-center justify-between p-4 bg-white rounded-2xl border border-indigo-100 hover:border-indigo-300 hover:shadow-sm transition-all text-sm font-bold text-indigo-700 group">
                    Kontrol Hutang
                    <span class="iconify group-hover:translate-x-1 transition-transform"
                        data-icon="mdi:chevron-right"></span>
                </a>
                <a href="{{ route('admin.order-payments.index') . ($branchId ? '?branch_id=' . $branchId : '') }}"
                    class="flex items-center justify-between p-4 bg-white rounded-2xl border border-indigo-100 hover:border-indigo-300 hover:shadow-sm transition-all text-sm font-bold text-indigo-700 group">
                    Kontrol Piutang
                    <span class="iconify group-hover:translate-x-1 transition-transform"
                        data-icon="mdi:chevron-right"></span>
                </a>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.getElementById('branchFilter')?.addEventListener('change', function() {
        const branchId = this.value;
        const url = new URL(window.location.href);
        if (branchId) {
            url.searchParams.set('branch_id', branchId);
        } else {
            url.searchParams.delete('branch_id');
        }
        window.location.href = url.toString();
    });
</script>
@endpush
@endsection