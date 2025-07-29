<?php
/**
 * Plugin Name: AI Content Agent (ACA)
 * Description: AI-powered content creation and management plugin that generates blog posts, ideas, and manages your content workflow automatically.
 * Version: 1.6.2
 * Author: AI Content Agent Team
 * License: GPL v2 or later
 * Text Domain: ai-content-agent
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('ACA_VERSION', '1.6.2');
define('ACA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ACA_PLUGIN_PATH', plugin_dir_path(__FILE__));

// Include required files
require_once ACA_PLUGIN_PATH . 'includes/class-aca-activator.php';
require_once ACA_PLUGIN_PATH . 'includes/class-aca-deactivator.php';
require_once ACA_PLUGIN_PATH . 'includes/class-aca-rest-api.php';
require_once ACA_PLUGIN_PATH . 'includes/class-aca-cron.php';

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
        
        // Prevent jQuery loading on our plugin page
        add_action('admin_enqueue_scripts', array($this, 'dequeue_jquery_on_plugin_page'), 100);
        
        // Add script loader tag modification to prevent jQuery conflicts
        add_filter('script_loader_tag', array($this, 'modify_script_loader_tag'), 10, 3);
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
            'AI Content Agent',
            'AI Content Agent',
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
        
        // Enqueue the compiled React app
        $css_file = ACA_PLUGIN_PATH . 'admin/css/index.css';
        $js_file = ACA_PLUGIN_PATH . 'admin/js/index.js';
        
        $css_version = ACA_VERSION . '-' . (file_exists($css_file) ? filemtime($css_file) : time());
        $js_version = ACA_VERSION . '-' . (file_exists($js_file) ? filemtime($js_file) : time());
        
        wp_enqueue_style('aca-styles', ACA_PLUGIN_URL . 'admin/css/index.css', array(), $css_version);
        // Explicitly set no dependencies to prevent jQuery from being loaded
        wp_enqueue_script('aca-app', ACA_PLUGIN_URL . 'admin/js/index.js', array(), $js_version, true);
        
        // Pass data to React app
        wp_localize_script('aca-app', 'acaData', array(
            'nonce' => wp_create_nonce('wp_rest'),
            'api_url' => rest_url('aca/v1/'),
            'admin_url' => admin_url(),
            'plugin_url' => ACA_PLUGIN_URL,
        ));
    }
    
    /**
     * Dequeue jQuery on plugin page to prevent migrate warnings
     * 
     * This method prevents the jQuery Migrate warning:
     * "JQMIGRATE: Migrate is installed, version 3.4.1"
     * 
     * The warning appears because WordPress automatically loads jQuery and jQuery Migrate
     * when scripts are enqueued in the admin area. Since this plugin uses React and doesn't
     * need jQuery, we dequeue these scripts to prevent unnecessary warnings.
     */
    public function dequeue_jquery_on_plugin_page($hook) {
        // Only dequeue on our plugin page
        if ('toplevel_page_ai-content-agent' == $hook) {
            // Remove jQuery and related scripts to prevent migrate warnings
            wp_dequeue_script('jquery-migrate');
            wp_dequeue_script('jquery-core');
            wp_dequeue_script('jquery');
            wp_dequeue_script('utils');
            wp_dequeue_script('wp-polyfill');
        }
    }
    
    /**
     * Modify script loader tag to prevent jQuery conflicts
     */
    public function modify_script_loader_tag($tag, $handle, $src) {
        // Only modify our plugin script
        if ($handle === 'aca-app') {
            // Add defer attribute to prevent conflicts
            $tag = str_replace(' src', ' defer src', $tag);
        }
        return $tag;
    }
}

// Initialize the plugin
new AI_Content_Agent();