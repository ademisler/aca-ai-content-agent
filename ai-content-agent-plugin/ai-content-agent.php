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

// Define plugin constants
define('ACA_VERSION', '2.3.14');
define('ACA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ACA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ACA_PLUGIN_FILE', __FILE__);
define('ACA_PLUGIN_PATH', plugin_dir_path(__FILE__)); // CRITICAL FIX: Missing constant

/**
 * Check if ACA Pro is active - FIXED VERSION
 * 
 * @return bool True if pro license is active, false otherwise
 */
function is_aca_pro_active() {
    // Use proper licensing system if available
    if (class_exists('ACA_Licensing')) {
        $licensing = new ACA_Licensing();
        return $licensing->is_pro_active();
    }
    
    // Fallback: require both status and license key (no more demo mode)
    $status = get_option('aca_license_status', 'inactive');
    $license_key = get_option('aca_license_key', '');
    
    return ($status === 'active' && !empty($license_key));
}

// Include required files with error handling
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

foreach ($required_files as $file) {
    $file_path = ACA_PLUGIN_DIR . $file;
    if (file_exists($file_path)) {
        require_once $file_path;
    } else {
        error_log("ACA Plugin: Missing required file: $file_path");
    }
}

// Activation and deactivation hooks
register_activation_hook(__FILE__, array('ACA_Activator', 'activate'));
register_deactivation_hook(__FILE__, array('ACA_Deactivator', 'deactivate'));

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
    
    public function __construct() {
        // Ensure WordPress is loaded before initializing
        if (function_exists('add_action')) {
            add_action('init', array($this, 'init'));
        } else {
            // WordPress not loaded yet, try later
            add_action('wp_loaded', array($this, 'init'));
        }
    }
    
    public function init() {
        try {
            // Initialize dependency installer
            if (file_exists(ACA_PLUGIN_DIR . 'install-dependencies.php')) {
                require_once ACA_PLUGIN_DIR . 'install-dependencies.php';
            }
            
            // Initialize REST API with error handling
            if (class_exists('ACA_Rest_Api')) {
                new ACA_Rest_Api();
            }
            
            // Initialize Cron jobs with error handling
            if (class_exists('ACA_Cron')) {
                new ACA_Cron();
            }
            
            // Handle Google Search Console OAuth callback
            add_action('admin_init', array($this, 'handle_gsc_oauth_callback'));
            
            // Add admin menu
            add_action('admin_menu', array($this, 'add_admin_menu'));
            
            // Enqueue admin scripts
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
            
        } catch (Error $e) {
            error_log('ACA Plugin Init Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            add_action('admin_notices', function() use ($e) {
                echo '<div class="notice notice-error"><p><strong>AI Content Agent Error:</strong> ' . esc_html($e->getMessage()) . '</p></div>';
            });
        } catch (Exception $e) {
            error_log('ACA Plugin Init Exception: ' . $e->getMessage());
            add_action('admin_notices', function() use ($e) {
                echo '<div class="notice notice-error"><p><strong>AI Content Agent Exception:</strong> ' . esc_html($e->getMessage()) . '</p></div>';
            });
        }
    }
    
    /**
     * Handle Google Search Console OAuth callback
     */
    public function handle_gsc_oauth_callback() {
        if (isset($_GET['page']) && $_GET['page'] === 'ai-content-agent' && 
            isset($_GET['gsc_auth']) && $_GET['gsc_auth'] === 'callback' && 
            isset($_GET['code'])) {
            
            require_once ACA_PLUGIN_DIR . 'includes/class-aca-google-search-console.php';
            
            $gsc = new ACA_Google_Search_Console();
            $result = $gsc->handle_oauth_callback($_GET['code']);
            
            if (is_wp_error($result)) {
                wp_die('Google Search Console authentication failed: ' . $result->get_error_message());
            } else {
                // Redirect back to settings page
                wp_redirect(admin_url('admin.php?page=ai-content-agent&view=settings&gsc_connected=1'));
                exit;
            }
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
        
        // Validate fallback files exist
        if (!file_exists($js_file) || !is_readable($js_file)) {
            // Log error and show admin notice
            error_log('ACA Error: No valid JavaScript assets found');
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>AI Content Agent: JavaScript assets are missing. Please rebuild the plugin.</p></div>';
            });
            return;
        }
        
        $css_version = ACA_VERSION . '-' . (file_exists($css_file) ? filemtime($css_file) : time());
        $js_version = ACA_VERSION . '-' . filemtime($js_file);
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

// Initialize the plugin when WordPress is ready
if (function_exists('add_action')) {
    // WordPress is loaded, initialize immediately
    new AI_Content_Agent();
    
    // Hook cron events
    add_action('aca_thirty_minute_event', array('ACA_Cron', 'thirty_minute_task'));
    add_action('aca_fifteen_minute_event', array('ACA_Cron', 'fifteen_minute_task'));
} else {
    // WordPress not loaded yet, wait for it
    add_action('plugins_loaded', function() {
        new AI_Content_Agent();
        
        // Hook cron events
        add_action('aca_thirty_minute_event', array('ACA_Cron', 'thirty_minute_task'));
        add_action('aca_fifteen_minute_event', array('ACA_Cron', 'fifteen_minute_task'));
    });
}
