@extends('layouts.app')

@section('title', $websiteIdentity->name . ' - ' . $websiteIdentity->tagline)
@section('description', $websiteIdentity->description)
@section('keywords', 'dokter, praktek pribadi, konsultasi, kesehatan, ' . strtolower($websiteIdentity->name))

@section('content')
    <!-- Navigation -->
    <x-templates.doctor.navbar :website-identity="$websiteIdentity" />
    
    <!-- Hero Section -->
    <x-templates.doctor.hero :website-identity="$websiteIdentity" />
    
    <!-- About Section -->
    <x-templates.doctor.about :website-identity="$websiteIdentity" />
    
    <!-- Services Section -->
    <x-templates.doctor.services :website-identity="$websiteIdentity" />
    
    <!-- Schedule Section -->
    <x-templates.doctor.schedule :website-identity="$websiteIdentity" />
    
    <!-- Testimonials Section -->
    <x-templates.doctor.testimonials :website-identity="$websiteIdentity" />
    
    <!-- Blog Section -->
    <x-landing.blog :blogs="$blogs" :website-identity="$websiteIdentity" />
    
    <!-- Contact Section -->
    <x-templates.doctor.contact :website-identity="$websiteIdentity" />
    
    <!-- Footer -->
    <x-templates.doctor.footer :website-identity="$websiteIdentity" />
@endsection