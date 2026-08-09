@extends('layouts.admin')

@section('title', 'Pilih Supplier')

@section('content')
<div class="max-w-[1200px] mx-auto p-6 font-sans antialiased text-slate-900" x-data="{ search: '' }">

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-8 h-[2px] bg-indigo-600"></span>
                <span class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Step 01 // Selection</span>
            </div>
            <h1 class="text-4xl font-black tracking-tighter text-slate-900 uppercase italic">Select Supplier</h1>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1">Tentukan mitra untuk entri pembelian baru</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.purchases.create-no-supplier') }}"
                class="h-10 px-5 flex items-center gap-3 bg-indigo-50 border border-indigo-100 text-indigo-600 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-indigo-100 transition-all active:scale-95 shadow-sm">
                <span class="iconify text-lg" data-icon="lucide:arrow-right"></span>
                Lanjutkan Tanpa Supplier
            </a>
            <a href="{{ route('admin.purchases.index') }}"
                class="h-10 px-5 flex items-center gap-3 bg-white border border-slate-200 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all active:scale-95 shadow-sm">
                <span class="iconify text-lg" data-icon="lucide:arrow-left"></span>
                Back to Registry
            </a>
        </div>
    </div>

    <div class="mb-8 relative group">
        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-600 transition-colors">
            <span class="iconify text-xl" data-icon="lucide:search"></span>
        </div>
        <input type="text" x-model="search"
            class="block w-full pl-14 pr-6 py-5 bg-white border-2 border-slate-100 rounded-2xl text-sm font-bold text-slate-800 placeholder:text-slate-300 placeholder:font-medium focus:outline-none focus:border-indigo-600 transition-all shadow-sm shadow-slate-100/50"
            placeholder="TYPE SUPPLIER NAME / ID...">
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto pb-2 custom-scrollbar">
            <table class="w-full border-collapse">
                <thead class="bg-slate-50/50 border-b border-slate-100">
                    <tr>
                        <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Supplier Entity</th>
                        <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Communication</th>
                        <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Location Data</th>
                        <th class="px-8 py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($suppliers as $supplier)
                    <tr class="group hover:bg-slate-50/50 transition-colors duration-150"
                        x-show="search === '' || '{{ strtolower($supplier->name) }}'.includes(search.toLowerCase())">
                        
                        <td class="px-8 py-6">
                            <div class="flex flex-col">
                                <span class="text-sm font-black text-slate-900 tracking-tight leading-none mb-1 group-hover:text-indigo-600 transition-colors italic uppercase">
                                    {{ $supplier->name }}
                                </span>
                                <span class="text-[10px] font-bold text-slate-400 tracking-[0.1em]">
                                    ID: SUP/{{ str_pad($supplier->id, 4, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>
                        </td>

                        <td class="px-8 py-6">
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-100 rounded text-[11px] font-black text-slate-600 border border-transparent group-hover:border-slate-200 transition-all">
                                <span class="iconify" data-icon="lucide:phone"></span>
                                {{ $supplier->phone ?? 'NO_CONTACT' }}
                            </div>
                        </td>

                        <td class="px-8 py-6">
                            <p class="text-[11px] font-bold text-slate-500 max-w-xs truncate italic">
                                {{ $supplier->address ?? '// ADDRESS_NOT_SET' }}
                            </p>
                        </td>

                        <td class="px-8 py-6 text-right">
                            <a href="{{ route('admin.purchases.per-supplier', $supplier->id) }}"
                                class="inline-flex items-center gap-3 px-5 py-2.5 bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-lg hover:bg-indigo-600 transition-all active:scale-95 shadow-lg shadow-slate-100">
                                Select
                                <span class="iconify text-lg" data-icon="lucide:arrow-right"></span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-24 text-center">
                            <span class="text-[10px] font-black text-slate-300 uppercase tracking-[0.5em]">No_Supplier_Entities_Available</span>
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
