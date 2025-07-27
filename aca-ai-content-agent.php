<?php
/**
 * Plugin Name: ACA - AI Content Agent
 * Plugin URI: https://ademisler.com
 * Description: ACA is an intelligent WordPress plugin that learns your existing content's tone and style to autonomously generate high-quality, SEO-friendly new posts.
 * Version: 1.3
 * Author: Adem Isler
 * Author URI: https://ademisler.com
 * Author Email: idemasler@gmail.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: aca-ai-content-agent
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define constants
define( 'ACA_AI_CONTENT_AGENT_VERSION', '1.3' );
define( 'ACA_AI_CONTENT_AGENT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ACA_AI_CONTENT_AGENT_PLUGIN_FILE', __FILE__ );

// Gumroad Product ID for Pro license verification
define( 'ACA_AI_CONTENT_AGENT_GUMROAD_PRODUCT_ID', 'aca-ai-content-agent-pro' );

// Developer/Testing mode constant - DISABLED FOR PRODUCTION
if (!defined('ACA_AI_CONTENT_AGENT_DEV_MODE')) {
    define('ACA_AI_CONTENT_AGENT_DEV_MODE', false); // Set to false for production
}

// SECURITY CHECK: Prevent developer mode in production
if (defined('ACA_AI_CONTENT_AGENT_DEV_MODE') && ACA_AI_CONTENT_AGENT_DEV_MODE) {
    // Check if this is a production environment
    if (defined('WP_ENVIRONMENT_TYPE') && WP_ENVIRONMENT_TYPE === 'production') {
        // Log the security violation
        error_log('SECURITY WARNING: ACA_AI_CONTENT_AGENT_DEV_MODE is enabled in production environment!');
        
        // Add admin notice if in admin area
        if (function_exists('add_action') && function_exists('is_admin') && is_admin()) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p><strong>SECURITY WARNING:</strong> ACA AI Content Agent developer mode was disabled in production environment for security reasons.</p></div>';
            });
        }
        
        // Force disable developer mode in production by overriding the constant
        // Note: We can't redefine constants, so we'll use a different approach
        add_filter('aca_ai_content_agent_dev_mode_enabled', '__return_false');
    }
}

// Load Composer autoloader
if (file_exists(plugin_dir_path(__FILE__) . 'vendor/autoload.php')) {
    require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';
}

// Load main plugin class
require_once plugin_dir_path(__FILE__) . 'includes/class-aca-plugin.php';

/**
 * Activation hook.
 */
function aca_ai_content_agent_activate() {
    try {
        require_once plugin_dir_path(__FILE__) . 'includes/core/class-aca-activator.php';
        ACA_Activator::activate();
    } catch (Exception $e) {
        // Log the error
        error_log('ACA AI Content Agent activation failed: ' . $e->getMessage());
        
        // Show error to user
        wp_die(
            '<h1>Plugin Activation Error</h1>' .
            '<p>ACA AI Content Agent could not be activated due to an error:</p>' .
            '<p><strong>' . esc_html($e->getMessage()) . '</strong></p>' .
            '<p>Please check your error logs for more details.</p>' .
            '<p><a href="' . admin_url('plugins.php') . '">Return to plugins page</a></p>'
        );
    }
}

/**
 * Deactivation hook.
 */
function aca_ai_content_agent_deactivate() {
    require_once plugin_dir_path(__FILE__) . 'includes/core/class-aca-deactivator.php';
    ACA_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'aca_ai_content_agent_activate');
register_deactivation_hook(__FILE__, 'aca_ai_content_agent_deactivate');

/**
 * Initialize the plugin.
 */
function aca_ai_content_agent_init() {
    try {
        // Initialize the main plugin instance
        return ACA_Plugin::instance();
    } catch (Exception $e) {
        // Log the error
        error_log('ACA AI Content Agent initialization failed: ' . $e->getMessage());
        
        // Show admin notice
        if (is_admin()) {
            add_action('admin_notices', function() use ($e) {
                echo '<div class="notice notice-error"><p><strong>ACA AI Content Agent Error:</strong> ' . esc_html($e->getMessage()) . '</p></div>';
            });
        }
        
        return false;
    }
}

// Initialize plugin after WordPress is loaded
add_action('plugins_loaded', 'aca_ai_content_agent_init');

/**
 * Wrapper function to encrypt data using the plugin's utility class.
 */
function aca_ai_content_agent_encrypt($data) {
    if (class_exists('ACA_Encryption_Util')) {
        return ACA_Encryption_Util::encrypt($data);
    }
    return $data;
}

/**
 * Wrapper function to decrypt data.
 */
function aca_ai_content_agent_decrypt($data) {
    if (class_exists('ACA_Encryption_Util')) {
        return ACA_Encryption_Util::decrypt($data);
    }
    return $data;
}

/**
 * Wrapper to safely decrypt data, falling back to the raw value on failure.
 */
function aca_ai_content_agent_safe_decrypt($data) {
    if (class_exists('ACA_Encryption_Util')) {
        return ACA_Encryption_Util::safe_decrypt($data);
    }
    return $data;
}

/**
 * Determine if ACA Pro is active.
 */
function aca_ai_content_agent_is_pro() {
    if (class_exists('ACA_Helper')) {
        return ACA_Helper::is_pro();
    }
    return false;
}
