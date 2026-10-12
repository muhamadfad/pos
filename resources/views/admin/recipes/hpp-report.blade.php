@extends('layouts.admin')

@section('title', 'Laporan HPP Menu')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<div class="p-2 lg:p-4 max-w-[1600px] mx-auto space-y-6" style="font-family: 'Inter', sans-serif;">
    
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <nav class="flex mb-2 text-xs font-medium text-gray-500 uppercase tracking-wider">
                <span>Reports</span>
                <span class="mx-2">/</span>
                <span class="text-black">HPP Analysis</span>
            </nav>
            <h1 class="text-3xl font-bold text-black tracking-tight">Analisis HPP & Margin</h1>
            <p class="text-sm text-gray-500 mt-1">Perhitungan Harga Pokok Penjualan per menu</p>
        </div>
        
        <div class="flex items-center gap-3">
            <button onclick="window.print()" 
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-50 border border-gray-200 text-slate-700 text-sm font-semibold rounded-md hover:bg-white transition-all shadow-sm">
                <span class="iconify text-lg" data-icon="lucide:printer"></span>
                Print
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-gray-50 rounded-md border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Menu</p>
                    <p class="text-2xl font-semibold text-black mt-1">{{ number_format($total_products) }}</p>
                </div>
                <div class="w-10 h-10 bg-gray-50 rounded-md flex items-center justify-center">
                    <span class="iconify text-xl text-black" data-icon="lucide:utensils"></span>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-50 rounded-md border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Rata-rata Margin</p>
                    <p class="text-2xl font-bold {{ number_format($average_margin) >= 50 ? 'text-black' : (number_format($average_margin) >= 30 ? 'text-black' : 'text-black') }} mt-1">
                        {{ number_format($average_margin, 1) }}%
                    </p>
                </div>
                <div class="w-10 h-10 bg-white border border-gray-200 rounded-md flex items-center justify-center">
                    <span class="iconify text-xl text-black" data-icon="lucide:trending-up"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel HPP -->
    <div class="bg-gray-50 rounded-md border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-gray-200">
                        <th class="px-4 py-2 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Menu</th>
                        <th class="px-4 py-2 text-[11px] font-semibold text-gray-500 uppercase tracking-wider text-right">Harga Jual</th>
                        <th class="px-4 py-2 text-[11px] font-semibold text-gray-500 uppercase tracking-wider text-right">HPP</th>
                        <th class="px-4 py-2 text-[11px] font-semibold text-gray-500 uppercase tracking-wider text-right">Margin</th>
                        <th class="px-4 py-2 text-[11px] font-semibold text-gray-500 uppercase tracking-wider text-center">Margin %</th>
                        <th class="px-4 py-2 text-[11px] font-semibold text-gray-500 uppercase tracking-wider text-center">Status</th>
                        <th class="px-4 py-2 text-[11px] font-semibold text-gray-500 uppercase tracking-wider text-right">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($products as $product)
                    @php
                        $marginPercent = $product['margin_percent'];
                        $statusClass = $marginPercent >= 50 ? 'bg-gray-50 border border-gray-200 text-black' : 
                                      ($marginPercent >= 30 ? 'bg-gray-50 border border-gray-200 text-black' : 
                                      ($marginPercent >= 15 ? 'bg-gray-50 text-black' : 'bg-rose-100 text-rose-700'));
                        $statusText = $marginPercent >= 50 ? 'Sangat Untung' : 
                                     ($marginPercent >= 30 ? 'Untung' : 
                                     ($marginPercent >= 15 ? 'Cukup' : 'Rendah'));
                    @endphp
                    <tr class="group hover:bg-white transition-colors">
                        <td class="px-4 py-2">
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-black">{{ $product['name'] }}</span>
                                <span class="text-[11px] text-gray-500">{{ $product['category'] }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-2 text-right font-semibold text-black">
                            Rp {{ number_format($product['selling_price'], 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-2 text-right text-rose-600 font-medium">
                            Rp {{ number_format($product['hpp'], 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-2 text-right text-black font-medium">
                            Rp {{ number_format($product['margin'], 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-2 text-center font-bold">
                            {{ number_format($product['margin_percent'], 1) }}%
                        </td>
                        <td class="px-4 py-2 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-right">
                            <button onclick="showDetail({{ json_encode($product) }})" 
                                class="p-2 text-black hover:bg-gray-50 rounded-md transition-all">
                                <span class="iconify text-lg" data-icon="lucide:eye"></span>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div id="detailModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-gray-50 rounded-md shadow-sm max-w-2xl w-full max-h-[80vh] overflow-hidden">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-black" id="modalTitle"></h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-600">
                <span class="iconify text-xl" data-icon="lucide:x"></span>
            </button>
        </div>
        <div class="p-6 overflow-y-auto" id="modalContent"></div>
        <div class="p-6 border-t border-gray-200 bg-white">
            <button onclick="closeModal()" class="w-full py-3 bg-slate-600 text-white font-semibold rounded-md hover:bg-slate-700 transition-all">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    function showDetail(product) {
        document.getElementById('modalTitle').innerHTML = product.name;
        
        let ingredientsHtml = '<h4 class="font-semibold text-slate-700 mb-3">Komposisi Bahan Baku:</h4>';
        ingredientsHtml += '<table class="w-full text-sm">';
        ingredientsHtml += '<thead><tr class="border-b"><th class="text-left py-2">Bahan</th><th class="text-right py-2">Jumlah</th><th class="text-right py-2">Harga</th><th class="text-right py-2">Subtotal</th></tr></thead>';
        ingredientsHtml += '<tbody>';
        
        product.ingredients.forEach(ing => {
            ingredientsHtml += `
                <tr class="border-b border-gray-200">
                    <td class="py-2">${ing.name}</td>
                    <td class="py-2 text-right">${ing.quantity}</td>
                    <td class="py-2 text-right">Rp ${new Intl.NumberFormat('id-ID').format(ing.unit_cost)}</td>
                    <td class="py-2 text-right">Rp ${new Intl.NumberFormat('id-ID').format(ing.subtotal)}</td>
                </tr>
            `;
        });
        
        ingredientsHtml += '</tbody></table>';
        
        ingredientsHtml += `
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-600">Total HPP:</span>
                    <span class="font-bold text-rose-600">Rp ${new Intl.NumberFormat('id-ID').format(product.hpp)}</span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-600">Harga Jual:</span>
                    <span class="font-bold text-black">Rp ${new Intl.NumberFormat('id-ID').format(product.selling_price)}</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-dashed border-gray-200">
                    <span class="font-semibold text-slate-700">Margin Keuntungan:</span>
                    <span class="font-bold text-black">Rp ${new Intl.NumberFormat('id-ID').format(product.margin)} (${product.margin_percent}%)</span>
                </div>
            </div>
        `;
        
        document.getElementById('modalContent').innerHTML = ingredientsHtml;
        document.getElementById('detailModal').classList.remove('hidden');
    }
    
    function closeModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }
    
    // Tutup modal klik di luar
    document.getElementById('detailModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>
@endsection
