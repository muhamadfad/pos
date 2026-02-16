<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\StockEntryController;
use App\Http\Controllers\Admin\StockOutController;
use App\Http\Controllers\Admin\DebtPaymentController;
use App\Http\Controllers\Admin\ReceivablePaymentController;
use App\Http\Controllers\PointOfSaleController;
use App\Http\Controllers\CashierShiftController;

// USER DASHBOARD
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Point of Sale (Kasir)
    Route::get('/pos', [PointOfSaleController::class, 'index'])->name('pos.index');
    Route::post('/pos/checkout', [PointOfSaleController::class, 'checkout'])->name('pos.checkout');

    // Cashier Shift Management
    Route::get('/pos/shift', [CashierShiftController::class, 'index'])->name('pos.shift.index');
    Route::post('/pos/shift/open', [CashierShiftController::class, 'store'])->name('pos.shift.open');
    Route::post('/pos/shift/close', [CashierShiftController::class, 'close'])->name('pos.shift.close');
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
        Route::resource('suppliers', SupplierController::class);
        Route::resource('customers', CustomerController::class);


        // ===== STOCK ENTRIES (Barang Masuk) =====
        Route::prefix('transactions/in')->name('transactions.in.')->group(function () {

            Route::get('/', [StockEntryController::class, 'index'])->name('index');

            Route::get('/select-supplier', [StockEntryController::class, 'selectSupplier'])->name('selectSupplier');

            Route::get('/supplier/{supplier}', [StockEntryController::class, 'transactionsPerSupplier'])->name('perSupplier');
            Route::get(
                '/supplier/{supplier}/download',
                [StockEntryController::class, 'downloadPdf']
            )->name('download');

            Route::get('/supplier/{supplier}/create', [StockEntryController::class, 'create'])->name('create');

            Route::post('/supplier/{supplier}', [StockEntryController::class, 'store'])->name('store');

            Route::get('/detail/{id}', [StockEntryController::class, 'show'])->name('show');
            Route::post('/{id}/pay', [DebtPaymentController::class, 'payDebt'])
                ->name('pay');
        });

        // ===== STOCK OUTS (Barang Keluar) =====
        Route::prefix('transactions/out')->name('transactions.out.')->group(function () {
            Route::get('/', [StockOutController::class, 'index'])->name('index');

            Route::get('/select-customer', [StockOutController::class, 'selectCustomer'])->name('selectCustomer');

            Route::get('/customer/{customer}', [StockOutController::class, 'perCustomer'])->name('perCustomer');
            Route::get('/customer/{customer}/download', [StockOutController::class, 'download'])->name('download');

            Route::get('/customer/{customer}/create', [StockOutController::class, 'create'])->name('create');
            Route::post('/customer/{customer}', [StockOutController::class, 'store'])->name('store');

            Route::get('/detail/{id}', [StockOutController::class, 'show'])->name('show');
            Route::post('/{id}/receive', [ReceivablePaymentController::class, 'receivePayment'])->name('receive');
        });

        // ===== DEBT PAYMENTS =====
        Route::resource('debt-payments', DebtPaymentController::class)
            ->only(['index', 'show', 'store']);

        // ===== RECEIVABLE PAYMENTS =====
        Route::resource('receivable-payments', ReceivablePaymentController::class)
            ->only(['index', 'show']);

        // ===== FINANCE =====
        Route::prefix('finance')->name('finance.')->group(function () {
            Route::get('/', [FinanceController::class, 'index'])->name('index');
            Route::get('daily', [FinanceController::class, 'daily'])->name('daily');
            Route::get('weekly', [FinanceController::class, 'weekly'])->name('weekly');
            Route::get('monthly', [FinanceController::class, 'monthly'])->name('monthly');
        });
    });

require __DIR__ . '/auth.php';
