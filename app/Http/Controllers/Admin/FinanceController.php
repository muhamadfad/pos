<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BarangKeluar;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    // ================= DASHBOARD UTAMA =================
    public function index()
    {
        $total = $this->calculateProfit();
        $daily = $this->calculateProfit(Carbon::today());
        $weekly = $this->calculateProfit(Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek());
        $monthly = $this->calculateProfit(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth());
        $yearly = $this->calculateProfit(Carbon::now()->startOfYear(), Carbon::now()->endOfYear());

        return view('admin.finance.index', compact('total', 'daily', 'weekly', 'monthly', 'yearly'));
    }

    // ================= LAPORAN HARIAN =================
    public function daily()
    {
        $today = Carbon::today();
        $finance = $this->calculateProfit($today);

        // Data grafik barang terlaris hari ini
        $topProducts = BarangKeluar::join('products', 'barang_keluar.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(barang_keluar.quantity) as total_qty'))
            ->whereDate('barang_keluar.date', $today)
            ->groupBy('products.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return view('admin.finance.daily', compact('finance', 'today', 'topProducts'));
    }

    // ================= LAPORAN MINGGUAN =================
    public function weekly()
{
    $start = Carbon::now()->startOfWeek();
    $end = Carbon::now()->endOfWeek();

    $finance = $this->calculateProfit($start, $end);

    // DATA GRAFIK: Produk paling banyak terjual minggu ini
    $topProducts = BarangKeluar::join('products', 'barang_keluar.product_id', '=', 'products.id')
                ->whereBetween('barang_keluar.date', [$start, $end])
                ->select('products.name', DB::raw('SUM(barang_keluar.quantity) as total_qty'))
                ->groupBy('products.name')
                ->orderByDesc('total_qty')
                ->get();

    return view('admin.finance.weekly', compact('finance', 'start', 'end', 'topProducts'));
}


    // ================= LAPORAN BULANAN =================
    public function monthly()
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $finance = $this->calculateProfit($start, $end);

        return view('admin.finance.monthly', compact('finance', 'start', 'end'));
    }

    // ================= LAPORAN TAHUNAN =================
    public function yearly()
    {
        $start = Carbon::now()->startOfYear();
        $end = Carbon::now()->endOfYear();
        $finance = $this->calculateProfit($start, $end);

        return view('admin.finance.yearly', compact('finance', 'start', 'end'));
    }

    // ================= HELPER =================
    private function calculateProfit($start = null, $end = null)
    {
        $query = BarangKeluar::join('products', 'barang_keluar.product_id', '=', 'products.id')
            ->select(
                DB::raw('SUM(barang_keluar.quantity * products.price) as income'),
                DB::raw('SUM(barang_keluar.quantity * products.purchase_price) as cost')
            );

        if ($start && $end) {
            $query->whereBetween('barang_keluar.date', [$start, $end]);
        } elseif ($start) {
            $query->whereDate('barang_keluar.date', $start);
        }

        $result = $query->first();

        $income = $result->income ?? 0;
        $cost = $result->cost ?? 0;
        $profit = $income - $cost;

        return compact('income', 'cost', 'profit');
    }
}
