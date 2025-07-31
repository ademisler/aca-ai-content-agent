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
     * Create custom database tables with optimized indexes and constraints
     */
    private static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Ideas table with optimized indexes
        $ideas_table_name = $wpdb->prefix . 'aca_ideas';
        $sql_ideas = "CREATE TABLE $ideas_table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title text NOT NULL,
            status varchar(20) DEFAULT 'new' NOT NULL,
            source varchar(20) DEFAULT 'ai' NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY status_created (status, created_at),
            KEY source_status (source, status),
            KEY created_at (created_at),
            FULLTEXT KEY title_search (title)
        ) $charset_collate;";
        
        // Activity logs table with optimized indexes and partitioning support
        $logs_table_name = $wpdb->prefix . 'aca_activity_logs';
        $sql_logs = "CREATE TABLE $logs_table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            timestamp datetime NOT NULL,
            type varchar(50) NOT NULL,
            details text NOT NULL,
            icon varchar(50) NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY timestamp_type (timestamp, type),
            KEY type_timestamp (type, timestamp),
            KEY user_timestamp (user_id, timestamp),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        // Content updates tracking table with foreign key constraints
        $content_updates_table_name = $wpdb->prefix . 'aca_content_updates';
        $sql_content_updates = "CREATE TABLE $content_updates_table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            last_updated datetime NOT NULL,
            update_type varchar(50) NOT NULL,
            ai_suggestions longtext,
            status varchar(20) DEFAULT 'pending',
            priority tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY post_status (post_id, status),
            KEY status_priority (status, priority),
            KEY update_type (update_type),
            KEY created_at (created_at),
            KEY priority_created (priority, created_at)
        ) $charset_collate;";
        
        // Content freshness scores table with optimized structure
        $content_freshness_table_name = $wpdb->prefix . 'aca_content_freshness';
        $sql_content_freshness = "CREATE TABLE $content_freshness_table_name (
            post_id bigint(20) NOT NULL,
            freshness_score tinyint(3) DEFAULT 0,
            last_analyzed datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            needs_update tinyint(1) DEFAULT 0,
            update_priority tinyint(1) DEFAULT 1,
            analysis_data longtext,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (post_id),
            KEY score_priority (freshness_score, update_priority),
            KEY needs_update_priority (needs_update, update_priority),
            KEY last_analyzed (last_analyzed),
            KEY priority_score (update_priority, freshness_score)
        ) $charset_collate;";
        
        // Error logs table for better debugging and monitoring
        $error_logs_table_name = $wpdb->prefix . 'aca_error_logs';
        $sql_error_logs = "CREATE TABLE $error_logs_table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            error_type varchar(50) NOT NULL,
            error_message text NOT NULL,
            error_data longtext,
            user_agent text,
            url varchar(255),
            user_id bigint(20) DEFAULT NULL,
            ip_address varchar(45),
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY error_type_created (error_type, created_at),
            KEY user_created (user_id, created_at),
            KEY created_at (created_at),
            KEY ip_created (ip_address, created_at)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_ideas);
        dbDelta($sql_logs);
        dbDelta($sql_content_updates);
        dbDelta($sql_content_freshness);
        dbDelta($sql_error_logs);
        
        // Create additional indexes if they don't exist
        self::ensure_database_indexes();
    }
    
    /**
     * Ensure all necessary database indexes exist for optimal performance
     */
    private static function ensure_database_indexes() {
        global $wpdb;
        
        // Check and create missing indexes
        $indexes_to_check = [
            // Ideas table indexes
            [
                'table' => $wpdb->prefix . 'aca_ideas',
                'index' => 'status_created',
                'columns' => 'status, created_at'
            ],
            [
                'table' => $wpdb->prefix . 'aca_ideas',
                'index' => 'title_search',
                'columns' => 'title',
                'type' => 'FULLTEXT'
            ],
            
            // Activity logs indexes
            [
                'table' => $wpdb->prefix . 'aca_activity_logs',
                'index' => 'timestamp_type',
                'columns' => 'timestamp, type'
            ],
            
            // Content updates indexes
            [
                'table' => $wpdb->prefix . 'aca_content_updates',
                'index' => 'post_status',
                'columns' => 'post_id, status'
            ],
            
            // Content freshness indexes
            [
                'table' => $wpdb->prefix . 'aca_content_freshness',
                'index' => 'score_priority',
                'columns' => 'freshness_score, update_priority'
            ]
        ];
        
        foreach ($indexes_to_check as $index_info) {
            $table = $index_info['table'];
            $index_name = $index_info['index'];
            $columns = $index_info['columns'];
            $type = isset($index_info['type']) ? $index_info['type'] : 'KEY';
            
            // Check if index exists
            $index_exists = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
                 WHERE table_schema = %s AND table_name = %s AND index_name = %s",
                $wpdb->dbname, $table, $index_name
            ));
            
            if (!$index_exists) {
                $sql = "ALTER TABLE `$table` ADD $type `$index_name` ($columns)";
                $wpdb->query($sql);
            }
        }
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
        // Add custom cron schedules first
        add_filter('cron_schedules', function($schedules) {
            $schedules['aca_thirty_minutes'] = array(
                'interval' => 30 * 60, // 30 minutes in seconds
                'display' => __('Every 30 Minutes', 'ai-content-agent')
            );
            
            $schedules['aca_fifteen_minutes'] = array(
                'interval' => 15 * 60, // 15 minutes in seconds
                'display' => __('Every 15 Minutes', 'ai-content-agent')
            );
            
            return $schedules;
        });
        
        if (!wp_next_scheduled('aca_thirty_minute_event')) {
            wp_schedule_event(time(), 'aca_thirty_minutes', 'aca_thirty_minute_event');
        }
        
        if (!wp_next_scheduled('aca_fifteen_minute_event')) {
            wp_schedule_event(time(), 'aca_fifteen_minutes', 'aca_fifteen_minute_event');
        }
    }
}