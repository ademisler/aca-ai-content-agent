<?php
/**
 * Rate Limiting System for AI Content Agent
 * 
 * Provides comprehensive rate limiting for API endpoints to prevent abuse
 * and ensure fair usage across all users and IP addresses.
 * 
 * @package AI_Content_Agent
 * @version 2.3.8
 * @since 2.3.8
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Advanced Rate Limiter with multiple strategies
 */
class ACA_Rate_Limiter {
    
    /**
     * Rate limit configurations
     */
    private static $rate_limits = [
        // General API endpoints
        'api_general' => [
            'requests' => 100,
            'window' => 3600, // 1 hour
            'burst' => 10 // burst allowance
        ],
        
        // AI content generation (more restrictive)
        'ai_generation' => [
            'requests' => 20,
            'window' => 3600, // 1 hour
            'burst' => 3
        ],
        
        // Settings updates
        'settings_update' => [
            'requests' => 50,
            'window' => 3600, // 1 hour
            'burst' => 5
        ],
        
        // File uploads
        'file_upload' => [
            'requests' => 10,
            'window' => 3600, // 1 hour
            'burst' => 2
        ],
        
        // Authentication attempts
        'auth_attempt' => [
            'requests' => 10,
            'window' => 900, // 15 minutes
            'burst' => 3
        ]
    ];
    
    /**
     * Check if request is allowed under rate limit
     * 
     * @param string $action The action being rate limited
     * @param string $identifier Unique identifier (IP, user ID, etc.)
     * @return array ['allowed' => bool, 'remaining' => int, 'reset_time' => int]
     */
    public static function is_allowed($action, $identifier = null) {
        if (!isset(self::$rate_limits[$action])) {
            return ['allowed' => true, 'remaining' => 999, 'reset_time' => 0];
        }
        
        $config = self::$rate_limits[$action];
        $identifier = $identifier ?: self::get_client_identifier();
        $cache_key = "aca_rate_limit_{$action}_{$identifier}";
        
        // Get current usage from cache
        $usage_data = wp_cache_get($cache_key);
        $current_time = time();
        
        if (!$usage_data) {
            // First request - initialize
            $usage_data = [
                'count' => 1,
                'window_start' => $current_time,
                'burst_count' => 1,
                'burst_start' => $current_time
            ];
            
            wp_cache_set($cache_key, $usage_data, '', $config['window']);
            
            return [
                'allowed' => true,
                'remaining' => $config['requests'] - 1,
                'reset_time' => $current_time + $config['window']
            ];
        }
        
        // Check if window has expired
        if ($current_time - $usage_data['window_start'] >= $config['window']) {
            // Reset window
            $usage_data = [
                'count' => 1,
                'window_start' => $current_time,
                'burst_count' => 1,
                'burst_start' => $current_time
            ];
            
            wp_cache_set($cache_key, $usage_data, '', $config['window']);
            
            return [
                'allowed' => true,
                'remaining' => $config['requests'] - 1,
                'reset_time' => $current_time + $config['window']
            ];
        }
        
        // Check burst limit (requests in last 60 seconds)
        if ($current_time - $usage_data['burst_start'] < 60) {
            if ($usage_data['burst_count'] >= $config['burst']) {
                return [
                    'allowed' => false,
                    'remaining' => 0,
                    'reset_time' => $usage_data['burst_start'] + 60,
                    'reason' => 'burst_limit_exceeded'
                ];
            }
        } else {
            // Reset burst counter
            $usage_data['burst_count'] = 0;
            $usage_data['burst_start'] = $current_time;
        }
        
        // Check overall window limit
        if ($usage_data['count'] >= $config['requests']) {
            return [
                'allowed' => false,
                'remaining' => 0,
                'reset_time' => $usage_data['window_start'] + $config['window'],
                'reason' => 'rate_limit_exceeded'
            ];
        }
        
        // Increment counters
        $usage_data['count']++;
        $usage_data['burst_count']++;
        
        wp_cache_set($cache_key, $usage_data, '', $config['window']);
        
        return [
            'allowed' => true,
            'remaining' => $config['requests'] - $usage_data['count'],
            'reset_time' => $usage_data['window_start'] + $config['window']
        ];
    }
    
    /**
     * Get unique client identifier
     * 
     * @return string
     */
    private static function get_client_identifier() {
        $user_id = get_current_user_id();
        if ($user_id) {
            return "user_{$user_id}";
        }
        
        // Fallback to IP address with privacy considerations
        $ip = self::get_client_ip();
        return "ip_" . hash('sha256', $ip . wp_salt());
    }
    
    /**
     * Get client IP address safely
     * 
     * @return string
     */
    private static function get_client_ip() {
        $ip_keys = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_CLIENT_IP',            // Proxy
            'HTTP_X_FORWARDED_FOR',      // Load balancer/proxy
            'HTTP_X_FORWARDED',          // Proxy
            'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
            'HTTP_FORWARDED_FOR',        // Proxy
            'HTTP_FORWARDED',            // Proxy
            'REMOTE_ADDR'                // Standard
        ];
        
        foreach ($ip_keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                // Handle comma-separated IPs (X-Forwarded-For)
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                
                // Validate IP
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
    
    /**
     * Add rate limit headers to response
     * 
     * @param array $limit_result Result from is_allowed()
     * @param string $action The action being rate limited
     */
    public static function add_headers($limit_result, $action) {
        if (!isset(self::$rate_limits[$action])) {
            return;
        }
        
        $config = self::$rate_limits[$action];
        
        header('X-RateLimit-Limit: ' . $config['requests']);
        header('X-RateLimit-Remaining: ' . ($limit_result['remaining'] ?? 0));
        header('X-RateLimit-Reset: ' . ($limit_result['reset_time'] ?? 0));
        
        if (!$limit_result['allowed']) {
            $retry_after = $limit_result['reset_time'] - time();
            header('Retry-After: ' . max(0, $retry_after));
        }
    }
    
    /**
     * Handle rate limit exceeded
     * 
     * @param array $limit_result Result from is_allowed()
     * @return WP_Error
     */
    public static function handle_rate_limit_exceeded($limit_result) {
        $retry_after = $limit_result['reset_time'] - time();
        $reason = $limit_result['reason'] ?? 'rate_limit_exceeded';
        
        $messages = [
            'rate_limit_exceeded' => 'Rate limit exceeded. Too many requests.',
            'burst_limit_exceeded' => 'Burst limit exceeded. Please slow down.'
        ];
        
        $message = $messages[$reason] ?? 'Rate limit exceeded.';
        
        return new WP_Error(
            'rate_limit_exceeded',
            $message,
            [
                'status' => 429,
                'retry_after' => max(0, $retry_after),
                'limit_type' => $reason
            ]
        );
    }
    
    /**
     * Middleware function for WordPress REST API
     * 
     * @param WP_REST_Request $request
     * @param string $action
     * @return WP_Error|true
     */
    public static function check_rate_limit($request, $action = 'api_general') {
        // Skip rate limiting for administrators in development
        if (defined('WP_DEBUG') && WP_DEBUG && current_user_can('administrator')) {
            return true;
        }
        
        $limit_result = self::is_allowed($action);
        
        // Add headers regardless of result
        self::add_headers($limit_result, $action);
        
        if (!$limit_result['allowed']) {
            return self::handle_rate_limit_exceeded($limit_result);
        }
        
        return true;
    }
    
    /**
     * Get rate limit status for a specific action
     * 
     * @param string $action
     * @param string $identifier
     * @return array
     */
    public static function get_status($action, $identifier = null) {
        return self::is_allowed($action, $identifier);
    }
    
    /**
     * Clear rate limit for a specific identifier and action
     * 
     * @param string $action
     * @param string $identifier
     * @return bool
     */
    public static function clear_limit($action, $identifier = null) {
        $identifier = $identifier ?: self::get_client_identifier();
        $cache_key = "aca_rate_limit_{$action}_{$identifier}";
        
        return wp_cache_delete($cache_key);
    }
    
    /**
     * Update rate limit configuration
     * 
     * @param string $action
     * @param array $config
     */
    public static function update_config($action, $config) {
        self::$rate_limits[$action] = array_merge(
            self::$rate_limits[$action] ?? [],
            $config
        );
    }
    
    /**
     * Get all rate limit configurations
     * 
     * @return array
     */
    public static function get_configs() {
        return self::$rate_limits;
    }
}

/**
 * Rate limiting helper functions
 */

/**
 * Check rate limit for specific action
 * 
 * @param string $action
 * @param string $identifier
 * @return array
 */
function aca_check_rate_limit($action, $identifier = null) {
    return ACA_Rate_Limiter::is_allowed($action, $identifier);
}

/**
 * Apply rate limiting to REST API endpoint
 * 
 * @param WP_REST_Request $request
 * @param string $action
 * @return WP_Error|true
 */
function aca_apply_rate_limit($request, $action = 'api_general') {
    return ACA_Rate_Limiter::check_rate_limit($request, $action);
}