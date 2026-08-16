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
        $userBranchId = $user->branch_id;
        
        // Untuk Super Admin: bisa pilih branch atau 'all'
        // Untuk user biasa: hanya branch sendiri
        $selectedBranchId = $request->get('branch_id');
        
        if ($isSuperAdmin) {
            // Super admin bisa memilih branch
            $branchId = ($selectedBranchId && $selectedBranchId !== 'all') ? $selectedBranchId : null;
            $showAllBranches = ($selectedBranchId === 'all' || !$selectedBranchId);
        } else {
            // User biasa hanya branch sendiri
            $branchId = $userBranchId;
            $showAllBranches = false;
        }
        
        // =======================
        // BASIC KPI
        // =======================
        
        $totalProducts = Product::count();
        
        // Low stock & Out of stock dari branch_stock (untuk INGREDIENTS)
        $lowStockQuery = BranchStock::where('quantity', '>', 0)
            ->whereColumn('quantity', '<=', 'min_stock');
        
        $outOfStockQuery = BranchStock::where('quantity', 0);
        
        if (!$showAllBranches && $branchId) {
            $lowStockQuery->where('branch_id', $branchId);
            $outOfStockQuery->where('branch_id', $branchId);
        }
        
        $lowStock = $lowStockQuery->count();
        $outOfStock = $outOfStockQuery->count();
        
        // Total users
        if ($showAllBranches) {
            $totalUsers = User::count();
        } else {
            $totalUsers = User::where('branch_id', $branchId)->count();
        }
        
        // =======================
        // PENDAPATAN HARI INI
        // =======================
        $todayRevenueQuery = Order::whereDate('date', Carbon::today())
            ->where('payment_status', 'paid');
        
        if (!$showAllBranches && $branchId) {
            $todayRevenueQuery->where('branch_id', $branchId);
        }
        
        $todayRevenue = $todayRevenueQuery->sum('total_amount') ?? 0;
        
        // Jumlah transaksi hari ini
        $todayTransactionsQuery = Order::whereDate('date', Carbon::today());
        
        if (!$showAllBranches && $branchId) {
            $todayTransactionsQuery->where('branch_id', $branchId);
        }
        
        $todayTransactions = $todayTransactionsQuery->count();
        
        // Pendapatan kemarin untuk perbandingan
        $yesterdayRevenueQuery = Order::whereDate('date', Carbon::yesterday())
            ->where('payment_status', 'paid');
        
        if (!$showAllBranches && $branchId) {
            $yesterdayRevenueQuery->where('branch_id', $branchId);
        }
        
        $yesterdayRevenue = $yesterdayRevenueQuery->sum('total_amount') ?? 0;
        
        // Hitung persentase perubahan
        $revenueChange = 0;
        if ($yesterdayRevenue > 0) {
            $revenueChange = (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100;
        } elseif ($todayRevenue > 0) {
            $revenueChange = 100;
        }
        
        // =======================
        // PIUTANG & HUTANG
        // =======================
        
        $piutangQuery = Order::where('payment_status', '!=', 'paid');
        $hutangQuery = Purchase::where('payment_status', '!=', 'paid');
        
        if (!$showAllBranches && $branchId) {
            $piutangQuery->where('branch_id', $branchId);
            $hutangQuery->where('branch_id', $branchId);
        }
        
        $totalPiutang = $piutangQuery->sum('remaining_receivable') ?? 0;
        $totalHutang = $hutangQuery->sum('remaining_debt') ?? 0;
        
        // =======================
        // CHART PENDAPATAN
        // =======================
        
        $revenueQuery = Order::selectRaw('MONTH(date) as month, SUM(total_amount) as total')
            ->whereYear('date', now()->year)
            ->where('payment_status', 'paid');
        
        $expenseQuery = Expense::selectRaw('MONTH(date) as month, SUM(amount) as total')
            ->whereYear('date', now()->year);
        
        if (!$showAllBranches && $branchId) {
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
            $revenues[] = (float)($revenueRaw[$m] ?? 0);
            $expenses[] = (float)($expenseRaw[$m] ?? 0);
        }
        
        // =======================
        // DISTRIBUSI STOK (BERDASARKAN INGREDIENTS/Bahan Baku)
        // =======================
        
        $stockQuery = BranchStock::join('ingredients', 'branch_stock.ingredient_id', '=', 'ingredients.id')
            ->selectRaw('ingredients.name as ingredient_name, SUM(branch_stock.quantity) as total');
        
        if (!$showAllBranches && $branchId) {
            $stockQuery->where('branch_stock.branch_id', $branchId);
        }
        
        $stockData = $stockQuery->groupBy('ingredients.id', 'ingredients.name')
            ->orderBy('total', 'desc')
            ->limit(10)
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
                    'id' => $branch->id,
                    'name' => $branch->name,
                    'total_orders' => Order::where('branch_id', $branch->id)->count(),
                    'total_revenue' => (float)(Order::where('branch_id', $branch->id)->sum('total_amount') ?? 0),
                    'today_revenue' => (float)(Order::where('branch_id', $branch->id)
                        ->whereDate('date', Carbon::today())
                        ->where('payment_status', 'paid')
                        ->sum('total_amount') ?? 0),
                    'total_ingredients' => BranchStock::where('branch_id', $branch->id)
                        ->distinct('ingredient_id')
                        ->count('ingredient_id'),
                    'low_stock_count' => BranchStock::where('branch_id', $branch->id)
                        ->whereColumn('quantity', '<=', 'min_stock')
                        ->where('quantity', '>', 0)
                        ->count(),
                    'out_of_stock_count' => BranchStock::where('branch_id', $branch->id)
                        ->where('quantity', 0)
                        ->count(),
                ];
            }
        }
        
        // Branch name untuk display
        $currentBranchName = 'Semua Cabang';
        if (!$showAllBranches && $branchId) {
            $currentBranch = Branch::find($branchId);
            $currentBranchName = $currentBranch ? $currentBranch->name : 'Cabang ' . $branchId;
        } elseif (!$showAllBranches && !$branchId && $userBranchId) {
            $currentBranch = Branch::find($userBranchId);
            $currentBranchName = $currentBranch ? $currentBranch->name : 'Cabang ' . $userBranchId;
        }
        
        return view('dashboard', compact(
            'totalProducts',
            'lowStock',
            'outOfStock',
            'totalUsers',
            'todayRevenue',
            'todayTransactions',
            'revenueChange',
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
            'branchId',
            'showAllBranches',
            'currentBranchName',
            'userBranchId'
        ));
    }
    
    /**
     * Filter dashboard berdasarkan branch (AJAX)
     */
    public function filter(Request $request)
    {
        $branchId = $request->get('branch_id');
        $user = auth()->user();
        
        if ($user->role !== 'super_admin') {
            return redirect()->route('dashboard');
        }
        
        if ($branchId && $branchId !== 'all') {
            return redirect()->route('dashboard', ['branch_id' => $branchId]);
        }
        
        return redirect()->route('dashboard');
    }
    
    /**
     * Get activities for AJAX
     */
/**
 * Get activities for AJAX
 */
/**
 * Get activities for AJAX
 */
    public function activities(Request $request)
    {
        try {
            $user = auth()->user();
            $isSuperAdmin = $user->role === 'super_admin';
            $branchId = $request->get('branch_id');
            
            // Validasi: user biasa hanya bisa lihat branch sendiri
            if (!$isSuperAdmin) {
                $branchId = $user->branch_id;
            }
            
            $showAllBranches = ($branchId === 'all' || !$branchId) && $isSuperAdmin;
            
            $activities = collect();
            
            // Ambil 5 order terakhir
            $orderQuery = Order::with('customer', 'branch')
                ->orderBy('created_at', 'desc')
                ->limit(5);
            
            if (!$showAllBranches && $branchId && $branchId !== 'all') {
                $orderQuery->where('branch_id', $branchId);
            }
            
            $recentOrders = $orderQuery->get();
            
            foreach ($recentOrders as $order) {
                $branchInfo = ($isSuperAdmin && $showAllBranches && $order->branch) 
                    ? " [{$order->branch->name}]" 
                    : '';
                
                $activities->push([
                    'type' => 'order',
                    'icon' => 'mdi:cart',
                    'color' => 'indigo',
                    'message' => "Transaksi #{$order->id}{$branchInfo} - " . ($order->customer->name ?? 'Umum') . " - Rp " . number_format($order->total_amount, 0, ',', '.'),
                    'time' => $order->created_at->diffForHumans(),
                    'sort_time' => $order->created_at->timestamp,
                ]);
            }
            
            // Ambil 5 stok menipis terakhir (ingredients) - PERBAIKAN: gunakan id untuk sorting
            $stockQuery = BranchStock::with(['ingredient', 'branch'])
                ->where('quantity', '>', 0)
                ->whereRaw('quantity <= min_stock')
                ->orderBy('id', 'desc')  // Gunakan id karena tidak ada created_at/updated_at
                ->limit(5);
            
            if (!$showAllBranches && $branchId && $branchId !== 'all') {
                $stockQuery->where('branch_id', $branchId);
            }
            
            $lowStocks = $stockQuery->get();
            
            foreach ($lowStocks as $stock) {
                // Pastikan ingredient ada
                $ingredientName = $stock->ingredient ? $stock->ingredient->name : 'Bahan tidak diketahui';
                $unitName = $stock->ingredient && $stock->ingredient->unit 
                    ? ($stock->ingredient->unit->name ?? 'unit') 
                    : 'unit';
                
                $branchInfo = ($isSuperAdmin && $showAllBranches && $stock->branch) 
                    ? " [{$stock->branch->name}]" 
                    : '';
                
                $activities->push([
                    'type' => 'stock',
                    'icon' => 'mdi:alert',
                    'color' => 'amber',
                    'message' => "Stok menipis: {$ingredientName}{$branchInfo} (sisa {$stock->quantity} {$unitName})",
                    'time' => 'Baru saja',
                    'sort_time' => $stock->id, // Gunakan id untuk sorting
                ]);
            }
            
            // Ambil 3 transaksi pending
            $paymentQuery = Order::with('branch')
                ->where('payment_status', '!=', 'paid')
                ->orderBy('created_at', 'desc')
                ->limit(3);
            
            if (!$showAllBranches && $branchId && $branchId !== 'all') {
                $paymentQuery->where('branch_id', $branchId);
            }
            
            $pendingPayments = $paymentQuery->get();
            
            foreach ($pendingPayments as $payment) {
                $branchInfo = ($isSuperAdmin && $showAllBranches && $payment->branch) 
                    ? " [{$payment->branch->name}]" 
                    : '';
                
                $remainingAmount = $payment->remaining_receivable ?? ($payment->total_amount - $payment->total_paid);
                
                $activities->push([
                    'type' => 'payment',
                    'icon' => 'mdi:cash-clock',
                    'color' => 'purple',
                    'message' => "Pembayaran tertunda #{$payment->id}{$branchInfo} - Rp " . number_format($remainingAmount, 0, ',', '.'),
                    'time' => $payment->created_at->diffForHumans(),
                    'sort_time' => $payment->created_at->timestamp,
                ]);
            }
            
            // Sort activities by sort_time (newest first)
            $sortedActivities = $activities->sortByDesc(function($activity) {
                return $activity['sort_time'];
            })->values();
            
            return response()->json([
                'success' => true,
                'activities' => $sortedActivities->take(10)
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Activities error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat aktivitas: ' . $e->getMessage(),
                'activities' => []
            ], 500);
        }
    }
    
    /**
     * Export report to PDF
     */
    public function export(Request $request)
    {

        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        $userBranchId = $user->branch_id;
        
        // Untuk Super Admin: bisa pilih branch atau 'all'
        // Untuk user biasa: hanya branch sendiri
        $selectedBranchId = $request->get('branch_id');
        
        if ($isSuperAdmin) {
            // Super admin bisa memilih branch
            $branchId = ($selectedBranchId && $selectedBranchId !== 'all') ? $selectedBranchId : null;
            $showAllBranches = ($selectedBranchId === 'all' || !$selectedBranchId);
        } else {
            // User biasa hanya branch sendiri
            $branchId = $userBranchId;
            $showAllBranches = false;
        }
        
        // =======================
        // BASIC KPI
        // =======================
        
        $totalProducts = Product::count();
        
        // Low stock & Out of stock dari branch_stock (untuk INGREDIENTS)
        $lowStockQuery = BranchStock::where('quantity', '>', 0)
            ->whereColumn('quantity', '<=', 'min_stock');
        
        $outOfStockQuery = BranchStock::where('quantity', 0);
        
        if (!$showAllBranches && $branchId) {
            $lowStockQuery->where('branch_id', $branchId);
            $outOfStockQuery->where('branch_id', $branchId);
        }
        
        $lowStock = $lowStockQuery->count();
        $outOfStock = $outOfStockQuery->count();
        
        // Total users
        if ($showAllBranches) {
            $totalUsers = User::count();
        } else {
            $totalUsers = User::where('branch_id', $branchId)->count();
        }
        
        // =======================
        // PENDAPATAN HARI INI
        // =======================
        $todayRevenueQuery = Order::whereDate('date', Carbon::today())
            ->where('payment_status', 'paid');
        
        if (!$showAllBranches && $branchId) {
            $todayRevenueQuery->where('branch_id', $branchId);
        }
        
        $todayRevenue = $todayRevenueQuery->sum('total_amount') ?? 0;
        
        // Jumlah transaksi hari ini
        $todayTransactionsQuery = Order::whereDate('date', Carbon::today());
        
        if (!$showAllBranches && $branchId) {
            $todayTransactionsQuery->where('branch_id', $branchId);
        }
        
        $todayTransactions = $todayTransactionsQuery->count();
        
        // Pendapatan kemarin untuk perbandingan
        $yesterdayRevenueQuery = Order::whereDate('date', Carbon::yesterday())
            ->where('payment_status', 'paid');
        
        if (!$showAllBranches && $branchId) {
            $yesterdayRevenueQuery->where('branch_id', $branchId);
        }
        
        $yesterdayRevenue = $yesterdayRevenueQuery->sum('total_amount') ?? 0;
        
        // Hitung persentase perubahan
        $revenueChange = 0;
        if ($yesterdayRevenue > 0) {
            $revenueChange = (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100;
        } elseif ($todayRevenue > 0) {
            $revenueChange = 100;
        }
        
        // =======================
        // PIUTANG & HUTANG
        // =======================
        
        $piutangQuery = Order::where('payment_status', '!=', 'paid');
        $hutangQuery = Purchase::where('payment_status', '!=', 'paid');
        
        if (!$showAllBranches && $branchId) {
            $piutangQuery->where('branch_id', $branchId);
            $hutangQuery->where('branch_id', $branchId);
        }
        
        $totalPiutang = $piutangQuery->sum('remaining_receivable') ?? 0;
        $totalHutang = $hutangQuery->sum('remaining_debt') ?? 0;
        
        // =======================
        // CHART PENDAPATAN
        // =======================
        
        $revenueQuery = Order::selectRaw('MONTH(date) as month, SUM(total_amount) as total')
            ->whereYear('date', now()->year)
            ->where('payment_status', 'paid');
        
        $expenseQuery = Expense::selectRaw('MONTH(date) as month, SUM(amount) as total')
            ->whereYear('date', now()->year);
        
        if (!$showAllBranches && $branchId) {
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
            $revenues[] = (float)($revenueRaw[$m] ?? 0);
            $expenses[] = (float)($expenseRaw[$m] ?? 0);
        }
        
        // =======================
        // DISTRIBUSI STOK (BERDASARKAN INGREDIENTS/Bahan Baku)
        // =======================
        
        $stockQuery = BranchStock::join('ingredients', 'branch_stock.ingredient_id', '=', 'ingredients.id')
            ->selectRaw('ingredients.name as ingredient_name, SUM(branch_stock.quantity) as total');
        
        if (!$showAllBranches && $branchId) {
            $stockQuery->where('branch_stock.branch_id', $branchId);
        }
        
        $stockData = $stockQuery->groupBy('ingredients.id', 'ingredients.name')
            ->orderBy('total', 'desc')
            ->limit(10)
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
                    'id' => $branch->id,
                    'name' => $branch->name,
                    'total_orders' => Order::where('branch_id', $branch->id)->count(),
                    'total_revenue' => (float)(Order::where('branch_id', $branch->id)->sum('total_amount') ?? 0),
                    'today_revenue' => (float)(Order::where('branch_id', $branch->id)
                        ->whereDate('date', Carbon::today())
                        ->where('payment_status', 'paid')
                        ->sum('total_amount') ?? 0),
                    'total_ingredients' => BranchStock::where('branch_id', $branch->id)
                        ->distinct('ingredient_id')
                        ->count('ingredient_id'),
                    'low_stock_count' => BranchStock::where('branch_id', $branch->id)
                        ->whereColumn('quantity', '<=', 'min_stock')
                        ->where('quantity', '>', 0)
                        ->count(),
                    'out_of_stock_count' => BranchStock::where('branch_id', $branch->id)
                        ->where('quantity', 0)
                        ->count(),
                ];
            }
        }
        
        // Branch name untuk display
        $currentBranchName = 'Semua Cabang';
        if (!$showAllBranches && $branchId) {
            $currentBranch = Branch::find($branchId);
            $currentBranchName = $currentBranch ? $currentBranch->name : 'Cabang ' . $branchId;
        } elseif (!$showAllBranches && !$branchId && $userBranchId) {
            $currentBranch = Branch::find($userBranchId);
            $currentBranchName = $currentBranch ? $currentBranch->name : 'Cabang ' . $userBranchId;
        }
        
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.pdf.dashboard', compact(
            'totalProducts',
            'lowStock',
            'outOfStock',
            'totalUsers',
            'todayRevenue',
            'todayTransactions',
            'revenueChange',
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
            'branchId',
            'showAllBranches',
            'currentBranchName',
            'userBranchId'
        ));
        
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('Laporan_Dashboard_' . date('Y-m-d') . '.pdf');
    }
}