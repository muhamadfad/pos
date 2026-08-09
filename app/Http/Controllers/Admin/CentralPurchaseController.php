<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Ingredient;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Services\KasproService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CentralPurchaseController extends Controller
{
    private function getPusatBranch()
    {
        $branch = Branch::where('code', 'PUSAT')->first();
        if (!$branch) {
            $branch = Branch::create([
                'name' => 'Gudang Pusat',
                'code' => 'PUSAT',
                'address' => 'Pusat',
                'is_active' => true,
            ]);
        }
        return $branch;
    }

    public function index(Request $request)
    {
        $pusat = $this->getPusatBranch();

        $query = Purchase::with(['supplier', 'items.ingredient'])
            ->where('branch_id', $pusat->id)
            ->latest('date');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay(),
            ]);
        } elseif ($request->filter === 'month') {
            $query->whereMonth('date', Carbon::now()->month)
                ->whereYear('date', Carbon::now()->year);
        }

        $purchases = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => Purchase::where('branch_id', $pusat->id)->count(),
            'paid'  => Purchase::where('branch_id', $pusat->id)->where('payment_status', 'paid')->count(),
            'debt'  => Purchase::where('branch_id', $pusat->id)->whereIn('payment_status', ['unpaid', 'partial'])->count(),
        ];

        return view('admin.central-purchases.index', compact('purchases', 'stats'));
    }

    public function create()
    {
        $pusat = $this->getPusatBranch();
        $suppliers = Supplier::orderBy('name')->get();
        
        // Ambil semua bahan baku dari semua cabang untuk referensi (unique by name)
        // Jika belum ada di Pusat, akan di-copy otomatis saat disimpan
        $allIngredients = Ingredient::with('unit')->where('is_active', true)->orderBy('name')->get();
        $ingredients = $allIngredients->unique('name')->values();

        return view('admin.central-purchases.create', compact('suppliers', 'ingredients'));
    }

    public function store(Request $request, KasproService $kaspro)
    {
        $request->validate([
            'supplier_id'             => 'nullable|exists:suppliers,id',
            'date'                    => 'required|date',
            'paid'                    => 'nullable|numeric|min:0',
            'payment_method'          => 'nullable|string',
            'reference_number'        => 'nullable|string|max:100',
            'notes'                   => 'nullable|string',
            'items'                   => 'required|array|min:1',
            'items.*.ingredient_id'   => 'required|exists:ingredients,id',
            'items.*.quantity'        => 'required|numeric|min:0.1',
            'items.*.purchase_price'  => 'required|numeric|min:0',
        ]);

        $pusat = $this->getPusatBranch();
        $supplier = $request->supplier_id ? Supplier::findOrFail($request->supplier_id) : null;

        DB::transaction(function () use ($request, $supplier, $pusat, $kaspro) {
            $total = collect($request->items)->sum(fn($i) => $i['quantity'] * $i['purchase_price']);
            $paid  = $request->paid ?? 0;

            $purchase = Purchase::create([
                'branch_id'        => $pusat->id,
                'reference_number' => $request->reference_number,
                'supplier_id'      => $supplier ? $supplier->id : null,
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

                $templateIngredient = Ingredient::findOrFail($item['ingredient_id']);
                
                // Cari atau buat bahan baku di Gudang Pusat
                $ingredient = Ingredient::firstOrCreate(
                    [
                        'name'      => $templateIngredient->name,
                        'branch_id' => $pusat->id,
                    ],
                    [
                        'unit_id'       => $templateIngredient->unit_id,
                        'current_stock' => 0,
                        'min_stock'     => $templateIngredient->min_stock,
                        'unit_cost'     => $templateIngredient->unit_cost ?? 0,
                        'is_active'     => true,
                    ]
                );

                PurchaseItem::create([
                    'purchase_id'    => $purchase->id,
                    'ingredient_id'  => $ingredient->id,
                    'quantity'       => $item['quantity'],
                    'purchase_price' => $item['purchase_price'],
                    'subtotal'       => $subtotal,
                ]);

                $oldStock  = $ingredient->current_stock;
                $oldPrice  = $ingredient->unit_cost ?? 0;

                $newQty    = $item['quantity'];
                $newPrice  = $item['purchase_price'];

                $oldTotal  = $oldStock * $oldPrice;
                $newTotal  = $newQty * $newPrice;
                $totalQty  = $oldStock + $newQty;

                $averagePrice = $totalQty > 0 ? ($oldTotal + $newTotal) / $totalQty : $newPrice;

                // Auto-kategorisasi (Kaspro)
                $categorization = $kaspro->categorize($request->notes ?? 'Pembelian Pusat dari ' . $supplier->name);
                $purchase->kaspro_category = $categorization['category'];
                $purchase->kaspro_confidence = $categorization['confidence'];
                $purchase->kaspro_needs_review = $categorization['needs_review'];
                
                $anomaly = $kaspro->detectAnomaly($purchase, 'EXPENSE');
                $purchase->kaspro_is_anomaly = $anomaly['is_anomaly'];
                $purchase->kaspro_risk_score = $anomaly['risk_score'];
                $purchase->kaspro_anomaly_flags = $anomaly['flags'];
                $purchase->save();

                $ingredient->update([
                    'current_stock' => $totalQty,
                    'unit_cost' => $averagePrice,
                ]);
            }
        });

        return redirect()->route('admin.central-purchases.index')->with('success', 'Pembelian Pusat berhasil disimpan!');
    }

    public function show($id)
    {
        $pusat = $this->getPusatBranch();
        $purchase = Purchase::where('branch_id', $pusat->id)->with(['supplier', 'items.ingredient', 'payments'])->findOrFail($id);
        
        return view('admin.central-purchases.show', compact('purchase'));
    }
}
