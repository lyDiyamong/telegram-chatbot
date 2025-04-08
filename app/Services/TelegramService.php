<?php

namespace App\Services;

use App\Contracts\TelegramServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService implements TelegramServiceInterface
{
    protected string $apiUrl;
    protected string $botToken;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}";
    }

    public function sendMessage(string $chatId, string $message): bool
    {
        try {
            $response = Http::post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            if (!$response->successful()) {
                Log::error('Failed to send Telegram message', [
                    'chat_id' => $chatId,
                    'error' => $response->json(),
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error sending Telegram message', [
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function getFileUrl(string $fileId): string
    {
        $response = Http::get("{$this->apiUrl}/getFile", ['file_id' => $fileId]);

        if (!$response->successful()) {
            throw new \Exception('Failed to get file path from Telegram');
        }

        $filePath = $response->json('result.file_path');
        return "https://api.telegram.org/file/bot{$this->botToken}/{$filePath}";
    }

    public function downloadFile(string $fileId, string $fileType = 'audio'): array
    {
        $response = Http::get("{$this->apiUrl}/getFile", ['file_id' => $fileId]);

        if (!$response->successful()) {
            throw new \Exception('Failed to get file path from Telegram');
        }

        $filePath = $response->json('result.file_path');
        $fileUrl = "https://api.telegram.org/file/bot{$this->botToken}/{$filePath}";

        return [
            'url' => $fileUrl,
            'path' => $filePath,
            'type' => $fileType,
        ];
    }
}
