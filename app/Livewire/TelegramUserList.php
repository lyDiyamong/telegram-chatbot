<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TelegramUser;
use App\Models\TelegramMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Illuminate\Support\Str;

class TelegramUserList extends Component
{
    public Collection $users;
    public $selectedUserId = null;
    public string $search = '';

    protected $listeners = [
        'echo:users,.user.added' => 'handleUserAdded',
        'echo:telegram-messages,.MessageReceived' => 'handleNewMessage',
        // 'echo:telegram-messages,.MessageRead' => 'handleMessageRead'
    ];

    public function mount()
    {
        $this->users = collect([]);
        $this->refreshUsers();
    }

    public function updatedSearch()
    {
        $this->refreshUsers();
    }

    public function refreshUsers()
    {
        $query = TelegramUser::with(['lastMessage']);

        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('username', 'like', $searchTerm)
                    ->orWhere('first_name', 'like', $searchTerm)
                    ->orWhere('last_name', 'like', $searchTerm)
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", [$searchTerm]);
            });
        }

        $this->users = $query->latest()
            ->get()
            ->map(function ($user) {
                $unreadMessages = TelegramMessage::where('telegram_user_id', $user->id)
                    ->where('is_read', false)
                    ->get();

                $user->unread_count = $unreadMessages->count();
                $user->last_message_time = $user->lastMessage?->created_at;
                $user->last_message_preview = $user->lastMessage ? Str::limit($user->lastMessage->content, 30) : null;
                $user->last_message_is_read = $user->lastMessage?->is_read ?? true;

                return $user;
            });
    }

    public function selectUser($userId)
    {
        Log::info('User selected', ['user_id' => $userId]);
        $this->selectedUserId = $userId;

        // Mark messages as read
        TelegramMessage::where('telegram_user_id', $userId)
            ->where('is_read', false)
            ->where('from_admin', false) // Only mark user messages as read
            ->update(['is_read' => true]);

        // Dispatch event to load conversation
        $this->dispatch('conversationSelected', userId: $userId)->to('chat-conversation');

        $this->refreshUsers(); // Update unread counts
    }

    #[On('messageReceived')]
    public function handleMessageReceived()
    {
        Log::info('Message received event received');
        $this->refreshUsers();
    }

    public function handleUserAdded($event)
    {
        Log::info('User added event received', $event);
        $this->refreshUsers();
    }

    public function handleNewMessage($event)
    {
        Log::info('New message event received', $event);
        $this->refreshUsers();
    }

    public function handleMessageRead($event)
    {
        Log::info('Message read event received', $event);
        $this->refreshUsers();
    }

    public function clearSearch()
    {
        $this->search = '';
        $this->refreshUsers();
    }

    public function render(): View
    {
        return view('livewire.telegram-user-list');
    }
}
