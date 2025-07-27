<?php
/**
 * Plugin Name: ACA - AI Content Agent
 * Plugin URI: https://github.com/your-username/aca-ai-content-agent
 * Description: An intelligent content generation and management assistant for WordPress. ACA analyzes your existing content to learn your site's unique voice and style, then uses that knowledge to generate high-quality, SEO-friendly blog posts, automating content creation while maintaining brand identity.
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: aca-ai-content-agent
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
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

// Include required files
require_once ACA_PLUGIN_PATH . 'includes/class-aca-activator.php';
require_once ACA_PLUGIN_PATH . 'includes/class-aca-deactivator.php';
require_once ACA_PLUGIN_PATH . 'includes/class-aca-core.php';

// Activation and deactivation hooks
register_activation_hook(__FILE__, array('ACA_Activator', 'activate'));
register_deactivation_hook(__FILE__, array('ACA_Deactivator', 'deactivate'));

// Initialize the plugin
function run_aca_ai_content_agent() {
    $plugin = new ACA_Core();
    $plugin->run();
}

// Start the plugin
run_aca_ai_content_agent();