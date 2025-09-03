@props(['title' => null, 'description' => null])

@php
    $identity = website_identity();
    $siteTitle = $title ? $title . ' - ' . website_name() : website_name();
    $siteDescription = $description ?: $identity->description;
@endphp

{{-- Basic Meta Tags --}}
<title>{{ $siteTitle }}</title>
<meta name="description" content="{{ $siteDescription }}">
<meta name="keywords" content="{{ $identity->tagline ?? 'sistem manajemen pegawai' }}">
<meta name="author" content="{{ $identity->name ?? 'WebKhanza' }}">

{{-- Favicon --}}
<link rel="icon" type="image/x-icon" href="{{ website_favicon() }}">
<link rel="shortcut icon" href="{{ website_favicon() }}">

{{-- Open Graph Meta Tags --}}
<meta property="og:title" content="{{ $siteTitle }}">
<meta property="og:description" content="{{ $siteDescription }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
@if(website_logo())
<meta property="og:image" content="{{ website_logo() }}">
@endif
<meta property="og:site_name" content="{{ website_name() }}">

{{-- Twitter Card Meta Tags --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $siteTitle }}">
<meta name="twitter:description" content="{{ $siteDescription }}">
@if(website_logo())
<meta name="twitter:image" content="{{ website_logo() }}">
@endif

{{-- Additional Meta Tags --}}
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta charset="utf-8">
<meta name="robots" content="index, follow">
<meta name="googlebot" content="index, follow">
<meta name="application-name" content="{{ website_name() }}">
<meta name="msapplication-TileColor" content="#fbbf24">
<meta name="theme-color" content="#fbbf24">