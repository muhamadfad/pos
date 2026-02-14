<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\MitraController;
use App\Http\Controllers\Admin\BarangMasukController;
use App\Http\Controllers\Admin\BarangKeluarController;
use App\Http\Controllers\Admin\PembayaranHutangController;

// USER DASHBOARD
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ADMIN ROUTES
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Master Data
        Route::resource('categories', CategoryController::class);
        Route::resource('units', UnitController::class);
        Route::resource('products', ProductController::class);
        Route::get('/products/barcode/{barcode}', [ProductController::class, 'getByBarcode'])
            ->name('products.barcode');
        Route::resource('users', UserController::class);
        Route::resource('mitra', MitraController::class);

      
        // ===== TRANSAKSI BARANG MASUK =====
        Route::prefix('transactions/in')->name('transactions.in.')->group(function () {

            Route::get('/', [BarangMasukController::class, 'index'])->name('index');

            Route::get('/pilih-mitra', [BarangMasukController::class, 'pilihMitra'])->name('pilihMitra');

            Route::get('/mitra/{mitra}', [BarangMasukController::class, 'transaksiPerMitra'])->name('perMitra');
            Route::get('/mitra/{mitra}/download', 
                        [BarangMasukController::class, 'downloadPdf']
                    )->name('download');

            Route::get('/mitra/{mitra}/create', [BarangMasukController::class, 'create'])->name('create');

            Route::post('/mitra/{mitra}', [BarangMasukController::class, 'store'])->name('store');

            Route::get('/detail/{id}', [BarangMasukController::class, 'show'])->name('show');
            Route::post('/{id}/bayar', [PembayaranHutangController::class, 'bayarHutang'])
    ->name('bayar');

        });

        // ===== TRANSAKSI BARANG KELUAR =====
        Route::prefix('transactions/out')->name('transactions.out.')->group(function () {
            Route::get('/', [BarangKeluarController::class, 'index'])->name('index');
            Route::get('/create', [BarangKeluarController::class, 'create'])->name('create');
            Route::post('/', [BarangKeluarController::class, 'store'])->name('store');
        });

        // ===== PEMBAYARAN HUTANG =====
        Route::resource('pembayaran-hutang', PembayaranHutangController::class)
            ->only(['index', 'show', 'store']);

        // ===== FINANCE =====
        Route::prefix('finance')->name('finance.')->group(function () {
            Route::get('/', [FinanceController::class, 'index'])->name('index');
            Route::get('daily', [FinanceController::class, 'daily'])->name('daily');
            Route::get('weekly', [FinanceController::class, 'weekly'])->name('weekly');
            Route::get('monthly', [FinanceController::class, 'monthly'])->name('monthly');
        });
    });

require __DIR__.'/auth.php';
