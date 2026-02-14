<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
        {
            $query = Product::with('category');

            // Search
            if ($request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('barcode', 'like', '%' . $request->search . '%');
                });
            }

            // Filter kategori
            if ($request->category) {
                $query->where('category_id', $request->category);
            }

            // Sorting
            if ($request->sort) {
                if ($request->sort === 'price') {
                    $query->orderBy('price', 'asc');
                } elseif ($request->sort === 'stock') {
                    $query->orderBy('stock', 'asc');
                }
            }

            // Stok habis di atas
            $query->orderByRaw("stock = 0 DESC");

            $perPage = $request->per_page ?? 10;

            $products = $query->paginate($perPage)->withQueryString();
            $categories = Category::orderBy('name')->get();

            // Kalau AJAX request
            if ($request->ajax()) {
                return view('admin.products.partials.table', compact('products'))->render();
            }

            return view('admin.products.index', compact('products', 'categories'));
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
