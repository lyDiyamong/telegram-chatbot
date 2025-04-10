<?php

namespace App\Jobs\Telegram;

use App\Jobs\Job;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Log;

class SendTelegramMessage extends Job
{

    /**
     * Create a new job instance.
     */
    public function __construct(
        private string $chatId,
        private string|array $message
    ) {}

    /**
     * Execute the job.
     */
    public function handle(TelegramService $telegramService): void
    {
        try {
            $telegramService->sendMessage($this->chatId, $this->message);
        } catch (\Exception $e) {
            Log::error('Failed to send Telegram message', [
                'chat_id' => $this->chatId,
                'message' => $this->message,
                'error' => $e->getMessage()
            ]);
        }
    }
}
