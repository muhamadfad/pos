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
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\PurchasePaymentController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OrderPaymentController;
use App\Http\Controllers\Admin\ExpenseController;
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
    Route::get('/pos/customer-debt/{customer}', [PointOfSaleController::class, 'customerDebt'])->name('pos.customer-debt');
    Route::post('/pos/pay-debt', [PointOfSaleController::class, 'payDebt'])->name('pos.pay-debt');

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

        // ===== PURCHASES (Barang Masuk dari Supplier) =====
        Route::prefix('purchases')->name('purchases.')->group(function () {
            Route::get('/', [PurchaseController::class, 'index'])->name('index');
            Route::get('/select-supplier', [PurchaseController::class, 'selectSupplier'])->name('select-supplier');
            Route::get('/supplier/{supplier}', [PurchaseController::class, 'perSupplier'])->name('per-supplier');
            Route::get('/supplier/{supplier}/download', [PurchaseController::class, 'downloadPdf'])->name('download');
            Route::get('/supplier/{supplier}/create', [PurchaseController::class, 'create'])->name('create');
            Route::post('/supplier/{supplier}', [PurchaseController::class, 'store'])->name('store');
            Route::get('/{purchase}', [PurchaseController::class, 'show'])->name('show');
            Route::post('/{purchase}/pay', [PurchasePaymentController::class, 'pay'])->name('pay');
        });

        // ===== PURCHASE PAYMENTS (Hutang ke Supplier) =====
        Route::prefix('purchase-payments')->name('purchase-payments.')->group(function () {
            Route::get('/', [PurchasePaymentController::class, 'index'])->name('index');
            Route::post('/', [PurchasePaymentController::class, 'store'])->name('store');
        });

        // ===== ORDERS (Penjualan ke Customer) =====
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/select-customer', [OrderController::class, 'selectCustomer'])->name('select-customer');
            Route::get('/customer/{customer}', [OrderController::class, 'perCustomer'])->name('per-customer');
            Route::get('/customer/{customer}/download', [OrderController::class, 'downloadPdf'])->name('download');
            Route::get('/customer/{customer}/create', [OrderController::class, 'create'])->name('create');
            Route::post('/customer/{customer}', [OrderController::class, 'store'])->name('store');
            Route::get('/{order}', [OrderController::class, 'show'])->name('show');
            Route::post('/{order}/receive', [OrderPaymentController::class, 'receive'])->name('receive');
        });

        // ===== ORDER PAYMENTS (Piutang dari Customer) =====
        Route::prefix('order-payments')->name('order-payments.')->group(function () {
            Route::get('/', [OrderPaymentController::class, 'index'])->name('index');
        });

        // ===== FINANCE =====
        Route::prefix('finance')->name('finance.')->group(function () {
            Route::get('/', [FinanceController::class, 'index'])->name('index');
            Route::get('cashflow', [FinanceController::class, 'cashflow'])->name('cashflow');
            Route::get('daily', [FinanceController::class, 'daily'])->name('daily');
            Route::get('weekly', [FinanceController::class, 'weekly'])->name('weekly');
            Route::get('monthly', [FinanceController::class, 'monthly'])->name('monthly');
        });

        // ===== EXPENSES =====
        Route::resource('expenses', ExpenseController::class);
    });

require __DIR__ . '/auth.php';
