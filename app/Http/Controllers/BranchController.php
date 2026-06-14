<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\User;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\BranchStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    /**
     * Display a listing of branches.
     */
    public function index(Request $request)
    {
        $query = Branch::query();
        
        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }
        
        // Status filter
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }
        
        $branches = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Get statistics for each branch
        foreach ($branches as $branch) {
            $branch->stats = [
                'total_users' => User::where('branch_id', $branch->id)->count(),
                'total_orders' => Order::where('branch_id', $branch->id)->count(),
                'total_revenue' => Order::where('branch_id', $branch->id)->sum('total_amount') ?? 0,
                'total_purchases' => Purchase::where('branch_id', $branch->id)->count(),
                'total_products' => BranchStock::where('branch_id', $branch->id)->distinct('ingredient_id')->count('ingredient_id'),
                'low_stock_count' => BranchStock::where('branch_id', $branch->id)
                    ->whereColumn('quantity', '<=', 'min_stock')
                    ->where('quantity', '>', 0)
                    ->count(),
            ];
        }
        
        return view('super.branches.index', compact('branches'));
    }

    /**
     * Show form to create new branch.
     */
    public function create()
    {
        return view('super.branches.create');
    }

    /**
     * Store a newly created branch.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:branches,code',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $branch = Branch::create([
            'code' => $request->code,
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'is_active' => $request->is_active ?? 1,
        ]);
        
        return redirect()->route('super.branches.index')
            ->with('success', "Cabang '{$branch->name}' berhasil ditambahkan");
    }

    /**
     * Display branch details.
     */
    public function show($id)
    {
        $branch = Branch::findOrFail($id);
        
        // Get detailed statistics
        $stats = [
            'users' => User::where('branch_id', $branch->id)->get(),
            'recent_orders' => Order::where('branch_id', $branch->id)
                ->with('customer')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
            'recent_purchases' => Purchase::where('branch_id', $branch->id)
                ->with('supplier')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
            'top_products' => DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('orders.branch_id', $branch->id)
                ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
                ->groupBy('products.id', 'products.name')
                ->orderBy('total_sold', 'desc')
                ->limit(5)
                ->get(),
            'monthly_revenue' => Order::where('branch_id', $branch->id)
                ->select(DB::raw('MONTH(date) as month'), DB::raw('SUM(total_amount) as total'))
                ->whereYear('date', now()->year)
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray(),
        ];
        
        return view('super.branches.show', compact('branch', 'stats'));
    }

    /**
     * Show form to edit branch.
     */
    public function edit($id)
    {
        $branch = Branch::findOrFail($id);
        return view('super.branches.edit', compact('branch'));
    }

    /**
     * Update branch information.
     */
    public function update(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:branches,code,' . $branch->id,
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $branch->update([
            'code' => $request->code,
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'is_active' => $request->is_active ?? $branch->is_active,
        ]);
        
        return redirect()->route('super.branches.index')
            ->with('success', "Cabang '{$branch->name}' berhasil diperbarui");
    }

    /**
     * Delete branch.
     */
    public function destroy($id)
    {
        $branch = Branch::findOrFail($id);
        
        // Check if branch has users
        $userCount = User::where('branch_id', $branch->id)->count();
        if ($userCount > 0) {
            return redirect()->back()->with('error', "Cabang tidak dapat dihapus karena masih memiliki {$userCount} user. Pindahkan user terlebih dahulu.");
        }
        
        // Check if branch has orders
        $orderCount = Order::where('branch_id', $branch->id)->count();
        if ($orderCount > 0) {
            return redirect()->back()->with('error', "Cabang tidak dapat dihapus karena masih memiliki {$orderCount} transaksi.");
        }
        
        $branchName = $branch->name;
        $branch->delete();
        
        return redirect()->route('super.branches.index')
            ->with('success', "Cabang '{$branchName}' berhasil dihapus");
    }

    /**
     * Toggle branch active status.
     */
    public function toggleStatus($id)
    {
        $branch = Branch::findOrFail($id);
        $branch->is_active = !$branch->is_active;
        $branch->save();
        
        $status = $branch->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Cabang '{$branch->name}' berhasil {$status}");
    }

    /**
     * Get branch statistics (AJAX).
     */
    public function stats($id)
    {
        $branch = Branch::findOrFail($id);
        
        $stats = [
            'id' => $branch->id,
            'name' => $branch->name,
            'code' => $branch->code,
            'total_users' => User::where('branch_id', $branch->id)->count(),
            'total_orders' => Order::where('branch_id', $branch->id)->count(),
            'total_revenue' => Order::where('branch_id', $branch->id)->sum('total_amount') ?? 0,
            'total_purchases' => Purchase::where('branch_id', $branch->id)->count(),
            'total_expenses' => DB::table('expenses')->where('branch_id', $branch->id)->sum('amount') ?? 0,
            'low_stock_count' => BranchStock::where('branch_id', $branch->id)
                ->whereColumn('quantity', '<=', 'min_stock')
                ->where('quantity', '>', 0)
                ->count(),
            'out_of_stock_count' => BranchStock::where('branch_id', $branch->id)
                ->where('quantity', 0)
                ->count(),
        ];
        
        return response()->json($stats);
    }
}