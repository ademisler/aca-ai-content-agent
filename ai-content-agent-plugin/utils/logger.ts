/**
 * Production-safe logging utility
 * Only logs in development mode to prevent performance impact in production
 */

const isDevelopment = process.env.NODE_ENV === 'development';

export const logger = {
    log: (message: string, ...args: any[]) => {
        if (isDevelopment) {
            console.log(`[ACA] ${message}`, ...args);
        }
    },

    error: (message: string, error?: any, ...args: any[]) => {
        if (isDevelopment) {
            console.error(`[ACA ERROR] ${message}`, error, ...args);
        }
        // In production, we might want to send errors to a logging service
        // For now, we'll still log errors but with minimal info
        else if (error) {
            console.error(`[ACA] ${message}`);
        }
    },

    warn: (message: string, ...args: any[]) => {
        if (isDevelopment) {
            console.warn(`[ACA WARN] ${message}`, ...args);
        }
    },

    info: (message: string, ...args: any[]) => {
        if (isDevelopment) {
            console.info(`[ACA INFO] ${message}`, ...args);
        }
    },

    debug: (message: string, ...args: any[]) => {
        if (isDevelopment) {
            console.debug(`[ACA DEBUG] ${message}`, ...args);
        }
    }
};

export default logger;