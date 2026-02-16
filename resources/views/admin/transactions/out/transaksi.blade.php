@extends('layouts.admin')

@section('title', 'Transaksi - ' . $customer->name)

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                <span class="iconify text-blue-600" data-icon="mdi:package-variant-closed-remove"></span>
                Transaksi Barang Keluar
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Customer: <span class="font-medium text-gray-700">{{ $customer->name }}</span>
                @if($customer->type)
                <span
                    class="ml-2 px-2 py-0.5 text-xs rounded-full
                        {{ $customer->type === 'toko' ? 'bg-blue-100 text-blue-700' :
                           ($customer->type === 'proyek' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700') }}">
                    {{ strtoupper($customer->type) }}
                </span>
                @endif
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.transactions.out.download', $customer) }}"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm flex items-center gap-2">
                <span class="iconify" data-icon="mdi:file-pdf-box"></span>
                Download PDF
            </a>
            <a href="{{ route('admin.transactions.out.create', $customer) }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm flex items-center gap-2">
                <span class="iconify" data-icon="mdi:plus"></span>
                Transaksi Baru
            </a>
            <a href="{{ route('admin.transactions.out.selectCustomer') }}"
                class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 text-sm flex items-center gap-2">
                <span class="iconify" data-icon="mdi:arrow-left"></span>
                Pilih Customer Lain
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @include('admin.transactions.out.partials.table')

</div>
@endsection
