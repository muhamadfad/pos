@extends('layouts.admin')

@section('title', 'Transaksi - ' . $customer->name)

@section('content')
<div class="bg-gray-50 p-6 rounded-md shadow-sm border border-gray-200">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                <span class="iconify text-black" data-icon="mdi:package-variant-closed-remove"></span>
                Transaksi Barang Keluar
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Customer: <span class="font-medium text-gray-700">{{ $customer->name }}</span>
                @if($customer->type)
                <span
                    class="ml-2 px-2 py-0.5 text-xs rounded-md
                        {{ $customer->type === 'toko' ? 'bg-gray-50 border border-gray-200 text-black' :
                           ($customer->type === 'proyek' ? 'bg-gray-50 border border-gray-200 text-black' : 'bg-gray-100 text-gray-700') }}">
                    {{ strtoupper($customer->type) }}
                </span>
                @endif
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.transactions.out.download', $customer) }}"
                class="px-4 py-2 bg-black text-white text-white rounded-md hover:bg-gray-800 text-sm flex items-center gap-2">
                <span class="iconify" data-icon="mdi:file-pdf-box"></span>
                Download PDF
            </a>
            <a href="{{ route('admin.transactions.out.create', $customer) }}"
                class="px-4 py-2 bg-black text-white text-white rounded-md hover:bg-gray-800 text-sm flex items-center gap-2">
                <span class="iconify" data-icon="mdi:plus"></span>
                Transaksi Baru
            </a>
            <a href="{{ route('admin.transactions.out.selectCustomer') }}"
                class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300 text-sm flex items-center gap-2">
                <span class="iconify" data-icon="mdi:arrow-left"></span>
                Pilih Customer Lain
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-gray-50 border border-gray-200 text-black rounded-md">
        {{ session('success') }}
    </div>
    @endif

    @include('admin.transactions.out.partials.table')

</div>
@endsection
