/**
 * Interactive Elements & Enhanced UX
 * Menambahkan interaktivitas yang menarik untuk landing page
 */

class InteractiveElements {
    constructor() {
        this.init();
    }
    
    init() {
        this.setupButtonAnimations();
        this.setupParallaxElements();
        this.setupHoverEffects();
        this.setupScrollAnimations();
        this.setupTypingEffect();
        this.setupParticles();
        this.setupSmoothScrolling();
        this.setupNavbarEnhancements();
        
        console.log('🎨 Interactive Elements initialized');
    }
    
    // Enhanced button animations dengan ripple effect
    setupButtonAnimations() {
        document.querySelectorAll('.btn-primary, .btn-outline, .btn-white').forEach(button => {
            button.addEventListener('click', (e) => {
                this.createRippleEffect(e, button);
                this.addClickAnimation(button);
            });
            
            // Hover glow effect
            button.addEventListener('mouseenter', (e) => {
                this.addGlowEffect(e.target);
            });
            
            button.addEventListener('mouseleave', (e) => {
                this.removeGlowEffect(e.target);
            });
        });
    }
    
    createRippleEffect(e, button) {
        const ripple = document.createElement('span');
        const rect = button.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple 0.6s ease-out;
            pointer-events: none;
            z-index: 1;
        `;
        
        button.style.position = 'relative';
        button.style.overflow = 'hidden';
        button.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    }
    
    addClickAnimation(button) {
        button.style.transform = 'scale(0.95)';
        setTimeout(() => {
            button.style.transform = '';
        }, 150);
    }
    
    addGlowEffect(element) {
        element.style.filter = 'drop-shadow(0 0 20px rgba(59, 130, 246, 0.5))';
        element.style.transition = 'all 0.3s ease';
    }
    
    removeGlowEffect(element) {
        element.style.filter = '';
    }
    
    // Parallax untuk background elements
    setupParallaxElements() {
        const parallaxElements = document.querySelectorAll('[data-parallax]');
        
        if (parallaxElements.length && window.innerWidth > 768) {
            window.addEventListener('scroll', this.throttle(() => {
                const scrolled = window.pageYOffset;
                
                parallaxElements.forEach(element => {
                    const speed = element.dataset.parallax || 0.5;
                    const yPos = -(scrolled * speed);
                    element.style.transform = `translate3d(0, ${yPos}px, 0)`;
                });
            }, 10));
        }
    }
    
    // Hover effects untuk cards dan elements
    setupHoverEffects() {
        // Card tilt effect
        document.querySelectorAll('.card-feature, .card-contact').forEach(card => {
            card.addEventListener('mouseenter', (e) => {
                this.addCardTiltEffect(e.target);
            });
            
            card.addEventListener('mousemove', (e) => {
                this.updateCardTilt(e, e.currentTarget);
            });
            
            card.addEventListener('mouseleave', (e) => {
                this.removeCardTiltEffect(e.target);
            });
        });
        
        // Icon bounce pada hover
        document.querySelectorAll('.icon-feature, .icon-contact').forEach(icon => {
            icon.addEventListener('mouseenter', () => {
                icon.style.animation = 'bounce 0.6s ease';
            });
            
            icon.addEventListener('animationend', () => {
                icon.style.animation = '';
            });
        });
    }
    
    addCardTiltEffect(card) {
        card.style.transition = 'transform 0.1s ease-out';
        card.style.transformStyle = 'preserve-3d';
    }
    
    updateCardTilt(e, card) {
        const rect = card.getBoundingClientRect();
        const centerX = rect.left + rect.width / 2;
        const centerY = rect.top + rect.height / 2;
        const mouseX = e.clientX - centerX;
        const mouseY = e.clientY - centerY;
        
        const rotateX = (mouseY / (rect.height / 2)) * -10;
        const rotateY = (mouseX / (rect.width / 2)) * 10;
        
        card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
    }
    
    removeCardTiltEffect(card) {
        card.style.transform = '';
        card.style.transition = 'transform 0.3s ease';
    }
    
    // Scroll animations yang lebih smooth
    setupScrollAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                    
                    // Staggered animation untuk children
                    const children = entry.target.querySelectorAll('.animate-on-scroll');
                    children.forEach((child, index) => {
                        setTimeout(() => {
                            child.classList.add('animate-in');
                        }, index * 100);
                    });
                }
            });
        }, observerOptions);
        
        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });
    }
    
    // Typing effect untuk hero text
    setupTypingEffect() {
        const heroTitle = document.querySelector('#hero-typing-text');
        if (heroTitle) {
            const text = heroTitle.textContent;
            heroTitle.textContent = '';
            
            let i = 0;
            const typeWriter = () => {
                if (i < text.length) {
                    heroTitle.textContent += text.charAt(i);
                    i++;
                    setTimeout(typeWriter, 100);
                } else {
                    // Add blinking cursor
                    heroTitle.style.borderRight = '3px solid #3b82f6';
                    heroTitle.style.animation = 'blink 1s infinite';
                }
            };
            
            setTimeout(typeWriter, 1000);
        }
    }
    
    // Particle effects di background
    setupParticles() {
        if (window.innerWidth > 768) {
            this.createFloatingParticles();
        }
    }
    
    createFloatingParticles() {
        const particleContainer = document.createElement('div');
        particleContainer.className = 'particle-container';
        particleContainer.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
            overflow: hidden;
        `;
        
        document.body.appendChild(particleContainer);
        
        for (let i = 0; i < 20; i++) {
            const particle = document.createElement('div');
            particle.className = 'floating-particle';
            particle.style.cssText = `
                position: absolute;
                width: ${Math.random() * 6 + 2}px;
                height: ${Math.random() * 6 + 2}px;
                background: rgba(59, 130, 246, 0.1);
                border-radius: 50%;
                left: ${Math.random() * 100}%;
                top: ${Math.random() * 100}%;
                animation: float ${Math.random() * 10 + 10}s infinite linear;
            `;
            
            particleContainer.appendChild(particle);
        }
    }
    
    // Smooth scrolling yang enhanced
    setupSmoothScrolling() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = anchor.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    const headerHeight = 80;
                    const targetPosition = targetElement.offsetTop - headerHeight;
                    
                    // Custom smooth scroll dengan easing
                    this.smoothScrollTo(targetPosition, 800);
                }
            });
        });
    }
    
    smoothScrollTo(target, duration) {
        const start = window.pageYOffset;
        const change = target - start;
        const startTime = performance.now();
        
        const animateScroll = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Easing function (easeInOutCubic)
            const ease = progress < 0.5 
                ? 4 * progress * progress * progress 
                : 1 - Math.pow(-2 * progress + 2, 3) / 2;
            
            window.scrollTo(0, start + change * ease);
            
            if (progress < 1) {
                requestAnimationFrame(animateScroll);
            }
        };
        
        requestAnimationFrame(animateScroll);
    }
    
    // Enhanced navbar dengan solid background
    setupNavbarEnhancements() {
        const navbar = document.querySelector('nav');
        if (!navbar) return;
        
        window.addEventListener('scroll', this.throttle(() => {
            const scrollY = window.scrollY;
            
            if (scrollY > 50) {
                navbar.style.borderBottom = '1px solid rgba(59, 130, 246, 0.1)';
                navbar.classList.add('shadow-lg');
            } else {
                navbar.style.borderBottom = '';
                navbar.classList.remove('shadow-lg');
            }
        }, 10));
    }
    
    // Utility throttle function
    throttle(func, delay) {
        let timeoutId;
        let lastExecTime = 0;
        return function (...args) {
            const currentTime = Date.now();
            
            if (currentTime - lastExecTime > delay) {
                func.apply(this, args);
                lastExecTime = currentTime;
            } else {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => {
                    func.apply(this, args);
                    lastExecTime = Date.now();
                }, delay - (currentTime - lastExecTime));
            }
        };
    }
}

// CSS animations
const animationCSS = `
@keyframes ripple {
    to {
        transform: scale(4);
        opacity: 0;
    }
}

@keyframes float {
    0%, 100% { transform: translateY(0px) translateX(0px); }
    25% { transform: translateY(-20px) translateX(10px); }
    50% { transform: translateY(-10px) translateX(-10px); }
    75% { transform: translateY(-30px) translateX(5px); }
}

@keyframes bounce {
    0%, 20%, 53%, 80%, 100% { transform: translate3d(0, 0, 0); }
    40%, 43% { transform: translate3d(0, -15px, 0); }
    70% { transform: translate3d(0, -8px, 0); }
    90% { transform: translate3d(0, -3px, 0); }
}

@keyframes blink {
    0%, 50% { border-right-color: #3b82f6; }
    51%, 100% { border-right-color: transparent; }
}

.animate-on-scroll {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.animate-on-scroll.animate-in {
    opacity: 1;
    transform: translateY(0);
}

.floating-particle {
    will-change: transform;
}
`;

// Inject CSS
const styleSheet = document.createElement('style');
styleSheet.textContent = animationCSS;
document.head.appendChild(styleSheet);

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.interactiveElements = new InteractiveElements();
});