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
    const errorData = await response.json().catch(() => ({ message: 'Network error' }));
    throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
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
};

// Style Guide API
export const styleGuideApi = {
  get: () => apiFetch('style-guide'),
  analyze: () => apiFetch('style-guide/analyze', { method: 'POST' }),
  save: (styleGuide: any) => apiFetch('style-guide', {
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
  generateSimilar: (baseTitle: string) => apiFetch('ideas/similar', {
    method: 'POST',
    body: JSON.stringify({ baseTitle }),
  }),
  add: (title: string) => apiFetch('ideas', {
    method: 'POST',
    body: JSON.stringify({ title }),
  }),
  update: (id: number, title: string) => apiFetch(`ideas/${id}`, {
    method: 'PUT',
    body: JSON.stringify({ title }),
  }),
  delete: (id: number) => apiFetch(`ideas/${id}`, { method: 'DELETE' }),
};

// Drafts API
export const draftsApi = {
  get: () => apiFetch('drafts'),
  create: (ideaId: number) => apiFetch('drafts/create', {
    method: 'POST',
    body: JSON.stringify({ ideaId }),
  }),
  update: (id: number, updates: any) => apiFetch(`drafts/${id}`, {
    method: 'PUT',
    body: JSON.stringify(updates),
  }),
  publish: (id: number) => apiFetch(`drafts/${id}/publish`, { method: 'POST' }),
  schedule: (id: number, date: string) => apiFetch(`drafts/${id}/schedule`, {
    method: 'POST',
    body: JSON.stringify({ date }),
  }),
};

// Published posts API
export const publishedApi = {
  get: () => apiFetch('published'),
};

// Activity logs API
export const activityApi = {
  get: () => apiFetch('activity-logs'),
};