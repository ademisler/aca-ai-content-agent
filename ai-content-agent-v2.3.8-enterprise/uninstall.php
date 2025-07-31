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

// Drop custom database tables using prepared statements for security
$tables_to_drop = array(
    $wpdb->prefix . 'aca_ideas',
    $wpdb->prefix . 'aca_activity_logs',
    $wpdb->prefix . 'aca_content_updates',
    $wpdb->prefix . 'aca_content_freshness',
    $wpdb->prefix . 'aca_error_logs'
);

foreach ($tables_to_drop as $table) {
    // Use prepared statement for table name (WordPress doesn't support table name placeholders, 
    // but we validate the table name format for security)
    $table_name = sanitize_text_field($table);
    if (preg_match('/^[a-zA-Z0-9_]+$/', $table_name)) {
        $wpdb->query($wpdb->prepare("DROP TABLE IF EXISTS `%s`", $table_name));
    }
}

// Delete all post meta fields created by the plugin using safe method
$wpdb->delete(
    $wpdb->postmeta,
    array(
        'meta_key' => '_aca_last_freshness_check'
    ),
    array('%s')
);

// Clean up any remaining plugin data using prepared statement
$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", 'aca_%'));

error_log('ACA: Plugin completely uninstalled and all data removed');