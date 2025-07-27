<?php
/**
 * WordPress Functions Mock System
 * 
 * Comprehensive mocking of WordPress functions for isolated testing
 */

// Prevent multiple inclusions
if (defined('ACA_WORDPRESS_MOCKS_LOADED')) {
    return;
}
define('ACA_WORDPRESS_MOCKS_LOADED', true);

// WordPress Constants
if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/wordpress/');
}
if (!defined('WP_CONTENT_DIR')) {
    define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
}
if (!defined('WP_PLUGIN_DIR')) {
    define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');
}
if (!defined('WPINC')) {
    define('WPINC', 'wp-includes');
}

// Global variables
global $wpdb, $wp_actions, $wp_filters, $wp_options;
$wp_actions = [];
$wp_filters = [];
$wp_options = [
    'aca_ai_content_agent_options' => [],
    'aca_ai_content_agent_gemini_api_key' => 'mock_api_key_encrypted',
    'aca_ai_content_agent_license_key' => 'mock_license_key',
    'aca_ai_content_agent_style_guide' => 'Mock style guide content'
];

/**
 * Mock WordPress Database Class
 */
class MockWPDB {
    public $prefix = 'wp_';
    public $options = 'wp_options';
    public $posts = 'wp_posts';
    public $users = 'wp_users';
    public $usermeta = 'wp_usermeta';
    public $postmeta = 'wp_postmeta';
    
    // Mock tables for plugin
    public $aca_ideas = 'wp_aca_ai_content_agent_ideas';
    public $aca_logs = 'wp_aca_ai_content_agent_logs';
    public $aca_clusters = 'wp_aca_ai_content_agent_clusters';
    public $aca_cluster_items = 'wp_aca_ai_content_agent_cluster_items';
    
    public function __construct() {
        // Initialize mock data
        $this->setupMockTables();
    }
    
    private function setupMockTables() {
        // Mock table data would be setup here
    }
    
    public function prepare($query, ...$args) {
        // Mock prepare - just return formatted query
        return vsprintf(str_replace('%s', "'%s'", $query), $args);
    }
    
    public function query($query) {
        // Mock query execution
        return true;
    }
    
    public function get_results($query) {
        // Return mock results based on query
        if (strpos($query, 'aca_ai_content_agent_ideas') !== false) {
            return [
                (object) ['id' => 1, 'title' => 'Mock Idea 1', 'status' => 'pending'],
                (object) ['id' => 2, 'title' => 'Mock Idea 2', 'status' => 'approved']
            ];
        }
        return [];
    }
    
    public function get_row($query) {
        $results = $this->get_results($query);
        return !empty($results) ? $results[0] : null;
    }
    
    public function get_var($query) {
        // Return mock single value
        return 'mock_value';
    }
    
    public function insert($table, $data, $format = null) {
        // Mock insert
        return true;
    }
    
    public function update($table, $data, $where, $format = null, $where_format = null) {
        // Mock update
        return 1;
    }
    
    public function delete($table, $where, $where_format = null) {
        // Mock delete
        return 1;
    }
}

// WordPress Core Functions
if (!function_exists('wp_die')) {
    function wp_die($message, $title = '', $args = []) {
        echo "WP_DIE: {$message}\n";
        exit(1);
    }
}

if (!function_exists('add_action')) {
    function add_action($hook, $callback, $priority = 10, $accepted_args = 1) {
        global $wp_actions;
        $wp_actions[$hook][] = ['callback' => $callback, 'priority' => $priority];
        return true;
    }
}

if (!function_exists('add_filter')) {
    function add_filter($hook, $callback, $priority = 10, $accepted_args = 1) {
        global $wp_filters;
        $wp_filters[$hook][] = ['callback' => $callback, 'priority' => $priority];
        return true;
    }
}

if (!function_exists('apply_filters')) {
    function apply_filters($hook, $value, ...$args) {
        global $wp_filters;
        if (isset($wp_filters[$hook])) {
            foreach ($wp_filters[$hook] as $filter) {
                if (is_callable($filter['callback'])) {
                    $value = call_user_func($filter['callback'], $value, ...$args);
                }
            }
        }
        return $value;
    }
}

if (!function_exists('do_action')) {
    function do_action($hook, ...$args) {
        global $wp_actions;
        if (isset($wp_actions[$hook])) {
            foreach ($wp_actions[$hook] as $action) {
                if (is_callable($action['callback'])) {
                    call_user_func($action['callback'], ...$args);
                }
            }
        }
    }
}

// Options API
if (!function_exists('get_option')) {
    function get_option($option, $default = false) {
        global $wp_options;
        return $wp_options[$option] ?? $default;
    }
}

if (!function_exists('update_option')) {
    function update_option($option, $value, $autoload = null) {
        global $wp_options;
        $wp_options[$option] = $value;
        return true;
    }
}

if (!function_exists('delete_option')) {
    function delete_option($option) {
        global $wp_options;
        unset($wp_options[$option]);
        return true;
    }
}

// Transients
if (!function_exists('get_transient')) {
    function get_transient($transient) {
        return get_option('_transient_' . $transient, false);
    }
}

if (!function_exists('set_transient')) {
    function set_transient($transient, $value, $expiration = 0) {
        return update_option('_transient_' . $transient, $value);
    }
}

if (!function_exists('delete_transient')) {
    function delete_transient($transient) {
        return delete_option('_transient_' . $transient);
    }
}

// Security Functions
if (!function_exists('wp_verify_nonce')) {
    function wp_verify_nonce($nonce, $action = -1) {
        return true; // Always pass in testing
    }
}

if (!function_exists('wp_create_nonce')) {
    function wp_create_nonce($action = -1) {
        return 'mock_nonce_' . md5($action);
    }
}

if (!function_exists('current_user_can')) {
    function current_user_can($capability, $object_id = null) {
        return true; // Assume admin permissions in testing
    }
}

if (!function_exists('is_admin')) {
    function is_admin() {
        return true;
    }
}

if (!function_exists('wp_doing_ajax')) {
    function wp_doing_ajax() {
        return false;
    }
}

// Sanitization Functions
if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($str) {
        return trim(strip_tags($str));
    }
}

if (!function_exists('sanitize_email')) {
    function sanitize_email($email) {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }
}

if (!function_exists('sanitize_url')) {
    function sanitize_url($url) {
        return filter_var($url, FILTER_SANITIZE_URL);
    }
}

// Escaping Functions
if (!function_exists('esc_html')) {
    function esc_html($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_attr')) {
    function esc_attr($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_url')) {
    function esc_url($url) {
        return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    }
}

// Internationalization
if (!function_exists('__')) {
    function __($text, $domain = 'default') {
        return $text;
    }
}

if (!function_exists('_e')) {
    function _e($text, $domain = 'default') {
        echo $text;
    }
}

if (!function_exists('esc_html__')) {
    function esc_html__($text, $domain = 'default') {
        return esc_html($text);
    }
}

if (!function_exists('esc_attr__')) {
    function esc_attr__($text, $domain = 'default') {
        return esc_attr($text);
    }
}

// WordPress HTTP API
if (!function_exists('wp_remote_get')) {
    function wp_remote_get($url, $args = []) {
        return [
            'response' => ['code' => 200, 'message' => 'OK'],
            'body' => '{"mock": "response"}'
        ];
    }
}

if (!function_exists('wp_remote_post')) {
    function wp_remote_post($url, $args = []) {
        return wp_remote_get($url, $args);
    }
}

if (!function_exists('wp_remote_retrieve_response_code')) {
    function wp_remote_retrieve_response_code($response) {
        return $response['response']['code'] ?? 200;
    }
}

if (!function_exists('wp_remote_retrieve_body')) {
    function wp_remote_retrieve_body($response) {
        return $response['body'] ?? '';
    }
}

if (!function_exists('is_wp_error')) {
    function is_wp_error($thing) {
        return $thing instanceof WP_Error;
    }
}

// Mock WP_Error class
if (!class_exists('WP_Error')) {
    class WP_Error {
        public $errors = [];
        public $error_data = [];
        
        public function __construct($code = '', $message = '', $data = '') {
            if (!empty($code)) {
                $this->errors[$code][] = $message;
                if (!empty($data)) {
                    $this->error_data[$code] = $data;
                }
            }
        }
        
        public function get_error_code() {
            return key($this->errors);
        }
        
        public function get_error_message($code = '') {
            if (empty($code)) {
                $code = $this->get_error_code();
            }
            return $this->errors[$code][0] ?? '';
        }
        
        public function add($code, $message, $data = '') {
            $this->errors[$code][] = $message;
            if (!empty($data)) {
                $this->error_data[$code] = $data;
            }
        }
    }
}

// Plugin specific functions
if (!function_exists('plugin_dir_path')) {
    function plugin_dir_path($file) {
        return dirname($file) . '/';
    }
}

if (!function_exists('plugin_dir_url')) {
    function plugin_dir_url($file) {
        return 'http://example.com/wp-content/plugins/' . basename(dirname($file)) . '/';
    }
}

// User functions
if (!function_exists('wp_get_current_user')) {
    function wp_get_current_user() {
        return (object) [
            'ID' => 1,
            'user_login' => 'testuser',
            'display_name' => 'Test User',
            'user_email' => 'test@example.com'
        ];
    }
}

if (!function_exists('get_current_user_id')) {
    function get_current_user_id() {
        return 1;
    }
}

// Admin functions
if (!function_exists('admin_url')) {
    function admin_url($path = '', $scheme = 'admin') {
        return 'http://example.com/wp-admin/' . $path;
    }
}

// Cron functions
if (!function_exists('wp_schedule_event')) {
    function wp_schedule_event($timestamp, $recurrence, $hook, $args = []) {
        return true;
    }
}

if (!function_exists('wp_unschedule_event')) {
    function wp_unschedule_event($timestamp, $hook, $args = []) {
        return true;
    }
}

if (!function_exists('wp_clear_scheduled_hook')) {
    function wp_clear_scheduled_hook($hook, $args = []) {
        return true;
    }
}

// Initialize global $wpdb
$wpdb = new MockWPDB();