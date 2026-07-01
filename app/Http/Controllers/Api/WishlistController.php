<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class WishlistController extends Controller
{
    #[OA\Get(
        path: '/api/wishlist',
        summary: 'Get wishlist',
        description: 'Get all products in the authenticated user\'s wishlist.',
        security: [['sanctum' => []]],
        tags: ['Wishlist'],
        responses: [
            new OA\Response(response: 200, description: 'Wishlist items'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function index(Request $request)
    {
        $wishlist = Wishlist::with('product')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $wishlist
        ]);
    }

    #[OA\Post(
        path: '/api/wishlist',
        summary: 'Add to wishlist',
        description: 'Add a product to the wishlist.',
        security: [['sanctum' => []]],
        tags: ['Wishlist'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['product_id'],
                properties: [
                    new OA\Property(property: 'product_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Added to wishlist'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 409, description: 'Product already in wishlist'),
        ]
    )]
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

    #[OA\Delete(
        path: '/api/wishlist/{productId}',
        summary: 'Remove from wishlist',
        description: 'Remove a product from the wishlist by product ID.',
        security: [['sanctum' => []]],
        tags: ['Wishlist'],
        parameters: [
            new OA\Parameter(name: 'productId', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Removed from wishlist'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
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