<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderPaymentController extends Controller
{
    public function index()
    {
        $orders = Order::with('customer')
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('admin.orders.payments.index', compact('orders'));
    }

    public function receive(Request $request, Order $order)
    {
        $request->validate([
            'payment_date'   => 'required|date',
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'nullable|string|max:50',
            'reference'      => 'nullable|string|max:100',
            'notes'          => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $order) {
            $order = Order::lockForUpdate()->findOrFail($order->id);

            if ($order->payment_status === 'paid') {
                throw new \Exception('Order ini sudah lunas.');
            }

            if ($request->amount > $order->remaining_receivable) {
                throw new \Exception('Jumlah pembayaran melebihi sisa piutang.');
            }

            OrderPayment::create([
                'order_id'       => $order->id,
                'payment_date'   => $request->payment_date,
                'amount'         => $request->amount,
                'payment_method' => $request->payment_method ?? 'cash',
                'reference'      => $request->reference,
                'notes'          => $request->notes,
            ]);

            $newTotalPaid = $order->total_paid + $request->amount;
            $newRemaining = $order->total_amount - $newTotalPaid;

            $order->update([
                'total_paid'           => $newTotalPaid,
                'remaining_receivable' => max(0, $newRemaining),
                'payment_status'       => $newRemaining <= 0 ? 'paid' : 'partial',
            ]);
        });

        return back()->with('success', 'Pembayaran piutang berhasil dicatat.');
    }
}
