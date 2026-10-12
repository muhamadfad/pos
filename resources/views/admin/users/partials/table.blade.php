<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse min-w-[800px]">
        <thead>
            <tr class="bg-white border-b border-gray-200">
                <th class="px-6 py-5 text-[10px] font-semibold text-gray-500 uppercase tracking-[0.2em]">#</th>
                <th class="px-6 py-5 text-[10px] font-semibold text-gray-500 uppercase tracking-[0.2em]">User & Kredensial
                </th>
                <th class="px-6 py-5 text-[10px] font-semibold text-gray-500 uppercase tracking-[0.2em]">Role Otoritas
                </th>
                <th class="px-6 py-5 text-[10px] font-semibold text-gray-500 uppercase tracking-[0.2em]">Status Akses</th>
                <th class="px-6 py-5 text-[10px] font-semibold text-gray-500 uppercase tracking-[0.2em] text-center">
                    Tindakan</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200 font-medium">
            @forelse ($users as $user)
            <tr class="hover:bg-white transition-all duration-200 group">
                <td class="px-6 py-5 text-gray-500 text-xs">
                    {{ $users->firstItem() + $loop->index }}
                </td>

                <td class="px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-md bg-slate-100 flex items-center justify-center text-gray-500 font-bold transition-all group-hover:bg-gray-50 group-hover:text-black">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p
                                class="text-sm font-semibold text-black tracking-tight leading-none mb-1 group-hover:text-black transition-colors">
                                {{ $user->name }}
                            </p>
                            <p class="text-xs text-gray-500 font-medium tracking-tight">
                                {{ $user->email }}
                            </p>
                        </div>
                    </div>
                </td>

                <td class="px-6 py-5">
                    <div class="flex items-center gap-2">
                        @if($user->role === 'admin')
                        <span
                            class="p-1 px-3 rounded-md bg-gray-50 text-black text-[10px] font-semibold uppercase tracking-widest border border-gray-200">
                            Administrator
                        </span>
                        @else
                        <span
                            class="p-1 px-3 rounded-md bg-slate-100 text-gray-500 text-[10px] font-semibold uppercase tracking-widest border border-gray-200">
                            Staff / Kasir
                        </span>
                        @endif
                    </div>
                </td>

                <td class="px-6 py-5">
                    <div class="flex items-center gap-2">
                        @if($user->is_active)
                        <span class="flex items-center gap-1.5 text-black text-xs font-bold">
                            <span class="w-2 h-2 rounded-md bg-white border border-gray-2000 animate-pulse"></span>
                            Aktif
                        </span>
                        @else
                        <span class="flex items-center gap-1.5 text-gray-500 text-xs font-bold">
                            <span class="w-2 h-2 rounded-md bg-slate-300"></span>
                            Suspend
                        </span>
                        @endif
                    </div>
                </td>

                <td class="px-6 py-5">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('admin.users.edit', $user) }}"
                            class="p-2 bg-gray-50 border border-gray-200 rounded-md text-gray-600 hover:text-black hover:border-gray-200 hover:bg-gray-50 transition-all shadow-sm">
                            <span class="iconify text-lg" data-icon="mdi:pencil-outline"></span>
                        </a>

                        <button type="button"
                            @click="confirmDelete('{{ route('admin.users.destroy', $user) }}', '{{ addslashes($user->name) }}')"
                            {{ auth()->id() == $user->id ? 'disabled' : '' }}
                            class="p-2 bg-gray-50 border border-gray-200 rounded-md text-gray-600 hover:text-rose-600
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
                        <p class="font-semibold uppercase tracking-widest text-xs">Data user belum tersedia</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="p-8 bg-white border-t border-gray-200">
    {{ $users->links() }}
</div>
