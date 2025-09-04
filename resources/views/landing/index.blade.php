@extends('layouts.app')

@section('title', $websiteIdentity->name . ' - ' . $websiteIdentity->tagline)
@section('description', $websiteIdentity->description)
@section('keywords', 'webkhanza, manajemen pegawai, absensi, sistem informasi, ' . strtolower($websiteIdentity->name))

@section('content')
    <!-- Modern Loading Screen -->
    <div id="loadingScreen" class="fixed inset-0 z-50 flex items-center justify-center bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800">
        <div class="text-center">
            <!-- Logo Animation -->
            <div class="relative mb-8">
                <div class="w-20 h-20 mx-auto mb-4 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30">
                    <i class="fas fa-rocket text-white text-3xl animate-bounce"></i>
                </div>
                <div class="absolute inset-0 w-20 h-20 mx-auto bg-white/10 rounded-2xl animate-ping"></div>
            </div>
            
            <!-- Loading Text -->
            <h2 class="text-2xl font-bold text-white mb-2">{{ $websiteIdentity->name ?? 'WebKhanza' }}</h2>
            <p class="text-blue-100 mb-8">Preparing amazing experience...</p>
            
            <!-- Modern Progress Bar -->
            <div class="w-64 h-2 mx-auto bg-white/20 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-white to-blue-200 rounded-full animate-progress"></div>
            </div>
            
            <!-- Loading Dots -->
            <div class="flex justify-center mt-6 space-x-2">
                <div class="w-2 h-2 bg-white rounded-full animate-pulse" style="animation-delay: 0s;"></div>
                <div class="w-2 h-2 bg-white rounded-full animate-pulse" style="animation-delay: 0.2s;"></div>
                <div class="w-2 h-2 bg-white rounded-full animate-pulse" style="animation-delay: 0.4s;"></div>
            </div>
        </div>
        
        <!-- Background Animation -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 left-1/4 w-32 h-32 bg-white/5 rounded-full blur-xl animate-float"></div>
            <div class="absolute top-3/4 right-1/4 w-24 h-24 bg-white/3 rounded-full blur-xl animate-float" style="animation-delay: 2s;"></div>
            <div class="absolute bottom-1/4 left-1/3 w-28 h-28 bg-white/4 rounded-full blur-xl animate-float" style="animation-delay: 4s;"></div>
        </div>
    </div>

    <!-- Navigation -->
    <x-landing.navbar :website-identity="$websiteIdentity" />
    
    <!-- Hero Section -->
    <x-landing.hero :website-identity="$websiteIdentity" />
    
    <!-- About Section -->
    <x-landing.about :website-identity="$websiteIdentity" />
    
    <!-- Features Section -->
    <x-landing.features />
    
    <!-- Contact Section -->
    <x-landing.contact :website-identity="$websiteIdentity" />
    
    <!-- Footer -->
    <x-landing.footer :website-identity="$websiteIdentity" />
@endsection

@push('styles')
<style>
    /* Modern Loading Animations */
    @keyframes animate-progress {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    
    .animate-progress {
        animation: animate-progress 2s ease-in-out infinite;
    }
    
    @keyframes fadeInScale {
        0% { 
            opacity: 0; 
            transform: scale(0.8) translateY(20px); 
        }
        100% { 
            opacity: 1; 
            transform: scale(1) translateY(0); 
        }
    }
    
    /* Loading Screen Transition */
    .loading-exit {
        opacity: 0;
        transform: scale(1.1);
        transition: all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }
    
    /* Section divider */
    .section-divider {
        height: 2px;
        background: linear-gradient(90deg, transparent, #3b82f6, transparent);
        margin: 0 auto;
        width: 100px;
    }
    
    /* Scroll Reveal Animation */
    [data-aos] {
        opacity: 0;
        transition-property: opacity, transform;
    }
    
    [data-aos].aos-animate {
        opacity: 1;
    }
    
    /* Custom scrollbar for modern browsers */
    ::-webkit-scrollbar {
        width: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    ::-webkit-scrollbar-thumb {
        background: linear-gradient(45deg, var(--color-primary), var(--color-secondary));
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: var(--color-secondary);
    }
    
    /* Smooth hover effects */
    .hover-lift {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }
    
    /* Mobile menu improvements */
    @media (max-width: 991.98px) {
        .navbar-collapse {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            margin-top: 1rem;
            padding: 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-nav .nav-link {
            padding: 0.75rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .navbar-nav .nav-link:last-child {
            border-bottom: none;
        }
    }
    
    /* Print styles */
    @media print {
        .navbar, .footer, #backToTop {
            display: none !important;
        }
        
        .hero-section {
            min-height: auto;
            padding: 2rem 0;
        }
        
        .section-padding {
            padding: 1rem 0;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Enhanced page loading with modern animation
    document.addEventListener('DOMContentLoaded', function() {
        const loadingScreen = document.getElementById('loadingScreen');
        
        if (loadingScreen) {
            // Simulate loading progress
            let progress = 0;
            const progressBar = loadingScreen.querySelector('.animate-progress');
            
            const simulateLoading = () => {
                progress += Math.random() * 30;
                if (progress >= 100) {
                    progress = 100;
                    // Complete loading
                    setTimeout(() => {
                        loadingScreen.classList.add('loading-exit');
                        setTimeout(() => {
                            loadingScreen.style.display = 'none';
                            // Trigger page animations
                            document.body.classList.add('page-loaded');
                        }, 600);
                    }, 500);
                } else {
                    setTimeout(simulateLoading, 200 + Math.random() * 300);
                }
            };
            
            // Start loading simulation
            setTimeout(simulateLoading, 300);
        }
        
        // Enhanced smooth scrolling with offset for fixed navbar
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    const headerHeight = document.querySelector('.navbar').offsetHeight;
                    const targetPosition = targetElement.offsetTop - headerHeight;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
        
        // Parallax effect for hero section
        const heroSection = document.querySelector('.hero-section');
        if (heroSection) {
            window.addEventListener('scroll', function() {
                const scrolled = window.pageYOffset;
                const parallax = heroSection.querySelector('.hero-content');
                if (parallax && scrolled < heroSection.offsetHeight) {
                    parallax.style.transform = `translateY(${scrolled * 0.1}px)`;
                }
            });
        }
        
        // Add intersection observer for animation triggers
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, observerOptions);
        
        // Observe all sections
        document.querySelectorAll('section').forEach(section => {
            observer.observe(section);
        });
        
        // Add hover effects to interactive elements
        document.querySelectorAll('.feature-card, .contact-card').forEach(card => {
            card.classList.add('hover-lift');
        });
        
        // Enhanced navbar scroll effect
        let lastScrollTop = 0;
        const navbar = document.querySelector('.navbar');
        
        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            // Add/remove scrolled class
            if (scrollTop > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
            
            // Hide/show navbar on scroll
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                // Scrolling down
                navbar.style.transform = 'translateY(-100%)';
            } else {
                // Scrolling up
                navbar.style.transform = 'translateY(0)';
            }
            
            lastScrollTop = scrollTop;
        });
        
        // Form validation enhancement
        const contactForm = document.getElementById('contactForm');
        if (contactForm) {
            contactForm.addEventListener('submit', function(e) {
                // Add custom validation logic here if needed
                const requiredFields = contactForm.querySelectorAll('[required]');
                let isValid = true;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Mohon lengkapi semua field yang diperlukan.');
                }
            });
        }
        
        // Add loading states to buttons
        document.querySelectorAll('.btn-primary-custom').forEach(button => {
            button.addEventListener('click', function() {
                if (this.type === 'submit') {
                    this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...';
                    this.disabled = true;
                    
                    setTimeout(() => {
                        this.disabled = false;
                        this.innerHTML = this.dataset.originalText || this.innerHTML;
                    }, 3000);
                }
            });
        });
    });
    
    // Handle page visibility changes
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            document.title = '👋 Kembali ke ' + '{{ $websiteIdentity->name }}';
        } else {
            document.title = '{{ $websiteIdentity->name }} - {{ $websiteIdentity->tagline }}';
        }
    });
    
    // Performance optimization: Lazy loading for images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
</script>
@endpush