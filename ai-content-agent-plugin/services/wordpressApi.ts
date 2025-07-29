/**
 * WordPress REST API wrapper for ACA plugin
 */

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

/**
 * Make authenticated API requests to WordPress REST API
 */
export async function apiFetch(path: string, options: RequestInit = {}): Promise<any> {
  // Check if aca_object is available
  if (!window.aca_object) {
    console.error('ACA Error: window.aca_object is not defined. Plugin scripts may not be loaded correctly.');
    throw new Error('Plugin configuration not loaded. Please refresh the page.');
  }

  const headers = {
    'X-WP-Nonce': window.aca_object.nonce,
    'Content-Type': 'application/json',
    ...options.headers,
  };

  const response = await fetch(`${window.aca_object.api_url}${path}`, {
    ...options,
    headers,
  });

  if (!response.ok) {
    let errorMessage = `HTTP error! status: ${response.status}`;
    
    try {
      const contentType = response.headers.get('content-type');
      if (contentType && contentType.includes('application/json')) {
        const errorData = await response.json();
        errorMessage = errorData.message || errorMessage;
      } else {
        // If response is HTML (WordPress critical error), extract meaningful part
        const htmlText = await response.text();
        if (htmlText.includes('critical error')) {
          errorMessage = 'WordPress critical error occurred. Check server logs.';
        } else {
          errorMessage = `Server returned non-JSON response: ${response.status}`;
        }
      }
    } catch (parseError) {
      errorMessage = `Failed to parse error response: ${response.status}`;
    }
    
    throw new Error(errorMessage);
  }

  return response.json();
}

// Settings API
export const settingsApi = {
  get: () => apiFetch('settings'),
  save: (settings: any) => apiFetch('settings', {
    method: 'POST',
    body: JSON.stringify(settings),
  }),
  update: (settings: any) => apiFetch('settings', {
    method: 'POST',
    body: JSON.stringify(settings),
  }),
};

// Style Guide API
export const styleGuideApi = {
  get: () => apiFetch('style-guide'),
  analyze: () => apiFetch('style-guide/analyze', { method: 'POST' }),
  save: (styleGuide: any) => apiFetch('style-guide', {
    method: 'POST',
    body: JSON.stringify(styleGuide),
  }),
  update: (styleGuide: any) => apiFetch('style-guide', {
    method: 'POST',
    body: JSON.stringify(styleGuide),
  }),
};

// Ideas API
export const ideasApi = {
  get: () => apiFetch('ideas'),
  generate: (count: number = 5) => apiFetch('ideas/generate', {
    method: 'POST',
    body: JSON.stringify({ count }),
  }),
  generateSimilar: (ideaId: number) => apiFetch('ideas/similar', {
    method: 'POST',
    body: JSON.stringify({ ideaId }),
  }),
  create: (idea: any) => apiFetch('ideas', {
    method: 'POST',
    body: JSON.stringify(idea),
  }),
  update: (id: number, updates: any) => apiFetch(`ideas/${id}`, {
    method: 'PUT',
    body: JSON.stringify(updates),
  }),
  delete: (id: number) => apiFetch(`ideas/${id}`, { method: 'DELETE' }),
};

// Drafts API
export const draftsApi = {
  get: () => apiFetch('drafts'),
  createFromIdea: (ideaId: number) => apiFetch('drafts/create', {
    method: 'POST',
    body: JSON.stringify({ ideaId }),
  }),
  update: (id: number, updates: any) => apiFetch(`drafts/${id}`, {
    method: 'PUT',
    body: JSON.stringify(updates),
  }),
  schedule: (id: number, scheduledDate: string) => apiFetch(`drafts/${id}/schedule`, {
    method: 'POST',
    body: JSON.stringify({ scheduledDate }),
  }),
};

// Published posts API
export const publishedApi = {
  get: () => apiFetch('published'),
  publish: (draftId: number) => apiFetch(`drafts/${draftId}/publish`, { 
    method: 'POST' 
  }),
};

// Activity logs API
export const activityApi = {
  get: () => apiFetch('activity-logs'),
  create: (log: any) => apiFetch('activity-logs', {
    method: 'POST',
    body: JSON.stringify(log),
  }),
};