// Enhanced API error handling with retry logic and fallback strategies

export interface ApiError {
  message: string;
  code?: string;
  status?: number;
  retryable?: boolean;
  timestamp: number;
  endpoint?: string;
  retryCount?: number;
}

export interface RetryConfig {
  maxRetries: number;
  baseDelay: number;
  maxDelay: number;
  backoffMultiplier: number;
  retryCondition?: (error: ApiError) => boolean;
}

export interface FallbackStrategy {
  type: 'cache' | 'mock' | 'alternative' | 'graceful-degradation';
  handler: (error: ApiError, originalRequest: any) => Promise<any>;
}

export interface ErrorReportingConfig {
  enableReporting: boolean;
  reportToConsole: boolean;
  reportToServer: boolean;
  maxReportsPerSession: number;
  endpoint?: string;
}

/**
 * Enhanced API error handler with comprehensive retry and fallback mechanisms
 */
export class ApiErrorHandler {
  private retryConfig: RetryConfig;
  private fallbackStrategies: Map<string, FallbackStrategy[]>;
  private errorReporting: ErrorReportingConfig;
  private errorCounts: Map<string, number>;
  private sessionReportCount: number = 0;

  constructor(
    retryConfig: Partial<RetryConfig> = {},
    errorReporting: Partial<ErrorReportingConfig> = {}
  ) {
    this.retryConfig = {
      maxRetries: 3,
      baseDelay: 1000,
      maxDelay: 10000,
      backoffMultiplier: 2,
      retryCondition: this.defaultRetryCondition,
      ...retryConfig,
    };

    this.errorReporting = {
      enableReporting: true,
      reportToConsole: true,
      reportToServer: false,
      maxReportsPerSession: 50,
      ...errorReporting,
    };

    this.fallbackStrategies = new Map();
    this.errorCounts = new Map();
  }

  /**
   * Main API call wrapper with error handling
   */
  async handleApiCall<T>(
    apiCall: () => Promise<T>,
    endpoint: string,
    options: {
      retryConfig?: Partial<RetryConfig>;
      fallbackStrategies?: FallbackStrategy[];
      context?: any;
    } = {}
  ): Promise<T> {
    const config = { ...this.retryConfig, ...options.retryConfig };
    const strategies = options.fallbackStrategies || this.fallbackStrategies.get(endpoint) || [];

    let lastError: ApiError;
    let retryCount = 0;

    while (retryCount <= config.maxRetries) {
      try {
        const result = await apiCall();
        
        // Reset error count on success
        this.errorCounts.set(endpoint, 0);
        
        return result;
      } catch (error) {
        lastError = this.normalizeError(error, endpoint, retryCount);
        
        // Report error
        await this.reportError(lastError, options.context);
        
        // Check if we should retry
        if (retryCount < config.maxRetries && this.shouldRetry(lastError, config)) {
          const delay = this.calculateDelay(retryCount, config);
          await this.delay(delay);
          retryCount++;
          continue;
        }

        // Try fallback strategies
        for (const strategy of strategies) {
          try {
            const fallbackResult = await strategy.handler(lastError, options.context);
            this.reportFallbackSuccess(endpoint, strategy.type);
            return fallbackResult;
          } catch (fallbackError) {
            this.reportFallbackFailure(endpoint, strategy.type, fallbackError);
          }
        }

        // All retries and fallbacks failed
        break;
      }
    }

    // Increment error count
    const currentCount = this.errorCounts.get(endpoint) || 0;
    this.errorCounts.set(endpoint, currentCount + 1);

    throw lastError;
  }

  /**
   * Register fallback strategies for specific endpoints
   */
  registerFallbackStrategy(endpoint: string, strategy: FallbackStrategy) {
    if (!this.fallbackStrategies.has(endpoint)) {
      this.fallbackStrategies.set(endpoint, []);
    }
    this.fallbackStrategies.get(endpoint)!.push(strategy);
  }

  /**
   * Default retry condition
   */
  private defaultRetryCondition(error: ApiError): boolean {
    // Retry on network errors, timeouts, and 5xx errors
    if (!error.status) return true; // Network error
    if (error.status >= 500) return true; // Server error
    if (error.status === 429) return true; // Rate limit
    if (error.status === 408) return true; // Timeout
    
    return false;
  }

  /**
   * Check if error should be retried
   */
  private shouldRetry(error: ApiError, config: RetryConfig): boolean {
    if (error.retryable === false) return false;
    return config.retryCondition ? config.retryCondition(error) : this.defaultRetryCondition(error);
  }

  /**
   * Calculate delay for exponential backoff
   */
  private calculateDelay(retryCount: number, config: RetryConfig): number {
    const delay = config.baseDelay * Math.pow(config.backoffMultiplier, retryCount);
    const jitter = Math.random() * 0.1 * delay; // Add 10% jitter
    return Math.min(delay + jitter, config.maxDelay);
  }

  /**
   * Delay utility
   */
  private delay(ms: number): Promise<void> {
    return new Promise(resolve => setTimeout(resolve, ms));
  }

  /**
   * Normalize different error types into ApiError
   */
  private normalizeError(error: any, endpoint: string, retryCount: number): ApiError {
    const apiError: ApiError = {
      message: 'Unknown error',
      timestamp: Date.now(),
      endpoint,
      retryCount,
    };

    if (error instanceof Error) {
      apiError.message = error.message;
      apiError.code = (error as any).code;
    } else if (typeof error === 'string') {
      apiError.message = error;
    } else if (error && typeof error === 'object') {
      apiError.message = error.message || error.statusText || 'API Error';
      apiError.status = error.status || error.statusCode;
      apiError.code = error.code;
    }

    // Determine if error is retryable
    if (apiError.retryable === undefined) {
      apiError.retryable = this.defaultRetryCondition(apiError);
    }

    return apiError;
  }

  /**
   * Report error for monitoring and debugging
   */
  private async reportError(error: ApiError, context?: any) {
    if (!this.errorReporting.enableReporting) return;
    if (this.sessionReportCount >= this.errorReporting.maxReportsPerSession) return;

    this.sessionReportCount++;

    const errorReport = {
      ...error,
      context,
      userAgent: navigator.userAgent,
      url: window.location.href,
      sessionId: this.getSessionId(),
    };

    // Console reporting
    if (this.errorReporting.reportToConsole) {
      console.error('API Error:', errorReport);
    }

    // Server reporting
    if (this.errorReporting.reportToServer && this.errorReporting.endpoint) {
      try {
        await fetch(this.errorReporting.endpoint, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(errorReport),
        });
      } catch (reportingError) {
        console.warn('Failed to report error to server:', reportingError);
      }
    }
  }

  /**
   * Report successful fallback usage
   */
  private reportFallbackSuccess(endpoint: string, strategyType: string) {
    console.info(`Fallback strategy '${strategyType}' succeeded for endpoint: ${endpoint}`);
  }

  /**
   * Report failed fallback attempt
   */
  private reportFallbackFailure(endpoint: string, strategyType: string, error: any) {
    console.warn(`Fallback strategy '${strategyType}' failed for endpoint: ${endpoint}`, error);
  }

  /**
   * Get or generate session ID
   */
  private getSessionId(): string {
    let sessionId = sessionStorage.getItem('aca_session_id');
    if (!sessionId) {
      sessionId = Math.random().toString(36).substring(2, 15);
      sessionStorage.setItem('aca_session_id', sessionId);
    }
    return sessionId;
  }

  /**
   * Get error statistics
   */
  getErrorStats(): { [endpoint: string]: number } {
    return Object.fromEntries(this.errorCounts);
  }

  /**
   * Reset error counts
   */
  resetErrorStats() {
    this.errorCounts.clear();
    this.sessionReportCount = 0;
  }
}

/**
 * Pre-configured fallback strategies
 */
export const commonFallbackStrategies = {
  /**
   * Cache fallback - return cached data if available
   */
  cache: (cacheKey: string): FallbackStrategy => ({
    type: 'cache',
    handler: async (error: ApiError) => {
      const cached = localStorage.getItem(`aca_cache_${cacheKey}`);
      if (cached) {
        const data = JSON.parse(cached);
        if (Date.now() - data.timestamp < 24 * 60 * 60 * 1000) { // 24 hours
          console.info('Using cached data as fallback');
          return data.value;
        }
      }
      throw new Error('No valid cache available');
    },
  }),

  /**
   * Mock data fallback - return predefined mock data
   */
  mock: (mockData: any): FallbackStrategy => ({
    type: 'mock',
    handler: async () => {
      console.info('Using mock data as fallback');
      return mockData;
    },
  }),

  /**
   * Alternative endpoint fallback
   */
  alternative: (alternativeCall: () => Promise<any>): FallbackStrategy => ({
    type: 'alternative',
    handler: async () => {
      console.info('Using alternative endpoint as fallback');
      return await alternativeCall();
    },
  }),

  /**
   * Graceful degradation - return minimal functionality
   */
  gracefulDegradation: (minimalData: any): FallbackStrategy => ({
    type: 'graceful-degradation',
    handler: async () => {
      console.info('Gracefully degrading functionality');
      return minimalData;
    },
  }),
};

/**
 * User feedback utilities
 */
export class UserFeedbackManager {
  private static instance: UserFeedbackManager;
  private feedbackQueue: Array<{
    type: 'error' | 'warning' | 'info' | 'success';
    message: string;
    action?: string;
    timestamp: number;
  }> = [];

  static getInstance(): UserFeedbackManager {
    if (!UserFeedbackManager.instance) {
      UserFeedbackManager.instance = new UserFeedbackManager();
    }
    return UserFeedbackManager.instance;
  }

  /**
   * Show user-friendly error message
   */
  showError(error: ApiError, userMessage?: string) {
    const message = userMessage || this.getErrorMessage(error);
    this.addToQueue('error', message, 'retry');
    this.displayFeedback();
  }

  /**
   * Show success message
   */
  showSuccess(message: string) {
    this.addToQueue('success', message);
    this.displayFeedback();
  }

  /**
   * Show warning message
   */
  showWarning(message: string, action?: string) {
    this.addToQueue('warning', message, action);
    this.displayFeedback();
  }

  /**
   * Show info message
   */
  showInfo(message: string) {
    this.addToQueue('info', message);
    this.displayFeedback();
  }

  /**
   * Add message to queue
   */
  private addToQueue(type: 'error' | 'warning' | 'info' | 'success', message: string, action?: string) {
    this.feedbackQueue.push({
      type,
      message,
      action,
      timestamp: Date.now(),
    });

    // Limit queue size
    if (this.feedbackQueue.length > 10) {
      this.feedbackQueue.shift();
    }
  }

  /**
   * Display feedback to user
   */
  private displayFeedback() {
    // This would integrate with your UI framework
    // For now, we'll use a simple toast-like mechanism
    const latestFeedback = this.feedbackQueue[this.feedbackQueue.length - 1];
    
    // Create or update toast notification
    this.createToastNotification(latestFeedback);
  }

  /**
   * Create toast notification
   */
  private createToastNotification(feedback: any) {
    // Remove existing toast
    const existingToast = document.getElementById('aca-error-toast');
    if (existingToast) {
      existingToast.remove();
    }

    // Create new toast
    const toast = document.createElement('div');
    toast.id = 'aca-error-toast';
    toast.className = `aca-toast aca-toast-${feedback.type}`;
    toast.innerHTML = `
      <div class="aca-toast-content">
        <span class="aca-toast-message">${feedback.message}</span>
        ${feedback.action ? `<button class="aca-toast-action">${feedback.action}</button>` : ''}
        <button class="aca-toast-close">&times;</button>
      </div>
    `;

    // Add styles
    toast.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 10000;
      max-width: 400px;
      padding: 16px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      color: white;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      font-size: 14px;
      line-height: 1.4;
      background-color: ${this.getToastColor(feedback.type)};
      animation: slideIn 0.3s ease-out;
    `;

    // Add to DOM
    document.body.appendChild(toast);

    // Auto-remove after 5 seconds
    setTimeout(() => {
      if (toast.parentNode) {
        toast.remove();
      }
    }, 5000);

    // Add close handler
    const closeButton = toast.querySelector('.aca-toast-close');
    if (closeButton) {
      closeButton.addEventListener('click', () => toast.remove());
    }
  }

  /**
   * Get toast color based on type
   */
  private getToastColor(type: string): string {
    switch (type) {
      case 'error': return '#dc3545';
      case 'warning': return '#ffc107';
      case 'success': return '#28a745';
      case 'info': return '#17a2b8';
      default: return '#6c757d';
    }
  }

  /**
   * Get user-friendly error message
   */
  private getErrorMessage(error: ApiError): string {
    if (error.status === 429) {
      return 'Too many requests. Please wait a moment and try again.';
    }
    if (error.status === 401) {
      return 'Authentication failed. Please check your API keys.';
    }
    if (error.status === 403) {
      return 'Access denied. Please check your permissions.';
    }
    if (error.status === 404) {
      return 'The requested resource was not found.';
    }
    if (error.status && error.status >= 500) {
      return 'Server error. Please try again later.';
    }
    if (error.message.includes('network') || error.message.includes('fetch')) {
      return 'Network error. Please check your connection and try again.';
    }
    
    return error.message || 'An unexpected error occurred. Please try again.';
  }

  /**
   * Get all feedback messages
   */
  getFeedbackHistory() {
    return [...this.feedbackQueue];
  }

  /**
   * Clear feedback history
   */
  clearFeedback() {
    this.feedbackQueue = [];
  }
}

// Global instance
export const userFeedback = UserFeedbackManager.getInstance();

// Default error handler instance
export const defaultApiErrorHandler = new ApiErrorHandler();

// Add CSS for toast animations
const style = document.createElement('style');
style.textContent = `
  @keyframes slideIn {
    from {
      transform: translateX(100%);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }
  
  .aca-toast-content {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  
  .aca-toast-message {
    flex: 1;
  }
  
  .aca-toast-action,
  .aca-toast-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: inherit;
    padding: 4px 8px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
  }
  
  .aca-toast-action:hover,
  .aca-toast-close:hover {
    background: rgba(255, 255, 255, 0.3);
  }
`;
document.head.appendChild(style);