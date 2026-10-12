@extends('layouts.admin')

@section('title', 'Tambah Barang Keluar')

@section('content')
<div class="bg-gray-50 p-6 rounded-md shadow-sm border border-gray-200 max-w-6xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                <span class="iconify text-black" data-icon="mdi:package-variant-closed-remove"></span>
                Tambah Barang Keluar
            </h2>
            <p class="text-sm text-gray-500">Customer: {{ $customer->name }}</p>
        </div>
        <a href="{{ route('admin.transactions.out.perCustomer', $customer->id) }}"
            class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
            ← Kembali
        </a>
    </div>

    @if($errors->any())
    <div class="mb-6 rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <ul class="list-disc pl-5 space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.transactions.out.store', $customer->id) }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="date" name="date"
                    class="w-full rounded-md border-gray-300 focus:border-gray-200 focus:ring-black/5"
                    value="{{ date('Y-m-d') }}" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Diterima</label>
                <input type="number" name="paid" min="0" placeholder="0"
                    class="w-full rounded-md border-gray-300 focus:border-gray-200 focus:ring-black/5">
            </div>
        </div>

        <div class="border rounded-md overflow-hidden mb-6">
            <div class="flex items-center justify-between bg-gray-50 px-4 py-3 border-b">
                <span class="font-medium text-gray-700 flex items-center gap-2">
                    <span class="iconify" data-icon="mdi:clipboard-list"></span>
                    Daftar Barang
                </span>
                <button type="button"
                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-black text-white text-white rounded-md text-sm hover:bg-gray-800 transition"
                    onclick="addRow()">
                    <span class="iconify" data-icon="mdi:plus"></span>
                    Tambah Baris
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border-collapse" id="itemsTable">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 text-left">Barang</th>
                            <th class="px-4 py-3 text-center">Qty</th>
                            <th class="px-4 py-3 text-right">Harga Jual</th>
                            <th class="px-4 py-3 text-right">Subtotal</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2">
                                <select name="items[0][product_id]"
                                    class="w-full rounded-md border-gray-300 focus:border-gray-200 focus:ring-black/5"
                                    required>
                                    <option value="">-- Pilih Barang --</option>
                                    @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} (Stok: {{ $product->stock
                                        }})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <input type="number" name="items[0][quantity]" min="1"
                                    class="w-20 text-center rounded-md border-gray-300 focus:border-gray-200 focus:ring-black/5 qty"
                                    required oninput="calculateRow(this)">
                            </td>
                            <td class="px-4 py-2 text-right">
                                <input type="number" name="items[0][selling_price]" min="0"
                                    class="w-32 text-right rounded-md border-gray-300 focus:border-gray-200 focus:ring-black/5 price"
                                    required oninput="calculateRow(this)">
                            </td>
                            <td class="px-4 py-2 text-right">
                                <input type="text"
                                    class="w-32 text-right rounded-md border-gray-200 bg-gray-100 subtotal" readonly>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <button type="button" class="text-red-600 hover:underline text-sm"
                                    onclick="removeRow(this)">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('admin.transactions.out.perCustomer', $customer->id) }}"
                class="px-4 py-2 rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300 transition flex items-center gap-2">
                <span class="iconify" data-icon="mdi:close"></span>
                Batal
            </a>
            <button type="submit"
                class="px-5 py-2 rounded-md bg-black text-white text-white hover:bg-gray-800 transition font-medium flex items-center gap-2">
                <span class="iconify" data-icon="mdi:content-save"></span>
                Simpan Transaksi
            </button>
        </div>
    </form>
</div>

<script>
    let rowIndex = 1;

function addRow() {
    const table = document.getElementById('itemsTable').getElementsByTagName('tbody')[0];
    const newRow = document.createElement('tr');
    newRow.classList.add('hover:bg-gray-50');

    newRow.innerHTML = `
        <td class="px-4 py-2">
            <select name="items[${rowIndex}][product_id]"
                    class="w-full rounded-md border-gray-300 focus:border-gray-200 focus:ring-black/5"
                    required>
                <option value="">-- Pilih Barang --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }} (Stok: {{ $product->stock }})</option>
                @endforeach
            </select>
        </td>
        <td class="px-4 py-2 text-center">
            <input type="number" name="items[${rowIndex}][quantity]" min="1"
                   class="w-20 text-center rounded-md border-gray-300 focus:border-gray-200 focus:ring-black/5 qty"
                   required oninput="calculateRow(this)">
        </td>
        <td class="px-4 py-2 text-right">
            <input type="number" name="items[${rowIndex}][selling_price]" min="0"
                   class="w-32 text-right rounded-md border-gray-300 focus:border-gray-200 focus:ring-black/5 price"
                   required oninput="calculateRow(this)">
        </td>
        <td class="px-4 py-2 text-right">
            <input type="text"
                   class="w-32 text-right rounded-md border-gray-200 bg-gray-100 subtotal"
                   readonly>
        </td>
        <td class="px-4 py-2 text-center">
            <button type="button"
                    class="text-red-600 hover:underline text-sm"
                    onclick="removeRow(this)">
                Hapus
            </button>
        </td>
    `;
    table.appendChild(newRow);
    rowIndex++;
}

function removeRow(button) {
    const row = button.closest('tr');
    row.remove();
}

function calculateRow(input) {
    const row = input.closest('tr');
    const qty = row.querySelector('.qty').value || 0;
    const price = row.querySelector('.price').value || 0;
    const subtotal = qty * price;
    row.querySelector('.subtotal').value = subtotal.toLocaleString('id-ID');
}
</script>
@endsection
