@extends('layouts.admin')

@section('title', isset($customer) ? 'Edit Customer' : 'Tambah Customer')

@section('content')
<div class="max-w-xl mx-auto space-y-6 pb-12">

    <div class="flex items-center gap-4">
        <a href="{{ route('admin.customers.index') }}"
            class="inline-flex items-center justify-center w-11 h-11 rounded-2xl bg-white border border-slate-200 text-slate-400 hover:text-indigo-600 hover:border-indigo-100 transition-all shadow-sm group">
            <span class="iconify text-xl group-hover:-translate-x-1 transition-transform" data-icon="lucide:arrow-left"></span>
        </a>
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                {{ isset($customer) ? 'Edit Customer' : 'Tambah Customer' }}
            </h1>
            <p class="text-sm font-medium text-slate-500 mt-0.5">
                {{ isset($customer) ? 'Perbarui data profil pelanggan' : 'Daftarkan pelanggan baru ke sistem' }}
            </p>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-8">
            @if($errors->any())
            <div class="flex items-start gap-3 bg-rose-50 border border-rose-100 text-rose-700 rounded-2xl px-4 py-3 mb-6 text-sm">
                <span class="iconify mt-0.5 shrink-0 text-rose-500" data-icon="lucide:alert-circle"></span>
                <ul class="list-disc list-inside font-medium">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ isset($customer) ? route('admin.customers.update', $customer) : route('admin.customers.store') }}"
                method="POST" class="space-y-6">
                @csrf
                @if(isset($customer))
                    @method('PUT')
                @endif

                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                            <span class="iconify" data-icon="lucide:user"></span>
                        </div>
                        <input type="text" name="name" value="{{ old('name', $customer->name ?? '') }}"
                            placeholder="Contoh: Budi Santoso" autofocus required
                            class="w-full pl-11 pr-4 py-3 text-sm font-bold text-slate-700 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 transition-all placeholder:text-slate-300 placeholder:font-normal">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">
                        Nomor WhatsApp / HP
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                            <span class="iconify" data-icon="lucide:phone"></span>
                        </div>
                        <input type="text" name="phone" value="{{ old('phone', $customer->phone ?? '') }}"
                            placeholder="0812xxxx"
                            class="w-full pl-11 pr-4 py-3 text-sm font-bold text-slate-700 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 transition-all placeholder:text-slate-300 placeholder:font-normal">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">
                        Kategori Customer <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                            <span class="iconify" data-icon="lucide:tag"></span>
                        </div>
                        <select name="type" required
                            class="w-full pl-11 pr-10 py-3 text-sm font-bold text-slate-700 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 transition-all appearance-none bg-white">
                            <option value="retail" {{ old('type', $customer->type ?? 'retail') === 'retail' ? 'selected' : '' }}>Retail / Umum</option>
                            <option value="toko" {{ old('type', $customer->type ?? '') === 'toko' ? 'selected' : '' }}>Toko / Reseller</option>
                            <option value="project" {{ old('type', $customer->type ?? '') === 'project' ? 'selected' : '' }}>Proyek / Instansi</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                            <span class="iconify" data-icon="lucide:chevron-down"></span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Alamat Lengkap</label>
                    <div class="relative group">
                        <div class="absolute top-3.5 left-0 flex items-start pl-4 pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                            <span class="iconify" data-icon="lucide:map-pin"></span>
                        </div>
                        <textarea name="address" rows="3" placeholder="Masukkan alamat lengkap pengiriman..."
                            class="w-full pl-11 pr-4 py-3 text-sm font-medium text-slate-700 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 transition-all placeholder:text-slate-300">{{ old('address', $customer->address ?? '') }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.customers.index') }}"
                        class="px-6 py-3 text-sm font-bold text-slate-500 hover:text-slate-700 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-indigo-600 text-white text-sm font-black rounded-2xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100 active:scale-95">
                        <span class="iconify text-lg" data-icon="lucide:save"></span>
                        {{ isset($customer) ? 'Simpan Perubahan' : 'Daftarkan Customer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection