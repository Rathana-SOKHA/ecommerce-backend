<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Get reviews of a product
     */
    public function index($productId)
    {
        $product = Product::findOrFail($productId);

        $reviews = Review::with('user')
            ->where('product_id', $productId)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'product' => $product->name,
            'data' => $reviews
        ]);
    }

    /**
     * Create review
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        $user = $request->user();

        // Check duplicate review
        $exists = Review::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'You already reviewed this product'
            ], 409);
        }

        $review = Review::create([
            'user_id' => $user->id,
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Review submitted successfully',
            'data' => $review
        ]);
    }
}