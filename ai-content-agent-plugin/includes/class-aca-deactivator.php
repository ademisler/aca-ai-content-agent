<?php
/**
 * Plugin Deactivator
 */
if (!defined('ABSPATH')) exit;

class ACA_Deactivator {
    
    public static function deactivate() {
        self::cleanup_plugin_data();
        self::clear_scheduled_hooks();
        self::cleanup_static_classes();
    }
    
    private static function cleanup_plugin_data() {
        delete_option('aca_license_status');
        delete_option('aca_license_data');
        delete_option('aca_license_site_hash');
        delete_option('aca_settings');
        delete_option('aca_style_guide');
        delete_option('aca_google_auth_token');
        delete_option('aca_gsc_site_url');
        delete_option('aca_freshness_settings');
        delete_option('aca_last_freshness_analysis');
        error_log('ACA: Plugin data cleaned up on deactivation');
    }
    
    private static function clear_scheduled_hooks() {
        wp_clear_scheduled_hook('aca_thirty_minute_event');
        wp_clear_scheduled_hook('aca_fifteen_minute_event');
    }
    
    private static function cleanup_static_classes() {
        if (class_exists('ACA_Performance_Monitor')) {
            ACA_Performance_Monitor::cleanup();
        }
        if (class_exists('ACA_File_Manager')) {
            ACA_File_Manager::cleanup();
        }
        if (class_exists('ACA_Hook_Manager')) {
            ACA_Hook_Manager::cleanup();
        }
        if (class_exists('AI_Content_Agent')) {
            AI_Content_Agent::destroy_instance();
        }
        error_log('ACA: All static classes cleaned up');
    }
}
