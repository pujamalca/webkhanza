<section id="kontak" class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <!-- Section Header -->
        <div class="text-center mb-16 animate-on-scroll">
            <div class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-full mb-4">
                <i class="fas fa-phone-alt text-sm mr-2"></i>
                <span class="text-sm font-medium">Hubungi Kami</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Siap Melayani
                <span class="text-green-600">Konsultasi Anda</span>
            </h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Jangan tunda kesehatan Anda. Hubungi kami sekarang untuk konsultasi atau buat janji temu dengan dokter. Kami siap membantu 24/7 untuk layanan darurat.
            </p>
        </div>

        <div class="grid lg:grid-cols-2 gap-12 mb-16">
            <!-- Contact Information -->
            <div class="space-y-8 animate-on-scroll">
                <!-- Contact Cards -->
                <div class="space-y-6">
                    <!-- Phone -->
                    <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-3xl p-6 border border-green-200 hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-green-600 rounded-2xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-phone-alt text-white text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 mb-1">Telepon Konsultasi</h3>
                                <p class="text-gray-600 mb-3">Hubungi langsung untuk konsultasi dan appointment</p>
                                <a href="tel:{{ $websiteIdentity->phone }}" 
                                   class="text-2xl font-bold text-green-600 hover:text-green-700 transition-colors">
                                    {{ $websiteIdentity->phone }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- WhatsApp -->
                    <div class="bg-gradient-to-r from-emerald-50 to-emerald-100 rounded-3xl p-6 border border-emerald-200 hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-emerald-600 rounded-2xl flex items-center justify-center flex-shrink-0">
                                <i class="fab fa-whatsapp text-white text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 mb-1">WhatsApp</h3>
                                <p class="text-gray-600 mb-3">Chat langsung untuk konsultasi cepat dan appointment</p>
                                <a href="https://wa.me/{{ str_replace(['+', '-', ' '], '', $websiteIdentity->phone) }}" 
                                   class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white font-semibold rounded-xl hover:bg-emerald-700 transition-colors">
                                    <i class="fab fa-whatsapp mr-2"></i>
                                    <span class="text-white">Chat Sekarang</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-3xl p-6 border border-blue-200 hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-envelope text-white text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 mb-1">Email</h3>
                                <p class="text-gray-600 mb-3">Kirim pertanyaan atau permintaan informasi</p>
                                <a href="mailto:{{ $websiteIdentity->email }}" 
                                   class="text-lg font-semibold text-blue-600 hover:text-blue-700 transition-colors">
                                    {{ $websiteIdentity->email }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-3xl p-6 border border-purple-200 hover:shadow-lg transition-all duration-300">
                        <div class="flex items-start space-x-4">
                            <div class="w-16 h-16 bg-purple-600 rounded-2xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-white text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 mb-1">Lokasi Praktik</h3>
                                <p class="text-gray-600 mb-3">Alamat praktik untuk konsultasi langsung</p>
                                <p class="text-gray-700 leading-relaxed">{{ $websiteIdentity->address }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Operating Hours -->
                <div class="bg-gray-50 rounded-3xl p-6 border border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-clock text-gray-600 mr-3"></i>
                        Jam Operasional
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-700">Senin - Rabu - Jumat</span>
                            <span class="font-semibold text-green-600">08:00 - 12:00</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">Selasa - Kamis</span>
                            <span class="font-semibold text-green-600">14:00 - 18:00</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">Sabtu</span>
                            <span class="font-semibold text-blue-600">08:00 - 14:00</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">Minggu</span>
                            <span class="font-semibold text-red-600">Tutup</span>
                        </div>
                        <hr class="border-gray-300">
                        <div class="flex justify-between">
                            <span class="text-gray-700 font-medium">Darurat 24/7</span>
                            <span class="font-semibold text-red-600">Tersedia</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="bg-gradient-to-br from-blue-50 to-green-50 rounded-3xl p-8 border border-gray-100 animate-on-scroll" style="animation-delay: 200ms;">
                <div class="mb-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Buat Appointment</h3>
                    <p class="text-gray-600">Isi form berikut untuk membuat janji konsultasi dengan dokter</p>
                </div>

                <form class="space-y-6" action="#" method="POST">
                    @csrf
                    
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nama Lengkap *
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Masukkan nama lengkap Anda">
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nomor Telepon *
                        </label>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Contoh: 0812-3456-7890">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            Email
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="nama@email.com">
                    </div>

                    <!-- Service Type -->
                    <div>
                        <label for="service" class="block text-sm font-semibold text-gray-700 mb-2">
                            Jenis Layanan *
                        </label>
                        <select id="service" 
                                name="service" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <option value="">Pilih jenis layanan</option>
                            <option value="konsultasi-umum">Konsultasi Umum</option>
                            <option value="medical-checkup">Medical Check Up</option>
                            <option value="telemedicine">Telemedicine</option>
                            <option value="perawatan-luka">Perawatan Luka</option>
                            <option value="vaksinasi">Vaksinasi</option>
                            <option value="konsultasi-gizi">Konsultasi Gizi</option>
                            <option value="darurat">Konsultasi Darurat</option>
                        </select>
                    </div>

                    <!-- Preferred Date -->
                    <div>
                        <label for="date" class="block text-sm font-semibold text-gray-700 mb-2">
                            Tanggal Konsultasi *
                        </label>
                        <input type="date" 
                               id="date" 
                               name="date" 
                               required
                               min="{{ date('Y-m-d') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    </div>

                    <!-- Time Preference -->
                    <div>
                        <label for="time" class="block text-sm font-semibold text-gray-700 mb-2">
                            Waktu Preferensi *
                        </label>
                        <select id="time" 
                                name="time" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <option value="">Pilih waktu</option>
                            <option value="pagi">Pagi (08:00 - 12:00)</option>
                            <option value="siang">Siang (12:00 - 15:00)</option>
                            <option value="sore">Sore (15:00 - 18:00)</option>
                        </select>
                    </div>

                    <!-- Message -->
                    <div>
                        <label for="message" class="block text-sm font-semibold text-gray-700 mb-2">
                            Pesan / Keluhan
                        </label>
                        <textarea id="message" 
                                  name="message" 
                                  rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none"
                                  placeholder="Jelaskan keluhan atau pesan khusus untuk dokter (opsional)"></textarea>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-blue-600 to-green-600 text-white font-bold py-4 px-8 rounded-xl hover:from-blue-700 hover:to-green-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <i class="fas fa-paper-plane mr-2"></i>
                        <span class="text-white">Kirim Permintaan Appointment</span>
                    </button>

                    <!-- Note -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-info-circle text-yellow-600 mt-1"></i>
                            <div>
                                <p class="text-sm text-yellow-800">
                                    <strong>Catatan:</strong> Setelah mengirim form, kami akan menghubungi Anda dalam 1-2 jam untuk konfirmasi appointment. 
                                    Untuk konsultasi darurat, silakan hubungi langsung melalui telepon.
                                </p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Emergency CTA -->
        <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-3xl p-8 md:p-12 text-white text-center animate-on-scroll">
            <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-exclamation-triangle text-white text-3xl"></i>
            </div>
            <h3 class="text-2xl md:text-3xl font-bold mb-4">Darurat Medis?</h3>
            <p class="text-lg text-white/90 mb-6 max-w-2xl mx-auto">
                Jika Anda mengalami kondisi darurat medis, jangan menunggu appointment. 
                Hubungi kami segera melalui nomor darurat 24/7.
            </p>
            <a href="tel:{{ $websiteIdentity->phone }}" 
               class="inline-flex items-center px-8 py-4 bg-white text-red-600 font-bold text-xl rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-phone-alt mr-3 text-2xl"></i>
                <span class="text-red-600 font-bold">{{ $websiteIdentity->phone }}</span>
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
    
    // Form validation
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            
            // Simple validation
            if (!data.name || !data.phone || !data.service || !data.date || !data.time) {
                alert('Mohon lengkapi semua field yang wajib diisi.');
                return;
            }
            
            // In a real application, you would send this data to the server
            alert('Terima kasih! Permintaan appointment Anda telah dikirim. Kami akan menghubungi Anda segera untuk konfirmasi.');
        });
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
</style>