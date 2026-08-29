<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchasePaymentController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with(['supplier', 'items.ingredient'])
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('admin.purchases.payments.index', compact('purchases'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'purchase_id'    => 'required|exists:purchases,id',
            'payment_date'   => 'required|date',
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'nullable|string|max:50',
            'reference'      => 'nullable|string|max:100',
            'notes'          => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $purchase = Purchase::lockForUpdate()->findOrFail($request->purchase_id);

            if ($request->amount > $purchase->remaining_debt) {
                throw new \Exception('Jumlah pembayaran melebihi sisa hutang.');
            }

            PurchasePayment::create([
                'purchase_id'    => $purchase->id,
                'payment_date'   => $request->payment_date,
                'amount'         => $request->amount,
                'payment_method' => $request->payment_method ?? 'cash',
                'reference'      => $request->reference,
                'notes'          => $request->notes,
            ]);

            $newTotalPaid    = $purchase->total_paid + $request->amount;
            $newRemaining    = $purchase->total_amount - $newTotalPaid;

            $purchase->update([
                'total_paid'      => $newTotalPaid,
                'remaining_debt'  => max(0, $newRemaining),
                'payment_status'  => $newRemaining <= 0 ? 'paid' : 'partial',
            ]);
        });

        return back()->with('success', 'Pembayaran hutang berhasil dicatat.');
    }

    public function pay(Request $request, Purchase $purchase)
    {
        $request->validate([
            'payment_date'   => 'required|date',
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'nullable|string|max:50',
            'reference'      => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($request, $purchase) {
            $purchase = Purchase::lockForUpdate()->findOrFail($purchase->id);

            if ($request->amount > $purchase->remaining_debt) {
                throw new \Exception('Jumlah pembayaran melebihi sisa hutang.');
            }

            PurchasePayment::create([
                'purchase_id'    => $purchase->id,
                'payment_date'   => $request->payment_date,
                'amount'         => $request->amount,
                'payment_method' => $request->payment_method ?? 'cash',
                'reference'      => $request->reference,
            ]);

            $newTotalPaid   = $purchase->total_paid + $request->amount;
            $newRemaining   = $purchase->total_amount - $newTotalPaid;

            $purchase->update([
                'total_paid'     => $newTotalPaid,
                'remaining_debt' => max(0, $newRemaining),
                'payment_status' => $newRemaining <= 0 ? 'paid' : 'partial',
            ]);
        });

        return back()->with('success', 'Pembayaran hutang berhasil disimpan.');
    }
}
