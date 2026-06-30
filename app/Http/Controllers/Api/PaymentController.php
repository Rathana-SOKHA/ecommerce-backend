<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function upload(
        Request $request,
        Order $order,
        TelegramService $telegram
    ) {
        $user = $request->user();

        /*
        |----------------------------------
        | 1. Check ownership
        |----------------------------------
        */
        if ($order->user_id !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized order access'
            ], 403);
        }

        /*
        |----------------------------------
        | 2. Only QR allowed
        |----------------------------------
        */
        if ($order->payment_method !== 'qr') {
            return response()->json([
                'status' => false,
                'message' => 'This order is not QR payment'
            ], 422);
        }

        /*
        |----------------------------------
        | 3. Prevent duplicate payment
        |----------------------------------
        */
        if ($order->payment()->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Payment already submitted'
            ], 422);
        }

        /*
        |----------------------------------
        | 4. Validate request
        |----------------------------------
        */
        $request->validate([
            'payment_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'reference_number' => 'nullable|string'
        ]);

        /*
        |----------------------------------
        | 5. Upload image
        |----------------------------------
        */
        $imagePath = $request->file('payment_image')
            ->store('payments', 'public');

        /*
        |----------------------------------
        | 6. Create payment record
        |----------------------------------
        */
        $payment = Payment::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'payment_method' => 'qr',
            'amount' => $order->total_amount,
            'reference_number' => $request->reference_number,
            'payment_image' => $imagePath,
            'status' => 'pending',
            'paid_at' => now()
        ]);

        /*
        |----------------------------------
        | 7. Update order status
        |----------------------------------
        */
        $order->update([
            'status' => 'waiting_payment_verification'
        ]);

        /*
        |----------------------------------
        | 8. Telegram notification
        |----------------------------------
        */
        $message = "💳 NEW PAYMENT SUBMITTED\n\n";
        $message .= "Order ID: {$order->id}\n";
        $message .= "Customer: {$user->name}\n";
        $message .= "Amount: $" . number_format($order->total_amount, 2) . "\n";
        $message .= "Status: Waiting Verification\n";

        $telegram->sendMessage($message);

        /*
        |----------------------------------
        | 9. Response
        |----------------------------------
        */
        return response()->json([
            'status' => true,
            'message' => 'Payment uploaded successfully',
            'data' => $payment
        ]);
    }
}