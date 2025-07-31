/**
 * Internationalization (i18n) utility for AI Content Agent
 * Provides WordPress-compatible translation functions
 */

declare global {
    interface Window {
        wp?: {
            i18n?: {
                __: (text: string, domain?: string) => string;
                _x: (text: string, context: string, domain?: string) => string;
                _n: (single: string, plural: string, number: number, domain?: string) => string;
                _nx: (single: string, plural: string, number: number, context: string, domain?: string) => string;
                sprintf: (format: string, ...args: any[]) => string;
            };
        };
    }
}

const DOMAIN = 'ai-content-agent';

/**
 * Translate a string using WordPress i18n system
 */
export const __ = (text: string, domain: string = DOMAIN): string => {
    if (window.wp?.i18n?.__) {
        return window.wp.i18n.__(text, domain);
    }
    return text; // Fallback to original text
};

/**
 * Translate a string with context
 */
export const _x = (text: string, context: string, domain: string = DOMAIN): string => {
    if (window.wp?.i18n?._x) {
        return window.wp.i18n._x(text, context, domain);
    }
    return text; // Fallback to original text
};

/**
 * Translate plural strings
 */
export const _n = (single: string, plural: string, number: number, domain: string = DOMAIN): string => {
    if (window.wp?.i18n?._n) {
        return window.wp.i18n._n(single, plural, number, domain);
    }
    return number === 1 ? single : plural; // Simple fallback
};

/**
 * Translate plural strings with context
 */
export const _nx = (single: string, plural: string, number: number, context: string, domain: string = DOMAIN): string => {
    if (window.wp?.i18n?._nx) {
        return window.wp.i18n._nx(single, plural, number, context, domain);
    }
    return number === 1 ? single : plural; // Simple fallback
};

/**
 * String formatting (sprintf-like)
 */
export const sprintf = (format: string, ...args: any[]): string => {
    if (window.wp?.i18n?.sprintf) {
        return window.wp.i18n.sprintf(format, ...args);
    }
    // Simple fallback implementation
    return format.replace(/%[sd]/g, (match) => {
        const arg = args.shift();
        return arg !== undefined ? String(arg) : match;
    });
};

/**
 * Common translation strings for the plugin
 */
export const translations = {
    // Navigation
    dashboard: () => __('Dashboard'),
    ideas: () => __('Ideas'),
    drafts: () => __('Drafts'),
    published: () => __('Published'),
    settings: () => __('Settings'),
    calendar: () => __('Calendar'),
    contentFreshness: () => __('Content Freshness'),
    
    // Actions
    save: () => __('Save'),
    cancel: () => __('Cancel'),
    delete: () => __('Delete'),
    edit: () => __('Edit'),
    create: () => __('Create'),
    publish: () => __('Publish'),
    archive: () => __('Archive'),
    restore: () => __('Restore'),
    generate: () => __('Generate'),
    analyze: () => __('Analyze'),
    
    // Status messages
    loading: () => __('Loading...'),
    saving: () => __('Saving...'),
    saved: () => __('Saved successfully!'),
    error: () => __('An error occurred'),
    success: () => __('Success!'),
    
    // Form labels
    title: () => __('Title'),
    description: () => __('Description'),
    content: () => __('Content'),
    tags: () => __('Tags'),
    status: () => __('Status'),
    
    // Settings
    apiKey: () => __('API Key'),
    automationMode: () => __('Automation Mode'),
    licenseKey: () => __('License Key'),
    
    // Counts with pluralization
    ideaCount: (count: number) => _n('%d idea', '%d ideas', count),
    draftCount: (count: number) => _n('%d draft', '%d drafts', count),
    postCount: (count: number) => _n('%d post', '%d posts', count),
    
    // Error messages
    invalidApiKey: () => __('Invalid API key format'),
    networkError: () => __('Network error occurred'),
    permissionDenied: () => __('Permission denied'),
    
    // Pro features
    proFeature: () => __('Pro Feature'),
    upgradeRequired: () => __('Upgrade required to access this feature'),
    
    // Content freshness
    needsUpdate: () => __('Needs Update'),
    upToDate: () => __('Up to Date'),
    analyzing: () => __('Analyzing...'),
    
    // Time formats
    daysAgo: (days: number) => sprintf(_n('%d day ago', '%d days ago', days), days),
    hoursAgo: (hours: number) => sprintf(_n('%d hour ago', '%d hours ago', hours), hours),
    minutesAgo: (minutes: number) => sprintf(_n('%d minute ago', '%d minutes ago', minutes), minutes),
    
    // Accessibility
    openMenu: () => _x('Open menu', 'accessibility'),
    closeModal: () => _x('Close modal', 'accessibility'),
    loading: () => _x('Loading content', 'accessibility'),
    
    // Validation messages
    required: (field: string) => sprintf(__('%s is required'), field),
    tooShort: (field: string, min: number) => sprintf(__('%s must be at least %d characters'), field, min),
    tooLong: (field: string, max: number) => sprintf(__('%s must be less than %d characters'), field, max),
};

/**
 * RTL (Right-to-Left) support utility
 */
export const isRTL = (): boolean => {
    return document.documentElement.dir === 'rtl' || 
           document.body.dir === 'rtl' ||
           getComputedStyle(document.documentElement).direction === 'rtl';
};

/**
 * Get text direction for CSS
 */
export const getTextDirection = (): 'ltr' | 'rtl' => {
    return isRTL() ? 'rtl' : 'ltr';
};

/**
 * Get appropriate margin/padding properties for RTL
 */
export const getDirectionalStyle = (property: 'margin' | 'padding', side: 'left' | 'right') => {
    const rtl = isRTL();
    const actualSide = (side === 'left' && rtl) || (side === 'right' && !rtl) ? 'right' : 'left';
    return `${property}-${actualSide}`;
};

export default {
    __,
    _x,
    _n,
    _nx,
    sprintf,
    translations,
    isRTL,
    getTextDirection,
    getDirectionalStyle
};