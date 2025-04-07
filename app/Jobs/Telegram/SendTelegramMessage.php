<?php

namespace App\Jobs\Telegram;

use App\Jobs\Job;
use App\Services\TelegramService;

class SendTelegramMessage extends Job 
{

    /**
     * Create a new job instance.
     */
    public function __construct(
        private string $chatId,
        private string $message
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(TelegramService $telegramService): void
    {
        $telegramService->sendMessage($this->chatId, $this->message);
    }
}
