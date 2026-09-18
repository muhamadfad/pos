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
use App\Http\Controllers\BranchController;
use App\Http\Controllers\KasproController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\BranchStockController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =======================
// ROUTES UNTUK SEMUA USER YANG LOGIN
// =======================
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Home - redirect berdasarkan role
    Route::get('/', function () {
        $user = auth()->user();
        
        if ($user->role === 'kasir') {
            return redirect()->route('kasir.shift.index');
        }
        
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        
        // super_admin
        return redirect()->route('dashboard');
    })->name('home');
    
    // Dashboard untuk super_admin dan admin
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/activities', [DashboardController::class, 'activities'])->name('dashboard.activities');
    Route::get('/dashboard/export', [DashboardController::class, 'export'])->name('dashboard.export');
    Route::post('/dashboard/filter', [DashboardController::class, 'filter'])->name('dashboard.filter');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Point of Sale (Kasir & Admin bisa akses)
    Route::get('/pos', [PointOfSaleController::class, 'index'])->name('pos.index');
    Route::post('/pos/checkout', [PointOfSaleController::class, 'checkout'])->name('pos.checkout');
    Route::get('/pos/customer-debt/{customer}', [PointOfSaleController::class, 'customerDebt'])->name('pos.customer-debt');
    Route::post('/pos/pay-debt', [PointOfSaleController::class, 'payDebt'])->name('pos.pay-debt');
    Route::get('/pos/print/{order}', [PointOfSaleController::class, 'printReceipt'])->name('pos.print');
    Route::post('/pos/expenses', [PointOfSaleController::class, 'storeExpense'])->name('pos.expenses.store');

    // Cashier Shift Management
    Route::prefix('pos/shift')->name('pos.shift.')->group(function () {
        Route::get('/', [CashierShiftController::class, 'index'])->name('index');
        Route::post('/open', [CashierShiftController::class, 'store'])->name('open');
        Route::put('/update', [CashierShiftController::class, 'update'])->name('update');
        Route::post('/close', [CashierShiftController::class, 'close'])->name('close');
        Route::get('/report', [CashierShiftController::class, 'report'])->name('report');
    });

    // KasPro Routes (Intelligence)
    Route::prefix('kaspro')->name('kaspro.')->group(function () {
        Route::get('/dashboard', [KasproController::class, 'dashboard'])->name('dashboard');
        Route::get('/prediction', [KasproController::class, 'prediction'])->name('prediction');
        Route::get('/alerts', [KasproController::class, 'alerts'])->name('alerts');
        Route::post('/alerts/{alert}/read', [KasproController::class, 'markAsRead'])->name('alerts.read');
        Route::get('/margin-analysis', [KasproController::class, 'marginAnalysis'])->name('margin-analysis');
        Route::post('/predict', [KasproController::class, 'generatePrediction'])->name('generate');
    });
});

// =======================
// ROUTES UNTUK ADMIN & SUPER ADMIN
// =======================
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard Admin (redirect ke dashboard utama)
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // =======================
        // MASTER DATA
        // =======================
        Route::resource('categories', CategoryController::class);
        Route::resource('units', UnitController::class);
        Route::resource('products', ProductController::class);
        Route::get('/products/barcode/{barcode}', [ProductController::class, 'getByBarcode'])->name('products.barcode');
        Route::post('/products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
        
        // Ingredients (Bahan Baku)
        Route::resource('ingredients', IngredientController::class);
        Route::post('/ingredients/{ingredient}/adjust-stock', [IngredientController::class, 'adjustStock'])->name('ingredients.adjust-stock');
        Route::post('/ingredients/{ingredient}/quick-update-stock', [IngredientController::class, 'quickUpdateStock'])->name('ingredients.quick-update-stock');
        
        // Recipes (Resep Menu)
        Route::resource('recipes', RecipeController::class);
        Route::get('/recipes/{product}/details', [RecipeController::class, 'getByProduct'])->name('recipes.by-product');
        
        // Suppliers
        Route::resource('suppliers', SupplierController::class);
        Route::post('/suppliers/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('suppliers.toggle-status');
        
        // Customers
        Route::resource('customers', CustomerController::class);
        Route::post('/customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');
        Route::get('/customers/{customer}/debt', [CustomerController::class, 'debtHistory'])->name('customers.debt');
        
        // Users (untuk admin biasa hanya bisa manage user di branch-nya sendiri)
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        // =======================
        // =======================
        // PURCHASES (Barang Masuk dari Supplier)
        // =======================
        Route::prefix('purchases')->name('purchases.')->group(function () {
            Route::get('/', [PurchaseController::class, 'index'])->name('index');
            Route::get('/select-supplier', [PurchaseController::class, 'selectSupplier'])->name('select-supplier');
            Route::get('/supplier/{supplier}', [PurchaseController::class, 'perSupplier'])->name('per-supplier');
            Route::get('/supplier/{supplier}/download', [PurchaseController::class, 'downloadPdf'])->name('download');
            Route::get('/create', [PurchaseController::class, 'create'])->name('create-no-supplier');
            Route::post('/', [PurchaseController::class, 'store'])->name('store-no-supplier');
            Route::get('/supplier/{supplier}/create', [PurchaseController::class, 'create'])->name('create');
            Route::post('/supplier/{supplier}', [PurchaseController::class, 'store'])->name('store');
            Route::get('/{purchase}', [PurchaseController::class, 'show'])->name('show');
            Route::delete('/{purchase}', [PurchaseController::class, 'destroy'])->name('destroy');
            Route::post('/{purchase}/pay', [PurchasePaymentController::class, 'pay'])->name('pay');
        });

        // =======================
        // LOGISTIK PUSAT
        // =======================
        Route::middleware([\App\Http\Middleware\CheckPusatAccess::class])->group(function () {
            Route::prefix('central-purchases')->name('central-purchases.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\CentralPurchaseController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\CentralPurchaseController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\CentralPurchaseController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\Admin\CentralPurchaseController::class, 'show'])->name('show');
        });

        Route::prefix('central-productions')->name('central-productions.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\CentralProductionController::class, 'index'])->name('index');
            Route::get('/download-pdf', [\App\Http\Controllers\Admin\CentralProductionController::class, 'downloadPdf'])->name('download-pdf');
            Route::get('/download-combined-pdf', [\App\Http\Controllers\Admin\CentralProductionController::class, 'downloadCombinedPdf'])->name('download-combined-pdf');
            Route::get('/create', [\App\Http\Controllers\Admin\CentralProductionController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\CentralProductionController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\Admin\CentralProductionController::class, 'show'])->name('show');
        });
        
        Route::get('/central-stocks', [\App\Http\Controllers\Admin\CentralStockController::class, 'index'])->name('central-stocks.index');
        Route::get('/central-stocks/download-pdf', [\App\Http\Controllers\Admin\CentralStockController::class, 'downloadPdf'])->name('central-stocks.download-pdf');

        Route::prefix('stock-transfers')->name('stock-transfers.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\StockTransferController::class, 'index'])->name('index');
            Route::get('/download-pdf', [\App\Http\Controllers\Admin\StockTransferController::class, 'downloadPdf'])->name('download-pdf');
            Route::get('/create', [\App\Http\Controllers\Admin\StockTransferController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\StockTransferController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\Admin\StockTransferController::class, 'show'])->name('show');
        });

        Route::prefix('stock-returns')->name('stock-returns.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\StockReturnController::class, 'index'])->name('index');
            Route::get('/download-pdf', [\App\Http\Controllers\Admin\StockReturnController::class, 'downloadPdf'])->name('download-pdf');
            Route::get('/create', [\App\Http\Controllers\Admin\StockReturnController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\StockReturnController::class, 'store'])->name('store');
            Route::post('/daily-summary', [\App\Http\Controllers\Admin\StockReturnController::class, 'storeDailySummary'])->name('store-daily-summary');
            Route::get('/{id}', [\App\Http\Controllers\Admin\StockReturnController::class, 'show'])->name('show');
        });
        });

        // =======================
        // PURCHASE PAYMENTS (Hutang ke Supplier)
        // =======================
        Route::prefix('purchase-payments')->name('purchase-payments.')->group(function () {
            Route::get('/', [PurchasePaymentController::class, 'index'])->name('index');
            Route::post('/', [PurchasePaymentController::class, 'store'])->name('store');
            Route::get('/{payment}/receipt', [PurchasePaymentController::class, 'receipt'])->name('receipt');
        });

        // =======================
        // ORDERS (Penjualan ke Customer)
        // =======================
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/select-customer', [OrderController::class, 'selectCustomer'])->name('select-customer');
            Route::get('/customer/{customer}', [OrderController::class, 'perCustomer'])->name('per-customer');
            Route::get('/customer/{customer}/download', [OrderController::class, 'downloadPdf'])->name('download');
            Route::get('/customer/{customer}/create', [OrderController::class, 'create'])->name('create');
            Route::post('/customer/{customer}', [OrderController::class, 'store'])->name('store');
            Route::get('/{order}', [OrderController::class, 'show'])->name('show');
            Route::delete('/{order}', [OrderController::class, 'destroy'])->name('destroy');
            Route::get('/{order}/receipt', [OrderController::class, 'printReceipt'])->name('receipt');
            Route::post('/{order}/receive', [OrderPaymentController::class, 'receive'])->name('receive');
        });

        // =======================
        // ORDER PAYMENTS (Piutang dari Customer)
        // =======================
        Route::prefix('order-payments')->name('order-payments.')->group(function () {
            Route::get('/', [OrderPaymentController::class, 'index'])->name('index');
            Route::post('/', [OrderPaymentController::class, 'store'])->name('store');
            Route::get('/{payment}/receipt', [OrderPaymentController::class, 'receipt'])->name('receipt');
        });



        // =======================
        // FINANCE & REPORTING
        // =======================
        Route::prefix('finance')->name('finance.')->group(function () {
            Route::get('/', [FinanceController::class, 'index'])->name('index');
            Route::get('/cashflow', [FinanceController::class, 'cashflow'])->name('cashflow');
            Route::get('/daily', [FinanceController::class, 'daily'])->name('daily');
            Route::get('/weekly', [FinanceController::class, 'weekly'])->name('weekly');
            Route::get('/monthly', [FinanceController::class, 'monthly'])->name('monthly');
            Route::get('/yearly', [FinanceController::class, 'yearly'])->name('yearly');
            Route::get('/profit-loss', [FinanceController::class, 'profitLoss'])->name('profit-loss');
            Route::get('/export', [FinanceController::class, 'export'])->name('export');
        });

        // =======================
        // REPORTS
        // =======================

        // =======================
        // BRANCH STOCK MANAGEMENT (Stok Bahan Baku per Cabang)
        // =======================
        Route::prefix('branch-stock')->name('branch-stock.')->group(function () {
            Route::get('/', [BranchStockController::class, 'index'])->name('index');
            Route::get('/create', [BranchStockController::class, 'create'])->name('create');
            Route::post('/', [BranchStockController::class, 'store'])->name('store');
            Route::get('/{id}', [BranchStockController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [BranchStockController::class, 'edit'])->name('edit');
            Route::put('/{id}', [BranchStockController::class, 'update'])->name('update');
            Route::delete('/{id}', [BranchStockController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/adjust', [BranchStockController::class, 'adjustStock'])->name('adjust');
            Route::post('/bulk-update', [BranchStockController::class, 'bulkUpdate'])->name('bulk-update');
            Route::get('/low-stock/report', [BranchStockController::class, 'lowStockReport'])->name('low-stock');
        });
        
        // HPP Report
        Route::get('/hpp-report', [RecipeController::class, 'hppReport'])->name('hpp-report');
    });

// =======================
// EXPENSES (Pengeluaran - Bisa diakses Admin & Kasir)
// =======================
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('expenses', ExpenseController::class);
    Route::get('/expenses/category/{category}', [ExpenseController::class, 'byCategory'])->name('expenses.by-category');
});

// =======================
// ROUTES KHUSUS SUPER ADMIN
// =======================
Route::middleware(['auth', 'super_admin'])
    ->prefix('super')
    ->name('super.')
    ->group(function () {

        // Super Admin Dashboard (redirect ke dashboard utama dengan filter)
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // =======================
        // BRANCH MANAGEMENT
        // =======================
        Route::resource('branches', BranchController::class);
        Route::post('/branches/{branch}/toggle-status', [BranchController::class, 'toggleStatus'])->name('branches.toggle-status');
        Route::get('/branches/{branch}/stats', [BranchController::class, 'stats'])->name('branches.stats');
        
        // =======================
        // USER MANAGEMENT (ALL BRANCHES)
        // =======================
        Route::get('/users', [UserController::class, 'all'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::post('/users/{user}/change-branch', [UserController::class, 'changeBranch'])->name('users.change-branch');
        
        // =======================
        // SYSTEM MONITORING
        // =======================
        
        // =======================
        // SYSTEM SETTINGS
        // =======================
        
        // =======================
        // GLOBAL REPORTS (ALL BRANCHES)
        // =======================
        
        // =======================
        // DATABASE MANAGEMENT
        // =======================
    });

// =======================
// ROUTES KHUSUS KASIR (Sederhana)
// =======================
Route::middleware(['auth', 'kasir'])
    ->prefix('kasir')
    ->name('kasir.')
    ->group(function () {
        
        // Redirect root kasir ke shift
        Route::get('/', function () {
            return redirect()->route('kasir.shift.index');
        });
        
        // Dashboard Kasir (redirect ke shift)
        Route::get('/dashboard', function () {
            return redirect()->route('kasir.shift.index');
        });
        
        // POS - hanya bisa akses setelah shift dibuka
        Route::get('/pos', [PointOfSaleController::class, 'index'])->name('pos');
        Route::post('/pos/checkout', [PointOfSaleController::class, 'checkout'])->name('checkout');
        Route::get('/pos/print/{order}', [PointOfSaleController::class, 'printReceipt'])->name('print');
        Route::get('/pos/customer-debt/{customer}', [PointOfSaleController::class, 'customerDebt'])->name('customer-debt');
        Route::post('/pos/pay-debt', [PointOfSaleController::class, 'payDebt'])->name('pay-debt');
        Route::post('/pos/expenses', [PointOfSaleController::class, 'storeExpense'])->name('expenses.store');
        Route::get('/pos/transactions', [PointOfSaleController::class, 'transactions'])->name('pos.transactions');
        
        // Cashier Shift (halaman utama kasir)
        Route::get('/shift', [CashierShiftController::class, 'index'])->name('shift.index');
        Route::post('/shift/open', [CashierShiftController::class, 'store'])->name('shift.open');
        Route::post('/shift/close', [CashierShiftController::class, 'close'])->name('shift.close');
        
        // Transactions History
        Route::get('/transactions', [OrderController::class, 'kasirIndex'])->name('transactions');
        Route::get('/transactions/{order}', [OrderController::class, 'show'])->name('transactions.show');
        
        // Customer Debt Payment
        Route::get('/customers/debt', [CustomerController::class, 'kasirDebtList'])->name('customers.debt');
        Route::post('/customers/pay-debt', [PointOfSaleController::class, 'payDebt'])->name('customers.pay-debt');
        
        // Daily Report
    });

// =======================
// AUTHENTICATION ROUTES
// =======================
require __DIR__ . '/auth.php';

// =======================
// SERVE STORAGE FILES (Fallback for Windows PHP built-in server)
// =======================
Route::get('/pos-images/{path}', function ($path) {
    $fullPath = storage_path('app/public/products/' . $path);
    if (!file_exists($fullPath)) {
        abort(404);
    }
    return response()->file($fullPath);
})->where('path', '.*');

// =======================
// FALLBACK ROUTE (404)
// =======================
Route::fallback(function () {
    return view('errors.404');
});