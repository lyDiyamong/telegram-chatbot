<?php

namespace App\Contracts;

interface TelegramServiceInterface
{
    public function sendMessage(string $chatId, string|array $message): bool;
    public function getFileUrl(string $fileId): string;
    public function downloadFile(string $fileId, string $fileType = 'file'): array;
    public function sendAudio(string $chatId, string $audioFilePath, ?string $caption = null): bool;
}
