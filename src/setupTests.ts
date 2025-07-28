import '@testing-library/jest-dom';

// Mock WordPress global object
declare global {
    interface Window {
        aca_object: {
            nonce: string;
            api_url: string;
            admin_url: string;
            plugin_url: string;
        };
    }
}

// Set up WordPress mock
window.aca_object = {
    nonce: 'mock-nonce',
    api_url: 'http://localhost/wp-json/aca/v1/',
    admin_url: 'http://localhost/wp-admin/',
    plugin_url: 'http://localhost/wp-content/plugins/ai-content-agent/',
};

// Mock fetch for API calls
global.fetch = jest.fn();

// Mock console methods to reduce noise in tests
const originalError = console.error;
beforeAll(() => {
    console.error = (...args: any[]) => {
        if (
            typeof args[0] === 'string' &&
            args[0].includes('Warning: ReactDOM.render is deprecated')
        ) {
            return;
        }
        originalError.call(console, ...args);
    };
});

afterAll(() => {
    console.error = originalError;
});

// Clean up after each test
afterEach(() => {
    jest.clearAllMocks();
});

// Mock IntersectionObserver
(global as any).IntersectionObserver = class IntersectionObserver {
    root: Element | null = null;
    rootMargin: string = '';
    thresholds: ReadonlyArray<number> = [];
    
    constructor() {}
    observe() {
        return null;
    }
    disconnect() {
        return null;
    }
    unobserve() {
        return null;
    }
    takeRecords(): IntersectionObserverEntry[] {
        return [];
    }
};

// Mock ResizeObserver
global.ResizeObserver = class ResizeObserver {
    constructor() {}
    observe() {
        return null;
    }
    disconnect() {
        return null;
    }
    unobserve() {
        return null;
    }
};