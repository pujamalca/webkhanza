<section id="kontak" class="py-20 lg:py-28 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900 text-white relative overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute inset-0">
        <div class="absolute top-1/4 left-10 w-64 h-64 bg-blue-400/10 rounded-full blur-3xl animate-pulse" data-parallax="0.3"></div>
        <div class="absolute bottom-1/4 right-10 w-80 h-80 bg-blue-300/5 rounded-full blur-3xl" data-parallax="0.2"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-blue-500/5 rounded-full blur-3xl"></div>
    </div>
    
    <div class="container mx-auto px-4 relative z-10">
        <!-- Section Header -->
        <div class="text-center mb-16 animate-on-scroll">
            <div class="inline-flex items-center px-6 py-3 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full text-sm font-medium mb-6 hover:bg-white/20 transition-all duration-300">
                <div class="w-2 h-2 bg-green-400 rounded-full mr-3 animate-pulse"></div>
                <span class="text-blue-100">We're Online & Ready to Help</span>
            </div>
            <h2 class="text-4xl lg:text-5xl font-bold mb-6 leading-tight">
                Ready to <span class="text-gradient bg-gradient-to-r from-blue-400 to-blue-200 bg-clip-text text-transparent">Transform</span><br>
                Your Business?
            </h2>
            <p class="text-xl text-blue-100 max-w-3xl mx-auto leading-relaxed">
                Join thousands of companies that trust us with their employee management needs. 
                Let's discuss how we can help your organization grow.
            </p>
        </div>
        
        <!-- Quick Contact Options -->
        <div class="grid md:grid-cols-3 gap-6 mb-16">
            <!-- Phone Card -->
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/20 to-blue-600/10 rounded-2xl blur-sm group-hover:blur-0 transition-all duration-300"></div>
                <div class="relative bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6 hover:bg-white/10 hover:border-white/20 transition-all duration-300 text-center group animate-on-scroll" style="animation-delay: 100ms;">
                    <div class="w-16 h-16 bg-green-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                        <i class="fas fa-phone text-green-400 text-2xl group-hover:animate-pulse"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Call Us Now</h3>
                    <p class="text-blue-200 font-semibold mb-1">{{ $websiteIdentity->phone }}</p>
                    <p class="text-blue-300 text-sm mb-4">Available 24/7</p>
                    <a href="tel:{{ $websiteIdentity->phone }}" 
                       class="inline-flex items-center justify-center w-full px-4 py-2 bg-green-500 hover:bg-green-400 text-white rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg">
                        <i class="fas fa-phone mr-2"></i>
                        Call Now
                    </a>
                </div>
            </div>
            
            <!-- Email Card -->
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/20 to-blue-600/10 rounded-2xl blur-sm group-hover:blur-0 transition-all duration-300"></div>
                <div class="relative bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6 hover:bg-white/10 hover:border-white/20 transition-all duration-300 text-center group animate-on-scroll" style="animation-delay: 200ms;">
                    <div class="w-16 h-16 bg-blue-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                        <i class="fas fa-envelope text-blue-400 text-2xl group-hover:animate-bounce"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Email Us</h3>
                    <p class="text-blue-200 font-semibold mb-1">{{ $websiteIdentity->email }}</p>
                    <p class="text-blue-300 text-sm mb-4">Quick Response</p>
                    <a href="mailto:{{ $websiteIdentity->email }}" 
                       class="inline-flex items-center justify-center w-full px-4 py-2 bg-blue-500 hover:bg-blue-400 text-white rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg">
                        <i class="fas fa-envelope mr-2"></i>
                        Send Email
                    </a>
                </div>
            </div>
            
            <!-- Location Card -->
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/20 to-blue-600/10 rounded-2xl blur-sm group-hover:blur-0 transition-all duration-300"></div>
                <div class="relative bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6 hover:bg-white/10 hover:border-white/20 transition-all duration-300 text-center group animate-on-scroll" style="animation-delay: 300ms;">
                    <div class="w-16 h-16 bg-purple-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                        <i class="fas fa-map-marker-alt text-purple-400 text-2xl group-hover:animate-pulse"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Visit Office</h3>
                    <p class="text-blue-200 font-semibold mb-1 text-sm">{{ $websiteIdentity->address }}</p>
                    <p class="text-blue-300 text-sm mb-4">Open Mon-Fri</p>
                    <a href="https://maps.google.com/?q={{ urlencode($websiteIdentity->address) }}" 
                       target="_blank" class="inline-flex items-center justify-center w-full px-4 py-2 bg-purple-500 hover:bg-purple-400 text-white rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg">
                        <i class="fas fa-directions mr-2"></i>
                        Get Directions
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Contact Form -->
        <div class="max-w-5xl mx-auto animate-on-scroll" style="animation-delay: 400ms;">
            <div class="relative">
                <!-- Background glow effect -->
                <div class="absolute inset-0 bg-gradient-to-r from-blue-500/20 via-blue-400/10 to-blue-500/20 rounded-3xl blur-xl"></div>
                
                <div class="relative bg-white/95 backdrop-blur-sm rounded-3xl p-8 lg:p-12 shadow-2xl border border-white/20">
                    <div class="text-center mb-10">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-500/10 rounded-2xl mb-4">
                            <i class="fas fa-paper-plane text-blue-500 text-2xl"></i>
                        </div>
                        <h4 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-3">Let's Start a Conversation</h4>
                        <p class="text-gray-600 text-lg">Tell us about your project and we'll get back to you within 24 hours</p>
                    </div>
                    
                    <form id="contactForm" class="space-y-8">
                        <div class="grid md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label for="name" class="block text-sm font-bold text-gray-800 mb-2">
                                    <i class="fas fa-user mr-2 text-blue-500"></i>
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="name" required
                                       class="w-full px-6 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all duration-300 text-gray-900 placeholder-gray-500"
                                       placeholder="Enter your full name">
                            </div>
                            <div class="space-y-2">
                                <label for="email" class="block text-sm font-bold text-gray-800 mb-2">
                                    <i class="fas fa-envelope mr-2 text-blue-500"></i>
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="email" required
                                       class="w-full px-6 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all duration-300 text-gray-900 placeholder-gray-500"
                                       placeholder="your.email@example.com">
                            </div>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label for="phone" class="block text-sm font-bold text-gray-800 mb-2">
                                    <i class="fas fa-phone mr-2 text-blue-500"></i>
                                    Phone Number
                                </label>
                                <input type="tel" id="phone"
                                       class="w-full px-6 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all duration-300 text-gray-900 placeholder-gray-500"
                                       placeholder="+1 (555) 123-4567">
                            </div>
                            <div class="space-y-2">
                                <label for="company" class="block text-sm font-bold text-gray-800 mb-2">
                                    <i class="fas fa-building mr-2 text-blue-500"></i>
                                    Company/Organization
                                </label>
                                <input type="text" id="company"
                                       class="w-full px-6 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all duration-300 text-gray-900 placeholder-gray-500"
                                       placeholder="Your company name">
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <label for="subject" class="block text-sm font-bold text-gray-800 mb-2">
                                <i class="fas fa-tag mr-2 text-blue-500"></i>
                                Subject <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="subject" required
                                   class="w-full px-6 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all duration-300 text-gray-900 placeholder-gray-500"
                                   placeholder="What can we help you with?">
                        </div>
                        
                        <div class="space-y-2">
                            <label for="message" class="block text-sm font-bold text-gray-800 mb-2">
                                <i class="fas fa-comment-alt mr-2 text-blue-500"></i>
                                Message <span class="text-red-500">*</span>
                            </label>
                            <textarea id="message" rows="6" required
                                      class="w-full px-6 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all duration-300 resize-none text-gray-900 placeholder-gray-500"
                                      placeholder="Tell us more about your project or question..."></textarea>
                        </div>
                        
                        <div class="text-center pt-6">
                            <button type="submit" 
                                    class="inline-flex items-center justify-center px-12 py-5 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-bold text-lg rounded-2xl hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-all duration-300 transform hover:scale-105 hover:shadow-xl group relative overflow-hidden">
                                <span class="relative z-10 flex items-center">
                                    <i class="fas fa-paper-plane mr-3 transition-all duration-300 group-hover:translate-x-1 group-hover:rotate-12"></i>
                                    Send Message
                                </span>
                                <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-blue-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                            </button>
                            <p class="text-sm text-gray-500 mt-4">
                                <i class="fas fa-shield-alt mr-1"></i>
                                Your information is secure and will never be shared with third parties.
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Background Elements -->
        <div class="absolute top-1/4 left-10 w-32 h-32 bg-blue-600/10 rounded-full blur-xl"></div>
        <div class="absolute bottom-1/4 right-10 w-40 h-40 bg-purple-600/10 rounded-full blur-xl"></div>
    </div>
</section>

<style>
/* Fix button text colors in contact section */
#kontak .bg-green-500,
#kontak .bg-blue-500,
#kontak .bg-purple-500 {
    color: white !important;
}

#kontak .bg-green-500 *,
#kontak .bg-blue-500 *,
#kontak .bg-purple-500 * {
    color: white !important;
}

#kontak .hover\:bg-green-400:hover,
#kontak .hover\:bg-blue-400:hover,
#kontak .hover\:bg-purple-400:hover {
    color: white !important;
}

#kontak .hover\:bg-green-400:hover *,
#kontak .hover\:bg-blue-400:hover *,
#kontak .hover\:bg-purple-400:hover * {
    color: white !important;
}

/* Fix contact form button */
#kontak .bg-gradient-to-r.from-blue-500 {
    color: white !important;
}

#kontak .bg-gradient-to-r.from-blue-500 * {
    color: white !important;
}

#kontak .hover\:from-blue-600:hover {
    color: white !important;
}

#kontak .hover\:from-blue-600:hover * {
    color: white !important;
}

/* Specific button targeting */
#kontak a[href^="tel:"],
#kontak a[href^="mailto:"],
#kontak a[href*="maps.google.com"],
#kontak button[type="submit"] {
    color: white !important;
}

#kontak a[href^="tel:"] *,
#kontak a[href^="mailto:"] *,
#kontak a[href*="maps.google.com"] *,
#kontak button[type="submit"] * {
    color: white !important;
}

/* Override text-white class in contact section */
#kontak .text-white {
    color: white !important;
}
</style>

<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Get form data
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const phone = document.getElementById('phone').value;
    const company = document.getElementById('company').value;
    const subject = document.getElementById('subject').value;
    const message = document.getElementById('message').value;
    
    // Create mailto link
    const mailtoLink = `mailto:{{ $websiteIdentity->email }}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(`
Nama: ${name}
Email: ${email}
Telepon: ${phone}
Perusahaan: ${company}

Pesan:
${message}
    `)}`;
    
    // Open mail client
    window.location.href = mailtoLink;
    
    // Show success message
    alert('Terima kasih! Aplikasi email Anda akan terbuka dengan pesan yang sudah diisi.');
    
    // Reset form
    this.reset();
});
</script>