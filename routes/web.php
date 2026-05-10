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
use App\Http\Controllers\StockController;
use App\Http\Controllers\ReportController;
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
    
    // Dashboard (berdasarkan role akan diarahkan ke view yang sesuai)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.home');
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

    // Cashier Shift Management
    Route::prefix('pos/shift')->name('pos.shift.')->group(function () {
        Route::get('/', [CashierShiftController::class, 'index'])->name('index');
        Route::post('/open', [CashierShiftController::class, 'store'])->name('open');
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

        // Dashboard Admin
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/activities', [DashboardController::class, 'activities'])->name('dashboard.activities');
        Route::get('/dashboard/export', [DashboardController::class, 'export'])->name('dashboard.export');

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
        // STOCK MANAGEMENT
        // =======================
        Route::prefix('stock')->name('stock.')->group(function () {
            Route::get('/', [StockController::class, 'index'])->name('index');
            Route::get('/product/{product}', [StockController::class, 'productStock'])->name('product');
            Route::post('/adjust', [StockController::class, 'adjust'])->name('adjust');
            Route::post('/transfer', [StockController::class, 'transfer'])->name('transfer');
            Route::get('/movement', [StockController::class, 'movement'])->name('movement');
            Route::get('/export', [StockController::class, 'export'])->name('export');
            Route::get('/low-stock', [StockController::class, 'lowStock'])->name('low-stock');
        });

        // =======================
        // PURCHASES (Barang Masuk dari Supplier)
        // =======================
        Route::prefix('purchases')->name('purchases.')->group(function () {
            Route::get('/', [PurchaseController::class, 'index'])->name('index');
            Route::get('/select-supplier', [PurchaseController::class, 'selectSupplier'])->name('select-supplier');
            Route::get('/supplier/{supplier}', [PurchaseController::class, 'perSupplier'])->name('per-supplier');
            Route::get('/supplier/{supplier}/download', [PurchaseController::class, 'downloadPdf'])->name('download');
            Route::get('/supplier/{supplier}/create', [PurchaseController::class, 'create'])->name('create');
            Route::post('/supplier/{supplier}', [PurchaseController::class, 'store'])->name('store');
            Route::get('/{purchase}', [PurchaseController::class, 'show'])->name('show');
            Route::delete('/{purchase}', [PurchaseController::class, 'destroy'])->name('destroy');
            Route::post('/{purchase}/pay', [PurchasePaymentController::class, 'pay'])->name('pay');
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
        // EXPENSES (Pengeluaran)
        // =======================
        Route::resource('expenses', ExpenseController::class);
        Route::get('/expenses/category/{category}', [ExpenseController::class, 'byCategory'])->name('expenses.by-category');

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
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
            Route::get('/purchases', [ReportController::class, 'purchases'])->name('purchases');
            Route::get('/stock', [ReportController::class, 'stock'])->name('stock');
            Route::get('/profit', [ReportController::class, 'profit'])->name('profit');
            Route::get('/tax', [ReportController::class, 'tax'])->name('tax');
            Route::post('/generate', [ReportController::class, 'generate'])->name('generate');
            Route::get('/export/excel', [ReportController::class, 'exportExcel'])->name('export.excel');
            Route::get('/export/pdf', [ReportController::class, 'exportPdf'])->name('export.pdf');
        });

        // =======================
        // BRANCH STOCK MANAGEMENT (Stok Bahan Baku per Cabang) - TAMBAHKAN DI SINI
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
    });

// =======================
// ROUTES KHUSUS SUPER ADMIN
// =======================
Route::middleware(['auth', 'super_admin'])
    ->prefix('super')
    ->name('super.')
    ->group(function () {

        // Super Admin Dashboard
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
        Route::prefix('monitoring')->name('monitoring.')->group(function () {
            Route::get('/', [SuperDashboardController::class, 'monitoring'])->name('index');
            Route::get('/performance', [SuperDashboardController::class, 'performance'])->name('performance');
            Route::get('/comparison', [SuperDashboardController::class, 'branchComparison'])->name('comparison');
            Route::get('/activities', [SuperDashboardController::class, 'activities'])->name('activities');
            Route::get('/logs', [SuperDashboardController::class, 'systemLogs'])->name('logs');
        });
        
        // =======================
        // SYSTEM SETTINGS
        // =======================
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SystemSettingController::class, 'index'])->name('index');
            Route::post('/', [SystemSettingController::class, 'update'])->name('update');
            Route::get('/backup', [SystemSettingController::class, 'backup'])->name('backup');
            Route::post('/restore', [SystemSettingController::class, 'restore'])->name('restore');
            Route::get('/cache/clear', [SystemSettingController::class, 'clearCache'])->name('clear-cache');
        });
        
        // =======================
        // GLOBAL REPORTS (ALL BRANCHES)
        // =======================
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/global', [ReportController::class, 'global'])->name('global');
            Route::get('/branch-comparison', [ReportController::class, 'branchComparison'])->name('branch-comparison');
            Route::get('/consolidated', [ReportController::class, 'consolidated'])->name('consolidated');
            Route::get('/export/excel', [ReportController::class, 'exportGlobalExcel'])->name('export.excel');
            Route::get('/export/pdf', [ReportController::class, 'exportGlobalPdf'])->name('export.pdf');
        });
        
        // =======================
        // DATABASE MANAGEMENT
        // =======================
        Route::prefix('database')->name('database.')->group(function () {
            Route::get('/backup', [DatabaseController::class, 'backup'])->name('backup');
            Route::post('/restore', [DatabaseController::class, 'restore'])->name('restore');
            Route::get('/optimize', [DatabaseController::class, 'optimize'])->name('optimize');
        });


    });

// =======================
// ROUTES KHUSUS KASIR (Sederhana)
// =======================
Route::middleware(['auth', 'kasir'])
    ->prefix('kasir')
    ->name('kasir.')
    ->group(function () {
        
        // Kasir Dashboard
        Route::get('/dashboard', [PointOfSaleController::class, 'index'])->name('dashboard');
        
        // POS
        Route::get('/pos', [PointOfSaleController::class, 'index'])->name('pos');
        Route::post('/pos/checkout', [PointOfSaleController::class, 'checkout'])->name('checkout');
        Route::get('/pos/print/{order}', [PointOfSaleController::class, 'printReceipt'])->name('print');
        
        // Cashier Shift
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
        Route::get('/report/daily', [ReportController::class, 'kasirDaily'])->name('report.daily');
    });

        Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
            Route::get('/hpp-report', [App\Http\Controllers\RecipeController::class, 'hppReport']);
        });
// =======================
// AUTHENTICATION ROUTES
// =======================
require __DIR__ . '/auth.php';

// =======================
// FALLBACK ROUTE (404)
// =======================
Route::fallback(function () {
    return view('errors.404');
});