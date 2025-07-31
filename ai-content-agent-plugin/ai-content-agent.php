<?php
/**
 * Plugin Name: AI Content Agent - Enterprise Edition
 * Plugin URI: https://github.com/ademisler/ai-content-agent
 * Description: Enterprise-grade AI-powered WordPress content creation plugin with advanced automation, mobile responsiveness, internationalization, and robust error recovery. Features lazy loading, circuit breaker patterns, and graceful degradation for maximum reliability.
 * Version: 2.3.14
 * Author: Adem Isler
 * Author URI: https://github.com/ademisler
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ai-content-agent
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Network: false
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants with safety checks
if (!defined('ACA_VERSION')) {
    define('ACA_VERSION', '2.3.14');
}
if (!defined('ACA_PLUGIN_DIR')) {
    define('ACA_PLUGIN_DIR', plugin_dir_path(__FILE__));
}
if (!defined('ACA_PLUGIN_URL')) {
    define('ACA_PLUGIN_URL', plugin_dir_url(__FILE__));
}
if (!defined('ACA_PLUGIN_FILE')) {
    define('ACA_PLUGIN_FILE', __FILE__);
}
if (!defined('ACA_PLUGIN_PATH')) {
    define('ACA_PLUGIN_PATH', plugin_dir_path(__FILE__));
}

// CRITICAL FIX: Load Composer autoloader to prevent fatal errors
$autoloader_path = ACA_PLUGIN_DIR . 'vendor/autoload.php';
if (file_exists($autoloader_path)) {
    require_once $autoloader_path;
} else {
    // Log missing autoloader for debugging
    error_log('ACA Plugin Warning: Composer autoloader not found at ' . $autoloader_path);
}

// Note: is_aca_pro_active() function is defined in includes/class-aca-licensing.php

// Load optimization managers for better performance
// Load interfaces first
if (file_exists(ACA_PLUGIN_DIR . "includes/interfaces/class-aca-cache-interface.php")) {
    require_once ACA_PLUGIN_DIR . "includes/interfaces/class-aca-cache-interface.php";
}
if (file_exists(ACA_PLUGIN_DIR . "includes/interfaces/class-aca-performance-interface.php")) {
    require_once ACA_PLUGIN_DIR . "includes/interfaces/class-aca-performance-interface.php";
}
if (file_exists(ACA_PLUGIN_DIR . "includes/interfaces/class-aca-cleanup-interface.php")) {
if (file_exists(ACA_PLUGIN_DIR . "includes/interfaces/class-aca-container-interface.php")) {
    require_once ACA_PLUGIN_DIR . "includes/interfaces/class-aca-container-interface.php";
}
    require_once ACA_PLUGIN_DIR . "includes/interfaces/class-aca-cleanup-interface.php";
if (file_exists(ACA_PLUGIN_DIR . "includes/interfaces/class-aca-container-interface.php")) {
    require_once ACA_PLUGIN_DIR . "includes/interfaces/class-aca-container-interface.php";
}
}

if (file_exists(ACA_PLUGIN_DIR . 'includes/class-aca-file-manager.php')) {
    require_once ACA_PLUGIN_DIR . 'includes/class-aca-file-manager.php';
}
if (file_exists(ACA_PLUGIN_DIR . 'includes/class-aca-hook-manager.php')) {
    require_once ACA_PLUGIN_DIR . 'includes/class-aca-hook-manager.php';
}

// Include required files with optimized file handling
$required_files = [
    'includes/class-aca-licensing.php',
    'includes/class-aca-activator.php',
    'includes/class-aca-deactivator.php',
    'includes/class-aca-rest-api.php',
    'includes/class-aca-cron.php',
    'includes/class-aca-content-freshness.php',
    'includes/class-aca-rate-limiter.php',
    'includes/class-aca-performance-monitor.php',
    'includes/class-aca-google-search-console-hybrid.php',
    'includes/gsc-data-fix.php'
];

// Preload file existence checks for better performance
if (class_exists('ACA_File_Manager')) {
    $full_paths = array_map(function($file) {
        return ACA_PLUGIN_DIR . $file;
    }, $required_files);
    ACA_File_Manager::preload_files($full_paths);
}

foreach ($required_files as $file) {
    $file_path = ACA_PLUGIN_DIR . $file;
    if (class_exists('ACA_File_Manager')) {
        if (!ACA_File_Manager::require_once_safe($file_path)) {
            error_log("ACA Plugin: Failed to load required file: $file_path");
        }
    } else {
        // Fallback to standard file operations
        if (file_exists($file_path)) {
            require_once $file_path;
        } else {
            error_log("ACA Plugin: Missing required file: $file_path");
        }
    }
}

// Activation and deactivation hooks with class existence checks
register_activation_hook(__FILE__, function() {
    if (class_exists('ACA_Activator')) {
        ACA_Activator::activate();
    } else {
        error_log('ACA Plugin: ACA_Activator class not found during activation');
    }
});

register_deactivation_hook(__FILE__, function() {
    if (class_exists('ACA_Deactivator')) {
        ACA_Deactivator::deactivate();
    } else {
        error_log('ACA Plugin: ACA_Deactivator class not found during deactivation');
    }
});

// Migration hook for demo mode transition
register_activation_hook(__FILE__, 'aca_migrate_from_demo_mode');

/**
 * Migrate from demo mode to proper licensing
 */
function aca_migrate_from_demo_mode() {
    $current_status = get_option('aca_license_status');
    $license_key = get_option('aca_license_key');
    
    // If in demo mode (active status but no license key), reset
    if ($current_status === 'active' && empty($license_key)) {
        update_option('aca_license_status', 'inactive');
        
        // Add migration notice
        add_option('aca_demo_migration_notice', array(
            'message' => 'AI Content Agent has been migrated from demo mode. Please enter a valid Pro license key to access premium features.',
            'timestamp' => current_time('mysql'),
            'dismissed' => false
        ));
        
        // Log migration
        error_log('ACA: Plugin migrated from demo mode to proper licensing system on activation');
    }
}

/**
 * Main plugin class
 */
class AI_Content_Agent {
    
    private static $instance = null;
    private static $initialized_classes = [];
    private static $memory_threshold = 0.8; // 80% of memory limit
    private static $performance_tracker = null;
    
    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        // Memory check before initialization
        if (!$this->check_memory_availability()) {
            error_log('ACA Plugin: Insufficient memory available for initialization');
            return;
        }
        
        // Start performance tracking
        if (class_exists('ACA_Performance_Monitor')) {
            self::$performance_tracker = ACA_Performance_Monitor::start('plugin_initialization');
        }
        
        $this->init_hooks();
        $this->init();
    }
    
    /**
     * Prevent cloning of the instance
     */
    private function __clone() {}
    
    /**
     * Prevent unserialization of the instance
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
    
    /**
     * Get singleton instance
     * 
     * @return AI_Content_Agent
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Destroy singleton instance (for testing purposes)
     */
    public static function destroy_instance() {
        if (self::$instance !== null) {
            // Clean up performance tracking
            if (self::$performance_tracker) {
                ACA_Performance_Monitor::end(self::$performance_tracker);
                self::$performance_tracker = null;
            }
            
            // Clean up initialized classes
            self::$initialized_classes = [];
            
            // Destroy instance
            self::$instance = null;
        }
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        // Ensure WordPress is loaded before initializing
        if (function_exists('add_action')) {
            // WordPress is already loaded, proceed immediately
            return;
        } else {
            // WordPress not loaded yet, try later
            add_action('wp_loaded', array($this, 'init'));
        }
    }
    
    /**
     * Check if sufficient memory is available
     * 
     * @return bool
     */
    private function check_memory_availability() {
        $memory_limit = $this->get_memory_limit_bytes();
        if ($memory_limit === false) {
            return true; // Can't determine limit, proceed
        }
        
        $current_usage = memory_get_usage(true);
        $usage_ratio = $current_usage / $memory_limit;
        
        if ($usage_ratio > self::$memory_threshold) {
            error_log("ACA Plugin: Memory usage too high: " . round($usage_ratio * 100, 2) . "%");
            return false;
        }
        
        return true;
    }
    
    /**
     * Get memory limit in bytes
     * 
     * @return int|false
     */
    private function get_memory_limit_bytes() {
        $memory_limit = ini_get('memory_limit');
        if ($memory_limit === '-1') {
            return false; // Unlimited
        }
        
        $value = (int) $memory_limit;
        $unit = strtolower(substr($memory_limit, -1));
        
        switch ($unit) {
            case 'g':
                $value *= 1024 * 1024 * 1024;
                break;
            case 'm':
                $value *= 1024 * 1024;
                break;
            case 'k':
                $value *= 1024;
                break;
        }
        
        return $value;
    }
    
    /**
     * Lazy load class instances with dependency injection
     * 
     * @param string $class_name
     * @param array $dependencies
     * @return object|null
     */
    private function get_class_instance($class_name, $dependencies = []) {
        if (isset(self::$initialized_classes[$class_name])) {
            return self::$initialized_classes[$class_name];
        }
        
        if (!class_exists($class_name)) {
            error_log("ACA Plugin: Class $class_name not found");
            return null;
        }
        
        // Check memory before instantiation
        if (!$this->check_memory_availability()) {
            error_log("ACA Plugin: Skipping $class_name initialization due to memory constraints");
            return null;
        }
        
        try {
            // Use dependency injection if service container is available
            if (class_exists('ACA_Service_Container')) {
                self::$initialized_classes[$class_name] = ACA_Service_Container::make($class_name, $dependencies);
            } else {
                // Fallback to simple instantiation
                self::$initialized_classes[$class_name] = new $class_name();
            }
            
            return self::$initialized_classes[$class_name];
        } catch (Error $e) {
            error_log("ACA Plugin: Failed to initialize $class_name: " . $e->getMessage());
            return null;
        }
    }
    
    public function init() {
        try {
            // Load file manager for optimized file operations
            if (file_exists(ACA_PLUGIN_DIR . 'includes/class-aca-file-manager.php')) {
                require_once ACA_PLUGIN_DIR . 'includes/class-aca-file-manager.php';
            }
            
            // Initialize dependency installer with optimized file operations
            if (class_exists('ACA_File_Manager')) {
                ACA_File_Manager::require_once_safe(ACA_PLUGIN_DIR . 'install-dependencies.php');
            } else if (file_exists(ACA_PLUGIN_DIR . 'install-dependencies.php')) {
                require_once ACA_PLUGIN_DIR . 'install-dependencies.php';
            }
            
            // Initialize REST API with lazy loading
            $rest_api = $this->get_class_instance('ACA_Rest_Api');
            
            // Initialize Cron jobs with lazy loading
            $cron = $this->get_class_instance('ACA_Cron');
            
            // Handle Google Search Console OAuth callback
            add_action('admin_init', array($this, 'handle_gsc_oauth_callback'));
            
            // Add admin menu
            add_action('admin_menu', array($this, 'add_admin_menu'));
            
            // Enqueue admin scripts (only when needed)
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
            
            // End performance tracking
            if (self::$performance_tracker) {
                $metrics = ACA_Performance_Monitor::end(self::$performance_tracker);
                if ($metrics && isset($metrics['memory_used_mb']) && $metrics['memory_used_mb'] > 10) {
                    error_log("ACA Plugin: High memory usage during initialization: " . $metrics['memory_used_mb'] . "MB");
                }
            }
            
        } catch (Error $e) {
            error_log('ACA Plugin Init Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            if (self::$performance_tracker) {
                ACA_Performance_Monitor::end(self::$performance_tracker, ['error' => $e->getMessage()]);
            }
        }
    }
    
    /**
     * Handle Google Search Console OAuth callback
     */
    public function handle_gsc_oauth_callback() {
        // Only initialize GSC when actually needed
        if (!isset($_GET['code']) || !isset($_GET['state'])) {
            return;
        }
        
        if (!$this->check_memory_availability()) {
            wp_die('Insufficient memory to handle OAuth callback');
            return;
        }
        
        try {
            $gsc = $this->get_class_instance('ACA_Google_Search_Console_Hybrid');
            if ($gsc && method_exists($gsc, 'handle_oauth_callback')) {
                $gsc->handle_oauth_callback();
            }
        } catch (Error $e) {
            error_log('ACA Plugin GSC OAuth Error: ' . $e->getMessage());
            wp_die('OAuth callback failed. Please try again.');
        }
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            'AI Content Agent (ACA)',
            'AI Content Agent (ACA)',
            'manage_options',
            'ai-content-agent',
            array($this, 'admin_page'),
            'dashicons-edit-large',
            30
        );
    }
    
    /**
     * Admin page callback
     */
    public function admin_page() {
        // Load dependency installer if not already loaded
        if (!class_exists('ACA_Dependencies_Installer')) {
            require_once ACA_PLUGIN_DIR . 'install-dependencies.php';
        }
        
        // Display dependency status if needed
        if (!ACA_Dependencies_Installer::are_dependencies_installed()) {
            echo '<div class="wrap">';
            ACA_Dependencies_Installer::display_dependency_status();
            echo '</div>';
        }
        
        echo '<div id="root"></div>';
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        // Only load on our plugin page
        if ('toplevel_page_ai-content-agent' != $hook) {
            return;
        }
        
        // Find the latest built JS file in admin/assets
        $assets_dir = ACA_PLUGIN_DIR . 'admin/assets/';
        
        // Check if assets directory exists
        if (!is_dir($assets_dir)) {
            // Fallback to old files if assets directory doesn't exist
            $this->enqueue_fallback_assets();
            return;
        }
        
        $js_files = glob($assets_dir . 'index-*.js');
        
        if (!empty($js_files)) {
            // Get the most recent JS file by modification time
            $latest_file = '';
            $latest_time = 0;
            
            foreach ($js_files as $file) {
                // Validate file exists and is readable
                if (!file_exists($file) || !is_readable($file)) {
                    continue;
                }
                
                $file_time = filemtime($file);
                if ($file_time > $latest_time) {
                    $latest_time = $file_time;
                    $latest_file = $file;
                }
            }
            
            if ($latest_file && file_exists($latest_file)) {
                $js_filename = basename($latest_file);
                $js_version = ACA_VERSION . '-' . $latest_time;
                $script_handle = 'aca-app-' . md5($js_filename . $latest_time);
                
                // Enqueue the compiled React app (CSS is inlined in JS)
                wp_enqueue_script($script_handle, ACA_PLUGIN_URL . 'admin/assets/' . $js_filename, array(), $js_version, true);
                
                // Pass data to React app
                wp_localize_script($script_handle, 'acaData', array(
                    'nonce' => wp_create_nonce('wp_rest'),
                    'api_url' => rest_url('aca/v1/'),
                    'admin_url' => admin_url(),
                    'plugin_url' => ACA_PLUGIN_URL,
                ));
            } else {
                // Fallback if no valid file found
                $this->enqueue_fallback_assets();
            }
        } else {
            // Fallback to old files if new build doesn't exist
            $this->enqueue_fallback_assets();
        }
    }
    
    /**
     * Enqueue fallback assets with proper validation
     */
    private function enqueue_fallback_assets() {
        $css_file = ACA_PLUGIN_DIR . 'admin/css/index.css';
        $js_file = ACA_PLUGIN_DIR . 'admin/js/index.js';
        
        // Validate fallback files exist using optimized file operations
        $js_exists = class_exists('ACA_File_Manager') ? 
            ACA_File_Manager::file_exists_cached($js_file) : 
            file_exists($js_file);
            
        if (!$js_exists || !is_readable($js_file)) {
            // Log error and show admin notice
            error_log('ACA Error: No valid JavaScript assets found');
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>AI Content Agent: JavaScript assets are missing. Please rebuild the plugin.</p></div>';
            });
            return;
        }
        
        // Use optimized file modification time checks
        $css_mtime = class_exists('ACA_File_Manager') ? 
            ACA_File_Manager::filemtime_cached($css_file) : 
            (file_exists($css_file) ? filemtime($css_file) : time());
            
        $js_mtime = class_exists('ACA_File_Manager') ? 
            ACA_File_Manager::filemtime_cached($js_file) : 
            filemtime($js_file);
            
        $css_version = ACA_VERSION . '-' . ($css_mtime ?: time());
        $js_version = ACA_VERSION . '-' . ($js_mtime ?: time());
        $fallback_handle = 'aca-app-fallback-' . md5($js_version);
        
        // Only enqueue CSS if it exists
        if (file_exists($css_file) && is_readable($css_file)) {
            wp_enqueue_style('aca-styles', ACA_PLUGIN_URL . 'admin/css/index.css', array(), $css_version);
        }
        
        wp_enqueue_script($fallback_handle, ACA_PLUGIN_URL . 'admin/js/index.js', array(), $js_version, true);
        
        // Pass data to React app
        wp_localize_script($fallback_handle, 'acaData', array(
            'nonce' => wp_create_nonce('wp_rest'),
            'api_url' => rest_url('aca/v1/'),
            'admin_url' => admin_url(),
            'plugin_url' => ACA_PLUGIN_URL,
        ));
    }
}

// Initialize the plugin when WordPress is ready using singleton pattern
if (function_exists('add_action')) {
    // WordPress is loaded, initialize immediately
    AI_Content_Agent::get_instance();
    
    // Hook cron events with class existence checks
    if (class_exists('ACA_Cron')) {
        add_action('aca_thirty_minute_event', array('ACA_Cron', 'thirty_minute_task'));
        add_action('aca_fifteen_minute_event', array('ACA_Cron', 'fifteen_minute_task'));
    }
} else {
    // WordPress not loaded yet, wait for it
    add_action('plugins_loaded', function() {
        AI_Content_Agent::get_instance();
        
        // Hook cron events with class existence checks
        if (class_exists('ACA_Cron')) {
            add_action('aca_thirty_minute_event', array('ACA_Cron', 'thirty_minute_task'));
            add_action('aca_fifteen_minute_event', array('ACA_Cron', 'fifteen_minute_task'));
        }
    });
}
