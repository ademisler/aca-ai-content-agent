// Performance monitoring utilities

/**
 * Core Web Vitals monitoring
 */
export const webVitalsUtils = {
  /**
   * Measure and report Core Web Vitals
   */
  measureWebVitals: (callback: (metric: WebVitalMetric) => void) => {
    // Largest Contentful Paint (LCP)
    if ('PerformanceObserver' in window) {
      const lcpObserver = new PerformanceObserver((list) => {
        const entries = list.getEntries();
        const lastEntry = entries[entries.length - 1];
        callback({
          name: 'LCP',
          value: lastEntry.startTime,
          rating: lastEntry.startTime <= 2500 ? 'good' : lastEntry.startTime <= 4000 ? 'needs-improvement' : 'poor',
          timestamp: Date.now(),
        });
      });
      
      try {
        lcpObserver.observe({ entryTypes: ['largest-contentful-paint'] });
      } catch (e) {
        // LCP not supported
      }

      // First Input Delay (FID)
      const fidObserver = new PerformanceObserver((list) => {
        const entries = list.getEntries();
        entries.forEach((entry: any) => {
          callback({
            name: 'FID',
            value: entry.processingStart - entry.startTime,
            rating: entry.processingStart - entry.startTime <= 100 ? 'good' : entry.processingStart - entry.startTime <= 300 ? 'needs-improvement' : 'poor',
            timestamp: Date.now(),
          });
        });
      });

      try {
        fidObserver.observe({ entryTypes: ['first-input'] });
      } catch (e) {
        // FID not supported
      }

      // Cumulative Layout Shift (CLS)
      let clsValue = 0;
      const clsObserver = new PerformanceObserver((list) => {
        const entries = list.getEntries();
        entries.forEach((entry: any) => {
          if (!entry.hadRecentInput) {
            clsValue += entry.value;
          }
        });
        
        callback({
          name: 'CLS',
          value: clsValue,
          rating: clsValue <= 0.1 ? 'good' : clsValue <= 0.25 ? 'needs-improvement' : 'poor',
          timestamp: Date.now(),
        });
      });

      try {
        clsObserver.observe({ entryTypes: ['layout-shift'] });
      } catch (e) {
        // CLS not supported
      }
    }

    // Time to First Byte (TTFB)
    if ('performance' in window && 'getEntriesByType' in performance) {
      const navigation = performance.getEntriesByType('navigation')[0] as PerformanceNavigationTiming;
      if (navigation) {
        const ttfb = navigation.responseStart - navigation.requestStart;
        callback({
          name: 'TTFB',
          value: ttfb,
          rating: ttfb <= 600 ? 'good' : ttfb <= 1000 ? 'needs-improvement' : 'poor',
          timestamp: Date.now(),
        });
      }
    }
  },

  /**
   * Get current performance metrics
   */
  getCurrentMetrics: (): PerformanceMetrics => {
    const metrics: PerformanceMetrics = {
      memory: {},
      timing: {},
      navigation: {},
    };

    // Memory usage (Chrome only)
    if ('memory' in performance) {
      const memory = (performance as any).memory;
      metrics.memory = {
        usedJSHeapSize: memory.usedJSHeapSize,
        totalJSHeapSize: memory.totalJSHeapSize,
        jsHeapSizeLimit: memory.jsHeapSizeLimit,
      };
    }

    // Navigation timing
    if ('getEntriesByType' in performance) {
      const navigation = performance.getEntriesByType('navigation')[0] as PerformanceNavigationTiming;
      if (navigation) {
        metrics.timing = {
          domContentLoaded: navigation.domContentLoadedEventEnd - navigation.domContentLoadedEventStart,
          loadComplete: navigation.loadEventEnd - navigation.loadEventStart,
          firstPaint: 0,
          firstContentfulPaint: 0,
        };

        // First Paint and First Contentful Paint
        const paintEntries = performance.getEntriesByType('paint');
        paintEntries.forEach((entry) => {
          if (entry.name === 'first-paint') {
            metrics.timing.firstPaint = entry.startTime;
          } else if (entry.name === 'first-contentful-paint') {
            metrics.timing.firstContentfulPaint = entry.startTime;
          }
        });
      }
    }

    return metrics;
  },
};

/**
 * API response time tracking
 */
export const apiPerformanceUtils = {
  /**
   * Track API call performance
   */
  trackApiCall: async <T>(
    apiCall: () => Promise<T>,
    endpoint: string,
    callback?: (metrics: ApiMetrics) => void
  ): Promise<T> => {
    const startTime = performance.now();
    const startMemory = webVitalsUtils.getCurrentMetrics().memory.usedJSHeapSize || 0;

    try {
      const result = await apiCall();
      const endTime = performance.now();
      const endMemory = webVitalsUtils.getCurrentMetrics().memory.usedJSHeapSize || 0;

      const metrics: ApiMetrics = {
        endpoint,
        duration: endTime - startTime,
        success: true,
        timestamp: Date.now(),
        memoryDelta: endMemory - startMemory,
      };

      callback?.(metrics);
      return result;
    } catch (error) {
      const endTime = performance.now();
      const endMemory = webVitalsUtils.getCurrentMetrics().memory.usedJSHeapSize || 0;

      const metrics: ApiMetrics = {
        endpoint,
        duration: endTime - startTime,
        success: false,
        error: error instanceof Error ? error.message : 'Unknown error',
        timestamp: Date.now(),
        memoryDelta: endMemory - startMemory,
      };

      callback?.(metrics);
      throw error;
    }
  },

  /**
   * Create a wrapper for API functions
   */
  createApiWrapper: (callback?: (metrics: ApiMetrics) => void) => {
    return <T>(endpoint: string, apiCall: () => Promise<T>) => {
      return apiPerformanceUtils.trackApiCall(apiCall, endpoint, callback);
    };
  },
};

/**
 * Bundle size tracking
 */
export const bundleUtils = {
  /**
   * Estimate current bundle size
   */
  estimateBundleSize: (): BundleMetrics => {
    const scripts = document.querySelectorAll('script[src]');
    const stylesheets = document.querySelectorAll('link[rel="stylesheet"]');
    
    let estimatedSize = 0;
    const resources: ResourceInfo[] = [];

    // Estimate script sizes (rough approximation)
    scripts.forEach((script) => {
      const src = (script as HTMLScriptElement).src;
      if (src.includes('index-') && src.includes('.js')) {
        // Our main bundle - estimate based on typical sizes
        const estimatedScriptSize = 270000; // 270KB based on our optimization
        estimatedSize += estimatedScriptSize;
        resources.push({
          url: src,
          type: 'script',
          estimatedSize: estimatedScriptSize,
        });
      }
    });

    // Estimate stylesheet sizes
    stylesheets.forEach((link) => {
      const href = (link as HTMLLinkElement).href;
      const estimatedStyleSize = 15000; // 15KB typical CSS size
      estimatedSize += estimatedStyleSize;
      resources.push({
        url: href,
        type: 'stylesheet',
        estimatedSize: estimatedStyleSize,
      });
    });

    return {
      totalEstimatedSize: estimatedSize,
      resources,
      timestamp: Date.now(),
    };
  },

  /**
   * Monitor bundle size changes
   */
  monitorBundleSize: (callback: (metrics: BundleMetrics) => void) => {
    const initialMetrics = bundleUtils.estimateBundleSize();
    callback(initialMetrics);

    // Monitor for new script/style additions
    const observer = new MutationObserver((mutations) => {
      let hasNewResources = false;
      
      mutations.forEach((mutation) => {
        mutation.addedNodes.forEach((node) => {
          if (node.nodeType === Node.ELEMENT_NODE) {
            const element = node as Element;
            if (element.tagName === 'SCRIPT' || element.tagName === 'LINK') {
              hasNewResources = true;
            }
          }
        });
      });

      if (hasNewResources) {
        const newMetrics = bundleUtils.estimateBundleSize();
        callback(newMetrics);
      }
    });

    observer.observe(document.head, { childList: true, subtree: true });
    return () => observer.disconnect();
  },
};

/**
 * Error boundary reporting
 */
export const errorUtils = {
  /**
   * Set up global error handling
   */
  setupGlobalErrorHandling: (callback: (error: ErrorReport) => void) => {
    // JavaScript errors
    window.addEventListener('error', (event) => {
      const errorReport: ErrorReport = {
        type: 'javascript',
        message: event.message,
        source: event.filename,
        line: event.lineno,
        column: event.colno,
        stack: event.error?.stack,
        timestamp: Date.now(),
        userAgent: navigator.userAgent,
        url: window.location.href,
      };
      callback(errorReport);
    });

    // Promise rejections
    window.addEventListener('unhandledrejection', (event) => {
      const errorReport: ErrorReport = {
        type: 'promise',
        message: event.reason?.message || 'Unhandled promise rejection',
        stack: event.reason?.stack,
        timestamp: Date.now(),
        userAgent: navigator.userAgent,
        url: window.location.href,
      };
      callback(errorReport);
    });

    // Resource loading errors
    window.addEventListener('error', (event) => {
      if (event.target !== window) {
        const target = event.target as HTMLElement;
        const errorReport: ErrorReport = {
          type: 'resource',
          message: `Failed to load resource: ${target.tagName}`,
          source: (target as any).src || (target as any).href,
          timestamp: Date.now(),
          userAgent: navigator.userAgent,
          url: window.location.href,
        };
        callback(errorReport);
      }
    }, true);
  },

  /**
   * Create React error boundary reporter
   */
  createErrorBoundaryReporter: (callback: (error: ErrorReport) => void) => {
    return (error: Error, errorInfo: { componentStack: string }) => {
      const errorReport: ErrorReport = {
        type: 'react',
        message: error.message,
        stack: error.stack,
        componentStack: errorInfo.componentStack,
        timestamp: Date.now(),
        userAgent: navigator.userAgent,
        url: window.location.href,
      };
      callback(errorReport);
    };
  },
};

/**
 * Performance dashboard
 */
export const performanceDashboard = {
  /**
   * Initialize comprehensive performance monitoring
   */
  initialize: (config: PerformanceConfig = {}) => {
    const metrics = {
      webVitals: [] as WebVitalMetric[],
      apiCalls: [] as ApiMetrics[],
      errors: [] as ErrorReport[],
      bundle: null as BundleMetrics | null,
    };

    // Web Vitals monitoring
    if (config.enableWebVitals !== false) {
      webVitalsUtils.measureWebVitals((metric) => {
        metrics.webVitals.push(metric);
        config.onWebVital?.(metric);
      });
    }

    // Bundle size monitoring
    if (config.enableBundleMonitoring !== false) {
      bundleUtils.monitorBundleSize((bundleMetrics) => {
        metrics.bundle = bundleMetrics;
        config.onBundleChange?.(bundleMetrics);
      });
    }

    // Error monitoring
    if (config.enableErrorMonitoring !== false) {
      errorUtils.setupGlobalErrorHandling((error) => {
        metrics.errors.push(error);
        config.onError?.(error);
      });
    }

    // API monitoring wrapper
    const apiWrapper = apiPerformanceUtils.createApiWrapper((apiMetrics) => {
      metrics.apiCalls.push(apiMetrics);
      config.onApiCall?.(apiMetrics);
    });

    return {
      metrics,
      apiWrapper,
      getReport: () => ({
        ...metrics,
        currentPerformance: webVitalsUtils.getCurrentMetrics(),
        timestamp: Date.now(),
      }),
    };
  },
};

// Type definitions
interface WebVitalMetric {
  name: string;
  value: number;
  rating: 'good' | 'needs-improvement' | 'poor';
  timestamp: number;
}

interface PerformanceMetrics {
  memory: {
    usedJSHeapSize?: number;
    totalJSHeapSize?: number;
    jsHeapSizeLimit?: number;
  };
  timing: {
    domContentLoaded?: number;
    loadComplete?: number;
    firstPaint?: number;
    firstContentfulPaint?: number;
  };
  navigation: Record<string, any>;
}

interface ApiMetrics {
  endpoint: string;
  duration: number;
  success: boolean;
  error?: string;
  timestamp: number;
  memoryDelta?: number;
}

interface BundleMetrics {
  totalEstimatedSize: number;
  resources: ResourceInfo[];
  timestamp: number;
}

interface ResourceInfo {
  url: string;
  type: 'script' | 'stylesheet';
  estimatedSize: number;
}

interface ErrorReport {
  type: 'javascript' | 'promise' | 'resource' | 'react';
  message: string;
  source?: string;
  line?: number;
  column?: number;
  stack?: string;
  componentStack?: string;
  timestamp: number;
  userAgent: string;
  url: string;
}

interface PerformanceConfig {
  enableWebVitals?: boolean;
  enableBundleMonitoring?: boolean;
  enableErrorMonitoring?: boolean;
  onWebVital?: (metric: WebVitalMetric) => void;
  onBundleChange?: (metrics: BundleMetrics) => void;
  onError?: (error: ErrorReport) => void;
  onApiCall?: (metrics: ApiMetrics) => void;
}