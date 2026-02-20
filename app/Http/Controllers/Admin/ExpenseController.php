<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('user')->latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                    ->orWhere('category', 'like', '%' . $request->search . '%')
                    ->orWhere('reference', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->category) {
            $query->where('category', $request->category);
        }

        if ($request->date_from) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $expenses  = $query->paginate(15)->withQueryString();
        $totalAll  = Expense::sum('amount');
        $totalMonth = Expense::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');
        $totalToday = Expense::whereDate('date', today())->sum('amount');

        $categories = ['operational', 'salary', 'utility', 'transport', 'other'];

        if ($request->ajax()) {
            return view('admin.expenses.partials.table', compact('expenses'))->render();
        }

        return view('admin.expenses.index', compact(
            'expenses',
            'totalAll',
            'totalMonth',
            'totalToday',
            'categories'
        ));
    }

    public function create()
    {
        $categories = ['operational', 'salary', 'utility', 'transport', 'other'];
        return view('admin.expenses.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount'      => 'required|numeric|min:1',
            'date'        => 'required|date',
            'category'    => 'nullable|string|max:100',
            'reference'   => 'nullable|string|max:100',
            'notes'       => 'nullable|string',
        ]);

        Expense::create([
            'user_id'     => Auth::id(),
            'description' => $request->description,
            'amount'      => $request->amount,
            'date'        => $request->date,
            'category'    => $request->category,
            'reference'   => $request->reference,
            'notes'       => $request->notes,
        ]);

        return redirect()
            ->route('admin.expenses.index')
            ->with('success', 'Pengeluaran berhasil dicatat.');
    }

    public function edit(Expense $expense)
    {
        $categories = ['operational', 'salary', 'utility', 'transport', 'other'];
        return view('admin.expenses.form', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount'      => 'required|numeric|min:1',
            'date'        => 'required|date',
            'category'    => 'nullable|string|max:100',
            'reference'   => 'nullable|string|max:100',
            'notes'       => 'nullable|string',
        ]);

        $expense->update([
            'description' => $request->description,
            'amount'      => $request->amount,
            'date'        => $request->date,
            'category'    => $request->category,
            'reference'   => $request->reference,
            'notes'       => $request->notes,
        ]);

        return redirect()
            ->route('admin.expenses.index')
            ->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()
            ->route('admin.expenses.index')
            ->with('success', 'Pengeluaran berhasil dihapus.');
    }
}
