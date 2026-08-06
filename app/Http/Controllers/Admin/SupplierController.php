<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::withCount('purchases');

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('address', 'like', '%' . $request->search . '%')
                ->orWhere('phone', 'like', '%' . $request->search . '%');
        }

        $suppliers = $query->latest()->paginate(10);
        $total = Supplier::count();

        if ($request->ajax()) {
            return view('admin.suppliers.partials.table', compact('suppliers'))->render();
        }

        return view('admin.suppliers.index', compact('suppliers', 'total'));
    }

    public function show($id)
    {
        $supplier = Supplier::findOrFail($id);
        $purchases = $supplier->purchases()
            ->with('items.ingredient')
            ->latest()
            ->paginate(15);
        $totalTransaksi  = $supplier->purchases()->count();
        $totalPembelian  = $supplier->purchases()->sum('total_amount');
        $totalHutang     = $supplier->purchases()->whereIn('payment_status', ['unpaid', 'partial'])->sum('remaining_debt');
        return view('admin.suppliers.show', compact('supplier', 'purchases', 'totalTransaksi', 'totalPembelian', 'totalHutang'));
    }

    public function create()
    {
        return view('admin.suppliers.form');
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('admin.suppliers.form', compact('supplier'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        Supplier::create($request->all());

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->update($request->all());

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier berhasil dihapus.');
    }
}
