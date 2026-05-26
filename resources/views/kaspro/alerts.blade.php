@extends('layouts.admin')

@section('title', 'Peringatan KasPro')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Peringatan</h1>
        <p class="text-gray-600 mt-1">Daftar peringatan dari sistem KasPro</p>
    </div>

    <!-- Alert Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Darurat</p>
                    <p class="text-2xl font-bold text-red-600">2</p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <span class="text-xl">🔴</span>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Kritis</p>
                    <p class="text-2xl font-bold text-orange-600">3</p>
                </div>
                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                    <span class="text-xl">🟠</span>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Peringatan</p>
                    <p class="text-2xl font-bold text-yellow-600">4</p>
                </div>
                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                    <span class="text-xl">🟡</span>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Info</p>
                    <p class="text-2xl font-bold text-blue-600">2</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="text-xl">🔵</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex flex-wrap gap-2 mb-6 border-b border-gray-200 pb-2">
        <button class="px-4 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600">Semua (11)</button>
        <button class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">🔴 Darurat (2)</button>
        <button class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">🟠 Kritis (3)</button>
        <button class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">🟡 Peringatan (4)</button>
        <button class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">🔵 Info (2)</button>
        <button class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 ml-auto">Tandai Semua Dibaca</button>
    </div>

    <!-- Alerts List -->
    <div class="space-y-3">
        
        <!-- Emergency Alert 1 - Belum Dibaca -->
        <div class="bg-white rounded-xl shadow-sm border-l-4 border-red-500 p-5 hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                            🔴 Darurat
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                            Baru
                        </span>
                        <span class="text-sm font-semibold text-gray-900"><span class="iconify inline-block" data-icon="mdi:alert-outline"></span> Saldo Kas Kritis!</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3">Saldo kas saat ini hanya Rp 1.250.000. Bisnis hanya bisa bertahan 5 hari tanpa pemasukan baru. Segera lakukan tindakan!</p>
                    <div class="flex items-center gap-4 text-xs text-gray-400">
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            25 Maret 2026, 14:30
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            2 jam yang lalu
                        </span>
                    </div>
                </div>
                <button class="ml-4 px-3 py-1.5 text-sm text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-50 transition">
                    Tandai Dibaca
                </button>
            </div>
        </div>

        <!-- Emergency Alert 2 - Belum Dibaca -->
        <div class="bg-white rounded-xl shadow-sm border-l-4 border-red-500 p-5 hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                            🔴 Darurat
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                            Baru
                        </span>
                        <span class="text-sm font-semibold text-gray-900"><span class="iconify inline-block" data-icon="mdi:alert-outline"></span> Pembayaran Gaji Mendekat!</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3">7 hari lagi Anda harus membayar gaji Rp 5.000.000. Saldo saat ini Rp 1.250.000. Segera cari tambahan dana!</p>
                    <div class="flex items-center gap-4 text-xs text-gray-400">
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            25 Maret 2026, 10:15
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            5 jam yang lalu
                        </span>
                    </div>
                </div>
                <button class="ml-4 px-3 py-1.5 text-sm text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-50 transition">
                    Tandai Dibaca
                </button>
            </div>
        </div>

        <!-- Critical Alert 1 - Belum Dibaca -->
        <div class="bg-white rounded-xl shadow-sm border-l-4 border-orange-500 p-5 hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                            🟠 Kritis
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                            Baru
                        </span>
                        <span class="text-sm font-semibold text-gray-900">📉 Potensi Kekurangan Kas</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3">Bulan Juni 2026 diprediksi kekurangan kas sebesar Rp 18.725.288. Mulai sisihkan dana dari sekarang!</p>
                    <div class="flex items-center gap-4 text-xs text-gray-400">
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            24 Maret 2026, 08:00
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            1 hari yang lalu
                        </span>
                    </div>
                </div>
                <button class="ml-4 px-3 py-1.5 text-sm text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-50 transition">
                    Tandai Dibaca
                </button>
            </div>
        </div>

        <!-- Critical Alert 2 - Sudah Dibaca -->
        <div class="bg-gray-50 rounded-xl shadow-sm border-l-4 border-gray-300 p-5 hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                            🟠 Kritis
                        </span>
                        <span class="text-sm font-semibold text-gray-900"><span class="iconify inline-block" data-icon="mdi:alert-outline"></span> Saldo Kas Menipis</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3">Saldo kas hanya Rp 5.200.000. Bisnis hanya bisa bertahan 12 hari. Hindari pembelian besar dan fokus pada penagihan piutang.</p>
                    <div class="flex items-center gap-4 text-xs text-gray-400">
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            23 Maret 2026, 09:30
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            2 hari yang lalu
                        </span>
                        <span class="flex items-center gap-1 text-green-600">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Sudah dibaca
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Critical Alert 3 - Sudah Dibaca -->
        <div class="bg-gray-50 rounded-xl shadow-sm border-l-4 border-gray-300 p-5 hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                            🟠 Kritis
                        </span>
                        <span class="text-sm font-semibold text-gray-900"><span class="iconify inline-block" data-icon="mdi:chart-bar"></span> Pengeluaran Melebihi Target</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3">Pengeluaran bulan Maret mencapai Rp 18.096.043, melebihi anggaran 15%. Evaluasi ulang pengeluaran bulan depan.</p>
                    <div class="flex items-center gap-4 text-xs text-gray-400">
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            22 Maret 2026, 14:00
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            3 hari yang lalu
                        </span>
                        <span class="flex items-center gap-1 text-green-600">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Sudah dibaca
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Warning Alert 1 - Sudah Dibaca -->
        <div class="bg-gray-50 rounded-xl shadow-sm border-l-4 border-gray-300 p-5 hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                            🟡 Peringatan
                        </span>
                        <span class="text-sm font-semibold text-gray-900"><span class="iconify inline-block" data-icon="mdi:chart-bar"></span> Pengeluaran Tinggi</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3">Pengeluaran bulan ini mencapai Rp 24.088.787, naik 35% dibanding bulan lalu. Periksa detail pengeluaran dan evaluasi efisiensi!</p>
                    <div class="flex items-center gap-4 text-xs text-gray-400">
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            21 Maret 2026, 11:20
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            4 hari yang lalu
                        </span>
                        <span class="flex items-center gap-1 text-green-600">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Sudah dibaca
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Warning Alert 2 - Sudah Dibaca -->
        <div class="bg-gray-50 rounded-xl shadow-sm border-l-4 border-gray-300 p-5 hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                            🟡 Peringatan
                        </span>
                        <span class="text-sm font-semibold text-gray-900">🔍 Anomali Transaksi Terdeteksi</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3">Transaksi pembelian dari supplier baru "PT Bangun Jaya" dengan nominal Rp 8.500.000 pada pukul 02:15 AM. Mohon konfirmasi keabsahan transaksi.</p>
                    <div class="flex items-center gap-4 text-xs text-gray-400">
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            20 Maret 2026, 02:15
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            5 hari yang lalu
                        </span>
                        <span class="flex items-center gap-1 text-green-600">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Sudah dibaca
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Alert 1 - Sudah Dibaca -->
        <div class="bg-gray-50 rounded-xl shadow-sm border-l-4 border-gray-300 p-5 hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                            🔵 Info
                        </span>
                        <span class="text-sm font-semibold text-gray-900"><span class="iconify inline-block" data-icon="mdi:chart-bar"></span> Insight Pengeluaran</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3">Pengeluaran untuk kategori "Pembelian Stok" meningkat 15% dibanding bulan lalu. Cek apakah ada kenaikan harga dari supplier.</p>
                    <div class="flex items-center gap-4 text-xs text-gray-400">
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            17 Maret 2026, 10:30
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            8 hari yang lalu
                        </span>
                        <span class="flex items-center gap-1 text-green-600">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Sudah dibaca
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Alert 2 - Selesai -->
        <div class="bg-gray-50 rounded-xl shadow-sm border-l-4 border-green-400 p-5 hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                            🔵 Info
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                            ✓ Selesai
                        </span>
                        <span class="text-sm font-semibold text-gray-900">📈 Prediksi Positif</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3">Berdasarkan data historis, bulan Desember diprediksi terjadi peningkatan penjualan 40%. Siapkan stok lebih banyak!</p>
                    <div class="flex items-center gap-4 text-xs text-gray-400">
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            16 Maret 2026, 08:00
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            9 hari yang lalu
                        </span>
                        <span class="flex items-center gap-1 text-green-600">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Selesai
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Tips Section -->
    <div class="mt-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-5 border border-blue-100">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                <span class="text-xl">💡</span>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 mb-2">Tips Menghadapi Peringatan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div class="flex items-start gap-2">
                        <span class="text-red-500">🔴</span>
                        <span class="text-gray-600"><strong class="text-gray-800">Darurat</strong>: Segera evaluasi arus kas dan cari tambahan dana</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-orange-500">🟠</span>
                        <span class="text-gray-600"><strong class="text-gray-800">Kritis</strong>: Kurangi pengeluaran tidak perlu, percepat penagihan</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-yellow-500">🟡</span>
                        <span class="text-gray-600"><strong class="text-gray-800">Peringatan</strong>: Periksa detail transaksi, rencanakan anggaran</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-blue-500">🔵</span>
                        <span class="text-gray-600"><strong class="text-gray-800">Info</strong>: Gunakan sebagai insight untuk perencanaan bisnis</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function markAsRead(button) {
    const alertDiv = button.closest('.rounded-xl');
    alertDiv.classList.remove('border-l-red-500', 'border-l-orange-500');
    alertDiv.classList.add('border-l-gray-300', 'bg-gray-50');
    button.remove();
    const statusSpan = alertDiv.querySelector('.flex.items-center.gap-3.mb-2');
    if (statusSpan) {
        const readBadge = document.createElement('span');
        readBadge.className = 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700';
        readBadge.innerHTML = '✓ Sudah dibaca';
        statusSpan.appendChild(readBadge);
    }
}
</script>
@endsection