<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', $websiteIdentity->name ?? 'WebKhanza')</title>
    <meta name="description" content="@yield('description', $websiteIdentity->description ?? 'Sistem Manajemen Pegawai dan Absensi')">
    <meta name="keywords" content="@yield('keywords', 'webkhanza, manajemen pegawai, absensi, sistem informasi')">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:title" content="@yield('title', $websiteIdentity->name ?? 'WebKhanza')">
    <meta property="og:description" content="@yield('description', $websiteIdentity->description ?? 'Sistem Manajemen Pegawai dan Absensi')">
    @if($websiteIdentity->logo)
    <meta property="og:image" content="{{ asset('storage/' . $websiteIdentity->logo) }}">
    @endif
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ request()->url() }}">
    <meta property="twitter:title" content="@yield('title', $websiteIdentity->name ?? 'WebKhanza')">
    <meta property="twitter:description" content="@yield('description', $websiteIdentity->description ?? 'Sistem Manajemen Pegawai dan Absensi')">
    @if($websiteIdentity->logo)
    <meta property="twitter:image" content="{{ asset('storage/' . $websiteIdentity->logo) }}">
    @endif
    
    <!-- Favicon -->
    @if($websiteIdentity->favicon)
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $websiteIdentity->favicon) }}">
    @else
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px) rotate(0deg)' },
                            '50%': { transform: 'translateY(-20px) rotate(3deg)' },
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Theme Styles -->
    <x-theme-styles />
    
    <!-- Custom Component Styles -->
    <style>
        .btn-primary {
            @apply inline-flex items-center justify-center px-8 py-3 text-base font-semibold text-white rounded-full shadow-lg hover:shadow-xl hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-blue-300/50 transition-all duration-300 ease-in-out transform;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e3a8a);
            transform: translateY(-2px) scale(1.02);
        }
        
        .btn-outline {
            @apply inline-flex items-center justify-center px-8 py-3 text-base font-semibold bg-transparent border-2 rounded-full hover:text-white hover:shadow-lg hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-blue-300/50 transition-all duration-300 ease-in-out transform;
            color: #3b82f6;
            border-color: #3b82f6;
        }
        
        .btn-outline:hover {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border-color: transparent;
            transform: translateY(-2px) scale(1.02);
        }
        
        .btn-white {
            @apply inline-flex items-center justify-center px-8 py-3 text-base font-semibold bg-white border-2 border-white rounded-full shadow-lg hover:bg-transparent hover:text-white hover:border-white hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-white/50 transition-all duration-300 ease-in-out transform;
            color: #3b82f6;
        }
        
        .btn-white:hover {
            background: transparent;
            color: white;
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 0 30px rgba(255, 255, 255, 0.3);
        }
        
        .navbar-link {
            @apply text-gray-600 hover:text-blue-600 font-medium transition-colors duration-200 relative;
        }
        
        .navbar-link::after {
            content: '';
            @apply absolute bottom-0 left-1/2 w-0 h-0.5 transition-all duration-300 transform -translate-x-1/2;
            background-color: var(--color-primary);
        }
        
        .navbar-link:hover::after,
        .navbar-link.active::after {
            @apply w-full;
        }
        
        .text-gradient {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .bg-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='m36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        
        .glass {
            @apply bg-white bg-opacity-10 backdrop-blur-md border border-white border-opacity-20;
        }
        
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        .animate-on-scroll {
            @apply opacity-0 transform translate-y-8 transition-all duration-700 ease-out;
        }
        
        .animate-on-scroll.in-view {
            @apply opacity-100 translate-y-0;
        }
        
        .lazy-image {
            @apply opacity-0 transition-opacity duration-500 ease-in-out;
        }
        
        .lazy-image.loaded {
            @apply opacity-100;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(3deg); }
        }
    </style>
    
    @stack('styles')
</head>
<body class="scroll-smooth">
    @yield('content')
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Advanced Lazy Loading -->
    <script src="{{ asset('js/advanced-lazy-loading.js') }}"></script>
    
    <!-- Interactive Elements -->
    <script src="{{ asset('js/interactive-elements.js') }}"></script>
    
    @stack('scripts')
</body>
</html>