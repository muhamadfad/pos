@extends('layouts.admin')

@section('title', 'Barang Keluar')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                <span class="iconify text-blue-600" data-icon="mdi:package-variant-closed-remove"></span>
                Transaksi Barang Keluar
            </h2>
            <p class="text-sm text-gray-500 mt-1">Semua transaksi penjualan barang</p>
        </div>
        <a href="{{ route('admin.transactions.out.selectCustomer') }}"
            class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium flex items-center gap-2">
            <span class="iconify" data-icon="mdi:plus"></span>
            Transaksi Baru
        </a>
    </div>

    <div class="overflow-x-auto border rounded overflow-hidden border-gray-200">
        <table class="min-w-full text-sm text-left ">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Customer</th>
                    <th class="px-4 py-3">Total</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($stockOuts as $entry)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $entry->date->format('d M Y') }}</td>
                    <td class="px-4 py-3 font-medium">{{ $entry->customer->name ?? '-' }}</td>
                    <td class="px-4 py-3 font-semibold">Rp {{ number_format($entry->total_amount, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">
                        <span
                            class="px-3 py-1 rounded-full text-xs font-medium
                            {{ $entry->payment_status === 'LUNAS' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ strtoupper($entry->payment_status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('admin.transactions.out.show', $entry->id) }}"
                            class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                        Belum ada transaksi barang keluar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $stockOuts->links() }}
    </div>
</div>
@endsection
