<?php
/**
 * Plugin Name: AI Content Agent (ACA)
 * Description: AI-powered content creation and management plugin that generates blog posts, ideas, and manages your content workflow automatically.
 * Version: 1.3.8
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
define('ACA_VERSION', '1.3.8');
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
        // Initialize REST API
        new ACA_Rest_Api();
        
        // Initialize Cron jobs
        new ACA_Cron();
        
        // Add admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Enqueue admin scripts
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
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
        wp_enqueue_style('aca-styles', ACA_PLUGIN_URL . 'admin/css/index.css', array(), ACA_VERSION . '-' . filemtime(ACA_PLUGIN_PATH . 'admin/css/index.css'));
        wp_enqueue_script('aca-app', ACA_PLUGIN_URL . 'admin/js/index.js', array(), ACA_VERSION . '-' . filemtime(ACA_PLUGIN_PATH . 'admin/js/index.js'), true);
        
        // Pass data to React app
        wp_localize_script('aca-app', 'aca_object', array(
            'nonce' => wp_create_nonce('wp_rest'),
            'api_url' => rest_url('aca/v1/'),
            'admin_url' => admin_url(),
            'plugin_url' => ACA_PLUGIN_URL,
        ));
    }
}

// Initialize the plugin
new AI_Content_Agent();