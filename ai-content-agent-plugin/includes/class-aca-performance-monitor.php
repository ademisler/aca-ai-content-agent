<?php
/**
 * Performance Monitoring System for AI Content Agent
 * 
 * Provides comprehensive performance monitoring, profiling, and optimization
 * recommendations for the plugin.
 * 
 * @package AI_Content_Agent
 * @version 2.3.8
 * @since 2.3.8
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Advanced Performance Monitor
 */
class ACA_Performance_Monitor {
    
    /**
     * Performance metrics storage
     */
    private static $metrics = [];
    
    /**
     * Performance thresholds
     */
    private static $thresholds = [
        'memory_usage_mb' => 64,      // MB
        'execution_time_ms' => 1000,   // milliseconds
        'database_queries' => 10,      // per request
        'api_response_time_ms' => 2000 // milliseconds
    ];
    
    /**
     * Query cache storage
     */
    private static $query_cache = [];
    private static $cache_enabled = true;
    private static $cache_ttl = 300; // 5 minutes
    private static $max_cache_size = 100;
    
    /**
     * Database query optimization
     */
    private static $query_stats = [
        'total_queries' => 0,
        'cached_queries' => 0,
        'slow_queries' => []
    ];
    
    /**
     * Start performance monitoring for a specific operation
     * 
     * @param string $operation_name
     * @return string Unique tracking ID
     */
    public static function start($operation_name) {
        $tracking_id = uniqid($operation_name . '_', true);
        
        self::$metrics[$tracking_id] = [
            'operation' => $operation_name,
            'start_time' => microtime(true),
            'start_memory' => memory_get_usage(true),
            'start_peak_memory' => memory_get_peak_usage(true),
            'start_queries' => self::get_query_count(),
            'status' => 'running'
        ];
        
        return $tracking_id;
    }
    
    /**
     * End performance monitoring for an operation
     * 
     * @param string $tracking_id
     * @param array $additional_data
     * @return array Performance metrics
     */
    public static function end($tracking_id, $additional_data = []) {
        if (!isset(self::$metrics[$tracking_id])) {
            return null;
        }
        
        $metric = &self::$metrics[$tracking_id];
        $end_time = microtime(true);
        $end_memory = memory_get_usage(true);
        $end_peak_memory = memory_get_peak_usage(true);
        $end_queries = self::get_query_count();
        
        // Calculate metrics
        $metric['end_time'] = $end_time;
        $metric['execution_time_ms'] = round(($end_time - $metric['start_time']) * 1000, 2);
        $metric['memory_used_mb'] = round(($end_memory - $metric['start_memory']) / 1024 / 1024, 2);
        $metric['peak_memory_mb'] = round($end_peak_memory / 1024 / 1024, 2);
        $metric['queries_count'] = $end_queries - $metric['start_queries'];
        $metric['status'] = 'completed';
        $metric['additional_data'] = $additional_data;
        
        // Check thresholds and add warnings
        $metric['warnings'] = self::check_thresholds($metric);
        
        // Store in persistent storage if needed
        if (defined('WP_DEBUG') && WP_DEBUG) {
            self::log_performance_data($metric);
        }
        
        return $metric;
    }
    
    /**
     * Get current database query count
     * 
     * @return int
     */
    private static function get_query_count() {
        global $wpdb;
        return $wpdb->num_queries ?? 0;
    }
    
    /**
     * Check performance thresholds
     * 
     * @param array $metric
     * @return array Warnings
     */
    private static function check_thresholds($metric) {
        $warnings = [];
        
        if ($metric['execution_time_ms'] > self::$thresholds['execution_time_ms']) {
            $warnings[] = [
                'type' => 'slow_execution',
                'message' => "Execution time ({$metric['execution_time_ms']}ms) exceeded threshold (" . self::$thresholds['execution_time_ms'] . "ms)",
                'severity' => 'warning'
            ];
        }
        
        if ($metric['memory_used_mb'] > self::$thresholds['memory_usage_mb']) {
            $warnings[] = [
                'type' => 'high_memory_usage',
                'message' => "Memory usage ({$metric['memory_used_mb']}MB) exceeded threshold (" . self::$thresholds['memory_usage_mb'] . "MB)",
                'severity' => 'warning'
            ];
        }
        
        if ($metric['queries_count'] > self::$thresholds['database_queries']) {
            $warnings[] = [
                'type' => 'too_many_queries',
                'message' => "Database queries ({$metric['queries_count']}) exceeded threshold (" . self::$thresholds['database_queries'] . ")",
                'severity' => 'critical'
            ];
        }
        
        return $warnings;
    }
    
    /**
     * Log performance data for analysis
     * 
     * @param array $metric
     */
    private static function log_performance_data($metric) {
        $log_entry = [
            'timestamp' => current_time('mysql'),
            'operation' => $metric['operation'],
            'execution_time_ms' => $metric['execution_time_ms'],
            'memory_used_mb' => $metric['memory_used_mb'],
            'peak_memory_mb' => $metric['peak_memory_mb'],
            'queries_count' => $metric['queries_count'],
            'warnings' => $metric['warnings'],
            'url' => $_SERVER['REQUEST_URI'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ];
        
        // Store in database for analysis
        global $wpdb;
        $table_name = $wpdb->prefix . 'aca_performance_logs';
        
        // Create table if it doesn't exist
        self::ensure_performance_table();
        
        $wpdb->insert($table_name, $log_entry);
    }
    
    /**
     * Ensure performance logging table exists
     */
    private static function ensure_performance_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_performance_logs';
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            timestamp datetime NOT NULL,
            operation varchar(100) NOT NULL,
            execution_time_ms decimal(10,2) NOT NULL,
            memory_used_mb decimal(10,2) NOT NULL,
            peak_memory_mb decimal(10,2) NOT NULL,
            queries_count int NOT NULL,
            warnings longtext,
            url varchar(255),
            user_agent text,
            PRIMARY KEY  (id),
            KEY timestamp (timestamp),
            KEY operation (operation),
            KEY execution_time (execution_time_ms)
        ) $charset_collate;";
        
        $upgrade_file = ABSPATH . 'wp-admin/includes/upgrade.php';
        if (file_exists($upgrade_file)) {
            require_once($upgrade_file);
            dbDelta($sql);
        } else {
            error_log('ACA Plugin: WordPress upgrade.php not found');
        }
    }
    
    /**
     * Get performance statistics
     * 
     * @param string $operation
     * @param int $days
     * @return array
     */
    public static function get_stats($operation = null, $days = 7) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_performance_logs';
        $where_clause = "WHERE timestamp >= DATE_SUB(NOW(), INTERVAL %d DAY)";
        $params = [$days];
        
        if ($operation) {
            $where_clause .= " AND operation = %s";
            $params[] = $operation;
        }
        
        $query = "SELECT 
            operation,
            COUNT(*) as total_operations,
            AVG(execution_time_ms) as avg_execution_time,
            MAX(execution_time_ms) as max_execution_time,
            AVG(memory_used_mb) as avg_memory_usage,
            MAX(memory_used_mb) as max_memory_usage,
            AVG(queries_count) as avg_queries,
            MAX(queries_count) as max_queries,
            SUM(CASE WHEN warnings IS NOT NULL AND warnings != '[]' THEN 1 ELSE 0 END) as warning_count
        FROM {$table_name} 
        {$where_clause}
        GROUP BY operation
        ORDER BY avg_execution_time DESC";
        
        return $wpdb->get_results($wpdb->prepare($query, ...$params), ARRAY_A);
    }
    
    /**
     * Get slow operations
     * 
     * @param int $limit
     * @return array
     */
    public static function get_slow_operations($limit = 10) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_performance_logs';
        
        $query = "SELECT * FROM {$table_name} 
                 WHERE execution_time_ms > %d
                 ORDER BY execution_time_ms DESC 
                 LIMIT %d";
        
        return $wpdb->get_results(
            $wpdb->prepare($query, self::$thresholds['execution_time_ms'], $limit),
            ARRAY_A
        );
    }
    
    /**
     * Get memory-intensive operations
     * 
     * @param int $limit
     * @return array
     */
    public static function get_memory_intensive_operations($limit = 10) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_performance_logs';
        
        $query = "SELECT * FROM {$table_name} 
                 WHERE memory_used_mb > %d
                 ORDER BY memory_used_mb DESC 
                 LIMIT %d";
        
        return $wpdb->get_results(
            $wpdb->prepare($query, self::$thresholds['memory_usage_mb'], $limit),
            ARRAY_A
        );
    }
    
    /**
     * Monitor WordPress hooks performance
     * 
     * @param string $hook_name
     * @param callable $callback
     * @param array $args
     * @return mixed
     */
    public static function monitor_hook($hook_name, $callback, $args = []) {
        $tracking_id = self::start("hook_{$hook_name}");
        
        try {
            $result = call_user_func_array($callback, $args);
            self::end($tracking_id, ['hook' => $hook_name, 'success' => true]);
            return $result;
        } catch (Exception $e) {
            self::end($tracking_id, [
                'hook' => $hook_name, 
                'success' => false, 
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Monitor database operations
     * 
     * @param string $operation_name
     * @param callable $callback
     * @return mixed
     */
    public static function monitor_database_operation($operation_name, $callback) {
        global $wpdb;
        
        $start_queries = $wpdb->num_queries;
        $tracking_id = self::start("db_{$operation_name}");
        
        try {
            $result = call_user_func($callback);
            $end_queries = $wpdb->num_queries;
            
            self::end($tracking_id, [
                'operation' => $operation_name,
                'queries_executed' => $end_queries - $start_queries,
                'success' => true
            ]);
            
            return $result;
        } catch (Exception $e) {
            self::end($tracking_id, [
                'operation' => $operation_name,
                'success' => false,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Get current system performance
     * 
     * @return array
     */
    public static function get_system_performance() {
        return [
            'memory_usage' => [
                'current_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
                'peak_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
                'limit_mb' => ini_get('memory_limit')
            ],
            'execution_time' => [
                'current' => round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 3),
                'limit' => ini_get('max_execution_time')
            ],
            'database' => [
                'queries' => self::get_query_count(),
                'time' => self::get_database_time()
            ],
            'opcache' => [
                'enabled' => function_exists('opcache_get_status'),
                'status' => function_exists('opcache_get_status') ? opcache_get_status() : null
            ]
        ];
    }
    
    /**
     * Get database execution time
     * 
     * @return float
     */
    private static function get_database_time() {
        global $wpdb;
        return isset($wpdb->time_start) ? microtime(true) - $wpdb->time_start : 0;
    }
    
    /**
     * Generate performance report
     * 
     * @param int $days
     * @return array
     */
    public static function generate_report($days = 7) {
        return [
            'period' => $days . ' days',
            'generated_at' => current_time('mysql'),
            'overall_stats' => self::get_stats(null, $days),
            'slow_operations' => self::get_slow_operations(5),
            'memory_intensive' => self::get_memory_intensive_operations(5),
            'system_info' => self::get_system_performance(),
            'recommendations' => self::generate_recommendations()
        ];
    }
    
    /**
     * Generate performance recommendations
     * 
     * @return array
     */
    private static function generate_recommendations() {
        $recommendations = [];
        $system_perf = self::get_system_performance();
        
        // Memory recommendations
        if ($system_perf['memory_usage']['current_mb'] > 32) {
            $recommendations[] = [
                'type' => 'memory',
                'priority' => 'medium',
                'message' => 'Consider optimizing memory usage or increasing PHP memory limit'
            ];
        }
        
        // Database recommendations
        if ($system_perf['database']['queries'] > 20) {
            $recommendations[] = [
                'type' => 'database',
                'priority' => 'high',
                'message' => 'Too many database queries. Consider implementing caching or query optimization'
            ];
        }
        
        // OPcache recommendations
        if (!$system_perf['opcache']['enabled']) {
            $recommendations[] = [
                'type' => 'opcache',
                'priority' => 'high',
                'message' => 'Enable OPcache for better PHP performance'
            ];
        }
        
        return $recommendations;
    }
    
    /**
     * Clean old performance logs
     * 
     * @param int $days_to_keep
     * @return int Number of deleted records
     */
    public static function cleanup_logs($days_to_keep = 30) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_performance_logs';
        
        $result = $wpdb->query($wpdb->prepare(
            "DELETE FROM {$table_name} WHERE timestamp < DATE_SUB(NOW(), INTERVAL %d DAY)",
            $days_to_keep
        ));
        
        return $result !== false ? $result : 0;
    }

    /**
     * Cache database query result
     * 
     * @param string $query_hash
     * @param mixed $result
     * @param int $ttl
     */
    public static function cache_query_result($query_hash, $result, $ttl = null) {
        if (!self::$cache_enabled) {
            return;
        }
        
        $ttl = $ttl ?: self::$cache_ttl;
        
        // Prevent cache from growing too large
        if (count(self::$query_cache) >= self::$max_cache_size) {
            // Remove oldest entries
            $oldest_keys = array_slice(array_keys(self::$query_cache), 0, 10);
            foreach ($oldest_keys as $key) {
                unset(self::$query_cache[$key]);
            }
        }
        
        self::$query_cache[$query_hash] = [
            'result' => $result,
            'timestamp' => time(),
            'ttl' => $ttl
        ];
    }
    
    /**
     * Get cached query result
     * 
     * @param string $query_hash
     * @return mixed|null
     */
    public static function get_cached_query_result($query_hash) {
        if (!self::$cache_enabled || !isset(self::$query_cache[$query_hash])) {
            return null;
        }
        
        $cache_entry = self::$query_cache[$query_hash];
        
        // Check if cache entry is expired
        if (time() - $cache_entry['timestamp'] > $cache_entry['ttl']) {
            unset(self::$query_cache[$query_hash]);
            return null;
        }
        
        self::$query_stats['cached_queries']++;
        return $cache_entry['result'];
    }
    
    /**
     * Generate query hash for caching
     * 
     * @param string $query
     * @param array $args
     * @return string
     */
    public static function generate_query_hash($query, $args = []) {
        return md5($query . serialize($args));
    }
    
    /**
     * Monitor database query performance
     * 
     * @param string $query
     * @param float $execution_time
     * @param mixed $result
     */
    public static function log_query_performance($query, $execution_time, $result = null) {
        self::$query_stats['total_queries']++;
        
        // Log slow queries (over 100ms)
        if ($execution_time > 0.1) {
            self::$query_stats['slow_queries'][] = [
                'query' => substr($query, 0, 200) . (strlen($query) > 200 ? '...' : ''),
                'execution_time' => round($execution_time * 1000, 2),
                'timestamp' => time()
            ];
            
            // Keep only last 20 slow queries
            if (count(self::$query_stats['slow_queries']) > 20) {
                array_shift(self::$query_stats['slow_queries']);
            }
            
            // Log to error log if very slow (over 500ms)
            if ($execution_time > 0.5) {
                error_log("ACA Plugin: Slow query detected (" . round($execution_time * 1000, 2) . "ms): " . substr($query, 0, 100));
            }
        }
    }
    
    /**
     * Get database query statistics
     * 
     * @return array
     */
    public static function get_query_stats() {
        return self::$query_stats;
    }
    
    /**
     * Clear query cache
     */
    public static function clear_query_cache() {
        self::$query_cache = [];
    }
    
    /**
     * Enable/disable query caching
     * 
     * @param bool $enabled
     */
    public static function set_cache_enabled($enabled) {
        self::$cache_enabled = (bool) $enabled;
    }
    
    /**
     * Clean up all performance monitoring data
     * Used during plugin deactivation or testing
     */
    public static function cleanup() {
        // Clear all metrics
        self::$metrics = [];
        
        // Clear query cache
        self::$query_cache = [];
        
        // Reset query stats
        self::$query_stats = [
            'total_queries' => 0,
            'cached_queries' => 0,
            'slow_queries' => []
        ];
        
        // Reset thresholds to defaults
        self::$thresholds = [
            'memory_usage_mb' => 64,
            'execution_time_ms' => 1000,
            'database_queries' => 10,
            'api_response_time_ms' => 2000
        ];
        
        // Reset cache settings
        self::$cache_enabled = true;
        self::$cache_ttl = 300;
        self::$max_cache_size = 100;
        
        error_log('ACA Performance Monitor: Cleanup completed');
    }
    
    /**
     * Get memory usage summary
     * 
     * @return array
     */
    public static function get_memory_summary() {
        return [
            'current_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'peak_usage_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
            'limit_mb' => ini_get('memory_limit'),
            'active_metrics' => count(self::$metrics),
            'cached_queries' => count(self::$query_cache)
        ];
    }
}

/**
 * Performance monitoring helper functions
 */

/**
 * Start performance monitoring
 * 
 * @param string $operation_name
 * @return string
 */
function aca_perf_start($operation_name) {
    return ACA_Performance_Monitor::start($operation_name);
}

/**
 * End performance monitoring
 * 
 * @param string $tracking_id
 * @param array $additional_data
 * @return array
 */
function aca_perf_end($tracking_id, $additional_data = []) {
    return ACA_Performance_Monitor::end($tracking_id, $additional_data);
}

/**
 * Monitor a function execution
 * 
 * @param string $operation_name
 * @param callable $callback
 * @param array $args
 * @return mixed
 */
function aca_perf_monitor($operation_name, $callback, $args = []) {
    $tracking_id = aca_perf_start($operation_name);
    
    try {
        $result = call_user_func_array($callback, $args);
        aca_perf_end($tracking_id, ['success' => true]);
        return $result;
    } catch (Exception $e) {
        aca_perf_end($tracking_id, ['success' => false, 'error' => $e->getMessage()]);
        throw $e;
    }
}