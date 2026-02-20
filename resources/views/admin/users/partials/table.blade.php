<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse min-w-[800px]">
        <thead>
            <tr class="bg-slate-50/50 border-b border-slate-100">
                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">#</th>
                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">User & Kredensial
                </th>
                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Role Otoritas
                </th>
                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Status Akses</th>
                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">
                    Tindakan</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-slate-100 font-medium">
            @forelse ($users as $user)
            <tr class="hover:bg-slate-50 transition-all duration-200 group">
                <td class="px-6 py-5 text-slate-400 text-xs">
                    {{ $users->firstItem() + $loop->index }}
                </td>

                <td class="px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 font-bold transition-all group-hover:bg-indigo-100 group-hover:text-indigo-600">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p
                                class="text-sm font-black text-slate-800 tracking-tight leading-none mb-1 group-hover:text-indigo-600 transition-colors">
                                {{ $user->name }}
                            </p>
                            <p class="text-xs text-slate-400 font-medium tracking-tight">
                                {{ $user->email }}
                            </p>
                        </div>
                    </div>
                </td>

                <td class="px-6 py-5">
                    <div class="flex items-center gap-2">
                        @if($user->role === 'admin')
                        <span
                            class="p-1 px-3 rounded-lg bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest border border-indigo-100">
                            Administrator
                        </span>
                        @else
                        <span
                            class="p-1 px-3 rounded-lg bg-slate-100 text-slate-500 text-[10px] font-black uppercase tracking-widest border border-slate-200">
                            Staff / Kasir
                        </span>
                        @endif
                    </div>
                </td>

                <td class="px-6 py-5">
                    <div class="flex items-center gap-2">
                        @if($user->is_active)
                        <span class="flex items-center gap-1.5 text-emerald-600 text-xs font-bold">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Aktif
                        </span>
                        @else
                        <span class="flex items-center gap-1.5 text-slate-400 text-xs font-bold">
                            <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                            Suspend
                        </span>
                        @endif
                    </div>
                </td>

                <td class="px-6 py-5">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('admin.users.edit', $user) }}"
                            class="p-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:text-indigo-600 hover:border-indigo-100 hover:bg-indigo-50 transition-all shadow-sm">
                            <span class="iconify text-lg" data-icon="mdi:pencil-outline"></span>
                        </a>

                        <button type="button"
                            @click="confirmDelete('{{ route('admin.users.destroy', $user) }}', '{{ addslashes($user->name) }}')"
                            {{ auth()->id() == $user->id ? 'disabled' : '' }}
                            class="p-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:text-rose-600
                            hover:border-rose-100 hover:bg-rose-50 transition-all shadow-sm disabled:opacity-30
                            disabled:cursor-not-allowed">
                            <span class="iconify text-lg" data-icon="mdi:trash-can-outline"></span>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="py-24 text-center">
                    <div class="flex flex-col items-center justify-center space-y-3 opacity-30">
                        <span class="iconify text-6xl" data-icon="mdi:account-search-outline"></span>
                        <p class="font-black uppercase tracking-widest text-xs">Data user belum tersedia</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="p-8 bg-slate-50/50 border-t border-slate-100">
    {{ $users->links() }}
</div>
