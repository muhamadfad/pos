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

class PointOfSaleController extends Controller
{
    public function index()
    {
        $activeShift = CashierShift::where('user_id', Auth::id())
            ->where('status', 'open')
            ->latest()
            ->first();

        if (!$activeShift) {
            return redirect()->route('pos.shift.index');
        }

        // HAPUS where('stock', '>', 0) karena kolom stock tidak ada
        $products = Product::with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn($p) => [
                'id'          => $p->id,
                'barcode'     => $p->barcode,
                'name'        => $p->name,
                'price'       => $p->selling_price,
                'stock'       => null, // Set null karena kolom stock tidak ada
                'category'    => $p->category?->name ?? '-',
                'category_id' => $p->category_id,
            ]);

        $categories = Category::orderBy('name')->get(['id', 'name']);

        $customers = Customer::orderBy('name')->get(['id', 'name', 'type', 'phone'])
            ->map(fn($c) => [
                'id'    => $c->id,
                'name'  => $c->name,
                'type'  => $c->type,
                'phone' => $c->phone,
            ]);

        // Data hutang per customer (hanya yang punya unpaid/partial order)
        $customerDebts = Customer::whereHas(
            'orders',
            fn($q) =>
            $q->whereIn('payment_status', ['unpaid', 'partial'])
        )
            ->with([
                'orders' => fn($q) =>
                $q->whereIn('payment_status', ['unpaid', 'partial'])
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
            'products'      => $products,
            'categories'    => $categories,
            'customers'     => $customers,
            'customerDebts' => $customerDebts,
            'activeShift'   => [
                'id'               => $activeShift->id,
                'opened_at'        => $activeShift->opened_at->format('Y-m-d H:i:s'),
                'opening_balance'  => $activeShift->opening_balance,
                'total_sales'      => $activeShift->total_sales,
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

        $activeShift = CashierShift::where('user_id', Auth::id())
            ->where('status', 'open')
            ->first();

        if (!$activeShift) {
            return back()->withErrors(['error' => 'Silakan buka shift kasir terlebih dahulu.']);
        }

        // Kasbon & partial wajib pilih customer
        $paymentType = $request->payment_type ?? 'lunas';
        if (in_array($paymentType, ['kasbon', 'partial']) && !$request->customer_id) {
            return back()->withErrors(['customer_id' => 'Kasbon / Pembayaran sebagian wajib memilih customer.']);
        }

        DB::transaction(function () use ($request, $activeShift) {
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

                // HAPUS pengecekan stok karena kolom stock tidak ada
                // if ($product->stock < $item['qty']) {
                //     throw new \Exception("Stok {$product->name} tidak cukup (tersedia: {$product->stock})");
                // }

                $lineTotal = $item['price'] * $item['qty'];
                $subtotal += $lineTotal;

                $itemsData[] = [
                    'product_id'    => $item['product_id'],
                    'quantity'      => $item['qty'],
                    'selling_price' => $item['price'],
                    'discount'      => 0,
                    'subtotal'      => $lineTotal,
                    'product'       => $product,
                ];
            }

            $totalAmount = $subtotal;
            $paymentType = $request->payment_type ?? 'lunas';
            $paymentMethod = $request->payment_method ?? 'cash';

            // Kasbon: tidak bayar sama sekali
            // Partial: bayar sebagian
            // Lunas: bayar penuh (atau lebih)
            $totalPaid = match ($paymentType) {
                'kasbon'  => 0,
                'partial' => min($request->paid, $totalAmount),
                default   => $request->paid, // lunas
            };
            $remaining = max(0, $totalAmount - $totalPaid);
            $status = match (true) {
                $remaining <= 0          => 'paid',
                $totalPaid > 0           => 'partial',
                default                  => 'unpaid',
            };

            $order = Order::create([
                'customer_id'          => $customerId,
                'branch_id'            => auth()->user()->branch_id,
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
                'payment_method'       => $paymentType === 'kasbon' ? 'credit' : $paymentMethod,
            ]);

            foreach ($itemsData as $data) {
                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $data['product_id'],
                    'quantity'      => $data['quantity'],
                    'selling_price' => $data['selling_price'],
                    'cost_price'    => $data['product']->purchase_price,
                    'discount'      => $data['discount'],
                    'subtotal'      => $data['subtotal'],
                ]);

                // HAPUS pengurangan stok karena kolom stock tidak ada
                // $data['product']->decrement('stock', $data['quantity']);
            }

            $activeShift->increment('total_sales', $totalAmount);
            $activeShift->increment('transaction_count');
        });

        $msg = match ($paymentType) {
            'kasbon'  => 'Transaksi berhasil dicatat sebagai kasbon!',
            'partial' => 'Transaksi berhasil! Sisa tagihan dicatat sebagai kasbon.',
            default   => 'Transaksi berhasil!',
        };

        return back()->with('success', $msg);
    }

    /**
     * GET /pos/customer-debt/{customer}
     * Kembalikan data hutang customer untuk DebtPaymentPanel (JSON)
     */
    public function customerDebt(Customer $customer)
    {
        $orders = Order::where('customer_id', $customer->id)
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->orderBy('date', 'asc')   // FIFO: terlama dulu
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

    /**
     * POST /pos/pay-debt
     * Proses pembayaran kasbon dengan alokasi FIFO
     */
    public function payDebt(Request $request)
    {
        $request->validate([
            'customer_id'    => 'required|exists:customers,id',
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|in:cash,transfer',
        ]);

        $customer = Customer::findOrFail($request->customer_id);

        // Ambil hutang terlama dulu (FIFO)
        $orders = Order::where('customer_id', $customer->id)
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $totalDebt = $orders->sum('remaining_receivable');

        if ($request->amount > $totalDebt) {
            return back()->withErrors(['amount' => 'Jumlah pembayaran melebihi total hutang.']);
        }

        DB::transaction(function () use ($request, $orders) {
            $remaining = $request->amount;

            foreach ($orders as $order) {
                if ($remaining <= 0) break;

                $toApply = min($remaining, $order->remaining_receivable);

                // Catat di order_payments
                OrderPayment::create([
                    'order_id'       => $order->id,
                    'payment_date'   => now()->toDateString(),
                    'amount'         => $toApply,
                    'payment_method' => $request->payment_method,
                    'notes'          => 'Bayar kasbon via POS',
                ]);

                // Update order
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
        });

        return back()->with('debt_success', 'Pembayaran kasbon berhasil diproses.');
    }
}