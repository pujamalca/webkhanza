<footer class="bg-gray-900 text-white py-16 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 bg-pattern opacity-5"></div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="grid lg:grid-cols-4 md:grid-cols-2 gap-8 mb-12">
            <!-- Brand Section -->
            <div class="lg:col-span-1">
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        @if($websiteIdentity->logo)
                            <img src="{{ asset('storage/' . $websiteIdentity->logo) }}" 
                                 alt="{{ $websiteIdentity->name }}" 
                                 class="h-10 w-10 object-contain">
                        @endif
                        <span class="text-xl font-bold text-gradient">{{ $websiteIdentity->name }}</span>
                    </div>
                    
                    <p class="text-gray-300 leading-relaxed">{{ $websiteIdentity->description }}</p>
                    <p class="text-gray-400 italic">"{{ $websiteIdentity->tagline }}"</p>
                    
                    <!-- Social Media Links -->
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-blue-600 rounded-full flex items-center justify-center transition-colors duration-300 group">
                            <i class="fab fa-facebook text-gray-400 group-hover:text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-blue-400 rounded-full flex items-center justify-center transition-colors duration-300 group">
                            <i class="fab fa-twitter text-gray-400 group-hover:text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-pink-500 rounded-full flex items-center justify-center transition-colors duration-300 group">
                            <i class="fab fa-instagram text-gray-400 group-hover:text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-blue-700 rounded-full flex items-center justify-center transition-colors duration-300 group">
                            <i class="fab fa-linkedin text-gray-400 group-hover:text-white"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Menu Section -->
            <div>
                <h5 class="text-lg font-bold mb-4 text-white">Menu</h5>
                <ul class="space-y-3">
                    <li><a href="#beranda" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center group">
                        <i class="fas fa-chevron-right text-xs mr-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                        Beranda
                    </a></li>
                    <li><a href="#tentang" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center group">
                        <i class="fas fa-chevron-right text-xs mr-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                        Tentang
                    </a></li>
                    <li><a href="#fitur" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center group">
                        <i class="fas fa-chevron-right text-xs mr-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                        Fitur
                    </a></li>
                    <li><a href="#kontak" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center group">
                        <i class="fas fa-chevron-right text-xs mr-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                        Kontak
                    </a></li>
                    <li><a href="/admin" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center group">
                        <i class="fas fa-chevron-right text-xs mr-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                        Login Admin
                    </a></li>
                </ul>
            </div>
            
            <!-- Features Section -->
            <div>
                <h5 class="text-lg font-bold mb-4 text-white">Fitur Utama</h5>
                <ul class="space-y-3">
                    <li><a href="#fitur" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center group">
                        <i class="fas fa-users text-xs mr-2 text-blue-400"></i>
                        Manajemen Pegawai
                    </a></li>
                    <li><a href="#fitur" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center group">
                        <i class="fas fa-clock text-xs mr-2 text-blue-400"></i>
                        Sistem Absensi
                    </a></li>
                    <li><a href="#fitur" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center group">
                        <i class="fas fa-chart-line text-xs mr-2 text-blue-400"></i>
                        Laporan & Analitik
                    </a></li>
                    <li><a href="#fitur" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center group">
                        <i class="fas fa-calendar-alt text-xs mr-2 text-blue-400"></i>
                        Manajemen Cuti
                    </a></li>
                    <li><a href="#fitur" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center group">
                        <i class="fas fa-file-alt text-xs mr-2 text-blue-400"></i>
                        Berkas Digital
                    </a></li>
                </ul>
            </div>
            
            <!-- Contact Info Section -->
            <div>
                <h5 class="text-lg font-bold mb-4 text-white">Kontak Info</h5>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-blue-600/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-map-marker-alt text-blue-400 text-sm"></i>
                        </div>
                        <div class="text-gray-300 text-sm leading-relaxed">{{ $websiteIdentity->address }}</div>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-green-600/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-phone text-green-400 text-sm"></i>
                        </div>
                        <a href="tel:{{ $websiteIdentity->phone }}" 
                           class="text-gray-300 hover:text-white transition-colors duration-200">
                            {{ $websiteIdentity->phone }}
                        </a>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-purple-600/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-envelope text-purple-400 text-sm"></i>
                        </div>
                        <a href="mailto:{{ $websiteIdentity->email }}" 
                           class="text-gray-300 hover:text-white transition-colors duration-200">
                            {{ $websiteIdentity->email }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Divider -->
        <div class="border-t border-gray-800 my-8"></div>
        
        <!-- Bottom Section -->
        <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
            <div class="text-gray-400 text-sm">
                &copy; {{ date('Y') }} {{ $websiteIdentity->name }}. All rights reserved.
            </div>
            <div class="text-gray-400 text-sm">
                Powered by <span class="text-gradient font-medium">WebKhanza System</span>
            </div>
        </div>
    </div>
    
    <!-- Decorative Elements -->
    <div class="absolute top-10 right-10 w-32 h-32 bg-blue-600/5 rounded-full blur-xl"></div>
    <div class="absolute bottom-10 left-10 w-40 h-40 bg-purple-600/5 rounded-full blur-xl"></div>
</footer>

<!-- Back to Top Button -->
<button id="backToTop" 
        class="fixed bottom-6 right-6 z-50 w-12 h-12 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-full shadow-lg hover:shadow-xl hover:scale-110 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-300 opacity-0 invisible">
    <i class="fas fa-chevron-up"></i>
</button>

<script>
// Back to Top functionality
const backToTopButton = document.getElementById('backToTop');

window.addEventListener('scroll', function() {
    if (window.pageYOffset > 300) {
        backToTopButton.classList.remove('opacity-0', 'invisible');
        backToTopButton.classList.add('opacity-100', 'visible');
    } else {
        backToTopButton.classList.add('opacity-0', 'invisible');
        backToTopButton.classList.remove('opacity-100', 'visible');
    }
});

backToTopButton.addEventListener('click', function() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});
</script>