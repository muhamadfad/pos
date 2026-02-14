<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
{
    $query = Category::query();

    if ($request->search) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    $categories = $query->latest()->paginate(10);

    if ($request->ajax()) {
        return view('admin.categories.partials.table', compact('categories'))->render();
    }

    $total = Category::count();

    return view('admin.categories.index', compact('categories','total'));
}


    public function create()
    {
        return view('admin.categories.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
        ]);

        Category::create($request->only('name'));

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.form', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
        ]);

        $category->update($request->only('name'));

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
