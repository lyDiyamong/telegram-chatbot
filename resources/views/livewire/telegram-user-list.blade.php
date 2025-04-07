<div class="bg-gray-900 rounded-lg shadow-xl h-screen max-w-md border border-gray-800">
    <div class="p-4 border-b border-gray-800 bg-gray-850">
        <h2 class="text-xl font-semibold text-white flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
            </svg>
            Telegram Users
        </h2>
        <div class="mt-4 relative">
            <div class="relative">
                <input type="text" wire:model.live.debounce="search"
                    class="w-full bg-gray-800 border-gray-700 text-white rounded-lg pl-10 pr-4 py-2 focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Search by name or username...">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    @if (!empty($search))
                        <button wire:click="clearSearch" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg class="h-5 w-5 text-gray-400 hover:text-white" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    @endif
            </div>
        </div>
    </div>

    <div class="overflow-y-auto h-[calc(100vh-8rem)] scrollbar-thin scrollbar-thumb-gray-700 scrollbar-track-gray-900">
        <ul class="divide-y divide-gray-800">
            @forelse ($users as $user)
                <li wire:key="{{ $user->id }}" wire:click="selectUser({{ $user->id }})"
                    class="p-4 hover:bg-gray-800 transition-all duration-200 cursor-pointer {{ $selectedUserId === $user->id ? 'bg-gray-800 border-l-4 border-blue-500' : '' }}">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg">
                                <span
                                    class="text-white text-lg font-semibold">{{ substr($user->first_name, 0, 1) }}</span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start">
                                <p class="text-sm font-medium text-white truncate">
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </p>
                                @if ($user->last_message_time)
                                    <span class="text-xs text-gray-400">
                                        {{ $user->last_message_time->diffForHumans(null, true) }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-blue-400 truncate">
                                {{ '@' . $user->username }}
                            </p>
                            @if ($user->last_message_preview)
                                <div class="flex items-center gap-1 mt-1">
                                    @if ($user->last_message_is_read)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                    <p class="text-xs text-gray-400 truncate">
                                        {{ $user->last_message_preview }}
                                    </p>
                                </div>
                            @endif
                        </div>
                        @if ($user->unread_count > 0)
                            <div class="flex-shrink-0">
                                <flux:badge color="blue" size="sm" class="animate-pulse">
                                    {{ $user->unread_count }}
                                </flux:badge>
                            </div>
                        @endif
                    </div>
                </li>
            @empty
                <li class="p-8 text-center">
                    <div class="flex flex-col items-center justify-center text-gray-400">
                        @if (!empty($search))
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2 text-gray-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <p class="text-sm">No users found matching "{{ $search }}"</p>
                            <button wire:click="clearSearch" class="mt-2 text-blue-500 hover:text-blue-400 text-sm">
                                Clear search
                            </button>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2 text-gray-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                            </svg>
                            <p class="text-sm">No users found</p>
                        @endif
                    </div>
                </li>
            @endforelse
        </ul>
    </div>
</div>
