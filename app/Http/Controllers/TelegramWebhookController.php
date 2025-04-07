<?php

// app/Http/Controllers/TelegramWebhookController.php
namespace App\Http\Controllers;

use App\Services\TelegramMessageProcessor;
use App\Events\UserAdded;
use App\Events\TelegramMessageReceived;
use App\Models\TelegramMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    public function __construct(
        private TelegramMessageProcessor $messageProcessor
    ) {}

    public function __invoke(Request $request)
    {
        try {
            Log::info('Telegram webhook received', $request->all());

            $data = $request->all();

            if (!isset($data['message'])) {
                Log::info('No message in webhook data');
                return response()->json(['status' => 'success']);
            }

            $message = $this->messageProcessor->process($data['message']);

            if ($message) {
                $this->broadcastEvents($message);
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Telegram webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    protected function broadcastEvents(TelegramMessage $message): void
    {
        try {
            // Load the relationship if not already loaded
            if (!$message->relationLoaded('telegramUser')) {
                $message->load('telegramUser');
            }

            if (!$message->telegramUser) {
                Log::error('Telegram user not found for message', [
                    'message_id' => $message->id,
                    'telegram_user_id' => $message->telegram_user_id
                ]);
                return;
            }

            Log::info('Broadcasting message events', [
                'message_id' => $message->id,
                'user_id' => $message->telegramUser->id,
                'content' => $message->content ?? '[File attachment]'
            ]);

            // Broadcast user added event
            broadcast(new UserAdded($message->telegramUser))->toOthers();

            // Broadcast message received event
            broadcast(new TelegramMessageReceived(
                $message->telegramUser->id,
                $message->content ?? '[File attachment]',
                $message->id // Add message ID to the event
            ))->toOthers();
        } catch (\Exception $e) {
            Log::error('Error broadcasting events', [
                'error' => $e->getMessage(),
                'message_id' => $message->id ?? null,
                'telegram_user_id' => $message->telegram_user_id ?? null
            ]);
        }
    }
}
