<div x-data="{
    audio: null,
    isPlaying: false,
    currentTime: '0:00',
    duration: '0:00',
    progress: 0,
    wavePoints: [],
    init() {
        this.audio = new Audio('{{ $audio }}');
        this.generateWavePoints();

        this.audio.addEventListener('loadedmetadata', () => {
            this.duration = this.formatTime(this.audio.duration);
        });
        this.audio.addEventListener('timeupdate', () => {
            this.currentTime = this.formatTime(this.audio.currentTime);
            this.progress = (this.audio.currentTime / this.audio.duration) * 100;
            this.updateWave();
        });
        this.audio.addEventListener('ended', () => {
            this.isPlaying = false;
            this.progress = 0;
            this.currentTime = '0:00';
        });
    },
    generateWavePoints() {
        const points = [];
        const segments = 40;
        for (let i = 0; i < segments; i++) {
            const height = Math.sin((i / segments) * Math.PI * 2) * 0.3 + Math.random() * 0.4 + 0.3;
            points.push(height);
        }
        this.wavePoints = points;
    },
    updateWave() {
        if (!this.isPlaying) return;
        const firstPoint = this.wavePoints[0];
        this.wavePoints = [...this.wavePoints.slice(1), firstPoint];
    },
    getWaveHeight(index) {
        return Math.round(this.wavePoints[index] * 100);
    },
    togglePlay() {
        if (this.isPlaying) {
            this.audio.pause();
        } else {
            this.audio.play();
            this.startWaveAnimation();
        }
        this.isPlaying = !this.isPlaying;
    },
    startWaveAnimation() {
        if (this.isPlaying) {
            this.updateWave();
            setTimeout(() => this.startWaveAnimation(), 100);
        }
    },
    seek(event) {
        const rect = event.target.getBoundingClientRect();
        const x = event.clientX - rect.left;
        const percentage = x / rect.width;
        this.audio.currentTime = percentage * this.audio.duration;
    },
    formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = Math.floor(seconds % 60);
        return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
    }
}"
    class="flex items-center space-x-3 bg-opacity-50 rounded-xl p-2 min-w-[240px]">
    <!-- Play/Pause Button -->
    <button @click="togglePlay"
        class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-500 hover:bg-blue-600 transition-all duration-300 ease-in-out transform hover:scale-105 flex items-center justify-center">
        <svg x-show="!isPlaying"
            x-transition:enter="transition transform duration-200 ease-out"
            x-transition:enter-start="scale-95 opacity-0"
            x-transition:enter-end="scale-100 opacity-100"
            xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white"
            viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                clip-rule="evenodd" />
        </svg>
        <svg x-show="isPlaying"
            x-transition:enter="transition transform duration-200 ease-out"
            x-transition:enter-start="scale-95 opacity-0"
            x-transition:enter-end="scale-100 opacity-100"
            xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white"
            viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z"
                clip-rule="evenodd" />
        </svg>
    </button>

    <div class="flex-grow">
        <!-- Waveform Visualization -->
        <div class="relative h-8 bg-gray-800 rounded-lg cursor-pointer overflow-hidden"
            @click="seek">
            <!-- Progress Overlay -->
            <div class="absolute inset-0 bg-blue-500/20 transition-all duration-300 ease-in-out"
                :style="{ width: `${progress}%` }"></div>

            <!-- Dynamic Wave Pattern -->
            <div class="absolute inset-0 flex items-center justify-between px-0.5"
                style="gap: 2px;">
                <template x-for="(point, index) in wavePoints" :key="index">
                    <div class="flex items-center justify-center h-full">
                        <div class="w-1 transition-all duration-300 ease-in-out rounded-full"
                            :class="progress > (index * (100 / wavePoints.length)) ?
                                'bg-blue-400' : 'bg-gray-600'"
                            :style="{
                                height: getWaveHeight(index) + '%',
                                opacity: isPlaying ? '1' : '0.7',
                                transform: isPlaying ? 'scaleY(' + (1 + Math.sin(Date
                                        .now() / 1000 + index) * 0.1) + ')' :
                                    'scaleY(1)'
                            }">
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Time Display -->
        <div class="flex justify-between text-xs text-gray-400 mt-2">
            <span x-text="currentTime"
                class="transition-all duration-200 ease-in-out"></span>
            <span x-text="duration"
                class="transition-all duration-200 ease-in-out"></span>
        </div>
    </div>
</div>