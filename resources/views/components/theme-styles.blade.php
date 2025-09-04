@php
    $themeService = app(\App\Services\WebsiteThemeService::class);
@endphp

<style>
    {!! $themeService->generateCssVariables() !!}
    
    /* Additional theme-based styles */
    .btn-primary {
        background-color: var(--color-primary);
        border-color: var(--color-primary);
    }
    
    .btn-primary:hover {
        background-color: var(--color-secondary);
        border-color: var(--color-secondary);
    }
    
    .text-primary {
        color: var(--color-primary) !important;
    }
    
    .bg-primary {
        background-color: var(--color-primary) !important;
    }
    
    .border-primary {
        border-color: var(--color-primary) !important;
    }
    
    .btn-accent {
        background-color: var(--color-accent);
        border-color: var(--color-accent);
        color: white;
    }
    
    .btn-accent:hover {
        background-color: rgba(var(--color-accent-rgb), 0.8);
        border-color: rgba(var(--color-accent-rgb), 0.8);
    }
    
    /* Header dengan primary color */
    .app-header {
        background-color: var(--color-primary);
    }
    
    /* Sidebar dengan secondary color */
    .app-sidebar {
        background-color: var(--color-secondary);
    }
    
    /* Links dengan primary color */
    a {
        color: var(--color-primary);
    }
    
    a:hover {
        color: var(--color-secondary);
    }
</style>