<?php
/**
 * Plugin Name: ACA - AI Content Agent
 * Description: An intelligent plugin that learns your content's tone and style to autonomously generate high-quality, SEO-friendly new content.
 * Version: 1.0
 * Author: Adem Isler
 * Author URI: https://ademisler.com
 * Text Domain: aca
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Define constants
define( 'ACA_VERSION', '1.0' );
define( 'ACA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// Load Composer autoloader and Action Scheduler if available
if ( file_exists( ACA_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
    require_once ACA_PLUGIN_DIR . 'vendor/autoload.php';
}
if ( file_exists( ACA_PLUGIN_DIR . 'vendor/woocommerce/action-scheduler/action-scheduler.php' ) ) {
    require_once ACA_PLUGIN_DIR . 'vendor/woocommerce/action-scheduler/action-scheduler.php';
}

// Define Gumroad Product ID for license verification
define( 'ACA_GUMROAD_PRODUCT_ID', 'YOUR_GUMROAD_PRODUCT_ID_HERE' ); // IMPORTANT: Replace with your actual Gumroad Product ID

// Include required files
require_once ACA_PLUGIN_DIR . 'includes/api.php';
require_once ACA_PLUGIN_DIR . 'includes/class-aca-admin.php';
require_once ACA_PLUGIN_DIR . 'includes/class-aca-dashboard.php';
require_once ACA_PLUGIN_DIR . 'includes/class-aca.php';
require_once ACA_PLUGIN_DIR . 'includes/class-aca-onboarding.php';
require_once ACA_PLUGIN_DIR . 'includes/class-aca-cron.php';
require_once ACA_PLUGIN_DIR . 'includes/class-aca-privacy.php';
require_once ACA_PLUGIN_DIR . 'includes/licensing.php';

// Activation hook for onboarding and database setup
register_activation_hook(__FILE__, ['ACA', 'activate']);
register_deactivation_hook(__FILE__, 'aca_deactivate');

function aca_deactivate() {
    if ( function_exists( 'as_unschedule_all_actions' ) ) {
        as_unschedule_all_actions( 'aca_run_main_automation' );
        as_unschedule_all_actions( 'aca_reset_api_usage_counter' );
        as_unschedule_all_actions( 'aca_generate_style_guide' );
        as_unschedule_all_actions( 'aca_verify_license' );
    } else {
        wp_clear_scheduled_hook( 'aca_run_main_automation' );
        wp_clear_scheduled_hook( 'aca_reset_api_usage_counter' );
        wp_clear_scheduled_hook( 'aca_generate_style_guide' );
        wp_clear_scheduled_hook( 'aca_verify_license' );
    }
}

/**
 * Main ACA Class
 */
class ACA {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action('plugins_loaded', [$this, 'init']);
    }

    /**
     * Initialize the plugin.
     */
    public function init() {
        if (is_admin()) {
            new ACA_Admin();
            new ACA_Onboarding();
            $this->handle_redirect();
        }

        // Initialize cron jobs
        new ACA_Cron();

        // Register privacy hooks
        ACA_Privacy::init();
    }

    /**
     * Handle the redirect to the onboarding wizard.
     */
    public function handle_redirect() {
        if (get_transient('aca_activation_redirect')) {
            delete_transient('aca_activation_redirect');
            if (!is_multisite()) {
                wp_redirect(admin_url('index.php?page=aca-onboarding'));
                exit;
            }
        }
    }

    /**
     * Plugin activation hook.
     */
    public static function activate() {
        // Set redirect transient for onboarding
        set_transient('aca_activation_redirect', true, 30);

        // Create custom database tables
        self::create_database_tables();

        // Add custom capabilities
        self::add_capabilities();
    }

    /**
     * Create custom database tables.
     */
    public static function create_database_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // Table for ideas
        $table_name_ideas = $wpdb->prefix . 'aca_ideas';
        $sql_ideas = "CREATE TABLE $table_name_ideas (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            idea_title text NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending', // pending, approved, rejected, drafted
            created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            feedback tinyint(1) DEFAULT 0, -- -1 for downvote, 1 for upvote, 0 for no feedback
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_ideas);

        // Table for logs
        $table_name_logs = $wpdb->prefix . 'aca_logs';
        $sql_logs = "CREATE TABLE $table_name_logs (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            log_message text NOT NULL,
            log_type varchar(20) NOT NULL DEFAULT 'info', // info, success, warning, error
            created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_logs);

        // Table for content clusters (for strategic planning)
        $table_name_clusters = $wpdb->prefix . 'aca_clusters';
        $sql_clusters = "CREATE TABLE $table_name_clusters (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            topic text NOT NULL,
            created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_clusters);

        // Table for cluster items (sub topics)
        $table_name_cluster_items = $wpdb->prefix . 'aca_cluster_items';
        $sql_cluster_items = "CREATE TABLE $table_name_cluster_items (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            cluster_id mediumint(9) NOT NULL,
            subtopic_title text NOT NULL,
            created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (id),
            KEY cluster_id (cluster_id)
        ) $charset_collate;";
        dbDelta($sql_cluster_items);
    }

    /**
     * Add custom capabilities for the plugin.
     */
    public static function add_capabilities() {
        $roles = ['administrator', 'editor'];
        foreach ($roles as $role_name) {
            $role = get_role($role_name);
            if ($role) {
                $role->add_cap('manage_aca_settings');
                $role->add_cap('view_aca_dashboard');
            }
        }
        $author_role = get_role('author');
        if ($author_role) {
            $author_role->add_cap('view_aca_dashboard');
        }
    }
}

/**
 * Check if the user has a valid license.
 */
function aca_is_pro() {
    // Trigger a license check if needed.
    if ( function_exists( 'aca_maybe_check_license' ) ) {
        aca_maybe_check_license();
    }

    $active_flag  = get_option( 'aca_is_pro_active' ) === 'true';
    $valid_until  = intval( get_option( 'aca_license_valid_until', 0 ) );
    $status       = get_transient( 'aca_license_status' );
    $status_valid = ( false === $status ) ? true : ( 'valid' === $status );

    $is_active = $active_flag && $status_valid && ( 0 === $valid_until || $valid_until > time() );

    return apply_filters( 'aca_is_pro', $is_active );
}

// Instantiate the main class
new ACA();
