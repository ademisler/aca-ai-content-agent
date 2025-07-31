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
        self::cleanup_static_classes();
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
    
    /**
     * Clean up all static class data
     */
    private static function cleanup_static_classes() {
        // Clean up Performance Monitor
        if (class_exists('ACA_Performance_Monitor')) {
            ACA_Performance_Monitor::cleanup();
        }
        
        // Clean up File Manager
        if (class_exists('ACA_File_Manager')) {
            ACA_File_Manager::cleanup();
        }
        
        // Clean up Hook Manager
        if (class_exists('ACA_Hook_Manager')) {
            ACA_Hook_Manager::cleanup();
        }
        
        // Clean up Service Container
        if (class_exists('ACA_Service_Container')) {
            ACA_Service_Container::cleanup();
        }
        
        // Clean up main plugin singleton
        if (class_exists('AI_Content_Agent')) {
            AI_Content_Agent::destroy_instance();
        }
        
        error_log('ACA: All static classes cleaned up');
    }
}
    /**
     * Clean up all static class data
     */
    private static function cleanup_static_classes() {
        // Clean up Performance Monitor
        if (class_exists("ACA_Performance_Monitor")) {
            ACA_Performance_Monitor::cleanup();
        }
        
        // Clean up File Manager
        if (class_exists("ACA_File_Manager")) {
            ACA_File_Manager::cleanup();
        }
        
        // Clean up Hook Manager
        if (class_exists("ACA_Hook_Manager")) {
            ACA_Hook_Manager::cleanup();
        }
        
        // Clean up main plugin singleton
        if (class_exists("AI_Content_Agent")) {
            AI_Content_Agent::destroy_instance();
        }
        
        error_log("ACA: All static classes cleaned up");

    /**
     * Clean up all static class data
     */
    private static function cleanup_static_classes() {
        // Clean up Performance Monitor
        if (class_exists("ACA_Performance_Monitor")) {
            ACA_Performance_Monitor::cleanup();
        }
        
        // Clean up File Manager
        if (class_exists("ACA_File_Manager")) {
            ACA_File_Manager::cleanup();
        }
        
        // Clean up Hook Manager
        if (class_exists("ACA_Hook_Manager")) {
            ACA_Hook_Manager::cleanup();
        }
        
        // Clean up main plugin singleton
        if (class_exists("AI_Content_Agent")) {
            AI_Content_Agent::destroy_instance();
        }
        
        error_log("ACA: All static classes cleaned up");
    }
}
