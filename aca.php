<?php
/**
 * Plugin Name: ACA - AI Content Agent
 * Plugin URI:  https://yourwebsite.com/aca-ai-content-agent
 * Description: AI-powered content generation and optimization for WordPress.
 * Version:     1.0
 * Author:      Your Name
 * Author URI:  https://yourwebsite.com
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: aca-ai-content-agent
 * Domain Path: /languages
 *
 * @package ACA_AI_Content_Agent
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ACA_AI_CONTENT_AGENT_VERSION', '1.0' );
define( 'ACA_AI_CONTENT_AGENT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ACA_AI_CONTENT_AGENT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Autoload Composer dependencies
if ( file_exists( ACA_AI_CONTENT_AGENT_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
	require_once ACA_AI_CONTENT_AGENT_PLUGIN_DIR . 'vendor/autoload.php';
}

// Load Action Scheduler if not already loaded by another plugin
if ( ! class_exists( 'ActionScheduler_Action' ) && file_exists( ACA_AI_CONTENT_AGENT_PLUGIN_DIR . 'vendor/woocommerce/action-scheduler/action-scheduler.php' ) ) {
	require_once ACA_AI_CONTENT_AGENT_PLUGIN_DIR . 'vendor/woocommerce/action-scheduler/action-scheduler.php';
}

// Define your Gumroad Product ID
define( 'ACA_AI_CONTENT_AGENT_GUMROAD_PRODUCT_ID', 'YOUR_GUMROAD_PRODUCT_ID_HERE' ); // IMPORTANT: Replace with your actual Gumroad Product ID

// Include core plugin files
require_once ACA_AI_CONTENT_AGENT_PLUGIN_DIR . 'includes/api.php';
require_once ACA_AI_CONTENT_AGENT_PLUGIN_DIR . 'includes/class-aca-admin.php';
require_once ACA_AI_CONTENT_AGENT_PLUGIN_DIR . 'includes/class-aca-dashboard.php';
require_once ACA_AI_CONTENT_AGENT_PLUGIN_DIR . 'includes/class-aca.php';
require_once ACA_AI_CONTENT_AGENT_PLUGIN_DIR . 'includes/class-aca-onboarding.php';
require_once ACA_AI_CONTENT_AGENT_PLUGIN_DIR . 'includes/class-aca-cron.php';
require_once ACA_AI_CONTENT_AGENT_PLUGIN_DIR . 'includes/class-aca-privacy.php';
require_once ACA_AI_CONTENT_AGENT_PLUGIN_DIR . 'includes/licensing.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-aca-activator.php
 */
register_activation_hook(__FILE__, ['ACA_Bootstrap', 'activate']);
register_deactivation_hook(__FILE__, 'aca_ai_content_agent_deactivate');

/**
 * The code that runs during plugin deactivation.
 */
function aca_ai_content_agent_deactivate() {
    // Clear all scheduled actions for the plugin
    as_unschedule_all_actions( 'aca_ai_content_agent_run_main_automation' );
    as_unschedule_all_actions( 'aca_ai_content_agent_reset_api_usage_counter' );
    as_unschedule_all_actions( 'aca_ai_content_agent_generate_style_guide' );
    as_unschedule_all_actions( 'aca_ai_content_agent_verify_license' );
    as_unschedule_all_actions( 'aca_ai_content_agent_clean_logs' );

    // Also clear WP Cron schedules as a fallback
    wp_clear_scheduled_hook( 'aca_ai_content_agent_run_main_automation' );
    wp_clear_scheduled_hook( 'aca_ai_content_agent_reset_api_usage_counter' );
    wp_clear_scheduled_hook( 'aca_ai_content_agent_generate_style_guide' );
    wp_clear_scheduled_hook( 'aca_ai_content_agent_verify_license' );
    wp_clear_scheduled_hook( 'aca_ai_content_agent_clean_logs' );

    // Remove capabilities upon deactivation
    $role = get_role( 'administrator' );
    if ( $role ) {
        $role->remove_cap( 'manage_aca_ai_content_agent_settings' );
        $role->remove_cap( 'view_aca_ai_content_agent_dashboard' );
    }
}

/**
 * ACA Bootstrap Class
 * Handles plugin initialization and setup.
 */
class ACA_Bootstrap {

    public function __construct() {
        add_action('admin_init', [$this, 'handle_activation_redirect']);
    }

    /**
     * Handle the redirect to onboarding after activation.
     */
    public function handle_activation_redirect() {
        if (get_transient('aca_ai_content_agent_activation_redirect')) {
            delete_transient('aca_ai_content_agent_activation_redirect');
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            if (!isset($_GET['page']) || $_GET['page'] !== 'aca-ai-content-agent-onboarding') {
                wp_safe_redirect(admin_url('index.php?page=aca-ai-content-agent-onboarding'));
                exit;
            }
        }
    }

    /**
     * Activate the plugin.
     */
    public static function activate() {
        global $wpdb;

        // Load plugin text domain for activation messages
        load_plugin_textdomain( 'aca-ai-content-agent', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

        // Initialize classes that register hooks
        new ACA_AI_Content_Agent_Admin();
        new ACA_AI_Content_Agent_Onboarding();
        // Other initializations if needed
        new ACA_AI_Content_Agent_Cron(); // Schedule cron jobs on activation

        // Create custom database tables if they don't exist
        self::create_custom_tables();

        // Add custom capabilities
        self::add_custom_capabilities();

        // Set a transient to redirect to the onboarding page after activation
        set_transient('aca_ai_content_agent_activation_redirect', true, 30);

        // Log activation
        ACA_AI_Content_Agent_Engine::add_log('Plugin activated successfully.', 'success');
    }

    /**
     * Create custom database tables.
     */
    private static function create_custom_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Table for generated ideas
        $table_name_ideas = $wpdb->prefix . 'aca_ai_content_agent_ideas';
        $sql_ideas = "CREATE TABLE $table_name_ideas (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title tinytext NOT NULL,
            keywords text,
            status varchar(20) DEFAULT 'pending' NOT NULL,
            generated_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            post_id bigint(20) DEFAULT NULL,
            feedback int(1) DEFAULT 0 NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql_ideas );

        // Table for logs
        $table_name_logs = $wpdb->prefix . 'aca_ai_content_agent_logs';
        $sql_logs = "CREATE TABLE $table_name_logs (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            timestamp datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            level varchar(20) NOT NULL,
            message text NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta( $sql_logs );

        // Table for content clusters
        $table_name_clusters = $wpdb->prefix . 'aca_ai_content_agent_clusters';
        $sql_clusters = "CREATE TABLE $table_name_clusters (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            topic tinytext NOT NULL,
            status varchar(20) DEFAULT 'pending' NOT NULL,
            generated_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta( $sql_clusters );

        // Table for cluster items (subtopics)
        $table_name_cluster_items = $wpdb->prefix . 'aca_ai_content_agent_cluster_items';
        $sql_cluster_items = "CREATE TABLE $table_name_cluster_items (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            cluster_id mediumint(9) NOT NULL,
            subtopic_title tinytext NOT NULL,
            status varchar(20) DEFAULT 'pending' NOT NULL,
            post_id bigint(20) DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY cluster_id (cluster_id)
        ) $charset_collate;";
        dbDelta( $sql_cluster_items );
    }

    /**
     * Add custom capabilities for the plugin.
     */
    private static function add_custom_capabilities() {
        $role = get_role( 'administrator' );
        if ( $role ) {
            $role->add_cap( 'manage_aca_ai_content_agent_settings' );
            $role->add_cap( 'view_aca_ai_content_agent_dashboard' );
        }
    }
}

/**
 * Check if ACA Pro is active.
 *
 * @return bool True if ACA Pro is active, false otherwise.
 */
function aca_ai_content_agent_is_pro() {
    // Check if the license check function exists and run it
    if ( function_exists( 'aca_ai_content_agent_maybe_check_license' ) ) {
        aca_ai_content_agent_maybe_check_license();
    }

    $is_active    = false;
    $active_flag  = get_option( 'aca_ai_content_agent_is_pro_active' ) === 'true';
    $valid_until  = intval( get_option( 'aca_ai_content_agent_license_valid_until', 0 ) );
    $status       = get_transient( 'aca_ai_content_agent_license_status' );

    if ( $active_flag && $valid_until > time() && $status === 'valid' ) {
        $is_active = true;
    }

    return apply_filters( 'aca_ai_content_agent_is_pro', $is_active );
}

// Initialize the plugin
new ACA_Bootstrap();
