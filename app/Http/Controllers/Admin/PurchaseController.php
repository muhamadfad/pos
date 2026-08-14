<?php

namespace App\Http\Controllers\Admin;
use App\Services\KasproService;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseController extends Controller
{
    private function applyBranchFilter($query)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $user->branch_id) {
            $query->where('branch_id', $user->branch_id);
        } elseif ($user->isSuperAdmin()) {
            $pusat = Branch::where('code', 'PUSAT')->first();
            if ($pusat) {
                $query->where(function ($q) use ($pusat) {
                    $q->where('branch_id', '!=', $pusat->id)
                      ->orWhereNull('branch_id');
                });
            }
        }
        return $query;
    }

    public function index()
    {
        $query = Purchase::with(['supplier', 'items.ingredient'])
            ->latest('date');
            
        $query = $this->applyBranchFilter($query);
        $purchases = $query->paginate(15);

        $statsQuery = Purchase::query();
        $statsQuery = $this->applyBranchFilter($statsQuery);

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'paid'  => (clone $statsQuery)->where('payment_status', 'paid')->count(),
            'debt'  => (clone $statsQuery)->whereIn('payment_status', ['unpaid', 'partial'])->count(),
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
            ->with('items.ingredient')
            ->orderBy('date', 'desc');

        $query = $this->applyBranchFilter($query);

        if ($request->filled('search')) {
            $query->whereHas(
                'items.ingredient',
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

        $statsQuery = Purchase::where('supplier_id', $supplier->id);
        $statsQuery = $this->applyBranchFilter($statsQuery);

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'paid'  => (clone $statsQuery)->where('payment_status', 'paid')->count(),
            'debt'  => (clone $statsQuery)->whereIn('payment_status', ['unpaid', 'partial'])->count(),
        ];

        if ($request->ajax()) {
            return view('admin.purchases.partials.table', compact('purchases', 'stats'))->render();
        }

        $total = $stats['total'];

        return view('admin.purchases.per-supplier', compact('supplier', 'purchases', 'total', 'stats'));
    }

    public function create(?Supplier $supplier = null)
    {
        $ingredients = Ingredient::with('unit')->orderBy('name')->get();
        return view('admin.purchases.create', compact('supplier', 'ingredients'));
    }

    public function store(Request $request, ?Supplier $supplier = null, KasproService $kaspro)
    {
        $request->validate([
            'date'                    => 'required|date',
            'paid'                    => 'nullable|numeric|min:0',
            'payment_method'          => 'nullable|string',
            'reference_number'        => 'nullable|string|max:100',
            'notes'                   => 'nullable|string',
            'items'                   => 'required|array|min:1',
            'items.*.ingredient_id'   => 'required|exists:ingredients,id',
            'items.*.quantity'        => 'required|integer|min:1',
            'items.*.purchase_price'  => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $supplier, $kaspro) {
            $total = collect($request->items)->sum(fn($i) => $i['quantity'] * $i['purchase_price']);
            $paid  = $request->paid ?? 0;

            $purchase = Purchase::create([
                'reference_number' => $request->reference_number,
                'supplier_id'      => $supplier ? $supplier->id : null,
                'date'             => $request->date,
                'total_amount'     => $total,
                'total_paid'       => $paid,
                'remaining_debt'   => max(0, $total - $paid),
                'payment_status'   => $paid >= $total ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'),
                'payment_method'   => $request->payment_method ?? 'cash',
                'notes'            => $request->notes,
            ]);

            foreach ($request->items as $item) {
                $subtotal = $item['quantity'] * $item['purchase_price'];

                PurchaseItem::create([
                    'purchase_id'    => $purchase->id,
                    'ingredient_id'  => $item['ingredient_id'],
                    'quantity'       => $item['quantity'],
                    'purchase_price' => $item['purchase_price'],
                    'subtotal'       => $subtotal,
                ]);

                $ingredient = Ingredient::find($item['ingredient_id']);

                $oldStock  = $ingredient->current_stock;
                $oldPrice  = $ingredient->unit_cost ?? 0;

                $newQty    = $item['quantity'];
                $newPrice  = $item['purchase_price'];

                // Hitung total nilai lama & baru
                $oldTotal  = $oldStock * $oldPrice;
                $newTotal  = $newQty * $newPrice;

                // Hitung average baru
                $totalQty  = $oldStock + $newQty;

                $averagePrice = $totalQty > 0
                    ? ($oldTotal + $newTotal) / $totalQty
                    : $newPrice;


                // Auto-kategorisasi
                $categorization = $kaspro->categorize($request->notes ?? 'Pembelian' . ($supplier ? ' dari ' . $supplier->name : ''));
                $purchase->kaspro_category = $categorization['category'];
                $purchase->kaspro_confidence = $categorization['confidence'];
                $purchase->kaspro_needs_review = $categorization['needs_review'];
                
                // Deteksi anomali
                $anomaly = $kaspro->detectAnomaly($purchase, 'EXPENSE');
                $purchase->kaspro_is_anomaly = $anomaly['is_anomaly'];
                $purchase->kaspro_risk_score = $anomaly['risk_score'];
                $purchase->kaspro_anomaly_flags = $anomaly['flags'];
                
                $purchase->save();
                
                // Cek alert
                $kaspro->checkAndCreateAlerts();

                // Update ingredient
                $ingredient->update([
                    'current_stock' => $totalQty,
                    'unit_cost' => $averagePrice,
                ]);
            }
        });

        return redirect()->route('admin.purchases.per-supplier', $supplier)
            ->with('success', 'Pembelian berhasil disimpan!');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.ingredient', 'payments']);
        return view('admin.purchases.show', compact('purchase'));
    }

    public function downloadPdf(Request $request, Supplier $supplier)
    {
        $query = Purchase::where('supplier_id', $supplier->id)
            ->with('items.ingredient')
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
