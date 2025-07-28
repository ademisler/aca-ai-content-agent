<?php
/**
 * Plugin Name: ACA - AI Content Agent
 * Plugin URI: https://github.com/your-repo/aca-ai-content-agent
 * Description: An AI-powered content creation and management system for WordPress with automated idea generation, draft creation, and publishing workflows.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: aca-ai-content-agent
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
define('ACA_PLUGIN_VERSION', '1.0.0');
define('ACA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ACA_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('ACA_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main ACA Plugin Class
 */
class ACA_AI_Content_Agent {
    
    /**
     * Initialize the plugin
     */
    public function __construct() {
        add_action('init', array($this, 'init'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Initialize plugin functionality
     */
    public function init() {
        // Load includes
        $this->load_includes();
        
        // Initialize REST API
        add_action('rest_api_init', array($this, 'register_rest_routes'));
        
        // Add admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Enqueue admin scripts
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    /**
     * Load required files
     */
    private function load_includes() {
        require_once ACA_PLUGIN_PATH . 'includes/class-aca-database.php';
        require_once ACA_PLUGIN_PATH . 'includes/class-aca-rest-api.php';
        require_once ACA_PLUGIN_PATH . 'includes/class-aca-cron.php';
        require_once ACA_PLUGIN_PATH . 'includes/class-aca-gemini-service.php';
        require_once ACA_PLUGIN_PATH . 'includes/class-aca-stock-photo-service.php';
    }
    
    /**
     * Plugin activation hook
     */
    public function activate() {
        // Create custom database tables
        ACA_Database::create_tables();
        
        // Set default settings
        $default_settings = array(
            'mode' => 'manual',
            'autoPublish' => false,
            'searchConsoleUser' => null,
            'imageSourceProvider' => 'ai',
            'aiImageStyle' => 'photorealistic',
            'geminiApiKey' => '',
            'pexelsApiKey' => '',
            'unsplashApiKey' => '',
            'pixabayApiKey' => '',
            'seoPlugin' => 'none'
        );
        add_option('aca_settings', $default_settings);
        
        // Initialize empty style guide
        add_option('aca_style_guide', null);
        
        // Schedule cron jobs
        ACA_Cron::schedule_jobs();
        
        // Flush rewrite rules for REST API
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation hook
     */
    public function deactivate() {
        // Clear all cron jobs
        ACA_Cron::clear_jobs();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        $rest_api = new ACA_REST_API();
        $rest_api->register_routes();
    }
    
    /**
     * Add admin menu page
     */
    public function add_admin_menu() {
        add_menu_page(
            'AI Content Agent',
            'AI Content Agent',
            'manage_options',
            'aca-content-agent',
            array($this, 'render_admin_page'),
            'dashicons-edit-page',
            30
        );
    }
    
    /**
     * Render the main admin page (React SPA container)
     */
    public function render_admin_page() {
        echo '<div id="aca-react-app"></div>';
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        // Only load on our plugin page
        if ($hook !== 'toplevel_page_aca-content-agent') {
            return;
        }
        
        // Enqueue compiled React app
        wp_enqueue_script(
            'aca-react-app',
            ACA_PLUGIN_URL . 'build/main.js',
            array(),
            ACA_PLUGIN_VERSION,
            true
        );
        
        wp_enqueue_style(
            'aca-react-app',
            ACA_PLUGIN_URL . 'build/main.css',
            array(),
            ACA_PLUGIN_VERSION
        );
        
        // Localize script with essential data
        wp_localize_script('aca-react-app', 'acaData', array(
            'restUrl' => get_rest_url(null, 'aca/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
            'adminUrl' => admin_url(),
            'pluginUrl' => ACA_PLUGIN_URL
        ));
    }
}

// Initialize the plugin
new ACA_AI_Content_Agent();