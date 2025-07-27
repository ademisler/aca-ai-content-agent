import type { StyleGuide, ContentIdea, Draft, AppSettings, ActivityLog } from '../types';

/**
 * WordPress REST API Service
 * Handles all API calls to the WordPress backend
 */
class WordPressApiService {
    private baseUrl: string;
    private nonce: string;

    constructor() {
        this.baseUrl = window.acaData.restUrl;
        this.nonce = window.acaData.nonce;
    }

    /**
     * Make authenticated API request
     */
    private async makeRequest(endpoint: string, options: RequestInit = {}): Promise<any> {
        const url = `${this.baseUrl}${endpoint}`;
        
        const defaultOptions: RequestInit = {
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': this.nonce,
            },
        };

        const mergedOptions = {
            ...defaultOptions,
            ...options,
            headers: {
                ...defaultOptions.headers,
                ...options.headers,
            },
        };

        const response = await fetch(url, mergedOptions);
        
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }

        return response.json();
    }

    // Settings API
    async getSettings(): Promise<AppSettings> {
        return this.makeRequest('settings');
    }

    async updateSettings(settings: AppSettings): Promise<void> {
        await this.makeRequest('settings', {
            method: 'POST',
            body: JSON.stringify(settings),
        });
    }

    // Style Guide API
    async getStyleGuide(): Promise<StyleGuide | null> {
        return this.makeRequest('style-guide');
    }

    async updateStyleGuide(styleGuide: StyleGuide): Promise<void> {
        await this.makeRequest('style-guide', {
            method: 'POST',
            body: JSON.stringify(styleGuide),
        });
    }

    async analyzeStyle(): Promise<StyleGuide> {
        return this.makeRequest('analyze-style', {
            method: 'POST',
        });
    }

    // Ideas API
    async getIdeas(): Promise<ContentIdea[]> {
        return this.makeRequest('ideas');
    }

    async generateIdeas(count: number = 5, isAuto: boolean = false): Promise<ContentIdea[]> {
        return this.makeRequest('ideas', {
            method: 'POST',
            body: JSON.stringify({ count, isAuto }),
        });
    }

    async generateSimilarIdeas(baseTitle: string): Promise<ContentIdea[]> {
        return this.makeRequest('ideas/similar', {
            method: 'POST',
            body: JSON.stringify({ baseTitle }),
        });
    }

    async addManualIdea(title: string): Promise<ContentIdea> {
        return this.makeRequest('ideas/manual', {
            method: 'POST',
            body: JSON.stringify({ title }),
        });
    }

    async updateIdea(id: number, data: Partial<ContentIdea>): Promise<void> {
        await this.makeRequest(`ideas/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data),
        });
    }

    async deleteIdea(id: number): Promise<void> {
        await this.makeRequest(`ideas/${id}`, {
            method: 'DELETE',
        });
    }

    // Posts API
    async getPosts(status?: 'draft' | 'publish'): Promise<Draft[]> {
        const endpoint = status ? `posts?status=${status}` : 'posts';
        return this.makeRequest(endpoint);
    }

    async updatePost(id: number, data: Partial<Draft>): Promise<void> {
        await this.makeRequest(`posts/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data),
        });
    }

    async publishPost(id: number): Promise<void> {
        await this.makeRequest(`posts/${id}/publish`, {
            method: 'POST',
        });
    }

    async schedulePost(id: number, scheduledFor: string): Promise<void> {
        await this.makeRequest(`posts/${id}/schedule`, {
            method: 'POST',
            body: JSON.stringify({ scheduledFor }),
        });
    }

    // Create Draft API
    async createDraft(title: string): Promise<Draft> {
        return this.makeRequest('create-draft', {
            method: 'POST',
            body: JSON.stringify({ title }),
        });
    }

    // Activity Logs API
    async getActivityLogs(): Promise<ActivityLog[]> {
        return this.makeRequest('activity-logs');
    }
}

export const wordpressApiService = new WordPressApiService();