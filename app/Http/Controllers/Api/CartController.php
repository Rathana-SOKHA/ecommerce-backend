<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CartController extends Controller
{
    #[OA\Get(
        path: '/api/cart',
        summary: 'View cart',
        description: 'Get all items in the authenticated user\'s cart with total amount.',
        security: [['sanctum' => []]],
        tags: ['Cart'],
        responses: [
            new OA\Response(response: 200, description: 'Cart items with total'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
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

    #[OA\Post(
        path: '/api/cart',
        summary: 'Add to cart',
        description: 'Add a product to the cart or update quantity if already exists.',
        security: [['sanctum' => []]],
        tags: ['Cart'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['product_id', 'quantity'],
                properties: [
                    new OA\Property(property: 'product_id', type: 'integer', example: 1),
                    new OA\Property(property: 'quantity', type: 'integer', example: 2),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Added to cart or cart updated'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error or insufficient stock'),
        ]
    )]
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

    #[OA\Put(
        path: '/api/cart/{cart}',
        summary: 'Update cart item',
        description: 'Update the quantity of a cart item.',
        security: [['sanctum' => []]],
        tags: ['Cart'],
        parameters: [
            new OA\Parameter(name: 'cart', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['quantity'],
                properties: [
                    new OA\Property(property: 'quantity', type: 'integer', example: 3),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Quantity updated'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 422, description: 'Validation error or insufficient stock'),
        ]
    )]
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

    #[OA\Delete(
        path: '/api/cart/{cart}',
        summary: 'Remove cart item',
        description: 'Remove an item from the cart.',
        security: [['sanctum' => []]],
        tags: ['Cart'],
        parameters: [
            new OA\Parameter(name: 'cart', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Item removed'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden'),
        ]
    )]
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
