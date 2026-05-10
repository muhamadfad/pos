<?php

namespace App\Http\Controllers;

use App\Models\CashierShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashierShiftController extends Controller
{
    public function index()
    {
        // Get active shift if exists
        $activeShift = CashierShift::where('user_id', Auth::id())
            ->where('status', 'open')
            ->first();

        $expectedCash = $activeShift
            ? $activeShift->opening_balance + $activeShift->total_sales
            : 0;

        // Get transactions for active shift with proper item data
        $transactions = $activeShift
            ? $activeShift->orders()
                ->with([
                    'customer', 
                    'items' => function($query) {
                        $query->with('product');
                    }
                ])
                ->latest('date')
                ->get()
                // Di bagian map function, ubah return dari array [] menjadi object (object)
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
                                'product_name' => $item->product->name ?? 'Produk tidak ditemukan',
                                'quantity' => $item->quantity,
                                'selling_price' => $item->selling_price,
                                'price' => $item->selling_price,
                                'subtotal' => $item->subtotal,
                            ];
                        })->values()
                    ];
                })
            : collect();

        // Get last closed shift
        $lastClosedShift = CashierShift::where('user_id', Auth::id())
            ->where('status', 'closed')
            ->latest('closed_at')
            ->first();

        // Get total products
        $totalProducts = \App\Models\Product::count();

        return view('cashier.index', compact('activeShift', 'expectedCash', 'transactions', 'lastClosedShift', 'totalProducts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'opening_balance' => 'required|numeric|min:0',
        ]);

        // Check if there's already an open shift
        $existingShift = CashierShift::where('user_id', Auth::id())
            ->where('status', 'open')
            ->first();

        if ($existingShift) {
            return back()->withErrors(['error' => 'Anda masih memiliki shift yang belum ditutup']);
        }

        CashierShift::create([
            'user_id' => Auth::id(),
            'opened_at' => now(),
            'opening_balance' => $request->opening_balance,
            'status' => 'open',
        ]);

        return redirect()->route('pos.shift.index')->with('success', 'Kasir berhasil dibuka');
    }

    public function close(Request $request)
    {
        $request->validate([
            'actual_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $shift = CashierShift::where('user_id', Auth::id())
            ->where('status', 'open')
            ->firstOrFail();

        // Calculate expected cash
        $expectedCash = $shift->opening_balance + $shift->total_sales;

        $shift->update([
            'closed_at' => now(),
            'actual_cash' => $request->actual_cash,
            'expected_cash' => $expectedCash,
            'notes' => $request->notes,
            'status' => 'closed',
        ]);

        return redirect()->route('pos.shift.index')->with('success', 'Kasir berhasil ditutup');
    }
}