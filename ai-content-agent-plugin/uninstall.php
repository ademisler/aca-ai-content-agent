<?php
/**
 * Plugin uninstall functionality
 * Fired when the plugin is deleted from WordPress admin
 */

// If uninstall not called from WordPress, then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

// Delete all plugin options
delete_option('aca_settings');
delete_option('aca_style_guide');
delete_option('aca_google_auth_token');
delete_option('aca_gsc_site_url');
delete_option('aca_license_status');
delete_option('aca_license_data');
delete_option('aca_license_site_hash');
delete_option('aca_freshness_settings');
delete_option('aca_last_freshness_analysis');

// Clear all scheduled cron hooks
wp_clear_scheduled_hook('aca_thirty_minute_event');
wp_clear_scheduled_hook('aca_fifteen_minute_event');

// Drop custom database tables
$tables_to_drop = array(
    $wpdb->prefix . 'aca_ideas',
    $wpdb->prefix . 'aca_activity_logs',
    $wpdb->prefix . 'aca_content_updates',
    $wpdb->prefix . 'aca_content_freshness'
);

// Drop custom tables (direct queries required for uninstall)
foreach ($tables_to_drop as $table) {
    $wpdb->query($wpdb->prepare("DROP TABLE IF EXISTS `%s`", $table));
}

// Delete all post meta fields created by the plugin (optimized for uninstall)
$wpdb->query($wpdb->prepare(
    "DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s",
    '_aca_last_freshness_check'
));

// Clean up any remaining plugin data (direct query required for uninstall)
$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", 'aca_%'));

// Log uninstall completion using plugin's debug function
if (function_exists('aca_debug_log')) {
    aca_debug_log('Plugin completely uninstalled and all data removed');
} else if (defined('WP_DEBUG') && WP_DEBUG) {
    // Fallback for uninstall process when plugin functions may not be available
    error_log('ACA: Plugin completely uninstalled and all data removed'); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Uninstall logging fallback
}