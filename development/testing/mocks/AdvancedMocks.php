<?php
/**
 * Advanced Mock System
 * 
 * Extended mocking capabilities for comprehensive testing
 */

// Prevent multiple inclusions
if (defined('ACA_ADVANCED_MOCKS_LOADED')) {
    return;
}
define('ACA_ADVANCED_MOCKS_LOADED', true);

// Advanced WordPress functions
if (!function_exists('current_time')) {
    function current_time($type, $gmt = 0) {
        return ($type === 'mysql') ? date('Y-m-d H:i:s') : time();
    }
}

if (!function_exists('wp_upload_dir')) {
    function wp_upload_dir($time = null) {
        return [
            'path' => '/tmp/uploads',
            'url' => 'http://example.com/wp-content/uploads',
            'subdir' => '',
            'basedir' => '/tmp/uploads',
            'baseurl' => 'http://example.com/wp-content/uploads',
            'error' => false
        ];
    }
}

if (!function_exists('wp_get_attachment_url')) {
    function wp_get_attachment_url($attachment_id) {
        return "http://example.com/wp-content/uploads/attachment-{$attachment_id}.jpg";
    }
}

if (!function_exists('wp_insert_post')) {
    function wp_insert_post($postarr, $wp_error = false) {
        if (empty($postarr['post_title'])) {
            return $wp_error ? new WP_Error('empty_title', 'Post title is required') : 0;
        }
        return rand(1, 1000); // Mock post ID
    }
}

if (!function_exists('wp_update_post')) {
    function wp_update_post($postarr, $wp_error = false) {
        if (empty($postarr['ID'])) {
            return $wp_error ? new WP_Error('missing_id', 'Post ID is required') : 0;
        }
        return $postarr['ID'];
    }
}

if (!function_exists('get_post')) {
    function get_post($post_id, $output = OBJECT) {
        return (object) [
            'ID' => $post_id,
            'post_title' => "Mock Post {$post_id}",
            'post_content' => "Mock content for post {$post_id}",
            'post_status' => 'publish',
            'post_type' => 'post',
            'post_author' => 1
        ];
    }
}

if (!function_exists('get_posts')) {
    function get_posts($args = []) {
        $numberposts = isset($args['numberposts']) ? $args['numberposts'] : 5;
        $posts = [];
        
        for ($i = 1; $i <= $numberposts; $i++) {
            $posts[] = get_post($i);
        }
        
        return $posts;
    }
}

if (!function_exists('wp_delete_post')) {
    function wp_delete_post($postid, $force_delete = false) {
        return get_post($postid); // Return the "deleted" post
    }
}

// User functions
if (!function_exists('get_user_by')) {
    function get_user_by($field, $value) {
        return (object) [
            'ID' => 1,
            'user_login' => 'testuser',
            'user_email' => 'test@example.com',
            'display_name' => 'Test User',
            'user_registered' => '2025-01-01 00:00:00'
        ];
    }
}

if (!function_exists('wp_get_current_user')) {
    function wp_get_current_user() {
        return get_user_by('id', 1);
    }
}

if (!function_exists('user_can')) {
    function user_can($user, $capability, $args = null) {
        return true; // Always allow in testing
    }
}

// Meta functions
if (!function_exists('get_post_meta')) {
    function get_post_meta($post_id, $key = '', $single = false) {
        $meta_data = [
            'aca_idea_status' => 'pending',
            'aca_draft_content' => 'Mock draft content',
            'aca_style_guide' => 'Mock style guide'
        ];
        
        if (empty($key)) {
            return $meta_data;
        }
        
        $value = isset($meta_data[$key]) ? $meta_data[$key] : '';
        return $single ? $value : [$value];
    }
}

if (!function_exists('update_post_meta')) {
    function update_post_meta($post_id, $meta_key, $meta_value, $prev_value = '') {
        return true;
    }
}

if (!function_exists('delete_post_meta')) {
    function delete_post_meta($post_id, $meta_key, $meta_value = '') {
        return true;
    }
}

if (!function_exists('get_user_meta')) {
    function get_user_meta($user_id, $key = '', $single = false) {
        $meta_data = [
            'aca_preferences' => ['theme' => 'dark', 'notifications' => true],
            'aca_api_usage' => 150
        ];
        
        if (empty($key)) {
            return $meta_data;
        }
        
        $value = isset($meta_data[$key]) ? $meta_data[$key] : '';
        return $single ? $value : [$value];
    }
}

if (!function_exists('update_user_meta')) {
    function update_user_meta($user_id, $meta_key, $meta_value, $prev_value = '') {
        return true;
    }
}

// Taxonomy functions
if (!function_exists('wp_get_post_categories')) {
    function wp_get_post_categories($post_id = 0, $args = []) {
        return [
            (object) ['term_id' => 1, 'name' => 'Technology', 'slug' => 'technology'],
            (object) ['term_id' => 2, 'name' => 'AI', 'slug' => 'ai']
        ];
    }
}

if (!function_exists('wp_get_post_tags')) {
    function wp_get_post_tags($post_id = 0, $args = []) {
        return [
            (object) ['term_id' => 10, 'name' => 'WordPress', 'slug' => 'wordpress'],
            (object) ['term_id' => 11, 'name' => 'Plugin', 'slug' => 'plugin']
        ];
    }
}

// Media functions
if (!function_exists('wp_get_attachment_image_src')) {
    function wp_get_attachment_image_src($attachment_id, $size = 'thumbnail', $icon = false) {
        return [
            "http://example.com/wp-content/uploads/image-{$attachment_id}.jpg",
            300,
            200,
            true
        ];
    }
}

// Plugin/Theme functions
if (!function_exists('plugin_basename')) {
    function plugin_basename($file) {
        return basename(dirname($file)) . '/' . basename($file);
    }
}

if (!function_exists('register_activation_hook')) {
    function register_activation_hook($file, $function) {
        // Mock activation hook registration
        return true;
    }
}

if (!function_exists('register_deactivation_hook')) {
    function register_deactivation_hook($file, $function) {
        // Mock deactivation hook registration
        return true;
    }
}

if (!function_exists('add_menu_page')) {
    function add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function = '', $icon_url = '', $position = null) {
        return $menu_slug;
    }
}

if (!function_exists('add_submenu_page')) {
    function add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function = '') {
        return $menu_slug;
    }
}

// Settings API
if (!function_exists('register_setting')) {
    function register_setting($option_group, $option_name, $args = []) {
        return true;
    }
}

if (!function_exists('add_settings_section')) {
    function add_settings_section($id, $title, $callback, $page) {
        return true;
    }
}

if (!function_exists('add_settings_field')) {
    function add_settings_field($id, $title, $callback, $page, $section = 'default', $args = []) {
        return true;
    }
}

// AJAX functions
if (!function_exists('wp_ajax_nopriv_')) {
    function wp_ajax_nopriv_($action, $callback) {
        // Mock AJAX hook for non-logged in users
        return add_action("wp_ajax_nopriv_{$action}", $callback);
    }
}

if (!function_exists('wp_send_json_success')) {
    function wp_send_json_success($data = null) {
        echo json_encode(['success' => true, 'data' => $data]);
        exit;
    }
}

if (!function_exists('wp_send_json_error')) {
    function wp_send_json_error($data = null) {
        echo json_encode(['success' => false, 'data' => $data]);
        exit;
    }
}

if (!function_exists('wp_die')) {
    function wp_die($message, $title = '', $args = []) {
        echo "WP_DIE: {$message}";
        exit(1);
    }
}

// Caching functions
if (!function_exists('wp_cache_get')) {
    function wp_cache_get($key, $group = '') {
        return get_transient($key);
    }
}

if (!function_exists('wp_cache_set')) {
    function wp_cache_set($key, $data, $group = '', $expire = 0) {
        return set_transient($key, $data, $expire);
    }
}

if (!function_exists('wp_cache_delete')) {
    function wp_cache_delete($key, $group = '') {
        return delete_transient($key);
    }
}

// Filesystem functions
if (!function_exists('wp_upload_bits')) {
    function wp_upload_bits($name, $deprecated, $bits, $time = null) {
        $upload_dir = wp_upload_dir($time);
        $filename = $upload_dir['path'] . '/' . $name;
        
        return [
            'file' => $filename,
            'url' => $upload_dir['url'] . '/' . $name,
            'type' => 'text/plain',
            'error' => false
        ];
    }
}

// Database table creation
if (!function_exists('dbDelta')) {
    function dbDelta($queries) {
        return ['Created table mock_table'];
    }
}

// Advanced Mock Classes
class MockWP_Query {
    public $posts = [];
    public $post_count = 0;
    public $found_posts = 0;
    public $max_num_pages = 1;
    
    public function __construct($args = []) {
        $this->posts = get_posts($args);
        $this->post_count = count($this->posts);
        $this->found_posts = $this->post_count;
    }
    
    public function have_posts() {
        return $this->post_count > 0;
    }
    
    public function the_post() {
        // Mock the_post functionality
        return true;
    }
}

class MockWP_User {
    public $ID;
    public $user_login;
    public $user_email;
    public $display_name;
    
    public function __construct($user_id = 1) {
        $user = get_user_by('id', $user_id);
        $this->ID = $user->ID;
        $this->user_login = $user->user_login;
        $this->user_email = $user->user_email;
        $this->display_name = $user->display_name;
    }
    
    public function has_cap($capability) {
        return true;
    }
    
    public function add_cap($cap, $grant = true) {
        return true;
    }
    
    public function remove_cap($cap) {
        return true;
    }
}

// Global instances
global $wp_query, $current_user;
$wp_query = new MockWP_Query();
$current_user = new MockWP_User();

// Advanced error simulation
class MockWP_Error extends WP_Error {
    public function __construct($code = '', $message = '', $data = '') {
        parent::__construct($code, $message, $data);
    }
    
    public static function create_mock_error($type = 'generic') {
        switch ($type) {
            case 'api':
                return new self('api_error', 'API request failed', ['status' => 500]);
            case 'database':
                return new self('db_error', 'Database query failed');
            case 'permission':
                return new self('permission_denied', 'Insufficient permissions');
            case 'validation':
                return new self('invalid_data', 'Data validation failed');
            default:
                return new self('generic_error', 'An error occurred');
        }
    }
}

// Performance monitoring
class MockPerformanceMonitor {
    private static $timers = [];
    private static $memory_usage = [];
    
    public static function start_timer($name) {
        self::$timers[$name] = microtime(true);
    }
    
    public static function end_timer($name) {
        if (isset(self::$timers[$name])) {
            return microtime(true) - self::$timers[$name];
        }
        return 0;
    }
    
    public static function log_memory($checkpoint) {
        self::$memory_usage[$checkpoint] = memory_get_usage();
    }
    
    public static function get_memory_diff($start, $end) {
        if (isset(self::$memory_usage[$start]) && isset(self::$memory_usage[$end])) {
            return self::$memory_usage[$end] - self::$memory_usage[$start];
        }
        return 0;
    }
    
    public static function get_stats() {
        return [
            'timers' => self::$timers,
            'memory' => self::$memory_usage
        ];
    }
}

// Mock external API responses
class MockExternalAPI {
    public static function get_gemini_response($prompt) {
        return [
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => "Mock AI response for: {$prompt}"]
                        ]
                    ]
                ]
            ]
        ];
    }
    
    public static function get_gumroad_response($license_key) {
        return [
            'success' => true,
            'purchase' => [
                'product_name' => 'ACA AI Content Agent Pro',
                'sale_id' => 'mock_sale_' . md5($license_key),
                'created_at' => date('Y-m-d H:i:s'),
                'email' => 'customer@example.com'
            ]
        ];
    }
    
    public static function simulate_api_failure($type = 'timeout') {
        switch ($type) {
            case 'timeout':
                return new WP_Error('timeout', 'Request timed out');
            case 'rate_limit':
                return new WP_Error('rate_limit', 'Rate limit exceeded');
            case 'invalid_key':
                return new WP_Error('invalid_key', 'Invalid API key');
            default:
                return new WP_Error('api_error', 'API request failed');
        }
    }
}

// Mock file system operations
class MockFileSystem {
    private static $virtual_files = [];
    
    public static function write_file($path, $content) {
        self::$virtual_files[$path] = $content;
        return true;
    }
    
    public static function read_file($path) {
        return isset(self::$virtual_files[$path]) ? self::$virtual_files[$path] : false;
    }
    
    public static function file_exists($path) {
        return isset(self::$virtual_files[$path]) || file_exists($path);
    }
    
    public static function delete_file($path) {
        unset(self::$virtual_files[$path]);
        return true;
    }
    
    public static function get_virtual_files() {
        return self::$virtual_files;
    }
}

// Initialize performance monitoring
MockPerformanceMonitor::start_timer('plugin_load');
MockPerformanceMonitor::log_memory('initial');