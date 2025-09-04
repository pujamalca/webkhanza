/**
 * Advanced Lazy Loading System with Intersection Observer
 * Features: Images, Components, Animations, Performance monitoring
 */

class AdvancedLazyLoading {
    constructor() {
        this.imageObserver = null;
        this.componentObserver = null;
        this.animationObserver = null;
        this.loadedImages = new Set();
        this.loadingQueue = [];
        this.performanceMetrics = {
            imagesLoaded: 0,
            totalLoadTime: 0,
            averageLoadTime: 0
        };
        
        this.init();
    }
    
    init() {
        if (!('IntersectionObserver' in window)) {
            this.fallbackLazyLoading();
            return;
        }
        
        this.setupImageObserver();
        this.setupComponentObserver();
        this.setupAnimationObserver();
        this.setupPreloading();
        this.monitorPerformance();
        
        console.log('🚀 Advanced Lazy Loading System initialized');
    }
    
    setupImageObserver() {
        const imageOptions = {
            root: null,
            rootMargin: '50px',
            threshold: 0.01
        };
        
        this.imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.loadImage(entry.target);
                    this.imageObserver.unobserve(entry.target);
                }
            });
        }, imageOptions);
        
        // Observe all lazy images
        document.querySelectorAll('img[loading="lazy"], .lazy-image').forEach(img => {
            this.imageObserver.observe(img);
        });
    }
    
    setupComponentObserver() {
        const componentOptions = {
            root: null,
            rootMargin: '100px',
            threshold: 0.1
        };
        
        this.componentObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.loadComponent(entry.target);
                    this.componentObserver.unobserve(entry.target);
                }
            });
        }, componentOptions);
        
        // Observe lazy components
        document.querySelectorAll('[data-lazy-component]').forEach(component => {
            this.componentObserver.observe(component);
        });
    }
    
    setupAnimationObserver() {
        const animationOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };
        
        this.animationObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.triggerAnimation(entry.target);
                    // Keep observing for repeat animations if needed
                    if (!entry.target.dataset.animateOnce) {
                        this.animationObserver.unobserve(entry.target);
                    }
                }
            });
        }, animationOptions);
        
        // Observe animation elements
        document.querySelectorAll('.animate-on-scroll').forEach(element => {
            this.animationObserver.observe(element);
        });
    }
    
    async loadImage(img) {
        const startTime = performance.now();
        
        // Show loading placeholder
        this.showImagePlaceholder(img);
        
        try {
            const src = img.dataset.src || img.src;
            
            // Create new image for preloading
            const newImg = new Image();
            
            await new Promise((resolve, reject) => {
                newImg.onload = resolve;
                newImg.onerror = reject;
                newImg.src = src;
            });
            
            // Update actual image
            img.src = src;
            img.classList.add('loaded');
            img.classList.remove('lazy-placeholder');
            
            // Track performance
            const loadTime = performance.now() - startTime;
            this.updatePerformanceMetrics(loadTime);
            
            // Add to loaded set
            this.loadedImages.add(img);
            
            // Trigger load event
            img.dispatchEvent(new CustomEvent('lazyloaded', {
                detail: { loadTime, src }
            }));
            
        } catch (error) {
            console.warn('Failed to load image:', img.src, error);
            this.showImageError(img);
        }
    }
    
    showImagePlaceholder(img) {
        if (!img.classList.contains('lazy-placeholder')) {
            img.classList.add('lazy-placeholder');
            
            // Create skeleton loader
            const placeholder = document.createElement('div');
            placeholder.className = 'absolute inset-0 bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200 animate-pulse rounded';
            placeholder.style.backgroundSize = '200% 100%';
            placeholder.style.animation = 'shimmer 1.5s infinite linear';
            
            // Position placeholder
            if (img.parentNode.style.position !== 'relative') {
                img.parentNode.style.position = 'relative';
            }
            
            img.parentNode.appendChild(placeholder);
            
            // Remove placeholder when image loads
            img.addEventListener('lazyloaded', () => {
                placeholder.remove();
            }, { once: true });
        }
    }
    
    showImageError(img) {
        img.classList.add('lazy-error');
        img.alt = 'Failed to load image';
        
        // Create error placeholder
        const errorDiv = document.createElement('div');
        errorDiv.className = 'flex items-center justify-center bg-gray-100 text-gray-400 rounded';
        errorDiv.style.width = img.width + 'px' || '100%';
        errorDiv.style.height = img.height + 'px' || '200px';
        errorDiv.innerHTML = '<i class="fas fa-image text-2xl"></i>';
        
        img.parentNode.insertBefore(errorDiv, img);
        img.style.display = 'none';
    }
    
    loadComponent(component) {
        const componentType = component.dataset.lazyComponent;
        
        switch (componentType) {
            case 'stats-counter':
                this.loadStatsCounter(component);
                break;
            case 'chart':
                this.loadChart(component);
                break;
            case 'map':
                this.loadMap(component);
                break;
            default:
                this.loadGenericComponent(component);
        }
    }
    
    loadStatsCounter(component) {
        const counters = component.querySelectorAll('[data-count]');
        
        counters.forEach(counter => {
            const target = parseInt(counter.dataset.count);
            const duration = parseInt(counter.dataset.duration) || 2000;
            const startTime = performance.now();
            
            const updateCounter = (currentTime) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                
                // Easing function
                const easeOutQuart = 1 - Math.pow(1 - progress, 4);
                const currentValue = Math.floor(target * easeOutQuart);
                
                counter.textContent = currentValue.toLocaleString();
                
                if (progress < 1) {
                    requestAnimationFrame(updateCounter);
                }
            };
            
            requestAnimationFrame(updateCounter);
        });
    }
    
    triggerAnimation(element) {
        element.classList.add('in-view');
        
        // Add staggered animation delays for children
        const children = element.querySelectorAll('.animate-on-scroll');
        children.forEach((child, index) => {
            setTimeout(() => {
                child.classList.add('in-view');
            }, index * 100);
        });
        
        // Dispatch custom event
        element.dispatchEvent(new CustomEvent('animated', {
            detail: { element }
        }));
    }
    
    setupPreloading() {
        // Preload critical images
        const criticalImages = document.querySelectorAll('[data-preload]');
        criticalImages.forEach(img => {
            this.preloadImage(img.dataset.preload || img.src);
        });
        
        // Preload next section images when user scrolls
        let lastScrollY = window.scrollY;
        
        window.addEventListener('scroll', this.throttle(() => {
            const scrollDirection = window.scrollY > lastScrollY ? 'down' : 'up';
            lastScrollY = window.scrollY;
            
            if (scrollDirection === 'down') {
                this.preloadNextSectionImages();
            }
        }, 100), { passive: true });
    }
    
    preloadImage(src) {
        if (this.loadingQueue.includes(src)) return;
        
        this.loadingQueue.push(src);
        
        const img = new Image();
        img.onload = () => {
            this.loadingQueue = this.loadingQueue.filter(s => s !== src);
        };
        img.src = src;
    }
    
    preloadNextSectionImages() {
        const viewportHeight = window.innerHeight;
        const scrollY = window.scrollY;
        const preloadZone = scrollY + viewportHeight * 1.5;
        
        const images = document.querySelectorAll('img[data-src]:not(.loaded)');
        
        images.forEach(img => {
            const imgTop = img.offsetTop;
            if (imgTop <= preloadZone && !this.loadingQueue.includes(img.dataset.src)) {
                this.preloadImage(img.dataset.src);
            }
        });
    }
    
    monitorPerformance() {
        // Monitor image loading performance only (removed web-vitals import)
        document.addEventListener('lazyloaded', (e) => {
            console.log(`📸 Image loaded in ${e.detail.loadTime.toFixed(2)}ms:`, e.detail.src);
        });
    }
    
    updatePerformanceMetrics(loadTime) {
        this.performanceMetrics.imagesLoaded++;
        this.performanceMetrics.totalLoadTime += loadTime;
        this.performanceMetrics.averageLoadTime = this.performanceMetrics.totalLoadTime / this.performanceMetrics.imagesLoaded;
    }
    
    fallbackLazyLoading() {
        // Fallback for browsers without IntersectionObserver
        const images = document.querySelectorAll('img[data-src]');
        
        const loadImagesInViewport = () => {
            images.forEach(img => {
                if (this.isInViewport(img)) {
                    this.loadImage(img);
                }
            });
        };
        
        window.addEventListener('scroll', this.throttle(loadImagesInViewport, 200));
        window.addEventListener('resize', this.throttle(loadImagesInViewport, 200));
        
        // Initial load
        loadImagesInViewport();
    }
    
    isInViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }
    
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
    
    // Public methods
    getPerformanceMetrics() {
        return this.performanceMetrics;
    }
    
    observeNewElements() {
        // Re-observe any new lazy elements
        document.querySelectorAll('img[loading="lazy"]:not(.observed)').forEach(img => {
            img.classList.add('observed');
            this.imageObserver.observe(img);
        });
        
        document.querySelectorAll('.animate-on-scroll:not(.observed)').forEach(element => {
            element.classList.add('observed');
            this.animationObserver.observe(element);
        });
    }
    
    destroy() {
        if (this.imageObserver) this.imageObserver.disconnect();
        if (this.componentObserver) this.componentObserver.disconnect();
        if (this.animationObserver) this.animationObserver.disconnect();
    }
}

// Add shimmer animation CSS
const shimmerCSS = `
@keyframes shimmer {
    0% {
        background-position: -200px 0;
    }
    100% {
        background-position: calc(200px + 100%) 0;
    }
}
`;

const style = document.createElement('style');
style.textContent = shimmerCSS;
document.head.appendChild(style);

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.lazyLoader = new AdvancedLazyLoading();
});

// Make available globally
window.AdvancedLazyLoading = AdvancedLazyLoading;