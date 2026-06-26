<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        $user = $request->user();

        $cartItems = Cart::with('product')
            ->where('user_id', $user->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Cart is empty'
            ], 422);
        }

        DB::beginTransaction();

        try {

            $total = 0;

            foreach ($cartItems as $item) {

                if ($item->quantity > $item->product->stock) {

                    DB::rollBack();

                    return response()->json([
                        'status' => false,
                        'message' => $item->product->name . ' stock not enough'
                    ], 422);
                }

                $total += (
                    $item->product->price *
                    $item->quantity
                );
            }

            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $total,
                'status' => 'pending'
            ]);

            foreach ($cartItems as $item) {

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price
                ]);

                $item->product->decrement(
                    'stock',
                    $item->quantity
                );
            }

            Cart::where(
                'user_id',
                $user->id
            )->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Order created successfully',
                'order_id' => $order->id
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}