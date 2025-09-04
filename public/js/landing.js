/**
 * Landing Page JavaScript
 * Modern, performant, and accessible
 */

class LandingPage {
    constructor() {
        this.isLoaded = false;
        this.scrollPosition = 0;
        this.ticking = false;
        
        this.init();
    }
    
    init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.onDOMReady());
        } else {
            this.onDOMReady();
        }
    }
    
    onDOMReady() {
        this.setupLoadingScreen();
        this.setupNavbar();
        this.setupSmoothScrolling();
        this.setupScrollAnimations();
        this.setupParallax();
        this.setupFormHandling();
        this.setupPerformanceOptimizations();
        this.setupAccessibility();
        this.isLoaded = true;
    }
    
    setupLoadingScreen() {
        const loadingScreen = document.querySelector('.loading-screen');
        if (loadingScreen) {
            // Minimum loading time for better UX
            setTimeout(() => {
                loadingScreen.style.opacity = '0';
                setTimeout(() => {
                    loadingScreen.style.display = 'none';
                }, 500);
            }, 800);
        }
    }
    
    setupNavbar() {
        const navbar = document.querySelector('.navbar');
        if (!navbar) return;
        
        let lastScrollY = window.scrollY;
        let ticking = false;
        
        const updateNavbar = () => {
            const currentScrollY = window.scrollY;
            
            // Add/remove scrolled class
            if (currentScrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
            
            // Hide/show navbar on scroll (optional)
            if (currentScrollY > lastScrollY && currentScrollY > 100) {
                navbar.style.transform = 'translateY(-100%)';
            } else {
                navbar.style.transform = 'translateY(0)';
            }
            
            lastScrollY = currentScrollY;
            ticking = false;
        };
        
        const onScroll = () => {
            if (!ticking) {
                requestAnimationFrame(updateNavbar);
                ticking = true;
            }
        };
        
        window.addEventListener('scroll', onScroll, { passive: true });
        
        // Active nav link highlighting
        this.highlightActiveNavLink();
    }
    
    highlightActiveNavLink() {
        const navLinks = document.querySelectorAll('.navbar-nav .nav-link[href^="#"]');
        const sections = document.querySelectorAll('section[id]');
        
        if (!navLinks.length || !sections.length) return;
        
        const updateActiveLink = () => {
            const scrollPosition = window.scrollY + 100;
            
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
        updateActiveLink(); // Initial call
    }
    
    setupSmoothScrolling() {
        // Enhanced smooth scrolling with proper offset calculation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                e.preventDefault();
                
                const targetId = anchor.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                
                if (!targetElement) return;
                
                const navbar = document.querySelector('.navbar');
                const navbarHeight = navbar ? navbar.offsetHeight : 0;
                const targetPosition = targetElement.offsetTop - navbarHeight - 20;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
                
                // Update URL without triggering scroll
                history.pushState(null, null, `#${targetId}`);
            });
        });
    }
    
    setupScrollAnimations() {
        // Intersection Observer for scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                    
                    // Trigger AOS animations if available
                    if (window.AOS) {
                        entry.target.classList.add('aos-animate');
                    }
                }
            });
        }, observerOptions);
        
        // Observe sections and cards
        document.querySelectorAll('section, .feature-card, .contact-card').forEach(el => {
            observer.observe(el);
        });
    }
    
    setupParallax() {
        // Subtle parallax effect for hero section
        const heroSection = document.querySelector('.hero-section');
        if (!heroSection) return;
        
        let ticking = false;
        
        const updateParallax = () => {
            const scrolled = window.pageYOffset;
            const heroContent = heroSection.querySelector('.hero-content');
            const heroImage = heroSection.querySelector('.hero-image');
            
            if (scrolled < heroSection.offsetHeight) {
                if (heroContent) {
                    heroContent.style.transform = `translateY(${scrolled * 0.1}px)`;
                }
                if (heroImage) {
                    heroImage.style.transform = `translateY(${scrolled * 0.05}px)`;
                }
            }
            
            ticking = false;
        };
        
        const onScroll = () => {
            if (!ticking) {
                requestAnimationFrame(updateParallax);
                ticking = true;
            }
        };
        
        window.addEventListener('scroll', onScroll, { passive: true });
    }
    
    setupFormHandling() {
        const contactForm = document.getElementById('contactForm');
        if (!contactForm) return;
        
        // Form validation
        const validateField = (field) => {
            const value = field.value.trim();
            const isValid = field.type === 'email' ? 
                this.validateEmail(value) : 
                value.length > 0;
            
            field.classList.toggle('is-invalid', !isValid);
            field.classList.toggle('is-valid', isValid);
            
            return isValid;
        };
        
        // Real-time validation
        contactForm.querySelectorAll('input, textarea').forEach(field => {
            field.addEventListener('blur', () => validateField(field));
            field.addEventListener('input', () => {
                if (field.classList.contains('is-invalid')) {
                    validateField(field);
                }
            });
        });
        
        // Form submission
        contactForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const formData = new FormData(contactForm);
            const isValid = this.validateForm(contactForm);
            
            if (!isValid) {
                this.showNotification('Mohon lengkapi semua field yang diperlukan.', 'error');
                return;
            }
            
            // Show loading state
            const submitBtn = contactForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...';
            submitBtn.disabled = true;
            
            // Simulate form submission (replace with actual implementation)
            this.submitForm(formData)
                .then(() => {
                    this.showNotification('Pesan berhasil dikirim!', 'success');
                    contactForm.reset();
                })
                .catch(() => {
                    this.showNotification('Terjadi kesalahan. Silakan coba lagi.', 'error');
                })
                .finally(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        });
    }
    
    validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    validateForm(form) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });
        
        return isValid;
    }
    
    async submitForm(formData) {
        // Placeholder for actual form submission
        return new Promise((resolve, reject) => {
            setTimeout(() => {
                // Simulate success/failure
                Math.random() > 0.1 ? resolve() : reject();
            }, 2000);
        });
    }
    
    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : type} position-fixed`;
        notification.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        `;
        notification.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }
    
    setupPerformanceOptimizations() {
        // Lazy loading for images
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.classList.remove('lazy');
                            imageObserver.unobserve(img);
                        }
                    }
                });
            });
            
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
        
        // Preload critical resources
        this.preloadCriticalResources();
    }
    
    preloadCriticalResources() {
        const criticalImages = document.querySelectorAll('img[data-critical]');
        criticalImages.forEach(img => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'image';
            link.href = img.src || img.dataset.src;
            document.head.appendChild(link);
        });
    }
    
    setupAccessibility() {
        // Skip link for keyboard navigation
        const skipLink = document.createElement('a');
        skipLink.href = '#main-content';
        skipLink.textContent = 'Skip to main content';
        skipLink.className = 'sr-only sr-only-focusable position-absolute';
        skipLink.style.cssText = `
            z-index: 10000;
            padding: 1rem;
            background: var(--color-primary);
            color: white;
            text-decoration: none;
            border-radius: 0 0 10px 0;
        `;
        document.body.prepend(skipLink);
        
        // Focus management
        this.setupFocusManagement();
        
        // Keyboard navigation
        this.setupKeyboardNavigation();
    }
    
    setupFocusManagement() {
        // Trap focus in mobile menu when open
        const navbarToggler = document.querySelector('.navbar-toggler');
        const navbarCollapse = document.querySelector('.navbar-collapse');
        
        if (navbarToggler && navbarCollapse) {
            navbarToggler.addEventListener('click', () => {
                const isExpanded = navbarToggler.getAttribute('aria-expanded') === 'true';
                if (isExpanded) {
                    const firstFocusable = navbarCollapse.querySelector('a, button');
                    if (firstFocusable) {
                        setTimeout(() => firstFocusable.focus(), 100);
                    }
                }
            });
        }
    }
    
    setupKeyboardNavigation() {
        // ESC key to close mobile menu
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const navbarCollapse = document.querySelector('.navbar-collapse.show');
                if (navbarCollapse) {
                    const toggler = document.querySelector('.navbar-toggler');
                    if (toggler) {
                        toggler.click();
                        toggler.focus();
                    }
                }
            }
        });
    }
}

// Initialize when script loads
new LandingPage();

// Back to top functionality
document.addEventListener('DOMContentLoaded', () => {
    const backToTopBtn = document.getElementById('backToTop');
    if (!backToTopBtn) return;
    
    let isVisible = false;
    
    const toggleVisibility = () => {
        const shouldShow = window.pageYOffset > 300;
        if (shouldShow !== isVisible) {
            isVisible = shouldShow;
            backToTopBtn.style.display = isVisible ? 'block' : 'none';
        }
    };
    
    window.addEventListener('scroll', toggleVisibility, { passive: true });
    
    backToTopBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
});

// Page visibility API for better UX
document.addEventListener('visibilitychange', () => {
    const originalTitle = document.title;
    const websiteName = originalTitle.split(' - ')[0];
    
    if (document.hidden) {
        document.title = `👋 Kembali ke ${websiteName}`;
    } else {
        document.title = originalTitle;
    }
});