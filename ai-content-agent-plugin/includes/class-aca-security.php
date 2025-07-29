<?php
/**
 * Security utilities for the AI Content Agent plugin
 * Handles nonce validation, capability checks, input sanitization, and SQL injection prevention
 */

if (!defined('ABSPATH')) {
    exit;
}

class ACA_Security {
    
    /**
     * Initialize security measures
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'setup_security_headers'));
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_security_scripts'));
        add_filter('wp_headers', array(__CLASS__, 'add_security_headers'));
    }
    
    /**
     * Verify WordPress nonce with enhanced security
     */
    public static function verify_nonce($nonce, $action = 'wp_rest', $user_id = false) {
        if (empty($nonce)) {
            return new WP_Error('missing_nonce', 'Security token is missing', array('status' => 403));
        }
        
        // Verify the nonce
        $verified = wp_verify_nonce($nonce, $action);
        if (!$verified) {
            // Log failed nonce attempts
            error_log(sprintf(
                'ACA Security: Failed nonce verification. Action: %s, User ID: %d, IP: %s',
                $action,
                get_current_user_id(),
                self::get_client_ip()
            ));
            
            return new WP_Error('invalid_nonce', 'Invalid security token', array('status' => 403));
        }
        
        return true;
    }
    
    /**
     * Check user capabilities with role-based access control
     */
    public static function check_capability($capability, $user_id = null) {
        if ($user_id === null) {
            $user_id = get_current_user_id();
        }
        
        if (!$user_id) {
            return new WP_Error('not_authenticated', 'User not authenticated', array('status' => 401));
        }
        
        $user = get_user_by('id', $user_id);
        if (!$user) {
            return new WP_Error('invalid_user', 'Invalid user', array('status' => 401));
        }
        
        // Check if user has required capability
        if (!user_can($user, $capability)) {
            // Log unauthorized access attempts
            error_log(sprintf(
                'ACA Security: Unauthorized access attempt. User: %s, Required capability: %s, IP: %s',
                $user->user_login,
                $capability,
                self::get_client_ip()
            ));
            
            return new WP_Error('insufficient_permissions', 'Insufficient permissions', array('status' => 403));
        }
        
        return true;
    }
    
    /**
     * Enhanced input sanitization
     */
    public static function sanitize_input($input, $type = 'text') {
        if (is_null($input)) {
            return null;
        }
        
        switch ($type) {
            case 'text':
                return sanitize_text_field($input);
                
            case 'textarea':
                return sanitize_textarea_field($input);
                
            case 'email':
                $sanitized = sanitize_email($input);
                return is_email($sanitized) ? $sanitized : null;
                
            case 'url':
                return esc_url_raw($input);
                
            case 'int':
                return intval($input);
                
            case 'float':
                return floatval($input);
                
            case 'bool':
                return (bool) $input;
                
            case 'array':
                if (!is_array($input)) {
                    return array();
                }
                return array_map(array(__CLASS__, 'sanitize_recursive'), $input);
                
            case 'json':
                if (is_string($input)) {
                    $decoded = json_decode($input, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return self::sanitize_recursive($decoded);
                    }
                }
                return null;
                
            case 'slug':
                return sanitize_title($input);
                
            case 'key':
                return sanitize_key($input);
                
            case 'html':
                return wp_kses_post($input);
                
            case 'filename':
                return sanitize_file_name($input);
                
            default:
                return sanitize_text_field($input);
        }
    }
    
    /**
     * Recursively sanitize arrays and objects
     */
    private static function sanitize_recursive($data) {
        if (is_array($data)) {
            return array_map(array(__CLASS__, 'sanitize_recursive'), $data);
        } elseif (is_object($data)) {
            $vars = get_object_vars($data);
            foreach ($vars as $key => $value) {
                $data->$key = self::sanitize_recursive($value);
            }
            return $data;
        } else {
            return self::sanitize_input($data, 'text');
        }
    }
    
    /**
     * Prepare SQL queries with proper escaping to prevent SQL injection
     */
    public static function prepare_query($query, $args = array()) {
        global $wpdb;
        
        if (empty($args)) {
            return $query;
        }
        
        // Ensure args is an array
        if (!is_array($args)) {
            $args = array($args);
        }
        
        return $wpdb->prepare($query, $args);
    }
    
    /**
     * Validate and sanitize database table names
     */
    public static function validate_table_name($table_name) {
        global $wpdb;
        
        // Remove any non-alphanumeric characters except underscores
        $sanitized = preg_replace('/[^a-zA-Z0-9_]/', '', $table_name);
        
        // Ensure it starts with the WordPress prefix
        if (strpos($sanitized, $wpdb->prefix) !== 0) {
            $sanitized = $wpdb->prefix . $sanitized;
        }
        
        return $sanitized;
    }
    
    /**
     * Rate limiting for API endpoints
     */
    public static function check_rate_limit($action, $limit = 60, $window = 3600) {
        $user_id = get_current_user_id();
        $ip = self::get_client_ip();
        $key = sprintf('aca_rate_limit_%s_%s_%d', $action, $ip, $user_id);
        
        $current_count = get_transient($key);
        
        if ($current_count === false) {
            set_transient($key, 1, $window);
            return true;
        }
        
        if ($current_count >= $limit) {
            // Log rate limit violations
            error_log(sprintf(
                'ACA Security: Rate limit exceeded. Action: %s, User ID: %d, IP: %s, Count: %d',
                $action,
                $user_id,
                $ip,
                $current_count
            ));
            
            return new WP_Error('rate_limit_exceeded', 'Rate limit exceeded', array('status' => 429));
        }
        
        set_transient($key, $current_count + 1, $window);
        return true;
    }
    
    /**
     * Validate API key format and strength
     */
    public static function validate_api_key($api_key, $min_length = 20) {
        if (empty($api_key)) {
            return new WP_Error('empty_api_key', 'API key cannot be empty');
        }
        
        if (strlen($api_key) < $min_length) {
            return new WP_Error('weak_api_key', sprintf('API key must be at least %d characters', $min_length));
        }
        
        // Check for common weak patterns
        if (preg_match('/^(test|demo|sample|example)/i', $api_key)) {
            return new WP_Error('invalid_api_key', 'Invalid API key format');
        }
        
        return true;
    }
    
    /**
     * Get client IP address with proxy support
     */
    public static function get_client_ip() {
        $ip_keys = array(
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_CLIENT_IP',            // Proxy
            'HTTP_X_FORWARDED_FOR',      // Load balancer/proxy
            'HTTP_X_FORWARDED',          // Proxy
            'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
            'HTTP_FORWARDED_FOR',        // Proxy
            'HTTP_FORWARDED',            // Proxy
            'REMOTE_ADDR'                // Standard
        );
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Setup security headers
     */
    public static function setup_security_headers() {
        if (!is_admin() || !current_user_can('manage_options')) {
            return;
        }
        
        // Only apply to our plugin pages
        if (!isset($_GET['page']) || $_GET['page'] !== 'ai-content-agent') {
            return;
        }
        
        // Content Security Policy
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; connect-src 'self' https://generativelanguage.googleapis.com https://console.cloud.google.com;");
        
        // Other security headers
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
    
    /**
     * Add security headers via WordPress filter
     */
    public static function add_security_headers($headers) {
        if (is_admin() && isset($_GET['page']) && $_GET['page'] === 'ai-content-agent') {
            $headers['X-Content-Type-Options'] = 'nosniff';
            $headers['X-Frame-Options'] = 'SAMEORIGIN';
            $headers['X-XSS-Protection'] = '1; mode=block';
            $headers['Referrer-Policy'] = 'strict-origin-when-cross-origin';
        }
        
        return $headers;
    }
    
    /**
     * Enqueue security-related scripts
     */
    public static function enqueue_security_scripts() {
        if (is_admin() && isset($_GET['page']) && $_GET['page'] === 'ai-content-agent') {
            // Add nonce to JavaScript for AJAX requests
            wp_localize_script('aca-admin', 'acaSecurity', array(
                'nonce' => wp_create_nonce('wp_rest'),
                'ajaxNonce' => wp_create_nonce('aca_ajax'),
            ));
        }
    }
    
    /**
     * Validate file uploads
     */
    public static function validate_file_upload($file) {
        if (!isset($file['error']) || is_array($file['error'])) {
            return new WP_Error('invalid_file', 'Invalid file parameters');
        }
        
        // Check for upload errors
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                return new WP_Error('no_file', 'No file was uploaded');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return new WP_Error('file_too_large', 'File exceeds maximum size');
            default:
                return new WP_Error('upload_error', 'Unknown upload error');
        }
        
        // Validate file size (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            return new WP_Error('file_too_large', 'File size exceeds 5MB limit');
        }
        
        // Validate file type
        $allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'text/plain', 'application/json');
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->file($file['tmp_name']);
        
        if (!in_array($mime_type, $allowed_types)) {
            return new WP_Error('invalid_file_type', 'File type not allowed');
        }
        
        return true;
    }
    
    /**
     * Encrypt sensitive data
     */
    public static function encrypt_data($data, $key = null) {
        if ($key === null) {
            $key = self::get_encryption_key();
        }
        
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
        
        return base64_encode($iv . $encrypted);
    }
    
    /**
     * Decrypt sensitive data
     */
    public static function decrypt_data($encrypted_data, $key = null) {
        if ($key === null) {
            $key = self::get_encryption_key();
        }
        
        $data = base64_decode($encrypted_data);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        
        return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
    }
    
    /**
     * Get or generate encryption key
     */
    private static function get_encryption_key() {
        $key = get_option('aca_encryption_key');
        
        if (!$key) {
            $key = wp_generate_password(32, false);
            update_option('aca_encryption_key', $key);
        }
        
        return $key;
    }
    
    /**
     * Log security events
     */
    public static function log_security_event($event_type, $message, $data = array()) {
        $log_entry = array(
            'timestamp' => current_time('mysql'),
            'event_type' => $event_type,
            'message' => $message,
            'user_id' => get_current_user_id(),
            'ip_address' => self::get_client_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'data' => $data
        );
        
        // Log to WordPress error log
        error_log('ACA Security Event: ' . json_encode($log_entry));
        
        // Optionally store in database for security dashboard
        global $wpdb;
        $wpdb->insert(
            $wpdb->prefix . 'aca_security_logs',
            array(
                'event_type' => $event_type,
                'message' => $message,
                'user_id' => get_current_user_id(),
                'ip_address' => self::get_client_ip(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'data' => json_encode($data),
                'created_at' => current_time('mysql')
            )
        );
    }
}

// Initialize security
ACA_Security::init();