@extends('layouts.admin')

@section('title', 'Detail Customer: ' . $customer->name)

@section('content')
{{-- Inisialisasi Alpine.js --}}
<div x-data="customerDetail()" class="max-w-6xl mx-auto space-y-6 pb-12">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.customers.index') }}"
                class="inline-flex items-center justify-center w-11 h-11 rounded-2xl bg-white border border-slate-200 text-slate-400 hover:text-indigo-600 transition-all shadow-sm group">
                <span class="iconify text-xl group-hover:-translate-x-1 transition-transform" data-icon="lucide:arrow-left"></span>
            </a>
            <div>
                <nav class="flex items-center gap-2 mb-0.5">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest font-mono">ID-{{ str_pad($customer->id, 4, '0', STR_PAD_LEFT) }}</span>
                </nav>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $customer->name }}</h1>
            </div>
        </div>
        <a href="{{ route('admin.customers.edit', $customer) }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 text-slate-600 text-sm font-bold rounded-xl hover:bg-slate-50 transition shadow-sm">
            <span class="iconify" data-icon="lucide:edit-3"></span>
            Edit Profil
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-4">Informasi Kontak</p>
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500">
                        <span class="iconify font-bold" data-icon="lucide:phone"></span>
                    </div>
                    <span class="text-sm font-bold text-slate-700">{{ $customer->phone ?? '-' }}</span>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 shrink-0">
                        <span class="iconify text-sm" data-icon="lucide:map-pin"></span>
                    </div>
                    <span class="text-xs font-medium text-slate-500">{{ $customer->address ?? 'Alamat tidak tersedia' }}</span>
                </div>
            </div>
        </div>

        <div class="bg-indigo-600 rounded-3xl p-6 text-white shadow-lg shadow-indigo-100 relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 opacity-10 rotate-12">
                <span class="iconify text-9xl" data-icon="lucide:shopping-bag"></span>
            </div>
            <p class="text-[11px] font-bold text-indigo-200 uppercase tracking-widest">Total Transaksi</p>
            <div class="mt-4 flex items-baseline gap-1">
                <span class="text-4xl font-black">{{ $totalTransaksi }}</span>
                <span class="text-sm font-medium text-indigo-100">Order</span>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Total Pembelian</p>
            <p class="text-2xl font-black text-slate-900 tracking-tight mt-2">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</p>
            @if($totalPiutang > 0)
                <p class="text-xs font-bold text-rose-500 mt-2 flex items-center gap-1">
                    <span class="iconify" data-icon="lucide:alert-circle"></span>
                    Piutang: Rp {{ number_format($totalPiutang, 0, ',', '.') }}
                </p>
            @endif
        </div>
    </div>

    <div class="relative min-h-[400px]">
        {{-- Loader --}}
        <div x-show="isLoading" class="absolute inset-0 z-20 bg-white/60 backdrop-blur-[1px] flex items-center justify-center rounded-3xl">
            <span class="iconify text-4xl text-indigo-600 animate-spin" data-icon="lucide:loader-2"></span>
        </div>

        {{-- ID INI PENTING: Target update HTML --}}
        <div id="tableContent" class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            @include('admin.customers.partials.table-detail')
        </div>
    </div>
</div>

<script>
    function customerDetail() {
        return {
            isLoading: false,
            fetchData(url) {
                if (!url) return;
                this.isLoading = true;
                
                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.text())
                .then(html => {
                    document.getElementById('tableContent').innerHTML = html;
                    if (window.Iconify) window.Iconify.scan();
                    // Scroll smooth ke tabel
                    document.getElementById('tableContent').scrollIntoView({ behavior: 'smooth', block: 'start' });
                })
                .catch(err => console.error('Error fetching data:', err))
                .finally(() => {
                    this.isLoading = false;
                });
            }
        }
    }
</script>
@endsection