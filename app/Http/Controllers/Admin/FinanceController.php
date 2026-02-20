<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Expense;
use App\Models\Purchase;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function index()
    {
        $total   = $this->calculateProfit();
        $daily   = $this->calculateProfit(Carbon::today());
        $weekly  = $this->calculateProfit(Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek());
        $monthly = $this->calculateProfit(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth());
        $yearly  = $this->calculateProfit(Carbon::now()->startOfYear(), Carbon::now()->endOfYear());

        $totalIncome       = $total['income'];
        $totalExpense      = $total['expenses'];
        $totalSupplierPaid = $total['supplierPaid'];
        $netCashflow       = $total['netCashflow'];

        return view('admin.finance.index', compact(
            'total',
            'daily',
            'weekly',
            'monthly',
            'yearly',
            'totalIncome',
            'totalExpense',
            'totalSupplierPaid',
            'netCashflow'
        ));
    }

    public function cashflow()
    {
        // Breakdown per bulan (12 bulan terakhir)
        $months = collect(range(11, 0))->map(function ($i) {
            $start = Carbon::now()->subMonths($i)->startOfMonth();
            $end   = Carbon::now()->subMonths($i)->endOfMonth();

            $summary = $this->calculateProfit($start, $end);

            return [
                'label'        => $start->translatedFormat('M Y'),
                'income'       => $summary['income'],
                'supplier'     => $summary['supplierPaid'],
                'operasional'  => $summary['expenses'],
                'net'          => $summary['netCashflow'],
            ];
        });

        $total   = $this->calculateProfit();
        $totalIncome       = $total['income'];
        $totalSupplierPaid = $total['supplierPaid'];
        $totalOperasional  = $total['expenses'];
        $netCashflow       = $total['netCashflow'];

        // Rincian expenses per kategori
        $expenseByCategory = Expense::select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        // Top 10 pembelian supplier terbesar
        $topSupplierPurchases = Purchase::with('supplier')
            ->orderByDesc('total_paid')
            ->limit(10)
            ->get();

        return view('admin.finance.cashflow', compact(
            'months',
            'totalIncome',
            'totalSupplierPaid',
            'totalOperasional',
            'netCashflow',
            'expenseByCategory',
            'topSupplierPurchases'
        ));
    }

    public function daily()
    {
        $todayStr = Carbon::today()->toDateString();
        $todayObj = Carbon::today();
        $finance = $this->calculateProfit($todayObj);

        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereDate('orders.date', $todayStr)
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_qty'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $item->total_qty = (float) $item->total_qty;
                return $item;
            });

        // Expenses detail for the day
        $expenses = Expense::whereDate('date', $todayStr)->get();

        return view('admin.finance.daily', [
            'finance'     => $finance,
            'today'       => $todayObj,
            'topProducts' => $topProducts,
            'expenses'    => $expenses,
        ]);
    }

    public function weekly()
    {
        $start = Carbon::now()->startOfWeek();
        $end   = Carbon::now()->endOfWeek();
        $finance = $this->calculateProfit($start, $end);

        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.date', [$start->toDateString(), $end->toDateString()])
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_qty'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $item->total_qty = (float) $item->total_qty;
                return $item;
            });

        return view('admin.finance.weekly', compact('finance', 'start', 'end', 'topProducts'));
    }

    public function monthly()
    {
        $start = Carbon::now()->startOfMonth();
        $end   = Carbon::now()->endOfMonth();
        $finance = $this->calculateProfit($start, $end);

        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.date', [$start->toDateString(), $end->toDateString()])
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_qty'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $item->total_qty = (float) $item->total_qty;
                return $item;
            });

        return view('admin.finance.monthly', compact('finance', 'start', 'end', 'topProducts'));
    }

    // ================= HELPER =================
    private function calculateProfit($start = null, $end = null)
    {
        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                DB::raw('SUM(order_items.subtotal) as income'),
                DB::raw('SUM(order_items.quantity * products.purchase_price) as cost')
            );

        if ($start && $end) {
            $query->whereBetween('orders.date', [$start, $end]);
        } elseif ($start) {
            $query->whereDate('orders.date', $start);
        }

        $result = $query->first();

        $income = $result->income ?? 0;
        $cost   = $result->cost ?? 0;
        $profit = $income - $cost;

        // Expenses in period
        $expenseQuery = Expense::query();
        $purchaseQuery = Purchase::query();

        if ($start && $end) {
            $expenseQuery->whereBetween('date', [$start, $end]);
            $purchaseQuery->whereBetween('date', [$start, $end]);
        } elseif ($start) {
            $expenseQuery->whereDate('date', $start);
            $purchaseQuery->whereDate('date', $start);
        }

        $expenses = $expenseQuery->sum('amount');
        $supplierPaid = $purchaseQuery->sum('total_paid');
        $netCashflow = $income - $supplierPaid - $expenses;

        return compact('income', 'cost', 'profit', 'expenses', 'supplierPaid', 'netCashflow');
    }
}
