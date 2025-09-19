{{-- Livewire Styles Component --}}
@props(['theme' => 'light'])

{{-- Filament Livewire Styles --}}
@livewireStyles

{{-- Enhanced Livewire Custom Styles --}}
<style>
    /* Livewire Loading States */
    [wire\:loading] {
        opacity: 0.7;
        pointer-events: none;
    }

    [wire\:loading]::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    /* Dark mode loading states */
    .dark [wire\:loading]::before {
        background: rgba(0, 0, 0, 0.5);
    }

    /* Livewire Component Base Styles */
    .livewire-component {
        position: relative;
    }

    /* Enhanced Livewire Cards */
    .livewire-card {
        @apply bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm;
    }

    .livewire-card-header {
        @apply px-6 py-4 border-b border-gray-200 dark:border-gray-700;
    }

    .livewire-card-body {
        @apply p-6;
    }

    /* Livewire Buttons */
    .livewire-btn-primary {
        @apply px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2;
    }

    .livewire-btn-secondary {
        @apply px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-300 font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2;
    }

    .dark .livewire-btn-secondary {
        @apply bg-gray-700 hover:bg-gray-600 text-gray-200 border-gray-600;
    }

    /* Livewire Alerts */
    .livewire-alert {
        @apply px-4 py-3 rounded-lg font-medium text-sm;
    }

    .livewire-alert-success {
        @apply bg-green-50 text-green-800 border border-green-200;
    }

    .livewire-alert-warning {
        @apply bg-yellow-50 text-yellow-800 border border-yellow-200;
    }

    .livewire-alert-error {
        @apply bg-red-50 text-red-800 border border-red-200;
    }

    .livewire-alert-info {
        @apply bg-blue-50 text-blue-800 border border-blue-200;
    }

    /* Dark mode alert styles */
    .dark .livewire-alert-success {
        @apply bg-green-900/20 text-green-300 border-green-700;
    }

    .dark .livewire-alert-warning {
        @apply bg-yellow-900/20 text-yellow-300 border-yellow-700;
    }

    .dark .livewire-alert-error {
        @apply bg-red-900/20 text-red-300 border-red-700;
    }

    .dark .livewire-alert-info {
        @apply bg-blue-900/20 text-blue-300 border-blue-700;
    }

    /* Livewire Form Elements */
    .livewire-input, .livewire-textarea, .livewire-select {
        @apply w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent;
    }

    .livewire-input:focus, .livewire-textarea:focus, .livewire-select:focus {
        @apply outline-none ring-2 ring-blue-500 border-transparent;
    }

    /* Livewire Tables */
    .livewire-table {
        @apply w-full border-collapse;
    }

    .livewire-table th {
        @apply px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600;
    }

    .livewire-table td {
        @apply px-4 py-3 text-sm text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-600;
    }

    .livewire-table tbody tr:hover {
        @apply bg-gray-50 dark:bg-gray-700;
    }

    /* Animations */
    @keyframes livewire-fade-in {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes livewire-pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .livewire-fade-in {
        animation: livewire-fade-in 0.3s ease-out;
    }

    .livewire-pulse {
        animation: livewire-pulse 2s infinite;
    }

    /* Wire:dirty and wire:offline states */
    [wire\:dirty] {
        @apply border-yellow-400 bg-yellow-50 dark:bg-yellow-900/20;
    }

    [wire\:offline] {
        @apply opacity-50 pointer-events-none;
    }

    [wire\:offline]::after {
        content: 'Offline - Reconnecting...';
        @apply absolute top-2 right-2 px-2 py-1 bg-red-500 text-white text-xs rounded;
    }

    /* Custom scrollbars for Livewire components */
    .livewire-component ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    .livewire-component ::-webkit-scrollbar-track {
        @apply bg-gray-100 dark:bg-gray-700 rounded;
    }

    .livewire-component ::-webkit-scrollbar-thumb {
        @apply bg-gray-300 dark:bg-gray-500 rounded;
    }

    .livewire-component ::-webkit-scrollbar-thumb:hover {
        @apply bg-gray-400 dark:bg-gray-400;
    }

    /* Theme-specific overrides */
    @if($theme === 'dark')
        body {
            @apply bg-gray-900 text-gray-100;
        }
    @endif
</style>

{{-- Theme-specific JavaScript for enhanced functionality --}}
<script>
    // Enhanced Livewire loading indicators
    document.addEventListener('livewire:init', function () {
        // Add loading class to components during requests
        Livewire.hook('morph.updating', ({ component, cleanup }) => {
            component.el.classList.add('livewire-loading');

            cleanup(() => {
                component.el.classList.remove('livewire-loading');
            });
        });

        // Enhanced dirty state tracking
        Livewire.hook('morph.updated', ({ el, component }) => {
            // Auto-remove dirty state after successful update
            setTimeout(() => {
                el.querySelectorAll('[wire\\:dirty]').forEach(element => {
                    element.classList.remove('wire-dirty');
                });
            }, 1000);
        });

        // Offline state management
        window.addEventListener('offline', () => {
            document.body.classList.add('livewire-offline');
        });

        window.addEventListener('online', () => {
            document.body.classList.remove('livewire-offline');
        });
    });

    // Auto-focus first input in Livewire forms
    document.addEventListener('livewire:navigated', function () {
        const firstInput = document.querySelector('form [wire\\:model]:first-of-type');
        if (firstInput && !firstInput.value) {
            setTimeout(() => firstInput.focus(), 100);
        }
    });
</script>