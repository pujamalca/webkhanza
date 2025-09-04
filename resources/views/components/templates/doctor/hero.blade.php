<section id="beranda" class="relative min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 flex items-center">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg">
            <g fill="none" fill-rule="evenodd">
                <g fill="#3B82F6" fill-opacity="0.1">
                    <circle cx="30" cy="30" r="4"/>
                    <circle cx="0" cy="30" r="4"/>
                    <circle cx="60" cy="30" r="4"/>
                    <circle cx="30" cy="0" r="4"/>
                    <circle cx="30" cy="60" r="4"/>
                </g>
            </g>
        </svg>
    </div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center min-h-screen py-20">
            <!-- Content Column -->
            <div class="space-y-8 animate-on-scroll">
                <!-- Professional Badge -->
                <div class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-full border border-green-200">
                    <i class="fas fa-stethoscope text-sm mr-2"></i>
                    <span class="text-sm font-medium">Praktik Dokter Profesional</span>
                </div>
                
                <!-- Main Heading -->
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight text-gray-900">
                    <span class="block">{{ $websiteIdentity->name }}</span>
                    <span class="block text-blue-600 mt-2">{{ $websiteIdentity->tagline }}</span>
                </h1>
                
                <!-- Description -->
                <p class="text-lg md:text-xl text-gray-600 leading-relaxed max-w-xl">
                    {{ $websiteIdentity->description }}
                </p>
                
                <!-- Doctor Info Card -->
                <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 max-w-md">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-md text-white text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Dr. {{ explode(' ', $websiteIdentity->name)[0] ?? $websiteIdentity->name }}</h3>
                            <p class="text-gray-600">Dokter Umum</p>
                            <div class="flex items-center mt-1">
                                <div class="flex text-yellow-400">
                                    <i class="fas fa-star text-sm"></i>
                                    <i class="fas fa-star text-sm"></i>
                                    <i class="fas fa-star text-sm"></i>
                                    <i class="fas fa-star text-sm"></i>
                                    <i class="fas fa-star text-sm"></i>
                                </div>
                                <span class="text-sm text-gray-500 ml-2">5.0 (100+ ulasan)</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 items-start">
                    <a href="#kontak" class="inline-flex items-center px-8 py-4 bg-blue-600 text-white font-bold text-lg rounded-2xl shadow-lg hover:bg-blue-700 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-blue-300 transition-all duration-300 transform hover:scale-105 group">
                        <div class="w-6 h-6 mr-3 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-calendar text-white text-sm"></i>
                        </div>
                        <span class="text-white font-bold">Buat Janji</span>
                    </a>
                    
                    <a href="#jadwal" class="inline-flex items-center px-8 py-4 bg-white border-2 border-blue-600 text-blue-600 font-bold text-lg rounded-2xl shadow-lg hover:bg-blue-600 hover:text-white focus:outline-none focus:ring-4 focus:ring-blue-300 transition-all duration-300 transform hover:scale-105 group">
                        <div class="w-6 h-6 mr-3 bg-blue-600 group-hover:bg-white/20 rounded-full flex items-center justify-center transition-all duration-300">
                            <i class="fas fa-clock text-white text-sm"></i>
                        </div>
                        <span class="text-blue-600 group-hover:text-white font-bold transition-colors duration-300">Lihat Jadwal</span>
                    </a>
                </div>
                
                <!-- Contact Info -->
                <div class="flex flex-wrap gap-6 pt-4">
                    <div class="flex items-center text-gray-600">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-phone text-green-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Telepon</p>
                            <p class="text-sm text-gray-600">{{ $websiteIdentity->phone }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center text-gray-600">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-map-marker-alt text-blue-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Lokasi</p>
                            <p class="text-sm text-gray-600">Praktik Pribadi</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Image Column -->
            <div class="relative animate-on-scroll" style="animation-delay: 200ms;">
                <div class="relative">
                    <!-- Main Image Container -->
                    <div class="relative bg-white rounded-3xl p-8 shadow-2xl border border-gray-100">
                        @if($websiteIdentity->logo)
                            <img src="{{ asset('storage/' . $websiteIdentity->logo) }}" 
                                 alt="{{ $websiteIdentity->name }}"
                                 class="w-full max-w-md mx-auto h-auto object-contain lazy-image">
                        @else
                            <!-- Doctor Illustration -->
                            <div class="flex items-center justify-center h-96 bg-gradient-to-br from-blue-50 to-green-50 rounded-2xl border border-gray-100">
                                <div class="text-center">
                                    <div class="w-32 h-32 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-6">
                                        <i class="fas fa-user-md text-white text-5xl"></i>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Dokter Profesional</h3>
                                    <p class="text-gray-600">Pelayanan kesehatan terpercaya</p>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Floating Elements -->
                        <div class="absolute -top-6 -left-6 w-12 h-12 bg-green-500 rounded-full flex items-center justify-center shadow-lg animate-bounce">
                            <i class="fas fa-heartbeat text-white text-sm"></i>
                        </div>
                        <div class="absolute -bottom-6 -right-6 w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center shadow-lg animate-pulse">
                            <i class="fas fa-shield-alt text-white text-sm"></i>
                        </div>
                    </div>
                    
                    <!-- Decorative Cards -->
                    <div class="absolute top-8 -left-8 bg-white rounded-2xl p-4 shadow-xl border border-gray-100 hidden md:block">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                            <span class="text-gray-800 text-sm font-medium">Online Today</span>
                        </div>
                    </div>
                    
                    <div class="absolute bottom-8 -right-8 bg-white rounded-2xl p-4 shadow-xl border border-gray-100 hidden md:block">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-calendar text-white text-xs"></i>
                            </div>
                            <div>
                                <div class="text-gray-800 text-sm font-medium">Tersedia Hari Ini</div>
                                <div class="text-gray-500 text-xs">Buat janji konsultasi</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Background Decoration -->
                    <div class="absolute -inset-4 bg-gradient-to-r from-blue-100/50 to-green-100/50 rounded-3xl blur-xl -z-10"></div>
                </div>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-on-scroll" style="animation-delay: 600ms;">
            <a href="#tentang" 
               class="flex flex-col items-center text-gray-600 hover:text-blue-600 transition-colors duration-300 group">
                <span class="text-sm mb-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">Scroll untuk lanjut</span>
                <div class="w-6 h-10 border-2 border-gray-400 group-hover:border-blue-600 rounded-full flex justify-center transition-colors duration-300">
                    <div class="w-1 h-3 bg-gray-400 group-hover:bg-blue-600 rounded-full mt-2 animate-bounce transition-colors duration-300"></div>
                </div>
            </a>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
    
    // Parallax effect for floating elements
    if (window.innerWidth > 768) {
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;
            
            const floatingElements = document.querySelectorAll('.animate-bounce, .animate-pulse');
            floatingElements.forEach((el, index) => {
                const speed = (index + 1) * 0.1;
                el.style.transform = `translateY(${rate * speed}px)`;
            });
        }, { passive: true });
    }
});
</script>

<style>
.animate-on-scroll {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s ease-out;
}

.animate-on-scroll.in-view {
    opacity: 1;
    transform: translateY(0);
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

.float-animation {
    animation: float 6s ease-in-out infinite;
}
</style>