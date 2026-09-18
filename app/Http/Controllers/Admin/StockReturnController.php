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

        return view('admin.stock-returns.create', compact('branches', 'ingredients', 'pusat'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $pusat = $this->getPusatBranch();
        $request->validate([
            'from_branch_id' => 'required|exists:branches,id',
            'transfer_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.ingredient_id' => 'required|exists:ingredients,id',
            'items.*.quantity' => 'required|numeric|min:0.1',
            'items.*.notes' => 'nullable|string',
        ]);

        $pusat = $this->getPusatBranch();

        DB::transaction(function () use ($request, $pusat) {
            // Create return record (from branch to pusat)
            $transfer = StockTransfer::create([
                'reference_number' => 'RET-' . date('Ymd') . '-' . rand(1000, 9999),
                'from_branch_id' => $request->from_branch_id,
                'to_branch_id' => $pusat->id,
                'user_id' => Auth::id(),
                'transfer_date' => $request->transfer_date,
                'notes' => $request->notes,
                'status' => 'completed',
            ]);

            foreach ($request->items as $item) {
                // Kurangi stok cabang
                $branchIngredient = Ingredient::where('id', $item['ingredient_id'])->where('branch_id', $request->from_branch_id)->firstOrFail();
                
                if ($branchIngredient->current_stock < $item['quantity']) {
                    throw new \Exception("Stok {$branchIngredient->name} di cabang tidak mencukupi. (Sisa: {$branchIngredient->current_stock})");
                }

                $branchIngredient->decrement('current_stock', $item['quantity']);

                // Tambahkan stok ke Gudang Pusat (Cari atau buat Ingredient di gudang pusat dengan nama yang sama)
                $pusatIngredient = Ingredient::firstOrCreate(
                    [
                        'name' => $branchIngredient->name,
                        'branch_id' => $pusat->id
                    ],
                    [
                        'unit_id' => $branchIngredient->unit_id,
                        'current_stock' => 0,
                        'min_stock' => $branchIngredient->min_stock,
                        'unit_cost' => $branchIngredient->unit_cost,
                    ]
                );

                // Update rata-rata harga (Moving Average)
                $oldTotal = $pusatIngredient->current_stock * ($pusatIngredient->unit_cost ?? 0);
                $newTotal = $item['quantity'] * ($branchIngredient->unit_cost ?? 0);
                
                $totalQty = $pusatIngredient->current_stock + $item['quantity'];
                $averagePrice = $totalQty > 0 ? ($oldTotal + $newTotal) / $totalQty : 0;

                $pusatIngredient->increment('current_stock', $item['quantity']);
                $pusatIngredient->update(['unit_cost' => $averagePrice]);

                // Record detail mutasi
                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'ingredient_id' => $branchIngredient->id, // source ingredient
                    'quantity' => $item['quantity'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }
        });

        return redirect()->route('admin.stock-returns.index')->with('success', 'Berhasil melakukan return stok dari cabang ke pusat!');
    }

    public function show($id)
    {
        $return = StockTransfer::with(['fromBranch', 'user', 'items.ingredient'])->findOrFail($id);
        return view('admin.stock-returns.show', compact('return'));
    }
}
