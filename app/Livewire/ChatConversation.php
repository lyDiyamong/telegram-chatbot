<?php

namespace App\Livewire;

use App\Jobs\Telegram\SendTelegramMessage;
use Livewire\Component;
use App\Models\TelegramUser;
use App\Models\TelegramMessage;
use App\Services\TelegramService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class ChatConversation extends Component
{
    public ?TelegramUser $telegramUser = null;
    public string $newMessage = '';
    public Collection $messages;

    protected $listeners = [
        'echo:telegram-messages,.MessageReceived' => 'handleNewMessage'
    ];

    public function mount()
    {
        $this->messages = collect([]);
    }

    #[On('conversationSelected')]
    public function loadConversation($userId)
    {
        Log::info('Loading conversation for user', ['user_id' => $userId]);

        $this->telegramUser = TelegramUser::find($userId);
        if (!$this->telegramUser) {
            Log::error('User not found', ['user_id' => $userId]);
            return;
        }

        $this->messages = TelegramMessage::where('telegram_user_id', $userId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'sender' => $message->from_admin ? 'admin' : 'user',
                    'message' => $message->content,
                    'created_at' => $message->created_at,
                    'is_read' => $message->is_read
                ];
            });

        Log::info('Conversation loaded', [
            'user_id' => $userId,
            'message_count' => $this->messages->count()
        ]);

        // Mark user messages as read when loading conversation
        TelegramMessage::where('telegram_user_id', $userId)
            ->where('from_admin', false)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $this->dispatch('conversationLoaded');
    }

    public function sendMessage()
    {
        if (empty($this->newMessage) || !$this->telegramUser) {
            return;
        }

        try {
            // Create message in database
            $message = TelegramMessage::create([
                'telegram_user_id' => $this->telegramUser->id,
                'content' => $this->newMessage,
                'from_admin' => true,
                'is_read' => false,
            ]);

            // Send message via Telegram
            SendTelegramMessage::dispatch(
                $this->telegramUser->chat_id,
                $this->newMessage
            );

            // Add message to local collection
            $this->messages->push([
                'id' => $message->id,
                'sender' => 'admin',
                'message' => $this->newMessage,
                'created_at' => now(),
                'is_read' => false
            ]);

            $this->newMessage = '';

            // Dispatch events
            $this->dispatch('messageReceived')->to('telegram-user-list');
            $this->dispatch('messageSent');
            $this->dispatch('scrollToBottom');
        } catch (\Exception $e) {
            Log::error('Error sending message', [
                'error' => $e->getMessage(),
                'user_id' => $this->telegramUser->id
            ]);

            // Notify the user of the error
            $this->dispatch('error', message: 'Failed to send message. Please try again.');
        }
    }

    public function getUpdatedMessages()
    {
        if (!$this->telegramUser) {
            return;
        }

        $latestMessages = TelegramMessage::where('telegram_user_id', $this->telegramUser->id)
            ->where('created_at', '>', $this->messages->last()['created_at'] ?? now()->subYears(10))
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'sender' => $message->from_admin ? 'admin' : 'user',
                    'message' => $message->content,
                    'created_at' => $message->created_at,
                    'is_read' => $message->is_read
                ];
            });

        if ($latestMessages->isNotEmpty()) {
            $this->messages = $this->messages->concat($latestMessages);
            $this->dispatch('scrollToBottom');
        }
    }

    public function handleNewMessage($event)
    {
        Log::info('New message received in conversation', $event);

        if ($this->telegramUser && $event['telegram_user_id'] === $this->telegramUser->id) {
            $message = TelegramMessage::find($event['message_id']);

            if ($message) {
                $this->messages->push([
                    'id' => $message->id,
                    'sender' => 'user',
                    'message' => $message->content,
                    'created_at' => $message->created_at,
                    'is_read' => $message->is_read
                ]);

                // Mark the message as read immediately if we're in the conversation
                $message->update(['is_read' => true]);

                $this->dispatch('messageReceived');
                $this->dispatch('scrollToBottom');
            }
        }
    }

    public function render()
    {
        // Poll for new messages
        $this->getUpdatedMessages();

        return view('livewire.chat-conversation');
    }
}
