<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Models\Product;
use App\Models\Ingredient;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RecipeController extends Controller
{
    public function index(Request $request)
    {
        // Gunakan paginate, bukan get()
        $query = Product::whereHas('recipeItems')
            ->with(['category', 'recipeItems.ingredient']);
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%");
        }
        
        // Gunakan paginate() untuk mendapatkan Paginator instance
        $perPage = $request->per_page ?? 10;
        $products = $query->orderBy('name')->paginate($perPage);
        
        // Hitung statistik
        $totalProductsWithRecipe = Product::whereHas('recipeItems')->count();
        $totalIngredients = Ingredient::count();
        
        // Hitung rata-rata bahan per resep
        $totalRecipeItems = DB::table('recipe_items')->count();
        $avgIngredientsPerRecipe = $totalProductsWithRecipe > 0 
            ? round($totalRecipeItems / $totalProductsWithRecipe, 1) 
            : 0;
        
        return view('admin.recipes.index', compact('products', 'totalIngredients', 'avgIngredientsPerRecipe'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $ingredients = Ingredient::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        
        return view('admin.recipes.create', compact('products', 'ingredients', 'units'));
    }

    public function store(Request $request)
    {
        // Validasi
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'items' => 'required|array|min:1',
            'items.*.ingredient_id' => 'required|exists:ingredients,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Hapus resep lama jika ada
            RecipeItem::where('product_id', $request->product_id)->delete();

            // Simpan resep baru
            foreach ($request->items as $item) {
                RecipeItem::create([
                    'product_id' => $request->product_id,
                    'ingredient_id' => $item['ingredient_id'],
                    'quantity' => $item['quantity'],
                    'unit_id' => $item['unit_id'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.recipes.index')
                ->with('success', 'Resep berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollback();
            
            // Debug error
            dd($e->getMessage(), $e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'Gagal menyimpan resep: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $product = Product::with(['recipeItems.ingredient', 'recipeItems.unit'])->findOrFail($id);
        
        return view('admin.recipes.show', compact('product'));
    }

    public function edit($id)
    {
        $product = Product::with('recipeItems')->findOrFail($id);
        $ingredients = Ingredient::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        
        return view('admin.recipes.edit', compact('product', 'ingredients', 'units'));
    }

     public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.ingredient_id' => 'required|exists:ingredients,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_id' => 'nullable|exists:units,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            // Hapus semua item lama untuk product ini
            RecipeItem::where('product_id', $id)->delete();

            // Simpan item baru
            foreach ($request->items as $item) {
                RecipeItem::create([
                    'product_id' => $id,
                    'ingredient_id' => $item['ingredient_id'],
                    'quantity' => $item['quantity'],
                    'unit_id' => $item['unit_id'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.recipes.index')
                ->with('success', 'Resep berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui resep: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $productName = $product->name;

        RecipeItem::where('product_id', $id)->delete();

        return redirect()->route('admin.recipes.index')
            ->with('success', "Resep untuk '{$productName}' berhasil dihapus");
    }

    public function calculateHPP($productId)
    {
        $product = Product::with(['recipeItems.ingredient'])->findOrFail($productId);
        
        $totalHPP = 0;
        $details = [];
        
        foreach ($product->recipeItems as $item) {
            $ingredientCost = $item->ingredient->unit_cost ?? 0;
            $subtotal = $item->quantity * $ingredientCost;
            $totalHPP += $subtotal;
            
            $details[] = [
                'ingredient_name' => $item->ingredient->name,
                'quantity' => $item->quantity,
                'unit_name' => $item->ingredient->unit->name ?? '-',
                'unit_cost' => $ingredientCost,
                'subtotal' => $subtotal,
            ];
        }
        
        $margin = $product->selling_price - $totalHPP;
        $marginPercent = $totalHPP > 0 ? ($margin / $totalHPP) * 100 : 0;
        
        return response()->json([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'selling_price' => $product->selling_price,
            'hpp' => $totalHPP,
            'margin' => $margin,
            'margin_percent' => round($marginPercent, 2),
            'details' => $details,
        ]);
    }

    /**
     * Tampilkan laporan HPP semua menu
     */
    public function hppReport(Request $request)
{
    $products = Product::with(['recipeItems.ingredient', 'category'])
        ->whereHas('recipeItems')
        ->get();
    
    $report = [];
    
    foreach ($products as $product) {
        $hpp = 0;
        $ingredients = [];
        
        foreach ($product->recipeItems as $item) {
            $cost = $item->ingredient->unit_cost ?? 0;
            $subtotal = $item->quantity * $cost;
            $hpp += $subtotal;
            
            $ingredients[] = [
                'name' => $item->ingredient->name,
                'quantity' => $item->quantity,
                'unit_cost' => $cost,
                'subtotal' => $subtotal,
            ];
        }
        
        $report[] = [
            'id' => $product->id,
            'name' => $product->name,
            'category' => $product->category->name ?? '-',
            'selling_price' => $product->selling_price,
            'hpp' => $hpp,
            'margin' => $product->selling_price - $hpp,
            'margin_percent' => $hpp > 0 ? round((($product->selling_price - $hpp) / $hpp) * 100, 2) : 0,
            'ingredients' => $ingredients,
        ];
    }
    
    // Ubah ke collection
    $collection = collect($report);
    
    // Urutkan berdasarkan margin terendah
    $sorted = $collection->sortBy('margin_percent');
    
    return view('admin.recipes.hpp-report', [
        'products' => $sorted,
        'total_products' => $collection->count(),      // ← pakai collection
        'average_margin' => $collection->avg('margin_percent'), // ← pakai collection
    ]);
}
}