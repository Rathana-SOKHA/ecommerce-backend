<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // GET ALL PRODUCTS
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => Product::with('category')
                ->where('status', 1)
                ->latest()
                ->get()
        ]);
    }

    // GET ONE PRODUCT
    public function show($id)
    {
        $product = Product::with('category')->find($id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $product
        ]);
    }

    // SEARCH PRODUCTS
    public function search(Request $request)
    {
        $query = $request->query('q');

        $products = Product::with('category')
            ->where('name', 'LIKE', "%{$query}%")
            ->get();

        return response()->json([
            'status' => true,
            'data' => $products
        ]);
    }
}