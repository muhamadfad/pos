@extends('layouts.admin')

@section('title', 'Detail Menu')

@section('content')
<div class="max-w-[1200px] mx-auto space-y-6 pb-12">
    
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.products.index') }}" 
               class="inline-flex items-center justify-center w-9 h-9 bg-gray-50 border border-gray-200 rounded-md text-gray-500 hover:bg-white transition-colors shadow-sm">
                <span class="iconify" data-icon="lucide:arrow-left"></span>
            </a>
            <div>
                <nav class="flex items-center gap-2 text-xs font-medium text-gray-500 mb-0.5">
                    <span class="hover:text-gray-600 transition-colors cursor-pointer">Inventory</span>
                    <span class="iconify text-[10px]" data-icon="lucide:chevron-right"></span>
                    <span class="text-black">{{ $product->barcode }}</span>
                </nav>
                <h1 class="text-lg font-semibold text-black tracking-tight">Detail Menu</h1>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.products.edit', $product) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-[#001529] text-white text-sm font-semibold rounded-md hover:bg-[#001529] transition-all shadow-sm">
                <span class="iconify" data-icon="lucide:edit-3"></span>
                Edit Menu
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-gray-50 border border-gray-200 rounded-md shadow-sm overflow-hidden">
                <div class="p-6">
                    <div class="inline-flex items-center px-2 py-1 rounded-md bg-gray-50 text-black text-[10px] font-bold uppercase tracking-wider mb-4 border border-gray-200">
                        {{ $product->category->name ?? 'Uncategorized' }}
                    </div>
                    <h2 class="text-2xl font-bold text-black leading-tight mb-1">{{ $product->name }}</h2>
                    <p class="text-sm font-mono text-gray-500 tracking-tight">{{ $product->barcode ?? '-' }}</p>
                </div>
                {{-- HAPUS BAGIAN STATUS STOK --}}
            </div>

            <div class="bg-gray-50 border border-gray-200 rounded-md shadow-sm p-6 space-y-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest">Pricing Strategy</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Harga Beli Avg</span>
                        <span class="font-semibold text-black italic">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Harga Jual</span>
                        <span class="font-semibold text-black">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                    </div>
                    <div class="pt-3 border-t border-dashed border-gray-200 flex justify-between items-center">
                        <span class="text-xs font-bold text-black uppercase">Profit Margin</span>
                        <span class="text-base font-semibold text-black">Rp {{ number_format($product->selling_price - $product->purchase_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-8">
            <div x-data="{ tab: 'outgoing' }" class="bg-gray-50 border border-gray-200 rounded-md shadow-sm overflow-hidden min-h-[500px]">
                <div class="flex border-b border-gray-200 px-6">
                    <button @click="tab = 'outgoing'" 
                        :class="tab === 'outgoing' ? 'border-b-2 border-slate-900 text-black' : 'text-gray-500 hover:text-gray-600'" 
                        class="px-4 py-4 text-sm font-semibold transition-all">
                        Riwayat Penjualan
                    </button>

                </div>

                <div class="p-0">
                    <div x-show="tab === 'outgoing'" x-transition.opacity>
                        <table class="w-full text-left">
                            <thead class="bg-white text-[11px] font-bold text-gray-500 uppercase border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-2">Tanggal</th>
                                    <th class="px-4 py-2">Ref/Customer</th>
                                    <th class="px-4 py-2 text-center">Quantity</th>
                                    <th class="px-4 py-2 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-sm">
                                @forelse($outgoing as $item)
                                <tr class="hover:bg-white transition-colors">
                                    <td class="px-4 py-2 text-gray-500 font-medium">
                                        {{ optional($item->order->date ?? $item->order->created_at)->format('d M Y') }}
                                    </td>
                                    <td class="px-4 py-2">
                                        <span class="font-semibold text-slate-700">{{ $item->order->customer->name ?? 'General Sale' }}</span>
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <span class="inline-flex items-center text-rose-600 font-bold">
                                            -{{ $item->quantity }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-right font-semibold text-black">
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


                </div>
            </div>
        </div>
    </div>
</div>
@endsection
