<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Ingredient;
use App\Models\BranchStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BranchStockController extends Controller
{
    /**
     * Display a listing of branch stocks.
     */
    public function index(Request $request)
    {
        $query = BranchStock::with(['branch', 'ingredient']);
        
        // Filter by branch
        if ($request->has('branch_id') && $request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }
        
        // Filter by ingredient
        if ($request->has('ingredient_id') && $request->ingredient_id) {
            $query->where('ingredient_id', $request->ingredient_id);
        }
        
        // Filter by stock status
        if ($request->has('stock_status')) {
            switch ($request->stock_status) {
                case 'low':
                    $query->whereColumn('quantity', '<=', 'min_stock')
                          ->where('quantity', '>', 0);
                    break;
                case 'out':
                    $query->where('quantity', 0);
                    break;
                case 'normal':
                    $query->whereColumn('quantity', '>', 'min_stock');
                    break;
            }
        }
        
        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('ingredient', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            })->orWhereHas('branch', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
        }
        
        $branchStocks = $query->orderBy('last_updated', 'desc')->paginate(15);
        $branches = Branch::where('is_active', true)->get();
        $ingredients = Ingredient::orderBy('name')->get();
        
        // Get statistics
        $stats = [
            'total_items' => BranchStock::count(),
            'low_stock' => BranchStock::whereColumn('quantity', '<=', 'min_stock')
                                     ->where('quantity', '>', 0)
                                     ->count(),
            'out_of_stock' => BranchStock::where('quantity', 0)->count(),
            'total_value' => DB::table('branch_stock')
                ->join('ingredients', 'branch_stock.ingredient_id', '=', 'ingredients.id')
                ->select(DB::raw('SUM(branch_stock.quantity * ingredients.unit_cost) as total'))
                ->first()->total ?? 0,
        ];
        
        return view('inventory.branch-stock.index', compact('branchStocks', 'branches', 'ingredients', 'stats'));
    }

    /**
     * Show form to create new branch stock.
     */
    public function create()
    {
        $branches = Branch::where('is_active', true)->get();
        $ingredients = Ingredient::orderBy('name')->get();
        
        return view('inventory.branch-stock.create', compact('branches', 'ingredients'));
    }

    /**
     * Store a newly created branch stock.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required|exists:branches,id',
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity' => 'required|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // Check if combination already exists
        $exists = BranchStock::where('branch_id', $request->branch_id)
                             ->where('ingredient_id', $request->ingredient_id)
                             ->exists();
        
        if ($exists) {
            return redirect()->back()
                ->with('error', 'Stok untuk bahan baku ini sudah ada di cabang tersebut.')
                ->withInput();
        }
        
        BranchStock::create([
            'branch_id' => $request->branch_id,
            'ingredient_id' => $request->ingredient_id,
            'quantity' => $request->quantity,
            'min_stock' => $request->min_stock ?? 0,
            'max_stock' => $request->max_stock ?? 0,
            'last_updated' => now(),
        ]);
        
        return redirect()->route('inventory.branch-stock.index')
            ->with('success', 'Stok bahan baku berhasil ditambahkan');
    }

    /**
     * Display branch stock details.
     */
    public function show($id)
    {
        $branchStock = BranchStock::with(['branch', 'ingredient'])->findOrFail($id);
        
        // Get stock movement history (if you have stock_movements table)
        // $movements = StockMovement::where('branch_stock_id', $id)
        //                           ->orderBy('created_at', 'desc')
        //                           ->paginate(20);
        
        $movements = collect(); // Placeholder
        
        return view('inventory.branch-stock.show', compact('branchStock', 'movements'));
    }

    /**
     * Show form to edit branch stock.
     */
    public function edit($id)
    {
        $branchStock = BranchStock::findOrFail($id);
        $branches = Branch::where('is_active', true)->get();
        $ingredients = Ingredient::orderBy('name')->get();
        
        return view('inventory.branch-stock.edit', compact('branchStock', 'branches', 'ingredients'));
    }

    /**
     * Update branch stock.
     */
    public function update(Request $request, $id)
    {
        $branchStock = BranchStock::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required|exists:branches,id',
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity' => 'required|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // Check if combination already exists (excluding current)
        $exists = BranchStock::where('branch_id', $request->branch_id)
                             ->where('ingredient_id', $request->ingredient_id)
                             ->where('id', '!=', $id)
                             ->exists();
        
        if ($exists) {
            return redirect()->back()
                ->with('error', 'Stok untuk bahan baku ini sudah ada di cabang tersebut.')
                ->withInput();
        }
        
        $branchStock->update([
            'branch_id' => $request->branch_id,
            'ingredient_id' => $request->ingredient_id,
            'quantity' => $request->quantity,
            'min_stock' => $request->min_stock ?? 0,
            'max_stock' => $request->max_stock ?? 0,
            'last_updated' => now(),
        ]);
        
        return redirect()->route('inventory.branch-stock.index')
            ->with('success', 'Stok bahan baku berhasil diperbarui');
    }

    /**
     * Delete branch stock.
     */
    public function destroy($id)
    {
        $branchStock = BranchStock::findOrFail($id);
        $ingredientName = $branchStock->ingredient->name;
        $branchName = $branchStock->branch->name;
        
        $branchStock->delete();
        
        return redirect()->route('inventory.branch-stock.index')
            ->with('success', "Stok '{$ingredientName}' di cabang '{$branchName}' berhasil dihapus");
    }

    /**
     * Adjust stock (increment or decrement).
     */
    public function adjustStock(Request $request, $id)
    {
        $branchStock = BranchStock::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'adjustment' => 'required|integer|not_in:0',
            'reason' => 'nullable|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $oldQuantity = $branchStock->quantity;
        $newQuantity = $oldQuantity + $request->adjustment;
        
        if ($newQuantity < 0) {
            return response()->json(['error' => 'Stok tidak boleh negatif'], 422);
        }
        
        $branchStock->quantity = $newQuantity;
        $branchStock->last_updated = now();
        $branchStock->save();
        
        // Log movement (if you have stock_movements table)
        // StockMovement::create([
        //     'branch_stock_id' => $branchStock->id,
        //     'old_quantity' => $oldQuantity,
        //     'new_quantity' => $newQuantity,
        //     'adjustment' => $request->adjustment,
        //     'reason' => $request->reason,
        //     'user_id' => auth()->id(),
        // ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Stok berhasil disesuaikan',
            'new_quantity' => $newQuantity,
            'old_quantity' => $oldQuantity,
        ]);
    }

    /**
     * Bulk update stocks for a branch.
     */
    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required|exists:branches,id',
            'stocks' => 'required|array',
            'stocks.*.ingredient_id' => 'required|exists:ingredients,id',
            'stocks.*.quantity' => 'required|integer|min:0',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        DB::beginTransaction();
        
        try {
            foreach ($request->stocks as $stock) {
                BranchStock::updateOrCreate(
                    [
                        'branch_id' => $request->branch_id,
                        'ingredient_id' => $stock['ingredient_id'],
                    ],
                    [
                        'quantity' => $stock['quantity'],
                        'last_updated' => now(),
                    ]
                );
            }
            
            DB::commit();
            
            return redirect()->route('inventory.branch-stock.index')
                ->with('success', 'Stok cabang berhasil diperbarui secara massal');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui stok: ' . $e->getMessage());
        }
    }

    /**
     * Get low stock report.
     */
    public function lowStockReport(Request $request)
    {
        $query = BranchStock::with(['branch', 'ingredient'])
            ->whereColumn('quantity', '<=', 'min_stock')
            ->where('quantity', '>', 0);
        
        if ($request->has('branch_id') && $request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }
        
        $lowStocks = $query->orderBy('branch_id')->orderBy('quantity')->paginate(20);
        $branches = Branch::where('is_active', true)->get();
        
        return view('inventory.branch-stock.low-stock', compact('lowStocks', 'branches'));
    }
}