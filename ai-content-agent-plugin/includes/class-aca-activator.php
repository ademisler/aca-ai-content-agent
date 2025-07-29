<?php
/**
 * Plugin activation functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

class ACA_Activator {
    
    /**
     * Plugin activation
     */
    public static function activate() {
        self::create_tables();
        self::set_default_options();
        self::schedule_cron_jobs();
    }
    
    /**
     * Create custom database tables
     */
    private static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Ideas table
        $ideas_table_name = $wpdb->prefix . 'aca_ideas';
        $sql_ideas = "CREATE TABLE $ideas_table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title text NOT NULL,
            status varchar(20) DEFAULT 'new' NOT NULL,
            source varchar(20) DEFAULT 'ai' NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        
        // Activity logs table
        $logs_table_name = $wpdb->prefix . 'aca_activity_logs';
        $sql_logs = "CREATE TABLE $logs_table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            timestamp datetime NOT NULL,
            type varchar(50) NOT NULL,
            details text NOT NULL,
            icon varchar(50) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_ideas);
        dbDelta($sql_logs);
    }
    
    /**
     * Set default plugin options
     */
    private static function set_default_options() {
        $default_settings = array(
            'mode' => 'manual',
            'autoPublish' => false,
            'searchConsoleUser' => null,
            'gscClientId' => '',
            'gscClientSecret' => '',
            'imageSourceProvider' => 'ai',
            'aiImageStyle' => 'photorealistic',
            'pexelsApiKey' => '',
            'unsplashApiKey' => '',
            'pixabayApiKey' => '',
            'seoPlugin' => 'none',
            'geminiApiKey' => '',
        );
        
        add_option('aca_settings', $default_settings);
        add_option('aca_style_guide', null);
    }
    
    /**
     * Schedule WP-Cron jobs
     */
    private static function schedule_cron_jobs() {
        if (!wp_next_scheduled('aca_thirty_minute_event')) {
            wp_schedule_event(time(), 'aca_thirty_minutes', 'aca_thirty_minute_event');
        }
        
        if (!wp_next_scheduled('aca_fifteen_minute_event')) {
            wp_schedule_event(time(), 'aca_fifteen_minutes', 'aca_fifteen_minute_event');
        }
    }
}