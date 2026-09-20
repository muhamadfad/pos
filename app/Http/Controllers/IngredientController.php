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
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        $isAdmin = $user->role === 'admin';
        $userBranchId = $user->branch_id;
        
        $query = Ingredient::with('unit', 'branch');
        
        // Filter berdasarkan role
        if ($isSuperAdmin) {
            // Super admin: filter berdasarkan branch_id yang dipilih
            if ($request->has('branch_id') && $request->branch_id) {
                $query->where('branch_id', $request->branch_id);
            }
        } elseif ($user->canManagePusat()) {
            $pusat = Branch::where('code', 'PUSAT')->first();
            if ($request->has('branch_id') && $request->branch_id) {
                // Pastikan hanya bisa filter cabang sendiri atau PUSAT
                if ($request->branch_id == $userBranchId || $request->branch_id == $pusat?->id) {
                    $query->where('branch_id', $request->branch_id);
                } else {
                    $query->where('branch_id', $userBranchId);
                }
            } else {
                // Tampilkan cabang sendiri dan Pusat
                $query->whereIn('branch_id', array_filter([$userBranchId, $pusat?->id]));
            }
        } else {
            // Admin dan kasir hanya melihat bahan baku di cabangnya sendiri
            $query->where('branch_id', $userBranchId);
        }
        
        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%");
        }
        
        $ingredients = $query->orderBy('name')->paginate($request->per_page ?? 15);
        
        // Units untuk dropdown
        $units = Unit::all();
        
        // Branches untuk filter (hanya untuk super admin)
        $branches = [];
        if ($isSuperAdmin) {
            $branches = Branch::where('is_active', true)->get();
        } else {
            // Untuk admin biasa, kirim branch mereka saja
            $currentBranch = Branch::find($userBranchId);
            if ($currentBranch) {
                $branches = collect([$currentBranch]);
            }
        }
        
        // Get statistics berdasarkan role
        $statsQuery = Ingredient::query();
        if (!$isSuperAdmin) {
            $statsQuery->where('branch_id', $userBranchId);
        } elseif ($request->has('branch_id') && $request->branch_id) {
            $statsQuery->where('branch_id', $request->branch_id);
        }
        
        $stats = [
            'total_ingredients' => $statsQuery->count(),
            'low_stock' => (clone $statsQuery)->whereColumn('current_stock', '<=', 'min_stock')
                                          ->where('current_stock', '>', 0)
                                          ->count(),
            'out_of_stock' => (clone $statsQuery)->where('current_stock', 0)->count(),
            'total_value' => (clone $statsQuery)->sum(DB::raw('current_stock * unit_cost')) ?? 0,
        ];
        
        // Jika request AJAX, return partial view
        if ($request->ajax() || $request->wantsJson()) {
            return view('admin.ingredients.partials.table', compact('ingredients'))->render();
        }
        
        return view('admin.ingredients.index', compact('ingredients', 'units', 'branches', 'stats', 'isSuperAdmin', 'userBranchId'));
    }

    /**
     * Show form to create new ingredient.
     */
    public function create()
    {
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        $userBranchId = $user->branch_id;
        
        $units = Unit::all();
        
        // Branches: super admin bisa pilih cabang, admin biasa hanya cabang sendiri
        if ($isSuperAdmin) {
            $branches = Branch::where('is_active', true)->get();
        } elseif ($user->canManagePusat()) {
            $pusat = Branch::where('code', 'PUSAT')->first();
            $branches = Branch::where('id', $userBranchId)
                ->orWhere('id', $pusat?->id)
                ->where('is_active', true)->get();
        } else {
            $branches = Branch::where('id', $userBranchId)->where('is_active', true)->get();
        }
        
        return view('admin.ingredients.create', compact('units', 'branches', 'isSuperAdmin', 'userBranchId'));
    }

    /**
     * Store a newly created ingredient.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        $userBranchId = $user->branch_id;
        
        $rules = [
            'name' => 'required|string|max:255|unique:ingredients,name',
            'unit_id' => 'nullable|exists:units,id',
            'current_stock' => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
            'unit_cost' => 'nullable|numeric|min:0',
        ];
        
        // Super admin bisa memilih cabang, admin biasa menggunakan cabang sendiri
        if ($isSuperAdmin) {
            $rules['branch_id'] = 'required|exists:branches,id';
        }
        
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $data = $request->all();
        
                // Jika bukan super admin, set branch_id ke cabang user
        if (!$isSuperAdmin) {
            if ($user->canManagePusat() && isset($data['branch_id'])) {
                $pusat = Branch::where('code', 'PUSAT')->first();
                if ($data['branch_id'] != $pusat?->id && $data['branch_id'] != $userBranchId) {
                    $data['branch_id'] = $userBranchId;
                }
            } else {
                $data['branch_id'] = $userBranchId;
            }
        }
        
        $data['unit_cost'] = $data['unit_cost'] ?? 0;
        Ingredient::create($data);
        
        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Bahan baku berhasil ditambahkan');
    }

    /**
     * Display ingredient details.
     */
    public function show($id)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        $userBranchId = $user->branch_id;
        
        $ingredient = Ingredient::with('unit', 'branch')->findOrFail($id);
        
        // Cek akses: non-super admin hanya bisa melihat bahan baku di cabangnya sendiri
        $pusat = Branch::where('code', 'PUSAT')->first();
        $isPusat = $ingredient->branch_id == $pusat?->id;
        
        if (!$isSuperAdmin && $ingredient->branch_id != $userBranchId && !($isPusat && $user->canManagePusat())) {
            abort(403, 'Anda tidak memiliki akses ke bahan baku ini.');
        }
        
        $movements = collect(); // Placeholder untuk stock movements
        
        return view('admin.ingredients.show', compact('ingredient', 'movements'));
    }

    /**
     * Show form to edit ingredient.
     */
    public function edit($id)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        $userBranchId = $user->branch_id;
        
        $ingredient = Ingredient::findOrFail($id);
        
        // Cek akses: non-super admin hanya bisa edit bahan baku di cabangnya sendiri
        $pusat = Branch::where('code', 'PUSAT')->first();
        $isPusat = $ingredient->branch_id == $pusat?->id;
        
        if (!$isSuperAdmin && $ingredient->branch_id != $userBranchId && !($isPusat && $user->canManagePusat())) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit bahan baku ini.');
        }
        
        $units = Unit::all();
        
        // Branches: super admin bisa pilih cabang, admin biasa hanya cabang sendiri
        if ($isSuperAdmin) {
            $branches = Branch::where('is_active', true)->get();
        } elseif ($user->canManagePusat()) {
            $pusat = Branch::where('code', 'PUSAT')->first();
            $branches = Branch::where('id', $userBranchId)
                ->orWhere('id', $pusat?->id)
                ->where('is_active', true)->get();
        } else {
            $branches = Branch::where('id', $userBranchId)->where('is_active', true)->get();
        }
        
        return view('admin.ingredients.edit', compact('ingredient', 'units', 'branches', 'isSuperAdmin', 'userBranchId'));
    }

    /**
     * Update ingredient.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        $userBranchId = $user->branch_id;
        
        $ingredient = Ingredient::findOrFail($id);
        
        // Cek akses: non-super admin hanya bisa update bahan baku di cabangnya sendiri
        if (!$isSuperAdmin && $ingredient->branch_id != $userBranchId) {
            abort(403, 'Anda tidak memiliki akses untuk mengupdate bahan baku ini.');
        }
        
        $rules = [
            'name' => 'required|string|max:255|unique:ingredients,name,' . $id,
            'unit_id' => 'nullable|exists:units,id',
            'current_stock' => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
            'unit_cost' => 'nullable|numeric|min:0',
        ];
        
        // Super admin bisa mengubah cabang
        if ($isSuperAdmin) {
            $rules['branch_id'] = 'required|exists:branches,id';
        }
        
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $data = $request->all();
        
                // Jika bukan super admin, branch_id tidak boleh berubah ke cabang lain
        if (!$isSuperAdmin) {
            if ($user->canManagePusat() && isset($data['branch_id'])) {
                $pusat = Branch::where('code', 'PUSAT')->first();
                if ($data['branch_id'] != $pusat?->id && $data['branch_id'] != $userBranchId) {
                    unset($data['branch_id']);
                }
            } else {
                unset($data['branch_id']);
            }
        }
        
        $data['unit_cost'] = $data['unit_cost'] ?? 0;
        $ingredient->update($data);
        
        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Bahan baku berhasil diperbarui');
    }

    /**
     * Delete ingredient.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        $userBranchId = $user->branch_id;
        
        $ingredient = Ingredient::findOrFail($id);
        
        // Cek akses: non-super admin hanya bisa hapus bahan baku di cabangnya sendiri
        $pusat = Branch::where('code', 'PUSAT')->first();
        $isPusat = $ingredient->branch_id == $pusat?->id;
        
        if (!$isSuperAdmin && $ingredient->branch_id != $userBranchId && !($isPusat && $user->canManagePusat())) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus bahan baku ini.');
        }
        
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
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        $userBranchId = $user->branch_id;
        
        $ingredient = Ingredient::findOrFail($id);
        
        // Cek akses: non-super admin hanya bisa adjust stok di cabangnya sendiri
        $pusat = Branch::where('code', 'PUSAT')->first();
        $isPusat = $ingredient->branch_id == $pusat?->id;
        
        if (!$isSuperAdmin && $ingredient->branch_id != $userBranchId && !($isPusat && $user->canManagePusat())) {
            return response()->json(['error' => 'Anda tidak memiliki akses ke bahan baku ini.'], 403);
        }
        
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
        
        return response()->json([
            'success' => true,
            'message' => 'Stok berhasil disesuaikan',
            'new_stock' => $newStock,
            'old_stock' => $oldStock,
        ]);
    }

    /**
     * Quick adjust stock for opname.
     */
    public function quickUpdateStock(\Illuminate\Http\Request $request, $id)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        $userBranchId = $user->branch_id;
        
        $ingredient = \App\Models\Ingredient::findOrFail($id);
        
        $pusat = \App\Models\Branch::where('code', 'PUSAT')->first();
        $isPusat = $ingredient->branch_id == $pusat?->id;
        
        if (!$isSuperAdmin && $ingredient->branch_id != $userBranchId && !($isPusat && $user->canManagePusat())) {
            return response()->json(['error' => 'Anda tidak memiliki akses.'], 403);
        }
        
        $request->validate([
            'current_stock' => 'required|numeric|min:0',
        ]);
        
        $ingredient->current_stock = $request->current_stock;
        $ingredient->save();
        
        return redirect()->back()->with('success', "Stok {$ingredient->name} berhasil disesuaikan menjadi {$request->current_stock}");
    }
}