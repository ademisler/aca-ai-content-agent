<?php
/**
 * Plugin Name: ACA - AI Content Agent
 * Plugin URI:  https://yourwebsite.com/aca-ai-content-agent
 * Description: A WordPress plugin that uses AI to generate content ideas and drafts based on your existing content.
 * Version:     1.2
 * Author:      Your Name
 * Author URI:  https://yourwebsite.com
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: aca-ai-content-agent
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define constants
define( 'ACA_AI_CONTENT_AGENT_VERSION', '1.2' );
define( 'ACA_AI_CONTENT_AGENT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

define( 'ACA_AI_CONTENT_AGENT_GUMROAD_PRODUCT_ID', 'YOUR_PRODUCT_ID' );

// Load dependencies
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-aca-plugin.php';

/**
 * Activation hook.
 */
function aca_ai_content_agent_activate() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/core/class-aca-activator.php';
    ACA_Activator::activate();
    add_option('aca_activation_redirect', true);
}

/**
 * Deactivation hook.
 */
function aca_ai_content_agent_deactivate() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/core/class-aca-deactivator.php';
    ACA_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'aca_ai_content_agent_activate' );
register_deactivation_hook( __FILE__, 'aca_ai_content_agent_deactivate' );

/**
 * The main function for that returns ACA_Plugin
 *
 * @since 1.2
 * @return ACA_Plugin
 */
function aca_ai_content_agent() {
    return ACA_Plugin::instance();
}

// Global for backwards compatibility.
$GLOBALS['aca_ai_content_agent'] = aca_ai_content_agent();
