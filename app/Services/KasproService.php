<?php

namespace App\Services;

use App\Models\KasproAlert;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KasproService
{
    /**
     * Ambil user ID yang sedang login
     */
    private function getUserId()
    {
        return Auth::id();
    }
    
    /**
     * Cek apakah user adalah admin
     */
    private function isAdmin()
    {
        $user = Auth::user();
        return $user && $user->role === 'admin';
    }
    
    /**
     * Ambil data orders (penjualan)
     */
    private function getOrdersQuery()
    {
        $query = Order::query();
        
        if (!$this->isAdmin() && \Schema::hasColumn('orders', 'created_by')) {
            $query->where('created_by', $this->getUserId());
        }
        
        return $query;
    }
    
    /**
     * Ambil data purchases (pembelian)
     */
    private function getPurchasesQuery()
    {
        $query = Purchase::query();
        
        if (!$this->isAdmin() && \Schema::hasColumn('purchases', 'created_by')) {
            $query->where('created_by', $this->getUserId());
        }
        
        return $query;
    }
    
    /**
     * Ambil data expenses (pengeluaran operasional)
     */
    private function getExpensesQuery()
    {
        $query = Expense::query();
        
        if (!$this->isAdmin() && \Schema::hasColumn('expenses', 'created_by')) {
            $query->where('created_by', $this->getUserId());
        }
        
        return $query;
    }
    
    /**
     * Hitung saldo kas saat ini
     * ✅ PERBAIKAN: pakai total_paid untuk pengeluaran ke supplier
     */
    public function getCurrentCash()
    {
        $totalIncome = $this->getOrdersQuery()->sum('total_amount');
        $totalPurchaseExpense = $this->getPurchasesQuery()->sum('total_paid');  // ✅ UBAH: total_amount → total_paid
        $totalOperationalExpense = $this->getExpensesQuery()->sum('amount');
        
        $initialCash = 0;
        $currentCash = $initialCash + $totalIncome - ($totalPurchaseExpense + $totalOperationalExpense);
        
        return max($currentCash, 0);
    }
    
    /**
     * Hitung hari bertahan (runway)
     * ✅ PERBAIKAN: pakai total_paid untuk pengeluaran ke supplier
     */
    public function getDaysToSurvive()
    {
        $currentCash = $this->getCurrentCash();
        
        $startDate = Carbon::now()->subDays(30);
        
        $purchaseExpense = $this->getPurchasesQuery()
            ->whereDate('date', '>=', $startDate)
            ->sum('total_paid');  // ✅ UBAH: total_amount → total_paid
        
        $operationalExpense = $this->getExpensesQuery()
            ->whereDate('date', '>=', $startDate)
            ->sum('amount');
        
        $totalExpense30 = $purchaseExpense + $operationalExpense;
        $avgDailyExpense = $totalExpense30 / 30;
        
        if ($avgDailyExpense <= 0) {
            return [
                'current_cash' => $currentCash,
                'current_cash_formatted' => 'Rp ' . number_format($currentCash, 0, ',', '.'),
                'avg_daily_expense' => 0,
                'avg_daily_expense_formatted' => 'Rp 0',
                'days_to_survive' => 999,
                'status' => 'SUPER AMAN',
                'status_color' => 'green'
            ];
        }
        
        $daysToSurvive = floor($currentCash / $avgDailyExpense);
        
        if ($daysToSurvive > 90) {
            $status = 'SUPER AMAN';
            $statusColor = 'green';
        } elseif ($daysToSurvive > 30) {
            $status = 'AMAN';
            $statusColor = 'green';
        } elseif ($daysToSurvive > 15) {
            $status = 'WASPADA';
            $statusColor = 'yellow';
        } elseif ($daysToSurvive > 7) {
            $status = 'KRITIS';
            $statusColor = 'orange';
        } else {
            $status = 'DARURAT';
            $statusColor = 'red';
        }
        
        return [
            'current_cash' => $currentCash,
            'current_cash_formatted' => 'Rp ' . number_format($currentCash, 0, ',', '.'),
            'avg_daily_expense' => $avgDailyExpense,
            'avg_daily_expense_formatted' => 'Rp ' . number_format($avgDailyExpense, 0, ',', '.'),
            'days_to_survive' => $daysToSurvive,
            'status' => $status,
            'status_color' => $statusColor
        ];
    }
    
    /**
     * Ringkasan arus kas per periode
     * ✅ PERBAIKAN: pakai total_paid untuk pengeluaran ke supplier
     */
    public function getCashFlowSummary($period = 'monthly')
    {
        if ($period == 'daily') {
            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } elseif ($period == 'weekly') {
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
        } else {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }
        
        $incomeFromOrders = $this->getOrdersQuery()
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->sum('total_amount');
        
        $expenseFromPurchases = $this->getPurchasesQuery()
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->sum('total_paid');  // ✅ UBAH: total_amount → total_paid
        
        $expenseFromOperational = $this->getExpensesQuery()
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->sum('amount');
        
        return [
            'total_income' => $incomeFromOrders,
            'total_expense' => $expenseFromPurchases + $expenseFromOperational,
            'expense_breakdown' => [
                'stok' => $expenseFromPurchases,
                'operasional' => $expenseFromOperational,
            ],
            'net_cash_flow' => $incomeFromOrders - ($expenseFromPurchases + $expenseFromOperational),
            'period' => $period,
            'start_date' => $startDate->format('d/m/Y'),
            'end_date' => $endDate->format('d/m/Y')
        ];
    }
    
    /**
     * Ambil data historis per bulan
     * ✅ PERBAIKAN: pakai total_paid untuk pengeluaran ke supplier
     */
    public function getHistoricalData($months = 12)
    {
        $startDate = Carbon::now()->subMonths($months)->startOfMonth();
        
        $incomes = $this->getOrdersQuery()
            ->whereDate('date', '>=', $startDate)
            ->select(
                DB::raw('DATE_FORMAT(date, "%Y-%m") as month'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('month')
            ->get()
            ->keyBy('month');
        
        $purchaseExpenses = $this->getPurchasesQuery()
            ->whereDate('date', '>=', $startDate)
            ->select(
                DB::raw('DATE_FORMAT(date, "%Y-%m") as month'),
                DB::raw('SUM(total_paid) as total')  // ✅ UBAH: total_amount → total_paid
            )
            ->groupBy('month')
            ->get()
            ->keyBy('month');
        
        $operationalExpenses = $this->getExpensesQuery()
            ->whereDate('date', '>=', $startDate)
            ->select(
                DB::raw('DATE_FORMAT(date, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->get()
            ->keyBy('month');
        
        $result = [];
        for ($i = 0; $i < $months; $i++) {
            $month = Carbon::now()->subMonths($i)->format('Y-m');
            $result[] = [
                'month' => $month,
                'income' => $incomes[$month]->total ?? 0,
                'expense' => ($purchaseExpenses[$month]->total ?? 0) + ($operationalExpenses[$month]->total ?? 0)
            ];
        }
        
        return array_reverse($result);
    }
    
    /**
     * Ambil data per bulan tertentu (untuk musiman)
     * ✅ PERBAIKAN: pakai total_paid untuk pengeluaran ke supplier
     */
    public function getDataByMonth($month, $year)
    {
        $income = $this->getOrdersQuery()
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->sum('total_amount');
        
        $expense = $this->getPurchasesQuery()
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->sum('total_paid');  // ✅ UBAH: total_amount → total_paid
        
        $expense += $this->getExpensesQuery()
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->sum('amount');
        
        return [
            'income' => $income,
            'expense' => $expense
        ];
    }
    
    /**
     * Data untuk chart historis
     */
    public function getHistoricalChartData()
    {
        $historical = $this->getHistoricalData(12);
        
        $months = [];
        $income = [];
        $expense = [];
        
        foreach ($historical as $data) {
            $months[] = Carbon::parse($data['month'] . '-01')->translatedFormat('M Y');
            $income[] = $data['income'];
            $expense[] = $data['expense'];
        }
        
        return [
            'months' => $months,
            'income' => $income,
            'expense' => $expense
        ];
    }
    
    /**
     * ==================== PREDIKSI DENGAN PRIORITAS MUSIMAN ====================
     * Menggunakan data tahun lalu sebagai acuan utama
     */
    
    /**
     * Prediksi arus kas dengan prioritas pola musiman
     * Menggunakan data tahun lalu sebagai acuan utama
     */
    public function predictCashFlow($months = 3)
    {
        // Ambil data historis 12 bulan
        $historicalData = $this->getHistoricalData(12);
        
        if (count($historicalData) < 12) {
            return [
                'predictions' => [],
                'cash_flow_gaps' => [],
                'recommendations' => ['Data historis minimal 12 bulan diperlukan untuk prediksi musiman yang akurat.'],
                'trend_analysis' => null
            ];
        }
        
        $currentYear = Carbon::now()->year;
        $lastYear = $currentYear - 1;
        
        // ========== 1. HITUNG POLA MUSIMAN DARI TAHUN LALU ==========
        $monthlyDataLastYear = [];
        $totalIncomeLastYear = 0;
        
        for ($month = 1; $month <= 12; $month++) {
            $data = $this->getDataByMonth($month, $lastYear);
            $monthlyDataLastYear[$month] = $data;
            $totalIncomeLastYear += $data['income'];
        }
        
        $avgMonthlyLastYear = $totalIncomeLastYear / 12;
        
        // Faktor musiman (rasio terhadap rata-rata)
        $seasonalFactors = [];
        foreach ($monthlyDataLastYear as $month => $data) {
            $seasonalFactors[$month] = $avgMonthlyLastYear > 0 ? $data['income'] / $avgMonthlyLastYear : 1;
        }
        
        // ========== 2. HITUNG GROWTH RATE (PERTUMBUHAN) ==========
        $currentMonth = Carbon::now()->month;
        $totalIncomeThisYear = 0;
        $totalIncomeLastYearPeriod = 0;
        
        for ($month = 1; $month <= $currentMonth; $month++) {
            $dataThisYear = $this->getDataByMonth($month, $currentYear);
            $dataLastYear = $this->getDataByMonth($month, $lastYear);
            
            $totalIncomeThisYear += $dataThisYear['income'];
            $totalIncomeLastYearPeriod += $dataLastYear['income'];
        }
        
        // Hitung growth rate (batasi antara 0.7 dan 1.5 agar tidak ekstrim)
        $growthRate = 1.0;
        if ($totalIncomeLastYearPeriod > 0) {
            $growthRate = $totalIncomeThisYear / $totalIncomeLastYearPeriod;
            $growthRate = max(0.7, min(1.5, $growthRate));
        }
        
        // ========== 3. BUAT PREDIKSI BERDASARKAN MUSIMAN + GROWTH ==========
        $predictions = [];
        $currentDate = Carbon::now();
        
        for ($i = 0; $i < $months; $i++) {
            $targetMonth = $currentDate->copy()->addMonths($i + 1);
            $monthNumber = $targetMonth->month;
            
            // Data tahun lalu untuk bulan yang sama
            $lastYearData = $monthlyDataLastYear[$monthNumber] ?? ['income' => 0, 'expense' => 0];
            
            // Prediksi = data tahun lalu × growth rate
            $predictedIncome = $lastYearData['income'] * $growthRate;
            $predictedExpense = $lastYearData['expense'] * $growthRate;
            
            // Validasi: jika growth rate tidak masuk akal, gunakan faktor musiman saja
            if ($growthRate < 0.5 || $growthRate > 2) {
                $predictedIncome = $avgMonthlyLastYear * $seasonalFactors[$monthNumber];
                $predictedExpense = $lastYearData['expense'] * $seasonalFactors[$monthNumber];
            }
            
            $predictions[] = [
                'month' => $targetMonth->format('Y-m'),
                'month_name' => $targetMonth->translatedFormat('F Y'),
                'predicted_income' => round($predictedIncome),
                'predicted_expense' => round($predictedExpense),
                'predicted_net' => round($predictedIncome - $predictedExpense),
                'seasonal_factor' => round($seasonalFactors[$monthNumber], 2),
                'last_year_income' => round($lastYearData['income']),
                'growth_rate' => round($growthRate, 2)
            ];
        }
        
        // ========== 4. DETEKSI CASH FLOW GAP ==========
        $gaps = [];
        foreach ($predictions as $pred) {
            if ($pred['predicted_net'] < 0) {
                $gaps[] = [
                    'month' => $pred['month_name'],
                    'gap_amount' => abs($pred['predicted_net']),
                    'severity' => abs($pred['predicted_net']) > 5000000 ? 'HIGH' : 'MEDIUM'
                ];
            }
        }
        
        // ========== 5. REKOMENDASI DENGAN INSIGHT MUSIMAN ==========
        $recommendations = $this->generateSeasonalRecommendations($predictions, $seasonalFactors, $growthRate, $monthlyDataLastYear);
        
        // ========== 6. ANALISIS TREN ==========
        $trend = $this->analyzeTrend($historicalData);
        
        return [
            'predictions' => $predictions,
            'cash_flow_gaps' => $gaps,
            'recommendations' => $recommendations,
            'trend_analysis' => $trend,
            'growth_rate' => round($growthRate, 2),
            'peak_months' => $this->getPeakMonths($seasonalFactors)
        ];
    }
    
    /**
     * Generate rekomendasi dengan insight musiman yang akurat
     */
    private function generateSeasonalRecommendations($predictions, $seasonalFactors, $growthRate, $monthlyDataLastYear)
    {
        $recommendations = [];
        
        // Cari bulan puncak penjualan (faktor musiman tertinggi)
        $peakMonth = 1;
        $peakFactor = 0;
        foreach ($seasonalFactors as $month => $factor) {
            if ($factor > $peakFactor) {
                $peakFactor = $factor;
                $peakMonth = $month;
            }
        }
        
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        // Insight puncak penjualan
        if ($peakFactor > 1.2) {
            $peakIncome = $monthlyDataLastYear[$peakMonth]['income'] ?? 0;
            $formattedFactor = number_format($peakFactor, 1);
            $recommendations[] = "Insight: Bulan {$monthNames[$peakMonth]} tercatat sebagai puncak penjualan historis Anda (mencapai Rp " . 
                                  number_format($peakIncome, 0, ',', '.') . ", atau melonjak {$formattedFactor}x lipat dari rata-rata bulanan).";
            
            // Cek apakah bulan puncak ada dalam prediksi
            foreach ($predictions as $pred) {
                $predMonth = (int) substr($pred['month'], 5, 2);
                if ($predMonth == $peakMonth && $pred['predicted_income'] > 0) {
                    $recommendations[] = "📈 Persiapan Ekstra: Pada {$monthNames[$peakMonth]} {$pred['month']}, sistem memproyeksikan lonjakan pemasukan hingga Rp " . 
                                          number_format($pred['predicted_income'], 0, ',', '.') . 
                                          ". Sangat disarankan untuk mengamankan stok bahan baku 2-3 minggu sebelumnya!";
                }
            }
        }
        
        // Insight pertumbuhan
        if ($growthRate > 1.05) {
            $recommendations[] = "📈 Momentum Positif: Algoritma kami mencatat bisnis Anda sedang tumbuh " . round(($growthRate - 1) * 100) . 
                                 "% lebih pesat dibandingkan rekam jejak tahun lalu. Pertahankan strategi yang ada!";
        } elseif ($growthRate < 0.95) {
            $recommendations[] = "📉 Peringatan Penurunan: Sistem mendeteksi pelemahan performa sebesar " . round((1 - $growthRate) * 100) . 
                                 "% dari periode yang sama tahun lalu. Pertimbangkan untuk meluncurkan promo baru!";
        } else {
            $recommendations[] = "➡️ Kestabilan Terjaga: Alur performa kas Anda saat ini sangat identik dengan tahun lalu (Stabil). Tetap konsisten dan fokus pada retensi pelanggan.";
        }
        
        // Deteksi defisit di prediksi
        $hasDeficit = false;
        foreach ($predictions as $pred) {
            if ($pred['predicted_net'] < 0) {
                $recommendations[] = "⚠️ Peringatan Defisit: Pada bulan {$pred['month_name']}, sistem memprediksi arus kas negatif (tekor) sebesar Rp " . 
                                      number_format(abs($pred['predicted_net']), 0, ',', '.') . 
                                      ". Mulailah menyisihkan dana cadangan dari bulan-bulan profit.";
                $hasDeficit = true;
                break;
            }
        }
        
        // Cek stabilitas pengeluaran
        $expensePredictions = array_column($predictions, 'predicted_expense');
        if (count($expensePredictions) >= 2) {
            $maxExpense = max($expensePredictions);
            $minExpense = min($expensePredictions);
            if ($maxExpense > $minExpense * 1.5) {
                $recommendations[] = '📊 Volatilitas Pengeluaran: Rekam jejak menunjukkan rentang pengeluaran Anda sangat fluktuatif antar bulannya. Disarankan untuk mulai membuat batasan anggaran (budgeting) bulanan yang ketat.';
            }
        }
        
        // Rekomendasi default jika kosong
        if (empty($recommendations)) {
            $recommendations[] = '✅ Kondisi keuangan stabil. Tetap pantau pengeluaran dan evaluasi rutin.';
        }
        
        return $recommendations;
    }
    
    /**
     * Dapatkan bulan-bulan puncak penjualan
     */
    private function getPeakMonths($seasonalFactors)
    {
        $peaks = [];
        $monthNames = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];
        foreach ($seasonalFactors as $month => $factor) {
            if ($factor > 1.1) {
                $peaks[] = $monthNames[$month] . ' (' . round($factor, 1) . 'x)';
            }
        }
        return $peaks;
    }
    
    /**
     * Analisis tren dari data historis
     */
    private function analyzeTrend($historicalData)
    {
        $n = count($historicalData);
        
        if ($n < 2) {
            return [
                'income' => 'Data belum cukup',
                'expense' => 'Data belum cukup',
                'direction' => 'neutral'
            ];
        }
        
        $incomeChanges = [];
        $expenseChanges = [];
        
        for ($i = 1; $i < $n; $i++) {
            $incomeChanges[] = $historicalData[$i]['income'] - $historicalData[$i-1]['income'];
            $expenseChanges[] = $historicalData[$i]['expense'] - $historicalData[$i-1]['expense'];
        }
        
        $avgIncomeChange = array_sum($incomeChanges) / count($incomeChanges);
        $avgExpenseChange = array_sum($expenseChanges) / count($expenseChanges);
        
        $trend = [];
        
        if ($avgIncomeChange > 0) {
            $trend['income'] = "📈 Meningkat rata-rata Rp " . number_format($avgIncomeChange, 0, ',', '.') . " per bulan";
            $trend['income_direction'] = 'up';
        } elseif ($avgIncomeChange < 0) {
            $trend['income'] = "📉 Menurun rata-rata Rp " . number_format(abs($avgIncomeChange), 0, ',', '.') . " per bulan";
            $trend['income_direction'] = 'down';
        } else {
            $trend['income'] = "➡️ Stabil";
            $trend['income_direction'] = 'stable';
        }
        
        if ($avgExpenseChange > 0) {
            $trend['expense'] = "📈 Meningkat rata-rata Rp " . number_format($avgExpenseChange, 0, ',', '.') . " per bulan";
            $trend['expense_direction'] = 'up';
        } elseif ($avgExpenseChange < 0) {
            $trend['expense'] = "📉 Menurun rata-rata Rp " . number_format(abs($avgExpenseChange), 0, ',', '.') . " per bulan";
            $trend['expense_direction'] = 'down';
        } else {
            $trend['expense'] = "➡️ Stabil";
            $trend['expense_direction'] = 'stable';
        }
        
        if ($avgIncomeChange > 0 && $avgExpenseChange < 0) {
            $trend['direction'] = 'positive';
            $trend['direction_text'] = '✅ Tren POSITIF: Pemasukan naik, pengeluaran turun';
        } elseif ($avgIncomeChange < 0 && $avgExpenseChange > 0) {
            $trend['direction'] = 'negative';
            $trend['direction_text'] = 'Tren NEGATIF: Pemasukan turun, pengeluaran naik';
        } elseif ($avgIncomeChange > 0 && $avgExpenseChange > 0) {
            $trend['direction'] = 'mixed';
            $trend['direction_text'] = 'Tren CAMPURAN: Pemasukan dan pengeluaran sama-sama naik';
        } elseif ($avgIncomeChange < 0 && $avgExpenseChange < 0) {
            $trend['direction'] = 'mixed';
            $trend['direction_text'] = 'Tren CAMPURAN: Pemasukan dan pengeluaran sama-sama turun';
        } else {
            $trend['direction'] = 'stable';
            $trend['direction_text'] = '➡️ Tren STABIL: Tidak ada perubahan signifikan';
        }
        
        return $trend;
    }
    
    /**
     * Hitung volatilitas
     */
    private function calculateVolatility($data)
    {
        $n = count($data);
        if ($n < 2) return 0;
        
        $mean = array_sum($data) / $n;
        $variance = 0;
        
        foreach ($data as $value) {
            $variance += pow($value - $mean, 2);
        }
        
        $stdDev = sqrt($variance / $n);
        
        return $mean > 0 ? $stdDev / $mean : 0;
    }
    
    /**
     * ==================== ALERT MANAGEMENT ====================
     */
    
    public function createAlert($type, $severity, $title, $message, $sourceType = null, $sourceId = null, $data = null)
    {
        $existing = KasproAlert::where('user_id', $this->getUserId())
            ->where('type', $type)
            ->where('is_resolved', false)
            ->first();
        
        if ($existing) {
            return $existing;
        }
        
        return KasproAlert::create([
            'user_id' => $this->getUserId(),
            'type' => $type,
            'severity' => $severity,
            'title' => $title,
            'message' => $message,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'data' => $data,
            'is_read' => false,
            'is_resolved' => false,
        ]);
    }
    
    public function checkAndCreateAlerts()
    {
        $survival = $this->getDaysToSurvive();
        if ($survival['days_to_survive'] <= 15) {
            $this->createAlert(
                'low_cash',
                $survival['days_to_survive'] <= 7 ? 'emergency' : 'critical',
                'Saldo Kas Menipis',
                "Bisnis hanya bisa bertahan {$survival['days_to_survive']} hari tanpa pemasukan baru.",
                null, null, $survival
            );
        }
        
        $cashFlow = $this->getCashFlowSummary('monthly');
        if ($cashFlow['total_income'] > 0 && $cashFlow['total_expense'] > $cashFlow['total_income'] * 0.8) {
            $percentage = round(($cashFlow['total_expense'] / $cashFlow['total_income']) * 100);
            $this->createAlert(
                'high_expense',
                'warning',
                'Pengeluaran Tinggi',
                "Pengeluaran bulan ini mencapai {$percentage}% dari pemasukan. Perhatikan efisiensi!",
                null, null, $cashFlow
            );
        }
        
        $prediction = $this->predictCashFlow(3);
        if (!empty($prediction['cash_flow_gaps'])) {
            foreach ($prediction['cash_flow_gaps'] as $gap) {
                $this->createAlert(
                    'cash_flow_gap',
                    $gap['severity'] == 'HIGH' ? 'critical' : 'warning',
                    '📉 Potensi Kekurangan Kas',
                    "Bulan {$gap['month']} diprediksi kekurangan kas sebesar Rp " . number_format($gap['gap_amount'], 0, ',', '.'),
                    null, null, $gap
                );
            }
        }
    }
    
    public function getUnreadAlerts()
    {
        return KasproAlert::where('user_id', $this->getUserId())
            ->where('is_read', false)
            ->where('is_resolved', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    public function getAllAlerts()
    {
        return KasproAlert::where('user_id', $this->getUserId())
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }
    
    public function markAlertAsRead($alertId)
    {
        $alert = KasproAlert::where('user_id', $this->getUserId())
            ->where('id', $alertId)
            ->first();
        
        if ($alert) {
            $alert->update(['is_read' => true]);
            return true;
        }
        
        return false;
    }
    
    public function resolveAlert($alertId)
    {
        $alert = KasproAlert::where('user_id', $this->getUserId())
            ->where('id', $alertId)
            ->first();
        
        if ($alert) {
            $alert->update([
                'is_resolved' => true,
                'resolved_at' => Carbon::now()
            ]);
            return true;
        }
        
        return false;
    }
}