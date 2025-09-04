<footer class="bg-gray-900 text-white py-16">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            <!-- Company Info -->
            <div class="lg:col-span-2">
                <div class="flex items-center space-x-3 mb-6">
                    @if($websiteIdentity->logo)
                        <img src="{{ asset('storage/' . $websiteIdentity->logo) }}" 
                             alt="{{ $websiteIdentity->name }}"
                             class="h-12 w-auto object-contain">
                    @else
                        <div class="h-12 w-12 bg-blue-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-user-md text-white text-xl"></i>
                        </div>
                    @endif
                    <div>
                        <h3 class="text-xl font-bold">{{ $websiteIdentity->name }}</h3>
                        <p class="text-gray-400 text-sm">Dokter Praktek Pribadi</p>
                    </div>
                </div>
                
                <p class="text-gray-300 leading-relaxed mb-6 max-w-md">
                    {{ $websiteIdentity->description }} Kami berkomitmen memberikan pelayanan kesehatan terbaik dengan pendekatan personal dan profesional untuk setiap pasien.
                </p>
                
                <!-- Contact Info -->
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-phone text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Telepon</p>
                            <a href="tel:{{ $websiteIdentity->phone }}" class="text-white hover:text-blue-400 transition-colors">
                                {{ $websiteIdentity->phone }}
                            </a>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-envelope text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Email</p>
                            <a href="mailto:{{ $websiteIdentity->email }}" class="text-white hover:text-green-400 transition-colors">
                                {{ $websiteIdentity->email }}
                            </a>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center mt-1">
                            <i class="fas fa-map-marker-alt text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Alamat</p>
                            <p class="text-white text-sm leading-relaxed">{{ $websiteIdentity->address }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div>
                <h4 class="text-lg font-semibold mb-6">Menu Utama</h4>
                <ul class="space-y-3">
                    <li>
                        <a href="#beranda" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center">
                            <i class="fas fa-home text-sm mr-2 text-gray-400"></i>
                            Beranda
                        </a>
                    </li>
                    <li>
                        <a href="#tentang" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center">
                            <i class="fas fa-user-md text-sm mr-2 text-gray-400"></i>
                            Tentang Dokter
                        </a>
                    </li>
                    <li>
                        <a href="#layanan" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center">
                            <i class="fas fa-medical-kit text-sm mr-2 text-gray-400"></i>
                            Layanan
                        </a>
                    </li>
                    <li>
                        <a href="#jadwal" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center">
                            <i class="fas fa-calendar-alt text-sm mr-2 text-gray-400"></i>
                            Jadwal Praktik
                        </a>
                    </li>
                    <li>
                        <a href="#testimoni" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center">
                            <i class="fas fa-star text-sm mr-2 text-gray-400"></i>
                            Testimoni
                        </a>
                    </li>
                    <li>
                        <a href="#kontak" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center">
                            <i class="fas fa-phone-alt text-sm mr-2 text-gray-400"></i>
                            Kontak
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Services -->
            <div>
                <h4 class="text-lg font-semibold mb-6">Layanan Kami</h4>
                <ul class="space-y-3">
                    <li>
                        <a href="#layanan" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center">
                            <i class="fas fa-stethoscope text-sm mr-2 text-gray-400"></i>
                            Konsultasi Umum
                        </a>
                    </li>
                    <li>
                        <a href="#layanan" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center">
                            <i class="fas fa-heartbeat text-sm mr-2 text-gray-400"></i>
                            Medical Check Up
                        </a>
                    </li>
                    <li>
                        <a href="#layanan" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center">
                            <i class="fas fa-video text-sm mr-2 text-gray-400"></i>
                            Telemedicine
                        </a>
                    </li>
                    <li>
                        <a href="#layanan" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center">
                            <i class="fas fa-band-aid text-sm mr-2 text-gray-400"></i>
                            Perawatan Luka
                        </a>
                    </li>
                    <li>
                        <a href="#layanan" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center">
                            <i class="fas fa-syringe text-sm mr-2 text-gray-400"></i>
                            Vaksinasi
                        </a>
                    </li>
                    <li>
                        <a href="#layanan" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center">
                            <i class="fas fa-apple-alt text-sm mr-2 text-gray-400"></i>
                            Konsultasi Gizi
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Working Hours -->
        <div class="bg-gray-800 rounded-2xl p-6 mb-12">
            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <h4 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-clock text-blue-400 mr-3"></i>
                        Jam Praktik
                    </h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-300">Senin - Rabu - Jumat</span>
                            <span class="text-green-400 font-semibold">08:00 - 12:00</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-300">Selasa - Kamis</span>
                            <span class="text-green-400 font-semibold">14:00 - 18:00</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-300">Sabtu</span>
                            <span class="text-blue-400 font-semibold">08:00 - 14:00</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-300">Minggu</span>
                            <span class="text-red-400 font-semibold">Tutup</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-400 mr-3"></i>
                        Layanan Darurat
                    </h4>
                    <div class="space-y-3">
                        <p class="text-gray-300 text-sm">
                            Untuk kondisi darurat medis, hubungi kami 24/7 melalui:
                        </p>
                        <div class="flex flex-col space-y-2">
                            <a href="tel:{{ $websiteIdentity->phone }}" 
                               class="inline-flex items-center text-red-400 hover:text-red-300 transition-colors">
                                <i class="fas fa-phone-alt mr-2"></i>
                                {{ $websiteIdentity->phone }}
                            </a>
                            <a href="https://wa.me/{{ str_replace(['+', '-', ' '], '', $websiteIdentity->phone) }}" 
                               class="inline-flex items-center text-green-400 hover:text-green-300 transition-colors">
                                <i class="fab fa-whatsapp mr-2"></i>
                                WhatsApp Darurat
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Social Media & Newsletter -->
        <div class="bg-gradient-to-r from-blue-900 to-green-900 rounded-2xl p-6 mb-12">
            <div class="grid md:grid-cols-2 gap-8 items-center">
                <div>
                    <h4 class="text-xl font-semibold mb-4">Ikuti Kami</h4>
                    <p class="text-gray-300 mb-4">
                        Dapatkan tips kesehatan dan informasi terbaru dari praktik kami.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-700 transition-colors">
                            <i class="fab fa-facebook-f text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-blue-400 rounded-lg flex items-center justify-center hover:bg-blue-500 transition-colors">
                            <i class="fab fa-twitter text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-pink-600 rounded-lg flex items-center justify-center hover:bg-pink-700 transition-colors">
                            <i class="fab fa-instagram text-white"></i>
                        </a>
                        <a href="https://wa.me/{{ str_replace(['+', '-', ' '], '', $websiteIdentity->phone) }}" class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center hover:bg-green-700 transition-colors">
                            <i class="fab fa-whatsapp text-white"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-xl font-semibold mb-4">Buat Appointment</h4>
                    <p class="text-gray-300 mb-4">
                        Siap untuk konsultasi? Hubungi kami sekarang!
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="#kontak" 
                           class="inline-flex items-center px-4 py-2 bg-white text-gray-900 font-semibold rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-calendar-plus mr-2"></i>
                            <span class="text-gray-900">Buat Appointment</span>
                        </a>
                        <a href="tel:{{ $websiteIdentity->phone }}" 
                           class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-phone mr-2"></i>
                            <span class="text-white">Telepon Sekarang</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Copyright -->
        <div class="border-t border-gray-700 pt-8 text-center">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <p class="text-gray-400 text-sm">
                    &copy; {{ date('Y') }} {{ $websiteIdentity->name }}. All rights reserved.
                </p>
                <div class="flex items-center space-x-6 text-sm">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">Privacy Policy</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">Terms of Service</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">Medical Disclaimer</a>
                </div>
            </div>
            
            <!-- Back to Top Button -->
            <div class="mt-8">
                <a href="#beranda" 
                   class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition-all duration-300 transform hover:scale-105 back-to-top">
                    <i class="fas fa-chevron-up mr-2"></i>
                    <span class="text-white">Kembali ke Atas</span>
                </a>
            </div>
        </div>
    </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scrolling for footer links
    document.querySelectorAll('footer a[href^="#"]').forEach(anchor => {
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
            }
        });
    });
    
    // Back to top button visibility
    const backToTopBtn = document.querySelector('.back-to-top');
    if (backToTopBtn) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopBtn.style.opacity = '1';
            } else {
                backToTopBtn.style.opacity = '0.7';
            }
        });
    }
});
</script>

<style>
.back-to-top {
    opacity: 0.7;
    transition: opacity 0.3s ease;
}

.back-to-top:hover {
    opacity: 1;
}
</style>