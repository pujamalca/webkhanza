<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;

class RealTimeClock extends Component
{
    public string $currentTime;
    public string $currentDate;
    
    public function mount(): void
    {
        $this->updateTime();
    }
    
    public function updateTime(): void
    {
        $now = Carbon::now('Asia/Jakarta');
        $this->currentTime = $now->format('H:i:s');
        $this->currentDate = $now->format('d/m/Y');
    }
    
    // Method untuk update manual jika diperlukan
    public function refreshTime(): void
    {
        $this->updateTime();
    }
    
    public function render()
    {
        return view('livewire.real-time-clock');
    }
}