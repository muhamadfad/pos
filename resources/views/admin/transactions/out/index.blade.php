@extends('layouts.admin')
@section('title', 'Barang Keluar')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm border">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">📤 Barang Keluar</h2>
        <a href="{{ route('admin.transactions.out.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            ➕ Tambah
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left border rounded-lg overflow-hidden">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">Produk</th>
                    <th class="px-4 py-3">Jumlah</th>
                    <th class="px-4 py-3">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($entries as $entry)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $entry->product->name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $entry->quantity }}</td>
                    <td class="px-4 py-3">{{ $entry->date->format('d-m-Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-4 py-6 text-center text-gray-500">
                        Belum ada data barang keluar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
