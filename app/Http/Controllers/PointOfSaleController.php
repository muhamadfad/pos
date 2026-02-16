<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\StockOut;
use App\Models\StockOutItem;
use App\Models\CashierShift;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PointOfSaleController extends Controller
{
    public function index()
    {
        // Check if there's an active shift
        $activeShift = CashierShift::where('user_id', Auth::id())
            ->where('status', 'open')
            ->latest()
            ->first();

        // If no active shift, redirect to shift page
        if (!$activeShift) {
            return redirect()->route('pos.shift.index');
        }

        $products = Product::with('category')
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'barcode' => $p->barcode,
                'name' => $p->name,
                'price' => $p->price,
                'stock' => $p->stock,
                'category' => $p->category?->name ?? '-',
                'category_id' => $p->category_id,
            ]);

        $categories = Category::orderBy('name')->get(['id', 'name']);

        return Inertia::render('point-of-sale/page', [
            'products' => $products,
            'categories' => $categories,
            'activeShift' => [
                'id' => $activeShift->id,
                'opened_at' => $activeShift->opened_at->format('Y-m-d H:i:s'),
                'opening_balance' => $activeShift->opening_balance,
                'total_sales' => $activeShift->total_sales,
                'transaction_count' => $activeShift->transaction_count,
            ],
        ])->rootView('inertia');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'paid' => 'required|numeric|min:0',
        ]);

        // Check if shift is open
        $activeShift = CashierShift::where('user_id', Auth::id())
            ->where('status', 'open')
            ->first();

        if (!$activeShift) {
            return back()->withErrors(['error' => 'Silakan buka kasir terlebih dahulu']);
        }

        DB::beginTransaction();

        try {
            // Get or create default "Cash/Walk-in" customer for POS transactions
            $cashCustomer = Customer::firstOrCreate(
                ['name' => 'Cash/Walk-in Customer'],
                [
                    'type' => 'perorangan',
                    'address' => '-',
                    'phone' => '-',
                ]
            );

            // Calculate total amount
            $totalAmount = 0;
            $stockOutItems = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->stock < $item['qty']) {
                    DB::rollBack();
                    return back()->withErrors([
                        'stock' => "Stok {$product->name} tidak cukup. Tersedia: {$product->stock}",
                    ]);
                }

                $subtotal = $item['price'] * $item['qty'];
                $totalAmount += $subtotal;

                $stockOutItems[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['qty'],
                    'selling_price' => $item['price'],
                    'subtotal' => $subtotal,
                ];
            }

            // Create stock out transaction
            $stockOut = StockOut::create([
                'date' => now()->toDateString(),
                'customer_id' => $cashCustomer->id,
                'cashier_shift_id' => $activeShift->id,
                'total_amount' => $totalAmount,
                'total_paid' => $request->paid,
                'remaining_receivable' => max(0, $totalAmount - $request->paid),
                'payment_status' => $request->paid >= $totalAmount ? 'LUNAS' : 'PIUTANG',
            ]);

            // Create stock out items and reduce stock
            foreach ($stockOutItems as $itemData) {
                StockOutItem::create([
                    'stock_out_id' => $stockOut->id,
                    ...$itemData,
                ]);

                Product::find($itemData['product_id'])->decrement('stock', $itemData['quantity']);
            }

            // Update shift statistics
            $activeShift->increment('total_sales', $totalAmount);
            $activeShift->increment('transaction_count');

            DB::commit();

            return back()->with('success', 'Transaksi berhasil! No. Transaksi: ' . $stockOut->id);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memproses transaksi: ' . $e->getMessage()]);
        }
    }
}
