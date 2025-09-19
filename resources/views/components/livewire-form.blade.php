{{-- Universal Livewire Form Component --}}
@props([
    'wire:submit' => '',
    'grid' => false,
    'columns' => '1', // 1, 2, 3, auto
    'gap' => '6'
])

@php
    $gridClasses = '';
    if ($grid) {
        $gridClasses = match($columns) {
            '1' => 'grid grid-cols-1 gap-' . $gap,
            '2' => 'grid grid-cols-1 md:grid-cols-2 gap-' . $gap,
            '3' => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-' . $gap,
            'auto' => 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-' . $gap,
            default => 'livewire-grid-responsive'
        };
    }
@endphp

<form {{ $attributes->merge(['class' => 'livewire-form ' . $gridClasses]) }}>
    {{ $slot }}
</form>