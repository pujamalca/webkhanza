<section id="tentang" class="py-20 lg:py-28 bg-gradient-to-br from-gray-50 to-white">
    <div class="container mx-auto px-4">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            
            <!-- Content Column -->
            <div class="space-y-8 animate-on-scroll">
                <!-- Section Header -->
                <div class="space-y-4">
                    <div class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                        <i class="fas fa-info-circle mr-2"></i>
                        Tentang Kami
                    </div>
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 leading-tight">
                        Tentang <span class="text-gradient">{{ $websiteIdentity->name }}</span>
                    </h2>
                    <p class="text-xl text-gray-600 leading-relaxed">
                        Solusi terdepan untuk manajemen pegawai dan sistem absensi yang modern dan efisien.
                    </p>
                </div>
                
                <!-- Description -->
                <div class="prose prose-lg text-gray-600">
                    <p>
                        {{ $websiteIdentity->description }} Kami menyediakan platform yang komprehensif untuk 
                        mengelola data pegawai, absensi, dan berbagai aspek administratif lainnya dengan mudah dan efisien.
                    </p>
                </div>
                
                <!-- Features Grid -->
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="flex items-start space-x-4 group">
                        <div class="flex-shrink-0 w-12 h-12 bg-green-100 text-green-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-check text-lg"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-1">Mudah Digunakan</h4>
                            <p class="text-gray-600 text-sm">Interface yang intuitif dan user-friendly</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4 group">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-shield-alt text-lg"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-1">Keamanan Tinggi</h4>
                            <p class="text-gray-600 text-sm">Data terlindungi dengan enkripsi modern</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4 group">
                        <div class="flex-shrink-0 w-12 h-12 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-clock text-lg"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-1">Real-time</h4>
                            <p class="text-gray-600 text-sm">Update data secara real-time</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4 group">
                        <div class="flex-shrink-0 w-12 h-12 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-mobile-alt text-lg"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-1">Responsive</h4>
                            <p class="text-gray-600 text-sm">Dapat diakses dari berbagai device</p>
                        </div>
                    </div>
                </div>
                
                <!-- CTA Button -->
                <div class="pt-6">
                    <a href="#kontak" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-bold text-lg rounded-2xl shadow-xl hover:shadow-2xl hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 group relative overflow-hidden">
                        <!-- Background Animation -->
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-blue-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left rounded-2xl"></div>
                        
                        <!-- Content -->
                        <div class="relative z-10 flex items-center text-white">
                            <div class="w-6 h-6 mr-3 bg-white/20 rounded-full flex items-center justify-center transition-all duration-300 group-hover:rotate-90">
                                <i class="fas fa-rocket text-white text-sm"></i>
                            </div>
                            <span class="text-white font-bold">Get Started Today</span>
                        </div>
                        
                        <!-- Shine Effect -->
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent skew-x-12 transform -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                    </a>
                    
                    <p class="text-sm text-gray-500 mt-3">
                        <i class="fas fa-shield-alt mr-1"></i>
                        Trusted by 1000+ companies worldwide
                    </p>
                </div>
            </div>
            
            <!-- Stats Column -->
            <div class="relative animate-on-scroll" style="animation-delay: 200ms;">
                <div class="relative">
                    <!-- Background Decoration -->
                    <div class="absolute -inset-4 bg-gradient-to-r from-blue-500 to-blue-600 rounded-3xl opacity-10 blur-xl"></div>
                    
                    <!-- Main Stats Card -->
                    <div class="relative bg-white rounded-3xl p-8 shadow-2xl border border-gray-100">
                        <div class="text-center mb-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Statistik Performa</h3>
                            <p class="text-gray-600">Data real-time sistem kami</p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-8">
                            <!-- Stat 1 -->
                            <div class="text-center group">
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                    <i class="fas fa-headset text-white text-xl"></i>
                                </div>
                                <div class="text-3xl font-bold text-gradient mb-1">24/7</div>
                                <div class="text-gray-600 text-sm font-medium">Dukungan</div>
                            </div>
                            
                            <!-- Stat 2 -->
                            <div class="text-center group">
                                <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                    <i class="fas fa-server text-white text-xl"></i>
                                </div>
                                <div class="text-3xl font-bold text-gradient mb-1">99%</div>
                                <div class="text-gray-600 text-sm font-medium">Uptime</div>
                            </div>
                            
                            <!-- Stat 3 -->
                            <div class="text-center group">
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                    <i class="fas fa-lock text-white text-xl"></i>
                                </div>
                                <div class="text-3xl font-bold text-gradient mb-1">100%</div>
                                <div class="text-gray-600 text-sm font-medium">Aman</div>
                            </div>
                            
                            <!-- Stat 4 -->
                            <div class="text-center group">
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                    <i class="fas fa-rocket text-white text-xl"></i>
                                </div>
                                <div class="text-3xl font-bold text-gradient mb-1">Fast</div>
                                <div class="text-gray-600 text-sm font-medium">Modern</div>
                            </div>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="mt-8 p-4 bg-gray-50 rounded-2xl">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">System Performance</span>
                                <span class="text-sm font-bold text-green-600">98.5%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-green-400 to-blue-500 h-2 rounded-full" style="width: 98.5%"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Floating Cards -->
                    <div class="absolute -top-6 -left-6 bg-white rounded-2xl p-4 shadow-xl border border-gray-100 hidden lg:block">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                            <span class="text-sm font-medium text-gray-700">Online</span>
                        </div>
                    </div>
                    
                    <div class="absolute -bottom-6 -right-6 bg-white rounded-2xl p-4 shadow-xl border border-gray-100 hidden lg:block">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-users text-white text-xs"></i>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-700">Active Users</div>
                                <div class="text-xs text-gray-500">1,234+ online</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="absolute top-1/2 -right-8 bg-white rounded-2xl p-3 shadow-xl border border-gray-100 hidden xl:block">
                        <div class="text-center">
                            <div class="w-6 h-6 bg-yellow-400 rounded-full flex items-center justify-center mx-auto mb-1">
                                <i class="fas fa-trophy text-white text-xs"></i>
                            </div>
                            <div class="text-xs font-bold text-gray-700">#1</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bottom Section -->
        <div class="mt-20 pt-16 border-t border-gray-200">
            <div class="text-center space-y-6 animate-on-scroll">
                <h3 class="text-2xl font-bold text-gray-900">Mengapa Memilih {{ $websiteIdentity->name }}?</h3>
                <div class="grid md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                    <div class="text-center space-y-3">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center mx-auto">
                            <i class="fas fa-medal text-white text-2xl"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900">Terpercaya</h4>
                        <p class="text-gray-600 text-sm">Dipercaya oleh ribuan perusahaan di Indonesia</p>
                    </div>
                    
                    <div class="text-center space-y-3">
                        <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-green-600 rounded-2xl flex items-center justify-center mx-auto">
                            <i class="fas fa-cogs text-white text-2xl"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900">Customizable</h4>
                        <p class="text-gray-600 text-sm">Dapat disesuaikan dengan kebutuhan bisnis Anda</p>
                    </div>
                    
                    <div class="text-center space-y-3">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-purple-600 rounded-2xl flex items-center justify-center mx-auto">
                            <i class="fas fa-chart-line text-white text-2xl"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900">Scalable</h4>
                        <p class="text-gray-600 text-sm">Berkembang seiring pertumbuhan perusahaan Anda</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>