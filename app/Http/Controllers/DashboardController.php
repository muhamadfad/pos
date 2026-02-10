<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Total produk
        $totalProducts = Product::count();

        // Produk dengan stok <= 5 dianggap menipis
        $lowStock = Product::where('stock', '<=', 5)->count();

        // Total user
        $totalUsers = User::count();

        return view('dashboard', compact('totalProducts', 'lowStock', 'totalUsers'));
    }
}
