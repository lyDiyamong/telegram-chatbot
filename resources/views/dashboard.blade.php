<x-layouts.app :title="__('Dashboard')">
    <div class="container mx-auto px-4">
        <div class="flex gap-4 h-screen py-4">
            <div class="w-1/3">
                @livewire('telegram-user-list')
            </div>
            <div class="w-2/3">
                @livewire('chat-conversation')
            </div>
        </div>
    </div>
</x-layouts.app>
