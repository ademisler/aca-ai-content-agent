<?php
/**
 * Performance optimization utilities for the AI Content Agent plugin
 * Handles database query optimization, caching layer, asset loading optimization, and memory usage reduction
 */

if (!defined('ABSPATH')) {
    exit;
}

class ACA_Performance {
    
    private static $cache_group = 'aca_cache';
    private static $cache_expiration = 3600; // 1 hour default
    private static $query_cache = array();
    
    /**
     * Initialize performance optimizations
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'setup_caching'));
        add_action('wp_enqueue_scripts', array(__CLASS__, 'optimize_asset_loading'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'optimize_admin_assets'));
        add_filter('posts_clauses', array(__CLASS__, 'optimize_post_queries'), 10, 2);
        add_action('wp_footer', array(__CLASS__, 'cleanup_memory'));
        add_action('admin_footer', array(__CLASS__, 'cleanup_memory'));
        
        // Database optimization hooks
        add_action('aca_cleanup_expired_cache', array(__CLASS__, 'cleanup_expired_cache'));
        add_action('aca_optimize_database', array(__CLASS__, 'optimize_database_tables'));
        
        // Schedule cleanup tasks
        if (!wp_next_scheduled('aca_cleanup_expired_cache')) {
            wp_schedule_event(time(), 'daily', 'aca_cleanup_expired_cache');
        }
        
        if (!wp_next_scheduled('aca_optimize_database')) {
            wp_schedule_event(time(), 'weekly', 'aca_optimize_database');
        }
    }
    
    /**
     * Setup advanced caching layer
     */
    public static function setup_caching() {
        // Use object cache if available, fallback to transients
        if (function_exists('wp_cache_init') && !defined('WP_CACHE_DISABLED')) {
            // Object cache is available
            self::$cache_group = 'aca_object_cache';
        }
        
        // Setup cache groups
        wp_cache_add_global_groups(array(self::$cache_group));
    }
    
    /**
     * Advanced caching with multiple layers
     */
    public static function get_cache($key, $default = null) {
        $cache_key = self::get_cache_key($key);
        
        // Try object cache first
        if (function_exists('wp_cache_get')) {
            $cached = wp_cache_get($cache_key, self::$cache_group);
            if ($cached !== false) {
                return $cached;
            }
        }
        
        // Fallback to transient cache
        $cached = get_transient($cache_key);
        if ($cached !== false) {
            // Store in object cache for faster access
            if (function_exists('wp_cache_set')) {
                wp_cache_set($cache_key, $cached, self::$cache_group, self::$cache_expiration);
            }
            return $cached;
        }
        
        return $default;
    }
    
    /**
     * Set cache with multiple layers
     */
    public static function set_cache($key, $value, $expiration = null) {
        if ($expiration === null) {
            $expiration = self::$cache_expiration;
        }
        
        $cache_key = self::get_cache_key($key);
        
        // Set in object cache
        if (function_exists('wp_cache_set')) {
            wp_cache_set($cache_key, $value, self::$cache_group, $expiration);
        }
        
        // Set in transient cache as backup
        set_transient($cache_key, $value, $expiration);
        
        return true;
    }
    
    /**
     * Delete cache entry
     */
    public static function delete_cache($key) {
        $cache_key = self::get_cache_key($key);
        
        // Delete from object cache
        if (function_exists('wp_cache_delete')) {
            wp_cache_delete($cache_key, self::$cache_group);
        }
        
        // Delete from transient cache
        delete_transient($cache_key);
        
        return true;
    }
    
    /**
     * Flush all cache
     */
    public static function flush_cache() {
        // Flush object cache group
        if (function_exists('wp_cache_flush_group')) {
            wp_cache_flush_group(self::$cache_group);
        }
        
        // Clean up transients
        global $wpdb;
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
                '_transient_aca_%'
            )
        );
        
        return true;
    }
    
    /**
     * Generate cache key with prefix
     */
    private static function get_cache_key($key) {
        return 'aca_' . md5($key);
    }
    
    /**
     * Optimized database queries with caching
     */
    public static function get_cached_query($sql, $cache_key = null, $expiration = null) {
        if ($cache_key === null) {
            $cache_key = 'query_' . md5($sql);
        }
        
        // Check cache first
        $cached_result = self::get_cache($cache_key);
        if ($cached_result !== null) {
            return $cached_result;
        }
        
        // Execute query
        global $wpdb;
        $result = $wpdb->get_results($sql);
        
        // Cache the result
        if (!is_wp_error($result)) {
            self::set_cache($cache_key, $result, $expiration);
        }
        
        return $result;
    }
    
    /**
     * Optimize post queries
     */
    public static function optimize_post_queries($clauses, $query) {
        // Only optimize our plugin queries
        if (!isset($query->query_vars['aca_optimize']) || !$query->query_vars['aca_optimize']) {
            return $clauses;
        }
        
        // Add query caching
        $cache_key = 'post_query_' . md5(serialize($clauses));
        $cached_posts = self::get_cache($cache_key);
        
        if ($cached_posts !== null) {
            // Return cached result by modifying the WHERE clause to match cached IDs
            if (!empty($cached_posts)) {
                $post_ids = wp_list_pluck($cached_posts, 'ID');
                $clauses['where'] .= " AND {$query->query_vars['wpdb']->posts}.ID IN (" . implode(',', array_map('intval', $post_ids)) . ")";
            }
        }
        
        return $clauses;
    }
    
    /**
     * Optimize database tables
     */
    public static function optimize_database_tables() {
        global $wpdb;
        
        // Get ACA plugin tables
        $tables = array(
            $wpdb->prefix . 'aca_ideas',
            $wpdb->prefix . 'aca_drafts',
            $wpdb->prefix . 'aca_activity_logs',
            $wpdb->prefix . 'aca_security_logs'
        );
        
        foreach ($tables as $table) {
            // Check if table exists
            $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table));
            
            if ($table_exists) {
                // Optimize table
                $wpdb->query("OPTIMIZE TABLE $table");
                
                // Add indexes if they don't exist
                self::add_missing_indexes($table);
                
                // Clean up old records (older than 6 months for logs)
                if (strpos($table, '_logs') !== false) {
                    $wpdb->query($wpdb->prepare(
                        "DELETE FROM $table WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH)"
                    ));
                }
            }
        }
        
        // Update option to track last optimization
        update_option('aca_last_db_optimization', current_time('mysql'));
    }
    
    /**
     * Add missing database indexes for performance
     */
    private static function add_missing_indexes($table) {
        global $wpdb;
        
        $indexes = array();
        
        switch ($table) {
            case $wpdb->prefix . 'aca_ideas':
                $indexes = array(
                    'status_idx' => 'status',
                    'created_at_idx' => 'created_at',
                    'status_created_idx' => 'status, created_at'
                );
                break;
                
            case $wpdb->prefix . 'aca_drafts':
                $indexes = array(
                    'status_idx' => 'status',
                    'created_at_idx' => 'created_at',
                    'post_id_idx' => 'post_id'
                );
                break;
                
            case $wpdb->prefix . 'aca_activity_logs':
                $indexes = array(
                    'type_idx' => 'type',
                    'created_at_idx' => 'created_at',
                    'type_created_idx' => 'type, created_at'
                );
                break;
                
            case $wpdb->prefix . 'aca_security_logs':
                $indexes = array(
                    'event_type_idx' => 'event_type',
                    'created_at_idx' => 'created_at',
                    'user_id_idx' => 'user_id',
                    'ip_address_idx' => 'ip_address'
                );
                break;
        }
        
        foreach ($indexes as $index_name => $columns) {
            // Check if index exists
            $index_exists = $wpdb->get_var($wpdb->prepare(
                "SHOW INDEX FROM $table WHERE Key_name = %s",
                $index_name
            ));
            
            if (!$index_exists) {
                $wpdb->query("ALTER TABLE $table ADD INDEX $index_name ($columns)");
            }
        }
    }
    
    /**
     * Optimize asset loading
     */
    public static function optimize_asset_loading() {
        // Only load assets on plugin pages
        if (!self::is_plugin_page()) {
            return;
        }
        
        // Preload critical resources
        self::preload_critical_resources();
        
        // Defer non-critical scripts
        add_filter('script_loader_tag', array(__CLASS__, 'defer_non_critical_scripts'), 10, 2);
        
        // Optimize CSS delivery
        add_filter('style_loader_tag', array(__CLASS__, 'optimize_css_delivery'), 10, 2);
    }
    
    /**
     * Optimize admin assets
     */
    public static function optimize_admin_assets($hook) {
        // Only load on our admin pages
        if (strpos($hook, 'ai-content-agent') === false) {
            return;
        }
        
        // Remove unnecessary admin scripts on our pages
        wp_dequeue_script('wp-color-picker');
        wp_dequeue_script('iris');
        wp_dequeue_script('wp-theme-plugin-editor');
        
        // Optimize our main script loading
        wp_script_add_data('aca-admin', 'async', true);
        
        // Add resource hints
        add_action('admin_head', array(__CLASS__, 'add_admin_resource_hints'));
    }
    
    /**
     * Preload critical resources
     */
    private static function preload_critical_resources() {
        $critical_resources = array(
            plugins_url('admin/assets/index.css', dirname(__FILE__)),
            plugins_url('admin/assets/index.js', dirname(__FILE__))
        );
        
        foreach ($critical_resources as $resource) {
            $file_type = pathinfo($resource, PATHINFO_EXTENSION);
            $as = ($file_type === 'css') ? 'style' : 'script';
            
            echo "<link rel='preload' href='$resource' as='$as'>\n";
        }
    }
    
    /**
     * Add resource hints for admin
     */
    public static function add_admin_resource_hints() {
        // DNS prefetch for external resources
        echo "<link rel='dns-prefetch' href='//generativelanguage.googleapis.com'>\n";
        echo "<link rel='dns-prefetch' href='//console.cloud.google.com'>\n";
        
        // Preconnect to critical origins
        echo "<link rel='preconnect' href='//generativelanguage.googleapis.com' crossorigin>\n";
    }
    
    /**
     * Defer non-critical scripts
     */
    public static function defer_non_critical_scripts($tag, $handle) {
        // List of scripts that can be deferred
        $defer_scripts = array(
            'aca-analytics',
            'aca-tracking',
            'aca-non-critical'
        );
        
        if (in_array($handle, $defer_scripts)) {
            return str_replace(' src', ' defer src', $tag);
        }
        
        return $tag;
    }
    
    /**
     * Optimize CSS delivery
     */
    public static function optimize_css_delivery($tag, $handle) {
        // List of non-critical CSS that can be loaded asynchronously
        $async_styles = array(
            'aca-admin-extras',
            'aca-animations'
        );
        
        if (in_array($handle, $async_styles)) {
            return str_replace("rel='stylesheet'", "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"", $tag);
        }
        
        return $tag;
    }
    
    /**
     * Memory usage optimization
     */
    public static function cleanup_memory() {
        // Clear query cache
        self::$query_cache = array();
        
        // Force garbage collection
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
        
        // Clear WordPress object cache if needed
        if (function_exists('wp_cache_flush') && self::should_flush_cache()) {
            wp_cache_flush();
        }
    }
    
    /**
     * Check if we should flush cache based on memory usage
     */
    private static function should_flush_cache() {
        $memory_limit = self::get_memory_limit();
        $current_usage = memory_get_usage(true);
        
        // Flush if using more than 80% of memory limit
        return ($current_usage / $memory_limit) > 0.8;
    }
    
    /**
     * Get PHP memory limit in bytes
     */
    private static function get_memory_limit() {
        $memory_limit = ini_get('memory_limit');
        
        if (preg_match('/^(\d+)(.)$/', $memory_limit, $matches)) {
            $number = (int) $matches[1];
            $suffix = strtoupper($matches[2]);
            
            switch ($suffix) {
                case 'K':
                    return $number * 1024;
                case 'M':
                    return $number * 1024 * 1024;
                case 'G':
                    return $number * 1024 * 1024 * 1024;
                default:
                    return $number;
            }
        }
        
        return 128 * 1024 * 1024; // Default 128MB
    }
    
    /**
     * Check if current page is a plugin page
     */
    private static function is_plugin_page() {
        return isset($_GET['page']) && strpos($_GET['page'], 'ai-content-agent') !== false;
    }
    
    /**
     * Cleanup expired cache entries
     */
    public static function cleanup_expired_cache() {
        global $wpdb;
        
        // Clean up expired transients
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
             WHERE option_name LIKE '_transient_timeout_aca_%' 
             AND option_value < UNIX_TIMESTAMP()"
        );
        
        // Clean up the corresponding transient data
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
             WHERE option_name LIKE '_transient_aca_%' 
             AND option_name NOT IN (
                 SELECT REPLACE(option_name, '_timeout', '') 
                 FROM {$wpdb->options} 
                 WHERE option_name LIKE '_transient_timeout_aca_%'
             )"
        );
        
        // Update cleanup timestamp
        update_option('aca_last_cache_cleanup', current_time('mysql'));
    }
    
    /**
     * Get performance statistics
     */
    public static function get_performance_stats() {
        return array(
            'memory_usage' => array(
                'current' => memory_get_usage(true),
                'peak' => memory_get_peak_usage(true),
                'limit' => self::get_memory_limit(),
                'percentage' => round((memory_get_usage(true) / self::get_memory_limit()) * 100, 2)
            ),
            'cache_stats' => array(
                'object_cache_enabled' => function_exists('wp_cache_get'),
                'last_cleanup' => get_option('aca_last_cache_cleanup'),
                'cache_size' => self::estimate_cache_size()
            ),
            'database_stats' => array(
                'last_optimization' => get_option('aca_last_db_optimization'),
                'query_count' => get_num_queries(),
                'query_time' => self::get_total_query_time()
            )
        );
    }
    
    /**
     * Estimate cache size
     */
    private static function estimate_cache_size() {
        global $wpdb;
        
        $size = $wpdb->get_var(
            "SELECT SUM(LENGTH(option_value)) 
             FROM {$wpdb->options} 
             WHERE option_name LIKE '_transient_aca_%'"
        );
        
        return $size ? intval($size) : 0;
    }
    
    /**
     * Get total query time (if query logging is enabled)
     */
    private static function get_total_query_time() {
        if (defined('SAVEQUERIES') && SAVEQUERIES) {
            global $wpdb;
            $total_time = 0;
            
            foreach ($wpdb->queries as $query) {
                $total_time += $query[1];
            }
            
            return round($total_time, 4);
        }
        
        return null;
    }
    
    /**
     * Asset optimization utilities
     */
    public static function optimize_images() {
        // Add WebP support detection
        add_filter('wp_generate_attachment_metadata', array(__CLASS__, 'generate_webp_versions'));
        
        // Lazy load images
        add_filter('the_content', array(__CLASS__, 'add_lazy_loading_to_images'));
        add_filter('post_thumbnail_html', array(__CLASS__, 'add_lazy_loading_to_thumbnails'));
    }
    
    /**
     * Generate WebP versions of images
     */
    public static function generate_webp_versions($metadata) {
        if (!function_exists('imagewebp')) {
            return $metadata;
        }
        
        $upload_dir = wp_upload_dir();
        $file_path = $upload_dir['basedir'] . '/' . $metadata['file'];
        
        if (file_exists($file_path)) {
            $webp_path = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $file_path);
            
            // Create WebP version
            $image = wp_get_image_editor($file_path);
            if (!is_wp_error($image)) {
                $image->save($webp_path, 'image/webp');
            }
        }
        
        return $metadata;
    }
    
    /**
     * Add lazy loading to images in content
     */
    public static function add_lazy_loading_to_images($content) {
        // Add loading="lazy" to images
        $content = preg_replace('/<img([^>]+?)src=/i', '<img$1loading="lazy" src=', $content);
        
        return $content;
    }
    
    /**
     * Add lazy loading to post thumbnails
     */
    public static function add_lazy_loading_to_thumbnails($html) {
        return str_replace('<img', '<img loading="lazy"', $html);
    }
}

// Initialize performance optimizations
ACA_Performance::init();