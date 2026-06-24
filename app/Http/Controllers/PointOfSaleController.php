<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\CashierShift;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PointOfSaleController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $branchId = $user->branch_id;
        
        $activeShift = CashierShift::where('user_id', Auth::id())
            ->where('branch_id', $branchId)  // Tambahkan filter branch
            ->where('status', 'open')
            ->latest()
            ->first();

        if (!$activeShift) {
            return redirect()->route('pos.shift.index');
        }

        $products = Product::with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn($p) => [
                'id'          => $p->id,
                'barcode'     => $p->barcode,
                'name'        => $p->name,
                'price'       => $p->selling_price,
                'stock'       => null,
                'category'    => $p->category?->name ?? '-',
                'category_id' => $p->category_id,
                'image'       => $p->image ? asset('pos-images/' . basename($p->image)) : null,
            ]);

        $categories = Category::orderBy('name')->get(['id', 'name']);

        $customers = Customer::orderBy('name')->get(['id', 'name', 'type', 'phone'])
            ->map(fn($c) => [
                'id'    => $c->id,
                'name'  => $c->name,
                'type'  => $c->type,
                'phone' => $c->phone,
            ]);

        $customerDebts = Customer::whereHas(
            'orders',
            fn($q) =>
            $q->whereIn('payment_status', ['unpaid', 'partial'])
                ->where('branch_id', $branchId)  // Filter branch
        )
            ->with([
                'orders' => fn($q) =>
                $q->whereIn('payment_status', ['unpaid', 'partial'])
                    ->where('branch_id', $branchId)  // Filter branch
                    ->orderBy('date', 'asc')
                    ->orderBy('id', 'asc')
                    ->select('id', 'customer_id', 'invoice_number', 'date', 'total_amount', 'total_paid', 'remaining_receivable', 'payment_status')
            ])
            ->orderBy('name')
            ->get(['id', 'name', 'phone'])
            ->map(fn($c) => [
                'id'         => $c->id,
                'name'       => $c->name,
                'phone'      => $c->phone,
                'total_debt' => $c->orders->sum('remaining_receivable'),
                'orders'     => $c->orders->values(),
            ]);

        return Inertia::render('point-of-sale/page', [
            'authUser'      => [
                'id'        => $user->id,
                'branch_id' => $branchId,
            ],
            'products'      => $products,
            'categories'    => $categories,
            'customers'     => $customers,
            'customerDebts' => $customerDebts,
            'activeShift'   => [
                'id'               => $activeShift->id,
                'opened_at'        => $activeShift->opened_at->format('Y-m-d H:i:s'),
                'opening_balance'  => $activeShift->opening_balance,
                'total_sales'      => (float) $activeShift->total_sales,
                'transaction_count' => $activeShift->transaction_count,
            ],
        ])->rootView('inertia');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'required|exists:products,id',
            'items.*.qty'            => 'required|integer|min:1',
            'items.*.price'          => 'required|numeric|min:0',
            'paid'                   => 'required|numeric|min:0',
            'customer_id'            => 'nullable|exists:customers,id',
            'payment_method'         => 'nullable|in:cash,transfer,credit',
            'payment_type'           => 'nullable|in:lunas,partial,kasbon',
        ]);

        $user = auth()->user();
        $branchId = $user->branch_id;
        
        $activeShift = CashierShift::where('user_id', Auth::id())
            ->where('branch_id', $branchId)
            ->where('status', 'open')
            ->first();

        if (!$activeShift) {
            return back()->withErrors(['error' => 'Silakan buka shift kasir terlebih dahulu.']);
        }

        $paymentType = $request->payment_type ?? 'lunas';
        if (in_array($paymentType, ['kasbon', 'partial']) && !$request->customer_id) {
            return back()->withErrors(['customer_id' => 'Kasbon / Pembayaran sebagian wajib memilih customer.']);
        }

        DB::beginTransaction();
        
        try {
            // Use selected customer or default walk-in
            $customerId = $request->customer_id;
            if (!$customerId) {
                $walkIn = Customer::firstOrCreate(
                    ['name' => 'Walk-in Customer'],
                    ['type' => 'retail', 'phone' => null, 'address' => null]
                );
                $customerId = $walkIn->id;
            }

            $subtotal = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                $lineTotal = $item['price'] * $item['qty'];
                $subtotal += $lineTotal;

                $itemsData[] = [
                    'product_id'    => $item['product_id'],
                    'quantity'      => $item['qty'],
                    'selling_price' => $item['price'],
                    'cost_price'    => $product->purchase_price ?? 0,
                    'discount'      => 0,
                    'subtotal'      => $lineTotal,
                    'product'       => $product,
                ];
            }

            $totalAmount = $subtotal;
            
            // Calculate payment
            $totalPaid = match ($paymentType) {
                'kasbon'  => 0,
                'partial' => min($request->paid, $totalAmount),
                default   => $request->paid,
            };
            $remaining = max(0, $totalAmount - $totalPaid);
            $status = match (true) {
                $remaining <= 0          => 'paid',
                $totalPaid > 0           => 'partial',
                default                  => 'unpaid',
            };

            // Generate invoice number
            $invoiceNumber = $this->generateInvoiceNumber($branchId);

            // Create order
            $order = Order::create([
                'invoice_number'       => $invoiceNumber,
                'customer_id'          => $customerId,
                'branch_id'            => $branchId,
                'cashier_shift_id'     => $activeShift->id,
                'user_id'              => Auth::id(),
                'date'                 => now()->toDateString(),
                'source'               => 'pos',
                'subtotal'             => $subtotal,
                'discount'             => 0,
                'total_amount'         => $totalAmount,
                'total_paid'           => $totalPaid,
                'remaining_receivable' => $remaining,
                'payment_status'       => $status,
                'payment_method'       => $paymentType === 'kasbon' ? 'credit' : ($request->payment_method ?? 'cash'),
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

            // If payment is made (not kasbon), record payment
            if ($totalPaid > 0) {
                OrderPayment::create([
                    'order_id'       => $order->id,
                    'user_id'        => Auth::id(),
                    'branch_id'      => $branchId,
                    'payment_date'   => now()->toDateString(),
                    'amount'         => $totalPaid,
                    'payment_method' => $request->payment_method ?? 'cash',
                    'notes'          => $paymentType === 'partial' ? 'Pembayaran sebagian' : 'Lunas',
                ]);
            }

            // Update cashier shift - PERBAIKAN DI SINI
            // Gunakan method increment atau update langsung dengan nilai numerik
            $activeShift->total_sales = $activeShift->total_sales + $totalAmount;
            $activeShift->transaction_count = $activeShift->transaction_count + 1;
            $activeShift->expected_cash = $activeShift->opening_balance + $activeShift->total_sales;
            $activeShift->save();
            
            // Refresh model untuk memastikan nilai terupdate
            $activeShift->refresh();

            DB::commit();

            $msg = match ($paymentType) {
                'kasbon'  => 'Transaksi berhasil dicatat sebagai kasbon!',
                'partial' => 'Transaksi berhasil! Sisa tagihan dicatat sebagai kasbon.',
                default   => 'Transaksi berhasil!',
            };

            return back()->with('success', $msg);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    private function generateInvoiceNumber($branchId)
    {
        $branchCode = sprintf('%03d', $branchId);
        $date = now()->format('Ymd');
        $lastOrder = Order::where('branch_id', $branchId)
            ->whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastOrder ? (intval(substr($lastOrder->invoice_number, -4)) + 1) : 1;
        
        return sprintf('INV/%s/%s/%04d', $branchCode, $date, $sequence);
    }

    public function customerDebt(Customer $customer)
    {
        $user = auth()->user();
        $branchId = $user->branch_id;
        
        $orders = Order::where('customer_id', $customer->id)
            ->where('branch_id', $branchId)  // Filter branch
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get(['id', 'invoice_number', 'date', 'total_amount', 'total_paid', 'remaining_receivable', 'payment_status']);

        return response()->json([
            'id'         => $customer->id,
            'name'       => $customer->name,
            'phone'      => $customer->phone,
            'total_debt' => $orders->sum('remaining_receivable'),
            'orders'     => $orders->values(),
        ]);
    }

    public function payDebt(Request $request)
    {
        $request->validate([
            'customer_id'    => 'required|exists:customers,id',
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|in:cash,transfer',
        ]);

        $user = auth()->user();
        $branchId = $user->branch_id;

        $customer = Customer::findOrFail($request->customer_id);

        $orders = Order::where('customer_id', $customer->id)
            ->where('branch_id', $branchId)  // Filter branch
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $totalDebt = $orders->sum('remaining_receivable');

        if ($request->amount > $totalDebt) {
            return back()->withErrors(['amount' => 'Jumlah pembayaran melebihi total hutang.']);
        }

        DB::beginTransaction();
        
        try {
            $remaining = $request->amount;

            foreach ($orders as $order) {
                if ($remaining <= 0) break;

                $toApply = min($remaining, $order->remaining_receivable);

                OrderPayment::create([
                    'order_id'       => $order->id,
                    'user_id'        => Auth::id(),
                    'branch_id'      => $branchId,
                    'payment_date'   => now()->toDateString(),
                    'amount'         => $toApply,
                    'payment_method' => $request->payment_method,
                    'notes'          => 'Bayar kasbon via POS',
                ]);

                $newTotalPaid  = $order->total_paid + $toApply;
                $newRemaining  = $order->remaining_receivable - $toApply;
                $newStatus     = $newRemaining <= 0 ? 'paid' : 'partial';

                $order->update([
                    'total_paid'           => $newTotalPaid,
                    'remaining_receivable' => $newRemaining,
                    'payment_status'       => $newStatus,
                ]);

                $remaining -= $toApply;
            }

            DB::commit();
            return back()->with('debt_success', 'Pembayaran kasbon berhasil diproses.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Pay debt error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function printReceipt($order)
    {
        $order = Order::with(['customer', 'items.product', 'cashierShift'])
            ->where('branch_id', auth()->user()->branch_id)
            ->findOrFail($order);
            
        return view('pos.receipt', compact('order'));
    }
}