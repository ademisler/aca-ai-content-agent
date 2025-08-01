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
        delete_option('aca_style_guide');
        delete_option('aca_google_auth_token');
        delete_option('aca_gsc_site_url');
        delete_option('aca_freshness_settings');
        delete_option('aca_last_freshness_analysis');
        
        error_log('ACA: Plugin data cleaned up on deactivation');
    }
    
    /**
     * Clear scheduled cron hooks
     */
    private static function clear_scheduled_hooks() {
        wp_clear_scheduled_hook('aca_thirty_minute_event');
        wp_clear_scheduled_hook('aca_fifteen_minute_event');
    }
}