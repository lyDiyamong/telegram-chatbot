<?php

namespace App\Livewire;

use App\Contracts\FileServiceInterface;
use App\Jobs\Telegram\SendTelegramMessage;
use Livewire\Component;
use App\Models\TelegramUser;
use App\Models\TelegramMessage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;

class ChatConversation extends Component
{
    use WithFileUploads;

    public ?TelegramUser $telegramUser = null;
    public string $newMessage = '';
    public Collection $messages;

    private FileServiceInterface $fileService;

    public function __construct()
    {
        $this->fileService = app(FileServiceInterface::class);
    }

    // #[Validate('nullable|string|max:1000')]
    public $message;

    // #[Validate('nullable|file|max:10240')] // 10MB Max
    public $document;

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
                    'file_path' => $message->file_path,
                    'created_at' => $message->created_at,
                    'is_read' => $message->is_read,
                    'file_type' => $message->file_type
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

    public function send()
    {
        if (!$this->telegramUser) {
            return;
        }

        // $this->validate();

        try {
            if ($this->document) {
                // Handle document upload
                $path = $this->document->store('telegram/documents', 'public');
                $fullPath = storage_path('app/public/' . $path);

                $fileUrl = $this->fileService->uploadFromUrl($fullPath);
                Log::info("Document path:", ['fileUrl' => $fileUrl]);

                // Create message in database
                $message = TelegramMessage::create([
                    'telegram_user_id' => $this->telegramUser->id,
                    'content' => $this->message,
                    'from_admin' => true,
                    'is_read' => false,
                    'file_path' => $fileUrl,
                    'file_type' => $this->document->getMimeType(),
                    'file_name' => $this->document->getClientOriginalName(),
                ]);

                // Send document via Telegram
                SendTelegramMessage::dispatch(
                    $this->telegramUser->chat_id,
                    [
                        'type' => 'document',
                        'content' => $fullPath,
                        'caption' => $this->message
                    ]
                );

                // Add message to local collection
                $this->messages->push([
                    'id' => $message->id,
                    'sender' => 'admin',
                    'message' => $this->message,
                    'file_path' => $fileUrl,
                    'file_type' => $this->document->getMimeType(),
                    'created_at' => now(),
                    'is_read' => false
                ]);

                $this->document = null;
            } elseif ($this->message) {
                // Handle text message
                $message = TelegramMessage::create([
                    'telegram_user_id' => $this->telegramUser->id,
                    'content' => $this->message,
                    'from_admin' => true,
                    'is_read' => false,
                ]);

                // Send message via Telegram
                SendTelegramMessage::dispatch(
                    $this->telegramUser->chat_id,
                    $this->message
                );

                // Add message to local collection
                $this->messages->push([
                    'id' => $message->id,
                    'sender' => 'admin',
                    'message' => $this->message,
                    'created_at' => now(),
                    'is_read' => false
                ]);
            }

            $this->message = '';

            // Dispatch events
            $this->dispatch('messageReceived')->to('telegram-user-list');
            $this->dispatch('messageSent');
            $this->dispatch('scrollToBottom');
        } catch (\Exception $e) {
            Log::error('Error sending message', [
                'error' => $e->getMessage(),
                'user_id' => $this->telegramUser->id,
                'trace' => $e->getTraceAsString()
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
                    'file_path' => $message->file_path,
                    'created_at' => $message->created_at,
                    'is_read' => $message->is_read,
                    'file_type' => $message->file_type
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
                    'file_path' => $message->file_path,
                    'created_at' => $message->created_at,
                    'is_read' => $message->is_read,
                    'file_type' => $message->file_type
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
