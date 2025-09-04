<nav class="bg-white/95 backdrop-blur-md shadow-lg sticky top-0 z-50 border-b border-gray-100">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <div class="flex items-center space-x-3">
                @if($websiteIdentity->logo)
                    <img src="{{ asset('storage/' . $websiteIdentity->logo) }}" 
                         alt="{{ $websiteIdentity->name }}"
                         class="h-10 w-auto object-contain">
                @else
                    <div class="h-10 w-10 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-md text-white text-lg"></i>
                    </div>
                @endif
                <div>
                    <h1 class="text-xl font-bold text-gray-900">{{ $websiteIdentity->name }}</h1>
                    <p class="text-xs text-gray-500">Dokter Praktek Pribadi</p>
                </div>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="#beranda" class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200 scroll-smooth">
                    Beranda
                </a>
                <a href="#tentang" class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200 scroll-smooth">
                    Tentang
                </a>
                <a href="#layanan" class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200 scroll-smooth">
                    Layanan
                </a>
                <a href="#jadwal" class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200 scroll-smooth">
                    Jadwal Praktik
                </a>
                <a href="#testimoni" class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200 scroll-smooth">
                    Testimoni
                </a>
                <a href="#blog" class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200 scroll-smooth">
                    Artikel
                </a>
                <a href="#kontak" class="bg-blue-600 text-white px-6 py-2 rounded-full font-medium hover:bg-blue-700 transition-colors duration-200">
                    <span class="text-white">Konsultasi</span>
                </a>
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button type="button" 
                        class="mobile-menu-btn text-gray-700 hover:text-blue-600 focus:outline-none focus:text-blue-600 transition-colors duration-200"
                        aria-label="Open menu">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div class="mobile-menu hidden md:hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 bg-white border-t border-gray-200">
                <a href="#beranda" class="block px-3 py-2 text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200 scroll-smooth">
                    Beranda
                </a>
                <a href="#tentang" class="block px-3 py-2 text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200 scroll-smooth">
                    Tentang
                </a>
                <a href="#layanan" class="block px-3 py-2 text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200 scroll-smooth">
                    Layanan
                </a>
                <a href="#jadwal" class="block px-3 py-2 text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200 scroll-smooth">
                    Jadwal Praktik
                </a>
                <a href="#testimoni" class="block px-3 py-2 text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200 scroll-smooth">
                    Testimoni
                </a>
                <a href="#blog" class="block px-3 py-2 text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200 scroll-smooth">
                    Artikel
                </a>
                <a href="#kontak" class="block mx-3 my-2 px-4 py-2 bg-blue-600 text-white text-center rounded-full font-medium hover:bg-blue-700 transition-colors duration-200">
                    <span class="text-white">Konsultasi</span>
                </a>
            </div>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                const offsetTop = targetElement.offsetTop - 80; // Account for fixed navbar
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
                
                // Close mobile menu if open
                if (mobileMenu) {
                    mobileMenu.classList.add('hidden');
                }
            }
        });
    });

    // Highlight active section in navbar
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('nav a[href^="#"]');
    
    if (sections.length > 0 && navLinks.length > 0) {
        window.addEventListener('scroll', function() {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop - 120;
                if (pageYOffset >= sectionTop) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('text-blue-600');
                link.classList.add('text-gray-700');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.remove('text-gray-700');
                    link.classList.add('text-blue-600');
                }
            });
        });
    }
});
</script>