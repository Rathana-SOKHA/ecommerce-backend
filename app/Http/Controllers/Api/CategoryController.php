<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use OpenApi\Attributes as OA;

class CategoryController extends Controller
{
    #[OA\Get(
        path: '/api/categories',
        summary: 'List categories',
        description: 'Get all active categories (public).',
        tags: ['Product'],
        responses: [
            new OA\Response(response: 200, description: 'List of categories'),
        ]
    )]
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => Category::where('status', 1)->paginate(10)
        ]);
    }
}