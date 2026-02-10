{{-- resources/views/admin/finance/monthly.blade.php --}}

@extends('layouts.admin') {{-- pastikan layout admin dari Breeze --}}

@section('content')
<div class="p-6 bg-white rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-4">Laporan Bulanan</h1>
    <p class="text-gray-600 mb-6">
        Periode: {{ $start->format('d M Y') }} - {{ $end->format('d M Y') }}
    </p>

    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-200 divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Jenis</th>
                    <th class="px-4 py-2 text-right text-sm font-semibold text-gray-700">Total</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr>
                    <td class="px-4 py-2">Pendapatan</td>
                    <td class="px-4 py-2 text-right font-medium text-green-600">
                        Rp {{ number_format($income, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td class="px-4 py-2">Pengeluaran</td>
                    <td class="px-4 py-2 text-right font-medium text-red-600">
                        Rp {{ number_format($expense, 0, ',', '.') }}
                    </td>
                </tr>
                <tr class="font-semibold bg-gray-50">
                    <td class="px-4 py-2">Laba / Rugi</td>
                    <td class="px-4 py-2 text-right {{ $income - $expense >= 0 ? 'text-green-700' : 'text-red-700' }}">
                        Rp {{ number_format($income - $expense, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
