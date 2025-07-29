import '@testing-library/jest-dom';

// Mock WordPress globals
declare global {
  interface Window {
    acaData: {
      nonce: string;
      api_url: string;
      admin_url: string;
      plugin_url: string;
    };
  }
}

// Mock window.acaData for tests
Object.defineProperty(window, 'acaData', {
  value: {
    nonce: 'test-nonce',
    api_url: 'http://localhost/wp-json/aca/v1',
    admin_url: 'http://localhost/wp-admin',
    plugin_url: 'http://localhost/wp-content/plugins/ai-content-agent-plugin',
  },
  writable: true,
});

// Mock fetch for API calls
global.fetch = vi.fn();

// Setup cleanup after each test
afterEach(() => {
  vi.clearAllMocks();
});