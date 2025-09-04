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
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Theme Styles -->
    <x-theme-styles />
    
    
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