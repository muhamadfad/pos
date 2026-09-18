<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Ingredient;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockReturnController extends Controller
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

    public function index()
    {
        $pusat = $this->getPusatBranch();
        $user = Auth::user();
        
        $query = StockTransfer::with(['fromBranch', 'user', 'items.ingredient.unit'])
            ->where('to_branch_id', $pusat->id) // returns are bound for pusat
            ->orderBy('transfer_date', 'desc')
            ->orderBy('id', 'desc');

        if ($user->role !== 'super_admin' && $user->branch_id !== $pusat->id) {
            $query->where('from_branch_id', $user->branch_id);
        }

        $returns = $query->paginate(15);

        return view('admin.stock-returns.index', compact('returns'));
    }

    public function downloadPdf()
    {
        $pusat = $this->getPusatBranch();
        $user = Auth::user();
        $query = StockTransfer::with(['fromBranch', 'user', 'items.ingredient.unit'])
            ->where('to_branch_id', $pusat->id)
            ->orderBy('transfer_date', 'desc')
            ->orderBy('id', 'desc');

        if ($user->role !== 'super_admin' && $user->branch_id !== $pusat->id) {
            $query->where('from_branch_id', $user->branch_id);
        }

        $returns = $query->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.pdf.stock-returns', compact('returns'))->setPaper('a4', 'landscape');
        return $pdf->download("Laporan_Return_Stok.pdf");
    }

    public function create()
    {
        $pusat = $this->getPusatBranch();
        $user = Auth::user();
        
        // Jika user adalah admin pusat / super admin, bisa bantu inputkan untuk cabang
        if ($user->role === 'super_admin' || $user->branch_id === $pusat->id) {
            $branches = Branch::where('id', '!=', $pusat->id)->where('is_active', true)->orderBy('name')->get();
            $ingredients = Ingredient::with('unit')->where('branch_id', '!=', $pusat->id)->orderBy('name')->get();
        } else {
            // Jika user adalah admin cabang, kunci ke cabangnya sendiri
            $branches = Branch::where('id', $user->branch_id)->get();
            $ingredients = Ingredient::with('unit')->where('branch_id', $user->branch_id)->orderBy('name')->get();
        }
        
        $pusatIngredients = Ingredient::with('unit')->where('branch_id', $pusat->id)->orderBy('name')->get();

        return view('admin.stock-returns.create', compact('branches', 'ingredients', 'pusatIngredients', 'pusat'));
    }

        public function store(\Illuminate\Http\Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $pusat = $this->getPusatBranch();
        
        $request->validate([
            'from_branch_id' => 'required|exists:branches,id',
            'transfer_date' => 'required|date',
            'notes' => 'nullable|string',
            'source_ingredient_id' => 'required|exists:ingredients,id',
            'source_quantity' => 'required|numeric|min:0.1',
            'items' => 'required|array|min:1',
            'items.*.ingredient_id' => 'required|exists:ingredients,id',
            'items.*.quantity' => 'required|numeric|min:0.1',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $pusat) {
            // 1. Kurangi stok bahan baku dari cabang
            $sourceIngredient = \App\Models\Ingredient::findOrFail($request->source_ingredient_id);
            if ($sourceIngredient->current_stock < $request->source_quantity) {
                throw new \Exception("Stok cabang tidak mencukupi untuk return.");
            }
            $sourceIngredient->decrement('current_stock', $request->source_quantity);

            // 2. Buat record transfer
            $transfer = \App\Models\StockTransfer::create([
                'reference_number' => 'RET-' . date('Ymd') . '-' . rand(1000, 9999),
                'from_branch_id' => $request->from_branch_id,
                'to_branch_id' => $pusat->id,
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'transfer_date' => $request->transfer_date,
                'notes' => $request->notes,
                'status' => 'completed',
                'source_ingredient_id' => $request->source_ingredient_id,
                'source_quantity' => $request->source_quantity,
            ]);

            // 3. Tambah stok hasil pemisahan ke pusat
            foreach ($request->items as $item) {
                $targetIngredient = \App\Models\Ingredient::findOrFail($item['ingredient_id']);
                
                // Cari atau buat bahan baku yang sama di Gudang Pusat
                $pusatIngredient = \App\Models\Ingredient::firstOrCreate(
                    [
                        'name' => $targetIngredient->name,
                        'branch_id' => $pusat->id
                    ],
                    [
                        'unit_id' => $targetIngredient->unit_id,
                        'current_stock' => 0,
                        'min_stock' => $targetIngredient->min_stock ?? 0,
                        'unit_cost' => $targetIngredient->unit_cost ?? 0,
                    ]
                );

                // Update unit_cost rata-rata di pusat (opsional/disederhanakan)
                $oldTotalValue = $pusatIngredient->current_stock * $pusatIngredient->unit_cost;
                $newTotalValue = $item['quantity'] * $targetIngredient->unit_cost;
                $newStock = $pusatIngredient->current_stock + $item['quantity'];
                $averagePrice = $newStock > 0 ? ($oldTotalValue + $newTotalValue) / $newStock : $pusatIngredient->unit_cost;
                
                $pusatIngredient->increment('current_stock', $item['quantity']);
                $pusatIngredient->update(['unit_cost' => $averagePrice]);

                // Record mutasi ke items
                \App\Models\StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'ingredient_id' => $targetIngredient->id, // target ingredient
                    'quantity' => $item['quantity'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }
        });

        return redirect()->route('admin.stock-returns.index')->with('success', 'Berhasil mencatat pemisahan return dari cabang ke pusat!');
    }

    public function show($id)
    {
        $return = StockTransfer::with(['fromBranch', 'user', 'items.ingredient'])->findOrFail($id);
        return view('admin.stock-returns.show', compact('return'));
    }

    
    public function storeDailySummary(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'summary_date' => 'required|date',
            'summary_notes' => 'required|string',
        ]);

        \App\Models\DailyReturnSummary::updateOrCreate(
            ['date' => $request->summary_date],
            ['notes' => $request->summary_notes]
        );

        return redirect()->back()->with('success', 'Rekap pemisahan gabungan harian berhasil disimpan.');
    }
}