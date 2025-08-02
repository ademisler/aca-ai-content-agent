<?php
/**
 * Plugin deactivation functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

class ACA_Deactivator {
    
    /**
     * Plugin deactivation
     */
    public static function deactivate() {
        self::clear_scheduled_hooks();
        self::cleanup_plugin_data();
    }
    
    /**
     * Clean up plugin data on deactivation
     */
    private static function cleanup_plugin_data() {
        // Clear license data to allow fresh installation
        delete_option('aca_license_status');
        delete_option('aca_license_data');
        delete_option('aca_license_site_hash');
        
        // Clear all plugin settings to ensure fresh start
        delete_option('aca_settings');
        
        // Clear migration system data
        delete_option('aca_db_version');
        delete_option('aca_migration_log');
        delete_transient('aca_migration_check_done');
        
        // Clear all ACA transients
        delete_transient('aca_gsc_reauth_required');
        delete_transient('aca_gsc_scope_reauth_required');
        delete_transient('aca_google_access_token');
        delete_transient('aca_token_refresh_lock');
        delete_transient('aca_thirty_minute_task_lock');
        delete_transient('aca_fifteen_minute_task_lock');
        
        // Clear GSC token validation cache (dynamic transients)
        global $wpdb;
        $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", '_transient_aca_gsc_scope_validation_%'));
        $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", '_transient_timeout_aca_gsc_scope_validation_%'));
        delete_option('aca_style_guide');
        delete_option('aca_google_auth_token');
        delete_option('aca_gsc_site_url');
        delete_option('aca_freshness_settings');
        delete_option('aca_last_freshness_analysis');
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('ACA: Plugin data cleaned up on deactivation');
        }
    }
    
    /**
     * Clear scheduled cron hooks
     */
    private static function clear_scheduled_hooks() {
        wp_clear_scheduled_hook('aca_thirty_minute_event');
        wp_clear_scheduled_hook('aca_fifteen_minute_event');
    }
}