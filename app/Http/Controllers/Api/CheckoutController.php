<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class CheckoutController extends Controller
{
    #[OA\Post(
        path: '/api/checkout',
        summary: 'Place order',
        description: 'Checkout the cart, create an order, and clear the cart. Supports COD and QR payment.',
        security: [['sanctum' => []]],
        tags: ['Checkout'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['payment_method'],
                properties: [
                    new OA\Property(property: 'payment_method', type: 'string', enum: ['cod', 'qr'], example: 'cod'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Order created successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error or cart empty'),
            new OA\Response(response: 500, description: 'Server error'),
        ]
    )]
    public function checkout(
        Request $request,
        TelegramService $telegram
    ) {
        $user = $request->user();

        /*
        |------------------------------------------
        | 1. Validate payment method
        |------------------------------------------
        */
        $request->validate([
            'payment_method' => [
                'required',
                'in:cod,qr'
            ]
        ]);

        $paymentMethod = $request->payment_method;

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

            /*
            |------------------------------------------
            | 2. Validate stock + calculate total
            |------------------------------------------
            */
            foreach ($cartItems as $item) {

                if ($item->quantity > $item->product->stock) {
                    DB::rollBack();

                    return response()->json([
                        'status' => false,
                        'message' => $item->product->name . ' stock not enough'
                    ], 422);
                }

                $total += $item->product->price * $item->quantity;
            }

            /*
            |------------------------------------------
            | 3. Determine order status
            |------------------------------------------
            */
            $status = $paymentMethod === 'cod'
                ? 'pending'
                : 'waiting_payment';

            /*
            |------------------------------------------
            | 4. Create Order
            |------------------------------------------
            */
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $total,
                'status' => $status,
                'payment_method' => $paymentMethod
            ]);

            /*
            |------------------------------------------
            | 5. Create Order Items
            |------------------------------------------
            */
            foreach ($cartItems as $item) {

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price
                ]);

                /*
                |--------------------------------------
                | Stock handling:
                | COD → decrease immediately
                | QR → decrease after payment approval
                |--------------------------------------
                */
                if ($paymentMethod === 'cod') {
                    $item->product->decrement('stock', $item->quantity);
                }
            }

            /*
            |------------------------------------------
            | 6. Clear cart
            |------------------------------------------
            */
            Cart::where('user_id', $user->id)->delete();

            DB::commit();

            /*
            |------------------------------------------
            | 7. Reload order for Telegram
            |------------------------------------------
            */
            $order = Order::with(['user', 'items.product'])
                ->find($order->id);

            /*
            |------------------------------------------
            | 8. Telegram message
            |------------------------------------------
            */
            $message = "";

            $message .= "🛒 NEW ORDER RECEIVED\n\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "🆔 Order #{$order->id}\n\n";

            $message .= "👤 Customer\n";
            $message .= "Name : {$order->user->name}\n";
            $message .= "Email : {$order->user->email}\n\n";

            $message .= "━━━━━━━━━━━━━━━━━━━━━━\n\n";
            $message .= "🛍 Products\n\n";

            $index = 1;

            foreach ($order->items as $item) {

                $subtotal = $item->price * $item->quantity;

                $message .= "{$index}. {$item->product->name}\n";
                $message .= "Qty : {$item->quantity}\n";
                $message .= "Price : $" . number_format($item->price, 2) . "\n";
                $message .= "Subtotal : $" . number_format($subtotal, 2) . "\n\n";

                $index++;
            }

            $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "💰 Total : $" . number_format($order->total_amount, 2) . "\n";
            $message .= "💳 Payment Method : " . strtoupper($order->payment_method) . "\n";
            $message .= "📌 Status : " . ucfirst($order->status) . "\n";
            $message .= "🕒 Order Time : " . now()->format('Y-m-d H:i:s');

            $telegram->sendMessage($message);

            /*
            |------------------------------------------
            | 9. Response
            |------------------------------------------
            */
            return response()->json([
                'status' => true,
                'message' => 'Order created successfully',
                'data' => [
                    'order_id' => $order->id,
                    'payment_method' => $order->payment_method,
                    'status' => $order->status,
                    'total_amount' => $order->total_amount,
                ]
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