<?php

namespace App\Http\Controllers;

use App\Services\KasproService;
use Illuminate\Http\Request;
use App\Models\KasproAlert;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Product;
use App\Models\RecipeItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KasproController extends Controller
{
    protected $kaspro;
    
    public function __construct(KasproService $kaspro)
    {
        $this->kaspro = $kaspro;
    }
    
    public function dashboard(Request $request)
    {
        $survival = $this->kaspro->getDaysToSurvive();
        $period = 'monthly';
        $customStart = null;
        $customEnd = null;

        if ($request->has('start_date') && $request->has('end_date')) {
            $period = 'custom';
            $customStart = $request->start_date;
            $customEnd = $request->end_date;
        }

        $cashFlow = $this->kaspro->getCashFlowSummary($period, $customStart, $customEnd);
        $prediction = $this->kaspro->predictCashFlow(3);
        $recentAlerts = KasproAlert::where('user_id', auth()->id())
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Ambil data historis untuk chart
        $historicalData = $this->getHistoricalChartData();
        
        return view('kaspro.dashboard', compact('survival', 'cashFlow', 'prediction', 'recentAlerts', 'historicalData'));
    }
    
    public function prediction()
    {
        $prediction = $this->kaspro->predictCashFlow(6);
        $historicalData = $this->getHistoricalChartData();
        
        return view('kaspro.prediction', compact('prediction', 'historicalData'));
    }
    
    public function alerts(Request $request)
    {
        $query = KasproAlert::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc');
            
        if ($request->has('severity')) {
            $query->where('severity', $request->severity);
        }
            
        $alerts = $query->paginate(20)->appends($request->query());
        
        return view('kaspro.alerts', compact('alerts'));
    }
    
    public function markAsRead($alertId)
    {
        $alert = KasproAlert::where('user_id', auth()->id())
            ->where('id', $alertId)
            ->first();
        
        if ($alert) {
            $alert->update(['is_read' => true]);
            return back()->with('success', 'Peringatan ditandai sebagai dibaca');
        }
        
        return back()->with('error', 'Peringatan tidak ditemukan');
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
     * Analisis margin per produk berdasarkan HPP dari ingredients
     */
    public function marginAnalysis(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        $userBranchId = $user->branch_id;
        
        // Filter branch
        $branchId = $request->get('branch_id');
        if (!$isSuperAdmin) {
            $branchId = $userBranchId;
        }
        
        // Filter periode
        $period = $request->get('period', 'monthly'); // daily, weekly, monthly, yearly, all
        $dateRange = $this->getDateRange($period);
        
        // Query untuk mendapatkan produk yang terjual dalam periode
        $orderItemsQuery = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.payment_status', 'paid');
        
        // Filter berdasarkan branch
        if ($branchId) {
            $orderItemsQuery->where('orders.branch_id', $branchId);
        }
        
        // Filter berdasarkan tanggal
        if ($dateRange['start']) {
            $orderItemsQuery->whereBetween('orders.date', [$dateRange['start'], $dateRange['end']]);
        }
        
        // Ambil data produk yang terjual
        $productSales = $orderItemsQuery->select(
            'products.id as product_id',
            DB::raw('COALESCE(order_items.custom_name, products.name) as product_name'),
            'products.selling_price',
            'products.purchase_price',
            DB::raw('SUM(order_items.quantity) as total_quantity'),
            DB::raw('SUM(order_items.subtotal) as total_revenue')
        )
        ->groupBy('products.id', 'products.name', 'order_items.custom_name', 'products.selling_price', 'products.purchase_price')
        ->orderBy('total_revenue', 'desc')
        ->get();
        
        $productMargins = [];
        $totalRevenue = 0;
        $TotalHPP = 0;
        $totalProfit = 0;
        
        foreach ($productSales as $product) {
            // Hitung HPP berdasarkan resep ingredients
            $hpp = $this->calculateProductHPP($product->product_id);
            $totalHPPProduct = $hpp * $product->total_quantity;
            $profit = $product->total_revenue - $totalHPPProduct;
            $margin = $product->total_revenue > 0 ? ($profit / $product->total_revenue) * 100 : 0;
            
            $productMargins[] = (object) [
                'product_id' => $product->product_id,
                'product_name' => $product->product_name,
                'selling_price' => $product->selling_price,
                'hpp' => $hpp,
                'total_quantity' => $product->total_quantity,
                'total_revenue' => $product->total_revenue,
                'total_hpp' => $totalHPPProduct,
                'profit' => $profit,
                'margin' => $margin,
                'margin_percent' => round($margin, 2),
            ];
            
            $totalRevenue += $product->total_revenue;
            $TotalHPP += $totalHPPProduct;
            $totalProfit += $profit;
        }
        
        // Urutkan berdasarkan margin terendah (untuk warning)
        usort($productMargins, function($a, $b) {
            return $a->margin <=> $b->margin;
        });
        
        // Produk dengan margin rendah (< 20%)
        $lowMarginProducts = array_filter($productMargins, function($p) {
            return $p->margin < 20;
        });
        
        // Ringkasan per kategori
        $categorySummary = $this->getCategorySummary($dateRange, $branchId);
        
        // Overall margin
        $overallMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;
        
        // Get branches for filter
        $branches = [];
        if ($isSuperAdmin) {
            $branches = \App\Models\Branch::where('is_active', true)->get();
        }
        
        $currentBranchName = 'Semua Cabang';
        if ($branchId) {
            $branch = \App\Models\Branch::find($branchId);
            $currentBranchName = $branch ? $branch->name : 'Cabang';
        }
        
        return view('kaspro.margin-analysis', compact(
            'productMargins',
            'lowMarginProducts',
            'categorySummary',
            'totalRevenue',
            'TotalHPP',
            'totalProfit',
            'overallMargin',
            'period',
            'dateRange',
            'isSuperAdmin',
            'branches',
            'branchId',
            'currentBranchName'
        ));
    }
    
    /**
     * Get ringkasan margin per kategori
     */
    private function getCategorySummary($dateRange, $branchId)
    {
        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.payment_status', 'paid');
        
        if ($branchId) {
            $query->where('orders.branch_id', $branchId);
        }
        
        if ($dateRange['start']) {
            $query->whereBetween('orders.date', [$dateRange['start'], $dateRange['end']]);
        }
        
        $categories = $query->select(
            'categories.id as category_id',
            'categories.name as category_name',
            DB::raw('SUM(order_items.quantity) as total_quantity'),
            DB::raw('SUM(order_items.subtotal) as total_revenue')
        )
        ->groupBy('categories.id', 'categories.name')
        ->get();
        
        $summary = [];
        
        foreach ($categories as $category) {
            // Ambil semua produk dalam kategori ini
            $products = Product::where('category_id', $category->category_id)->get();
            $totalHPP = 0;
            
            foreach ($products as $product) {
                // Hitung quantity terjual untuk produk ini dalam periode
                $quantityQuery = DB::table('order_items')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->where('order_items.product_id', $product->id)
                    ->where('orders.payment_status', 'paid');
                
                if ($branchId) {
                    $quantityQuery->where('orders.branch_id', $branchId);
                }
                
                if ($dateRange['start']) {
                    $quantityQuery->whereBetween('orders.date', [$dateRange['start'], $dateRange['end']]);
                }
                
                $quantitySold = $quantityQuery->sum('order_items.quantity') ?? 0;
                $hpp = $this->calculateProductHPP($product->id);
                $totalHPP += $hpp * $quantitySold;
            }
            
            $profit = $category->total_revenue - $totalHPP;
            $margin = $category->total_revenue > 0 ? ($profit / $category->total_revenue) * 100 : 0;
            
            $summary[] = [
                'category_name' => $category->category_name,
                'total_quantity' => $category->total_quantity,
                'total_revenue' => $category->total_revenue,
                'total_hpp' => $totalHPP,
                'profit' => $profit,
                'margin' => $margin,
            ];
        }
        
        // Urutkan berdasarkan margin terendah
        usort($summary, function($a, $b) {
            return $a['margin'] <=> $b['margin'];
        });
        
        return $summary;
    }
    
    /**
     * Get date range based on period
     */
    private function getDateRange($period)
    {
        switch ($period) {
            case 'daily':
                return [
                    'start' => Carbon::today()->startOfDay(),
                    'end' => Carbon::today()->endOfDay(),
                    'label' => 'Hari Ini'
                ];
            case 'weekly':
                return [
                    'start' => Carbon::now()->startOfWeek(),
                    'end' => Carbon::now()->endOfWeek(),
                    'label' => 'Minggu Ini'
                ];
            case 'monthly':
                return [
                    'start' => Carbon::now()->startOfMonth(),
                    'end' => Carbon::now()->endOfMonth(),
                    'label' => 'Bulan Ini'
                ];
            case 'yearly':
                return [
                    'start' => Carbon::now()->startOfYear(),
                    'end' => Carbon::now()->endOfYear(),
                    'label' => 'Tahun Ini'
                ];
            default: // all
                return [
                    'start' => null,
                    'end' => null,
                    'label' => 'Semua Waktu'
                ];
        }
    }
    
    /**
     * Ambil data historis untuk chart (AMAN dari bug tanggal)
     * Menampilkan 6 bulan terakhir, bulan berjalan ditampilkan partial (sd hari ini)
     */
    private function getHistoricalChartData()
    {
        $data = [
            'months' => [],
            'income' => [],
            'expense' => []
        ];
        
        // Ambil 6 bulan terakhir (termasuk bulan berjalan)
        for ($i = 5; $i >= 0; $i--) {
            // Mulai dari AWAL bulan (tanggal 1) - AMAN dari bug tanggal 31
            $startOfMonth = Carbon::now()->startOfMonth()->subMonths($i);
            $endOfMonth = $startOfMonth->copy()->endOfMonth();
            
            // Jika bulan ini adalah bulan berjalan, batasi sampai HARI INI
            $isCurrentMonth = ($i == 0);
            if ($isCurrentMonth) {
                $endOfMonth = Carbon::now(); // bulan berjalan: hanya sampai hari ini
            }
            
            $monthName = $startOfMonth->translatedFormat('M Y');
            if ($isCurrentMonth) {
                $monthName .= ' (sd hari ini)';
            }
            
            // ÃƒÂ¢Ã…â€œÃ¢â‚¬Â¦ Income dari orders (pakai whereBetween, lebih akurat)
            $income = Order::whereBetween('date', [$startOfMonth, $endOfMonth])
                ->where('payment_status', 'paid')
                ->sum('total_amount');
            
            // ÃƒÂ¢Ã…â€œÃ¢â‚¬Â¦ Expense dari purchases (pakai total_paid, sesuai arus kas riil)
            $expense = Purchase::whereBetween('date', [$startOfMonth, $endOfMonth])
                ->sum('total_paid');
            
            // ÃƒÂ¢Ã…â€œÃ¢â‚¬Â¦ Expense dari expenses (operasional)
            $expense += Expense::whereBetween('date', [$startOfMonth, $endOfMonth])
                ->sum('amount');
            
            $data['months'][] = $monthName;
            $data['income'][] = (float) $income;
            $data['expense'][] = (float) $expense;
        }
        
        return $data;
    }
}