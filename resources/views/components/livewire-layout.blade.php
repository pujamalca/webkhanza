{{-- Universal Livewire Layout Component --}}
@props([
    'title' => '',
    'container' => 'max-w-7xl',
    'padding' => 'py-8',
    'theme' => 'auto' // auto, light, dark
])

{{-- Vite Assets - Load once per page --}}
@once
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endonce

{{-- Universal Livewire Container --}}
<div {{ $attributes->merge(['class' => "min-h-screen bg-gray-50 dark:bg-gray-900 $padding"]) }}
     x-data="livewireTheme"
     x-init="init()">

    <div class="{{ $container }} mx-auto px-4 sm:px-6 lg:px-8">

        @if($title)
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ $title }}
                </h1>
            </div>
        @endif

        {{ $slot }}
    </div>
</div>

{{-- Universal Theme Management Script --}}
@once
<script>
    // Universal Livewire Theme Management
    window.livewireTheme = {
        darkMode: false,

        init() {
            this.detectTheme();
            this.updateTheme();
            this.setupObservers();
            this.setupEventListeners();
        },

        detectTheme() {
            const htmlHasDark = document.documentElement.classList.contains('dark');
            const bodyHasDark = document.body.classList.contains('dark');
            const filamentDark = document.querySelector('[data-theme="dark"]') !== null;

            this.darkMode = htmlHasDark || bodyHasDark || filamentDark;
        },

        updateTheme() {
            if (this.darkMode) {
                document.body.classList.add('dark');
                document.documentElement.classList.add('dark');
            } else {
                document.body.classList.remove('dark');
                document.documentElement.classList.remove('dark');
            }

            // Dispatch event for other components
            window.dispatchEvent(new CustomEvent('livewire-theme-changed', {
                detail: { darkMode: this.darkMode }
            }));
        },

        setupObservers() {
            // Watch for theme changes
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        this.detectTheme();
                        this.updateTheme();
                    }
                });
            });

            observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['class']
            });

            observer.observe(document.body, {
                attributes: true,
                attributeFilter: ['class']
            });
        },

        setupEventListeners() {
            // Listen for Filament theme events
            window.addEventListener('theme-changed', () => {
                setTimeout(() => {
                    this.detectTheme();
                    this.updateTheme();
                }, 100);
            });

            // System theme changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                this.detectTheme();
                this.updateTheme();
            });
        }
    };

    // Auto-initialize
    document.addEventListener('DOMContentLoaded', function() {
        if (!window.livewireTheme.initialized) {
            window.livewireTheme.init();
            window.livewireTheme.initialized = true;
        }
    });
</script>
@endonce

{{-- Universal Livewire Enhancement Styles --}}
@once
<style>
    /* Universal Livewire Styles */
    .livewire-container {
        @apply min-h-screen bg-gray-50 dark:bg-gray-900;
    }

    .livewire-wrapper {
        @apply max-w-7xl mx-auto px-4 sm:px-6 lg:px-8;
    }

    .livewire-section {
        @apply bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow;
    }

    .livewire-form {
        @apply space-y-6;
    }

    .livewire-grid {
        @apply grid gap-4;
    }

    .livewire-grid-responsive {
        @apply grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6;
    }

    .livewire-input {
        @apply w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200;
    }

    .livewire-textarea {
        @apply w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-vertical transition-colors duration-200;
    }

    .livewire-select {
        @apply w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200;
    }

    .livewire-label {
        @apply block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2;
    }

    .livewire-label-bold {
        @apply block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2;
    }

    .livewire-button-group {
        @apply flex items-center justify-end gap-3 pt-4;
    }

    .livewire-error {
        @apply text-red-600 dark:text-red-400 text-xs mt-1;
    }

    .livewire-card {
        @apply bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 shadow-sm;
    }

    .livewire-card-header {
        @apply border-b border-gray-200 dark:border-gray-700 pb-4 mb-6;
    }

    .livewire-title {
        @apply text-lg font-semibold text-gray-900 dark:text-gray-100;
    }

    .livewire-subtitle {
        @apply text-sm text-gray-600 dark:text-gray-400;
    }

    /* Loading states */
    [wire\:loading] {
        @apply opacity-75 pointer-events-none;
    }

    [wire\:loading]::after {
        content: '';
        @apply absolute inset-0 bg-gray-100 dark:bg-gray-800 bg-opacity-50 flex items-center justify-center;
    }

    /* Form enhancements */
    .livewire-input:focus,
    .livewire-textarea:focus,
    .livewire-select:focus {
        @apply outline-none;
    }

    .livewire-input[readonly] {
        @apply bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400;
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
        .livewire-grid-responsive {
            @apply grid-cols-1 gap-4;
        }

        .livewire-button-group {
            @apply flex-col items-stretch gap-2;
        }
    }

    /* Animation classes */
    .livewire-fade-in {
        animation: livewire-fade-in 0.3s ease-out;
    }

    @keyframes livewire-fade-in {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Utility classes */
    .livewire-hidden {
        @apply hidden;
    }

    .livewire-visible {
        @apply block;
    }
</style>
@endonce