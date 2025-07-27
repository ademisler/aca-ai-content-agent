<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://ademisler.com
 * @since      1.2.0
 *
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes/core
 */

/**
 * Uninstaller class for ACA AI Content Agent.
 *
 * Handles plugin uninstallation and cleanup.
 *
 * @since 1.2.0
 * @author     Adem Isler <idemasler@gmail.com>
 */
class ACA_Uninstaller {

	/**
	 * The main uninstallation method.
	 *
	 * @since    1.2.0
	 */
	public static function uninstall() {
        global $wpdb;

        $tables = [
            $wpdb->prefix . 'aca_ai_content_agent_ideas',
            $wpdb->prefix . 'aca_ai_content_agent_logs',
            $wpdb->prefix . 'aca_ai_content_agent_clusters',
            $wpdb->prefix . 'aca_ai_content_agent_cluster_items',
        ];

        foreach ( $tables as $table ) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            $wpdb->query( "DROP TABLE IF EXISTS `{$table}`" );
        }

        if ( function_exists( 'as_unschedule_all_actions' ) ) {
            as_unschedule_all_actions( 'aca_ai_content_agent_run_main_automation' );
            as_unschedule_all_actions( 'aca_ai_content_agent_reset_api_usage_counter' );
            as_unschedule_all_actions( 'aca_ai_content_agent_generate_style_guide' );
            as_unschedule_all_actions( 'aca_ai_content_agent_verify_license' );
            as_unschedule_all_actions( 'aca_ai_content_agent_clean_logs' );
        }

        // Transient and option keys to be cleaned
        $option_keys = [
            'aca_ai_content_agent_options',
            'aca_ai_content_agent_prompts',
            'aca_ai_content_agent_gemini_api_key',
            'aca_ai_content_agent_license_key',
            'aca_ai_content_agent_is_pro_active',
            'aca_ai_content_agent_license_data',
            'aca_ai_content_agent_license_valid_until',
            'aca_ai_content_agent_api_usage_current_month',
            'aca_ai_content_agent_idea_count_current_month',
            'aca_ai_content_agent_draft_count_current_month',
            'aca_ai_content_agent_brand_profiles',
            'aca_ai_content_agent_style_guide',
        ];
        foreach ($option_keys as $key) {
            delete_option($key);
        }
        // Transient keys to be cleaned (delete by prefix)
        $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", '_transient_aca_%' ) );
        $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", '_transient_timeout_aca_%' ) );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", '_transient_aca_admin_notice_%' ) );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", '_transient_timeout_aca_admin_notice_%' ) );

        $roles = [ 'administrator', 'editor', 'author' ];
        foreach ( $roles as $role_name ) {
            $role = get_role( $role_name );
            if ( $role ) {
                $role->remove_cap( 'manage_aca_ai_content_agent_settings' );
                $role->remove_cap( 'view_aca_ai_content_agent_dashboard' );
            }
        }

        remove_role( 'aca_content_manager' );
	}

    /**
     * Clean up all plugin data.
     *
     * Removes all database tables, options, and transients created by the plugin.
     * This method is called when the plugin is uninstalled.
     *
     * @since 1.2.0
     * @return void
     */
    public static function cleanup() {
        global $wpdb;

        // SECURITY FIX: Enhanced cleanup with cache service
        if (class_exists('ACA_Cache_Service')) {
            ACA_Cache_Service::clear_all();
        }

        // Drop custom tables
        $tables = [
            $wpdb->prefix . 'aca_ai_content_agent_ideas',
            $wpdb->prefix . 'aca_ai_content_agent_logs',
            $wpdb->prefix . 'aca_ai_content_agent_clusters',
            $wpdb->prefix . 'aca_ai_content_agent_cluster_items',
        ];

        foreach ( $tables as $table ) {
            $wpdb->query( "DROP TABLE IF EXISTS `{$table}`" );
        }

        // Delete all plugin options
        $options = [
            'aca_ai_content_agent_options',
            'aca_ai_content_agent_prompts',
            'aca_ai_content_agent_gemini_api_key',
            'aca_ai_content_agent_license_key',
            'aca_ai_content_agent_is_pro_active',
            'aca_ai_content_agent_license_data',
            'aca_ai_content_agent_license_valid_until',
            'aca_ai_content_agent_style_guide',
            'aca_ai_content_agent_onboarding_complete',
            'aca_ai_content_agent_api_usage_current_month',
            'aca_ai_content_agent_idea_count_current_month',
            'aca_ai_content_agent_draft_count_current_month',
        ];

        foreach ( $options as $option ) {
            delete_option( $option );
        }

        // SECURITY FIX: Enhanced transient cleanup
        $transient_patterns = [
            '_transient_aca_%',
            '_transient_timeout_aca_%',
            '_transient_aca_admin_notice_%',
            '_transient_timeout_aca_admin_notice_%',
            '_transient_aca_cache_%',
            '_transient_timeout_aca_cache_%',
            '_transient_aca_api_%',
            '_transient_timeout_aca_api_%',
        ];

        foreach ( $transient_patterns as $pattern ) {
            $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $pattern ) );
        }

        // Clear any scheduled cron events
        wp_clear_scheduled_hook( 'aca_ai_content_agent_run_main_automation' );
        wp_clear_scheduled_hook( 'aca_ai_content_agent_reset_api_usage_counter' );
        wp_clear_scheduled_hook( 'aca_ai_content_agent_generate_style_guide' );
        wp_clear_scheduled_hook( 'aca_ai_content_agent_verify_license' );
        wp_clear_scheduled_hook( 'aca_ai_content_agent_clean_logs' );

        // SECURITY FIX: Clear user meta data
        $wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'aca_ai_content_agent_%'" );

        // SECURITY FIX: Clear post meta data
        $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_aca_ai_content_agent_%'" );
    }

}