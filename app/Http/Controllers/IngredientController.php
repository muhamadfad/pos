<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Unit;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class IngredientController extends Controller
{
    /**
     * Display a listing of ingredients.
     */
    public function index(Request $request)
    {
        $query = Ingredient::with('unit', 'branch');
        
        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%");
        }
        
        // Branch filter
        if ($request->has('branch_id') && $request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }
        
        $ingredients = $query->orderBy('name')->paginate(15);
        $units = Unit::all();
        $branches = Branch::where('is_active', true)->get();
        
        // Get statistics
        $stats = [
            'total_ingredients' => Ingredient::count(),
            'low_stock' => Ingredient::whereColumn('current_stock', '<=', 'min_stock')
                                      ->where('current_stock', '>', 0)
                                      ->count(),
            'out_of_stock' => Ingredient::where('current_stock', 0)->count(),
            'total_value' => Ingredient::sum(DB::raw('current_stock * unit_cost')) ?? 0,
        ];
        
        return view('admin.ingredients.index', compact('ingredients', 'units', 'branches', 'stats'));
    }

    /**
     * Show form to create new ingredient.
     */
    public function create()
    {
        $units = Unit::all();
        $branches = Branch::where('is_active', true)->get();
        
        return view('admin.ingredients.create', compact('units', 'branches'));
    }

    /**
     * Store a newly created ingredient.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:ingredients,name',
            'unit_id' => 'nullable|exists:units,id',
            'current_stock' => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
            'unit_cost' => 'nullable|numeric|min:0',
            'branch_id' => 'nullable|exists:branches,id',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        Ingredient::create($request->all());
        
        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Bahan baku berhasil ditambahkan');
    }

    /**
     * Display ingredient details.
     */
    public function show($id)
    {
        $ingredient = Ingredient::with('unit', 'branch')->findOrFail($id);
        
        // Get stock movement history (if you have stock_movements table)
        // $movements = StockMovement::where('ingredient_id', $id)->orderBy('created_at', 'desc')->paginate(20);
        $movements = collect(); // Placeholder
        
        return view('inventory.ingredients.show', compact('ingredient', 'movements'));
    }

    /**
     * Show form to edit ingredient.
     */
    public function edit($id)
    {
        $ingredient = Ingredient::findOrFail($id);
        $units = Unit::all();
        $branches = Branch::where('is_active', true)->get();
        
        return view('admin.ingredients.edit', compact('ingredient', 'units', 'branches'));
    }

    /**
     * Update ingredient.
     */
    public function update(Request $request, $id)
    {
        $ingredient = Ingredient::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:ingredients,name,' . $id,
            'unit_id' => 'nullable|exists:units,id',
            'current_stock' => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
            'unit_cost' => 'nullable|numeric|min:0',
            'branch_id' => 'nullable|exists:branches,id',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $ingredient->update($request->all());
        
        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Bahan baku berhasil diperbarui');
    }

    /**
     * Delete ingredient.
     */
    public function destroy($id)
    {
        $ingredient = Ingredient::findOrFail($id);
        
        // Check if ingredient is used in recipes
        $recipeCount = DB::table('recipe_items')->where('ingredient_id', $id)->count();
        if ($recipeCount > 0) {
            return redirect()->back()
                ->with('error', "Bahan baku tidak dapat dihapus karena masih digunakan dalam {$recipeCount} resep.");
        }
        
        $name = $ingredient->name;
        $ingredient->delete();
        
        return redirect()->route('admin.ingredients.index')
            ->with('success', "Bahan baku '{$name}' berhasil dihapus");
    }

    /**
     * Adjust ingredient stock.
     */
    public function adjustStock(Request $request, $id)
    {
        $ingredient = Ingredient::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'adjustment' => 'required|numeric|not_in:0',
            'reason' => 'nullable|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $oldStock = $ingredient->current_stock;
        $newStock = $oldStock + $request->adjustment;
        
        if ($newStock < 0) {
            return response()->json(['error' => 'Stok tidak boleh negatif'], 422);
        }
        
        $ingredient->current_stock = $newStock;
        $ingredient->save();
        
        // Log movement (if you have stock_movements table)
        // StockMovement::create([
        //     'ingredient_id' => $ingredient->id,
        //     'old_stock' => $oldStock,
        //     'new_stock' => $newStock,
        //     'adjustment' => $request->adjustment,
        //     'reason' => $request->reason,
        //     'user_id' => auth()->id(),
        // ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Stok berhasil disesuaikan',
            'new_stock' => $newStock,
            'old_stock' => $oldStock,
        ]);
    }
}