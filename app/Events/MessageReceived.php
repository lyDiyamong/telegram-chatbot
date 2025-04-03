<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use App\Models\TelegramUser;
use App\Models\Message;

class MessageReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $telegramUser;
    public $message;

    public function __construct(TelegramUser $telegramUser, Message $message)
    {
        $this->telegramUser = $telegramUser;
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new Channel('telegram');
    }

    public function broadcastAs()
    {
        return 'message.received';
    }
}

