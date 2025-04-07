<?php

namespace App\Services;

use App\Models\TelegramUser;
use App\Models\TelegramMessage;
use App\Contracts\FileServiceInterface;
use App\Contracts\TelegramServiceInterface;
use Illuminate\Support\Facades\Log;

class TelegramMessageProcessor
{
    public function __construct(
        private TelegramUser $telegramUser,
        private TelegramMessage $telegramMessage,
        private FileServiceInterface $fileService,
        private TelegramServiceInterface $telegramService
    ) {}

    public function process(array $messageData): ?TelegramMessage
    {
        $chatId = $messageData['chat']['id'];
        $telegramUser = $this->getOrCreateUser($messageData['chat']);

        if (isset($messageData['text'])) {
            return $this->createTextMessage($telegramUser, $messageData['text']);
        }

        if (isset($messageData['document'])) {
            return $this->processDocument($telegramUser, $messageData['document'], $messageData['caption'] ?? null);
        }

        if (isset($messageData['photo'])) {
            return $this->processPhoto($telegramUser, $messageData['photo'], $messageData['caption'] ?? null);
        }

        return null;
    }

    protected function getOrCreateUser(array $chatData): TelegramUser
    {
        return $this->telegramUser->updateOrCreate(
            ['chat_id' => $chatData['id']],
            [
                'first_name' => $chatData['first_name'] ?? '',
                'last_name' => $chatData['last_name'] ?? '',
                'username' => $chatData['username'] ?? '',
            ]
        );
    }

    protected function createTextMessage(TelegramUser $user, string $text): TelegramMessage
    {
        return $this->telegramMessage->create([
            'telegram_user_id' => $user->id,
            'content' => $text,
            'from_admin' => false,
            'is_read' => false,
        ]);
    }

    protected function processDocument(TelegramUser $user, array $document, ?string $caption): TelegramMessage
    {
        $fileUrl = $this->telegramService->getFileUrl($document['file_id']);

        Log::info('Processing document', [
            'fileUrl' => $fileUrl,
            'document' => $document,
            'caption' => $caption,
        ]);

        $filePath = $this->fileService->uploadFromUrl(
            $fileUrl,
            'telegram/documents',
            $document['file_name']
        );

        return $this->telegramMessage->create([
            'telegram_user_id' => $user->id,
            'content' => $caption ?? null,
            'file_path' => $filePath,
            'file_type' => $document['mime_type'],
            'file_name' => $document['file_name'],
            'from_admin' => false,
            'is_read' => false,
        ]);
    }

    protected function processPhoto(TelegramUser $user, array $photos, ?string $caption): TelegramMessage
    {
        // Get highest resolution photo (last in array)
        $photo = end($photos);
        $fileUrl = $this->telegramService->getFileUrl($photo['file_id']);
        
        $filePath = $this->fileService->uploadFromUrl(
            $fileUrl,
            'telegram/photos',
            'photo_' . time() . '.jpg'
        );

        return $this->telegramMessage->create([
            'telegram_user_id' => $user->id,
            'content' => $caption ?? null,
            'file_path' => $filePath,
            'file_type' => 'image/jpeg',
            'file_name' => 'photo.jpg',
            'from_admin' => false,
            'is_read' => false,
        ]);
    }
}