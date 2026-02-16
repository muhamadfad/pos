<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\StockOut;
use App\Models\StockOutItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockOutController extends Controller
{
    public function index()
    {
        $stockOuts = StockOut::with('customer', 'items.product')
            ->latest('date')
            ->paginate(20);

        return view('admin.transactions.out.index', compact('stockOuts'));
    }

    public function selectCustomer()
    {
        $customers = Customer::orderBy('name')->get();
        return view('admin.transactions.out.customer', compact('customers'));
    }

    public function perCustomer(Customer $customer)
    {
        $transactions = StockOut::where('customer_id', $customer->id)
            ->with('items.product')
            ->latest('date')
            ->paginate(15);

        return view('admin.transactions.out.transaksi', compact('customer', 'transactions'));
    }

    public function create(Customer $customer)
    {
        $products = Product::where('stock', '>', 0)->orderBy('name')->get();
        return view('admin.transactions.out.create', compact('customer', 'products'));
    }

    public function store(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'paid' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.selling_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = 0;

            // Calculate total
            foreach ($validated['items'] as $item) {
                $subtotal = $item['quantity'] * $item['selling_price'];
                $totalAmount += $subtotal;
            }

            $totalPaid = $validated['paid'];
            $remainingReceivable = $totalAmount - $totalPaid;
            $paymentStatus = $remainingReceivable <= 0 ? 'LUNAS' : 'PIUTANG';

            // Create stock out
            $stockOut = StockOut::create([
                'date' => $validated['date'],
                'customer_id' => $customer->id,
                'total_amount' => $totalAmount,
                'total_paid' => $totalPaid,
                'remaining_receivable' => $remainingReceivable,
                'payment_status' => $paymentStatus,
            ]);

            // Create items and reduce stock
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok {$product->name} tidak mencukupi");
                }

                $subtotal = $item['quantity'] * $item['selling_price'];

                StockOutItem::create([
                    'stock_out_id' => $stockOut->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'selling_price' => $item['selling_price'],
                    'subtotal' => $subtotal,
                ]);

                // Reduce stock
                $product->decrement('stock', $item['quantity']);
            }

            DB::commit();

            return redirect()
                ->route('admin.transactions.out.perCustomer', $customer)
                ->with('success', 'Transaksi barang keluar berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $transaction = StockOut::with(['customer', 'items.product', 'payments'])
            ->findOrFail($id);

        return view('admin.transactions.out.show', compact('transaction'));
    }

    public function download(Customer $customer)
    {
        $transactions = StockOut::where('customer_id', $customer->id)
            ->with('items.product')
            ->latest('date')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.transactions.out.pdf', [
            'customer' => $customer,
            'transactions' => $transactions,
        ]);

        return $pdf->download("barang-keluar-{$customer->name}.pdf");
    }
}
