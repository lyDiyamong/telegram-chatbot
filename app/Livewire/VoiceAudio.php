<?php

namespace App\Livewire;

use Livewire\Component;

class VoiceAudio extends Component
{
    public $audio;
    public function render()
    {
        return view('livewire.components.voice-audio');
    }
}
