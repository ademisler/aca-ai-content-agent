// Mobile and touch utilities for responsive design

/**
 * Touch gesture utilities
 */
export const touchUtils = {
  /**
   * Detect if device supports touch
   */
  isTouchDevice: (): boolean => {
    return 'ontouchstart' in window || navigator.maxTouchPoints > 0;
  },

  /**
   * Get touch-friendly minimum sizes
   */
  getTouchSizes: () => ({
    minTouchTarget: '44px', // WCAG AA minimum
    minTouchSpacing: '8px',
    recommendedTouchTarget: '48px',
  }),

  /**
   * Handle touch events with proper preventDefault for iOS
   */
  handleTouchStart: (callback: (event: TouchEvent) => void) => {
    return (event: TouchEvent) => {
      // Prevent iOS bounce scroll when needed
      if (event.touches.length > 1) {
        event.preventDefault();
      }
      callback(event);
    };
  },

  /**
   * Swipe gesture detection
   */
  detectSwipe: (
    element: HTMLElement,
    onSwipe: (direction: 'left' | 'right' | 'up' | 'down') => void,
    threshold: number = 50
  ) => {
    let startX = 0;
    let startY = 0;
    let endX = 0;
    let endY = 0;

    const handleTouchStart = (e: TouchEvent) => {
      startX = e.touches[0].clientX;
      startY = e.touches[0].clientY;
    };

    const handleTouchEnd = (e: TouchEvent) => {
      endX = e.changedTouches[0].clientX;
      endY = e.changedTouches[0].clientY;

      const deltaX = endX - startX;
      const deltaY = endY - startY;

      if (Math.abs(deltaX) > Math.abs(deltaY)) {
        // Horizontal swipe
        if (Math.abs(deltaX) > threshold) {
          onSwipe(deltaX > 0 ? 'right' : 'left');
        }
      } else {
        // Vertical swipe
        if (Math.abs(deltaY) > threshold) {
          onSwipe(deltaY > 0 ? 'down' : 'up');
        }
      }
    };

    element.addEventListener('touchstart', handleTouchStart, { passive: true });
    element.addEventListener('touchend', handleTouchEnd, { passive: true });

    return () => {
      element.removeEventListener('touchstart', handleTouchStart);
      element.removeEventListener('touchend', handleTouchEnd);
    };
  },
};

/**
 * Responsive design utilities
 */
export const responsiveUtils = {
  /**
   * Breakpoints following mobile-first approach
   */
  breakpoints: {
    xs: '320px',
    sm: '640px',
    md: '768px',
    lg: '1024px',
    xl: '1280px',
    '2xl': '1536px',
  },

  /**
   * Get current screen size category
   */
  getCurrentBreakpoint: (): string => {
    const width = window.innerWidth;
    if (width < 640) return 'xs';
    if (width < 768) return 'sm';
    if (width < 1024) return 'md';
    if (width < 1280) return 'lg';
    if (width < 1536) return 'xl';
    return '2xl';
  },

  /**
   * Check if current screen is mobile
   */
  isMobile: (): boolean => {
    return window.innerWidth < 768;
  },

  /**
   * Check if current screen is tablet
   */
  isTablet: (): boolean => {
    const width = window.innerWidth;
    return width >= 768 && width < 1024;
  },

  /**
   * Check if current screen is desktop
   */
  isDesktop: (): boolean => {
    return window.innerWidth >= 1024;
  },

  /**
   * Listen for breakpoint changes
   */
  onBreakpointChange: (callback: (breakpoint: string) => void) => {
    let currentBreakpoint = responsiveUtils.getCurrentBreakpoint();

    const handleResize = () => {
      const newBreakpoint = responsiveUtils.getCurrentBreakpoint();
      if (newBreakpoint !== currentBreakpoint) {
        currentBreakpoint = newBreakpoint;
        callback(newBreakpoint);
      }
    };

    window.addEventListener('resize', handleResize);
    return () => window.removeEventListener('resize', handleResize);
  },
};

/**
 * Mobile-specific UI patterns
 */
export const mobileUIUtils = {
  /**
   * Create mobile-friendly dropdown/select
   */
  createMobileSelect: (options: { value: string; label: string }[]) => {
    const select = document.createElement('select');
    select.className = 'w-full p-3 text-base border rounded-lg focus:ring-2 focus:ring-blue-500';

    options.forEach(option => {
      const optionElement = document.createElement('option');
      optionElement.value = option.value;
      optionElement.textContent = option.label;
      select.appendChild(optionElement);
    });

    return select;
  },

  /**
   * Create mobile-friendly button with proper touch target
   */
  createTouchButton: (text: string, onClick: () => void) => {
    const button = document.createElement('button');
    button.textContent = text;
    button.className = 'min-h-[44px] px-4 py-2 text-base font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 active:bg-blue-800 transition-colors';
    button.addEventListener('click', onClick);
    return button;
  },

  /**
   * Create mobile-friendly input with proper sizing
   */
  createTouchInput: (type: string = 'text', placeholder?: string) => {
    const input = document.createElement('input');
    input.type = type;
    input.className = 'w-full min-h-[44px] px-3 py-2 text-base border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500';
    if (placeholder) {
      input.placeholder = placeholder;
    }
    return input;
  },

  /**
   * Handle mobile keyboard behavior
   */
  handleMobileKeyboard: () => {
    // Prevent zoom on input focus in iOS
    const viewport = document.querySelector('meta[name="viewport"]');
    if (viewport) {
      const originalContent = viewport.getAttribute('content') || '';
      
      const preventZoom = () => {
        viewport.setAttribute('content', originalContent + ', user-scalable=no');
      };
      
      const allowZoom = () => {
        viewport.setAttribute('content', originalContent);
      };

      // Prevent zoom on input focus
      document.addEventListener('focusin', (e) => {
        if (e.target instanceof HTMLInputElement || e.target instanceof HTMLTextAreaElement) {
          preventZoom();
        }
      });

      // Re-enable zoom after input blur
      document.addEventListener('focusout', () => {
        setTimeout(allowZoom, 100);
      });
    }
  },
};

/**
 * Mobile performance optimizations
 */
export const mobilePerformanceUtils = {
  /**
   * Lazy load images with intersection observer
   */
  lazyLoadImages: (selector: string = 'img[data-src]') => {
    const images = document.querySelectorAll(selector);
    
    if ('IntersectionObserver' in window) {
      const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const img = entry.target as HTMLImageElement;
            const src = img.dataset.src;
            if (src) {
              img.src = src;
              img.removeAttribute('data-src');
              imageObserver.unobserve(img);
            }
          }
        });
      });

      images.forEach(img => imageObserver.observe(img));
    } else {
      // Fallback for older browsers
      images.forEach(img => {
        const src = (img as HTMLImageElement).dataset.src;
        if (src) {
          (img as HTMLImageElement).src = src;
        }
      });
    }
  },

  /**
   * Optimize scroll performance with passive listeners
   */
  optimizeScrolling: (callback: () => void) => {
    let ticking = false;

    const handleScroll = () => {
      if (!ticking) {
        requestAnimationFrame(() => {
          callback();
          ticking = false;
        });
        ticking = true;
      }
    };

    window.addEventListener('scroll', handleScroll, { passive: true });
    return () => window.removeEventListener('scroll', handleScroll);
  },

  /**
   * Reduce animations on low-end devices
   */
  shouldReduceAnimations: (): boolean => {
    // Check for reduced motion preference
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      return true;
    }

    // Check for low-end device indicators
    const connection = (navigator as any).connection;
    if (connection) {
      // Slow connection
      if (connection.effectiveType === 'slow-2g' || connection.effectiveType === '2g') {
        return true;
      }
      // Data saver mode
      if (connection.saveData) {
        return true;
      }
    }

    // Low memory device
    const memory = (navigator as any).deviceMemory;
    if (memory && memory < 4) {
      return true;
    }

    return false;
  },
};

/**
 * Mobile accessibility enhancements
 */
export const mobileA11yUtils = {
  /**
   * Enhance focus visibility for touch devices
   */
  enhanceFocusVisibility: () => {
    const style = document.createElement('style');
    style.textContent = `
      /* Enhanced focus styles for mobile */
      .focus-enhanced:focus {
        outline: 3px solid #3b82f6;
        outline-offset: 2px;
        box-shadow: 0 0 0 6px rgba(59, 130, 246, 0.3);
      }
      
      /* Touch target size enforcement */
      .touch-target {
        min-height: 44px;
        min-width: 44px;
      }
      
      /* High contrast mode support */
      @media (prefers-contrast: high) {
        .contrast-enhanced {
          border: 2px solid;
          background: Canvas;
          color: CanvasText;
        }
      }
    `;
    document.head.appendChild(style);
  },

  /**
   * Add touch-friendly spacing
   */
  addTouchSpacing: (elements: NodeListOf<Element>) => {
    elements.forEach(element => {
      const htmlElement = element as HTMLElement;
      const style = window.getComputedStyle(htmlElement);
      const currentMargin = parseInt(style.marginBottom) || 0;
      
      if (currentMargin < 8) {
        htmlElement.style.marginBottom = '8px';
      }
    });
  },

  /**
   * Improve tap target sizes
   */
  improveTapTargets: () => {
    const clickableElements = document.querySelectorAll('button, a, input, select, textarea, [role="button"]');
    
    clickableElements.forEach(element => {
      const htmlElement = element as HTMLElement;
      const rect = htmlElement.getBoundingClientRect();
      
      if (rect.height < 44 || rect.width < 44) {
        htmlElement.classList.add('touch-target');
      }
    });
  },
};

/**
 * Initialize mobile optimizations
 */
export const initializeMobileOptimizations = () => {
  // Handle mobile keyboard
  mobileUIUtils.handleMobileKeyboard();
  
  // Enhance accessibility
  mobileA11yUtils.enhanceFocusVisibility();
  
  // Lazy load images
  mobilePerformanceUtils.lazyLoadImages();
  
  // Improve tap targets
  mobileA11yUtils.improveTapTargets();
  
  // Add viewport meta tag if missing
  if (!document.querySelector('meta[name="viewport"]')) {
    const viewport = document.createElement('meta');
    viewport.name = 'viewport';
    viewport.content = 'width=device-width, initial-scale=1.0, shrink-to-fit=no';
    document.head.appendChild(viewport);
  }
};