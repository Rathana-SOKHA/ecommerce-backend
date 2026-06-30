<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    public function sendMessage(string $message): bool
    {
        $response = Http::post(
            "https://api.telegram.org/bot" .
            config('telegram.bot_token') .
            "/sendMessage",
            [
                'chat_id' => config('telegram.chat_id'),
                'text' => $message,
                'parse_mode' => 'HTML'
            ]
        );

        return $response->successful();
    }
}