<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // VIEW CART
    public function index(Request $request)
    {
        $cart = Cart::with('product')
            ->where('user_id', $request->user()->id)
            ->get();

        $total = 0;

        foreach ($cart as $item) {
            $total += $item->product->price * $item->quantity;
        }

        return response()->json([
            'status' => true,
            'total' => $total,
            'data' => $cart
        ]);
    }

    // ADD TO CART
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($request->quantity > $product->stock) {
            return response()->json([
                'status' => false,
                'message' => 'Insufficient stock'
            ], 422);
        }

        $cart = Cart::where('user_id', $request->user()->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cart) {

            $newQuantity = $cart->quantity + $request->quantity;

            if ($newQuantity > $product->stock) {
                return response()->json([
                    'status' => false,
                    'message' => 'Insufficient stock'
                ], 422);
            }

            $cart->update([
                'quantity' => $newQuantity
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Cart updated'
            ]);
        }

        Cart::create([
            'user_id' => $request->user()->id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Added to cart'
        ]);
    }

    // UPDATE QUANTITY
    public function update(Request $request, Cart $cart)
    {
        if ($cart->user_id !== $request->user()->id) {
            abort(403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $product = $cart->product;

        if ($request->quantity > $product->stock) {
            return response()->json([
                'status' => false,
                'message' => 'Insufficient stock'
            ], 422);
        }

        $cart->update([
            'quantity' => $request->quantity
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Quantity updated'
        ]);
    }

    // REMOVE ITEM
    public function destroy(Request $request, Cart $cart)
    {
        if ($cart->user_id !== $request->user()->id) {
            abort(403);
        }

        $cart->delete();

        return response()->json([
            'status' => true,
            'message' => 'Item removed'
        ]);
    }
}
