<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\StockEntry;
use App\Models\StockOut;

class TransactionController extends Controller
{
    // ======= Stock In =======
    public function indexIn()
    {
        $entries = StockEntry::with('items.product')->latest()->get();
        return view('admin.transactions.in.index', compact('entries'));
    }

    public function createIn()
    {
        $products = Product::all();
        return view('admin.transactions.in.create', compact('products'));
    }

    public function storeIn(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'date' => 'required|date',
        ]);

        StockEntry::create($request->all());

        $product = Product::find($request->product_id);
        $product->increment('stock', $request->quantity);

        return redirect()->route('admin.transactions.in.index')->with('success', 'Stock entry added successfully.');
    }

    // ======= Stock Out =======
    public function indexOut()
    {
        $entries = StockOut::with('product')->latest()->get();
        return view('admin.transactions.out.index', compact('entries'));
    }

    public function createOut()
    {
        $products = Product::all();
        return view('admin.transactions.out.create', compact('products'));
    }

    public function storeOut(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'date' => 'required|date',
        ]);

        $product = Product::find($request->product_id);
        if ($request->quantity > $product->stock) {
            return back()->withErrors(['quantity' => 'Stok tidak cukup']);
        }

        StockOut::create($request->all());

        $product->decrement('stock', $request->quantity);

        return redirect()->route('admin.transactions.out.index')->with('success', 'Stock out added successfully.');
    }
}
