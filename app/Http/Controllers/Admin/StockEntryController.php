<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\StockEntry;
use App\Models\StockEntryItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class StockEntryController extends Controller
{
    // 1. Index all stock entries
    public function index()
    {
        $stockEntries = StockEntry::with(['supplier', 'items.product'])
            ->latest()
            ->paginate(15);

        return view('admin.transactions.in.index', compact('stockEntries'));
    }

    // 2. Select supplier
    public function selectSupplier()
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('admin.transactions.in.supplier', compact('suppliers'));
    }

    // 3. Transactions per supplier
    public function transactionsPerSupplier(Request $request, $supplierId)
    {
        $supplier = Supplier::findOrFail($supplierId);

        $query = StockEntry::where('supplier_id', $supplierId)
            ->with('items.product')
            ->orderBy('date', 'desc');

        // Search
        if ($request->filled('search')) {
            $query->whereHas('items.product', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // Date range (priority)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        } else {
            // Quick filter
            if ($request->filter === 'today') {
                $query->whereDate('date', Carbon::today());
            }

            if ($request->filter === 'month') {
                $query->whereMonth('date', Carbon::now()->month)
                    ->whereYear('date', Carbon::now()->year);
            }
        }

        $transactions = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view(
                'admin.transactions.in.partials.table',
                compact('transactions')
            )->render();
        }

        $total = StockEntry::where('supplier_id', $supplierId)->count();

        return view(
            'admin.transactions.in.transaksi',
            compact('supplier', 'transactions', 'total')
        );
    }

    // 4. Create form
    public function create($supplierId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        $products = Product::orderBy('name')->get();

        return view('admin.transactions.in.create', compact('supplier', 'products'));
    }

    // 5. Store transaction
    public function store(Request $request, $supplierId)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.purchase_price' => 'required|numeric|min:0',
            'date' => 'required|date',
            'paid' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $supplierId) {

            $total = collect($validated['items'])->sum(function ($item) {
                return $item['quantity'] * $item['purchase_price'];
            });

            $paid = $validated['paid'] ?? 0;
            $status = $paid >= $total ? 'LUNAS' : 'HUTANG';

            $stockEntry = StockEntry::create([
                'supplier_id' => $supplierId,
                'date' => $validated['date'],
                'total_amount' => $total,
                'total_paid' => $paid,
                'remaining_debt' => $total - $paid,
                'payment_status' => $status,
            ]);

            foreach ($validated['items'] as $item) {
                $subtotal = $item['quantity'] * $item['purchase_price'];

                StockEntryItem::create([
                    'stock_entry_id' => $stockEntry->id,
                    'product_id'     => $item['product_id'],
                    'quantity'       => $item['quantity'],
                    'purchase_price' => $item['purchase_price'],
                    'subtotal'       => $subtotal,
                ]);

                Product::where('id', $item['product_id'])
                    ->increment('stock', $item['quantity']);
            }
        });

        return redirect()->route('admin.transactions.in.perSupplier', $supplierId)
            ->with('success', 'Transaksi berhasil disimpan!');
    }

    // 6. Show detail
    public function show($id)
    {
        $transaction = StockEntry::with([
            'supplier',
            'items.product',
            'payments'
        ])->findOrFail($id);

        return view('admin.transactions.in.show', compact('transaction'));
    }

    // 7. Download PDF
    public function downloadPdf(Request $request, $supplierId)
    {
        $supplier = Supplier::findOrFail($supplierId);

        $query = StockEntry::where('supplier_id', $supplierId)
            ->with('items.product')
            ->orderBy('date', 'desc');

        if ($request->filled('search')) {
            $query->whereHas('items.product', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        } else {
            if ($request->filter === 'today') {
                $query->whereDate('date', Carbon::today());
            }

            if ($request->filter === 'month') {
                $query->whereMonth('date', Carbon::now()->month)
                    ->whereYear('date', Carbon::now()->year);
            }
        }

        $transactions = $query->get();

        $pdf = Pdf::loadView('admin.transactions.in.pdf', [
            'supplier' => $supplier,
            'transactions' => $transactions
        ])->setPaper('a4', 'portrait');

        return $pdf->download('Report_Transaction_' . $supplier->name . '.pdf');
    }
}
