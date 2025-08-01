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
        
        // Initialize migration system for future updates (safe addition)
        self::initialize_migration_system();
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
        
        // Content updates tracking table
        $content_updates_table_name = $wpdb->prefix . 'aca_content_updates';
        $sql_content_updates = "CREATE TABLE $content_updates_table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            last_updated datetime NOT NULL,
            update_type varchar(50) NOT NULL,
            ai_suggestions longtext,
            status varchar(20) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY post_id (post_id),
            KEY status (status)
        ) $charset_collate;";
        
        // Content freshness scores table
        $content_freshness_table_name = $wpdb->prefix . 'aca_content_freshness';
        $sql_content_freshness = "CREATE TABLE $content_freshness_table_name (
            post_id bigint(20) NOT NULL,
            freshness_score tinyint(3) DEFAULT 0,
            last_analyzed datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            needs_update tinyint(1) DEFAULT 0,
            update_priority tinyint(1) DEFAULT 1,
            PRIMARY KEY  (post_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_ideas);
        dbDelta($sql_logs);
        dbDelta($sql_content_updates);
        dbDelta($sql_content_freshness);
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
            'imageSourceProvider' => 'pexels',
            'aiImageStyle' => 'photorealistic',
            'googleCloudProjectId' => '',
            'googleCloudLocation' => 'us-central1',
            'pexelsApiKey' => '',
            'unsplashApiKey' => '',
            'pixabayApiKey' => '',
            'seoPlugin' => 'none',
            'geminiApiKey' => '',
        );
        
        add_option('aca_settings', $default_settings);
        add_option('aca_style_guide', null);
        
        // Content freshness default settings
        $default_freshness_settings = array(
            'analysisFrequency' => 'weekly',
            'autoUpdate' => false,
            'updateThreshold' => 70,
            'enabled' => true
        );
        add_option('aca_freshness_settings', $default_freshness_settings);
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
    
    /**
     * Initialize migration system (new method - add at the end of class)
     */
    private static function initialize_migration_system() {
        $migration_file = plugin_dir_path(__FILE__) . 'class-aca-migration-manager.php';
        
        if (file_exists($migration_file)) {
            require_once $migration_file;
            
            $migration_manager = new ACA_Migration_Manager();
            $result = $migration_manager->run_migrations();
            
            if (is_wp_error($result)) {
                error_log('ACA Migration initialization failed: ' . $result->get_error_message());
                // Don't fail activation - just log the error
            }
        }
    }
}