<div class="bg-gray-900 rounded-lg shadow-xl h-screen border-l border-gray-800">
    @if ($telegramUser)
        <!-- Chat Header -->
        <div x-data x-init="$nextTick(() => {
            const messageContainer = document.getElementById('chat-messages');
            if (messageContainer) {
                messageContainer.scrollTop = messageContainer.scrollHeight;
            }
        })"
            class="p-4 border-b border-gray-800 bg-gray-850 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg">
                        <span class="text-white font-semibold">{{ substr($telegramUser->first_name, 0, 1) }}</span>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-white">
                        {{ $telegramUser->first_name }} {{ $telegramUser->last_name }}
                    </h3>
                    <p class="text-sm text-blue-400">
                        {{ '@' . $telegramUser->username }}
                    </p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <flux:button variant="ghost" size="sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                    </svg>
                </flux:button>
                <flux:button variant="ghost" size="sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"
                            clip-rule="evenodd" />
                    </svg>
                </flux:button>
            </div>
        </div>

        <!-- Chat Messages -->
        <div class="h-[calc(100vh-10rem)] overflow-y-auto p-4 space-y-4 bg-gray-900 scroll-smooth" {{-- Auto scroll to bottom --}}
            x-ref="messageContainer" x-init="$nextTick(() => { $refs.messageContainer.scrollTop = $refs.messageContainer.scrollHeight; })" wire:loading.class="opacity-50">
            @foreach ($messages as $message)
                <div class="flex {{ $message['sender'] == 'admin' ? 'justify-end' : 'justify-start' }}"
                    wire:key="message-{{ $message['id'] }}">
                    @if ($message['sender'] != 'admin')
                        <div class="flex-shrink-0 mr-2">
                            <div
                                class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                <span
                                    class="text-white text-xs font-semibold">{{ substr($telegramUser->first_name, 0, 1) }}</span>
                            </div>
                        </div>
                    @endif
                    <div class="group relative max-w-[70%]">
                        <div
                            class="{{ $message['sender'] == 'admin' ? 'bg-blue-600 text-white' : 'bg-gray-800 text-gray-100' }} rounded-2xl px-4 py-2 shadow-md">
                            @if ($message['sender'] != 'admin')
                                <p class="text-xs font-medium text-blue-400 mb-1">{{ $telegramUser->first_name }}</p>
                            @endif
                            @if (isset($message['file_type']))
                                @if (explode('/', $message['file_type'])[0] == 'image')
                                    <img src="{{ $message['file_path'] }}" alt="Image"
                                        class="w-[200px] h-[200px] rounded-md mb-2">
                                @elseif (explode('/', $message['file_type'])[0] == 'audio')
                                    <livewire:voice-audio :audio="$message['file_path']" />
                                @endif
                            @endif

                            <p class="text-sm">{{ $message['message'] }}</p>
                            <div class="flex items-center justify-end gap-1 mt-1">
                                <p
                                    class="text-xs {{ $message['sender'] == 'admin' ? 'text-blue-200' : 'text-gray-400' }}">
                                    {{ $message['created_at']->format('h:i A') }}
                                </p>
                                @if ($message['sender'] == 'admin')
                                    <span class="text-blue-200">
                                        @if ($message['is_read'])
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Chat Input -->
        <div class="p-4 border-t border-gray-800 bg-gray-850">
            <form wire:submit.prevent="send" class="space-y-2">
                <div class="flex space-x-2">
                    <div class="relative flex-1 flex items-center" wire:ignore>
                        <flux:input type="text" wire:model="message"
                            class="flex-1 bg-gray-800 border-gray-700 text-white placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Type your message..." autocomplete="off" />

                        <!-- Voice Recording Button -->
                        <div x-data="voiceRecorder()" x-init="init()" class="absolute right-2">
                            <button type="button" x-show="!recording" @click.prevent="startRecording"
                                class="p-2 text-gray-400 hover:text-blue-500 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M7 4a3 3 0 016 0v4a3 3 0 11-6 0V4zm4 10.93A7.001 7.001 0 0017 8a1 1 0 10-2 0A5 5 0 015 8a1 1 0 00-2 0 7.001 7.001 0 006 6.93V17H6a1 1 0 100 2h8a1 1 0 100-2h-3v-2.07z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>

                            <!-- Recording Interface -->
                            <div x-show="recording" class="flex items-center space-x-2">
                                <span class="text-red-500 animate-pulse" x-text="formatDuration(duration)"></span>
                                <button type="button" @click.prevent="stopRecording"
                                    class="p-2 text-blue-500 hover:text-blue-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <button type="button" @click.prevent="cancelRecording"
                                    class="p-2 text-red-500 hover:text-red-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <flux:button type="submit" color="blue" x-bind:disabled="$wire.isRecording">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path
                                d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                        </svg>
                    </flux:button>
                </div>

                <div class="flex items-center space-x-2">
                    <flux:input type="file" wire:model="document"
                        class="flex-1 bg-gray-800 border-gray-700 text-white placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" />
                    @if ($document)
                        <span class="text-sm text-gray-400">{{ $document->getClientOriginalName() }}</span>
                        <button type="button" wire:click="$set('document', null)"
                            class="text-red-500 hover:text-red-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    @endif
                </div>
            </form>
        </div>
    @else
        <div class="h-full flex items-center justify-center bg-gray-900">
            <div class="text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-600 mb-4" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <p class="text-gray-400">Select a user to start chatting</p>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        const scrollToBottom = () => {
            const messageContainer = document.getElementById('chat-messages');
            if (messageContainer) {
                setTimeout(() => {
                    messageContainer.scrollTop = messageContainer.scrollHeight;
                }, 200);
            }
        };

        // When conversation is loaded
        Livewire.on('conversationLoaded', () => {
            scrollToBottom();
        });

        // When message is sent or received
        Livewire.on('messageSent', scrollToBottom);
        Livewire.on('messageReceived', scrollToBottom);

        // Watch for changes in the messages container
        const observer = new MutationObserver(scrollToBottom);
        const messageContainer = document.getElementById('chat-messages');
        if (messageContainer) {
            observer.observe(messageContainer, {
                childList: true,
                subtree: true
            });
        }

        // Voice Recorder Alpine Component
        Alpine.data('voiceRecorder', () => ({
            mediaRecorder: null,
            audioChunks: [],
            duration: 0,
            timer: null,
            recording: false,

            init() {
                this.recording = false;
                this.duration = 0;
            },

            async startRecording() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({
                        audio: true
                    });

                    // Use WebM with Opus codec (widely supported)
                    const options = {
                        mimeType: 'audio/webm;codecs=opus',
                        audioBitsPerSecond: 128000
                    };

                    this.mediaRecorder = new MediaRecorder(stream, options);
                    this.audioChunks = [];

                    this.mediaRecorder.ondataavailable = (event) => {
                        this.audioChunks.push(event.data);
                    };

                    this.mediaRecorder.onstop = async () => {
                        const audioBlob = new Blob(this.audioChunks, {
                            type: 'audio/webm;codecs=opus'
                        });
                        const reader = new FileReader();
                        reader.readAsDataURL(audioBlob);
                        reader.onloadend = () => {
                            Livewire.dispatch('voiceRecordingStopped', {
                                audioData: reader.result
                            });
                        };
                        this.stopTimer();
                        stream.getTracks().forEach(track => track.stop());
                    };

                    // Request data every second to ensure we get all the audio
                    this.mediaRecorder.start(1000);
                    this.startTimer();
                    this.recording = true;
                    Livewire.dispatch('voiceRecordingStarted');
                } catch (error) {
                    console.error('Error accessing microphone:', error);
                    alert(
                        'Could not access microphone. Please check your browser permissions and make sure you are using a supported browser.'
                        );
                }
            },

            stopRecording() {
                if (this.mediaRecorder && this.mediaRecorder.state === 'recording') {
                    this.mediaRecorder.stop();
                    this.recording = false;
                }
            },

            cancelRecording() {
                if (this.mediaRecorder && this.mediaRecorder.state === 'recording') {
                    this.mediaRecorder.stop();
                    this.audioChunks = [];
                    this.stopTimer();
                    this.recording = false;
                    Livewire.dispatch('voiceRecordingCancelled');
                }
            },

            startTimer() {
                this.duration = 0;
                this.timer = setInterval(() => {
                    this.duration++;
                }, 1000);
            },

            stopTimer() {
                if (this.timer) {
                    clearInterval(this.timer);
                    this.timer = null;
                }
            },

            formatDuration(seconds) {
                const minutes = Math.floor(seconds / 60);
                const remainingSeconds = seconds % 60;
                return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
            }
        }));
    });
</script>
