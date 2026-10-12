@extends('layouts.admin')

@section('title', 'Pilih Customer')

@section('content')
<div class="bg-gray-50 p-6 rounded-md shadow-sm border border-gray-200">

    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
            <span class="iconify text-black" data-icon="mdi:account-group"></span>
            Pilih Customer
        </h2>
        <p class="text-sm text-gray-500 mt-1">Pilih customer untuk transaksi barang keluar</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($customers as $customer)
        <a href="{{ route('admin.transactions.out.perCustomer', $customer) }}"
            class="block p-5 bg-gray-50 border-2 border-gray-200 rounded-md hover:border-gray-200 hover:shadow-sm transition group">

            <div class="flex items-start justify-between mb-3">
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-800 group-hover:text-black transition">
                        {{ $customer->name }}
                    </h3>
                    <span
                        class="inline-block mt-1 px-2 py-0.5 text-xs rounded-md
                            {{ $customer->type === 'toko' ? 'bg-gray-50 border border-gray-200 text-black' :
                               ($customer->type === 'proyek' ? 'bg-gray-50 border border-gray-200 text-black' : 'bg-gray-100 text-gray-700') }}">
                        {{ strtoupper($customer->type) }}
                    </span>
                </div>
                <span class="text-black group-hover:translate-x-1 transition-transform">→</span>
            </div>

            <div class="text-sm text-gray-600 space-y-1">
                @if($customer->phone)
                <p class="flex items-center gap-1">
                    <span class="iconify" data-icon="mdi:phone"></span>
                    {{ $customer->phone }}
                </p>
                @endif
                @if($customer->address)
                <p class="flex items-start gap-1 line-clamp-2">
                    <span class="iconify mt-0.5" data-icon="mdi:map-marker"></span>
                    {{ $customer->address }}
                </p>
                @endif
            </div>
        </a>
        @empty
        <div class="col-span-3 text-center py-12">
            <p class="text-gray-400 mb-4">Belum ada customer</p>
            <a href="{{ route('admin.customers.create') }}"
                class="inline-block px-5 py-2 bg-black text-white text-white rounded-md hover:bg-gray-800">
                ➕ Tambah Customer
            </a>
        </div>
        @endforelse
    </div>

</div>
@endsection
