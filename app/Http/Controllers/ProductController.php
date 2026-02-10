<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', [
            'categories' => $categories,
            'isEdit' => false
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'barcode' => 'required|unique:products,barcode',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'purchase_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        Product::create([
            'barcode' => $request->barcode,
            'name' => $request->name,
            'category_id' => $request->category_id,
            'purchase_price' => $request->purchase_price,
            'price' => $request->price,
            'stock' => $request->stock,
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Barang berhasil ditambahkan');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.create', [
            'product' => $product,
            'categories' => $categories,
            'isEdit' => true
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'barcode' => 'required|unique:products,barcode,' . $product->id,
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'purchase_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $product->update([
            'barcode' => $request->barcode,
            'name' => $request->name,
            'category_id' => $request->category_id,
            'purchase_price' => $request->purchase_price,
            'price' => $request->price,
            'stock' => $request->stock,
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Barang berhasil diperbarui');
    }

    public function getByBarcode($barcode)
    {
        $product = Product::where('barcode', $barcode)->first();

        if (!$product) {
            return response()->json(null, 404);
        }

        return response()->json([
            'name' => $product->name,
            'price' => $product->price,
            'purchase_price' => $product->purchase_price,
            'category_id' => $product->category_id,
        ]);
    }
}
