<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\BranchStock;
use App\Models\Branch;
use App\Models\Ingredient;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        $branchId = $user->branch_id;
        
        // Jika ada filter branch_id dari request (untuk super admin)
        $filterBranch = $request->get('branch_id');
        if ($isSuperAdmin && $filterBranch && $filterBranch !== 'all') {
            $branchId = $filterBranch;
        }
        
        // =======================
        // BASIC KPI
        // =======================
        
        $totalProducts = Product::count();
        
        // Low stock & Out of stock dari branch_stock (untuk INGREDIENTS)
        $lowStockQuery = BranchStock::where('quantity', '>', 0)
            ->whereColumn('quantity', '<=', 'min_stock');
        
        $outOfStockQuery = BranchStock::where('quantity', 0);
        
        if (!$isSuperAdmin || ($filterBranch && $filterBranch !== 'all')) {
            $lowStockQuery->where('branch_id', $branchId);
            $outOfStockQuery->where('branch_id', $branchId);
        }
        
        $lowStock = $lowStockQuery->count();
        $outOfStock = $outOfStockQuery->count();
        
        // Total users
        if ($isSuperAdmin && (!$filterBranch || $filterBranch === 'all')) {
            $totalUsers = User::count();
        } else {
            $totalUsers = User::where('branch_id', $branchId)->count();
        }
        
        // =======================
        // PIUTANG & HUTANG
        // =======================
        
        $piutangQuery = Order::where('payment_status', '!=', 'paid');
        $hutangQuery = Purchase::where('payment_status', '!=', 'paid');
        
        if (!$isSuperAdmin || ($filterBranch && $filterBranch !== 'all')) {
            $piutangQuery->where('branch_id', $branchId);
            $hutangQuery->where('branch_id', $branchId);
        }
        
        $totalPiutang = $piutangQuery->sum('remaining_receivable') ?? 0;
        $totalHutang = $hutangQuery->sum('remaining_debt') ?? 0;
        
        // =======================
        // CHART PENDAPATAN
        // =======================
        
        $revenueQuery = Order::selectRaw('MONTH(date) as month, SUM(total_amount) as total')
            ->whereYear('date', now()->year);
        
        $expenseQuery = Expense::selectRaw('MONTH(date) as month, SUM(amount) as total')
            ->whereYear('date', now()->year);
        
        if (!$isSuperAdmin || ($filterBranch && $filterBranch !== 'all')) {
            $revenueQuery->where('branch_id', $branchId);
            $expenseQuery->where('branch_id', $branchId);
        }
        
        $revenueRaw = $revenueQuery->groupBy('month')->pluck('total', 'month');
        $expenseRaw = $expenseQuery->groupBy('month')->pluck('total', 'month');
        
        $months = [];
        $revenues = [];
        $expenses = [];
        
        for ($m = 1; $m <= 12; $m++) {
            $months[] = Carbon::create()->month($m)->format('M');
            $revenues[] = $revenueRaw[$m] ?? 0;
            $expenses[] = $expenseRaw[$m] ?? 0;
        }
        
        // =======================
        // DISTRIBUSI STOK (BERDASARKAN INGREDIENTS/Bahan Baku)
        // =======================
        
        // Perbaikan: Gunakan ingredients, bukan products + categories
        $stockQuery = BranchStock::join('ingredients', 'branch_stock.ingredient_id', '=', 'ingredients.id')
            ->selectRaw('ingredients.name as ingredient_name, SUM(branch_stock.quantity) as total');
        
        if (!$isSuperAdmin || ($filterBranch && $filterBranch !== 'all')) {
            $stockQuery->where('branch_stock.branch_id', $branchId);
        }
        
        $stockData = $stockQuery->groupBy('ingredients.id', 'ingredients.name')
            ->orderBy('total', 'desc')
            ->limit(10) // Batasi 10 bahan baku teratas
            ->get();
        
        $stockNames = $stockData->pluck('ingredient_name')->toArray();
        $stockSeries = $stockData->pluck('total')
            ->map(fn($v) => (int)$v)
            ->toArray();
        
        // =======================
        // DATA BRANCH UNTUK SUPER ADMIN
        // =======================
        
        $branches = [];
        $branchStats = [];
        
        if ($isSuperAdmin) {
            $branches = Branch::where('is_active', true)->get();
            
            foreach ($branches as $branch) {
                $branchStats[$branch->id] = [
                    'name' => $branch->name,
                    'total_orders' => Order::where('branch_id', $branch->id)->count(),
                    'total_revenue' => Order::where('branch_id', $branch->id)->sum('total_amount') ?? 0,
                    'total_ingredients' => BranchStock::where('branch_id', $branch->id)
                        ->distinct('ingredient_id')
                        ->count('ingredient_id'),
                    'low_stock_count' => BranchStock::where('branch_id', $branch->id)
                        ->whereColumn('quantity', '<=', 'min_stock')
                        ->where('quantity', '>', 0)
                        ->count(),
                ];
            }
        }
        
        return view('dashboard', compact(
            'totalProducts',
            'lowStock',
            'outOfStock',
            'totalUsers',
            'totalPiutang',
            'totalHutang',
            'months',
            'revenues',
            'expenses',
            'stockNames',
            'stockSeries',
            'isSuperAdmin',
            'branches',
            'branchStats',
            'branchId'
        ));
    }
    
    /**
     * Filter dashboard berdasarkan branch (AJAX)
     */
    public function filter(Request $request)
    {
        $branchId = $request->get('branch_id');
        
        // Ambil data yang sudah difilter
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        
        if (!$isSuperAdmin) {
            $branchId = $user->branch_id;
        }
        
        $queryParams = [];
        if ($branchId && $branchId !== 'all') {
            $queryParams['branch_id'] = $branchId;
        }
        
        return redirect()->route('dashboard', $queryParams);
    }
    
    /**
     * Get activities for AJAX
     */
    public function activities(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        $branchId = $request->get('branch_id');
        
        if (!$isSuperAdmin) {
            $branchId = $user->branch_id;
        }
        
        $activities = collect();
        
        // Ambil 5 order terakhir
        $orderQuery = Order::with('customer')
            ->orderBy('created_at', 'desc')
            ->limit(5);
        
        if ($branchId && $branchId !== 'all') {
            $orderQuery->where('branch_id', $branchId);
        }
        
        $recentOrders = $orderQuery->get();
        
        foreach ($recentOrders as $order) {
            $activities->push([
                'type' => 'order',
                'message' => "Transaksi #{$order->id} - " . ($order->customer->name ?? 'Umum') . " - Rp " . number_format($order->total_amount, 0, ',', '.'),
                'time' => $order->created_at->diffForHumans(),
            ]);
        }
        
        // Ambil 5 stok menipis terakhir
        $stockQuery = BranchStock::with('product')
            ->whereRaw('quantity <= min_stock')
            ->where('quantity', '>', 0)
            ->orderBy('last_updated', 'desc')
            ->limit(5);
        
        if ($branchId && $branchId !== 'all') {
            $stockQuery->where('branch_id', $branchId);
        }
        
        $lowStocks = $stockQuery->get();
        
        foreach ($lowStocks as $stock) {
            $activities->push([
                'type' => 'stock',
                'message' => "Stok menipis: {$stock->product->name} (sisa {$stock->quantity})",
                'time' => $stock->last_updated ? $stock->last_updated->diffForHumans() : 'Baru saja',
            ]);
        }
        
        return response()->json([
            'activities' => $activities->take(10)
        ]);
    }
    
    /**
     * Export report to PDF
     */
    public function export(Request $request)
    {
        // Implementasi export sesuai kebutuhan
        // Bisa menggunakan Barryvdh\DomPDF atau Maatwebsite\Excel
        
        return back()->with('info', 'Fitur export sedang dalam pengembangan');
    }
}