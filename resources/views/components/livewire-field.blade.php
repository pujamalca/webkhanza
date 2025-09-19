{{-- Universal Livewire Form Field Component --}}
@props([
    'label' => '',
    'type' => 'text',
    'required' => false,
    'readonly' => false,
    'placeholder' => '',
    'wire:model' => '',
    'options' => [], // for select
    'rows' => 4, // for textarea
    'error' => null
])

@php
    $fieldId = 'field_' . str_replace(['.', '[', ']'], ['_', '_', ''], $attributes->get('wire:model', uniqid()));
    $fieldClasses = match($type) {
        'textarea' => 'livewire-textarea',
        'select' => 'livewire-select',
        default => 'livewire-input'
    };

    if ($readonly) {
        $fieldClasses .= ' bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400';
    }
@endphp

<div class="livewire-form-field">
    @if($label)
        <label for="{{ $fieldId }}" class="livewire-label">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    @if($type === 'textarea')
        <textarea
            id="{{ $fieldId }}"
            rows="{{ $rows }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($readonly) readonly @endif
            {{ $attributes->merge(['class' => $fieldClasses]) }}
        >{{ $slot }}</textarea>

    @elseif($type === 'select')
        <select
            id="{{ $fieldId }}"
            @if($required) required @endif
            {{ $attributes->merge(['class' => $fieldClasses]) }}
        >
            @if($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif

            @if(is_array($options))
                @foreach($options as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            @else
                {{ $slot }}
            @endif
        </select>

    @else
        <input
            type="{{ $type }}"
            id="{{ $fieldId }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($readonly) readonly @endif
            {{ $attributes->merge(['class' => $fieldClasses]) }}
        />
    @endif

    @error($attributes->get('wire:model'))
        <span class="livewire-error">{{ $message }}</span>
    @enderror

    @if($error)
        <span class="livewire-error">{{ $error }}</span>
    @endif
</div>