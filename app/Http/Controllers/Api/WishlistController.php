<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    // GET USER WISHLIST
    public function index(Request $request)
    {
        $wishlist = Wishlist::with('product')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $wishlist
        ]);
    }

    // ADD TO WISHLIST
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => [
                'required',
                'exists:products,id'
            ]
        ]);

        $exists = Wishlist::where(
            'user_id',
            $request->user()->id
        )
        ->where(
            'product_id',
            $request->product_id
        )
        ->exists();

        if ($exists) {

            return response()->json([
                'status' => false,
                'message' => 'Product already in wishlist'
            ], 409);
        }

        Wishlist::create([
            'user_id' => $request->user()->id,
            'product_id' => $request->product_id
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Added to wishlist'
        ]);
    }

    // REMOVE FROM WISHLIST
    public function destroy(Request $request, $productId)
    {
        Wishlist::where(
            'user_id',
            $request->user()->id
        )
        ->where(
            'product_id',
            $productId
        )
        ->delete();

        return response()->json([
            'status' => true,
            'message' => 'Removed from wishlist'
        ]);
    }
}