<section id="beranda" 
         class="relative min-h-screen flex items-center overflow-hidden bg-pattern"
         style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
    
    <!-- Background Elements -->
    <div class="absolute inset-0">
        <!-- Floating Shapes -->
        <div class="absolute top-1/4 left-1/4 w-32 h-32 bg-white bg-opacity-10 rounded-full blur-xl float-animation"></div>
        <div class="absolute top-1/2 right-1/4 w-24 h-24 bg-white bg-opacity-5 rounded-full blur-xl float-animation" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-1/4 left-1/3 w-40 h-40 bg-white bg-opacity-5 rounded-full blur-xl float-animation" style="animation-delay: 4s;"></div>
        
        <!-- Gradient Orb -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-bl from-white/20 to-transparent rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-gradient-to-tr from-white/10 to-transparent rounded-full blur-3xl"></div>
    </div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center min-h-screen py-20">
            <!-- Content Column -->
            <div class="text-white space-y-8 animate-on-scroll">
                <!-- Badge -->
                <div class="inline-flex items-center px-4 py-2 bg-white/20 rounded-full backdrop-blur-sm border border-white/30">
                    <span class="text-sm font-medium">✨ Selamat datang di {{ $websiteIdentity->name }}</span>
                </div>
                
                <!-- Main Heading -->
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight">
                    <span class="block">{{ $websiteIdentity->name }}</span>
                    <span class="block text-white/90 mt-2 text-2xl md:text-3xl lg:text-4xl font-normal">
                        {{ $websiteIdentity->tagline }}
                    </span>
                </h1>
                
                <!-- Description -->
                <p class="text-lg md:text-xl text-white/80 leading-relaxed max-w-xl">
                    {{ $websiteIdentity->description }}
                </p>
                
                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 items-start">
                    <a href="#fitur" class="relative inline-flex items-center px-8 py-4 bg-white text-blue-600 font-bold text-lg rounded-2xl shadow-xl hover:shadow-2xl focus:outline-none focus:ring-4 focus:ring-white/50 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 group overflow-hidden">
                        <!-- Background Animation -->
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-blue-700 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left rounded-2xl"></div>
                        
                        <!-- Content -->
                        <div class="relative z-10 flex items-center group-hover:text-white transition-colors duration-300">
                            <div class="w-6 h-6 mr-3 bg-blue-600 group-hover:bg-white/20 rounded-full flex items-center justify-center transition-all duration-300">
                                <i class="fas fa-rocket text-white group-hover:text-white text-sm transition-all duration-300 group-hover:rotate-12 group-hover:scale-110"></i>
                            </div>
                            Start Now
                        </div>
                        
                        <!-- Shine Effect -->
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent skew-x-12 transform -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                    </a>
                    
                    <a href="#kontak" class="relative inline-flex items-center px-8 py-4 bg-transparent border-2 border-white text-white font-bold text-lg rounded-2xl shadow-lg hover:shadow-xl hover:bg-white hover:text-blue-600 focus:outline-none focus:ring-4 focus:ring-white/50 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 group">
                        <div class="relative z-10 flex items-center">
                            <div class="w-6 h-6 mr-3 bg-white/20 group-hover:bg-blue-600 rounded-full flex items-center justify-center transition-all duration-300">
                                <i class="fas fa-phone text-white group-hover:text-white text-sm transition-all duration-300 group-hover:rotate-12"></i>
                            </div>
                            Contact Us
                        </div>
                    </a>
                </div>
                
                <!-- Trust Indicators -->
                <div class="flex items-center space-x-6 pt-4">
                    <div class="flex items-center text-white/80">
                        <div class="flex -space-x-2 mr-3">
                            <div class="w-8 h-8 bg-white/20 rounded-full border-2 border-white flex items-center justify-center">
                                <i class="fas fa-check text-xs"></i>
                            </div>
                            <div class="w-8 h-8 bg-white/20 rounded-full border-2 border-white flex items-center justify-center">
                                <i class="fas fa-shield-alt text-xs"></i>
                            </div>
                            <div class="w-8 h-8 bg-white/20 rounded-full border-2 border-white flex items-center justify-center">
                                <i class="fas fa-clock text-xs"></i>
                            </div>
                        </div>
                        <span class="text-sm font-medium">Trusted by 1000+ companies</span>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="flex flex-wrap gap-8 pt-8">
                    <div class="text-center">
                        <div class="text-3xl font-bold">24/7</div>
                        <div class="text-white/70 text-sm">Dukungan</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">99%</div>
                        <div class="text-white/70 text-sm">Uptime</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">100%</div>
                        <div class="text-white/70 text-sm">Aman</div>
                    </div>
                </div>
            </div>
            
            <!-- Image Column -->
            <div class="relative animate-on-scroll" style="animation-delay: 200ms;">
                <div class="relative">
                    <!-- Decorative Elements -->
                    <div class="absolute -inset-4 bg-white/10 rounded-3xl blur-xl"></div>
                    <div class="absolute top-4 right-4 w-24 h-24 bg-white/20 rounded-full blur-lg"></div>
                    <div class="absolute bottom-4 left-4 w-32 h-32 bg-white/10 rounded-full blur-lg"></div>
                    
                    <!-- Main Image Container -->
                    <div class="relative bg-white/20 backdrop-blur-sm rounded-3xl p-8 border border-white/30 shadow-2xl">
                        @if($websiteIdentity->logo)
                            <img src="{{ asset('storage/' . $websiteIdentity->logo) }}" 
                                 alt="{{ $websiteIdentity->name }}"
                                 class="w-full max-w-md mx-auto h-auto object-contain filter drop-shadow-2xl lazy-image"
                                 loading="lazy">
                        @else
                            <!-- Placeholder -->
                            <div class="flex items-center justify-center h-96 bg-white/10 rounded-2xl border-2 border-dashed border-white/30">
                                <div class="text-center">
                                    <i class="fas fa-building text-6xl text-white/50 mb-4"></i>
                                    <p class="text-white/70">Logo Perusahaan</p>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Floating Elements -->
                        <div class="absolute -top-6 -left-6 w-12 h-12 bg-yellow-400 rounded-full flex items-center justify-center shadow-lg animate-bounce">
                            <i class="fas fa-star text-white text-sm"></i>
                        </div>
                        <div class="absolute -bottom-6 -right-6 w-12 h-12 bg-green-400 rounded-full flex items-center justify-center shadow-lg animate-pulse">
                            <i class="fas fa-check text-white text-sm"></i>
                        </div>
                    </div>
                    
                    <!-- Additional Decorative Cards -->
                    <div class="absolute top-8 -left-8 glass rounded-2xl p-4 shadow-xl hidden md:block">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                            <span class="text-white text-sm font-medium">System Online</span>
                        </div>
                    </div>
                    
                    <div class="absolute bottom-8 -right-8 glass rounded-2xl p-4 shadow-xl hidden md:block">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-users text-white text-xs"></i>
                            </div>
                            <div>
                                <div class="text-white text-sm font-medium">Active Users</div>
                                <div class="text-white/70 text-xs">1,234 online</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-on-scroll" style="animation-delay: 600ms;">
            <a href="#tentang" 
               class="flex flex-col items-center text-white/80 hover:text-white transition-colors duration-300 group">
                <span class="text-sm mb-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">Scroll untuk lanjut</span>
                <div class="w-6 h-10 border-2 border-white/50 rounded-full flex justify-center">
                    <div class="w-1 h-3 bg-white rounded-full mt-2 animate-bounce"></div>
                </div>
            </a>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Parallax effect for hero section
    const heroSection = document.getElementById('beranda');
    
    if (heroSection && window.innerWidth > 768) {
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;
            
            const floatingElements = heroSection.querySelectorAll('.float-animation');
            floatingElements.forEach((el, index) => {
                const speed = (index + 1) * 0.1;
                el.style.transform = `translateY(${rate * speed}px)`;
            });
        }, { passive: true });
    }
    
    // Intersection Observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        observer.observe(el);
    });
});
</script>