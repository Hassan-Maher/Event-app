<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->get();

        $totalproducts = Product::count();

        return view('admin.products' , compact('products' , 'totalproducts'));
    }

    public function show($product_id)
    {
        $product = Product::with(['store.provider', 'service', 'city', 'image', 'options'])->findOrFail($product_id);

        return view('admin.productShow' , compact('product'));
    }
}

