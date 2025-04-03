<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TelegramMessageReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $telegram_user_id,
        public string $message
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('telegram-messages')
        ];
    }

    public function broadcastAs(): string
    {
        return 'MessageReceived';
    }
}
