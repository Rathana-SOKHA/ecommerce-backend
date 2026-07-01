<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    #[OA\Get(
        path: '/api/products',
        summary: 'List products',
        description: 'Get all active products with category (public, paginated).',
        tags: ['Product'],
        parameters: [
            new OA\Parameter(
                name: 'page',
                in: 'query',
                description: 'Page number',
                required: false,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated list of products'),
        ]
    )]
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => Product::with('category')
                ->where('status', 1)
                ->latest()
                ->paginate(10)
        ]);
    }

    #[OA\Get(
        path: '/api/products/{id}',
        summary: 'Get product',
        description: 'Get a single product by ID with category (public).',
        tags: ['Product'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Product details'),
            new OA\Response(response: 404, description: 'Product not found'),
        ]
    )]
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

    #[OA\Get(
        path: '/api/products-search',
        summary: 'Search products',
        description: 'Search products by name (public, paginated).',
        tags: ['Product'],
        parameters: [
            new OA\Parameter(
                name: 'q',
                in: 'query',
                description: 'Search query',
                required: true,
                schema: new OA\Schema(type: 'string', example: 'shirt')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated search results'),
        ]
    )]
    public function search(Request $request)
    {
        $query = $request->query('q');

        $products = Product::with('category')
            ->where('name', 'LIKE', "%{$query}%")
            ->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $products
        ]);
    }
}
