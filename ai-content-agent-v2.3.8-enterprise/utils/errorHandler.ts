/**
 * Standardized Error Handler for AI Content Agent
 * 
 * Provides consistent error handling patterns, logging, and user feedback
 * across all frontend components and API interactions.
 * 
 * @package AI_Content_Agent
 * @version 2.3.8
 * @since 2.3.8
 */

import logger from './logger';

/**
 * Error severity levels
 */
export enum ErrorSeverity {
    LOW = 'low',
    MEDIUM = 'medium',
    HIGH = 'high',
    CRITICAL = 'critical'
}

/**
 * Error categories for better organization
 */
export enum ErrorCategory {
    API = 'api',
    VALIDATION = 'validation',
    AUTHENTICATION = 'authentication',
    NETWORK = 'network',
    PERMISSION = 'permission',
    RATE_LIMIT = 'rate_limit',
    UNKNOWN = 'unknown'
}

/**
 * Standardized error interface
 */
export interface StandardError {
    id: string;
    message: string;
    category: ErrorCategory;
    severity: ErrorSeverity;
    timestamp: string;
    context?: Record<string, any>;
    stack?: string;
    userMessage?: string;
    actionable?: boolean;
    retryable?: boolean;
}

/**
 * Error handler configuration
 */
interface ErrorHandlerConfig {
    logToConsole: boolean;
    logToBackend: boolean;
    showToast: boolean;
    trackAnalytics: boolean;
}

/**
 * Default error handler configuration
 */
const DEFAULT_CONFIG: ErrorHandlerConfig = {
    logToConsole: process.env.NODE_ENV === 'development',
    logToBackend: true,
    showToast: true,
    trackAnalytics: false
};

/**
 * Standardized Error Handler - MEDIUM PRIORITY FIX
 */
export class ErrorHandler {
    private static config: ErrorHandlerConfig = DEFAULT_CONFIG;
    private static toastCallback: ((message: string, type: 'error' | 'warning') => void) | null = null;

    /**
     * Set toast callback for user notifications
     */
    static setToastCallback(callback: (message: string, type: 'error' | 'warning') => void): void {
        this.toastCallback = callback;
    }

    /**
     * Configure error handler behavior
     */
    static configure(config: Partial<ErrorHandlerConfig>): void {
        this.config = { ...this.config, ...config };
    }

    /**
     * Handle any error with standardized processing
     */
    static handle(error: any, context?: Record<string, any>): StandardError {
        const standardError = this.standardizeError(error, context);
        
        // Log to console if enabled
        if (this.config.logToConsole) {
            logger.error('Standardized Error:', standardError);
        }

        // Log to backend if enabled
        if (this.config.logToBackend) {
            this.logToBackend(standardError);
        }

        // Show toast notification if enabled
        if (this.config.showToast && this.toastCallback) {
            const toastType = standardError.severity === ErrorSeverity.CRITICAL ? 'error' : 'warning';
            this.toastCallback(standardError.userMessage || standardError.message, toastType);
        }

        // Track analytics if enabled
        if (this.config.trackAnalytics) {
            this.trackError(standardError);
        }

        return standardError;
    }

    /**
     * Convert any error to standardized format
     */
    private static standardizeError(error: any, context?: Record<string, any>): StandardError {
        const id = `ACA-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
        const timestamp = new Date().toISOString();

        // Handle different error types
        if (error instanceof Error) {
            return {
                id,
                message: error.message,
                category: this.categorizeError(error),
                severity: this.determineSeverity(error),
                timestamp,
                context,
                stack: error.stack,
                userMessage: this.generateUserMessage(error),
                actionable: this.isActionable(error),
                retryable: this.isRetryable(error)
            };
        }

        // Handle API response errors
        if (error?.response) {
            return {
                id,
                message: error.response.data?.message || `HTTP ${error.response.status}`,
                category: ErrorCategory.API,
                severity: this.determineApiSeverity(error.response.status),
                timestamp,
                context: { ...context, status: error.response.status, url: error.config?.url },
                userMessage: this.generateApiUserMessage(error.response.status),
                actionable: true,
                retryable: error.response.status >= 500
            };
        }

        // Handle string errors
        if (typeof error === 'string') {
            return {
                id,
                message: error,
                category: ErrorCategory.UNKNOWN,
                severity: ErrorSeverity.MEDIUM,
                timestamp,
                context,
                userMessage: error,
                actionable: false,
                retryable: false
            };
        }

        // Handle unknown error types
        return {
            id,
            message: 'An unknown error occurred',
            category: ErrorCategory.UNKNOWN,
            severity: ErrorSeverity.MEDIUM,
            timestamp,
            context: { ...context, originalError: error },
            userMessage: 'Something went wrong. Please try again.',
            actionable: true,
            retryable: true
        };
    }

    /**
     * Categorize error based on type and message
     */
    private static categorizeError(error: Error): ErrorCategory {
        const message = error.message.toLowerCase();
        
        if (message.includes('network') || message.includes('fetch')) {
            return ErrorCategory.NETWORK;
        }
        if (message.includes('unauthorized') || message.includes('forbidden')) {
            return ErrorCategory.AUTHENTICATION;
        }
        if (message.includes('validation') || message.includes('invalid')) {
            return ErrorCategory.VALIDATION;
        }
        if (message.includes('rate limit') || message.includes('too many requests')) {
            return ErrorCategory.RATE_LIMIT;
        }
        if (message.includes('permission') || message.includes('access denied')) {
            return ErrorCategory.PERMISSION;
        }
        
        return ErrorCategory.UNKNOWN;
    }

    /**
     * Determine error severity
     */
    private static determineSeverity(error: Error): ErrorSeverity {
        const message = error.message.toLowerCase();
        
        if (message.includes('critical') || message.includes('fatal')) {
            return ErrorSeverity.CRITICAL;
        }
        if (message.includes('network') || message.includes('timeout')) {
            return ErrorSeverity.HIGH;
        }
        if (message.includes('validation') || message.includes('invalid')) {
            return ErrorSeverity.MEDIUM;
        }
        
        return ErrorSeverity.LOW;
    }

    /**
     * Determine API error severity based on status code
     */
    private static determineApiSeverity(status: number): ErrorSeverity {
        if (status >= 500) return ErrorSeverity.CRITICAL;
        if (status === 429) return ErrorSeverity.HIGH;
        if (status >= 400) return ErrorSeverity.MEDIUM;
        return ErrorSeverity.LOW;
    }

    /**
     * Generate user-friendly error message
     */
    private static generateUserMessage(error: Error): string {
        const message = error.message.toLowerCase();
        
        if (message.includes('network')) {
            return 'Network connection issue. Please check your internet connection and try again.';
        }
        if (message.includes('unauthorized')) {
            return 'Authentication required. Please log in and try again.';
        }
        if (message.includes('forbidden')) {
            return 'You don\'t have permission to perform this action.';
        }
        if (message.includes('validation')) {
            return 'Please check your input and try again.';
        }
        if (message.includes('rate limit')) {
            return 'Too many requests. Please wait a moment and try again.';
        }
        
        return 'Something went wrong. Please try again or contact support if the problem persists.';
    }

    /**
     * Generate user-friendly API error message
     */
    private static generateApiUserMessage(status: number): string {
        switch (Math.floor(status / 100)) {
            case 4:
                if (status === 401) return 'Authentication required. Please log in.';
                if (status === 403) return 'You don\'t have permission for this action.';
                if (status === 404) return 'The requested resource was not found.';
                if (status === 429) return 'Too many requests. Please wait and try again.';
                return 'There was a problem with your request. Please check and try again.';
            case 5:
                return 'Server error. Please try again in a moment.';
            default:
                return 'An unexpected error occurred. Please try again.';
        }
    }

    /**
     * Check if error is actionable by user
     */
    private static isActionable(error: Error): boolean {
        const message = error.message.toLowerCase();
        return message.includes('validation') || 
               message.includes('unauthorized') || 
               message.includes('forbidden') ||
               message.includes('rate limit');
    }

    /**
     * Check if error is retryable
     */
    private static isRetryable(error: Error): boolean {
        const message = error.message.toLowerCase();
        return message.includes('network') || 
               message.includes('timeout') || 
               message.includes('server') ||
               message.includes('rate limit');
    }

    /**
     * Log error to backend
     */
    private static async logToBackend(error: StandardError): Promise<void> {
        try {
            if (window.acaData) {
                await fetch(`${window.acaData.api_url}debug/error`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': window.acaData.nonce
                    },
                    body: JSON.stringify({
                        error_id: error.id,
                        error: error.message,
                        category: error.category,
                        severity: error.severity,
                        timestamp: error.timestamp,
                        context: error.context,
                        stack: error.stack,
                        user_agent: navigator.userAgent,
                        url: window.location.href
                    })
                });
            }
        } catch (logError) {
            logger.error('Failed to log error to backend:', logError);
        }
    }

    /**
     * Track error for analytics
     */
    private static trackError(error: StandardError): void {
        // Placeholder for analytics tracking
        // Could integrate with Google Analytics, Mixpanel, etc.
        if (typeof gtag !== 'undefined') {
            gtag('event', 'exception', {
                description: error.message,
                fatal: error.severity === ErrorSeverity.CRITICAL,
                custom_map: {
                    error_id: error.id,
                    category: error.category,
                    severity: error.severity
                }
            });
        }
    }
}

/**
 * Convenience functions for common error handling patterns
 */
export const handleApiError = (error: any, context?: Record<string, any>): StandardError => {
    return ErrorHandler.handle(error, { ...context, source: 'api' });
};

export const handleValidationError = (error: any, context?: Record<string, any>): StandardError => {
    return ErrorHandler.handle(error, { ...context, source: 'validation' });
};

export const handleNetworkError = (error: any, context?: Record<string, any>): StandardError => {
    return ErrorHandler.handle(error, { ...context, source: 'network' });
};

/**
 * Error handling hook for React components
 */
export const useErrorHandler = () => {
    return {
        handleError: ErrorHandler.handle,
        handleApiError,
        handleValidationError,
        handleNetworkError,
        setToastCallback: ErrorHandler.setToastCallback
    };
};

export default ErrorHandler;