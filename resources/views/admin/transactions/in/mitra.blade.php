@extends('layouts.admin')

@section('title', 'Pilih Mitra')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm border">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">🏪 Pilih Mitra / Supplier</h2>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left border rounded-lg overflow-hidden">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">Nama Mitra</th>
                    <th class="px-4 py-3">Jenis</th>
                    <th class="px-4 py-3">No HP</th>
                    <th class="px-4 py-3">Alamat</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($mitras as $mitra)
                <tr class="hover:bg-gray-50">

                    <td class="px-4 py-3 font-medium text-gray-800">
                        {{ $mitra->nama_mitra }}
                    </td>

                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                            {{ $mitra->jenis_mitra == 'Supplier' 
                                ? 'bg-blue-100 text-blue-700' 
                                : 'bg-green-100 text-green-700' }}">
                            {{ $mitra->jenis_mitra }}
                        </span>
                    </td>

                    <td class="px-4 py-3">
                        {{ $mitra->no_hp }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $mitra->alamat }}
                    </td>

                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('admin.transactions.in.perMitra', $mitra->id) }}"
                           class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                            Pilih
                        </a>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                        Data mitra belum tersedia.
                    </tr>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
