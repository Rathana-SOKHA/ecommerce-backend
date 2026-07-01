<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ReviewController extends Controller
{
    #[OA\Get(
        path: '/api/products/{id}/reviews',
        summary: 'Get product reviews',
        description: 'Get paginated reviews for a specific product (public).',
        tags: ['Review'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Product reviews'),
            new OA\Response(response: 404, description: 'Product not found'),
        ]
    )]
    public function index($productId)
    {
        $product = Product::findOrFail($productId);

        $reviews = Review::with('user')
            ->where('product_id', $productId)
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => true,
            'product' => $product->name,
            'data' => $reviews
        ]);
    }

    #[OA\Post(
        path: '/api/products/{product}/reviews',
        summary: 'Create review',
        description: 'Submit a product review with rating (authenticated).',
        security: [['sanctum' => []]],
        tags: ['Review'],
        parameters: [
            new OA\Parameter(name: 'product', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['product_id', 'rating'],
                properties: [
                    new OA\Property(property: 'product_id', type: 'integer', example: 1),
                    new OA\Property(property: 'rating', type: 'integer', minimum: 1, maximum: 5, example: 4),
                    new OA\Property(property: 'comment', type: 'string', example: 'Great product!', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Review submitted successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 409, description: 'Already reviewed this product'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
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