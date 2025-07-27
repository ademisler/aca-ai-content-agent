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
        try {
            // Create database tables
            self::create_custom_tables();
            
            // Add custom capabilities
            self::add_custom_capabilities();
            
            // Set activation redirect
            set_transient('aca_ai_content_agent_activation_redirect', true, 30);
            
            // Log successful activation
            if (class_exists('ACA_Log_Service')) {
                ACA_Log_Service::info('Plugin activated successfully');
            }
            
        } catch (Exception $e) {
            // Log activation error
            if (class_exists('ACA_Log_Service')) {
                ACA_Log_Service::error('Plugin activation failed: ' . $e->getMessage());
            }
            
            // Re-throw the exception to show error to user
            throw $e;
        }
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
        
        // Check if we can access the database
        if (!$wpdb) {
            throw new Exception('Database connection not available');
        }
        
        $charset_collate = method_exists($wpdb, 'get_charset_collate') ? $wpdb->get_charset_collate() : 'DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';

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
        
        // Include upgrade.php for dbDelta function
        if (!function_exists('dbDelta')) {
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        }
        
        $result = dbDelta( $sql_ideas );
        if (!empty($wpdb->last_error)) {
            throw new Exception('Failed to create ideas table: ' . $wpdb->last_error);
        }

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
        
        $result = dbDelta( $sql_logs );
        if (!empty($wpdb->last_error)) {
            throw new Exception('Failed to create logs table: ' . $wpdb->last_error);
        }

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
        
        $result = dbDelta( $sql_clusters );
        if (!empty($wpdb->last_error)) {
            throw new Exception('Failed to create clusters table: ' . $wpdb->last_error);
        }

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
        
        $result = dbDelta( $sql_cluster_items );
        if (!empty($wpdb->last_error)) {
            throw new Exception('Failed to create cluster items table: ' . $wpdb->last_error);
        }

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
        
        // Add indexes if they don't exist - MySQL 8.0 compatible
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
                // Check if index exists first
                $index_exists = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM information_schema.statistics 
                     WHERE table_schema = DATABASE() 
                     AND table_name = %s 
                     AND index_name = %s",
                    $table,
                    $index_name
                ));
                
                if (!$index_exists) {
                    $wpdb->query("CREATE INDEX {$index_name} ON {$table} ({$columns})");
                }
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
        try {
            $capabilities = [
                'manage_aca_ai_content_agent_settings' => true,
                'view_aca_ai_content_agent_dashboard'  => true,
            ];

            // Create custom role
            add_role('aca_content_manager', __('ACA Content Manager', 'aca-ai-content-agent'), $capabilities);

            // Add capabilities to roles that should have access
            $roles_to_update = ['administrator', 'editor', 'author'];
            
            foreach ($roles_to_update as $role_name) {
                $role = get_role($role_name);
                if ($role) {
                    foreach ($capabilities as $cap => $grant) {
                        $role->add_cap($cap);
                    }
                    
                    // Ensure edit_posts capability exists for content management
                    if (!$role->has_cap('edit_posts') && $role_name === 'author') {
                        $role->add_cap('edit_posts');
                    }
                }
            }
            
            // Ensure current user has the necessary capabilities
            $current_user = wp_get_current_user();
            if ($current_user->exists() && current_user_can('manage_options')) {
                foreach ($capabilities as $cap => $grant) {
                    if (!$current_user->has_cap($cap)) {
                        $current_user->add_cap($cap);
                    }
                }
            }
            
            // Log successful capability addition
            if (class_exists('ACA_Log_Service')) {
                ACA_Log_Service::info('Custom capabilities added successfully');
            }
            
        } catch (Exception $e) {
            throw new Exception('Failed to add custom capabilities: ' . $e->getMessage());
        }
    }
}