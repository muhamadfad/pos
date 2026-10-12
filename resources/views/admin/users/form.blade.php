@extends('layouts.admin')

@section('title', isset($user) ? 'Edit User' : 'Tambah User')

@section('content')
<div class="space-y-8">

    <!-- Header & Navigation -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-black tracking-tight flex items-center gap-3">
                <span class="p-2 bg-black text-white rounded-md shadow-sm">
                    <span class="iconify"
                        data-icon="{{ isset($user) ? 'mdi:account-edit' : 'mdi:account-plus' }}"></span>
                </span>
                {{ isset($user) ? 'Edit User' : 'Tambah User' }}
            </h1>
            <p class="text-gray-500 font-medium mt-1">
                {{ isset($user) ? 'Perbarui informasi dan hak akses pengguna ini.' : 'Daftarkan pengguna baru ke
                sistem.' }}
            </p>
        </div>

        <a href="{{ route('admin.users.index') }}"
            class="flex items-center gap-2 px-5 py-2.5 bg-gray-50 border border-gray-200 rounded-md font-bold text-sm text-gray-600 hover:bg-white transition-all shadow-sm">
            <span class="iconify" data-icon="mdi:arrow-left"></span>
            Kembali
        </a>
    </div>

    <!-- Form Area -->
    <div class="bg-gray-50 rounded-lg border border-gray-200 shadow-sm overflow-hidden p-8 md:p-12">

        <form action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}"
            method="POST" class="space-y-10" x-data="{ showPw: false, role: '{{ old('role', $user->role ?? '') }}' }">
            @csrf
            @if(isset($user))
            @method('PUT')
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

                <!-- Basic Info -->
                <div class="space-y-6">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-widest flex items-center gap-2">
                        Informasi Dasar
                        <div class="h-px flex-1 bg-slate-100"></div>
                    </h3>

                    <!-- Nama -->
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700 tracking-tight">Nama Lengkap</label>
                        <input type="text" name="name" placeholder="Contoh: Ahmad Kasir"
                            value="{{ old('name', $user->name ?? '') }}" required
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-md text-sm font-medium focus:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-black/5 focus:border-gray-200 transition-all">
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700 tracking-tight">Alamat Email</label>
                        <input type="email" name="email" placeholder="staff@cotoselerarakyat.com"
                            value="{{ old('email', $user->email ?? '') }}" required
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-md text-sm font-medium focus:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-black/5 focus:border-gray-200 transition-all">
                    </div>
                </div>

                <!-- Access & Status -->
                <div class="space-y-6">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-widest flex items-center gap-2">
                        Otoritas & Akses
                        <div class="h-px flex-1 bg-slate-100"></div>
                    </h3>

                    <!-- Password -->
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700 tracking-tight">
                            Kata Sandi
                            @if(isset($user))
                            <span class="text-[10px] text-gray-500 font-bold tracking-normal ml-1">(Opsional)</span>
                            @endif
                        </label>
                        <div class="relative">
                            <input :type="showPw ? 'text' : 'password'" name="password" placeholder="Min. 6 Karakter"
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-md text-sm font-medium focus:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-black/5 focus:border-gray-200 transition-all"
                                {{ isset($user) ? '' : 'required' }}>
                            <button type="button" @click="showPw = !showPw"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-black transition-colors">
                                <span class="iconify text-xl"
                                    :data-icon="showPw ? 'mdi:eye-off-outline' : 'mdi:eye-outline'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Role -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700 tracking-tight">Role User</label>
                            <select name="role" x-model="role" required
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-md text-sm font-medium focus:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-black/5 focus:border-gray-200 transition-all">
                                <option value="">Pilih Otoritas</option>
                                <option value="super_admin" {{ old('role', $user->role ?? '') == 'super_admin' ? 'selected' : '' }}>Super Admin (Owner / Pusat)</option>
                                <option value="admin" {{ old('role', $user->role ?? '') == 'admin' ? 'selected' : '' }}>Administrator (Akses Cabang)</option>
                                <option value="kasir" {{ old('role', $user->role ?? '') == 'kasir' ? 'selected' : '' }}>Kasir (POS Only)</option>
                            </select>
                        </div>

                        <!-- Cabang (Only for admin and kasir) -->
                        <div class="space-y-2" x-show="role === 'admin' || role === 'kasir'">
                            <label class="text-sm font-semibold text-slate-700 tracking-tight">Penempatan Cabang</label>
                            <select name="branch_id" :required="role === 'admin' || role === 'kasir'"
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-md text-sm font-medium focus:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-black/5 focus:border-gray-200 transition-all">
                                <option value="">Pilih Cabang</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id', $user->branch_id ?? '') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Can Manage Pusat (Only for admin) -->
                        <div class="space-y-2 sm:col-span-2" x-show="role === 'admin'">
                            <div class="flex items-center gap-4 py-2 border-t border-gray-200 mt-2">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="can_manage_pusat" class="sr-only peer" value="1" {{ old('can_manage_pusat', $user->can_manage_pusat ?? false) ? 'checked' : '' }}>
                                    <div class="relative w-11 h-6 shrink-0 bg-slate-200 peer-focus:outline-none rounded-md peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-gray-50 after:border-gray-300 after:border after:rounded-md after:h-5 after:w-5 after:transition-all peer-checked:bg-black">
                                    </div>
                                    <div class="ml-3">
                                        <span class="block text-sm font-bold text-slate-700 tracking-tight">Beri Akses Kelola Logistik Pusat</span>
                                        <span class="block text-xs font-medium text-gray-500">Admin cabang ini dapat menambah produksi, menyetujui transfer, & mengelola stok gudang pusat.</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Status Aktif -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700 tracking-tight">Status Account</label>
                            <div class="flex items-center gap-4 py-3.5">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" class="sr-only peer" {{ old('is_active',
                                        $user->is_active ?? true) ? 'checked' : '' }}>
                                    <div
                                        class="relative w-11 h-6 shrink-0 bg-slate-200 peer-focus:outline-none rounded-md peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-gray-50 after:border-gray-300 after:border after:rounded-md after:h-5 after:w-5 after:transition-all peer-checked:bg-white border border-gray-2000">
                                    </div>
                                    <span class="ml-3 text-sm font-bold text-gray-600 tracking-tight">Aktifkan
                                        Akses</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Footer Action -->
            <div class="flex items-center justify-end gap-4 pt-10 border-t border-gray-200">
                @if ($errors->any())
                <span class="text-rose-500 text-xs font-semibold uppercase tracking-widest mr-auto">Periksa kembali data
                    inputan</span>
                @endif

                <button type="submit"
                    class="px-10 py-2 bg-black text-white rounded-md font-semibold text-sm tracking-tight shadow-sm  hover:bg-gray-800 transition-all hover:scale-[1.02] active:scale-95 flex items-center gap-2">
                    <span class="iconify text-lg" data-icon="mdi:content-save-outline"></span>
                    {{ isset($user) ? 'Perbarui Data User' : 'Simpan User Baru' }}
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
