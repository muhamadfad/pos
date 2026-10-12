@extends('layouts.admin')

@section('title', 'Terutang')

@section('content')
<div class="bg-gray-50 p-6 rounded-md shadow-sm border">

    <h2 class="text-xl font-semibold text-gray-800 mb-6">📄 Daftar Terutang</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left border rounded-md overflow-hidden">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Supplier</th>
                    <th class="px-4 py-3">Produk</th>
                    <th class="px-4 py-3">Total Hutang</th>
                    <th class="px-4 py-3">Jatuh Tempo</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($terutangs as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $item->stockEntry->date->format('d-m-Y') }}</td>
                    <td class="px-4 py-3">{{ $item->supplier->name }}</td>
                    <td class="px-4 py-3">{{ $item->stockEntry->product->name }}</td>
                    <td class="px-4 py-3">Rp {{ number_format($item->total_hutang, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">
                        {{ $item->tanggal_jatuh_tempo ?
                        \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d-m-Y') : '-' }}
                    </td>
                    <td class="px-4 py-3">
                        @if ($item->status === 'lunas')
                        <span class="px-2 py-1 text-xs rounded bg-gray-50 border border-gray-200 text-black">Lunas</span>
                        @else
                        <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">Belum Lunas</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if ($item->status === 'belum_lunas')
                        <form id="lunas-form-{{ $item->id }}" action="{{ route('admin.terutang.update', $item->id) }}"
                            method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="lunas">
                            <button type="button"
                                onclick="confirmAction('lunas-form-{{ $item->id }}', 'Tandai hutang ini sebagai lunas?')"
                                class="px-3 py-1 text-xs bg-black text-white text-white rounded hover:bg-gray-800">
                                Tandai Lunas
                            </button>
                        </form>
                        @else
                        <span class="text-sm text-gray-500">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                        Data terutang belum tersedia.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $terutangs->links() }}
    </div>

</div>
@endsection
