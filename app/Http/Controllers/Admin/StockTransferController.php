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

class StockTransferController extends Controller
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
        $transfers = StockTransfer::with(['toBranch', 'user'])
            ->orderBy('transfer_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('admin.stock-transfers.index', compact('transfers'));
    }

    public function create()
    {
        $pusat = $this->getPusatBranch();
        
        // Hanya cabang selain pusat
        $branches = Branch::where('id', '!=', $pusat->id)->where('is_active', true)->orderBy('name')->get();
        
        // Ambil stok bahan yang ada di pusat
        $ingredients = Ingredient::with('unit')->where('branch_id', $pusat->id)->orderBy('name')->get();

        return view('admin.stock-transfers.create', compact('branches', 'ingredients', 'pusat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'to_branch_id' => 'required|exists:branches,id',
            'transfer_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.ingredient_id' => 'required|exists:ingredients,id',
            'items.*.quantity' => 'required|numeric|min:0.1',
        ]);

        $pusat = $this->getPusatBranch();

        DB::transaction(function () use ($request, $pusat) {
            // Create transfer record
            $transfer = StockTransfer::create([
                'reference_number' => 'TRF-' . date('Ymd') . '-' . rand(1000, 9999),
                'from_branch_id' => $pusat->id,
                'to_branch_id' => $request->to_branch_id,
                'user_id' => Auth::id(),
                'transfer_date' => $request->transfer_date,
                'notes' => $request->notes,
                'status' => 'completed',
            ]);

            foreach ($request->items as $item) {
                // Kurangi stok pusat
                $pusatIngredient = Ingredient::where('id', $item['ingredient_id'])->where('branch_id', $pusat->id)->firstOrFail();
                
                if ($pusatIngredient->current_stock < $item['quantity']) {
                    throw new \Exception("Stok {$pusatIngredient->name} di Gudang Pusat tidak mencukupi. (Sisa: {$pusatIngredient->current_stock})");
                }

                $pusatIngredient->decrement('current_stock', $item['quantity']);

                // Tambahkan stok ke cabang (Cari atau buat Ingredient di cabang tujuan dengan nama yang sama)
                $branchIngredient = Ingredient::firstOrCreate(
                    [
                        'name' => $pusatIngredient->name,
                        'branch_id' => $request->to_branch_id
                    ],
                    [
                        'unit_id' => $pusatIngredient->unit_id,
                        'current_stock' => 0,
                        'min_stock' => $pusatIngredient->min_stock,
                        'unit_cost' => $pusatIngredient->unit_cost,
                    ]
                );

                // Update rata-rata harga (Moving Average)
                $oldTotal = $branchIngredient->current_stock * ($branchIngredient->unit_cost ?? 0);
                $newTotal = $item['quantity'] * ($pusatIngredient->unit_cost ?? 0);
                
                $totalQty = $branchIngredient->current_stock + $item['quantity'];
                $averagePrice = $totalQty > 0 ? ($oldTotal + $newTotal) / $totalQty : 0;

                $branchIngredient->increment('current_stock', $item['quantity']);
                $branchIngredient->update(['unit_cost' => $averagePrice]);

                // Record detail mutasi
                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'ingredient_id' => $pusatIngredient->id,
                    'quantity' => $item['quantity'],
                ]);
            }
        });

        return redirect()->route('admin.stock-transfers.index')->with('success', 'Berhasil mendistribusikan stok ke cabang!');
    }

    public function show($id)
    {
        $transfer = StockTransfer::with(['toBranch', 'user', 'items.ingredient'])->findOrFail($id);
        return view('admin.stock-transfers.show', compact('transfer'));
    }
}
