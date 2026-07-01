<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with([
            'order',
            'user'
        ])
        ->latest()
        ->paginate(10);

        return view(
            'admin.payments.index',
            compact('payments')
        );
    }

    public function show(Payment $payment)
    {
        $payment->load([
            'order.items.product',
            'user'
        ]);

        return view(
            'admin.payments.show',
            compact('payment')
        );
    }

    public function approve(
        Payment $payment,
        TelegramService $telegram
    ) {
        if ($payment->status !== 'pending') {
            return back()->with(
                'error',
                'This payment has already been ' . $payment->status . '.'
            );
        }

        $payment->update([
            'status' => 'approved'
        ]);

        $payment->order->update([
            'status' => 'paid'
        ]);

        // Decrease stock after payment approval
        // (COD already decreases stock at checkout)
        if ($payment->order->payment_method === 'qr') {
            foreach ($payment->order->items as $item) {
                $item->product->decrement(
                    'stock',
                    $item->quantity
                );
            }
        }

        // Telegram notification
        $message = "✅ PAYMENT APPROVED\n\n";
        $message .= "Payment #{$payment->id}\n";
        $message .= "Order #{$payment->order_id}\n";
        $message .= "Customer: {$payment->user->name}\n";
        $message .= "Amount: $" . number_format($payment->amount, 2) . "\n";
        $message .= "Status: Approved ✅";

        $telegram->sendMessage($message);

        return back()->with(
            'success',
            'Payment approved successfully. Order has been marked as paid.'
        );
    }

    public function reject(
        Request $request,
        Payment $payment,
        TelegramService $telegram
    ) {
        if ($payment->status !== 'pending') {
            return back()->with(
                'error',
                'This payment has already been ' . $payment->status . '.'
            );
        }

        $request->validate([
            'rejection_reason' => [
                'nullable',
                'string',
                'max:500'
            ]
        ]);

        $payment->update([
            'status' => 'rejected'
        ]);

        $payment->order->update([
            'status' => 'payment_rejected'
        ]);

        // Telegram notification
        $message = "❌ PAYMENT REJECTED\n\n";
        $message .= "Payment #{$payment->id}\n";
        $message .= "Order #{$payment->order_id}\n";
        $message .= "Customer: {$payment->user->name}\n";
        $message .= "Amount: $" . number_format($payment->amount, 2) . "\n";
        $message .= "Status: Rejected ❌\n";

        if ($request->rejection_reason) {
            $message .= "Reason: {$request->rejection_reason}";
        }

        $telegram->sendMessage($message);

        return back()->with(
            'success',
            'Payment rejected successfully.'
        );
    }
}
