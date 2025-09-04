<section id="tentang" class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- Image Column -->
            <div class="relative animate-on-scroll">
                <!-- Main Image -->
                <div class="relative">
                    <div class="bg-gradient-to-br from-blue-50 to-green-50 rounded-3xl p-8 shadow-lg border border-gray-100">
                        <div class="flex items-center justify-center h-80 bg-white rounded-2xl shadow-lg">
                            <div class="text-center">
                                <div class="w-24 h-24 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-user-md text-white text-3xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 mb-2">Dokter Berpengalaman</h3>
                                <p class="text-gray-600 text-sm">Melayani dengan dedikasi tinggi</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Floating Stats Cards -->
                    <div class="absolute -top-6 -right-6 bg-white rounded-2xl p-4 shadow-xl border border-gray-100">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">10+</div>
                            <div class="text-xs text-gray-600">Tahun Pengalaman</div>
                        </div>
                    </div>
                    
                    <div class="absolute -bottom-6 -left-6 bg-white rounded-2xl p-4 shadow-xl border border-gray-100">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">1000+</div>
                            <div class="text-xs text-gray-600">Pasien Dilayani</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Content Column -->
            <div class="space-y-6 animate-on-scroll" style="animation-delay: 200ms;">
                <!-- Badge -->
                <div class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-full">
                    <i class="fas fa-info-circle text-sm mr-2"></i>
                    <span class="text-sm font-medium">Tentang Dokter</span>
                </div>
                
                <!-- Heading -->
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 leading-tight">
                    Dokter Profesional dengan 
                    <span class="text-blue-600">Pengalaman Luas</span>
                </h2>
                
                <!-- Description -->
                <p class="text-lg text-gray-600 leading-relaxed">
                    {{ $websiteIdentity->description }} Kami berkomitmen memberikan pelayanan kesehatan terbaik dengan pendekatan yang personal dan profesional.
                </p>
                
                <!-- Features List -->
                <div class="space-y-4">
                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mt-1 flex-shrink-0">
                            <i class="fas fa-check text-green-600 text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Pelayanan Komprehensif</h3>
                            <p class="text-gray-600">Diagnosis dan pengobatan yang menyeluruh untuk berbagai kondisi kesehatan</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mt-1 flex-shrink-0">
                            <i class="fas fa-heart text-blue-600 text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Pendekatan Personal</h3>
                            <p class="text-gray-600">Perhatian khusus untuk setiap pasien dengan waktu konsultasi yang cukup</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mt-1 flex-shrink-0">
                            <i class="fas fa-shield-alt text-purple-600 text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Privasi Terjamin</h3>
                            <p class="text-gray-600">Kerahasiaan pasien adalah prioritas utama dalam setiap konsultasi</p>
                        </div>
                    </div>
                </div>
                
                <!-- Credentials -->
                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Kredensial & Sertifikasi</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-white text-xs"></i>
                            </div>
                            <span class="text-gray-700 text-sm">Dokter Umum Bersertifikat</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-certificate text-white text-xs"></i>
                            </div>
                            <span class="text-gray-700 text-sm">Lisensi Praktik Aktif</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-award text-white text-xs"></i>
                            </div>
                            <span class="text-gray-700 text-sm">Pelatihan Berkelanjutan</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-handshake text-white text-xs"></i>
                            </div>
                            <span class="text-gray-700 text-sm">Anggota IDI</span>
                        </div>
                    </div>
                </div>
                
                <!-- CTA Button -->
                <div class="pt-4">
                    <a href="#kontak" 
                       class="inline-flex items-center px-8 py-4 bg-blue-600 text-white font-semibold rounded-2xl shadow-lg hover:bg-blue-700 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <div class="w-6 h-6 mr-3 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-phone text-white text-sm"></i>
                        </div>
                        <span class="text-white">Konsultasi Sekarang</span>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Stats Section -->
        <div class="mt-16 pt-16 border-t border-gray-200">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="animate-on-scroll" style="animation-delay: 100ms;">
                    <div class="text-3xl md:text-4xl font-bold text-blue-600 mb-2">10+</div>
                    <div class="text-gray-600">Tahun Pengalaman</div>
                </div>
                <div class="animate-on-scroll" style="animation-delay: 200ms;">
                    <div class="text-3xl md:text-4xl font-bold text-green-600 mb-2">1000+</div>
                    <div class="text-gray-600">Pasien Dilayani</div>
                </div>
                <div class="animate-on-scroll" style="animation-delay: 300ms;">
                    <div class="text-3xl md:text-4xl font-bold text-purple-600 mb-2">98%</div>
                    <div class="text-gray-600">Tingkat Kepuasan</div>
                </div>
                <div class="animate-on-scroll" style="animation-delay: 400ms;">
                    <div class="text-3xl md:text-4xl font-bold text-red-600 mb-2">24/7</div>
                    <div class="text-gray-600">Konsultasi Darurat</div>
                </div>
            </div>
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
</style>