@extends('layouts.admin')

@section('title', isset($supplier) ? 'Edit Supplier' : 'Tambah Supplier')

@section('content')
<div class="max-w-[800px] mx-auto space-y-6 pb-10">

    <div class="flex items-center gap-4">
        <a href="{{ route('admin.suppliers.index') }}"
            class="inline-flex items-center justify-center w-10 h-10 rounded-md bg-gray-50 border border-gray-200 text-gray-500 hover:text-black hover:border-gray-200 hover:bg-gray-50 transition-all shadow-sm group">
            <span class="iconify text-xl group-active:scale-90 transition-transform" data-icon="lucide:chevron-left"></span>
        </a>
        <div>
            <nav class="flex items-center gap-2 mb-0.5">
                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Supplier</span>
                <span class="iconify text-slate-300 text-[10px]" data-icon="lucide:chevron-right"></span>
                <span class="text-[10px] font-bold text-black uppercase tracking-widest">{{ isset($supplier) ? 'Update' : 'Registrasi' }}</span>
            </nav>
            <h1 class="text-2xl font-bold text-black tracking-tight">
                {{ isset($supplier) ? 'Edit Supplier' : 'Tambah Supplier Baru' }}
            </h1>
        </div>
    </div>

    <div class="bg-gray-50 rounded-md border border-gray-200 shadow-sm overflow-hidden">
        <div class="h-1.5 w-full bg-black"></div>

        <div class="p-8">
            @if ($errors->any())
            <div class="flex items-start gap-3 bg-rose-50 border border-rose-100 text-rose-700 rounded-md px-4 py-3 mb-8 text-sm animate-pulse">
                <span class="iconify mt-0.5 shrink-0 text-rose-500 text-lg" data-icon="lucide:alert-circle"></span>
                <div class="font-medium">
                    <p class="font-bold mb-1">Terjadi kesalahan:</p>
                    <ul class="list-disc list-inside text-xs opacity-80">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <form action="{{ isset($supplier) ? route('admin.suppliers.update', $supplier) : route('admin.suppliers.store') }}"
                method="POST" class="space-y-6">
                @csrf
                @if(isset($supplier))
                @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2 ml-1">
                            Nama Supplier <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-500 group-focus-within:text-black transition-colors">
                                <span class="iconify text-lg" data-icon="lucide:truck"></span>
                            </div>
                            <input type="text" name="name" value="{{ old('name', $supplier->name ?? '') }}"
                                placeholder="Masukkan nama perusahaan atau perorangan" autofocus required
                                class="w-full pl-11 pr-4 py-3 text-sm font-medium border rounded-md transition-all outline-none
                                {{ $errors->has('name') ? 'border-rose-300 bg-rose-50/30 focus:ring-4 focus:ring-rose-50' : 'border-gray-200 focus:border-gray-200 focus:ring-4 focus:ring-black/5' }}">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2 ml-1">
                            Nomor Telepon
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-500 group-focus-within:text-black transition-colors">
                                <span class="iconify text-lg" data-icon="lucide:phone"></span>
                            </div>
                            <input type="text" name="phone" value="{{ old('phone', $supplier->phone ?? '') }}"
                                placeholder="08xxxxxxxxxx"
                                class="w-full pl-11 pr-4 py-3 text-sm font-medium border border-gray-200 rounded-md outline-none focus:border-gray-200 focus:ring-4 focus:ring-black/5 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2 ml-1">
                            Jenis / Kategori
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-500 group-focus-within:text-black transition-colors">
                                <span class="iconify text-lg" data-icon="lucide:tag"></span>
                            </div>
                            <input type="text" name="type" value="{{ old('type', $supplier->type ?? '') }}"
                                placeholder="Contoh: Grosir, Depo, dll"
                                class="w-full pl-11 pr-4 py-3 text-sm font-medium border border-gray-200 rounded-md outline-none focus:border-gray-200 focus:ring-4 focus:ring-black/5 transition-all">
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2 ml-1">
                            Alamat Lengkap
                        </label>
                        <div class="relative group">
                            <div class="absolute top-3.5 left-4 flex items-start pointer-events-none text-gray-500 group-focus-within:text-black transition-colors">
                                <span class="iconify text-lg" data-icon="lucide:map-pin"></span>
                            </div>
                            <textarea name="address" rows="3" placeholder="Masukkan alamat lengkap supplier..."
                                class="w-full pl-11 pr-4 py-3 text-sm font-medium border border-gray-200 rounded-md outline-none focus:border-gray-200 focus:ring-4 focus:ring-black/5 transition-all shadow-sm">{{ old('address', $supplier->address ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 mt-8">
                    <a href="{{ route('admin.suppliers.index') }}"
                        class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-slate-700 hover:bg-white rounded-md transition-all active:scale-95">
                        Batalkan
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-black text-white text-sm font-bold rounded-md hover:bg-gray-800 shadow-sm  transition-all active:scale-95">
                        <span class="iconify text-lg" data-icon="lucide:save"></span>
                        {{ isset($supplier) ? 'Simpan Perubahan' : 'Daftarkan Supplier' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-md border border-gray-200 text-black">
        <span class="iconify text-xl shrink-0" data-icon="lucide:info"></span>
        <p class="text-xs font-medium leading-relaxed">
            Pastikan data **Nama Supplier** diisi dengan benar. Data ini akan muncul pada saat pembuatan laporan barang masuk dan transaksi pembelian stok.
        </p>
    </div>

</div>
@endsection
