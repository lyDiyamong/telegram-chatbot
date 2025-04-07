<?php

namespace App\Contracts;

interface TelegramServiceInterface
{
    public function sendMessage(string $chatId, string $message): bool;
    public function getFileUrl(string $fileId): string;
}
