/**
 * Navigation State Manager for AI Content Agent
 * 
 * Provides persistent navigation state management using URL parameters
 * and localStorage to maintain user context across page refreshes.
 * 
 * @package AI_Content_Agent
 * @version 2.3.8
 * @since 2.3.8
 */

import { View } from '../types';

/**
 * Navigation state interface
 */
interface NavigationState {
    currentView: View;
    settingsSection?: string;
    modalState?: {
        isOpen: boolean;
        type?: string;
        data?: any;
    };
    filters?: Record<string, any>;
    searchQuery?: string;
    pagination?: {
        page: number;
        perPage: number;
    };
}

/**
 * Navigation state manager - MEDIUM PRIORITY FIX
 */
export class NavigationManager {
    private static readonly STORAGE_KEY = 'aca_navigation_state';
    private static readonly URL_PARAM_PREFIX = 'aca_';
    private static listeners: Set<(state: NavigationState) => void> = new Set();
    private static currentState: NavigationState = {
        currentView: 'dashboard'
    };

    /**
     * Initialize navigation manager
     */
    static initialize(): NavigationState {
        // Load state from URL first (higher priority)
        const urlState = this.loadFromURL();
        
        // Load state from localStorage as fallback
        const storageState = this.loadFromStorage();
        
        // Merge states with URL taking precedence
        this.currentState = {
            ...storageState,
            ...urlState
        };

        // Listen for browser navigation events
        window.addEventListener('popstate', this.handlePopState.bind(this));
        
        // Listen for storage changes (multi-tab sync)
        window.addEventListener('storage', this.handleStorageChange.bind(this));

        return this.currentState;
    }

    /**
     * Get current navigation state
     */
    static getState(): NavigationState {
        return { ...this.currentState };
    }

    /**
     * Update navigation state
     */
    static setState(updates: Partial<NavigationState>, options: {
        updateURL?: boolean;
        updateStorage?: boolean;
        notify?: boolean;
    } = {}): void {
        const { 
            updateURL = true, 
            updateStorage = true, 
            notify = true 
        } = options;

        // Update current state
        this.currentState = { ...this.currentState, ...updates };

        // Update URL if requested
        if (updateURL) {
            this.updateURL(this.currentState);
        }

        // Update localStorage if requested
        if (updateStorage) {
            this.saveToStorage(this.currentState);
        }

        // Notify listeners if requested
        if (notify) {
            this.notifyListeners();
        }
    }

    /**
     * Navigate to a specific view
     */
    static navigateTo(view: View, options?: {
        settingsSection?: string;
        replace?: boolean;
        preserveState?: boolean;
    }): void {
        const { settingsSection, replace = false, preserveState = false } = options || {};

        const updates: Partial<NavigationState> = {
            currentView: view
        };

        // Add settings section if navigating to settings
        if (view === 'settings' && settingsSection) {
            updates.settingsSection = settingsSection;
        } else if (view !== 'settings') {
            // Clear settings section when leaving settings
            updates.settingsSection = undefined;
        }

        // Clear modal state when navigating unless preserving state
        if (!preserveState) {
            updates.modalState = { isOpen: false };
        }

        this.setState(updates, {
            updateURL: true,
            updateStorage: true,
            notify: true
        });

        // Use replaceState instead of pushState if requested
        if (replace) {
            const url = this.buildURL(this.currentState);
            window.history.replaceState(this.currentState, '', url);
        }
    }

    /**
     * Open modal with state persistence
     */
    static openModal(type: string, data?: any): void {
        this.setState({
            modalState: {
                isOpen: true,
                type,
                data
            }
        }, {
            updateURL: false, // Don't update URL for modals
            updateStorage: true,
            notify: true
        });
    }

    /**
     * Close modal
     */
    static closeModal(): void {
        this.setState({
            modalState: { isOpen: false }
        }, {
            updateURL: false,
            updateStorage: true,
            notify: true
        });
    }

    /**
     * Update filters
     */
    static updateFilters(filters: Record<string, any>): void {
        this.setState({
            filters: { ...this.currentState.filters, ...filters }
        });
    }

    /**
     * Update search query
     */
    static updateSearch(query: string): void {
        this.setState({
            searchQuery: query
        });
    }

    /**
     * Update pagination
     */
    static updatePagination(page: number, perPage?: number): void {
        this.setState({
            pagination: {
                page,
                perPage: perPage || this.currentState.pagination?.perPage || 10
            }
        });
    }

    /**
     * Subscribe to navigation state changes
     */
    static subscribe(listener: (state: NavigationState) => void): () => void {
        this.listeners.add(listener);
        return () => {
            this.listeners.delete(listener);
        };
    }

    /**
     * Load state from URL parameters
     */
    private static loadFromURL(): Partial<NavigationState> {
        const params = new URLSearchParams(window.location.search);
        const state: Partial<NavigationState> = {};

        // Load view
        const view = params.get(`${this.URL_PARAM_PREFIX}view`) as View;
        if (view && this.isValidView(view)) {
            state.currentView = view;
        }

        // Load settings section
        const section = params.get(`${this.URL_PARAM_PREFIX}section`);
        if (section) {
            state.settingsSection = section;
        }

        // Load search query
        const search = params.get(`${this.URL_PARAM_PREFIX}search`);
        if (search) {
            state.searchQuery = search;
        }

        // Load pagination
        const page = params.get(`${this.URL_PARAM_PREFIX}page`);
        const perPage = params.get(`${this.URL_PARAM_PREFIX}per_page`);
        if (page || perPage) {
            state.pagination = {
                page: page ? parseInt(page, 10) : 1,
                perPage: perPage ? parseInt(perPage, 10) : 10
            };
        }

        return state;
    }

    /**
     * Load state from localStorage
     */
    private static loadFromStorage(): Partial<NavigationState> {
        try {
            const stored = localStorage.getItem(this.STORAGE_KEY);
            if (stored) {
                const parsed = JSON.parse(stored);
                // Validate the stored state
                if (this.isValidState(parsed)) {
                    return parsed;
                }
            }
        } catch (error) {
            console.warn('Failed to load navigation state from storage:', error);
        }
        return {};
    }

    /**
     * Save state to localStorage
     */
    private static saveToStorage(state: NavigationState): void {
        try {
            // Only save persistent state (exclude temporary modal state)
            const persistentState = {
                currentView: state.currentView,
                settingsSection: state.settingsSection,
                filters: state.filters,
                searchQuery: state.searchQuery,
                pagination: state.pagination
            };
            localStorage.setItem(this.STORAGE_KEY, JSON.stringify(persistentState));
        } catch (error) {
            console.warn('Failed to save navigation state to storage:', error);
        }
    }

    /**
     * Update URL with current state
     */
    private static updateURL(state: NavigationState): void {
        const url = this.buildURL(state);
        const currentURL = window.location.pathname + window.location.search;
        
        if (url !== currentURL) {
            window.history.pushState(state, '', url);
        }
    }

    /**
     * Build URL from state
     */
    private static buildURL(state: NavigationState): string {
        const params = new URLSearchParams();
        
        // Add view parameter
        if (state.currentView !== 'dashboard') {
            params.set(`${this.URL_PARAM_PREFIX}view`, state.currentView);
        }

        // Add settings section if in settings view
        if (state.currentView === 'settings' && state.settingsSection) {
            params.set(`${this.URL_PARAM_PREFIX}section`, state.settingsSection);
        }

        // Add search query
        if (state.searchQuery) {
            params.set(`${this.URL_PARAM_PREFIX}search`, state.searchQuery);
        }

        // Add pagination
        if (state.pagination && state.pagination.page > 1) {
            params.set(`${this.URL_PARAM_PREFIX}page`, state.pagination.page.toString());
        }
        if (state.pagination && state.pagination.perPage !== 10) {
            params.set(`${this.URL_PARAM_PREFIX}per_page`, state.pagination.perPage.toString());
        }

        const queryString = params.toString();
        return window.location.pathname + (queryString ? '?' + queryString : '');
    }

    /**
     * Handle browser back/forward navigation
     */
    private static handlePopState(event: PopStateEvent): void {
        if (event.state) {
            this.currentState = event.state;
        } else {
            // Fallback to URL parsing if no state
            this.currentState = { ...this.currentState, ...this.loadFromURL() };
        }
        this.notifyListeners();
    }

    /**
     * Handle localStorage changes (multi-tab sync)
     */
    private static handleStorageChange(event: StorageEvent): void {
        if (event.key === this.STORAGE_KEY && event.newValue) {
            try {
                const newState = JSON.parse(event.newValue);
                if (this.isValidState(newState)) {
                    this.currentState = { ...this.currentState, ...newState };
                    this.notifyListeners();
                }
            } catch (error) {
                console.warn('Failed to parse storage change:', error);
            }
        }
    }

    /**
     * Notify all listeners of state changes
     */
    private static notifyListeners(): void {
        this.listeners.forEach(listener => {
            try {
                listener(this.getState());
            } catch (error) {
                console.error('Navigation listener error:', error);
            }
        });
    }

    /**
     * Validate if a view is valid
     */
    private static isValidView(view: string): view is View {
        const validViews: View[] = [
            'dashboard', 'style-guide', 'ideas', 'drafts', 
            'published', 'settings', 'calendar', 'content-freshness'
        ];
        return validViews.includes(view as View);
    }

    /**
     * Validate navigation state
     */
    private static isValidState(state: any): state is NavigationState {
        return state && 
               typeof state === 'object' && 
               (!state.currentView || this.isValidView(state.currentView));
    }

    /**
     * Clear all navigation state
     */
    static clear(): void {
        localStorage.removeItem(this.STORAGE_KEY);
        this.currentState = { currentView: 'dashboard' };
        this.updateURL(this.currentState);
        this.notifyListeners();
    }

    /**
     * Get URL for a specific view (useful for generating links)
     */
    static getViewURL(view: View, options?: { settingsSection?: string }): string {
        const tempState: NavigationState = {
            currentView: view,
            settingsSection: options?.settingsSection
        };
        return this.buildURL(tempState);
    }
}

/**
 * React hook for using navigation manager
 */
export const useNavigationState = () => {
    const [state, setState] = React.useState<NavigationState>(NavigationManager.getState());

    React.useEffect(() => {
        const unsubscribe = NavigationManager.subscribe(setState);
        return unsubscribe;
    }, []);

    return {
        state,
        navigateTo: NavigationManager.navigateTo,
        openModal: NavigationManager.openModal,
        closeModal: NavigationManager.closeModal,
        updateFilters: NavigationManager.updateFilters,
        updateSearch: NavigationManager.updateSearch,
        updatePagination: NavigationManager.updatePagination,
        getViewURL: NavigationManager.getViewURL,
        clear: NavigationManager.clear
    };
};

/**
 * Initialize navigation manager on module load
 */
if (typeof window !== 'undefined') {
    NavigationManager.initialize();
}

export default NavigationManager;