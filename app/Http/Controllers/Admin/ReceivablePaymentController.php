<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockOut;
use App\Models\ReceivablePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceivablePaymentController extends Controller
{
    public function index()
    {
        $payments = ReceivablePayment::with('stockOut.customer')
            ->latest('payment_date')
            ->paginate(20);

        return view('admin.receivable-payments.index', compact('payments'));
    }

    public function receivePayment(Request $request, $id)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $stockOut = StockOut::findOrFail($id);

            if ($stockOut->payment_status === 'LUNAS') {
                throw new \Exception('Transaksi sudah lunas');
            }

            $amount = $validated['amount'];

            if ($amount > $stockOut->remaining_receivable) {
                throw new \Exception('Jumlah pembayaran melebihi sisa piutang');
            }

            // Create payment record
            ReceivablePayment::create([
                'stock_out_id' => $stockOut->id,
                'payment_date' => $validated['payment_date'],
                'amount' => $amount,
                'payment_method' => $validated['payment_method'] ?? 'cash',
            ]);

            // Update stock out
            $newTotalPaid = $stockOut->total_paid + $amount;
            $newRemainingReceivable = $stockOut->total_amount - $newTotalPaid;
            $newPaymentStatus = $newRemainingReceivable <= 0 ? 'LUNAS' : 'PIUTANG';

            $stockOut->update([
                'total_paid' => $newTotalPaid,
                'remaining_receivable' => $newRemainingReceivable,
                'payment_status' => $newPaymentStatus,
            ]);

            DB::commit();

            return back()->with('success', 'Pembayaran piutang berhasil dicatat');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
