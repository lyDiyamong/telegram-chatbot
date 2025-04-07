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
        public string $message,
        public int $message_id
    ) {}

    public function broadcastOn()
    {
        return new Channel('telegram-messages');
    }

    public function broadcastAs()
    {
        return 'MessageReceived';
    }

    public function broadcastWith()
    {
        return [
            'telegram_user_id' => $this->telegram_user_id,
            'message' => $this->message,
            'message_id' => $this->message_id,
            'timestamp' => now()->toIso8601String()
        ];
    }
}
