/**
 * Optimized Data Loader for AI Content Agent
 * 
 * Eliminates redundant API calls and implements intelligent caching
 * to improve page load performance and reduce server load.
 * 
 * @package AI_Content_Agent
 * @version 2.3.8
 * @since 2.3.8
 */

import { settingsApi, styleGuideApi, ideasApi, draftsApi, publishedApi, activityApi, contentFreshnessApi } from '../services/wordpressApi';

/**
 * Cache interface for storing API responses
 */
interface CacheEntry<T> {
    data: T;
    timestamp: number;
    expiry: number;
}

/**
 * Data loader cache with TTL (Time To Live)
 */
class DataCache {
    private cache = new Map<string, CacheEntry<any>>();
    private readonly DEFAULT_TTL = 5 * 60 * 1000; // 5 minutes

    set<T>(key: string, data: T, ttl: number = this.DEFAULT_TTL): void {
        this.cache.set(key, {
            data,
            timestamp: Date.now(),
            expiry: Date.now() + ttl
        });
    }

    get<T>(key: string): T | null {
        const entry = this.cache.get(key);
        if (!entry) return null;
        
        if (Date.now() > entry.expiry) {
            this.cache.delete(key);
            return null;
        }
        
        return entry.data as T;
    }

    has(key: string): boolean {
        const entry = this.cache.get(key);
        if (!entry) return false;
        
        if (Date.now() > entry.expiry) {
            this.cache.delete(key);
            return false;
        }
        
        return true;
    }

    clear(): void {
        this.cache.clear();
    }

    invalidate(pattern: string): void {
        for (const key of this.cache.keys()) {
            if (key.includes(pattern)) {
                this.cache.delete(key);
            }
        }
    }
}

/**
 * Singleton data cache instance
 */
const dataCache = new DataCache();

/**
 * Request deduplication - prevents multiple identical requests
 */
class RequestDeduplicator {
    private pendingRequests = new Map<string, Promise<any>>();

    async dedupe<T>(key: string, requestFn: () => Promise<T>): Promise<T> {
        if (this.pendingRequests.has(key)) {
            return this.pendingRequests.get(key);
        }

        const promise = requestFn().finally(() => {
            this.pendingRequests.delete(key);
        });

        this.pendingRequests.set(key, promise);
        return promise;
    }
}

const requestDeduplicator = new RequestDeduplicator();

/**
 * Optimized data loader with caching and deduplication
 */
export class OptimizedDataLoader {
    
    /**
     * Load settings with caching
     */
    static async loadSettings(forceRefresh = false): Promise<any> {
        const cacheKey = 'settings';
        
        if (!forceRefresh && dataCache.has(cacheKey)) {
            return dataCache.get(cacheKey);
        }

        return requestDeduplicator.dedupe(cacheKey, async () => {
            const data = await settingsApi.get();
            dataCache.set(cacheKey, data, 10 * 60 * 1000); // 10 minutes TTL for settings
            return data;
        });
    }

    /**
     * Load style guide with caching
     */
    static async loadStyleGuide(forceRefresh = false): Promise<any> {
        const cacheKey = 'style_guide';
        
        if (!forceRefresh && dataCache.has(cacheKey)) {
            return dataCache.get(cacheKey);
        }

        return requestDeduplicator.dedupe(cacheKey, async () => {
            const data = await styleGuideApi.get();
            dataCache.set(cacheKey, data, 15 * 60 * 1000); // 15 minutes TTL
            return data;
        });
    }

    /**
     * Load ideas with caching
     */
    static async loadIdeas(forceRefresh = false): Promise<any> {
        const cacheKey = 'ideas';
        
        if (!forceRefresh && dataCache.has(cacheKey)) {
            return dataCache.get(cacheKey);
        }

        return requestDeduplicator.dedupe(cacheKey, async () => {
            const data = await ideasApi.get();
            dataCache.set(cacheKey, data, 2 * 60 * 1000); // 2 minutes TTL for dynamic content
            return data;
        });
    }

    /**
     * Load drafts with caching
     */
    static async loadDrafts(forceRefresh = false): Promise<any> {
        const cacheKey = 'drafts';
        
        if (!forceRefresh && dataCache.has(cacheKey)) {
            return dataCache.get(cacheKey);
        }

        return requestDeduplicator.dedupe(cacheKey, async () => {
            const data = await draftsApi.get();
            dataCache.set(cacheKey, data, 1 * 60 * 1000); // 1 minute TTL for frequently changing content
            return data;
        });
    }

    /**
     * Load published posts with caching
     */
    static async loadPublished(forceRefresh = false): Promise<any> {
        const cacheKey = 'published';
        
        if (!forceRefresh && dataCache.has(cacheKey)) {
            return dataCache.get(cacheKey);
        }

        return requestDeduplicator.dedupe(cacheKey, async () => {
            const data = await publishedApi.get();
            dataCache.set(cacheKey, data, 5 * 60 * 1000); // 5 minutes TTL
            return data;
        });
    }

    /**
     * Load activity logs with caching
     */
    static async loadActivityLogs(forceRefresh = false): Promise<any> {
        const cacheKey = 'activity_logs';
        
        if (!forceRefresh && dataCache.has(cacheKey)) {
            return dataCache.get(cacheKey);
        }

        return requestDeduplicator.dedupe(cacheKey, async () => {
            const data = await activityApi.get();
            dataCache.set(cacheKey, data, 30 * 1000); // 30 seconds TTL for activity logs
            return data;
        });
    }

    /**
     * Load content freshness data with caching
     */
    static async loadContentFreshness(forceRefresh = false): Promise<any> {
        const cacheKey = 'content_freshness';
        
        if (!forceRefresh && dataCache.has(cacheKey)) {
            return dataCache.get(cacheKey);
        }

        return requestDeduplicator.dedupe(cacheKey, async () => {
            try {
                const data = await contentFreshnessApi.getPosts(50, 'all');
                dataCache.set(cacheKey, data, 5 * 60 * 1000); // 5 minutes TTL
                return data;
            } catch (error) {
                // Content freshness is a Pro feature, return null if not available
                return null;
            }
        });
    }

    /**
     * Load all initial data efficiently - HIGH PRIORITY OPTIMIZATION
     */
    static async loadAllInitialData(forceRefresh = false): Promise<{
        settings: any;
        styleGuide: any;
        ideas: any;
        drafts: any;
        published: any;
        activityLogs: any;
        contentFreshness: any;
    }> {
        // Use Promise.allSettled to prevent one failure from breaking everything
        const [
            settingsResult,
            styleGuideResult,
            ideasResult,
            draftsResult,
            publishedResult,
            activityResult,
            freshnessResult
        ] = await Promise.allSettled([
            this.loadSettings(forceRefresh),
            this.loadStyleGuide(forceRefresh),
            this.loadIdeas(forceRefresh),
            this.loadDrafts(forceRefresh),
            this.loadPublished(forceRefresh),
            this.loadActivityLogs(forceRefresh),
            this.loadContentFreshness(forceRefresh)
        ]);

        return {
            settings: settingsResult.status === 'fulfilled' ? settingsResult.value : null,
            styleGuide: styleGuideResult.status === 'fulfilled' ? styleGuideResult.value : null,
            ideas: ideasResult.status === 'fulfilled' ? ideasResult.value : [],
            drafts: draftsResult.status === 'fulfilled' ? draftsResult.value : [],
            published: publishedResult.status === 'fulfilled' ? publishedResult.value : [],
            activityLogs: activityResult.status === 'fulfilled' ? activityResult.value : [],
            contentFreshness: freshnessResult.status === 'fulfilled' ? freshnessResult.value : null
        };
    }

    /**
     * Invalidate cache for specific data type
     */
    static invalidateCache(type: string): void {
        dataCache.invalidate(type);
    }

    /**
     * Clear all cached data
     */
    static clearCache(): void {
        dataCache.clear();
    }

    /**
     * Get cache statistics for debugging
     */
    static getCacheStats(): { totalEntries: number; hitRate: number } {
        // This would require tracking hits/misses, simplified for now
        return {
            totalEntries: (dataCache as any).cache.size,
            hitRate: 0.85 // Placeholder, would track actual hit rate
        };
    }
}

/**
 * Hook for React components to use optimized data loading
 */
export function useOptimizedDataLoader() {
    return {
        loadSettings: OptimizedDataLoader.loadSettings,
        loadStyleGuide: OptimizedDataLoader.loadStyleGuide,
        loadIdeas: OptimizedDataLoader.loadIdeas,
        loadDrafts: OptimizedDataLoader.loadDrafts,
        loadPublished: OptimizedDataLoader.loadPublished,
        loadActivityLogs: OptimizedDataLoader.loadActivityLogs,
        loadContentFreshness: OptimizedDataLoader.loadContentFreshness,
        loadAllInitialData: OptimizedDataLoader.loadAllInitialData,
        invalidateCache: OptimizedDataLoader.invalidateCache,
        clearCache: OptimizedDataLoader.clearCache,
        getCacheStats: OptimizedDataLoader.getCacheStats
    };
}

export default OptimizedDataLoader;