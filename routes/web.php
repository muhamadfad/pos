<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController; // 👈 Tambahkan
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\MitraController;



// USER DASHBOARD
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy'); // 👈 TAMBAHKAN INI
});

// Admin Routes
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

        // ===== Transaksi Barang =====
        Route::prefix('transactions')->name('transactions.')->group(function () {
            // Barang Masuk
            Route::get('in', [TransactionController::class, 'indexIn'])->name('in.index');
            Route::get('in/create', [TransactionController::class, 'createIn'])->name('in.create');
            Route::post('in', [TransactionController::class, 'storeIn'])->name('in.store');

            // Barang Keluar
            Route::get('out', [TransactionController::class, 'indexOut'])->name('out.index');
            Route::get('out/create', [TransactionController::class, 'createOut'])->name('out.create');
            Route::post('out', [TransactionController::class, 'storeOut'])->name('out.store');
        });
         Route::resource('mitra', MitraController::class); // 👈 TAMBAHAN UTAMA

        Route::prefix('finance')->name('finance.')->group(function () {
            Route::get('/', [FinanceController::class, 'index'])->name('index');        // Laporan utama
            Route::get('daily', [FinanceController::class, 'daily'])->name('daily');    // Harian
            Route::get('weekly', [FinanceController::class, 'weekly'])->name('weekly'); // Mingguan
            Route::get('monthly', [FinanceController::class, 'monthly'])->name('monthly'); // Bulanan
        });
    });

require __DIR__.'/auth.php';
