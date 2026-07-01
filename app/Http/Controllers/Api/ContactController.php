<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use OpenApi\Attributes as OA;

class ContactController extends Controller
{
    #[OA\Post(
        path: '/api/contact',
        summary: 'Send contact message',
        description: 'Send a contact form message via Telegram. Rate limited to 5 requests per minute.',
        tags: ['Contact'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'subject', 'message'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 100, example: 'John Doe'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
                    new OA\Property(property: 'subject', type: 'string', maxLength: 150, example: 'Product Inquiry'),
                    new OA\Property(property: 'message', type: 'string', example: 'I have a question about your products.'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Message sent successfully'),
            new OA\Response(response: 422, description: 'Validation error'),
            new OA\Response(response: 429, description: 'Too many requests'),
            new OA\Response(response: 500, description: 'Failed to send message'),
        ]
    )]
    public function send(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string'],
        ]);

        // Build the Telegram message
        $message = implode("\n", [
            "📩 New Contact Message",
            "",
            "👤 Name: {$validated['name']}",
            "📧 Email: {$validated['email']}",
            "📝 Subject: {$validated['subject']}",
            "",
            "💬 Message:",
            $validated['message'],
        ]);

        // Send to Telegram
        $response = Http::post(
            "https://api.telegram.org/bot" . config('telegram.bot_token') . "/sendMessage",
            [
                'chat_id' => config('telegram.chat_id'),
                'text' => $message,
            ]
        );

        if ($response->successful()) {
            return response()->json([
                'status' => true,
                'message' => 'Your message has been sent successfully.'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Unable to send your message.'
        ], 500);
    }
}