<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg h-screen max-w-md">
    <div class="p-4 border-b dark:border-gray-700">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Telegram Users</h2>
    </div>

    <div class="overflow-y-auto h-[calc(100vh-5rem)]">
        <ul class="divide-y dark:divide-gray-700">
            @forelse ($users as $user)
                <li wire:key="{{ $user->id }}" wire:click="selectUser({{ $user->id }})"
                    class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer {{ $selectedUserId === $user->id ? 'bg-blue-50 dark:bg-gray-700' : '' }}">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                                <span
                                    class="text-white text-lg font-semibold">{{ substr($user->first_name, 0, 1) }}</span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ $user->first_name }} {{ $user->last_name }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                {{ '@' . $user->username }}
                            </p>
                            @if ($user->lastMessage)
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 truncate">
                                    {{ Str::limit($user->lastMessage->content, 30) }}
                                </p>
                            @endif
                        </div>
                        @if ($user->unread_count > 0)
                            <div class="flex-shrink-0">
                                <span
                                    class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-blue-500 rounded-full">
                                    {{ $user->unread_count }}
                                </span>
                            </div>
                        @endif
                    </div>
                </li>
            @empty
                <li class="p-4 text-center text-gray-500 dark:text-gray-400">
                    No users found.
                </li>
            @endforelse
        </ul>
    </div>
</div>
