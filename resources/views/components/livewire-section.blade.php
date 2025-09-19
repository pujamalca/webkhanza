{{-- Universal Livewire Section Component --}}
@props([
    'title' => '',
    'subtitle' => '',
    'padding' => 'p-6'
])

<div {{ $attributes->merge(['class' => "livewire-section $padding"]) }}>
    @if($title || $subtitle)
        <div class="livewire-card-header">
            @if($title)
                <h3 class="livewire-title">{{ $title }}</h3>
            @endif
            @if($subtitle)
                <p class="livewire-subtitle mt-1">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    <div class="livewire-section-content">
        {{ $slot }}
    </div>
</div>