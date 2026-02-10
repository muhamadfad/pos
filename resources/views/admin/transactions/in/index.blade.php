@extends('layouts.admin')

@section('title', 'Barang Masuk')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm border">

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">📥 Barang Masuk</h2>
        <a href="{{ route('admin.transactions.in.create') }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            + Tambah
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left border rounded-lg overflow-hidden">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Produk</th>
                    <th class="px-4 py-3">Jumlah</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($entries as $entry)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $entry->date->format('d-m-Y') }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $entry->product->name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $entry->quantity }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-4 py-6 text-center text-gray-500">
                        Data barang masuk belum tersedia.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
