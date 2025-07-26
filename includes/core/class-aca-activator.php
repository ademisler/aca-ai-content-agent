<?php
/**
 * Fired during plugin activation
 *
 * @link       https://ademisler.com
 * @since      1.2.0
 *
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes/core
 */

/**
 * The activator.
 *
 * @link       https://ademisler.com
 * @since      1.2.0
 *
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes/core
 */
class ACA_Activator {

	/**
	 * The main activation method.
	 *
	 * Creates necessary database tables, assigns custom capabilities,
	 * and sets up activation redirect for first-time users.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public static function activate() {
        self::create_custom_tables();
        self::add_custom_capabilities();
        set_transient('aca_ai_content_agent_activation_redirect', true, 30);
	}

    /**
     * Create custom database tables for the plugin.
     *
     * Creates tables for storing ideas, logs, content clusters, and cluster items.
     * Uses WordPress dbDelta for safe table creation and updates.
     *
     * @since 1.2.0
     * @return void
     */
    private static function create_custom_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Table for generated ideas
        $table_name_ideas = $wpdb->prefix . 'aca_ai_content_agent_ideas';
        $sql_ideas = "CREATE TABLE $table_name_ideas (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title tinytext NOT NULL,
            keywords text,
            status varchar(20) DEFAULT 'pending' NOT NULL,
            generated_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            post_id bigint(20) DEFAULT NULL,
            feedback int(1) DEFAULT 0 NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            ip_address varchar(45) DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY status (status),
            KEY generated_date (generated_date),
            KEY user_id (user_id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql_ideas );

        // Table for logs - ENHANCED with new fields
        $table_name_logs = $wpdb->prefix . 'aca_ai_content_agent_logs';
        $sql_logs = "CREATE TABLE $table_name_logs (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            timestamp datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            level varchar(20) NOT NULL,
            message text NOT NULL,
            context text DEFAULT NULL,
            user_id bigint(20) DEFAULT NULL,
            ip_address varchar(45) DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY level (level),
            KEY timestamp (timestamp),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta( $sql_logs );

        // Table for content clusters
        $table_name_clusters = $wpdb->prefix . 'aca_ai_content_agent_clusters';
        $sql_clusters = "CREATE TABLE $table_name_clusters (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            topic tinytext NOT NULL,
            status varchar(20) DEFAULT 'pending' NOT NULL,
            generated_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY status (status),
            KEY generated_date (generated_date)
        ) $charset_collate;";
        dbDelta( $sql_clusters );

        // Table for cluster items (subtopics)
        $table_name_cluster_items = $wpdb->prefix . 'aca_ai_content_agent_cluster_items';
        $sql_cluster_items = "CREATE TABLE $table_name_cluster_items (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            cluster_id mediumint(9) NOT NULL,
            subtopic_title tinytext NOT NULL,
            status varchar(20) DEFAULT 'pending' NOT NULL,
            post_id bigint(20) DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY cluster_id (cluster_id),
            KEY status (status)
        ) $charset_collate;";
        dbDelta( $sql_cluster_items );

        // SECURITY FIX: Add indexes for better performance
        self::add_database_indexes();
    }

    /**
     * Add database indexes for better performance.
     *
     * @since 1.2.0
     * @return void
     */
    private static function add_database_indexes() {
        global $wpdb;
        
        // Add indexes if they don't exist
        $indexes = [
            $wpdb->prefix . 'aca_ai_content_agent_ideas' => [
                'status_generated_date' => 'status, generated_date',
                'user_id_status' => 'user_id, status'
            ],
            $wpdb->prefix . 'aca_ai_content_agent_logs' => [
                'level_timestamp' => 'level, timestamp',
                'user_id_timestamp' => 'user_id, timestamp'
            ],
            $wpdb->prefix . 'aca_ai_content_agent_clusters' => [
                'status_generated_date' => 'status, generated_date'
            ]
        ];

        foreach ($indexes as $table => $table_indexes) {
            foreach ($table_indexes as $index_name => $columns) {
                $wpdb->query("CREATE INDEX IF NOT EXISTS {$index_name} ON {$table} ({$columns})");
            }
        }
    }

    /**
     * Add custom capabilities for the plugin.
     *
     * Creates a new role 'aca_content_manager' with plugin-specific capabilities
     * and adds these capabilities to the administrator role.
     *
     * @since 1.2.0
     * @return void
     */
    private static function add_custom_capabilities() {
        $capabilities = [
            'manage_aca_ai_content_agent_settings' => true,
            'view_aca_ai_content_agent_dashboard'  => true,
        ];

        add_role('aca_content_manager', __('ACA Content Manager', 'aca-ai-content-agent'), $capabilities);

        $admin_role = get_role('administrator');
        if ($admin_role) {
            foreach ($capabilities as $cap => $grant) {
                $admin_role->add_cap($cap);
            }
        }
    }
}