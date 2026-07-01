<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class OrderController extends Controller
{
    #[OA\Get(
        path: '/api/orders',
        summary: 'Order history',
        description: 'Get paginated list of orders for the authenticated user.',
        security: [['sanctum' => []]],
        tags: ['Order'],
        responses: [
            new OA\Response(response: 200, description: 'Paginated list of orders'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function index(Request $request)
    {
        $orders = Order::where(
                'user_id',
                $request->user()->id
            )
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }

    #[OA\Get(
        path: '/api/orders/{order}',
        summary: 'Order detail',
        description: 'Get a single order with its items.',
        security: [['sanctum' => []]],
        tags: ['Order'],
        parameters: [
            new OA\Parameter(name: 'order', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Order details with items'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Order not found'),
        ]
    )]
    public function show(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $order->load([
            'items.product'
        ]);

        return response()->json([
            'status' => true,
            'data' => $order
        ]);
    }
}
