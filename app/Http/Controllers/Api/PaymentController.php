<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;

class PaymentController extends Controller
{
    #[OA\Get(
        path: '/api/payments',
        summary: 'Get user payment history',
        description: 'Returns a paginated list of payments for the authenticated user.',
        security: [['sanctum' => []]],
        tags: ['Payment'],
        parameters: [
            new OA\Parameter(
                name: 'page',
                in: 'query',
                description: 'Page number',
                required: false,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
            new OA\Parameter(
                name: 'per_page',
                in: 'query',
                description: 'Items per page (default: 10)',
                required: false,
                schema: new OA\Schema(type: 'integer', example: 10)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated list of payments',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'current_page', type: 'integer', example: 1),
                                new OA\Property(
                                    property: 'data',
                                    type: 'array',
                                    items: new OA\Items(ref: '#/components/schemas/Payment')
                                ),
                                new OA\Property(property: 'last_page', type: 'integer', example: 1),
                                new OA\Property(property: 'per_page', type: 'integer', example: 10),
                                new OA\Property(property: 'total', type: 'integer', example: 5),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function index(Request $request)
    {
        $perPage = $request->integer('per_page', 10);
        $perPage = max(1, min(100, $perPage));

        $payments = Payment::with([
            'order'
        ])
        ->where('user_id', $request->user()->id)
        ->latest()
        ->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $payments
        ]);
    }

    #[OA\Get(
        path: '/api/payments/order/{order}',
        summary: 'Get payment status by order',
        description: 'Check if a payment has been submitted for a specific order.',
        security: [['sanctum' => []]],
        tags: ['Payment'],
        parameters: [
            new OA\Parameter(
                name: 'order',
                in: 'path',
                description: 'Order ID',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Payment status for the order',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'boolean', example: true),
                        new OA\Property(property: 'exists', type: 'boolean', example: true),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Payment', nullable: true),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Unauthorized order access'),
        ]
    )]
    public function byOrder(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $payment = Payment::where('order_id', $order->id)
            ->with(['order'])
            ->first();

        return response()->json([
            'status' => true,
            'exists' => $payment ? true : false,
            'data' => $payment
        ]);
    }

    #[OA\Get(
        path: '/api/payments/{payment}',
        summary: 'Get payment detail',
        description: 'Returns full payment details including order items and user info.',
        security: [['sanctum' => []]],
        tags: ['Payment'],
        parameters: [
            new OA\Parameter(
                name: 'payment',
                in: 'path',
                description: 'Payment ID',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Payment details',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'boolean', example: true),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Payment'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Payment not found'),
        ]
    )]
    public function show(Request $request, Payment $payment)
    {
        if ($payment->user_id !== $request->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $payment->load([
            'order.items.product',
            'user'
        ]);

        return response()->json([
            'status' => true,
            'data' => $payment
        ]);
    }

    #[OA\Post(
        path: '/api/payments/upload/{order}',
        summary: 'Upload payment proof',
        description: 'Upload QR payment proof image for an order. Only works for orders with payment_method = qr.',
        security: [['sanctum' => []]],
        tags: ['Payment'],
        parameters: [
            new OA\Parameter(
                name: 'order',
                in: 'path',
                description: 'Order ID',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: 'payment_image',
                            type: 'string',
                            format: 'binary',
                            description: 'Payment proof image (jpg, jpeg, png, max 2MB)'
                        ),
                        new OA\Property(
                            property: 'reference_number',
                            type: 'string',
                            description: 'Optional reference number',
                            nullable: true,
                            example: 'TRX123456'
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Payment uploaded successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Payment uploaded successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Payment'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Unauthorized order access'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
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
