<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Ingredient;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CentralStockSplitController extends Controller
{
    private function getPusatBranch()
    {
        return Branch::where('code', 'PUSAT')->firstOrFail();
    }

    public function index(Request $request)
    {
        $pusat = $this->getPusatBranch();
        
        $query = StockTransfer::with(['items.ingredient', 'user'])
            ->where('from_branch_id', $pusat->id)
            ->where('to_branch_id', $pusat->id)
            ->where('status', 'split')
            ->orderBy('transfer_date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('transfer_date', [
                $request->start_date,
                $request->end_date
            ]);
        }

        $splits = $query->paginate(10);

        return view('admin.central-splits.index', compact('splits'));
    }

    public function create()
    {
        $pusat = $this->getPusatBranch();
        
        $ingredients = Ingredient::with('unit')
            ->where('branch_id', $pusat->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.central-splits.create', compact('ingredients'));
    }

        public function store(Request $request)
    {
        // Filter out empty items before validation
        if ($request->has('items') && is_array($request->items)) {
            $filteredItems = array_filter($request->items, function($item) {
                return !empty($item['ingredient_id']);
            });
            $request->merge(['items' => array_values($filteredItems)]);
        }
        $request->validate([
            'date' => 'required|date',
            'source_ingredient_id' => 'required|exists:ingredients,id',
            'source_quantity' => 'required|numeric|min:0.1',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.ingredient_id' => 'required|exists:ingredients,id',
            'items.*.quantity' => 'required|numeric|min:0.1',
            'items.*.notes' => 'nullable|string',
        ]);

        $pusat = $this->getPusatBranch();
        $sourceIngredient = Ingredient::findOrFail($request->source_ingredient_id);

        if ($sourceIngredient->branch_id !== $pusat->id) {
            return back()->with('error', 'Bahan baku sumber tidak valid.');
        }

        

        try {
            DB::beginTransaction();

            $transfer = StockTransfer::create([
                'reference_number' => $request->reference_number ?: 'SPLIT-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
                'from_branch_id' => $pusat->id,
                'to_branch_id' => $pusat->id,
                'user_id' => auth()->id(),
                'transfer_date' => $request->date,
                'notes' => $request->notes,
                'status' => 'completed',
                'source_ingredient_id' => $request->source_ingredient_id,
                'source_quantity' => $request->source_quantity,
            ]);

            $sourceIngredient->decrement('current_stock', $request->source_quantity);

            foreach ($request->items as $item) {
                $targetIngredient = Ingredient::findOrFail($item['ingredient_id']);
                $targetIngredient->increment('current_stock', $item['quantity']);

                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'ingredient_id' => $item['ingredient_id'],
                    'quantity' => $item['quantity'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.central-splits.index')->with('success', 'Pemisahan stok berhasil diproses.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $pusat = $this->getPusatBranch();
        
        $split = StockTransfer::with(['items.ingredient.unit', 'user', 'sourceIngredient.unit'])
            ->where('from_branch_id', $pusat->id)
            ->where('to_branch_id', $pusat->id)
            ->where('status', 'split')
            ->findOrFail($id);

        return view('admin.central-splits.show', compact('split'));
    }
}
