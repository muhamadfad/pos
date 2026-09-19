<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Production;
use App\Models\ProductionItem;
use App\Models\Ingredient;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CentralProductionController extends Controller
{
    private function getPusatBranch()
    {
        return Branch::where('name', 'like', '%Pusat%')->firstOrFail();
    }

    public function index(Request $request)
    {
        $pusat = $this->getPusatBranch();
        
        $query = Production::with(['resultIngredient'])
            ->where('branch_id', $pusat->id)
            ->latest('date');

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $productions = $query->paginate(15)->withQueryString();

        return view('admin.central-productions.index', compact('productions'));
    }

    public function downloadPdf(Request $request)
    {
        $pusat = $this->getPusatBranch();
        $query = Production::with(['resultIngredient', 'items.ingredient'])
            ->where('branch_id', $pusat->id)
            ->latest('date');

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $productions = $query->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.pdf.central-productions', compact('productions', 'pusat'))->setPaper('a4', 'landscape');
        return $pdf->download("Laporan_Produksi_Pusat.pdf");
    }

    public function downloadCombinedPdf(Request $request)
    {
        $pusat = $this->getPusatBranch();
        
        $startDate = $request->start_date ?? \Carbon\Carbon::now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? \Carbon\Carbon::now()->endOfMonth()->toDateString();
        
        // 1. Productions
        $productions = Production::with(['resultIngredient', 'items.ingredient'])
            ->where('branch_id', $pusat->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->latest('date')
            ->get();

        // 2. Transfers (Distribusi)
        $transfers = \App\Models\StockTransfer::with(['toBranch', 'user', 'items.ingredient.unit'])
            ->where('from_branch_id', $pusat->id)
            ->where('to_branch_id', '!=', $pusat->id)
            ->whereBetween('transfer_date', [$startDate, $endDate])
            ->orderBy('transfer_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // 3. Returns (Return Cabang)
        $returns = \App\Models\StockTransfer::with(['fromBranch', 'user', 'items.ingredient.unit'])
            ->where('to_branch_id', $pusat->id)
            ->where('from_branch_id', '!=', $pusat->id)
            ->whereBetween('transfer_date', [$startDate, $endDate])
            ->orderBy('transfer_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // Override request manually so the view uses the default date if not provided
        $request->merge(['start_date' => $startDate, 'end_date' => $endDate]);

                // 4. Daily Return Summaries
        $dailyReturnSummaries = \App\Models\DailyReturnSummary::whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'asc')
            ->get();

                // 5. Splits (Pemisahan Bahan)
        $splits = \App\Models\StockTransfer::with(['items.ingredient.unit', 'user', 'sourceIngredient.unit'])
            ->where('from_branch_id', $pusat->id)
            ->where('to_branch_id', $pusat->id)
            ->whereNotNull('source_ingredient_id')
            ->whereBetween('transfer_date', [$startDate, $endDate])
            ->orderBy('transfer_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.pdf.central-logistics-combined', compact('productions', 'transfers', 'returns', 'pusat', 'request', 'dailyReturnSummaries', 'splits'))
            ->setPaper('a4', 'landscape');
            
        return $pdf->download("Laporan_Logistik_Lengkap.pdf");
    }

    public function create()
    {
        $pusat = $this->getPusatBranch();
        
        // Hanya tampilkan bahan baku yang ada di cabang pusat (aktif)
        $ingredients = Ingredient::with('unit')
            ->where('branch_id', $pusat->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.central-productions.create', compact('ingredients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'                    => 'required|date',
            'result_ingredient_id'    => 'required|exists:ingredients,id',
            'result_quantity'         => 'required|numeric|min:0.1',
            'reference_number'        => 'nullable|string|max:100',
            'notes'                   => 'nullable|string',
            'items'                   => 'required|array|min:1',
            'items.*.ingredient_id'   => 'required|exists:ingredients,id|different:result_ingredient_id',
            'items.*.quantity'        => 'required|numeric|min:0.1',
        ]);

        $pusat = $this->getPusatBranch();

        DB::transaction(function () use ($request, $pusat) {
            $totalCost = 0;
            $itemsData = [];

            // 1. Hitung total cost dan siapkan data item (pemotongan stok)
            foreach ($request->items as $item) {
                $ingredient = Ingredient::findOrFail($item['ingredient_id']);


                $cost = $ingredient->unit_cost ?? 0;
                $subtotal = $cost * $item['quantity'];
                $totalCost += $subtotal;

                $itemsData[] = [
                    'ingredient_id' => $ingredient->id,
                    'quantity'      => $item['quantity'],
                    'unit_cost'     => $cost,
                    'subtotal'      => $subtotal,
                ];

                // Potong stok bahan mentah
                $ingredient->update([
                    'current_stock' => $ingredient->current_stock - $item['quantity']
                ]);
            }

            // 2. Buat record produksi
            $production = Production::create([
                'branch_id'            => $pusat->id,
                'result_ingredient_id' => $request->result_ingredient_id,
                'reference_number'     => $request->reference_number ?? 'PRD-' . strtoupper(uniqid()),
                'date'                 => $request->date,
                'result_quantity'      => $request->result_quantity,
                'total_cost'           => $totalCost,
                'notes'                => $request->notes,
            ]);

            // 3. Simpan item produksi
            foreach ($itemsData as $data) {
                ProductionItem::create(array_merge($data, ['production_id' => $production->id]));
            }

            // 4. Tambah stok hasil produksi dan hitung rata-rata modal (Weighted Average)
            $resultIngredient = Ingredient::findOrFail($request->result_ingredient_id);
            
            $oldStock = $resultIngredient->current_stock;
            $oldCost = $resultIngredient->unit_cost ?? 0;
            
            $newStock = $oldStock + $request->result_quantity;
            
            $oldTotalValue = $oldStock * $oldCost;
            $newTotalValue = $oldTotalValue + $totalCost;
            
            $averageCost = $newStock > 0 ? $newTotalValue / $newStock : 0;
            
            $resultIngredient->update([
                'current_stock' => $newStock,
                'unit_cost'     => $averageCost
            ]);
        });

        return redirect()->route('admin.central-productions.index')->with('success', 'Produksi berhasil dicatat. Stok bahan mentah telah dikurangi dan stok hasil produksi telah ditambahkan beserta penyesuaian harga modal.');
    }

    public function show($id)
    {
        $pusat = $this->getPusatBranch();
        
        $production = Production::with(['resultIngredient.unit', 'items.ingredient.unit'])
            ->where('branch_id', $pusat->id)
            ->findOrFail($id);

        return view('admin.central-productions.show', compact('production'));
    }
}
