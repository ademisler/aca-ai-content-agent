<?php
/**
 * Fired during plugin activation
 *
 * @link       https://yourwebsite.com
 * @since      1.2.0
 *
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes/core
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.2.0
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes/core
 * @author     Your Name <email@example.com>
 */
class ACA_Activator {

	/**
	 * The main activation method.
	 *
	 * @since    1.2.0
	 */
	public static function activate() {
        self::create_custom_tables();
        self::add_custom_capabilities();
        set_transient('aca_ai_content_agent_activation_redirect', true, 30);
	}

    /**
     * Create custom database tables.
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
            PRIMARY KEY  (id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql_ideas );

        // Table for logs
        $table_name_logs = $wpdb->prefix . 'aca_ai_content_agent_logs';
        $sql_logs = "CREATE TABLE $table_name_logs (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            timestamp datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            level varchar(20) NOT NULL,
            message text NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta( $sql_logs );

        // Table for content clusters
        $table_name_clusters = $wpdb->prefix . 'aca_ai_content_agent_clusters';
        $sql_clusters = "CREATE TABLE $table_name_clusters (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            topic tinytext NOT NULL,
            status varchar(20) DEFAULT 'pending' NOT NULL,
            generated_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
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
            KEY cluster_id (cluster_id)
        ) $charset_collate;";
        dbDelta( $sql_cluster_items );
    }

    /**
     * Add custom capabilities for the plugin.
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