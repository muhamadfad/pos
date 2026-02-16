<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockEntry;
use App\Models\DebtPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebtPaymentController extends Controller
{
    public function index()
    {
        $transactions = StockEntry::with('supplier')
            ->where('payment_status', 'HUTANG')
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('admin.debt-payments.index', compact('transactions'));
    }

    public function show($id)
    {
        $transaction = StockEntry::with(['supplier', 'items.product', 'payments'])
            ->findOrFail($id);

        return view('admin.debt-payments.show', compact('transaction'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'stock_entry_id'  => 'required|exists:stock_entries,id',
            'payment_date'    => 'required|date',
            'amount'          => 'required|numeric|min:1',
            'payment_method'  => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($validated) {

            $transaction = StockEntry::lockForUpdate()
                ->findOrFail($validated['stock_entry_id']);

            $previousTotal = $transaction->payments()->sum('amount');
            $remainingDebt = $transaction->total_amount - $previousTotal;

            if ($validated['amount'] > $remainingDebt) {
                throw new \Exception('Payment amount exceeds remaining debt.');
            }

            DebtPayment::create($validated);

            $newTotalPaid = $previousTotal + $validated['amount'];
            $newRemaining = $transaction->total_amount - $newTotalPaid;

            $transaction->update([
                'total_paid' => $newTotalPaid,
                'remaining_debt' => $newRemaining,
                'payment_status' => $newRemaining <= 0 ? 'LUNAS' : 'HUTANG',
            ]);
        });

        return back()->with('success', 'Pembayaran hutang berhasil dicatat.');
    }

    public function payDebt(Request $request, $id)
    {
        $request->validate([
            'payment_date'   => 'required|date',
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($request, $id) {

            $transaction = StockEntry::findOrFail($id);
            $remaining = $transaction->total_amount - $transaction->total_paid;

            if ($request->amount > $remaining) {
                abort(400, 'Payment amount exceeds remaining debt.');
            }

            DebtPayment::create([
                'stock_entry_id' => $transaction->id,
                'payment_date'   => $request->payment_date,
                'amount'         => $request->amount,
                'payment_method' => $request->payment_method,
            ]);

            $transaction->increment('total_paid', $request->amount);

            if ($transaction->total_paid >= $transaction->total_amount) {
                $transaction->update([
                    'payment_status' => 'LUNAS'
                ]);
            }
        });

        return back()->with('success', 'Pembayaran hutang berhasil disimpan.');
    }
}
