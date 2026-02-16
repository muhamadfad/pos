@extends('layouts.admin')

@section('title', 'Pilih Customer')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">

    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
            <span class="iconify text-blue-600" data-icon="mdi:account-group"></span>
            Pilih Customer
        </h2>
        <p class="text-sm text-gray-500 mt-1">Pilih customer untuk transaksi barang keluar</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($customers as $customer)
        <a href="{{ route('admin.transactions.out.perCustomer', $customer) }}"
            class="block p-5 bg-white border-2 border-gray-200 rounded-xl hover:border-blue-500 hover:shadow-lg transition group">

            <div class="flex items-start justify-between mb-3">
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-800 group-hover:text-blue-600 transition">
                        {{ $customer->name }}
                    </h3>
                    <span
                        class="inline-block mt-1 px-2 py-0.5 text-xs rounded-full
                            {{ $customer->type === 'toko' ? 'bg-blue-100 text-blue-700' :
                               ($customer->type === 'proyek' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700') }}">
                        {{ strtoupper($customer->type) }}
                    </span>
                </div>
                <span class="text-blue-500 group-hover:translate-x-1 transition-transform">→</span>
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
                class="inline-block px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                ➕ Tambah Customer
            </a>
        </div>
        @endforelse
    </div>

</div>
@endsection
