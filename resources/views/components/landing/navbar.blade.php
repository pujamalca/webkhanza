<nav x-data="{ open: false, scrolled: false }" 
     x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 50)"
     :class="{ 'shadow-xl': scrolled }"
     class="fixed top-0 left-0 right-0 z-50 bg-white transition-all duration-300 py-4">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between">
            <!-- Logo & Brand -->
            <a href="{{ route('landing.index') }}" class="flex items-center space-x-3 group">
                @if($websiteIdentity->logo)
                    <img src="{{ asset('storage/' . $websiteIdentity->logo) }}" 
                         alt="{{ $websiteIdentity->name }}" 
                         class="h-10 w-10 object-contain transition-transform duration-300 group-hover:scale-110 lazy-image"
                         loading="lazy">
                @endif
                <span class="text-xl font-bold text-gradient hover:scale-105 transition-transform duration-300">
                    {{ $websiteIdentity->name }}
                </span>
            </a>
            
            <!-- Desktop Navigation -->
            <div class="hidden lg:flex items-center space-x-8">
                <a href="#beranda" class="navbar-link">Beranda</a>
                <a href="#tentang" class="navbar-link">Tentang</a>
                <a href="#fitur" class="navbar-link">Fitur</a>
                <a href="#blog" class="navbar-link">Blog</a>
                <a href="#kontak" class="navbar-link">Kontak</a>
                <a href="/admin" class="relative inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-300 transform hover:scale-105 group">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10 flex items-center">
                        <div class="w-5 h-5 mr-2 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-crown text-xs transition-transform duration-300 group-hover:rotate-12"></i>
                        </div>
                        Admin
                    </div>
                    <div class="absolute top-0 right-0 w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                </a>
            </div>
            
            <!-- Mobile Menu Button -->
            <button @click="open = !open" 
                    class="lg:hidden p-2 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200"
                    :aria-expanded="open.toString()"
                    aria-label="Toggle navigation menu">
                <svg class="w-6 h-6 transition-transform duration-200" 
                     :class="{ 'rotate-45': open }"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <!-- Mobile Navigation -->
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2"
             class="lg:hidden mt-4 bg-white rounded-2xl p-6 shadow-xl border border-gray-100">
            <div class="flex flex-col space-y-4">
                <a href="#beranda" @click="open = false" 
                   class="navbar-link text-center py-3 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                    Beranda
                </a>
                <a href="#tentang" @click="open = false" 
                   class="navbar-link text-center py-3 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                    Tentang
                </a>
                <a href="#fitur" @click="open = false" 
                   class="navbar-link text-center py-3 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                    Fitur
                </a>
                <a href="#blog" @click="open = false" 
                   class="navbar-link text-center py-3 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                    Blog
                </a>
                <a href="#kontak" @click="open = false" 
                   class="navbar-link text-center py-3 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                    Kontak
                </a>
                <div class="pt-4 border-t border-gray-200">
                    <a href="/admin" class="relative w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:from-blue-700 hover:to-blue-800 focus:outline-none transition-all duration-300 transform hover:scale-105 group">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative z-10 flex items-center">
                            <div class="w-5 h-5 mr-2 bg-white/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-crown text-xs transition-transform duration-300 group-hover:rotate-12"></i>
                            </div>
                            Admin Panel
                        </div>
                        <div class="absolute top-1 right-1 w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced active nav link highlighting
    const navLinks = document.querySelectorAll('.navbar-link[href^="#"]');
    const sections = document.querySelectorAll('section[id]');
    
    if (navLinks.length && sections.length) {
        const updateActiveLink = () => {
            const scrollPosition = window.scrollY + 150;
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.offsetHeight;
                const sectionId = section.getAttribute('id');
                
                if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                    navLinks.forEach(link => {
                        link.classList.remove('active');
                        if (link.getAttribute('href') === `#${sectionId}`) {
                            link.classList.add('active');
                        }
                    });
                }
            });
        };
        
        window.addEventListener('scroll', updateActiveLink, { passive: true });
        updateActiveLink();
    }
    
    // Enhanced smooth scrolling with offset
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                const offsetTop = targetElement.offsetTop - 80;
                
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
                
                // Close mobile menu if open
                if (window.innerWidth < 1024) {
                    const alpineComponent = document.querySelector('[x-data]').__x.$data;
                    if (alpineComponent) {
                        alpineComponent.open = false;
                    }
                }
            }
        });
    });
});
</script>