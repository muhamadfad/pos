<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\PurchaseItem;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'unit']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('barcode', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->sort === 'price') {
            $query->orderBy('selling_price', 'asc');
        } elseif ($request->sort === 'stock') {
            $query->orderBy('stock', 'asc');
        }

        $query->orderByRaw('stock = 0 DESC')->orderBy('name');

        $perPage = $request->per_page ?? 10;
        $products = $query->paginate($perPage)->withQueryString();
        $categories = Category::orderBy('name')->get();

        if ($request->ajax()) {
            return view('admin.products.partials.table', compact('products'))->render();
        }

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();

        return view('admin.products.create', [
            'categories' => $categories,
            'units'      => $units,
            'isEdit'     => false,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'barcode'        => 'nullable|unique:products,barcode',
            'name'           => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id',
            'unit_id'        => 'nullable|exists:units,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price'  => 'required|numeric|min:0',
            'stock'          => 'required|integer|min:0',
            'min_stock'      => 'nullable|integer|min:0',
        ]);

        Product::create([
            'barcode'        => $request->barcode,
            'name'           => $request->name,
            'category_id'    => $request->category_id,
            'unit_id'        => $request->unit_id,
            'purchase_price' => $request->purchase_price,
            'selling_price'  => $request->selling_price,
            'stock'          => $request->stock,
            'min_stock'      => $request->min_stock ?? 0,
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'unit']);

        $incoming = PurchaseItem::with(['purchase.supplier'])
            ->where('product_id', $product->id)
            ->latest()
            ->limit(15)
            ->get();

        $outgoing = OrderItem::with(['order.customer'])
            ->where('product_id', $product->id)
            ->latest()
            ->limit(15)
            ->get();

        return view('admin.products.show', compact('product', 'incoming', 'outgoing'));
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();

        return view('admin.products.create', [
            'product'    => $product,
            'categories' => $categories,
            'units'      => $units,
            'isEdit'     => true,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'barcode'        => 'nullable|unique:products,barcode,' . $product->id,
            'name'           => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id',
            'unit_id'        => 'nullable|exists:units,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price'  => 'required|numeric|min:0',
            'stock'          => 'required|integer|min:0',
            'min_stock'      => 'nullable|integer|min:0',
        ]);

        $product->update([
            'barcode'        => $request->barcode,
            'name'           => $request->name,
            'category_id'    => $request->category_id,
            'unit_id'        => $request->unit_id,
            'purchase_price' => $request->purchase_price,
            'selling_price'  => $request->selling_price,
            'stock'          => $request->stock,
            'min_stock'      => $request->min_stock ?? 0,
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus');
    }

    public function getByBarcode($barcode)
    {
        $product = Product::where('barcode', $barcode)->first();

        if (sortproduct) {
            return response()->json(null, 404);
        }

        return response()->json([
            'id'             => $product->id,
            'name'           => $product->name,
            'selling_price'  => $product->selling_price,
            'purchase_price' => $product->purchase_price,
            'category_id'    => $product->category_id,
            'stock'          => $product->stock,
        ]);
    }
}
