<div 
    class="flex items-center space-x-3 text-xs"
    wire:poll.1s="updateTime"
>
    <div class="flex items-center space-x-1.5">
        <div class="text-gray-600 dark:text-gray-400">
            <span class="font-medium">{{ $currentDate }}</span>
        </div>
    </div>
    <div class="flex items-center space-x-1.5">
        <div class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></div>
        <div class="font-mono font-semibold text-primary-600 dark:text-primary-400 text-xs">
            {{ $currentTime }}
        </div>
    </div>
</div>