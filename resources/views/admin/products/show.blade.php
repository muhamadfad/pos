@extends('layouts.admin')

@section('title', 'Detail Menu')

@section('content')
<div class="max-w-[1200px] mx-auto space-y-6 pb-12">
    
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.products.index') }}" 
               class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 rounded-lg text-slate-500 hover:bg-slate-50 transition-colors shadow-sm">
                <span class="iconify" data-icon="lucide:arrow-left"></span>
            </a>
            <div>
                <nav class="flex items-center gap-2 text-xs font-medium text-slate-400 mb-0.5">
                    <span class="hover:text-slate-600 transition-colors cursor-pointer">Inventory</span>
                    <span class="iconify text-[10px]" data-icon="lucide:chevron-right"></span>
                    <span class="text-indigo-600">{{ $product->barcode }}</span>
                </nav>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight">Detail Menu</h1>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.products.edit', $product) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition-all shadow-sm">
                <span class="iconify" data-icon="lucide:edit-3"></span>
                Edit Menu
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <div class="inline-flex items-center px-2 py-1 rounded-md bg-indigo-50 text-indigo-600 text-[10px] font-bold uppercase tracking-wider mb-4 border border-indigo-100">
                        {{ $product->category->name ?? 'Uncategorized' }}
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900 leading-tight mb-1">{{ $product->name }}</h2>
                    <p class="text-sm font-mono text-slate-500 tracking-tight">{{ $product->barcode ?? '-' }}</p>
                </div>
                {{-- HAPUS BAGIAN STATUS STOK --}}
            </div>

            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 space-y-5">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Pricing Strategy</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500">Harga Beli Avg</span>
                        <span class="font-semibold text-slate-900 italic">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500">Harga Jual</span>
                        <span class="font-semibold text-slate-900">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                    </div>
                    <div class="pt-3 border-t border-dashed border-slate-200 flex justify-between items-center">
                        <span class="text-xs font-bold text-indigo-600 uppercase">Profit Margin</span>
                        <span class="text-lg font-bold text-indigo-600">Rp {{ number_format($product->selling_price - $product->purchase_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-8">
            <div x-data="{ tab: 'outgoing' }" class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden min-h-[500px]">
                <div class="flex border-b border-slate-100 px-6">
                    <button @click="tab = 'outgoing'" 
                        :class="tab === 'outgoing' ? 'border-b-2 border-slate-900 text-slate-900' : 'text-slate-400 hover:text-slate-600'" 
                        class="px-4 py-4 text-sm font-semibold transition-all">
                        Riwayat Penjualan
                    </button>
                    <button @click="tab = 'incoming'" 
                        :class="tab === 'incoming' ? 'border-b-2 border-slate-900 text-slate-900' : 'text-slate-400 hover:text-slate-600'" 
                        class="px-4 py-4 text-sm font-semibold transition-all">
                        Riwayat Menu Masuk
                    </button>
                </div>

                <div class="p-0">
                    <div x-show="tab === 'outgoing'" x-transition.opacity>
                        <table class="w-full text-left">
                            <thead class="bg-slate-50/50 text-[11px] font-bold text-slate-400 uppercase border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-3">Tanggal</th>
                                    <th class="px-6 py-3">Ref/Customer</th>
                                    <th class="px-6 py-3 text-center">Quantity</th>
                                    <th class="px-6 py-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-sm">
                                @forelse($outgoing as $item)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4 text-slate-500 font-medium">
                                        {{ optional($item->order->date ?? $item->order->created_at)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-semibold text-slate-700">{{ $item->order->customer->name ?? 'General Sale' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center text-rose-600 font-bold">
                                            -{{ $item->quantity }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-semibold text-slate-900">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center gap-2 opacity-30">
                                            <span class="iconify text-4xl" data-icon="lucide:shopping-bag"></span>
                                            <p class="text-sm font-medium">Belum ada aktivitas penjualan</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div x-show="tab === 'incoming'" x-transition.opacity>
                        <table class="w-full text-left">
                            <thead class="bg-slate-50/50 text-[11px] font-bold text-slate-400 uppercase border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-3">Tanggal</th>
                                    <th class="px-6 py-3">Ref/Supplier</th>
                                    <th class="px-6 py-3 text-center">Quantity</th>
                                    <th class="px-6 py-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-sm">
                                @forelse($incoming as $item)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4 text-slate-500 font-medium">
                                        {{ optional($item->purchase->date ?? $item->purchase->created_at)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-semibold text-slate-700">{{ $item->purchase->supplier->name ?? 'Unknown' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center text-emerald-600 font-bold">
                                            +{{ $item->quantity }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-semibold text-slate-900">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center gap-2 opacity-30">
                                            <span class="iconify text-4xl" data-icon="lucide:truck"></span>
                                            <p class="text-sm font-medium">Belum ada riwayat menu masuk</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection