@extends('layouts.admin')

@section('title', 'Barang Masuk')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm border">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">📥 Barang Masuk</h2>
        <a href="{{ route('admin.transactions.in.pilihMitra') }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            + Tambah Transaksi
        </a>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left border rounded-lg overflow-hidden">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Mitra</th>
                    <th class="px-4 py-3">Daftar Barang</th>
                    <th class="px-4 py-3 text-right">Total</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($barangMasuk as $row)
                <tr class="hover:bg-gray-50 align-top">

                    <!-- Tanggal -->
                    <td class="px-4 py-3 whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}
                    </td>

                    <!-- Mitra -->
                    <td class="px-4 py-3 font-medium text-gray-800">
                        {{ $row->mitra->nama_mitra ?? '-' }}
                    </td>

                    <!-- Barang -->
                    <td class="px-4 py-3">
                        @foreach ($row->items as $item)
                            <div class="mb-2">
                                <div class="font-medium text-gray-800">
                                    {{ $item->product->name ?? '-' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $item->qty }} x 
                                    Rp {{ number_format($item->harga_beli ?? 0, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </td>

                    <!-- Total -->
                    <td class="px-4 py-3 text-right font-semibold text-gray-800">
                        Rp {{ number_format($row->total_harga, 0, ',', '.') }}
                    </td>

                    <!-- Status -->
                    <td class="px-4 py-3 text-center">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            {{ $row->status_pembayaran == 'LUNAS' 
                                ? 'bg-green-100 text-green-700' 
                                : 'bg-yellow-100 text-yellow-700' }}">
                            {{ strtoupper($row->status_pembayaran) }}
                        </span>
                    </td>

                   

                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                        Data barang masuk belum tersedia.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $barangMasuk->links() }}
    </div>

</div>
@endsection
