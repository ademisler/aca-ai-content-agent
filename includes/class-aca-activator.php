<?php

/**
 * Fired during plugin activation
 */
class ACA_Activator {

    /**
     * Plugin activation handler
     */
    public static function activate() {
        self::create_tables();
        self::create_default_settings();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Create plugin database tables
     */
    private static function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Ideas table
        $ideas_table = $wpdb->prefix . 'aca_ideas';
        $ideas_sql = "CREATE TABLE $ideas_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title text NOT NULL,
            status varchar(20) DEFAULT 'new',
            source varchar(20) DEFAULT 'ai',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        // Drafts table
        $drafts_table = $wpdb->prefix . 'aca_drafts';
        $drafts_sql = "CREATE TABLE $drafts_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title text NOT NULL,
            content longtext NOT NULL,
            meta_title varchar(255),
            meta_description text,
            focus_keywords text,
            featured_image_url text,
            status varchar(20) DEFAULT 'draft',
            post_id mediumint(9),
            scheduled_for datetime,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        // Activity log table
        $activity_table = $wpdb->prefix . 'aca_activity_log';
        $activity_sql = "CREATE TABLE $activity_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            type varchar(50) NOT NULL,
            details text NOT NULL,
            icon varchar(50),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($ideas_sql);
        dbDelta($drafts_sql);
        dbDelta($activity_sql);
    }

    /**
     * Create default plugin settings
     */
    private static function create_default_settings() {
        $default_settings = array(
            'mode' => 'manual',
            'auto_publish' => false,
            'gemini_api_key' => '',
            'image_source_provider' => 'ai',
            'ai_image_style' => 'photorealistic',
            'pexels_api_key' => '',
            'unsplash_api_key' => '',
            'pixabay_api_key' => '',
            'seo_plugin' => 'none'
        );

        $style_guide = array(
            'tone' => '',
            'sentence_structure' => '',
            'paragraph_length' => '',
            'formatting_style' => '',
            'custom_instructions' => '',
            'last_analyzed' => ''
        );

        add_option('aca_settings', $default_settings);
        add_option('aca_style_guide', $style_guide);
    }
}