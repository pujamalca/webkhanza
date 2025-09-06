import { AdvancedLazyLoading } from '../../public/js/advanced-lazy-loading';

describe('AdvancedLazyLoading', () => {
  let lazyLoader;
  let mockDocument;
  let mockWindow;

  beforeEach(() => {
    // Setup mock DOM
    mockDocument = {
      querySelectorAll: jest.fn(),
      addEventListener: jest.fn(),
      createElement: jest.fn().mockReturnValue({
        textContent: '',
        appendChild: jest.fn(),
      }),
      head: {
        appendChild: jest.fn(),
      },
    };

    mockWindow = {
      IntersectionObserver: jest.fn().mockImplementation((callback) => ({
        observe: jest.fn(),
        unobserve: jest.fn(),
        disconnect: jest.fn(),
      })),
      addEventListener: jest.fn(),
      removeEventListener: jest.fn(),
      performance: {
        now: jest.fn().mockReturnValue(0),
      },
      scrollY: 0,
      innerHeight: 1000,
      requestAnimationFrame: jest.fn(cb => cb(0)),
      Image: class {
        constructor() {
          this.onload = null;
          this.onerror = null;
          this.src = '';
        }
      },
    };

    // Replace global document and window
    global.document = mockDocument;
    global.window = mockWindow;

    // Mock querySelector to return empty arrays
    mockDocument.querySelectorAll.mockReturnValue([]);

    // Create a new instance before each test
    lazyLoader = new AdvancedLazyLoading();
  });

  afterEach(() => {
    jest.clearAllMocks();
  });

  describe('Constructor and Initialization', () => {
    test('initializes with correct default properties', () => {
      expect(lazyLoader.imageObserver).toBeDefined();
      expect(lazyLoader.componentObserver).toBeDefined();
      expect(lazyLoader.animationObserver).toBeDefined();
      expect(lazyLoader.loadedImages).toBeInstanceOf(Set);
      expect(lazyLoader.loadingQueue).toBeInstanceOf(Array);
      expect(lazyLoader.performanceMetrics).toEqual({
        imagesLoaded: 0,
        totalLoadTime: 0,
        averageLoadTime: 0
      });
    });

    test('fallback loading called when IntersectionObserver not supported', () => {
      // Reset window and remove IntersectionObserver
      mockWindow.IntersectionObserver = undefined;
      
      // Recreate lazyLoader
      const fallbackSpy = jest.spyOn(AdvancedLazyLoading.prototype, 'fallbackLazyLoading');
      const lazyLoaderWithoutIO = new AdvancedLazyLoading();

      expect(fallbackSpy).toHaveBeenCalled();
    });
  });

  describe('Image Loading', () => {
    test('loadImage handles successful image loading', async () => {
      const mockImg = {
        dataset: { src: 'test.jpg' },
        classList: {
          add: jest.fn(),
          remove: jest.fn(),
        },
        dispatchEvent: jest.fn(),
        addEventListener: jest.fn(),
        parentNode: {
          style: {},
          appendChild: jest.fn(),
        },
      };

      // Mock Image class to simulate successful load
      const originalImage = window.Image;
      window.Image = class {
        constructor() {
          this.onload = null;
          this.onerror = null;
          this.src = '';
        }
        set onload(fn) {
          setTimeout(() => fn(), 10);
        }
      };

      await lazyLoader.loadImage(mockImg);

      expect(mockImg.classList.add).toHaveBeenCalledWith('loaded');
      expect(mockImg.classList.remove).toHaveBeenCalledWith('lazy-placeholder');
      expect(mockImg.dispatchEvent).toHaveBeenCalled();

      // Restore original Image class
      window.Image = originalImage;
    });

    test('loadImage handles image loading error', async () => {
      const mockImg = {
        dataset: { src: 'test.jpg' },
        classList: {
          add: jest.fn(),
        },
        parentNode: {
          insertBefore: jest.fn(),
        },
        style: {
          display: '',
        },
      };

      // Mock Image class to simulate load error
      const originalImage = window.Image;
      window.Image = class {
        constructor() {
          this.onload = null;
          this.onerror = null;
          this.src = '';
        }
        set onerror(fn) {
          setTimeout(() => fn(new Error('Load failed')), 10);
        }
      };

      await lazyLoader.loadImage(mockImg);

      expect(mockImg.classList.add).toHaveBeenCalledWith('lazy-error');
      expect(mockImg.parentNode.insertBefore).toHaveBeenCalled();
      expect(mockImg.style.display).toBe('none');

      // Restore original Image class
      window.Image = originalImage;
    });
  });

  describe('Performance Metrics', () => {
    test('updatePerformanceMetrics calculates metrics correctly', () => {
      const initialMetrics = { ...lazyLoader.performanceMetrics };

      lazyLoader.updatePerformanceMetrics(100);

      expect(lazyLoader.performanceMetrics.imagesLoaded).toBe(1);
      expect(lazyLoader.performanceMetrics.totalLoadTime).toBe(100);
      expect(lazyLoader.performanceMetrics.averageLoadTime).toBe(100);

      lazyLoader.updatePerformanceMetrics(200);

      expect(lazyLoader.performanceMetrics.imagesLoaded).toBe(2);
      expect(lazyLoader.performanceMetrics.totalLoadTime).toBe(300);
      expect(lazyLoader.performanceMetrics.averageLoadTime).toBe(150);
    });

    test('getPerformanceMetrics returns correct metrics', () => {
      const metrics = lazyLoader.getPerformanceMetrics();
      expect(metrics).toEqual({
        imagesLoaded: 0,
        totalLoadTime: 0,
        averageLoadTime: 0
      });
    });
  });

  describe('Throttle Utility', () => {
    test('throttle limits function calls', () => {
      jest.useFakeTimers();

      const mockFn = jest.fn();
      const throttledFn = lazyLoader.throttle(mockFn, 100);

      // First call should execute immediately
      throttledFn();
      expect(mockFn).toHaveBeenCalledTimes(1);

      // Subsequent calls within throttle period should be ignored
      throttledFn();
      throttledFn();
      expect(mockFn).toHaveBeenCalledTimes(1);

      // Fast forward time
      jest.advanceTimersByTime(150);

      // Now it should execute again
      throttledFn();
      expect(mockFn).toHaveBeenCalledTimes(2);

      jest.useRealTimers();
    });
  });

  describe('Component and Observers', () => {
    test('destroy method disconnects all observers', () => {
      const disconnectSpy = jest.fn();
      
      lazyLoader.imageObserver = { disconnect: disconnectSpy };
      lazyLoader.componentObserver = { disconnect: disconnectSpy };
      lazyLoader.animationObserver = { disconnect: disconnectSpy };

      lazyLoader.destroy();

      expect(disconnectSpy).toHaveBeenCalledTimes(3);
    });

    test('observeNewElements adds new elements to observers', () => {
      const imgMock = {
        classList: {
          add: jest.fn(),
        }
      };
      const animationMock = {
        classList: {
          add: jest.fn(),
        }
      };

      mockDocument.querySelectorAll
        .mockReturnValueOnce([imgMock])  // for images
        .mockReturnValueOnce([animationMock]);  // for animation elements

      const imgObserveSpy = jest.fn();
      const animationObserveSpy = jest.fn();

      lazyLoader.imageObserver = { 
        observe: imgObserveSpy 
      };
      lazyLoader.animationObserver = { 
        observe: animationObserveSpy 
      };

      lazyLoader.observeNewElements();

      expect(imgMock.classList.add).toHaveBeenCalledWith('observed');
      expect(animationMock.classList.add).toHaveBeenCalledWith('observed');
      expect(imgObserveSpy).toHaveBeenCalledWith(imgMock);
      expect(animationObserveSpy).toHaveBeenCalledWith(animationMock);
    });
  });

  describe('Fallback Loading', () => {
    test('fallbackLazyLoading works for browsers without IntersectionObserver', () => {
      // Setup mock images
      const mockImages = [
        { 
          offsetTop: 500,
          getBoundingClientRect: jest.fn().mockReturnValue({
            top: 0,
            left: 0,
            bottom: 500,
            right: 500
          })
        }
      ];

      mockDocument.querySelectorAll.mockReturnValue(mockImages);

      const loadImageSpy = jest.spyOn(lazyLoader, 'loadImage');
      const isInViewportSpy = jest.spyOn(lazyLoader, 'isInViewport').mockReturnValue(true);

      lazyLoader.fallbackLazyLoading();

      // Trigger scroll/resize events
      const scrollHandler = mockWindow.addEventListener.mock.calls
        .find(call => call[0] === 'scroll')[1];
      scrollHandler();

      expect(isInViewportSpy).toHaveBeenCalledWith(mockImages[0]);
      expect(loadImageSpy).toHaveBeenCalledWith(mockImages[0]);
    });
  });
});