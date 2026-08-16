@extends('layouts.admin')

@section('content')
<div class="container mx-auto">
    <div class="w-full">
        @if($activeShift)
        <!-- SHIFT AKTIF - Show Resume & Options -->
        <div id="shiftActiveCard" class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <!-- Header -->
            <div class="p-6 border-b border-slate-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center shrink-0">
                            <span class="iconify text-emerald-600" data-icon="mdi:cash-check" data-width="24"></span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h1 class="text-xl font-semibold text-slate-800">Shift Aktif</h1>
                                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-lg">OPEN</span>
                                <button type="button" onclick="document.getElementById('editShiftModal').classList.remove('hidden')" class="ml-2 px-3 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs font-bold rounded-lg flex items-center gap-1 transition-colors border border-blue-200">
                                    <span class="iconify" data-icon="mdi:pencil"></span> Edit Info
                                </button>
                            </div>
                            <p class="text-slate-500 text-sm mt-1">Dibuka {{ $activeShift->opened_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6">

                <!-- Shift Summary Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
                    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                                <span class="iconify text-blue-600" data-icon="mdi:account"
                                    data-width="16"></span>
                            </div>
                            <div class="text-xs text-slate-400 uppercase tracking-wide">Kasir</div>
                        </div>
                        <div class="font-semibold text-slate-800">{{ $activeShift->cashier_name ?? $activeShift->user->name ?? 'Kasir' }}</div>
                        <div class="text-sm text-slate-500">Shift Aktif</div>
                    </div>
                    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
                                <span class="iconify text-orange-600" data-icon="mdi:calendar-clock"
                                    data-width="16"></span>
                            </div>
                            <div class="text-xs text-slate-400 uppercase tracking-wide">Jadwal Shift</div>
                        </div>
                        <div class="font-semibold text-slate-800">{{ $activeShift->shift_time ?? '-' }}</div>
                        <div class="text-sm text-slate-500">Waktu Kerja</div>
                    </div>
                    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center">
                                <span class="iconify text-emerald-600" data-icon="mdi:clock-outline"
                                    data-width="16"></span>
                            </div>
                            <div class="text-xs text-slate-400 uppercase tracking-wide">Waktu Buka</div>
                        </div>
                        <div class="font-semibold text-slate-800">{{ $activeShift->opened_at->format('d M Y') }}</div>
                        <div class="text-sm text-slate-500">{{ $activeShift->opened_at->format('H:i') }}</div>
                    </div>
                    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center">
                                <span class="iconify text-purple-600" data-icon="mdi:timer-outline"
                                    data-width="16"></span>
                            </div>
                            <div class="text-xs text-slate-400 uppercase tracking-wide">Durasi</div>
                        </div>
                        <div class="font-semibold text-slate-800">{{ $activeShift->opened_at->diffForHumans(null, true)
                            }}
                        </div>
                        <div class="text-sm text-slate-500">Shift berlangsung</div>
                    </div>
                </div>

                <!-- Financial Summary -->
                <div class="bg-slate-50 rounded-2xl p-5 space-y-3">
                    <div class="flex items-center justify-between py-2">
                        <div class="flex items-center gap-2">
                            <span class="iconify text-slate-400" data-icon="mdi:wallet-outline" data-width="20"></span>
                            <span class="text-slate-600 font-medium">Modal Awal</span>
                        </div>
                        <span class="font-semibold text-slate-800">Rp {{ number_format($activeShift->opening_balance, 0,
                            ',',
                            '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <div class="flex items-center gap-2">
                            <span class="iconify text-slate-400" data-icon="mdi:cash-multiple" data-width="20"></span>
                            <span class="text-slate-600 font-medium">Total Penjualan</span>
                        </div>
                        <span class="font-semibold text-slate-800">Rp {{ number_format($activeShift->total_sales, 0,
                            ',',
                            '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <div class="flex items-center gap-2">
                            <span class="iconify text-slate-400" data-icon="mdi:receipt" data-width="20"></span>
                            <span class="text-slate-600 font-medium">Jumlah Transaksi</span>
                        </div>
                        <span class="font-semibold text-slate-800">{{ $activeShift->transaction_count }}
                            transaksi</span>
                    </div>
                    <div class="border-t border-slate-200 pt-3 mt-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="iconify text-emerald-600" data-icon="mdi:cash-check"
                                    data-width="22"></span>
                                <span class="text-emerald-700 font-semibold">Kas Seharusnya</span>
                            </div>
                            <span class="font-semibold text-xl text-emerald-600">Rp {{ number_format($expectedCash, 0,
                                ',',
                                '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Transactions List -->
                @if($transactions->count() > 0)
                <div class="mt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                            <span class="iconify text-slate-500" data-icon="mdi:receipt-text" data-width="22"></span>
                            Daftar Transaksi
                        </h3>
                        <span class="px-3 py-1 bg-blue-50 text-blue-700 text-sm font-semibold rounded-lg">{{
                            $transactions->count() }} transaksi</span>
                    </div>

                    <div class="grid gap-3">
                        @foreach($transactions as $index => $transaction)
                        <div onclick="showTransactionDetail({{ $transaction->id }})"
                            class="bg-white rounded-xl shadow-sm border border-slate-100 hover:border-blue-300 hover:shadow-md transition-all cursor-pointer group">
                            <div class="p-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <div
                                                class="w-10 h-10 bg-blue-50 group-hover:bg-blue-100 rounded-lg flex items-center justify-center transition-colors">
                                                <span class="iconify text-blue-600" data-icon="mdi:receipt"
                                                    data-width="20"></span>
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="font-semibold text-slate-800">#{{ str_pad($index + 1,
                                                        3, '0', STR_PAD_LEFT) }}</span>
                                                    <span class="text-slate-400">•</span>
                                                    <span class="text-sm text-slate-600">{{
                                                        $transaction->created_at->format('H:i') }}</span>
                                                </div>
                                                <div class="text-sm text-slate-500 mt-0.5">{{
                                                    $transaction->customer->name }}</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 ml-13">
                                            @if($transaction->payment_status === 'paid')
                                            <span
                                                class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-xs font-medium rounded">
                                                <span class="iconify inline" data-icon="mdi:check-circle"
                                                    data-width="12"></span> Lunas
                                            </span>
                                            @elseif($transaction->payment_status === 'partial')
                                            <span
                                                class="px-2 py-0.5 bg-amber-50 text-amber-700 text-xs font-medium rounded">
                                                <span class="iconify inline" data-icon="mdi:clock-outline"
                                                    data-width="12"></span> Sebagian
                                            </span>
                                            @else
                                            <span
                                                class="px-2 py-0.5 bg-rose-50 text-rose-700 text-xs font-medium rounded">
                                                <span class="iconify inline" data-icon="mdi:alert-circle"
                                                    data-width="12"></span> Piutang
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-semibold text-slate-800">Rp {{
                                            number_format($transaction->total_amount, 0, ',', '.') }}</div>
                                        @if($transaction->payment_status !== 'paid')
                                        <div class="text-xs text-slate-500 mt-1">
                                            Bayar: Rp {{ number_format($transaction->total_paid, 0, ',', '.') }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Total Summary -->
                    <div
                        class="mt-4 bg-gradient-to-r from-emerald-50 to-green-50 rounded-xl border border-emerald-100 p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="iconify text-emerald-600" data-icon="mdi:cash-multiple"
                                    data-width="24"></span>
                                <span class="font-semibold text-emerald-900">Total Penjualan Shift</span>
                            </div>
                            <span class="text-2xl font-bold text-emerald-600">Rp {{
                                number_format($activeShift->total_sales, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                @else
                <div class="mt-6 p-8 bg-slate-50 rounded-2xl border border-slate-100 text-center">
                    <span class="iconify text-slate-300 mb-3" data-icon="mdi:receipt-text-outline"
                        data-width="48"></span>
                    <p class="text-slate-600 font-medium">Belum ada transaksi</p>
                    <p class="text-sm text-slate-400 mt-1">Transaksi akan muncul di sini setelah melakukan penjualan</p>
                </div>
                @endif

                <!-- Info Banner -->
                <div class="mt-6 mb-5 p-4 bg-blue-50 border border-blue-100 rounded-2xl">
                    <div class="flex items-start gap-3">
                        <span class="iconify text-blue-600 shrink-0 mt-0.5" data-icon="mdi:information"
                            data-width="20"></span>
                        <div class="text-sm">
                            <p class="font-semibold text-slate-700 mb-1">Pilih Aksi</p>
                            <p class="text-slate-600">
                                Lanjutkan penjualan atau tutup shift untuk menghitung kas akhir.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <a href="{{ route('pos.index') }}"
                        class="group py-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all shadow-sm hover:shadow-md text-center flex items-center justify-center gap-2">
                        <span class="iconify" data-icon="mdi:cash-register" data-width="20"></span>
                        <span>Lanjutkan Penjualan</span>
                    </a>
                    <button onclick="showCloseForm()"
                        class="group py-4 bg-rose-600 hover:bg-rose-700 text-white font-semibold rounded-xl transition-all shadow-sm hover:shadow-md flex items-center justify-center gap-2">
                        <span class="iconify" data-icon="mdi:lock" data-width="20"></span>
                        <span>Tutup Kasir</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- CLOSE FORM (Hidden by default) -->
        <div id="closeForm" class="hidden">
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-3">
                <span class="iconify text-red-500 shrink-0" data-icon="mdi:alert-circle" data-width="20"></span>
                <span class="text-sm">{{ $errors->first() }}</span>
            </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <!-- Header -->
                <div class="p-6 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <button onclick="hideCloseForm()" class="text-slate-400 hover:text-slate-600 transition-colors">
                            <span class="iconify" data-icon="mdi:arrow-left" data-width="24"></span>
                        </button>
                        <div>
                            <h2 class="text-xl font-semibold text-slate-800">Form Tutup Kasir</h2>
                            <p class="text-slate-500 text-sm mt-1">Hitung dan input kas akhir shift Anda</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 md:p-8">

                    <form action="{{ route('pos.shift.close') }}" method="POST" id="closeShiftForm">
                        @csrf

                        <!-- Expected Cash Info -->
                        <div class="mb-6 p-5 bg-emerald-50 border border-emerald-100 rounded-2xl">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center">
                                    <span class="iconify text-white" data-icon="mdi:calculator" data-width="20"></span>
                                </div>
                                <div>
                                    <div class="text-xs text-slate-400 uppercase tracking-wide">Kas Seharusnya
                                    </div>
                                    <div class="text-sm text-slate-600">Modal Awal + Total Penjualan</div>
                                </div>
                            </div>
                            <div class="text-2xl font-semibold text-emerald-600">Rp {{ number_format($expectedCash, 0,
                                ',',
                                '.') }}</div>
                        </div>

                        <!-- Actual Cash Input -->
                        <div class="mb-6">
                            <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-3">
                                <span class="iconify text-blue-600" data-icon="mdi:cash" data-width="18"></span>
                                Kas Aktual (Hasil Hitung)
                            </label>
                            <div class="relative">
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 flex items-center gap-2">
                                    <span class="text-slate-400 font-semibold text-lg">Rp</span>
                                </div>
                                <input type="number" name="actual_cash" id="actual_cash"
                                    class="w-full pl-14 pr-4 py-5 text-2xl font-semibold border-2 border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all bg-slate-50 focus:bg-white text-slate-800"
                                    placeholder="0" required min="0" step="1">
                            </div>
                            <p class="mt-2 text-sm text-slate-500 font-medium" id="formatted_actual"></p>
                        </div>

                        <!-- Difference Display -->
                        <div id="difference_display" class="mb-6 hidden">
                            <div class="p-4 rounded-xl border-2" id="difference_box">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold" id="difference_label"></span>
                                    <span class="text-xl font-black" id="difference_amount"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-8">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Catatan (Opsional)</label>
                            <textarea name="notes"
                                class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none resize-none text-slate-700"
                                rows="3"
                                placeholder="Tambahkan catatan jika ada selisih atau kejadian khusus..."></textarea>
                        </div>

                        <!-- Action Buttons -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <button type="button" onclick="hideCloseForm()"
                                class="py-4 border-2 border-slate-300 text-slate-700 font-semibold rounded-xl hover:bg-slate-50 transition-all flex items-center justify-center gap-2">
                                <span class="iconify" data-icon="mdi:close" data-width="20"></span>
                                Batal
                            </button>
                            <button type="submit"
                                class="py-4 bg-rose-600 hover:bg-rose-700 text-white font-semibold rounded-xl transition-all shadow-sm hover:shadow-md flex items-center justify-center gap-2">
                                <span class="iconify" data-icon="mdi:lock-check" data-width="20"></span>
                                Tutup Kasir
                            </button>
                        </div>
                </div>
                </form>

                <!-- Warning -->
                <div class="mt-6 pt-6 border-t border-slate-200">
                    <p class="text-xs text-center text-slate-500">
                        <span class="iconify inline" data-icon="mdi:alert" data-width="14"></span>
                        Setelah kasir ditutup, Anda harus membuka shift baru untuk melakukan transaksi
                    </p>
                </div>
            </div>
        </div>

        @else
        <!-- NO SHIFT - Show Open Form -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <span class="iconify text-slate-400" data-icon="mdi:cash-register" data-width="32"></span>
                    </div>
                    <h1 class="text-2xl font-semibold text-slate-800 mb-2">Kasir Ditutup</h1>
                    <p class="text-slate-500">Masukkan modal awal untuk membuka shift kasir</p>
                </div>

                <!-- Info Stats -->
                <div class="grid grid-cols-2 gap-4 mb-8">
                    @if($lastClosedShift)
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="iconify text-slate-400" data-icon="mdi:clock-outline" data-width="16"></span>
                            <div class="text-xs text-slate-500 uppercase tracking-wide">Terakhir Tutup</div>
                        </div>
                        <div class="font-semibold text-slate-800">{{ $lastClosedShift->closed_at->format('d M Y, H:i')
                            }}</div>
                        <div class="text-sm text-slate-600 mt-1">Kas: Rp {{ number_format($lastClosedShift->actual_cash,
                            0, ',', '.') }}</div>
                    </div>
                    @else
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="iconify text-slate-400" data-icon="mdi:clock-outline" data-width="16"></span>
                            <div class="text-xs text-slate-500 uppercase tracking-wide">Terakhir Tutup</div>
                        </div>
                        <div class="text-sm text-slate-500">Belum ada shift sebelumnya</div>
                    </div>
                    @endif
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="iconify text-slate-400" data-icon="mdi:package-variant" data-width="16"></span>
                            <div class="text-xs text-slate-500 uppercase tracking-wide">Total Produk</div>
                        </div>
                        <div class="font-semibold text-slate-800 text-2xl">{{ number_format($totalProducts, 0, ',', '.')
                            }}</div>
                        <div class="text-sm text-slate-600 mt-1">Produk tersedia</div>
                    </div>
                </div>

                @if($errors->any())
                <div
                    class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-3">
                    <span class="iconify text-red-500 shrink-0" data-icon="mdi:alert-circle" data-width="20"></span>
                    <span class="text-sm">{{ $errors->first() }}</span>
                </div>
                @endif

                @if(session('success'))
                <div
                    class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-3">
                    <span class="iconify text-green-500 shrink-0" data-icon="mdi:check-circle" data-width="20"></span>
                    <span class="text-sm">{{ session('success') }}</span>
                </div>
                @endif

                <form action="{{ route('pos.shift.open') }}" method="POST" id="openShiftForm">
                    @csrf

                    <!-- Modal Awal Input -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Modal Awal</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-medium">Rp</span>
                            <input type="number" name="opening_balance" id="opening_balance"
                                class="w-full pl-12 pr-4 py-3 text-lg font-medium border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-slate-800"
                                placeholder="0" required min="0" step="1000" autofocus>
                        </div>
                    </div>

                    <!-- Nama Kasir Input -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Nama Kasir</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 iconify text-slate-400" data-icon="mdi:account-outline" data-width="20"></span>
                            <input type="text" name="cashier_name" id="cashier_name"
                                class="w-full pl-12 pr-4 py-3 text-lg font-medium border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-slate-800"
                                placeholder="Masukkan nama kasir" required>
                        </div>
                    </div>

                    <!-- Jam Shift Input -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Jam Shift</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 iconify text-slate-400" data-icon="mdi:clock-outline" data-width="20"></span>
                            <input type="text" name="shift_time" id="shift_time"
                                class="w-full pl-12 pr-4 py-3 text-lg font-medium border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-slate-800 bg-white"
                                placeholder="Contoh: Pagi (08:00 - 15:00)" required>
                        </div>
                    </div>

                    <!-- Quick Amount Buttons -->
                    <div class="grid grid-cols-4 gap-2 mb-6">
                        <button type="button" onclick="setAmount(100000)"
                            class="py-2 bg-slate-50 hover:bg-blue-50 border border-slate-200 hover:border-blue-300 rounded-lg text-slate-700 hover:text-blue-600 text-sm font-medium transition-all">100k</button>
                        <button type="button" onclick="setAmount(200000)"
                            class="py-2 bg-slate-50 hover:bg-blue-50 border border-slate-200 hover:border-blue-300 rounded-lg text-slate-700 hover:text-blue-600 text-sm font-medium transition-all">200k</button>
                        <button type="button" onclick="setAmount(500000)"
                            class="py-2 bg-slate-50 hover:bg-blue-50 border border-slate-200 hover:border-blue-300 rounded-lg text-slate-700 hover:text-blue-600 text-sm font-medium transition-all">500k</button>
                        <button type="button" onclick="setAmount(1000000)"
                            class="py-2 bg-slate-50 hover:bg-blue-50 border border-slate-200 hover:border-blue-300 rounded-lg text-slate-700 hover:text-blue-600 text-sm font-medium transition-all">1jt</button>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all shadow-sm hover:shadow-md flex items-center justify-center gap-2">
                        <span class="iconify" data-icon="mdi:lock-open-variant" data-width="20"></span>
                        Buka Kasir
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Transaction Detail Modal -->
<div id="transactionModal"
    class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
        <div id="modalContent"></div>
    </div>
</div>

@if($activeShift)
<!-- Edit Shift Modal -->
<div id="editShiftModal" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl overflow-hidden relative">
        <form action="{{ route('pos.shift.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <h2 class="text-xl font-bold text-slate-800">Edit Info Shift</h2>
                <button type="button" onclick="document.getElementById('editShiftModal').classList.add('hidden')" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-200 hover:text-slate-600 transition-colors">
                    <span class="iconify" data-icon="mdi:close" data-width="20"></span>
                </button>
            </div>
            
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Nama Kasir</label>
                    <input type="text" name="cashier_name" value="{{ $activeShift->cashier_name }}" required
                        class="w-full px-4 py-3 text-sm border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-slate-800">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Jam Shift</label>
                    <input type="text" name="shift_time" required
                        class="w-full px-4 py-3 text-sm border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-white text-slate-800"
                        value="{{ $activeShift->shift_time }}" placeholder="Contoh: Pagi (08:00 - 15:00)">
                </div>
            </div>
            
            <div class="p-6 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('editShiftModal').classList.add('hidden')" class="px-6 py-2.5 rounded-xl text-slate-600 font-semibold hover:bg-slate-200 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    // Transaction detail data
    const transactionsData = {!! json_encode($transactions->map(function($t) {
        return [
            'id' => $t->id,
            'number' => '#' . str_pad($t->id, 5, '0', STR_PAD_LEFT),
            'created_at' => $t->created_at->format('d M Y, H:i'),
            'customer' => $t->customer->name,
            'payment_status' => $t->payment_status,
            'total_amount' => $t->total_amount,
            'total_paid' => $t->total_paid,
            'remaining_receivable' => $t->remaining_receivable,
            'items' => collect($t->items)->map(function($item) {
                return [
                    'product_name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => $item->subtotal,
                ];
            })->values()
        ];
    })->values()) !!};

    function showTransactionDetail(transactionId) {
        const transaction = transactionsData.find(t => t.id === transactionId);
        if (!transaction) return;

        let statusBadge = '';
        if (transaction.payment_status === 'paid') {
            statusBadge = '<span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-sm font-semibold rounded-lg"><span class="iconify inline" data-icon="mdi:check-circle" data-width="16"></span> Lunas</span>';
        } else if (transaction.payment_status === 'partial') {
            statusBadge = '<span class="px-3 py-1 bg-amber-100 text-amber-700 text-sm font-semibold rounded-lg"><span class="iconify inline" data-icon="mdi:clock-outline" data-width="16"></span> Dibayar Sebagian</span>';
        } else {
            statusBadge = '<span class="px-3 py-1 bg-rose-100 text-rose-700 text-sm font-semibold rounded-lg"><span class="iconify inline" data-icon="mdi:alert-circle" data-width="16"></span> Piutang</span>';
        }

        let itemsHtml = '';
        transaction.items.forEach((item, index) => {
            itemsHtml += `
                <div class="flex items-center justify-between py-3 ${index > 0 ? 'border-t border-slate-100' : ''}">
                    <div class="flex-1">
                        <div class="font-medium text-slate-800">${item.product_name}</div>
                        <div class="text-sm text-slate-500 mt-1">
                            ${item.quantity} × Rp ${new Intl.NumberFormat('id-ID').format(item.price)}
                        </div>
                    </div>
                    <div class="font-semibold text-slate-800">
                        Rp ${new Intl.NumberFormat('id-ID').format(item.subtotal)}
                    </div>
                </div>
            `;
        });

        const modalContent = `
            <div class="p-6 border-b border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-slate-800">Detail Transaksi</h3>
                        <p class="text-sm text-slate-500 mt-1">${transaction.number}</p>
                    </div>
                    <button onclick="closeTransactionModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <span class="iconify" data-icon="mdi:close" data-width="24"></span>
                    </button>
                </div>
            </div>

            <div class="p-6 max-h-[calc(90vh-180px)] overflow-y-auto">
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <div class="text-xs text-slate-400 uppercase tracking-wide mb-1">Customer</div>
                        <div class="font-medium text-slate-800">${transaction.customer}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-400 uppercase tracking-wide mb-1">Waktu</div>
                        <div class="font-medium text-slate-800">${transaction.created_at}</div>
                    </div>
                </div>

                <div class="mb-6">
                    <div class="text-xs text-slate-400 uppercase tracking-wide mb-1">Status Pembayaran</div>
                    ${statusBadge}
                </div>

                <div class="mb-6">
                    <h4 class="font-semibold text-slate-800 mb-3 flex items-center gap-2">
                        <span class="iconify text-slate-500" data-icon="mdi:package-variant" data-width="20"></span>
                        Item Produk
                    </h4>
                    <div class="bg-slate-50 rounded-xl p-4">
                        ${itemsHtml}
                    </div>
                </div>

                <div class="bg-gradient-to-r from-slate-50 to-slate-100 rounded-xl p-4 space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600">Total Belanja</span>
                        <span class="font-semibold text-slate-800">Rp ${new Intl.NumberFormat('id-ID').format(transaction.total_amount)}</span>
                    </div>
                    ${transaction.payment_status !== 'paid' ? `
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600">Sudah Dibayar</span>
                        <span class="font-semibold text-emerald-600">Rp ${new Intl.NumberFormat('id-ID').format(transaction.total_paid)}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm pt-2 border-t border-slate-200">
                        <span class="text-slate-600 font-medium">Sisa Piutang</span>
                        <span class="font-semibold text-rose-600">Rp ${new Intl.NumberFormat('id-ID').format(transaction.remaining_receivable)}</span>
                    </div>
                    ` : ''}
                </div>
            </div>

            <div class="p-6 border-t border-slate-100 bg-slate-50">
                <button onclick="closeTransactionModal()" class="w-full py-3 bg-slate-600 hover:bg-slate-700 text-white font-semibold rounded-xl transition-all">
                    Tutup
                </button>
            </div>
        `;

        document.getElementById('modalContent').innerHTML = modalContent;
        document.getElementById('transactionModal').classList.remove('hidden');
    }

    function closeTransactionModal() {
        document.getElementById('transactionModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('transactionModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeTransactionModal();
        }
    });

    // Open shift functions
function setAmount(amount) {
    const input = document.getElementById('opening_balance');
    if (input) {
        input.value = amount;
        updateFormattedAmount();
    }
}

function updateFormattedAmount() {
    const input = document.getElementById('opening_balance');
    const formatted = document.getElementById('formatted_amount');
    if (!input || !formatted) return;

    const value = parseFloat(input.value) || 0;

    if (value > 0) {
        formatted.textContent = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(value);
    } else {
        formatted.textContent = '';
    }
}

const openInput = document.getElementById('opening_balance');
if (openInput) {
    openInput.addEventListener('input', updateFormattedAmount);
}

// Close shift functions
function showCloseForm() {
    const shiftCard = document.getElementById('shiftActiveCard');
    const closeForm = document.getElementById('closeForm');

    if (shiftCard) shiftCard.classList.add('hidden');
    if (closeForm) closeForm.classList.remove('hidden');

    document.getElementById('actual_cash').focus();
}

function hideCloseForm() {
    const shiftCard = document.getElementById('shiftActiveCard');
    const closeForm = document.getElementById('closeForm');

    if (closeForm) closeForm.classList.add('hidden');
    if (shiftCard) shiftCard.classList.remove('hidden');
}

const expectedCash = {{ $activeShift ? (int)$expectedCash : 0 }};

function updateFormattedActual() {
    const input = document.getElementById('actual_cash');
    const formatted = document.getElementById('formatted_actual');
    if (!input || !formatted) return;

    const value = parseInt(input.value) || 0;

    if (value > 0) {
        formatted.textContent = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(value);

        // Calculate difference
        const difference = value - expectedCash;
        const diffDisplay = document.getElementById('difference_display');
        const diffBox = document.getElementById('difference_box');
        const diffLabel = document.getElementById('difference_label');
        const diffAmount = document.getElementById('difference_amount');

        if (!diffDisplay || !diffBox || !diffLabel || !diffAmount) return;

        diffDisplay.classList.remove('hidden');

        if (difference === 0) {
            diffBox.className = 'p-4 rounded-xl border-2 bg-green-50 border-green-200';
            diffLabel.className = 'font-semibold text-green-700';
            diffLabel.innerHTML = '<span class="iconify inline" data-icon="mdi:check-circle" data-width="18"></span> Sesuai';
            diffAmount.className = 'text-xl font-black text-green-600';
        } else if (difference > 0) {
            diffBox.className = 'p-4 rounded-xl border-2 bg-blue-50 border-blue-200';
            diffLabel.className = 'font-semibold text-blue-700';
            diffLabel.textContent = 'Kelebihan';
            diffAmount.className = 'text-xl font-black text-blue-600';
        } else {
            diffBox.className = 'p-4 rounded-xl border-2 bg-red-50 border-red-200';
            diffLabel.className = 'font-semibold text-red-700';
            diffLabel.textContent = 'Kekurangan';
            diffAmount.className = 'text-xl font-black text-red-600';
        }

        diffAmount.textContent = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(Math.abs(difference));
    } else {
        formatted.textContent = '';
        if (diffDisplay) diffDisplay.classList.add('hidden');
    }
}

const actualInput = document.getElementById('actual_cash');
if (actualInput) {
    actualInput.addEventListener('input', updateFormattedActual);
}

// Show close form if there are errors (validation failed)
@if($errors->any() && $activeShift)
    showCloseForm();
@endif
</script>
@endsection
