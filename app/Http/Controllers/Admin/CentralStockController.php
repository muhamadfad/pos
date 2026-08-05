<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class CentralStockController extends Controller
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

        $query = Ingredient::with('unit')->where('branch_id', $pusat->id);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $ingredients = $query->orderBy('name')->paginate($request->per_page ?? 15);

        return view('admin.central-stocks.index', compact('ingredients', 'pusat'));
    }
}
