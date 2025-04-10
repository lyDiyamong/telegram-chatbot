<?php

namespace App\Services;

use App\Contracts\TelegramServiceInterface;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;

class TelegramService implements TelegramServiceInterface
{
    protected string $botToken;
    protected Api $telegram;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->telegram = new Api($this->botToken);
    }

    /**
     * Send a text message to a Telegram chat.
     */
    public function sendMessage(string $chatId, string $message): bool
    {
        try {
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Error sending Telegram message', [
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send a document file (e.g. PDF, DOCX) to Telegram chat.
     */
    public function sendDocument(string $chatId, string $localFilePath, ?string $caption = null): bool
    {
        try {
            $this->telegram->sendDocument([
                'chat_id' => $chatId,
                'document' => fopen($localFilePath, 'r'),
                'caption' => $caption,
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Error sending Telegram document', [
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get a Telegram file's full URL for download.
     */
    public function getFileUrl(string $fileId): string
    {
        try {
            $file = $this->telegram->getFile(['file_id' => $fileId]);
            return "https://api.telegram.org/file/bot{$this->botToken}/{$file->filePath}";
        } catch (\Exception $e) {
            Log::error('Error getting Telegram file URL', [
                'file_id' => $fileId,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to get file path from Telegram');
        }
    }

    /**
     * Get file metadata and direct download link from Telegram.
     */
    public function downloadFile(string $fileId, string $fileType = 'file'): array
    {
        try {
            $file = $this->telegram->getFile(['file_id' => $fileId]);
            $filePath = $file->filePath;

            return [
                'url' => "https://api.telegram.org/file/bot{$this->botToken}/{$filePath}",
                'path' => $filePath,
                'type' => $fileType,
            ];
        } catch (\Exception $e) {
            Log::error('Error downloading Telegram file', [
                'file_id' => $fileId,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to get file path from Telegram');
        }
    }
}
