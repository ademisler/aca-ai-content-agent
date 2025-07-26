<?php
/**
 * Cache service for ACA AI Content Agent.
 *
 * Provides caching functionality for API responses, database queries,
 * and other expensive operations to improve performance.
 *
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Cache service class for ACA AI Content Agent.
 *
 * Handles caching of API responses, database queries, and other
 * expensive operations to improve performance.
 *
 * @since 1.2.0
 */
class ACA_Cache_Service {

    /**
     * Default cache expiration time in seconds.
     *
     * @since 1.2.0
     * @var int
     */
    const DEFAULT_CACHE_EXPIRATION = 3600; // 1 hour

    /**
     * Cache key prefix for the plugin.
     *
     * @since 1.2.0
     * @var string
     */
    const CACHE_PREFIX = 'aca_cache_';

    /**
     * Get cached data.
     *
     * @since 1.2.0
     * @param string $key The cache key.
     * @return mixed|false The cached data or false if not found.
     */
    public static function get($key) {
        $cache_key = self::CACHE_PREFIX . sanitize_key($key);
        return get_transient($cache_key);
    }

    /**
     * Set cached data.
     *
     * @since 1.2.0
     * @param string $key The cache key.
     * @param mixed $data The data to cache.
     * @param int $expiration Optional. Cache expiration time in seconds.
     * @return bool True on success, false on failure.
     */
    public static function set($key, $data, $expiration = null) {
        $cache_key = self::CACHE_PREFIX . sanitize_key($key);
        $expiration = $expiration ?: self::DEFAULT_CACHE_EXPIRATION;
        
        return set_transient($cache_key, $data, $expiration);
    }

    /**
     * Delete cached data.
     *
     * @since 1.2.0
     * @param string $key The cache key.
     * @return bool True on success, false on failure.
     */
    public static function delete($key) {
        $cache_key = self::CACHE_PREFIX . sanitize_key($key);
        return delete_transient($cache_key);
    }

    /**
     * Clear all plugin cache.
     *
     * @since 1.2.0
     * @return bool True on success, false on failure.
     */
    public static function clear_all() {
        global $wpdb;
        
        // Delete all transients with our prefix
        $wpdb->query($wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
            '_transient_' . self::CACHE_PREFIX . '%',
            '_transient_timeout_' . self::CACHE_PREFIX . '%'
        ));
        
        return true;
    }

    /**
     * Get or set cached data (cache-aside pattern).
     *
     * @since 1.2.0
     * @param string $key The cache key.
     * @param callable $callback The callback to generate data if not cached.
     * @param int $expiration Optional. Cache expiration time in seconds.
     * @return mixed The cached or generated data.
     */
    public static function remember($key, $callback, $expiration = null) {
        $cached = self::get($key);
        
        if ($cached !== false) {
            return $cached;
        }
        
        $data = call_user_func($callback);
        self::set($key, $data, $expiration);
        
        return $data;
    }

    /**
     * Cache API response with intelligent expiration.
     *
     * @since 1.2.0
     * @param string $endpoint The API endpoint.
     * @param array $params The API parameters.
     * @param callable $callback The callback to make the API call.
     * @return mixed The API response.
     */
    public static function cache_api_response($endpoint, $params, $callback) {
        $cache_key = 'api_' . md5($endpoint . serialize($params));
        
        return self::remember($cache_key, $callback, 1800); // 30 minutes for API responses
    }

    /**
     * Cache database query results.
     *
     * @since 1.2.0
     * @param string $query_hash The query hash.
     * @param callable $callback The callback to execute the query.
     * @param int $expiration Optional. Cache expiration time in seconds.
     * @return mixed The query results.
     */
    public static function cache_query($query_hash, $callback, $expiration = null) {
        $cache_key = 'query_' . md5($query_hash);
        $expiration = $expiration ?: 900; // 15 minutes for database queries
        
        return self::remember($cache_key, $callback, $expiration);
    }

    /**
     * Invalidate cache by pattern.
     *
     * @since 1.2.0
     * @param string $pattern The pattern to match cache keys.
     * @return bool True on success, false on failure.
     */
    public static function invalidate_pattern($pattern) {
        global $wpdb;
        
        $wpdb->query($wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
            '_transient_' . self::CACHE_PREFIX . $pattern . '%',
            '_transient_timeout_' . self::CACHE_PREFIX . $pattern . '%'
        ));
        
        return true;
    }
} 