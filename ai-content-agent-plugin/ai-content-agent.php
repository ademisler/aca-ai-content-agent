<?php
/**
 * Plugin Name: AI Content Agent (ACA)
 * Plugin URI: https://ademisler.gumroad.com/l/ai-content-agent-pro
 * Description: AI-powered content creation and management plugin that generates blog posts, ideas, and manages your content workflow automatically with Google Search Console integration and Pro features.
 * Version: 2.1.2
 * Author: Adem Isler
 * Author URI: https://ademisler.com/en
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
define('ACA_VERSION', '2.1.2');
define('ACA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ACA_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * Check if ACA Pro is active
 * 
 * @return bool True if pro license is active, false otherwise
 */
function is_aca_pro_active() {
    return get_option('aca_license_status') === 'active';
}

// Include required files
require_once ACA_PLUGIN_PATH . 'includes/class-aca-activator.php';
require_once ACA_PLUGIN_PATH . 'includes/class-aca-deactivator.php';
require_once ACA_PLUGIN_PATH . 'includes/class-aca-rest-api.php';
require_once ACA_PLUGIN_PATH . 'includes/class-aca-cron.php';
require_once ACA_PLUGIN_PATH . 'includes/class-aca-content-freshness.php';

// Activation and deactivation hooks
register_activation_hook(__FILE__, array('ACA_Activator', 'activate'));
register_deactivation_hook(__FILE__, array('ACA_Deactivator', 'deactivate'));

/**
 * Main plugin class
 */
class AI_Content_Agent {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
    }
    
    public function init() {
        // Initialize dependency installer
        require_once ACA_PLUGIN_PATH . 'install-dependencies.php';
        
        // Initialize REST API
        new ACA_Rest_Api();
        
        // Initialize Cron jobs
        new ACA_Cron();
        
        // Handle Google Search Console OAuth callback
        add_action('admin_init', array($this, 'handle_gsc_oauth_callback'));
        
        // Add admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Enqueue admin scripts
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    /**
     * Handle Google Search Console OAuth callback
     */
    public function handle_gsc_oauth_callback() {
        if (isset($_GET['page']) && $_GET['page'] === 'ai-content-agent' && 
            isset($_GET['gsc_auth']) && $_GET['gsc_auth'] === 'callback' && 
            isset($_GET['code'])) {
            
            require_once ACA_PLUGIN_PATH . 'includes/class-aca-google-search-console.php';
            
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
        $assets_dir = ACA_PLUGIN_PATH . 'admin/assets/';
        $js_files = glob($assets_dir . 'index-*.js');
        
        if (!empty($js_files)) {
            // Get the most recent JS file by modification time
            $latest_file = '';
            $latest_time = 0;
            
            foreach ($js_files as $file) {
                $file_time = filemtime($file);
                if ($file_time > $latest_time) {
                    $latest_time = $file_time;
                    $latest_file = $file;
                }
            }
            
            if ($latest_file) {
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
            }
        } else {
            // Fallback to old files if new build doesn't exist
            $css_file = ACA_PLUGIN_PATH . 'admin/css/index.css';
            $js_file = ACA_PLUGIN_PATH . 'admin/js/index.js';
            
            $css_version = ACA_VERSION . '-' . (file_exists($css_file) ? filemtime($css_file) : time());
            $js_version = ACA_VERSION . '-' . (file_exists($js_file) ? filemtime($js_file) : time());
            $fallback_handle = 'aca-app-fallback-' . md5($js_version);
            
            wp_enqueue_style('aca-styles', ACA_PLUGIN_URL . 'admin/css/index.css', array(), $css_version);
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
}

// Initialize the plugin
new AI_Content_Agent();

// Hook cron events
add_action('aca_thirty_minute_event', array('ACA_Cron', 'thirty_minute_task'));
add_action('aca_fifteen_minute_event', array('ACA_Cron', 'fifteen_minute_task'));