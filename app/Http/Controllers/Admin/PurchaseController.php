<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with(['supplier', 'items.product'])
            ->latest('date')
            ->paginate(15);

        $stats = [
            'total' => Purchase::count(),
            'paid'  => Purchase::where('payment_status', 'paid')->count(),
            'debt'  => Purchase::whereIn('payment_status', ['unpaid', 'partial'])->count(),
        ];

        return view('admin.purchases.index', compact('purchases', 'stats'));
    }

    public function selectSupplier()
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('admin.purchases.select-supplier', compact('suppliers'));
    }

    public function perSupplier(Request $request, Supplier $supplier)
    {
        $query = Purchase::where('supplier_id', $supplier->id)
            ->with('items.product')
            ->orderBy('date', 'desc');

        if ($request->filled('search')) {
            $query->whereHas(
                'items.product',
                fn($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
            );
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay(),
            ]);
        } elseif ($request->filter === 'today') {
            $query->whereDate('date', Carbon::today());
        } elseif ($request->filter === 'month') {
            $query->whereMonth('date', Carbon::now()->month)
                ->whereYear('date', Carbon::now()->year);
        }

        $purchases = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('admin.purchases.partials.table', compact('purchases'))->render();
        }

        $total = Purchase::where('supplier_id', $supplier->id)->count();

        return view('admin.purchases.per-supplier', compact('supplier', 'purchases', 'total'));
    }

    public function create(Supplier $supplier)
    {
        $products = Product::with('unit')->orderBy('name')->get();
        return view('admin.purchases.create', compact('supplier', 'products'));
    }

    public function store(Request $request, Supplier $supplier)
    {
        $request->validate([
            'date'                    => 'required|date',
            'paid'                    => 'nullable|numeric|min:0',
            'payment_method'          => 'nullable|string',
            'reference_number'        => 'nullable|string|max:100',
            'notes'                   => 'nullable|string',
            'items'                   => 'required|array|min:1',
            'items.*.product_id'      => 'required|exists:products,id',
            'items.*.quantity'        => 'required|integer|min:1',
            'items.*.purchase_price'  => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $supplier) {
            $total = collect($request->items)->sum(fn($i) => $i['quantity'] * $i['purchase_price']);
            $paid  = $request->paid ?? 0;

            $purchase = Purchase::create([
                'reference_number' => $request->reference_number,
                'supplier_id'      => $supplier->id,
                'date'             => $request->date,
                'total_amount'     => $total,
                'total_paid'       => $paid,
                'remaining_debt'   => $total - $paid,
                'payment_status'   => $paid >= $total ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'),
                'payment_method'   => $request->payment_method ?? 'cash',
                'notes'            => $request->notes,
            ]);

            foreach ($request->items as $item) {
                $subtotal = $item['quantity'] * $item['purchase_price'];

                PurchaseItem::create([
                    'purchase_id'    => $purchase->id,
                    'product_id'     => $item['product_id'],
                    'quantity'       => $item['quantity'],
                    'purchase_price' => $item['purchase_price'],
                    'subtotal'       => $subtotal,
                ]);

                Product::find($item['product_id'])->increment('stock', $item['quantity']);
            }
        });

        return redirect()->route('admin.purchases.per-supplier', $supplier)
            ->with('success', 'Pembelian berhasil disimpan!');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.product', 'payments']);
        return view('admin.purchases.show', compact('purchase'));
    }

    public function downloadPdf(Request $request, Supplier $supplier)
    {
        $query = Purchase::where('supplier_id', $supplier->id)
            ->with('items.product')
            ->orderBy('date', 'desc');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay(),
            ]);
        }

        $purchases = $query->get();

        $pdf = Pdf::loadView('admin.purchases.pdf', compact('supplier', 'purchases'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Purchases_' . $supplier->name . '.pdf');
    }
}
