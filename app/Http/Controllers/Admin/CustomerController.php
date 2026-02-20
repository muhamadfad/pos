<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::withCount('orders');

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('phone', 'like', '%' . $request->search . '%')
                ->orWhere('address', 'like', '%' . $request->search . '%');
        }

        $customers = $query->latest()->paginate(20);
        $total = Customer::count();

        if ($request->ajax()) {
            return view('admin.customers.partials.table', compact('customers'))->render();
        }

        return view('admin.customers.index', compact('customers', 'total'));
    }

    public function show(Customer $customer)
    {
        $orders = $customer->orders()
            ->with('items.product')
            ->latest()
            ->paginate(15);
        $totalTransaksi   = $customer->orders()->count();
        $totalPembelian   = $customer->orders()->sum('total_amount');
        $totalPiutang     = $customer->orders()->where('payment_status', '!=', 'paid')->sum('remaining_receivable');
        return view('admin.customers.show', compact('customer', 'orders', 'totalTransaksi', 'totalPembelian', 'totalPiutang'));
    }

    public function create()
    {
        return view('admin.customers.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'type' => 'required|in:retail,store,project,other',
        ]);

        Customer::create($validated);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer berhasil ditambahkan');
    }

    public function edit(Customer $customer)
    {
        return view('admin.customers.form', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'type' => 'required|in:retail,store,project,other',
        ]);

        $customer->update($validated);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer berhasil diperbarui');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer berhasil dihapus');
    }
}
