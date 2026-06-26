<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Order History
     */
    public function index(Request $request)
    {
        $orders = Order::where(
                'user_id',
                $request->user()->id
            )
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }

    /**
     * Order Detail
     */
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
