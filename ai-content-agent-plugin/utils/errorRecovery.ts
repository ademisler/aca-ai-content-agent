/**
 * Error Recovery and Retry Utility for AI Content Agent
 * Provides robust error handling, retry logic, and graceful degradation
 */

import logger from './logger';

export interface RetryOptions {
    maxAttempts?: number;
    baseDelay?: number;
    maxDelay?: number;
    backoffFactor?: number;
    shouldRetry?: (error: any) => boolean;
    onRetry?: (attempt: number, error: any) => void;
}

export interface NetworkError extends Error {
    status?: number;
    code?: string;
    isNetworkError: boolean;
}

/**
 * Default retry configuration
 */
const DEFAULT_RETRY_OPTIONS: Required<RetryOptions> = {
    maxAttempts: 3,
    baseDelay: 1000,
    maxDelay: 10000,
    backoffFactor: 2,
    shouldRetry: (error: any) => {
        // Retry on network errors, timeouts, and 5xx server errors
        if (error.isNetworkError) return true;
        if (error.name === 'TimeoutError') return true;
        if (error.status >= 500 && error.status < 600) return true;
        return false;
    },
    onRetry: (attempt: number, error: any) => {
        logger.warn(`Retry attempt ${attempt}`, error);
    }
};

/**
 * Create a network error object
 */
export const createNetworkError = (message: string, status?: number, code?: string): NetworkError => {
    const error = new Error(message) as NetworkError;
    error.isNetworkError = true;
    error.status = status;
    error.code = code;
    return error;
};

/**
 * Check if error is a network-related error
 */
export const isNetworkError = (error: any): boolean => {
    return (
        error.isNetworkError ||
        error.name === 'NetworkError' ||
        error.name === 'TimeoutError' ||
        error.code === 'NETWORK_ERROR' ||
        error.code === 'TIMEOUT' ||
        (error.status >= 500 && error.status < 600) ||
        error.message?.includes('fetch') ||
        error.message?.includes('network') ||
        error.message?.includes('timeout')
    );
};

/**
 * Sleep utility for delays
 */
const sleep = (ms: number): Promise<void> => {
    return new Promise(resolve => setTimeout(resolve, ms));
};

/**
 * Calculate exponential backoff delay
 */
const calculateDelay = (attempt: number, options: Required<RetryOptions>): number => {
    const delay = options.baseDelay * Math.pow(options.backoffFactor, attempt - 1);
    return Math.min(delay, options.maxDelay);
};

/**
 * Retry wrapper for async functions
 */
export const withRetry = async <T>(
    fn: () => Promise<T>,
    options: RetryOptions = {}
): Promise<T> => {
    const opts = { ...DEFAULT_RETRY_OPTIONS, ...options };
    let lastError: any;

    for (let attempt = 1; attempt <= opts.maxAttempts; attempt++) {
        try {
            return await fn();
        } catch (error) {
            lastError = error;
            
            // Don't retry if this is the last attempt
            if (attempt === opts.maxAttempts) {
                break;
            }

            // Check if we should retry this error
            if (!opts.shouldRetry(error)) {
                break;
            }

            // Call retry callback
            opts.onRetry(attempt, error);

            // Calculate and apply delay
            const delay = calculateDelay(attempt, opts);
            await sleep(delay);
        }
    }

    throw lastError;
};

/**
 * Timeout wrapper for promises
 */
export const withTimeout = <T>(
    promise: Promise<T>,
    timeoutMs: number,
    timeoutMessage: string = 'Operation timed out'
): Promise<T> => {
    return Promise.race([
        promise,
        new Promise<T>((_, reject) => {
            setTimeout(() => {
                const error = createNetworkError(timeoutMessage, 408, 'TIMEOUT');
                reject(error);
            }, timeoutMs);
        })
    ]);
};

/**
 * Enhanced fetch with retry and timeout
 */
export const robustFetch = async (
    url: string,
    options: RequestInit = {},
    retryOptions: RetryOptions = {},
    timeoutMs: number = 30000
): Promise<Response> => {
    return withRetry(async () => {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), timeoutMs);

        try {
            const response = await fetch(url, {
                ...options,
                signal: controller.signal
            });

            clearTimeout(timeoutId);

            if (!response.ok) {
                throw createNetworkError(
                    `HTTP ${response.status}: ${response.statusText}`,
                    response.status,
                    'HTTP_ERROR'
                );
            }

            return response;
        } catch (error: any) {
            clearTimeout(timeoutId);
            
            if (error.name === 'AbortError') {
                throw createNetworkError('Request timed out', 408, 'TIMEOUT');
            }

            if (!error.isNetworkError && !isNetworkError(error)) {
                throw createNetworkError(error.message || 'Network request failed', 0, 'NETWORK_ERROR');
            }

            throw error;
        }
    }, retryOptions);
};

/**
 * Circuit breaker pattern implementation
 */
export class CircuitBreaker {
    private failures = 0;
    private lastFailureTime = 0;
    private state: 'CLOSED' | 'OPEN' | 'HALF_OPEN' = 'CLOSED';

    constructor(
        private threshold: number = 5,
        private timeout: number = 60000,
        private monitoringPeriod: number = 10000
    ) {}

    async execute<T>(fn: () => Promise<T>): Promise<T> {
        if (this.state === 'OPEN') {
            if (Date.now() - this.lastFailureTime > this.timeout) {
                this.state = 'HALF_OPEN';
            } else {
                throw createNetworkError('Circuit breaker is OPEN', 503, 'CIRCUIT_BREAKER_OPEN');
            }
        }

        try {
            const result = await fn();
            this.onSuccess();
            return result;
        } catch (error) {
            this.onFailure();
            throw error;
        }
    }

    private onSuccess(): void {
        this.failures = 0;
        this.state = 'CLOSED';
    }

    private onFailure(): void {
        this.failures++;
        this.lastFailureTime = Date.now();

        if (this.failures >= this.threshold) {
            this.state = 'OPEN';
        }
    }

    getState(): string {
        return this.state;
    }
}

/**
 * Graceful degradation utility
 */
export class GracefulDegradation {
    private fallbacks = new Map<string, () => any>();
    private cache = new Map<string, { data: any; timestamp: number; ttl: number }>();

    /**
     * Register a fallback function for a service
     */
    registerFallback(service: string, fallback: () => any): void {
        this.fallbacks.set(service, fallback);
    }

    /**
     * Execute with fallback
     */
    async executeWithFallback<T>(
        service: string,
        primaryFn: () => Promise<T>,
        cacheKey?: string,
        cacheTTL: number = 300000 // 5 minutes
    ): Promise<T> {
        try {
            const result = await primaryFn();
            
            // Cache successful result
            if (cacheKey) {
                this.cache.set(cacheKey, {
                    data: result,
                    timestamp: Date.now(),
                    ttl: cacheTTL
                });
            }
            
            return result;
        } catch (error) {
            logger.error(`Primary service failed for ${service}`, error);

            // Try cached data first
            if (cacheKey) {
                const cached = this.getCachedData(cacheKey);
                if (cached) {
                    logger.warn(`Using cached data for ${service}`);
                    return cached;
                }
            }

            // Use fallback
            const fallback = this.fallbacks.get(service);
            if (fallback) {
                logger.warn(`Using fallback for ${service}`);
                return fallback();
            }

            // No fallback available, re-throw error
            throw error;
        }
    }

    /**
     * Get cached data if valid
     */
    private getCachedData(key: string): any | null {
        const cached = this.cache.get(key);
        if (!cached) return null;

        if (Date.now() - cached.timestamp > cached.ttl) {
            this.cache.delete(key);
            return null;
        }

        return cached.data;
    }

    /**
     * Clear cache
     */
    clearCache(key?: string): void {
        if (key) {
            this.cache.delete(key);
        } else {
            this.cache.clear();
        }
    }
}

/**
 * Global instances
 */
export const circuitBreaker = new CircuitBreaker();
export const gracefulDegradation = new GracefulDegradation();

/**
 * WordPress API wrapper with error recovery
 */
export const wpApiCall = async <T>(
    endpoint: string,
    options: RequestInit = {},
    retryOptions: RetryOptions = {}
): Promise<T> => {
    const baseUrl = (window as any).acaData?.rest_url || '/wp-json/';
    const nonce = (window as any).acaData?.nonce || '';
    
    const url = `${baseUrl}${endpoint}`;
    const headers = {
        'Content-Type': 'application/json',
        'X-WP-Nonce': nonce,
        ...options.headers
    };

    return circuitBreaker.execute(async () => {
        const response = await robustFetch(url, { ...options, headers }, retryOptions);
        return response.json();
    });
};

/**
 * Setup fallbacks for common services
 */
gracefulDegradation.registerFallback('ideas', () => ({
    ideas: [],
    message: 'Ideas service temporarily unavailable. Using cached data or empty state.'
}));

gracefulDegradation.registerFallback('drafts', () => ({
    drafts: [],
    message: 'Drafts service temporarily unavailable. Using cached data or empty state.'
}));

gracefulDegradation.registerFallback('settings', () => ({
    geminiApiKey: '',
    mode: 'manual',
    message: 'Settings service temporarily unavailable. Using default settings.'
}));

export default {
    withRetry,
    withTimeout,
    robustFetch,
    wpApiCall,
    CircuitBreaker,
    GracefulDegradation,
    circuitBreaker,
    gracefulDegradation,
    createNetworkError,
    isNetworkError
};