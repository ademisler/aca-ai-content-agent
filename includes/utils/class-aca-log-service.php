<?php
/**
 * The log service.
 *
 * @link       https://ademisler.com
 * @since      1.2.0
 *
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes/utils
 */

/**
 * Log service class for ACA AI Content Agent.
 *
 * Provides static methods for logging events and errors to the database.
 * Enhanced with structured logging and better error handling.
 *
 * @since 1.2.0
 */
class ACA_Log_Service {

    /**
     * Available log levels.
     *
     * @since 1.2.0
     * @var array
     */
    const LOG_LEVELS = [
        'debug' => 0,
        'info' => 1,
        'warning' => 2,
        'error' => 3,
        'critical' => 4
    ];

    /**
     * Default log level.
     *
     * @since 1.2.0
     * @var string
     */
    const DEFAULT_LOG_LEVEL = 'info';

    /**
     * Maximum log entries to keep.
     *
     * @since 1.2.0
     * @var int
     */
    const MAX_LOG_ENTRIES = 10000;

    /**
     * Add a log entry to the database.
     *
     * @since 1.2.0
     * @param string $message The message to log.
     * @param string $level The level of the log entry (e.g., 'info', 'error').
     * @param array $context Optional. Additional context data.
     * @return bool True on success, false on failure.
     */
    public static function add( $message, $level = 'info', $context = [] ) {
        global $wpdb;
        
        // SECURITY FIX: Validate log level
        if (!array_key_exists($level, self::LOG_LEVELS)) {
            $level = self::DEFAULT_LOG_LEVEL;
        }

        // SECURITY FIX: Sanitize message
        $message = sanitize_text_field($message);
        
        // SECURITY FIX: Limit message length
        if (strlen($message) > 1000) {
            $message = substr($message, 0, 997) . '...';
        }

        // SECURITY FIX: Add context information
        $context_data = '';
        if (!empty($context)) {
            $context_data = wp_json_encode($context);
            if (strlen($context_data) > 500) {
                $context_data = substr($context_data, 0, 497) . '...';
            }
        }

        $table_name = $wpdb->prefix . 'aca_ai_content_agent_logs';

        $result = $wpdb->insert(
            $table_name,
            [
                'message'   => $message,
                'level'     => $level,
                'timestamp' => current_time( 'mysql' ),
                'context'   => $context_data,
                'user_id'   => get_current_user_id(),
                'ip_address' => self::get_client_ip(),
            ]
        );

        if ($result === false) {
            // Fallback to error_log if database insert fails
            error_log("ACA Log Service Error: Failed to insert log entry - " . $wpdb->last_error);
            return false;
        }

        // SECURITY FIX: Clean old log entries
        self::cleanup_old_logs();

        return true;
    }

    /**
     * Get logs with filtering options.
     *
     * @since 1.2.0
     * @param array $args Optional. Query arguments.
     * @return array Array of log entries.
     */
    public static function get_logs($args = []) {
        global $wpdb;
        
        $defaults = [
            'level' => '',
            'limit' => 100,
            'offset' => 0,
            'orderby' => 'timestamp',
            'order' => 'DESC',
            'user_id' => '',
            'date_from' => '',
            'date_to' => ''
        ];

        $args = wp_parse_args($args, $defaults);
        $table_name = $wpdb->prefix . 'aca_ai_content_agent_logs';
        
        $where_conditions = [];
        $where_values = [];

        if (!empty($args['level'])) {
            $where_conditions[] = 'level = %s';
            $where_values[] = $args['level'];
        }

        if (!empty($args['user_id'])) {
            $where_conditions[] = 'user_id = %d';
            $where_values[] = $args['user_id'];
        }

        if (!empty($args['date_from'])) {
            $where_conditions[] = 'timestamp >= %s';
            $where_values[] = $args['date_from'];
        }

        if (!empty($args['date_to'])) {
            $where_conditions[] = 'timestamp <= %s';
            $where_values[] = $args['date_to'];
        }

        $where_clause = '';
        if (!empty($where_conditions)) {
            $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
        }

        $order_clause = sprintf('ORDER BY %s %s', 
            sanitize_sql_orderby($args['orderby']), 
            $args['order'] === 'ASC' ? 'ASC' : 'DESC'
        );

        $limit_clause = sprintf('LIMIT %d OFFSET %d', 
            absint($args['limit']), 
            absint($args['offset'])
        );

        $query = "SELECT * FROM {$table_name} {$where_clause} {$order_clause} {$limit_clause}";
        
        if (!empty($where_values)) {
            $query = $wpdb->prepare($query, $where_values);
        }

        return $wpdb->get_results($query);
    }

    /**
     * Clean up old log entries.
     *
     * @since 1.2.0
     * @return bool True on success, false on failure.
     */
    private static function cleanup_old_logs() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_ai_content_agent_logs';
        
        // Count total entries with error handling
        $total_entries = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
        
        if ($total_entries === null) {
            error_log('ACA Log Service: Failed to count log entries');
            return false;
        }
        
        if ($total_entries > self::MAX_LOG_ENTRIES) {
            $entries_to_delete = $total_entries - self::MAX_LOG_ENTRIES;
            
            $result = $wpdb->query($wpdb->prepare(
                "DELETE FROM {$table_name} ORDER BY timestamp ASC LIMIT %d",
                $entries_to_delete
            ));
            
            if ($result === false) {
                error_log('ACA Log Service: Failed to cleanup old logs');
                return false;
            }
        }

        return true;
    }

    /**
     * Get client IP address.
     *
     * @since 1.2.0
     * @return string The client IP address.
     */
    private static function get_client_ip() {
        $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }

    /**
     * Log a debug message.
     *
     * @since 1.2.0
     * @param string $message The message to log.
     * @param array $context Optional. Additional context data.
     * @return bool True on success, false on failure.
     */
    public static function debug($message, $context = []) {
        return self::add($message, 'debug', $context);
    }

    /**
     * Log an info message.
     *
     * @since 1.2.0
     * @param string $message The message to log.
     * @param array $context Optional. Additional context data.
     * @return bool True on success, false on failure.
     */
    public static function info($message, $context = []) {
        return self::add($message, 'info', $context);
    }

    /**
     * Log a warning message.
     *
     * @since 1.2.0
     * @param string $message The message to log.
     * @param array $context Optional. Additional context data.
     * @return bool True on success, false on failure.
     */
    public static function warning($message, $context = []) {
        return self::add($message, 'warning', $context);
    }

    /**
     * Log an error message.
     *
     * @since 1.2.0
     * @param string $message The message to log.
     * @param array $context Optional. Additional context data.
     * @return bool True on success, false on failure.
     */
    public static function error($message, $context = []) {
        return self::add($message, 'error', $context);
    }

    /**
     * Log a critical message.
     *
     * @since 1.2.0
     * @param string $message The message to log.
     * @param array $context Optional. Additional context data.
     * @return bool True on success, false on failure.
     */
    public static function critical($message, $context = []) {
        return self::add($message, 'critical', $context);
    }

    /**
     * Log performance metrics.
     *
     * @since 1.2.0
     * @param string $operation The operation being measured.
     * @param float $start_time The start time from microtime(true).
     * @param array $context Optional. Additional context data.
     * @return bool True on success, false on failure.
     */
    public static function log_performance($operation, $start_time, $context = []) {
        $end_time = microtime(true);
        $duration = round(($end_time - $start_time) * 1000, 2); // Convert to milliseconds
        
        $context['duration_ms'] = $duration;
        $context['operation'] = $operation;
        
        $level = $duration > 5000 ? 'warning' : 'info'; // Warning if > 5 seconds
        
        return self::add(
            sprintf('Performance: %s completed in %sms', $operation, $duration),
            $level,
            $context
        );
    }

    /**
     * Get performance statistics.
     *
     * @since 1.2.0
     * @param string $operation Optional. Specific operation to get stats for.
     * @param int $days Optional. Number of days to look back.
     * @return array Performance statistics.
     */
    public static function get_performance_stats($operation = '', $days = 7) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_ai_content_agent_logs';
        $cutoff_date = gmdate('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        $where_clause = "WHERE level IN ('info', 'warning') AND message LIKE 'Performance:%' AND timestamp >= %s";
        $params = [$cutoff_date];
        
        if (!empty($operation)) {
            $where_clause .= " AND context LIKE %s";
            $params[] = '%"operation":"' . $wpdb->esc_like($operation) . '"%';
        }
        
        $query = $wpdb->prepare(
            "SELECT 
                AVG(CAST(JSON_EXTRACT(context, '$.duration_ms') AS DECIMAL(10,2))) as avg_duration,
                MAX(CAST(JSON_EXTRACT(context, '$.duration_ms') AS DECIMAL(10,2))) as max_duration,
                MIN(CAST(JSON_EXTRACT(context, '$.duration_ms') AS DECIMAL(10,2))) as min_duration,
                COUNT(*) as total_operations
            FROM {$table_name} 
            {$where_clause}",
            ...$params
        );
        
        $stats = $wpdb->get_row($query, ARRAY_A);
        
        return $stats ?: [
            'avg_duration' => 0,
            'max_duration' => 0,
            'min_duration' => 0,
            'total_operations' => 0
        ];
    }
}