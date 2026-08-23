<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\Branch;
use App\Models\Product;
use App\Models\RecipeItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        $userBranchId = $user->branch_id;
        
        // Filter branch
        $branchId = $request->get('branch_id');
        if (!$isSuperAdmin) {
            $branchId = $userBranchId;
        }
        
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : null;
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : null;

        $total   = $this->calculateProfit($start, $end, $branchId);
        $daily   = $this->calculateProfit(Carbon::today(), null, $branchId);
        $weekly  = $this->calculateProfit(Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek(), $branchId);
        $monthly = $this->calculateProfit(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth(), $branchId);
        $yearly  = $this->calculateProfit(Carbon::now()->startOfYear(), Carbon::now()->endOfYear(), $branchId);

        $totalIncome       = $total['income'];
        $totalIncomeCash   = $total['income_cash'];
        $totalIncomeTransfer = $total['income_transfer'];
        $totalExpense      = $total['expenses'];
        $totalSupplierPaid = $total['supplierPaid'];
        $netCashflow       = $total['netCashflow'];
        
        // Get branches for filter (only for super admin)
        $branches = [];
        if ($isSuperAdmin) {
            $branches = Branch::where('is_active', true)->get();
        }
        
        $currentBranchName = 'Semua Cabang';
        if ($branchId) {
            $branch = Branch::find($branchId);
            $currentBranchName = $branch ? $branch->name : 'Cabang';
        }

        return view('admin.finance.index', compact(
            'startDate',
            'endDate',
            'total',
            'daily',
            'weekly',
            'monthly',
            'yearly',
                        'totalIncome',
            'totalIncomeCash',
            'totalIncomeTransfer',
            'totalExpense',
            'totalSupplierPaid',
            'netCashflow',
            'isSuperAdmin',
            'branches',
            'branchId',
            'currentBranchName'
        ));
    }

    public function cashflow(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        $userBranchId = $user->branch_id;
        
        // Filter branch
        $branchId = $request->get('branch_id');
        if (!$isSuperAdmin) {
            $branchId = $userBranchId;
        }
        
        // Breakdown per bulan (12 bulan terakhir)
        $months = collect(range(11, 0))->map(function ($i) use ($branchId) {
            $start = Carbon::now()->subMonths($i)->startOfMonth();
            $end   = Carbon::now()->subMonths($i)->endOfMonth();

            $summary = $this->calculateProfit($start, $end, $branchId);

            return [
                'label'        => $start->translatedFormat('M Y'),
                'income'       => $summary['income'],
                'supplier'     => $summary['supplierPaid'],
                'operasional'  => $summary['expenses'],
                'profit'       => $summary['profit'],
                'net'          => $summary['netCashflow'],
            ];
        });

        $total = $this->calculateProfit(null, null, $branchId);
        $totalIncome       = $total['income'];
        $totalIncomeCash   = $total['income_cash'];
        $totalIncomeTransfer = $total['income_transfer'];
        $totalSupplierPaid = $total['supplierPaid'];
        $totalOperasional  = $total['expenses'];
        $netCashflow       = $total['netCashflow'];
        $totalProfit       = $total['profit'];

        // Rincian expenses per kategori
        $expenseQuery = Expense::select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category');
        if ($branchId) {
            $expenseQuery->where('branch_id', $branchId);
        }
        $expenseByCategory = $expenseQuery->orderByDesc('total')->get();

        // Top 10 pembelian supplier terbesar
        $purchaseQuery = Purchase::with('supplier');
        if ($branchId) {
            $purchaseQuery->where('branch_id', $branchId);
        }
        $topSupplierPurchases = $purchaseQuery->orderByDesc('total_paid')->limit(100)->get();
        
        // Get branches for filter
        $branches = [];
        if ($isSuperAdmin) {
            $branches = Branch::where('is_active', true)->get();
        }
        
        $currentBranchName = 'Semua Cabang';
        if ($branchId) {
            $branch = Branch::find($branchId);
            $currentBranchName = $branch ? $branch->name : 'Cabang';
        }

        return view('admin.finance.cashflow', compact(
            'isSuperAdmin',
            'months',
                        'totalIncome',
            'totalIncomeCash',
            'totalIncomeTransfer',
            'totalSupplierPaid',
            'totalOperasional',
            'netCashflow',
            'totalProfit',
            'expenseByCategory',
            'topSupplierPurchases',
            'isSuperAdmin',
            'branches',
            'branchId',
            'currentBranchName'
        ));
    }

    public function daily(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        $userBranchId = $user->branch_id;
        
        // Filter branch
        $branchId = $request->get('branch_id');
        if (!$isSuperAdmin) {
            $branchId = $userBranchId;
        }
        
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
        } else {
            $start = Carbon::today();
            $end = null;
        }

        $finance = $this->calculateProfit($start, $end, $branchId);

        $topProductsQuery = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id');

        if ($startDate && $endDate) {
            $topProductsQuery->whereBetween('orders.date', [$start, $end]);
        } else {
            $topProductsQuery->whereDate('orders.date', Carbon::today()->toDateString());
        }
        
        $paymentMethodFilter = $request->get('payment_method');
        if (in_array($paymentMethodFilter, ['cash', 'transfer'])) {
            $topProductsQuery->where('orders.payment_method', $paymentMethodFilter);
        }
        
        if ($branchId) {
            $topProductsQuery->where('orders.branch_id', $branchId);
        }
        
        $topProducts = $topProductsQuery->select(DB::raw('COALESCE(order_items.custom_name, products.name) as display_name'), DB::raw('SUM(order_items.quantity) as total_qty'))
            ->groupBy('products.id', 'products.name', 'order_items.custom_name')
            ->orderByDesc('total_qty')
            ->limit(100)
            ->get()
            ->map(function ($item) {
                $item->name = $item->display_name;
                $item->total_qty = (float) $item->total_qty;
                return $item;
            });

                $expenseQuery = Expense::query();
        if ($startDate && $endDate) {
            $expenseQuery->whereBetween('date', [$start, $end]);
        } else {
            $expenseQuery->whereDate('date', Carbon::today()->toDateString());
        }
        if ($branchId) {
            $expenseQuery->where('branch_id', $branchId);
        }
        $expenses = $expenseQuery->get();
        
        // Get branches for filter
        $branches = [];
        if ($isSuperAdmin) {
            $branches = Branch::where('is_active', true)->get();
        }
        
        $currentBranchName = 'Semua Cabang';
        if ($branchId) {
            $branch = Branch::find($branchId);
            $currentBranchName = $branch ? $branch->name : 'Cabang';
        }

        return view('admin.finance.daily', compact('startDate', 'endDate', 'paymentMethodFilter', 
            'finance', 'topProducts', 
            'expenses',
            'isSuperAdmin',
            'branches',
            'branchId',
            'currentBranchName'
        ));
    }

    public function weekly(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        $userBranchId = $user->branch_id;
        
        // Filter branch
        $branchId = $request->get('branch_id');
        if (!$isSuperAdmin) {
            $branchId = $userBranchId;
        }
        
        $start = Carbon::now()->startOfWeek();
        $end   = Carbon::now()->endOfWeek();
        $finance = $this->calculateProfit($start, $end, $branchId);

        $topProductsQuery = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.date', [$start->toDateString(), $end->toDateString()]);
        
        if ($branchId) {
            $topProductsQuery->where('orders.branch_id', $branchId);
        }
        
        $topProducts = $topProductsQuery->select(DB::raw('COALESCE(order_items.custom_name, products.name) as display_name'), DB::raw('SUM(order_items.quantity) as total_qty'))
            ->groupBy('products.id', 'products.name', 'order_items.custom_name')
            ->orderByDesc('total_qty')
            ->limit(100)
            ->get()
            ->map(function ($item) {
                $item->name = $item->display_name;
                $item->total_qty = (float) $item->total_qty;
                return $item;
            });
        
        // Get branches for filter
        $branches = [];
        if ($isSuperAdmin) {
            $branches = Branch::where('is_active', true)->get();
        }
        
        $currentBranchName = 'Semua Cabang';
        if ($branchId) {
            $branch = Branch::find($branchId);
            $currentBranchName = $branch ? $branch->name : 'Cabang';
        }

        return view('admin.finance.weekly', compact('finance', 'start', 'end', 'topProducts', 'isSuperAdmin', 'branches', 'branchId', 'currentBranchName'));
    }

    public function monthly(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        $userBranchId = $user->branch_id;
        
        // Filter branch
        $branchId = $request->get('branch_id');
        if (!$isSuperAdmin) {
            $branchId = $userBranchId;
        }
        
        $start = Carbon::now()->startOfMonth();
        $end   = Carbon::now()->endOfMonth();
        $finance = $this->calculateProfit($start, $end, $branchId);

        $topProductsQuery = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.date', [$start->toDateString(), $end->toDateString()]);
        
        if ($branchId) {
            $topProductsQuery->where('orders.branch_id', $branchId);
        }
        
        $topProducts = $topProductsQuery->select(DB::raw('COALESCE(order_items.custom_name, products.name) as display_name'), DB::raw('SUM(order_items.quantity) as total_qty'))
            ->groupBy('products.id', 'products.name', 'order_items.custom_name')
            ->orderByDesc('total_qty')
            ->limit(100)
            ->get()
            ->map(function ($item) {
                $item->name = $item->display_name;
                $item->total_qty = (float) $item->total_qty;
                return $item;
            });
        
        // Get branches for filter
        $branches = [];
        if ($isSuperAdmin) {
            $branches = Branch::where('is_active', true)->get();
        }
        
        $currentBranchName = 'Semua Cabang';
        if ($branchId) {
            $branch = Branch::find($branchId);
            $currentBranchName = $branch ? $branch->name : 'Cabang';
        }

        return view('admin.finance.monthly', compact('finance', 'start', 'end', 'topProducts', 'isSuperAdmin', 'branches', 'branchId', 'currentBranchName'));
    }

    /**
     * Hitung HPP untuk satu produk berdasarkan resep (ingredients)
     */
    private function calculateProductHPP($productId)
    {
        $recipeItems = RecipeItem::where('product_id', $productId)
            ->with('ingredient')
            ->get();
        
        if ($recipeItems->isEmpty()) {
            // Jika tidak ada resep, gunakan purchase_price dari product
            $product = Product::find($productId);
            return $product->purchase_price ?? 0;
        }
        
        $totalCost = 0;
        
        foreach ($recipeItems as $item) {
            if ($item->ingredient && $item->ingredient->unit_cost > 0) {
                $totalCost += $item->ingredient->unit_cost * $item->quantity;
            }
        }
        
        return $totalCost;
    }

    /**
     * Hitung total HPP untuk semua item dalam order
     */
    private function calculateOrderHPP($orderId)
    {
        $orderItems = DB::table('order_items')
            ->where('order_id', $orderId)
            ->get();
        
        $totalHPP = 0;
        
        foreach ($orderItems as $item) {
            $hpp = $this->calculateProductHPP($item->product_id);
            $totalHPP += $hpp * $item->quantity;
        }
        
        return $totalHPP;
    }

    // ================= HELPER =================
    private function calculateProfit($start = null, $end = null, $branchId = null)
    {
        // Query untuk mendapatkan semua order dalam periode
        $orderQuery = Order::query();
        
        if ($start && $end) {
            $orderQuery->whereBetween('date', [
                Carbon::parse($start)->startOfDay(),
                Carbon::parse($end)->endOfDay()
            ]);
        } elseif ($start) {
            $orderQuery->whereDate('date', Carbon::parse($start));
        }
        
        // Filter by branch
        if ($branchId) {
            $orderQuery->where('branch_id', $branchId);
        }
        
        // Hanya order yang sudah lunas
        $orderQuery->where('payment_status', 'paid');
        
        $orders = $orderQuery->get();
        
        $totalIncome = 0;
        $totalIncomeCash = 0;
        $totalIncomeTransfer = 0;
        $totalHPP = 0;
        
        foreach ($orders as $order) {
            $totalIncome += $order->total_amount;
            
            if ($order->payment_method === 'transfer') {
                $totalIncomeTransfer += $order->total_amount;
            } else {
                $totalIncomeCash += $order->total_amount;
            }
            
            // Hitung HPP berdasarkan resep ingredients
            $orderItems = DB::table('order_items')
                ->where('order_id', $order->id)
                ->get();
            
            foreach ($orderItems as $item) {
                $hpp = $this->calculateProductHPP($item->product_id);
                $totalHPP += $hpp * $item->quantity;
            }
        }
        
        $profit = $totalIncome - $totalHPP;
        
        // Hitung expenses (biaya operasional)
        $expenseQuery = Expense::query();
        if ($start && $end) {
            $expenseQuery->whereBetween('date', [
                Carbon::parse($start)->startOfDay(),
                Carbon::parse($end)->endOfDay()
            ]);
        } elseif ($start) {
            $expenseQuery->whereDate('date', Carbon::parse($start));
        }
        if ($branchId) {
            $expenseQuery->where('branch_id', $branchId);
        }
        $expenses = $expenseQuery->sum('amount') ?? 0;
        
        // Hitung pembayaran ke supplier
        $purchaseQuery = Purchase::query();
        if ($start && $end) {
            $purchaseQuery->whereBetween('date', [
                Carbon::parse($start)->startOfDay(),
                Carbon::parse($end)->endOfDay()
            ]);
        } elseif ($start) {
            $purchaseQuery->whereDate('date', Carbon::parse($start));
        }
        if ($branchId) {
            $purchaseQuery->where('branch_id', $branchId);
        }
        $supplierPaid = $purchaseQuery->sum('total_paid') ?? 0;
        
        $netCashflow = $totalIncome - $supplierPaid - $expenses;
        
        return [
            'income' => $totalIncome,
            'income_cash' => $totalIncomeCash,
            'income_transfer' => $totalIncomeTransfer,
            'cost' => $totalHPP,
            'profit' => $profit,
            'expenses' => $expenses,
            'supplierPaid' => $supplierPaid,
            'netCashflow' => $netCashflow,
        ];
    }

    public function export(Request $request)
    {
        $period = $request->get('period', 'index');
        $branchId = $request->get('branch_id');
        
        $user = auth()->user();
        if ($user->role !== 'super_admin') {
            $branchId = $user->branch_id;
        }

        $startDate = null;
        $endDate = null;
        $title = "Laporan Keuangan";

        if ($period == 'daily') {
            $startDate = \Carbon\Carbon::today()->format('Y-m-d');
            $title = "Laporan Keuangan Harian";
        } elseif ($period == 'weekly') {
            $startDate = \Carbon\Carbon::now()->startOfWeek()->format('Y-m-d');
            $endDate = \Carbon\Carbon::now()->endOfWeek()->format('Y-m-d');
            $title = "Laporan Keuangan Mingguan";
        } elseif ($period == 'monthly') {
            $startDate = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
            $endDate = \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');
            $title = "Laporan Keuangan Bulanan";
        } else {
            $title = "Laporan Keuangan Keseluruhan";
        }

        $finance = $this->calculateProfit($startDate, $endDate, $branchId);
        
        $currentBranchName = 'Semua Cabang';
        if ($branchId) {
            $currentBranch = \App\Models\Branch::find($branchId);
            $currentBranchName = $currentBranch ? $currentBranch->name : 'Cabang ' . $branchId;
        }

        
        $ordersQuery = \App\Models\Order::with(['customer', 'items.product'])->where('payment_status', 'paid');
        $expensesQuery = \App\Models\Expense::query();
        $purchasesQuery = \App\Models\Purchase::with('supplier');

        if ($startDate && $endDate) {
            $ordersQuery->whereBetween('date', [\Carbon\Carbon::parse($startDate)->startOfDay(), \Carbon\Carbon::parse($endDate)->endOfDay()]);
            $expensesQuery->whereBetween('date', [\Carbon\Carbon::parse($startDate)->startOfDay(), \Carbon\Carbon::parse($endDate)->endOfDay()]);
            $purchasesQuery->whereBetween('date', [\Carbon\Carbon::parse($startDate)->startOfDay(), \Carbon\Carbon::parse($endDate)->endOfDay()]);
        } elseif ($startDate) {
            $ordersQuery->whereDate('date', \Carbon\Carbon::parse($startDate));
            $expensesQuery->whereDate('date', \Carbon\Carbon::parse($startDate));
            $purchasesQuery->whereDate('date', \Carbon\Carbon::parse($startDate));
        }

        if ($branchId) {
            $ordersQuery->where('branch_id', $branchId);
            $expensesQuery->where('branch_id', $branchId);
            $purchasesQuery->where('branch_id', $branchId);
        }

        $ordersList = $ordersQuery->orderBy('date', 'asc')->get();
        $expensesList = $expensesQuery->orderBy('date', 'asc')->get();
        $purchasesList = $purchasesQuery->orderBy('date', 'asc')->get();

        
        $productSummary = [];
        foreach ($ordersList as $order) {
            foreach ($order->items as $item) {
                $productName = $item->product ? $item->product->name : 'Produk Dihapus';
                if (!isset($productSummary[$productName])) {
                    $productSummary[$productName] = [
                        'total' => 0,
                        'cash' => 0,
                        'transfer' => 0
                    ];
                }
                $productSummary[$productName]['total'] += $item->quantity;
                if ($order->payment_method === 'cash') {
                    $productSummary[$productName]['cash'] += $item->quantity;
                } elseif ($order->payment_method === 'transfer') {
                    $productSummary[$productName]['transfer'] += $item->quantity;
                }
            }
        }
        uasort($productSummary, function($a, $b) {
            return $b['total'] <=> $a['total'];
        });

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.pdf.finance', compact(
            'productSummary', 
            'ordersList', 'expensesList', 'purchasesList', 
            'finance', 'title', 'startDate', 'endDate', 'currentBranchName', 'period'
        ));
        
        return $pdf->download('Laporan_Finance_'.date('Y-m-d').'.pdf');
    }
}
