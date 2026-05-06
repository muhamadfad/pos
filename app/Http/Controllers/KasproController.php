<?php

namespace App\Http\Controllers;

use App\Services\KasproService;
use Illuminate\Http\Request;
use App\Models\KasproAlert;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KasproController extends Controller
{
    protected $kaspro;
    
    public function __construct(KasproService $kaspro)
    {
        $this->kaspro = $kaspro;
    }
    
    public function dashboard()
    {
        $survival = $this->kaspro->getDaysToSurvive();
        $cashFlow = $this->kaspro->getCashFlowSummary('monthly');
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
    
    public function alerts()
    {
        $alerts = KasproAlert::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
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
    
    public function marginAnalysis()
    {
        // Analisis margin per produk dari order_items
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                'products.purchase_price',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('order_items.product_id', 'products.name', 'products.purchase_price')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();
        
        return view('kaspro.margin-analysis', compact('topProducts'));
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
            
            // ✅ Income dari orders (pakai whereBetween, lebih akurat)
            $income = Order::whereBetween('date', [$startOfMonth, $endOfMonth])
                ->sum('total_amount');
            
            // ✅ Expense dari purchases (pakai total_paid, sesuai arus kas riil)
            $expense = Purchase::whereBetween('date', [$startOfMonth, $endOfMonth])
                ->sum('total_paid');
            
            // ✅ Expense dari expenses (operasional)
            $expense += Expense::whereBetween('date', [$startOfMonth, $endOfMonth])
                ->sum('amount');
            
            $data['months'][] = $monthName;
            $data['income'][] = (float) $income;
            $data['expense'][] = (float) $expense;
        }
        
        return $data;
    }
}