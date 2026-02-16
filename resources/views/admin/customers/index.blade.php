@extends('layouts.admin')

@section('title', 'Customer')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                <span class="iconify text-blue-600" data-icon="mdi:account-group"></span>
                Data Customer
            </h2>
            <p class="text-sm text-gray-500 mt-1">Kelola data customer / pembeli</p>
        </div>
        <a href="{{ route('admin.customers.create') }}"
            class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium flex items-center gap-2">
            <span class="iconify" data-icon="mdi:plus"></span>
            Tambah Customer
        </a>
    </div> @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @include('admin.customers.partials.table')

</div>
@endsection
