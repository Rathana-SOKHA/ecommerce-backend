<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ContactController extends Controller
{
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