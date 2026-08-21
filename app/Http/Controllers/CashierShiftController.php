<?php

namespace App\Http\Controllers;

use App\Models\CashierShift;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashierShiftController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        $branchId = $user->branch_id;
        
        // Get active shift for current user and branch
        $activeShift = CashierShift::where('user_id', Auth::id())
            ->where('branch_id', $branchId)
            ->where('status', 'open')
            ->first();

        $expectedCash = 0;
        if ($activeShift) {
            $totalCashIncome = \App\Models\Order::where('cashier_shift_id', $activeShift->id)
                ->where('payment_method', 'cash')
                ->selectRaw('SUM(LEAST(total_amount, total_paid)) as val')->value('val') ?? 0;
            $totalExpense = \App\Models\Expense::where('user_id', Auth::id())
                ->where('branch_id', $branchId)
                ->where('created_at', '>=', $activeShift->opened_at)
                ->sum('amount');
            $totalCash = $totalCashIncome - $totalExpense;
            $expectedCash = $activeShift->opening_balance + $totalCash;
        }

        // Get transactions for active shift with proper item data
        $transactions = $activeShift
            ? $activeShift->orders()
                ->with([
                    'customer', 
                    'items' => function($query) {
                        $query->with('product');
                    }
                ])
                ->where('branch_id', $branchId) // Filter transaksi berdasarkan cabang
                ->latest('date')
                ->get()
                ->map(function($order) {
                    return (object) [
                        'id' => $order->id,
                        'created_at' => $order->created_at,
                        'date' => $order->date,
                        'customer' => $order->customer,
                        'payment_status' => $order->payment_status,
                        'total_amount' => $order->total_amount,
                        'total_paid' => $order->total_paid,
                        'remaining_receivable' => $order->remaining_receivable,
                        'items' => $order->items->map(function($item) {
                            return (object) [
                                'product_name' => $item->custom_name ?: ($item->product->name ?? 'Produk tidak ditemukan'),
                                'quantity' => $item->quantity,
                                'selling_price' => $item->selling_price,
                                'price' => $item->selling_price,
                                'subtotal' => $item->subtotal,
                            ];
                        })->values()
                    ];
                })
            : collect();

        // Get last closed shift for current user and branch
        $lastClosedShift = CashierShift::where('user_id', Auth::id())
            ->where('branch_id', $branchId)
            ->where('status', 'closed')
            ->latest('closed_at')
            ->first();

        // Get total products
        $totalProducts = \App\Models\Product::count();
        
        // Get branch info
        $branch = Branch::find($branchId);
        $branchName = $branch ? $branch->name : 'Tidak ada cabang';

        // Get all branches for super admin (for monitoring)
        $branches = [];
        if ($isSuperAdmin) {
            $branches = Branch::where('is_active', true)->get();
        }

        return view('cashier.index', compact(
            'activeShift', 
            'expectedCash', 
            'transactions', 
            'lastClosedShift', 
            'totalProducts',
            'branchName',
            'branchId',
            'isSuperAdmin',
            'branches'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'opening_balance' => 'required|numeric|min:0',
            'branch_id' => 'required_if:role,super_admin|exists:branches,id', // Validasi untuk super admin
            'cashier_name' => 'required|string|max:255',
            'shift_time' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        $branchId = $user->branch_id;
        
        // Jika super admin bisa memilih cabang
        if ($user->role === 'super_admin' && $request->has('branch_id')) {
            $branchId = $request->branch_id;
        }

        // Check if there's already an open shift for this user and branch
        $existingShift = CashierShift::where('user_id', Auth::id())
            ->where('branch_id', $branchId)
            ->where('status', 'open')
            ->first();

        if ($existingShift) {
            return back()->withErrors(['error' => 'Anda masih memiliki shift yang belum ditutup di cabang ini']);
        }

        // Check if there's any open shift in this branch from other user (optional, prevent multiple shifts same branch)
        // Dihapus sesuai permintaan user: "saya mau bisa beberapa kasir tapi tentunya beda laci per kasir lah"
        /*
        $branchOpenShift = CashierShift::where('branch_id', $branchId)
            ->where('status', 'open')
            ->exists();
            
        if ($branchOpenShift && $user->role !== 'super_admin') {
            return back()->withErrors(['error' => 'Masih ada shift yang sedang berjalan di cabang ini dari kasir lain']);
        }
        */

        CashierShift::create([
            'user_id' => Auth::id(),
            'cashier_name' => $request->cashier_name,
            'shift_time' => $request->shift_time,
            'branch_id' => $branchId,
            'opened_at' => now(),
            'opening_balance' => $request->opening_balance,
            'status' => 'open',
            'total_sales' => 0,
            'expected_cash' => 0,
            'actual_cash' => 0,
            'cash_difference' => 0
        ]);

        return redirect()->route('pos.shift.index')->with('success', 'Shift kasir berhasil dibuka');
    }

    public function update(Request $request)
    {
        $request->validate([
            'cashier_name' => 'required|string|max:255',
            'shift_time' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        $branchId = $user->branch_id;
        if ($user->role === 'super_admin' && $request->has('branch_id')) {
            $branchId = $request->branch_id;
        }

        $activeShift = CashierShift::where('user_id', Auth::id())
            ->where('branch_id', $branchId)
            ->where('status', 'open')
            ->first();

        if (!$activeShift) {
            return back()->withErrors(['error' => 'Tidak ada shift aktif yang bisa diubah']);
        }

        $activeShift->update([
            'cashier_name' => $request->cashier_name,
            'shift_time' => $request->shift_time,
        ]);

        return back()->with('success', 'Informasi shift berhasil diperbarui');
    }

    public function close(Request $request)
    {
        $request->validate([
            'actual_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $user = auth()->user();
        $branchId = $user->branch_id;
        
        // Jika super admin bisa menentukan branch
        if ($user->role === 'super_admin' && $request->has('branch_id')) {
            $branchId = $request->branch_id;
        }

        $shift = CashierShift::where('user_id', Auth::id())
            ->where('branch_id', $branchId)
            ->where('status', 'open')
            ->firstOrFail();

        // Calculate expected cash (only cash payments)
        $totalCashIncome = \App\Models\Order::where('cashier_shift_id', $shift->id)
            ->where('payment_method', 'cash')
            ->selectRaw('SUM(LEAST(total_amount, total_paid)) as val')->value('val') ?? 0;
            
        $totalExpense = \App\Models\Expense::where('user_id', Auth::id())
            ->where('branch_id', $branchId)
            ->where('created_at', '>=', $shift->opened_at)
            ->sum('amount');
            
        $totalCash = $totalCashIncome - $totalExpense;
        $expectedCash = $shift->opening_balance + $totalCash;

        $shift->update([
            'closed_at' => now(),
            'actual_cash' => $request->actual_cash,
            'expected_cash' => $expectedCash,
            'cash_difference' => $request->actual_cash - $expectedCash,
            'notes' => $request->notes,
            'status' => 'closed',
        ]);

        $branch = Branch::find($branchId);
        $branchName = $branch ? $branch->name : 'Cabang';

        return redirect()->route('pos.shift.index')
            ->with('success', "Kasir {$branchName} berhasil ditutup");
    }
    
    /**
     * Get shift report for specific branch (Super Admin only)
     */
    public function report(Request $request)
    {
        $user = auth()->user();
        
        if ($user->role !== 'super_admin') {
            abort(403, 'Akses ditolak');
        }
        
        $branchId = $request->get('branch_id');
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        
        $query = CashierShift::with('user', 'branch')
            ->whereBetween('opened_at', [$startDate, $endDate . ' 23:59:59']);
        
        if ($branchId && $branchId !== 'all') {
            $query->where('branch_id', $branchId);
        }
        
                $shifts = $query->orderBy('opened_at', 'desc')->get()->map(function($shift) {
            // Hitung Pengeluaran
            $shift->total_expense = \App\Models\Expense::where('user_id', $shift->user_id)
                ->where('branch_id', $shift->branch_id)
                ->where('created_at', '>=', $shift->opened_at)
                ->where(function($q) use ($shift) {
                    if ($shift->closed_at) {
                        $q->where('created_at', '<=', $shift->closed_at);
                    }
                })
                ->sum('amount');
                
            // Hitung Uang Tunai Masuk murni (total_sales - total_transfer)
            $totalTransfer = \App\Models\Order::where('cashier_shift_id', $shift->id)
                ->where('payment_method', 'transfer')
                ->selectRaw('SUM(LEAST(total_amount, total_paid)) as val')->value('val') ?? 0;
                
            $shift->pure_cash_income = $shift->total_sales - $totalTransfer;
            $shift->total_transfer = $totalTransfer;
            
            return $shift;
        });
        $branches = Branch::where('is_active', true)->get();
        
        $summary = [
            'total_opening' => $shifts->sum('opening_balance'),
            'total_sales' => $shifts->sum('total_sales'),
            'total_expected' => $shifts->sum('expected_cash'),
            'total_actual' => $shifts->sum('actual_cash'),
            'total_difference' => $shifts->sum('cash_difference'),
            'total_shifts' => $shifts->count(),
        ];
        
        return view('cashier.report', compact('shifts', 'branches', 'summary', 'branchId', 'startDate', 'endDate'));
    }
}