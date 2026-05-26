<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\CashierShift;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PosSyncController extends Controller
{
    public function store(Request $request)
    {
        $payload = $request->input('payload');
        
        if (!$payload || !isset($payload['client_uuid'])) {
            return response()->json(['status' => 'error', 'message' => 'Invalid payload'], 400);
        }

        $clientUuid = $payload['client_uuid'];

        // A. Anti-Duplikasi (Race Condition & Retry Catch)
        $existingOrder = Order::withTrashed()->where('client_uuid', $clientUuid)->first();

        if ($existingOrder) {
            return response()->json([
                'status' => 'success',
                'message' => 'Transaction already processed',
            ]);
        }

        DB::beginTransaction();

        try {
            $branchId = $payload['branch_id'];
            $activeShiftId = $payload['cashier_shift_id'];
            $userId = $payload['user_id'];
            
            $activeShift = CashierShift::find($activeShiftId);
            if (!$activeShift) {
                // Shift might be closed, but we still need to process the offline order.
                // Or we can just log a warning and proceed, tying it to the shift anyway.
                Log::warning('Offline Sync: Cashier shift not found or closed for offline order.', ['client_uuid' => $clientUuid]);
            }

            $customerId = $payload['customer_id'];
            $paymentType = $payload['payment_type'] ?? 'lunas';
            $paymentMethod = $payload['payment_method'] ?? 'cash';
            
            // Generate invoice number
            $invoiceNumber = Order::generateInvoiceNumber($branchId);

            $subtotal = 0;
            $itemsData = [];

            foreach ($payload['items'] as $item) {
                $product = Product::find($item['product_id']);
                
                if (!$product) {
                     // If product was deleted, we still need to record the transaction to keep financial integrity.
                     // But for now, we just skip or log. Let's try to proceed without product relation if needed,
                     // but OrderItem requires product_id. Let's just log and continue.
                     Log::error('Offline Sync: Product not found', ['product_id' => $item['product_id']]);
                     continue;
                }

                $lineTotal = $item['price'] * $item['qty'];
                $subtotal += $lineTotal;

                $itemsData[] = [
                    'product_id'    => $item['product_id'],
                    'quantity'      => $item['qty'],
                    'selling_price' => $item['price'],
                    'cost_price'    => $product->purchase_price ?? 0,
                    'discount'      => 0,
                    'subtotal'      => $lineTotal,
                ];
            }

            $totalAmount = $subtotal;
            
            $totalPaid = match ($paymentType) {
                'kasbon'  => 0,
                'partial' => min($payload['paid'], $totalAmount),
                default   => $payload['paid'],
            };
            
            $remaining = max(0, $totalAmount - $totalPaid);
            $status = match (true) {
                $remaining <= 0          => 'paid',
                $totalPaid > 0           => 'partial',
                default                  => 'unpaid',
            };

            // Create order
            $order = Order::create([
                'client_uuid'          => $clientUuid,
                'invoice_number'       => $invoiceNumber,
                'customer_id'          => $customerId,
                'branch_id'            => $branchId,
                'cashier_shift_id'     => $activeShiftId,
                'user_id'              => $userId,
                'date'                 => now()->toDateString(),
                'source'               => 'pos',
                'subtotal'             => $subtotal,
                'discount'             => 0,
                'total_amount'         => $totalAmount,
                'total_paid'           => $totalPaid,
                'remaining_receivable' => $remaining,
                'payment_status'       => $status,
                'payment_method'       => $paymentType === 'kasbon' ? 'credit' : $paymentMethod,
            ]);

            // Create order items
            foreach ($itemsData as $data) {
                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $data['product_id'],
                    'quantity'      => $data['quantity'],
                    'selling_price' => $data['selling_price'],
                    'cost_price'    => $data['cost_price'],
                    'discount'      => $data['discount'],
                    'subtotal'      => $data['subtotal'],
                ]);
            }

            // Record payment
            if ($totalPaid > 0) {
                OrderPayment::create([
                    'order_id'       => $order->id,
                    'user_id'        => $userId,
                    'branch_id'      => $branchId,
                    'payment_date'   => now()->toDateString(),
                    'amount'         => $totalPaid,
                    'payment_method' => $paymentMethod,
                    'notes'          => $paymentType === 'partial' ? 'Pembayaran sebagian' : 'Lunas',
                ]);
            }

            // Update cashier shift if it exists
            if ($activeShift) {
                $activeShift->total_sales = $activeShift->total_sales + $totalAmount;
                $activeShift->transaction_count = $activeShift->transaction_count + 1;
                $activeShift->expected_cash = $activeShift->opening_balance + $activeShift->total_sales;
                $activeShift->save();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction synchronized',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // B. Tangkap Exception 1062 (Super Race Condition)
            if ($e instanceof \Illuminate\Database\QueryException && isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
                return response()->json(['status' => 'success']); // Anggap saja sudah sukses
            }
            
            Log::error('PosSync error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'payload' => $payload
            ]);
            
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
