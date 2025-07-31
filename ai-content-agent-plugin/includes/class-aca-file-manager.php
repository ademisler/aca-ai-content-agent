<?php
/**
 * File Operations Manager for AI Content Agent
 * 
 * Provides optimized file handling with caching, lazy loading,
 * and performance monitoring capabilities.
 * 
 * @package AI_Content_Agent
 * @version 2.3.14
 * @since 2.3.14
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Optimized File Manager
 */
class ACA_File_Manager implements ACA_Cleanup_Interface {
    
    /**
     * File cache storage
     */
    private static $file_cache = [];
    private static $cache_enabled = true;
    private static $cache_ttl = 600; // 10 minutes
    private static $max_cache_size = 50;
    
    /**
     * File operation statistics
     */
    private static $file_stats = [
        'total_reads' => 0,
        'cached_reads' => 0,
        'slow_operations' => []
    ];
    
    /**
     * Optimized file_exists check with caching
     * 
     * @param string $file_path
     * @return bool
     */
    public static function file_exists_cached($file_path) {
        if (!self::$cache_enabled) {
            return file_exists($file_path);
        }
        
        $cache_key = 'exists_' . md5($file_path);
        
        // Check cache first
        if (isset(self::$file_cache[$cache_key])) {
            $cache_entry = self::$file_cache[$cache_key];
            if (time() - $cache_entry['timestamp'] < self::$cache_ttl) {
                return $cache_entry['result'];
            }
            unset(self::$file_cache[$cache_key]);
        }
        
        // Perform actual check
        $start_time = microtime(true);
        $result = file_exists($file_path);
        $execution_time = microtime(true) - $start_time;
        
        // Log slow operations
        if ($execution_time > 0.01) { // 10ms
            self::log_slow_operation('file_exists', $file_path, $execution_time);
        }
        
        // Cache result
        self::cache_file_operation($cache_key, $result);
        
        return $result;
    }
    
    /**
     * Optimized require_once with existence check and caching
     * 
     * @param string $file_path
     * @return bool Success status
     */
    public static function require_once_safe($file_path) {
        // Check if file exists first (cached)
        if (!self::file_exists_cached($file_path)) {
            error_log("ACA Plugin: File not found: $file_path");
            return false;
        }
        
        // Check if already included
        if (in_array(realpath($file_path), get_included_files())) {
            return true;
        }
        
        $start_time = microtime(true);
        
        try {
            require_once $file_path;
            $execution_time = microtime(true) - $start_time;
            
            // Log slow includes
            if ($execution_time > 0.05) { // 50ms
                self::log_slow_operation('require_once', $file_path, $execution_time);
            }
            
            return true;
            
        } catch (Error $e) {
            error_log("ACA Plugin: Failed to require file $file_path: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lazy load class files only when needed
     * 
     * @param string $class_name
     * @param string $file_path
     * @return bool
     */
    public static function lazy_load_class($class_name, $file_path) {
        // Don't load if class already exists
        if (class_exists($class_name, false)) {
            return true;
        }
        
        return self::require_once_safe($file_path);
    }
    
    /**
     * Get file modification time with caching
     * 
     * @param string $file_path
     * @return int|false
     */
    public static function filemtime_cached($file_path) {
        if (!self::file_exists_cached($file_path)) {
            return false;
        }
        
        $cache_key = 'mtime_' . md5($file_path);
        
        // Check cache
        if (isset(self::$file_cache[$cache_key])) {
            $cache_entry = self::$file_cache[$cache_key];
            if (time() - $cache_entry['timestamp'] < self::$cache_ttl) {
                return $cache_entry['result'];
            }
        }
        
        $result = filemtime($file_path);
        self::cache_file_operation($cache_key, $result);
        
        return $result;
    }
    
    /**
     * Optimized file reading with size limits
     * 
     * @param string $file_path
     * @param int $max_size Maximum file size in bytes (default 1MB)
     * @return string|false
     */
    public static function read_file_safe($file_path, $max_size = 1048576) {
        if (!self::file_exists_cached($file_path)) {
            return false;
        }
        
        // Check file size first
        $file_size = filesize($file_path);
        if ($file_size === false || $file_size > $max_size) {
            error_log("ACA Plugin: File too large or unreadable: $file_path (" . ($file_size ?: 'unknown') . " bytes)");
            return false;
        }
        
        $start_time = microtime(true);
        $content = file_get_contents($file_path);
        $execution_time = microtime(true) - $start_time;
        
        self::$file_stats['total_reads']++;
        
        // Log slow reads
        if ($execution_time > 0.1) { // 100ms
            self::log_slow_operation('file_get_contents', $file_path, $execution_time);
        }
        
        return $content;
    }
    
    /**
     * Cache file operation result
     * 
     * @param string $cache_key
     * @param mixed $result
     */
    private static function cache_file_operation($cache_key, $result) {
        if (!self::$cache_enabled) {
            return;
        }
        
        // Prevent cache from growing too large
        if (count(self::$file_cache) >= self::$max_cache_size) {
            $oldest_keys = array_slice(array_keys(self::$file_cache), 0, 10);
            foreach ($oldest_keys as $key) {
                unset(self::$file_cache[$key]);
            }
        }
        
        self::$file_cache[$cache_key] = [
            'result' => $result,
            'timestamp' => time()
        ];
    }
    
    /**
     * Log slow file operations
     * 
     * @param string $operation
     * @param string $file_path
     * @param float $execution_time
     */
    private static function log_slow_operation($operation, $file_path, $execution_time) {
        $slow_op = [
            'operation' => $operation,
            'file' => basename($file_path),
            'execution_time' => round($execution_time * 1000, 2),
            'timestamp' => time()
        ];
        
        self::$file_stats['slow_operations'][] = $slow_op;
        
        // Keep only last 20 slow operations
        if (count(self::$file_stats['slow_operations']) > 20) {
            array_shift(self::$file_stats['slow_operations']);
        }
        
        // Log very slow operations
        if ($execution_time > 0.2) { // 200ms
            error_log("ACA Plugin: Very slow file operation: $operation on " . basename($file_path) . " (" . round($execution_time * 1000, 2) . "ms)");
        }
    }
    
    /**
     * Get file operation statistics
     * 
     * @return array
     */
    public static function get_file_stats() {
        return self::$file_stats;
    }
    
    /**
     * Clear file cache
     */
    public static function clear_file_cache() {
        self::$file_cache = [];
    }
    
    /**
     * Enable/disable file caching
     * 
     * @param bool $enabled
     */
    public static function set_cache_enabled($enabled) {
        self::$cache_enabled = (bool) $enabled;
    }
    
    /**
     * Preload critical files during initialization
     * 
     * @param array $file_paths
     */
    public static function preload_files($file_paths) {
        foreach ($file_paths as $file_path) {
            if (self::file_exists_cached($file_path)) {
                // Just cache the existence, don't actually load
                self::filemtime_cached($file_path);
            }
        }
    }
    
    /**
     * Clean up all file manager data
     * Used during plugin deactivation or testing
     */
    public static function cleanup() {
        // Clear file cache
        self::$file_cache = [];
        
        // Reset file stats
        self::$file_stats = [
            'total_reads' => 0,
            'cached_reads' => 0,
            'slow_operations' => []
        ];
        
        // Reset cache settings to defaults
        self::$cache_enabled = true;
        self::$cache_ttl = 600;
        self::$max_cache_size = 50;
        
        error_log('ACA File Manager: Cleanup completed');
    }
    
    /**
     * Get file operation summary
     * 
     * @return array
     */
    public static function get_operation_summary() {
        return [
            'total_reads' => self::$file_stats['total_reads'],
            'cached_reads' => self::$file_stats['cached_reads'],
            'cache_hit_ratio' => self::$file_stats['total_reads'] > 0 ? 
                round((self::$file_stats['cached_reads'] / self::$file_stats['total_reads']) * 100, 2) : 0,
            'slow_operations_count' => count(self::$file_stats['slow_operations']),
            'cache_entries' => count(self::$file_cache),
            'cache_enabled' => self::$cache_enabled
        ];
    }

    /**
     * Get cleanup status
     * 
     * @return array
     */
    public static function get_cleanup_status() {
        return [
            "cache_entries" => count(self::$file_cache),
            "total_reads" => self::$file_stats["total_reads"],
            "cached_reads" => self::$file_stats["cached_reads"],
            "slow_operations" => count(self::$file_stats["slow_operations"]),
            "cache_enabled" => self::$cache_enabled
        ];
    }
}
