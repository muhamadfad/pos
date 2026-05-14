<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request; // <-- TAMBAHKAN INI

class FinanceController extends Controller
{
    public function index(Request $request) // <-- Sekarang Request dikenali
    {
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        $userBranchId = $user->branch_id;
        
        // Filter branch
        $branchId = $request->get('branch_id');
        if (!$isSuperAdmin) {
            $branchId = $userBranchId;
        }
        
        $total   = $this->calculateProfit(null, null, $branchId);
        $daily   = $this->calculateProfit(Carbon::today(), null, $branchId);
        $weekly  = $this->calculateProfit(Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek(), $branchId);
        $monthly = $this->calculateProfit(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth(), $branchId);
        $yearly  = $this->calculateProfit(Carbon::now()->startOfYear(), Carbon::now()->endOfYear(), $branchId);

        $totalIncome       = $total['income'];
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
            'total',
            'daily',
            'weekly',
            'monthly',
            'yearly',
            'totalIncome',
            'totalExpense',
            'totalSupplierPaid',
            'netCashflow',
            'isSuperAdmin',
            'branches',
            'branchId',
            'currentBranchName'
        ));
    }

    public function cashflow(Request $request) // <-- Tambahkan Request
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
        $topSupplierPurchases = $purchaseQuery->orderByDesc('total_paid')->limit(10)->get();
        
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
            'months',
            'totalIncome',
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
    
    $todayObj = Carbon::today();
    $todayStr = $todayObj->toDateString();
    $finance = $this->calculateProfit($todayObj, null, $branchId);

    $topProductsQuery = DB::table('order_items')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->whereDate('orders.date', $todayStr);
    
    if ($branchId) {
        $topProductsQuery->where('orders.branch_id', $branchId);
    }
    
    $topProducts = $topProductsQuery->select('products.name', DB::raw('SUM(order_items.quantity) as total_qty'))
        ->groupBy('products.id', 'products.name')
        ->orderByDesc('total_qty')
        ->limit(10)
        ->get()
        ->map(function ($item) {
            $item->total_qty = (float) $item->total_qty;
            return $item;
        });

    $expenseQuery = Expense::whereDate('date', $todayStr);
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

    return view('admin.finance.daily', compact(
        'finance', 
        'todayObj',    // <-- PASTIKAN VARIABLE INI ADA
        'topProducts', 
        'expenses',
        'isSuperAdmin',
        'branches',
        'branchId',
        'currentBranchName'
    ));
}

    public function weekly(Request $request) // <-- Tambahkan Request
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
        
        $topProducts = $topProductsQuery->select('products.name', DB::raw('SUM(order_items.quantity) as total_qty'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get()
            ->map(function ($item) {
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

    public function monthly(Request $request) // <-- Tambahkan Request
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
        
        $topProducts = $topProductsQuery->select('products.name', DB::raw('SUM(order_items.quantity) as total_qty'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get()
            ->map(function ($item) {
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

    // ================= HELPER =================
    private function calculateProfit($start = null, $end = null, $branchId = null)
    {
        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select(
                DB::raw('SUM(order_items.subtotal) as income'),
                DB::raw('SUM(order_items.cost_price * order_items.quantity) as cost'),
                DB::raw('SUM((order_items.selling_price - order_items.cost_price) * order_items.quantity) as profit')
            );

        if ($start && $end) {
            $query->whereBetween('orders.date', [
                Carbon::parse($start)->startOfDay(),
                Carbon::parse($end)->endOfDay()
            ]);
        } elseif ($start) {
            $query->whereDate('orders.date', Carbon::parse($start));
        }
        
        // Filter by branch
        if ($branchId) {
            $query->where('orders.branch_id', $branchId);
        }

        $result = $query->first();

        $income = $result->income ?? 0;
        $cost   = $result->cost ?? 0;
        $profit = $result->profit ?? 0;

        $expenseQuery  = Expense::query();
        $purchaseQuery = Purchase::query();

        if ($start && $end) {
            $expenseQuery->whereBetween('date', [
                Carbon::parse($start)->startOfDay(),
                Carbon::parse($end)->endOfDay()
            ]);
            $purchaseQuery->whereBetween('date', [
                Carbon::parse($start)->startOfDay(),
                Carbon::parse($end)->endOfDay()
            ]);
        } elseif ($start) {
            $expenseQuery->whereDate('date', Carbon::parse($start));
            $purchaseQuery->whereDate('date', Carbon::parse($start));
        }
        
        // Filter by branch
        if ($branchId) {
            $expenseQuery->where('branch_id', $branchId);
            $purchaseQuery->where('branch_id', $branchId);
        }

        $expenses     = $expenseQuery->sum('amount') ?? 0;
        $supplierPaid = $purchaseQuery->sum('total_paid') ?? 0;

        $netCashflow = $income - $supplierPaid - $expenses;

        return compact(
            'income',
            'cost',
            'profit',
            'expenses',
            'supplierPaid',
            'netCashflow'
        );
    }
}