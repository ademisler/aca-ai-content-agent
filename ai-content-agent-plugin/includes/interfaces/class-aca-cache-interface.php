<?php
/**
 * Cache Interface for AI Content Agent
 * 
 * @package AI_Content_Agent
 * @version 2.3.14
 * @since 2.3.14
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Cache Interface
 */
interface ACA_Cache_Interface {
    
    /**
     * Get cached item
     * 
     * @param string $key
     * @return mixed|null
     */
    public static function get($key);
    
    /**
     * Set cached item
     * 
     * @param string $key
     * @param mixed $value
     * @param int $ttl Time to live in seconds
     * @return bool
     */
    public static function set($key, $value, $ttl = 300);
    
    /**
     * Delete cached item
     * 
     * @param string $key
     * @return bool
     */
    public static function delete($key);
    
    /**
     * Clear all cache
     * 
     * @return bool
     */
    public static function clear();
    
    /**
     * Check if cache is enabled
     * 
     * @return bool
     */
    public static function is_enabled();
}
