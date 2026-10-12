@extends('layouts.admin')

@section('title', 'Pilih Supplier')

@section('content')
<div class="max-w-[1200px] mx-auto p-6 font-sans antialiased text-black" x-data="{ search: '' }">

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-8 h-[2px] bg-black"></span>
                <span class="text-[10px] font-semibold uppercase tracking-[0.3em] text-gray-500">Step 01 // Selection</span>
            </div>
            <h1 class="text-3xl font-semibold tracking-tighter text-black uppercase italic">Select Supplier</h1>
            <p class="text-[11px] font-bold text-gray-500 uppercase tracking-widest mt-1">Tentukan mitra untuk entri pembelian baru</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.purchases.create-no-supplier') }}"
                class="h-10 px-5 flex items-center gap-3 bg-gray-50 border border-gray-200 text-black rounded-md text-[10px] font-semibold uppercase tracking-widest hover:bg-gray-50 transition-all active:scale-95 shadow-sm">
                <span class="iconify text-lg" data-icon="lucide:arrow-right"></span>
                Lanjutkan Tanpa Supplier
            </a>
            <a href="{{ route('admin.purchases.index') }}"
                class="h-10 px-5 flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-md text-[10px] font-semibold uppercase tracking-widest hover:bg-white transition-all active:scale-95 shadow-sm">
                <span class="iconify text-lg" data-icon="lucide:arrow-left"></span>
                Back to Registry
            </a>
        </div>
    </div>

    <div class="mb-8 relative group">
        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-500 group-focus-within:text-black transition-colors">
            <span class="iconify text-xl" data-icon="lucide:search"></span>
        </div>
        <input type="text" x-model="search"
            class="block w-full pl-14 pr-6 py-5 bg-gray-50 border-2 border-gray-200 rounded-md text-sm font-bold text-black placeholder:text-slate-300 placeholder:font-medium focus:outline-none focus:border-gray-200 transition-all shadow-sm"
            placeholder="TYPE SUPPLIER NAME / ID...">
    </div>

    <div class="bg-gray-50 border border-gray-200 rounded-md shadow-sm overflow-hidden">
        <div class="overflow-x-auto pb-2 custom-scrollbar">
            <table class="w-full border-collapse">
                <thead class="bg-white border-b border-gray-200">
                    <tr>
                        <th class="px-8 py-5 text-left text-[10px] font-semibold text-gray-500 uppercase tracking-[0.2em]">Supplier Entity</th>
                        <th class="px-8 py-5 text-left text-[10px] font-semibold text-gray-500 uppercase tracking-[0.2em]">Communication</th>
                        <th class="px-8 py-5 text-left text-[10px] font-semibold text-gray-500 uppercase tracking-[0.2em]">Location Data</th>
                        <th class="px-8 py-5 text-right text-[10px] font-semibold text-gray-500 uppercase tracking-[0.2em]">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($suppliers as $supplier)
                    <tr class="group hover:bg-white transition-colors duration-150"
                        x-show="search === '' || '{{ strtolower($supplier->name) }}'.includes(search.toLowerCase())">
                        
                        <td class="px-8 py-6">
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-black tracking-tight leading-none mb-1 group-hover:text-black transition-colors italic uppercase">
                                    {{ $supplier->name }}
                                </span>
                                <span class="text-[10px] font-bold text-gray-500 tracking-[0.1em]">
                                    ID: SUP/{{ str_pad($supplier->id, 4, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>
                        </td>

                        <td class="px-8 py-6">
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-100 rounded text-[11px] font-semibold text-gray-600 border border-transparent group-hover:border-gray-200 transition-all">
                                <span class="iconify" data-icon="lucide:phone"></span>
                                {{ $supplier->phone ?? 'NO_CONTACT' }}
                            </div>
                        </td>

                        <td class="px-8 py-6">
                            <p class="text-[11px] font-bold text-gray-500 max-w-xs truncate italic">
                                {{ $supplier->address ?? '// ADDRESS_NOT_SET' }}
                            </p>
                        </td>

                        <td class="px-8 py-6 text-right">
                            <a href="{{ route('admin.purchases.per-supplier', $supplier->id) }}"
                                class="inline-flex items-center gap-3 px-5 py-2.5 bg-[#001529] text-white text-[10px] font-semibold uppercase tracking-[0.2em] rounded-md hover:bg-black transition-all active:scale-95 shadow-sm">
                                Select
                                <span class="iconify text-lg" data-icon="lucide:arrow-right"></span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-24 text-center">
                            <span class="text-[10px] font-semibold text-slate-300 uppercase tracking-[0.5em]">No_Supplier_Entities_Available</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    /* Custom Scrollbar Industrial */
    .custom-scrollbar::-webkit-scrollbar { height: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    
    /* Clean Table */
    table { border-spacing: 0; }
    tr { outline: none; }
</style>
@endsection
